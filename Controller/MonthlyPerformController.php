<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use GerenciadorRedes\Bundle\CocarBundle\Entity\MonthlyPerformance;

class MonthlyPerformController extends Controller
{
	/**
	* @Route("/monthlyperform", name="cocar_monthlyperform")
	* @Template()
	*/
	public function monthlyPerformanceAction()
	{
    	$em = $this->getDoctrine()->getManager();

    	$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

    	foreach($circuits as $cir)
    	{
    		for ($mes=1;$mes<=12;$mes++)
    		{
				//Período a ser gerado na tabela
				$uso_20_50_in  = 0;
				$uso_50_85_in  = 0;
				$uso_m_85_in   = 0;
				$uso_20_50_out = 0;
				$uso_50_85_out = 0;
				$uso_m_85_out  = 0;
				$ocorrencias   = 0;
				$volume_mensal_out = 0;
				$volume_mensal_in  = 0;	

				unset($c_out_rec);
				unset($c_in_rec);

				$ano = date('Y');
				$mes = ($mes < 10) ? "0".$mes : $mes;		
				$mes_ano = $ano."-".$mes."-01";
				$ldia = date("t", mktime(0,0,0,$mes,'01',$ano));

				$daily = $em->createQuery(
				        		"SELECT d FROM CocarBundle:DailyPerformance d
				        			WHERE d.codeInterface = :code
				        				AND (d.day >= :startDate AND d.day <= :endDate)"
				        	)
				        	->setParameter('code', $cir->getId())
				        	->setParameter('startDate', $mes_ano." 00:00:00")
				        	->setParameter('endDate', $ano."-".$mes."-".$ldia." 23:59:59")
							->getResult();

				foreach($daily as $d)
				{
					//volume mensal
					$volume_mensal_out += $d->getVolumeOut();
					$volume_mensal_in += $d->getVolumeIn();

					//cir mensal recomendado - separa valores
					if(isset($c_out_rec[$d->getCirOutRec()]))
					{	
						$c_out_rec[$d->getCirOutRec()]++;
						$c_in_rec[$d->getCirInRec()]++;
					}
					else 
					{
						$c_out_rec[$d->getCirOutRec()] = 1;
						$c_in_rec[$d->getCirInRec()] = 1;
					}

					$c_out = $d->getCirOut();
					$c_in = $d->getCirIn();

					//criticidade mensal - separa valores
					$ocorrencias++;

					$criticidade_out = $d->get719CriticalityOut();
					$criticidade_in = $d->get719CriticalityIn();

					if (($criticidade_out >= 20) && ($criticidade_out < 50))
					{
						$uso_20_50_out++;
					}elseif(($criticidade_out >= 50) && ($criticidade_out < 85))
					{
						$uso_50_85_out++;
					}elseif ($criticidade_out >= 85)
					{
						$uso_m_85_out++;
					}
					if(($criticidade_in >= 20) && ($criticidade_in < 50))
					{
						$uso_20_50_in++;
					}elseif (($criticidade_in >= 50) && ($criticidade_in < 85))
					{
						$uso_50_85_in++;
					}elseif ($criticidade_in >= 85)
					{
						$uso_m_85_in++;
					}
			
					//criticidade mensal - em %
					$uso_20_50_in  = 100 * $uso_20_50_in/$ocorrencias;
					$uso_50_85_in  = 100 * $uso_50_85_in/$ocorrencias;
					$uso_m_85_in   = 100 * $uso_m_85_in/$ocorrencias;
					$uso_20_50_out = 100 * $uso_20_50_out/$ocorrencias;
					$uso_50_85_out = 100 * $uso_50_85_out/$ocorrencias;
					$uso_m_85_out  = 100 * $uso_m_85_out/$ocorrencias;
				}

				if(isset($c_in_rec))
				{
					//cir mensal recomendado - apanha valor
					krsort($c_in_rec);
					$cir_in_rec_m = key($c_in_rec);
					if ($c_in_rec[$cir_in_rec_m] < 2)
					{
						next($c_in_rec);	
						$cir_in_rec_m = key($c_in_rec);
					}		
				}

				if(isset($c_out_rec))
				{
					krsort($c_out_rec);
					$cir_out_rec_m = key($c_out_rec);
					if ($c_out_rec[$cir_out_rec_m] < 2)
					{
						next ($c_out_rec);	
						$cir_out_rec_m = key ($c_out_rec);
					}
				}

				//volume mensal em Gigabytes
				$volume_mensal_out /= 1000000;
				$volume_mensal_in  /= 1000000;

				$mPerform = new MonthlyPerformance();

				$mPerform->setDate(new \DateTime($mes_ano));
				$mPerform->setCodeInterface($cir->getId());
				$mPerform->setUse2050In(isset($uso_20_50_in) ? $uso_20_50_in : 0);
				$mPerform->setUse2050Out(isset($uso_20_50_out) ? $uso_20_50_out : 0);
				$mPerform->setUse5085In(isset($uso_50_85_in) ? $uso_50_85_in : 0);
				$mPerform->setUse5085Out(isset($uso_50_85_out) ? $uso_50_85_out : 0);
				$mPerform->setUseM85In(isset($uso_m_85_in) ? $uso_m_85_in : 0);
				$mPerform->setUseM85Out(isset($uso_m_85_out) ? $uso_m_85_out : 0);
				$mPerform->setVolumeIn(isset($volume_mensal_in) ? $volume_mensal_in : 0);
				$mPerform->setVolumeOut(isset($volume_mensal_out) ? $volume_mensal_out : 0);
				$mPerform->setCirIn(isset($c_in) ? $c_in : 0);
				$mPerform->setCirOut(isset($c_out) ? $c_out : 0);
				$mPerform->setCirInRec(isset($cir_in_rec_m) ? $cir_in_rec_m : 0);
				$mPerform->setCirOutRec(isset($cir_out_rec_m) ? $cir_out_rec_m : 0);

				$em->persist($mPerform);
            	$em->flush();
			}
    	}

		return new Response();
	}
}