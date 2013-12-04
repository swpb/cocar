<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Swpb\Bundle\CocarBundle\Entity\Rrd;

class RrdLogController extends Controller
{
	private $dir;

	/**
	* @Route("/rrdlog", name="cocar_rrdlog")
	* @Template()
	*/
    public function rrdLogAction()
    {
        set_time_limit(0);

    	$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/";

    	$em = $this->getDoctrine()->getManager();

    	$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

    	foreach($circuits as $cir)
    	{
    		$rrd = $cir->getId() . ".rrd";
    		$arq = $this->dir . $rrd;

    		if(file_exists($arq))
    		{
    			$end   = (date('U')) - 120;
    			$start = $end - 86400;

    			$com = "rrdtool fetch $arq AVERAGE --start $start --end $end | sed -e \"s/ds[01]//g\" | 
    				sed \"s/nan/0/g\" | tr \":\" \" \" | tr -s \" \" | sed -e \"s/ \$//\" | grep -v \"^\$\"";

    			$lines = explode("\n", shell_exec($com));

    			$codInt = strtr($cir->getId(), ".", "_");

                for ($i=0; $i < count($lines); $i++)
                {
                    $fields  = explode(" ", $lines[$i]);
                    $date    = new \DateTime();
                    $date->setTimestamp(intval($fields[0]));
                    $volIn   = $this->calc(isset($fields[1]) ? $fields[1] : 0);
                    $volOut  = $this->calc(isset($fields[2]) ? $fields[2] : 0);

                    if ($date->format('Y-m-d H:i:s') != "1970-01-01 00:00:00" && $date->format('Y-m-d H:i:s') != "1969-12-31 21:00:00")
                    {
                        $rrdLog = new Rrd();

                        $rrdLog->setDatetime($date);
                        $rrdLog->setCodeInterface($codInt);
                        $rrdLog->setVolumeIn($volIn);
                        $rrdLog->setVolumeOut($volOut);

                        $em->persist($rrdLog);
                    }
                    $em->flush();
                }
    		}
    	}
        return new Response();
    }

	public function calc($value)
	{
        $value = strtr($value, ",", ".");
        settype ($value, "double");
        return round($value, 1);
	}

}
