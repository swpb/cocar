<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use GerenciadorRedes\Bundle\CocarBundle\Entity\DailyPerformance;

class DailyPerformController extends Controller
{
	/**
	* @Route("/dailyperform", name="cocar_dailyperform")
	* @Template()
	*/
	public function dailyPerformanceAction()
	{
		set_time_limit(0);

    	$em = $this->getDoctrine()->getManager();

    	#puxar o id do circuito
    	$circuits = $em->createQuery(
			            	"SELECT c FROM CocarBundle:Circuits c 
			            		WHERE c.operStatus = 'UP'"
			        	)->getResult();

    	foreach($circuits as $cir)
    	{
    		$perform = new DailyPerformance();

    		$speed = $cir->getSpeed();
    		$codInterface = $cir->getId();
			$cirIn  = $cir->getCirIn();	
			$cirOut = $cir->getCirOut();

			if ($cirIn == 0)
			{
				$cirIn  = $speed * 0.85;	
				$cirOut = $speed * 0.85;
			}

			$arq = strtr($codInterface, ".", "_");

			$day = strftime('%Y-%m-%d', (date('U')-86400));
			$startDate = $day . " 07:00:00";
			$endDate   = $day . " 19:00:00";

			$sum = $em->createQuery(
			        		"SELECT SUM(r.volumeIn) as vin, SUM(r.volumeOut) as vout FROM CocarBundle:Rrd r
			        			WHERE (r.datetime >= :start AND r.datetime <= :end)
			        				AND(r.codeInterface = :code)"
			        	)
			        	->setParameter('start', $day . " 00:00:00")
			        	->setParameter('end', $day . " 23:59:00")
			        	->setParameter('code', $codInterface)
						->getOneOrNullResult();

			if(!empty($sum))
			{
				$volIn  = $sum['vin'] * 60;
				$volOut = $sum['vout'] * 60;

				# CALCULA HMM IN: HORA, MEDIA, PICO, CRITICIDADE
				$hmmIn = 
				$this->HMM(array(
								'code' => $codInterface,
								'field' => "volumeIn", 
								'hStart' => 0, 
								'hEnd' => 23, 
								'cir' => $cirIn,
								'day' => $day
							 ));
				if(isset($hmmIn[0]))
					$hourHMMIn = $hmmIn[0] . ":" . $hmmIn[1] . ":00";
	
				# CALCULA HMM OUT: HORA, MEDIA, PICO, CRITICIDADE
				$hmmOut = 
				$this->HMM(array(
								'code' => $codInterface,
								'field' => "volumeOut", 
								'hStart' => 0, 
								'hEnd' => 23, 
								'cir' => $cirOut,
								'day' => $day
							 ));

				if(isset($hmmOut[0]))
					$hourHMMOut = $hmmOut[0] . ":" . $hmmOut[1] . ":00";

				# CALCULA HMM IN NO PERIODO : HORA, MEDIA, PICO, CRITICIDADE
				$hmmInPer = 
				$this->HMM(array(
								'code' => $codInterface,
								'field' => "volumeIn", 
								'hStart' => 7, 
								'hEnd' => 18, 
								'cir' => $cirIn,
								'day' => $day
							 ));
				if(isset($hmmInPer[0]))
					$hourHMMInPer = $hmmInPer[0] . ":" . $hmmInPer[1] . ":00";

				# CALCULA HMM OUT NO PERIODO: HORA, MEDIA, PICO, CRITICIDADE
				$hmmOutPer = 
				$this->HMM(array(
								'code' => $codInterface,
								'field' => "volumeOut", 
								'hStart' => 7, 
								'hEnd' => 18, 
								'cir' => $cirOut,
								'day' => $day
							 ));
				if(isset($hmmOutPer[0]))
					$hourHMMOutPer = $hmmOutPer[0] . ":" . $hmmOutPer[1] . ":00";
	
				# CALCULA PERIODO IN: HORA, MEDIA, PICO, CRITICIDADE
				for($i=1;$i<=4;$i++)
				{
					$peak = 
					$this->periodMax(array(
										'code' => $codInterface,
										'field' => ($i%2 == 0) ? "volumeIn" : "volumeOut", 
										'startDate' => $startDate, 
										'endDate' => $endDate, 
										'cir' => ($i%2 == 0) ? $cirIn : $cirOut
									 ));

					$media = 
					$this->periodSum(array(
										'code' => $codInterface,
										'field' => ($i%2 == 0) ? "volumeIn" : "volumeOut", 
										'startDate' => $startDate, 
										'endDate' => $endDate, 
										'cir' => ($i%2 == 0) ? $cirIn : $cirOut
									 ));

					$criticality = 
					$this->periodField(array(
										'code' => $codInterface,
										'field' => ($i%2 == 0) ? "volumeIn" : "volumeOut", 
										'startDate' => $startDate, 
										'endDate' => $endDate, 
										'cir' => ($i%2 == 0) ? $cirIn : $cirOut
									 ));

					if($i%2 == 0)
					{
						$perform->set719PeakIn($peak);
						$perform->set719MediaIn($media);
						$perform->set719CriticalityIn($criticality);
					}
					else
					{
						$perform->set719PeakOut($peak);
						$perform->set719MediaOut($media);
						$perform->set719CriticalityOut($criticality);
					}
				}
	
				$recommendedCircuitIn = 
				$this->recommendedCircuit(array(
											'code' => $codInterface,
											'field' => "volumeIn",
											'startDate' => $startDate,
											'endDate' => $endDate
										 ));

				$recommendedCircuitOut =
				$this->recommendedCircuit(array(
											'code' => $codInterface,
											'field' => "volumeOut",
											'startDate' => $startDate,
											'endDate' => $endDate
										 ));

				$perform->setCodeInterface($codInterface);
				$perform->setDay(new \DateTime($day));
				$perform->setCirIn($cirIn);
				$perform->setCirOut($cirOut);
				$perform->setCirInRec($recommendedCircuitIn);
				$perform->setCirOutRec($recommendedCircuitOut);
				$perform->setVolumeIn($volIn);
				$perform->setVolumeOut($volOut);
				$perform->setLossInHour(new \DateTime('00:00:00'));
				$perform->setLossOutHour(new \DateTime('00:00:00'));
				$perform->setHmmHourIn(isset($hourHMMIn) ? new \DateTime($hourHMMIn) : new \DateTime('00:00:00'));
				$perform->setHmmHourOut(isset($hourHMMOut) ? new \DateTime($hourHMMOut) : new \DateTime('00:00:00'));
				$perform->setHmmPeakIn(isset($hmmIn[3]) ? $hmmIn[3] : 0);
				$perform->setHmmPeakOut(isset($hmmOut[3]) ? $hmmOut[3] : 0);
				$perform->setHmmMediaIn(isset($hmmIn[2]) ? $hmmIn[2] : 0);
				$perform->setHmmMediaOut(isset($hmmOut[2]) ? $hmmOut[2] : 0);
				$perform->setHmmCriticalityIn(isset($hmmIn[4]) ? $hmmIn[4] : 0);
				$perform->setHmmCriticalityOut(isset($hmmOut[4]) ? $hmmOut[4] : 0);
				$perform->setHmmHourInPer(isset($hourHMMInPer) ? new \DateTime($hourHMMInPer) : new \DateTime('00:00:00'));
				$perform->setHmmHourOutPer(isset($hourHMMOutPer) ? new \DateTime($hourHMMOutPer) : new \DateTime('00:00:00'));
				$perform->setHmmPeakInPer(isset($hmmInPer[3]) ? $hmmInPer[3] : 0);
				$perform->setHmmPeakOutPer(isset($hmmOutPer[3]) ? $hmmOutPer[3] : 0);
				$perform->setHmmMediaInPer(isset($hmmInPer[2]) ? $hmmInPer[2] : 0);
				$perform->setHmmMediaOutPer(isset($hmmOutPer[2]) ? $hmmOutPer[2] : 0);
				$perform->setHmmCriticalityInPer(isset($hmmInPer[4]) ? $hmmInPer[4] : 0);
				$perform->setHmmCriticalityOutPer(isset($hmmOutPer[4]) ? $hmmOutPer[4] : 0);

				$em->persist($perform);
            	$em->flush();
			}
    	}
    	return new Response();
	}

