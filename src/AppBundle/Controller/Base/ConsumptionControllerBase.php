<?php

namespace AppBundle\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Psr\Log\LoggerInterface;

/**
 * Implements the common used functions of specialized Consumption<Resource>Controller.
 *
 * @author mysli
 */
abstract class ConsumptionControllerBase extends Controller {

    private $em;
    private $findAllNext;
    private $querySorted;

    abstract function getRessourceName();

    function init() {
        $this->em = $this->getDoctrine()->getManager();
        $this->findAllNext = $this->em->createQuery(
                'SELECT entity FROM AppBundle:' . $this->getRessourceName() . ' entity
    WHERE entity.absoluteValue > :fromAbsolute
    ORDER BY entity.captureDate ASC');
        $this->querySorted = $this->em->createQuery(
                'SELECT entity FROM AppBundle:' . $this->getRessourceName() . ' entity
        ORDER BY entity.captureDate ASC');
    }

    function getEM() {
        if (!isSet($this->em)) {
            $this->init();
        }
        return $this->em;
    }

    function getQueryFindAllNext() {
        if (!isSet($this->findAllNext)) {
            $this->init();
        }
        return $this->findAllNext;
    }

    function getQuerySorted() {
        if (!isSet($this->querySorted)) {
            $this->init();
        }
        return $this->querySorted;
    }

    function listAction(Request $request) {
        $entities = $this->getDoctrine()->getRepository("AppBundle:" . $this->getRessourceName())->findAll();
        $prevabs = 0;
        $currabs = 0;
        $prevdate = NULL;
        $currdate = NULL;
        foreach ($entities as $entity) {
            // $logger = $this->get('logger');
            // $logger->info($entity->getValue());
            $currabs = $entity->getAbsoluteValue();
            $diff = $currabs - $prevabs;
            $entity->setDiff($diff);
            $prevabs = $currabs;
            $currdate = $entity->getCaptureDate();
            if (isset($prevdate)) {
                $interval = $currdate->diff($prevdate)->format('%d');
                $logger = $this->get('logger');
                $logger->info($interval);
                if ($interval === false || $interval == 0) {
                    $entity->setDailyAvg(0);
                } else {
                    $entity->setDailyAvg($diff / $interval);
                }
            } else {
                $entity->setDailyAvg(0);
            }
            $prevdate = $currdate;
            continue;
        }
        return $this->render("consumptionmonitor/list" . $this->getRessourceName() . ".html.twig", array('consumption' => $entities));
    }

    function addAction(Request $request) {
        $entity = $this->createEntity();
        $entity->setCaptureDate(new\DateTime('now'));
        $logger = $this->get('logger');
        $logger->info($entity::name);
        $form = $this->createFormBuilder($entity)
                ->add('captureDate', DateType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
                ->add('value', TextType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('note', TextType::class, array('attr' => array('value' => '-', 'class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'erfassen', 'attr' => array('label' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //get data
            $date = $form['captureDate']->getData();
            $value = $form['value']->getData();
            $note = $form['note']->getData();
            $entity->setCaptureDate($date);
            $entity->setSubmitDate(new\DateTime('now'));
            $entity->setValue($value);
            $entity->setAbsoluteValue($entity->getAbsoluteValue() + $value);
            $entity->setNote($note);
            $this->getEM()->persist($entity);
            $this->getEM()->flush();
            $this->addflash('notice', 'Hinzugefuegt');
            return $this->redirect('add' . $this->getRessourceName());
        }
        return $this->render('consumptionmonitor/add.html.twig', array(
                    'form' => $form->createView()));
    }

    function deleteAction($id, Request $request) {#
        $em = $this->getDoctrine()->getManager();
        $consumption = $em->getRepository("AppBundle:" . $this->getRessourceName())->find($id);
        $em->remove($consumption);
        $em->flush();
        $this->addFlash('notice', "consumption $id removed");
        return $this->redirectToRoute("list" . $this->getRessourceName());
    }

    function editAction($id, Request $request) {
        $repository = $this->getDoctrine()->getRepository("AppBundle:" . $this->getRessourceName());
        $entity = $repository->find($id);
        $consumptions = $this->getDoctrine()->getRepository("AppBundle:" . $this->getRessourceName())->findAll();
        $oldvalue = $entity->getValue();
        $form = $this->createFormBuilder($entity)
                ->add('captureDate', DateType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('value', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('note', TextType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Fertig', 'attr' => array('label' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->add('cancel', SubmitType::class, array('label' => 'Abbrechen', 'attr' => array('label' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();
        $form->handleRequest($request);
        if ($form->get('cancel')->isClicked()) {
            $value = $form['value']->getData();
            return $this->redirect("/consumptionmonitor/list" . $this->getRessourceName());
        } else {
            if ($form->get('save')->isClicked() && $form->isSubmitted() && $form->isValid()) {
                $date = $form['captureDate']->getData();
                $note = $form['note']->getData();
                $entity->setCaptureDate($date);
                $entity->setSubmitDate(new\DateTime('now'));
                //Happens in calculateAbsoluteValue:$entity->setValue($value);
                $entity->setAbsoluteValue($this->calculateAbsoluteValue($repository, $entity, $oldvalue));
                $entity->setNote($note);
                $this->getEM()->persist($entity);
                $this->getEM()->flush();
                $this->addflash('notice', 'Bearbeitet');
                return $this->redirect("/consumptionmonitor/list" . $this->getRessourceName());
            }
        }
        return $this->render("consumptionmonitor/edit" . $this->getRessourceName() . ".html.twig", array('consumption' => $consumptions, 'form' => $form->createView(), 'edit' => $entity));
    }

    function calculateAbsoluteValue($repository, $entity, $oldValue) {
        $absolute = $entity->getAbsoluteValue(); //store prev value
        $newValue = $entity->getValue();
        if ($oldValue != $newValue) {
            $entities = $this->getQuerySorted()->getResult();
            $diff = $newValue - $oldValue;
            $absolute = $absolute + $diff;
            $this->updateNextAbsolutes($entity->getAbsoluteValue(), $diff);
        }
        return $absolute;
    }

    function updateNextAbsolutes($fromAbsolute, $diff) {
        $entities = $this->getQueryFindAllNext()->setParameter('fromAbsolute', $fromAbsolute)->getResult();
        foreach ($entities as $entity) {
            $entity->setAbsoluteValue($entity->getAbsoluteValue() + $diff);
            $this->getEM()->persist($entity);
        }
        $this->getEM()->flush();
    }

}
