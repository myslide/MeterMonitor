<?php

namespace AppBundle\Controller; #

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Epower;
use AppBundle\Entity\EntityBase;

define('EPOWER', 'Epower');

class ConsumptionEPowerController extends Base\ConsumptionControllerBase {

    private $ressourcename = EPOWER;

    function createEntity(): EntityBase {
        return new Epower();
    }

    function getRessourceName() {
        return $this->ressourcename;
    }

    /**
     * @Route("/consumptionmonitor/listEpower", name="listEpower")
     */
    function listAction(Request $request) {
        return parent::listAction($request);
    }

    /**
     * @Route("/consumptionmonitor/addEpower", name="addEpower")
     */
    public function addAction(Request $request) {
        return parent::addAction($request);
    }

    /**
     * @Route("/consumptionmonitor/editEpower/{id}", name="editEpower")
     */
    function editAction($id, Request $request) {
        return parent::editAction($id, $request);
    }

    /**
     * @Route("/consumptionmonitor/deleteEpower/{id}", name="deleteEpower")
     */
    function deleteAction($id, Request $request) {#
        return parent::deleteAction($id, $request);
    }

}
