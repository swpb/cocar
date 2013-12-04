<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MonitorController extends Controller
{
	private $dir;

	/**
	* @Route("/monitor", name="cocar_monitor")
	* @Template()
	*/
    public function monitorAction()
    {
        $this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/";

    	$em = $this->getDoctrine()->getManager();

    	$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

    	foreach($circuits as $cir)
    	{
            try
            {
                $id = $cir->getId();
        		$community = $cir->getCommunitySnmpBackbone();
        		$host = $cir->getIpBackbone();
                $codInterface = $cir->getCodeInterface();
                $numInterface = $cir->getNumSnmpInterface();

        		$com = "snmpget -Ov -t 1 -r 1 -c $community -v 1 $host .1.3.6.1.2.1.2.2.1.10.$numInterface .1.3.6.1.2.1.2.2.1.16.$numInterface";

                if($outPut = shell_exec($com))
                {
                    list($in, $out) = explode("\n", shell_exec($com));

                    $inOctets = $this->snmp($in);
                    $outOctets = $this->snmp($out);

                    if($inOctets || $outOctets)
                    {
                        $arqRrd = $this->dir . $id . '.rrd';

                        if (!file_exists($arqRrd))
                            $this->createRrd($arqRrd);
                        $this->updateRrd($arqRrd, $inOctets , $outOctets);
                    }
                }
            }
            catch(Exception $e)
            {
                return new Response($e->getMessage());
            }
    	}
        return new Response();
    }

    public function createRrd($arqRrd)
    {
        $create = "rrdtool create $arqRrd --step 60 " .
                    "DS:ds0:COUNTER:120:0:125000000 " .
                    "DS:ds1:COUNTER:120:0:125000000 " .
                    "RRA:AVERAGE:0.5:1:4320 " .
                    "RRA:AVERAGE:0.5:5:2016 " .
                    "RRA:AVERAGE:0.5:20:2232 " .
                    "RRA:AVERAGE:0.5:90:2976 " .
                    "RRA:AVERAGE:0.5:360:1460 " .
                    "RRA:AVERAGE:0.5:1440:730 " .
                    "RRA:MAX:0.5:1:4320 " .
                    "RRA:MAX:0.5:5:2016 " .
                    "RRA:MAX:0.5:20:2232 " .
                    "RRA:MAX:0.5:90:2976 " .
                    "RRA:MAX:0.5:360:1460 " .
                    "RRA:MAX:0.5:1440:730";
        shell_exec($create);
    }

    public function snmp($resp)
    {
        $resp = strstr($resp, ':');
        $resp = str_replace(":", "", $resp);
        return (trim($resp));
    }

    public function updateRrd($arqRrd, $in, $out, $date = null)
    {
        $date = empty($date) ? date('U') : $date;
        shell_exec("rrdtool update $arqRrd $date:$in:$out");
    }
}