	public function HMM ($params = array())
	{
		if(!empty($params))
		{	
			$bytesHMM = 0;

			for ($h=$params['hStart'];$h<=$params['hEnd'];$h++)
			{
				for ($m=0;$m<60;$m++)
				{
					$hour = $params['day']." ".$h.":".$m.":00";			
					$hHMM = $h + 1;
					$hHMM = ($hHMM == 24) ? 23 : $hHMM;
					$hour60min = $params['day']." ".$hHMM.":".$m.":00";

					$sum = $this->getDoctrine()->getManager()->createQuery("SELECT SUM(r.volumeIn) as volumeIn, SUM (r.volumeOut) as volumeOut FROM CocarBundle:Rrd r
												WHERE (r.datetime >= :start)
													AND(r.datetime <= :end)
													AND(r.codeInterface = :code)"
											)
								        	->setParameter('start', $hour)
								        	->setParameter('end', $hour60min)
								        	->setParameter('code', $params['code'])
											->getOneOrNullResult();

					if ($sum[$params['field']] > $bytesHMM)
					{
							$vectorHMM[0] = $h;
							$vectorHMM[1] = $m;
							$vectorHMM[2] = (8/60000) * $sum[$params['field']];
					}	
				}
			}

			if(isset($vectorHMM))
			{
				$hour = $params['day']." ".$vectorHMM[0].":".$vectorHMM[1].":00";
				$hHMM = $vectorHMM[0] + 1;
				$hour60min = $params['day']." ".$hHMM.":".$vectorHMM[1].":00";

				$max = $this->periodMax(
							array(
								'field' => $params['field'],
								'startDate' => $hour,
								'endDate' => $hour60min,
								'code' => $params['code']
							));

				if(!empty($max))
				{
					$vectorHMM[3] = (8/1000) * $max;	

					$field = $this->getDoctrine()->getManager()->createQuery("SELECT COUNT(r.volumeIn) as volumeIn, COUNT(r.volumeOut) as volumeOut FROM CocarBundle:Rrd r
												WHERE (r.datetime >= :start)
													AND(r.datetime < :end)
													AND(r.codeInterface = :code)
													AND(:field > :cir)"
											)
								        	->setParameter('start', $max['startDate'])
								        	->setParameter('end', $max['endDate'])
								        	->setParameter('code', $params['code'])
								        	->setParameter('field', $params['field'])
								        	->setParameter('cir', $params['cir']*(1000/8))
											->getOneOrNullResult();

					if(!empty($field))
						$vectorHMM[4] = (100/60) * $field[$params['field']];
				}
				return $vectorHMM;
			}
		}
		return array();
	}

