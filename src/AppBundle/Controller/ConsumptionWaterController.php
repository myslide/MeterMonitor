<?php

namespace AppBundle\Controller; #

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Water;
use AppBundle\Entity\EntityBase;

define('WATER', 'Water');

class ConsumptionWaterController extends Base\ConsumptionControllerBase {

    private $ressourcename = WATER;

    function createEntity(): EntityBase {
        return new Water();
    }

    function getRessourceName() {
        return $this->ressourcename;
    }

    /**
     * @Route("/consumptionmonitor/listWater", name="listWater")
     */
    function listAction(Request $request) {
        return parent::listAction($request);
    }

    /**
     * @Route("/consumptionmonitor/addWater", name="addWater")
     */
    public function addAction(Request $request) {
        return parent::addAction($request);
    }

    /**
     * @Route("/consumptionmonitor/editWater/{id}", name="editWater")
     */
    function editAction($id, Request $request) {
        return parent::editAction($id, $request);
    }

    /**
     * @Route("/consumptionmonitor/deleteWater/{id}", name="deleteWater")
     */
    function deleteAction($id, Request $request) {#
        return parent::deleteAction($id, $request);
    }

}
