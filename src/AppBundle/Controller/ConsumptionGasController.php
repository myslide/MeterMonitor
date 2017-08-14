<?php

namespace AppBundle\Controller; #

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Gas;
use AppBundle\Entity\EntityBase;

define('GAS', 'Gas');

class ConsumptionGasController extends Base\ConsumptionControllerBase {

    private $ressourcename = GAS;

    function createEntity(): EntityBase {
        return new Gas();
    }

    function getRessourceName() {
        return $this->ressourcename;
    }

    /**
     * @Route("/consumptionmonitor/listGas", name="listGas")
     */
    function listAction(Request $request) {
        return parent::listAction($request);
    }

    /**
     * @Route("/consumptionmonitor/addGas", name="addGas")
     */
    public function addAction(Request $request) {
        return parent::addAction($request);
    }

    /**
     * @Route("/consumptionmonitor/editGas/{id}", name="editGas")
     */
    function editAction($id, Request $request) {
        return parent::editAction($id, $request);
    }

    /**
     * @Route("/consumptionmonitor/deleteGas/{id}", name="deleteGas")
     */
    function deleteAction($id, Request $request) {#
        return parent::deleteAction($id, $request);
    }

}
