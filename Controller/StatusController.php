<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use GerenciadorRedes\Bundle\CocarBundle\Controller\SnmpController;

class StatusController extends Controller
{
	/**
	* @Route("/status", name="cocar_status")
	* @Template()
	*/
    public function statusAction()
    {
		$oids = array(
					"index" => ".1.3.6.1.2.1.2.2.1.1",
					"descr" => ".1.3.6.1.2.1.2.2.1.2",
					"adminStatus" => ".1.3.6.1.2.1.2.2.1.7",
					"operStatus" => ".1.3.6.1.2.1.2.2.1.8"
				);

		#verificar o campo geraAlarme='S' na tabela tb_circuits

    	$em = $this->getDoctrine()->getManager();

    	$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

    	foreach($circuits as $cir)
    	{
    		$obj = new SnmpController(
    			$cir->getIpBackbone(),
    			$cir->getCommunitySnmpBackbone(),
    			$cir->getCodeInterface()
    		);

    		if($obj->sysUpTime())
    		{
				$numInterface = $cir->getNumSnmpinterface();

				$MensageIBs = $oids{'descr'} . ".$numInterface " .
            	    $oids{'adminStatus'} . ".$numInterface " .
	      		    $oids{'operStatus'} . ".$numInterface ";

				list($ifDescr, $ifAdminStatus, $ifOperStatus) = $obj->fcSnmpGet($MensageIBs);

				$ifDescr = str_replace("-aal5 layer", "", $ifDescr);
				$ifDescr = str_replace("atm subif", "", $ifDescr);

                if($cir->getSerialBackbone() == $ifDescr)
				{
					$cir->setAdminStatus($this->status($ifAdminStatus));
					$cir->setOperStatus($this->status($ifOperStatus));

		            $em->persist($cir);
		            $em->flush();
				}
    		}
    		else
    		{
				$cir->setAdminStatus($this->status('INAT'));
				$cir->setOperStatus($this->status('INAT'));

				$em->persist($cir);
		        $em->flush();
			}
    	}

        return new Response();
    }

    public function status($status)
	{
		return preg_match("/1/i", $status) ? "UP" : 
			(preg_match("/2/i", $status) ? "DOWN" : "INAT");
	}
}