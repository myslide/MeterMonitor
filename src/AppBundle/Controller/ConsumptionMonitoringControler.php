<?php

namespace AppBundle\Controller; #

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class ConsumptionMonitoringControler extends Controller {

 
  /**
     * @Route("/", name="dashboard")
     */
public function indexAction(LoggerInterface $logger)
{
    // alternative way of getting the logger
    // $logger = $this->get('logger');

    $logger->info('I just got the logger');
    $logger->error('An error occurred');

    $logger->critical('I left the oven on!', array(
        // include extra "context" info in your logs
        'cause' => 'in_hurry',
    ));
return $this->render('consumptionmonitor/index.html.twig');
    // ...
}
    

 

    /**
     * @Route("/report", name="reportPage")
     */
    public function reportAction(Request $request) {
        return $this->render('consumptionmonitor/report.html.twig');
        // replace this example code with whatever you need
        //return $this->render('consumptionmonitor/index.html.twig', [
        //            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        //]);
    }

}
