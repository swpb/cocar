<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Swpb\Bundle\CocarBundle\Entity\Reliability;

class ReliabilityController extends Controller
{
	private $dir;

	/**
	* @Route("/reliability", name="cocar_reliability")
	* @Template()
	*/
    public function reabilityAction()
    {
    	$reliability = new Reliability();

        $this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/rly/";

    	$em = $this->getDoctrine()->getManager();

    	$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

    	foreach($circuits as $cir)
    	{
    		$codInterface = $cir->getCodeInterface();
    		$hostTip = $cir->getIpSerialRouterTip();
    		$communityTip = $cir->getCommunitySnmpRouterTip();
    		$snmpPortTip = $cir->getSnmpPortTip();

    		$com = "snmpget -Ov -t 1 -r 1 -c $communityTip -v 1 $hostTip .1.3.6.1.4.1.9.2.2.1.1.22.$snmpPortTip 2> /dev/null";

    		$rly = $this->get('cocar_monitor')->snmp(shell_exec($com));

			$rly = (!$rly) ? 0 : $rly;

			$date = ((int)(date('U')/600))*600;

			$reliability->setCodeInterface($codInterface);
			$reliability->setDate($date);
			$reliability->setRly($rly);

            $em->persist($reliability);
            $em->flush();

			$arqRrd = $this->dir . $codInterface . "_rly.rrd";

			if (!file_exists($arqRrd))
				$this->createRrdRly($arqRrd);

			$this->updateRrdRly($arqRrd, $date, $rly);
    	}
    	return new Response();
    }

	public function createRrdRly($arqRrd)
	{
		$com = "rrdtool create " . $arqRrd . " --step 600 " . 
				  "DS:rly:GAUGE:1200:0:256 " . 
				  "RRA:AVERAGE:0.5:1:480 " . 
				  "RRA:AVERAGE:0.5:2:510 " . 
				  "RRA:AVERAGE:0.5:9:500 " . 
				  "RRA:AVERAGE:0.5:36:500 " . 
				  "RRA:AVERAGE:0.5:144:370 " . 
				  "RRA:MIN:0.5:1:480 " . 
				  "RRA:MIN:0.5:2:510 " . 
				  "RRA:MIN:0.5:9:500 " . 
				  "RRA:MIN:0.5:36:500 " . 
				  "RRA:MIN:0.5:144:370";

		shell_exec($com);
	}

	public function updateRrdRly($arqRrd, $date, $rly)
	{
		shell_exec("rrdtool update $arqRrd $date:$rly");
	}
}
