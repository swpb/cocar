<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AlarmController extends Controller
{
	private $dir;

	/**
	* @Route("/generatealarm", name="cocar_generatealarm")
	* @Template()
	*/
	public function generateAlarmAction()
	{
		$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/";

    	$em = $this->getDoctrine()->getManager();

    	$circuits = $em->createQuery(
			            	"SELECT c FROM CocarBundle:Circuits c 
			            		WHERE c.generateAlarm = :alarm
			            			AND c.manages <> :manage
			            			AND c.status = 'UP'"
			        	)
    					->setParameter('alarm', true)
    					->setParameter('manage', 'Entidades Externas')
						->getResult();

    	foreach($circuits as $cir)
    	{
			$media_in = 0;
			$media_out = 0;
			$qtd = 0;

			$rrd = $cir->getId() . ".rrd";

			if(file_exists($this->dir . $rrd))
			{
				$fim = (date('U')) - 60;
				$inicio = $fim - 720;
				$com = "rrdtool fetch $this->dir$rrd AVERAGE --start $inicio --end $fim | sed -e \"s/ds[01]//g\" | 
				sed \"s/nan/0/g\" | tr \":\" \" \" | tr -s \" \" | sed -e \"s/ \$//\" | grep -v \"^\$\" |tail -10";

				$linhas = explode("\n", shell_exec($com));

				for ($i=0; $i < count($linhas) - 1; $i++)
				{
					$campos  = explode(" ", $linhas[$i]);
					$data 	 = strftime("%Y-%m-%d %H:%M:%S",$campos[0]);
					$vol_in  = $this->calc($campos[1]);
				    $vol_out = $this->calc($campos[2]);	
		
					if ($data != "1970-01-01 00:00:00" && $data != "1969-12-31 21:00:00")
					{
						$media_in  += $vol_in;
						$media_out += $vol_out;	
						$qtd++;
					}
				}
				
				$history   = strtoupper($cir->getHistory());	
				$hist_novo = "N";

				if ($qtd != 0)	
				{
					# INICIO DO TRATAMENTO COMO CIRCUITO
					$media_in  = round(($media_in * 0.008)/$qtd, 1);
					$media_out = round(($media_out * 0.008)/$qtd, 1);
					
					if (strtolower($cir->getTypeInterface()) == "circuito" && (!preg_match("/ETH/i", $cir->getSerialBackbone())))
					{	
						$cir_in	 = round(($cir->getCirIn() * 1.2), 1);
						$cir_out = round(($cir->getCirOut() * 1.2), 1);
					
						if ($media_in > $cir_in || $media_out > $cir_out)
						{
							if($history != "A1" && $history != "A2")
								$hist_novo = "A1";
							elseif($history == "A1" || $history == "A2")
								$hist_novo = "A2";
						}
						elseif($media_out == 0.0 || $media_in == 0.0 ) 
						{
							if($history != "Z1" && $history != "Z2" )
								$hist_novo = "Z1";
							elseif($history == "Z1" || $history == "Z2")
								$hist_novo = "Z2";
						}
					}
					else
					{ 
						# INICIO DO TRATAMENTO COMO CONCENTRADORA
						$porta = round(($cir->getSpeed() * 0.85), 1);

						if ($media_in > $porta || $media_out > $porta)
						{	
							if ($history != "A1" && $history != "A2")
								$hist_novo = "A1";
							elseif($history == "A1" || $history == "A2")
								$hist_novo = "A2";
						}
						elseif  ($media_out == 0.0 || $media_in == 0.0 ) 
						{
							if($history != "Z1" && $history != "Z2")
								$hist_novo = "Z1";
							elseif($history ==  "Z1" || $history == "Z2")
								$hist_novo = "Z2";
						}		
					}
				}
				else
				{
					$arq = strftime("%d-%m-%Y", $fim);
					$fp = fopen($this->dir."logs/".$arq.".log", 'a');
					$info = "Arquivo RRD: $rrd";
				}

				$cir->setHistory($hist_novo);

				$em->persist($cir);
            	$em->flush();
			}
    	}

		return new Response();
	}

	/**
	* @Route("/endalarm", name="cocar_endalarm")
	* @Template()
	*/
	public function endAlarmAction()
	{
		$em = $this->getDoctrine()->getManager();
		
		$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

		foreach ($circuits as $cir) {
			$cir->setHistory('N');

			$em->persist($cir);
        	$em->flush();
		}
		return new Response();
	}

	public function calc($value)
	{
		$value = strtr($value, ",", ".");	
		settype ($value, 'double');
		return round($value,1);
	}
}