	public function periodMax($params = array())
	{
		if(!empty($params))
		{
			$max = $this->getDoctrine()->getManager()->createQuery("SELECT MAX(r.volumeIn) as volumeIn, MAX(r.volumeOut) as volumeOut FROM CocarBundle:Rrd r
										WHERE (r.datetime >= :start)
											AND(r.datetime < :end)
											AND(r.codeInterface = :code)"
									)
						        	->setParameter('start', $params['startDate'])
						        	->setParameter('end', $params['endDate'])
						        	->setParameter('code', $params['code'])
									->getOneOrNullResult();

			return (!empty($max)) ? (8/1000) * $max[$params['field']] : null;

		}
		return null;
	}

	public function periodSum($params = array())
	{
		if(!empty($params))
		{
			$sum = $this->getDoctrine()->getManager()->createQuery("SELECT SUM(r.volumeIn) as volumeIn, SUM (r.volumeOut) as volumeOut FROM CocarBundle:Rrd r
										WHERE (r.datetime >= :start)
											AND(r.datetime < :end)
											AND(r.codeInterface = :code)"
									)
						        	->setParameter('start', $params['startDate'])
						        	->setParameter('end', $params['endDate'])
						        	->setParameter('code', $params['code'])
									->getOneOrNullResult();

			return (!empty($sum)) ? (8/720000) * $sum[$params['field']] : null;
		}
		return null;
	}

	public function periodField($params = array())
	{
		if(!empty($params))
		{
			$field = $this->getDoctrine()->getManager()->createQuery("SELECT COUNT(r.volumeIn) as volumeIn, COUNT(r.volumeOut) as volumeOut FROM CocarBundle:Rrd r
										WHERE (r.datetime >= :start)
											AND(r.datetime < :end)
											AND(r.codeInterface = :code)
											AND(:field > :cir)"
									)
						        	->setParameter('start', $params['startDate'])
						        	->setParameter('end', $params['endDate'])
						        	->setParameter('code', $params['code'])
						        	->setParameter('field', $params['field'])
						        	->setParameter('cir', $params['cir']*(1000/8))
									->getOneOrNullResult();

			return (!empty($field)) ? (100/720) * $field[$params['field']] : null;
		}
		return null;
	}

	public function recommendedCircuit($params = array())
	{
		if(!empty($params))
		{
			$recommendCir = array(
				16, 32, 48, 64, 80, 96, 112, 128, 160, 192, 224, 256, 288, 320, 352, 384, 
				448, 512, 640, 768, 896, 1024, 1152, 1280, 1408, 1536, 1664, 1792, 1920
			);

			$field = $this->getDoctrine()->getManager()->createQuery("SELECT r.volumeIn as volumeIn, r.volumeOut as volumeOut FROM CocarBundle:Rrd r
										WHERE (r.datetime >= :start AND r.datetime < :end)
											AND(r.codeInterface = :code)"
									)
						        	->setParameter('start', $params['startDate'])
						        	->setParameter('end', $params['endDate'])
						        	->setParameter('code', $params['code'])
						        	->getResult();
			$samples = 0;

			$cRec = array();

			foreach($field as $f)
			{
				$samples++;
				$f[$params['field']] *= (8/1000);
			    $cir = round($f[$params['field']], 0);
				
				$start  = 0;
				$end    = count($recommendCir) -1;
				$middle = ($start + $end)/2;

				while (isset($recommendCir[$middle]) && $start != $end && $cir != $recommendCir[$middle])
				{
					if ($cir > $recommendCir[$middle])
						$start = $middle + 1;
					else
						$end = $middle;

					$middle	= ($start + $end)/2;
				}

				$cRec[$middle] = isset($cRec[$middle]) ? $cRec[$middle] += 1 : 1;
			}

			$j = -1;
			$x = 0;
			while ($x < (0.95 * $samples) && isset($cRec[$j]))
			{
				$j++;
				$x += $cRec[$j];
			}

			return (isset($recommendCir[$j])) ? $recommendCir[$j] : 0;
		}
	}
}