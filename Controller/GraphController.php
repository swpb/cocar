<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use JpGraph;

class GraphController extends Controller
{
	private $dir;

	/**
	* @Route("/graph/{id}", name="cocar_graph")
	* @Method("GET")
	* @Template()
	*/
	public function graphAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$circuit = $em->getRepository('CocarBundle:Circuits')->find($id);

	    $form = $this->graphForm();

		return array('circuit' => $circuit, 'form' => $form->createView());
	}

	/**
	* @Route("/graphshow/{id}", name="cocar_graphshow")
	* @Method("GET")
	*/
	public function graphShowAction(Request $request, $id)
	{
		$form = $this->graphForm();

        $form->bind($request);

        if ($form->isValid())
        {
        	$em = $this->getDoctrine()->getManager();

        	$data = $form->getData();

        	switch($data['choice'])
        	{
        		case 0:
        			$img[] = $this->graphTraffic($id, $data['period']);
        			$img[] = $this->graphConcentrator($id, $data['period']); 
        			$img[] = $this->graphReliability($id, $data['period']);
        			break;
        		case 1: 
        			$img[] = $this->graphTraffic($id, $data['period']); 
        			break;
        		case 2: 
        			$img[] = $this->graphConcentrator($id, $data['period']); 
        			break;
        		case 3:
        			$img[] = $this->graphReliability($id, $data['period']);
        			break;
        		case 4:
        			$img = $this->graphMachine($id, $data['period']);
        			break;
        		default: 
        			$img[] = $this->graphTraffic($id, $data['period']);
        	}

        	$paginator = $this->get('knp_paginator');
		    $img       = $paginator->paginate($img, $this->get('request')->query->get('page', 1), 5);

			$circuit = $em->getRepository('CocarBundle:Circuits')->find($id);

				return $this->render('CocarBundle:Graph:graph.html.twig', 
			  		array('id' => $id, 'form' => $form->createView(), 'circuit' => $circuit, 'img' => $img));
		}

		return $this->redirect($this->generateUrl('entity'), array('id' => $id));
	}

    /**
     * @param mixed $id the circuit id
     *
     * @return \Symfony\Component\Form\Form the form
     */
    private function graphForm()
    {
        return $this->createFormBuilder()
	        ->add('choice', 'choice', array(
	                'choices' => array(
	                    0 => 'Todos',
	                    1 => 'Tráfego',
	                    2 => 'Concentradora',
	                    3 => 'Confiabilidade',
	                    4 => 'Maquinas',
	                ),
			        'multiple' => false,
			        'expanded' => true,
			        'required' => true,
	        ))
	        ->add('period', 'choice', array(
	                'choices' => array(
	                    '6hours'   => '6 horas',
	                    '8hours'   => '8 horas',
	                    '1day'     => '1 dia',
	                    '40hours'  => '40 horas',
	                    '160hours' => '1 semana',
	                    '720hours' => '1 mês',
	                    '4months'  => '4 meses',
	                    '1year'    => '1 ano'
	                ),
			        'required' => true,
	        ))
	        ->add('send', 'submit', array('label' => 'Enviar' ))
	        ->getForm();
    }

    /**
    * Retorna gráfico do tráfego
    */
	private function graphTraffic($id, $period = '6hours')
	{
		$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/";

        $em = $this->getDoctrine()->getManager();

        $circuits = $em->getRepository('CocarBundle:Circuits')->find($id);

		$codInterface  = $circuits->getCodeInterface();
		$typeInterface = $circuits->getTypeInterface();
		$cirIn  = $circuits->getCirIn();
		$cirOut = $circuits->getCirOut();
		$serial = $circuits->getSerialBackbone();
    	$name   = $circuits->getCodeInterface();
		$arq    = $circuits->getId() . ".rrd";

		if(!$this->rrdExists($arq)){
			return new Response("<H1>Erro: A Unidade: <FONT COLOR='red'>" . $name . "</FONT> não esta sendo monitorada.</H1>");
		}

		$cir = ($cirIn >= $cirOut) ? $cirIn : $cirOut;

		$cirBits = ($cir * 1000);

		$img = $codInterface . "_concentradora_" . $period . "_" . time() . ".png";
		$scales = $this->scales(1, $period);

		$args = array(
					'img' => $img,
					'period' => $period,
					'scales' => $scales,
					'name' => $name,
					'arq'  => $arq,
					'dir'  => $this->dir,
					'typeInterface' => $typeInterface,
					'serial'  => $serial,
					'cir'     => $cir,
					'cirBits' => $cirBits,
				);

		shell_exec($this->getRrdToolGraphCommand($args, true));

		return $img;
	}

    /**
    * Retorna gráfico da concentradora
    */
	private function graphConcentrator($id, $period = '6hours')
	{
		$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/";

        $em = $this->getDoctrine()->getManager();

        $circuits = $em->getRepository('CocarBundle:Circuits')->find($id);

		$codInterface  = $circuits->getCodeInterface();
		$typeInterface = $circuits->getTypeInterface();
		$cirIn  = $circuits->getCirIn();
		$cirOut = $circuits->getCirOut();
		$serial = $circuits->getSerialBackbone();
    	$name   = "Concentradora: " . $serial;
		$arq    = $circuits->getId() . ".rrd";

		if(!$this->rrdExists($arq))
		{
			return new Response("<H1>Erro: A Unidade: <FONT COLOR='red'>" . $name . "</FONT> não esta sendo monitorada.</H1>");
		}

		$cir = ($cirIn >= $cirOut) ? $cirIn : $cirOut;

		$cirBits = ($cir * 1000);

		$img = $codInterface . "_" . $period . "_" . time() . ".png";
		$scales = $this->scales(1, $period);

		$args = array(
					'img' => $img,
					'period' => $period,
					'scales' => $scales,
					'name' => $name,
					'arq'  => $arq,
					'dir'  => $this->dir,
					'typeInterface' => $typeInterface,
					'serial'  => $serial,
					'cir'     => $cir,
					'cirBits' => $cirBits,
				);

		shell_exec($this->getRrdToolGraphCommand($args));

		return $img;
	}

    /**
    * Retorna gráfico de confiabilidade
    */
	private function graphReliability($id, $period = '6hours')
	{
		$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/rly/";

        $em = $this->getDoctrine()->getManager();

        $circuits = $em->getRepository('CocarBundle:Circuits')->find($id);

        $scales = $this->scales(2, $period);

		$codInterface  = $circuits->getCodeInterface();
		$typeInterface = $circuits->getTypeInterface();
		$cirIn  = $circuits->getCirIn();
		$cirOut = $circuits->getCirOut();
		$serial = $circuits->getSerialBackbone();

		$arq = $codInterface . "_rly.rrd";

		if(!$this->rrdExists($arq))
		{
			return new Response("<H1>Erro: A Unidade: <FONT COLOR='red'>" . $name . "</FONT> não esta sendo monitorada.</H1>");
		}

		if(!preg_match("/Eth/i", $serial))
		{
			$img = $codInterface . "_" . $period . "_rly_" . time() . ".png";

			if($typeInterface == "circuito"){
				$name = "Ponta - " . $serial . " - " . $scales['scale'] . " (" . $scales['media'] . ")";
			}else{
				$name = "Concentradora - " . $codInterface . " - " . $circuits->getSerialRouterTip() . " - " .
				    				$scales['scale'] . " (" . $scales['media'] . ")";
			}

			$args = array(
						'img' => $img,
						'period' => $period,
						'scales' => $scales,
						'name' => $name,
						'arq'  => $arq,
						'dir'  => $this->dir,
						'typeInterface' => $typeInterface,
						'serial'  => $serial,
					);

			shell_exec($this->getRlyToolGraphCommand($args));

			return $img;
		}
	}

	public function rrdExists($arq)
	{
		return (!file_exists($this->dir . $arq)) ? false : true;
	}

	public function scales($type = 1, $period = '6hours')
	{
		if($type == 1)
		{
			switch (strtolower($period))
			{
				case '6hours':		$scale = "6 Horas";	 $step=60;	   $media="1 min";		break;
				case '8hours':		$scale = "8 Horas";	 $step=60;	   $media="1 min";		break;
				case '1day': 		$scale = "1 Dia";	 $step=300;	   $media="5 min";		break;
				case '40hours':		$scale = "40 Horas"; $step=300;	   $media="5 min";		break;
				case '160hours':	$scale = "1 Semana"; $step=1200;   $media="20 min";		break;
				case '720hours':	$scale = "1 Mes";	 $step=5400;   $media="90 min";		break;
				case '4months': 	$scale = "4 Meses";	 $step=21600;  $media="360 min";	break;
				case '1year':		$scale = "1 Ano";	 $step=86400;  $media="1440 min";	break;
				default: 			$scale = $period; 	 $media="desconhecido";
			}
		}
		elseif($type == 2)
		{
			switch (strtolower($period))
			{
				case '6hours':		$scale = "6 Horas";	  $step=600;	$media="10 min";	break;
				case '8hours':		$scale = "8 Horas";	  $step=600;	$media="10 min";	break;
				case '1day': 		$scale = "1 Dia";	  $step=600;	$media="10 min";	break;
				case '40hours':		$scale = "40 Horas";  $step=600;	$media="10 min";	break;
				case '160hours':	$scale = "1 Semana";  $step=1200;	$media="20 min";	break;
				case '720hours':	$scale = "1 Mes";	  $step=5400;	$media="90 min";	break;
				case '4months': 	$scale = "4 Meses";	  $step=21600;	$media="360 min";	break;
				case '1year':		$scale = "1 Ano";	  $step=86400;	$media="1440 min";	break;
				default: 			$scale = $period; 	  $media="desconhecido";
			}
		}
		return array('scale' => $scale, 'step' => $step, 'media' => $media);
	}

	/**
	* @Route("/graphdailyperform", name="cocar_graphdailyperform")
	*/
	public function graphDailyPerfomAction()
	{	
		$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/";

        $em = $this->getDoctrine()->getManager();

		$hoje 	 = date('Y-m-d');
		$dia_fim = strtotime($hoje) - 86400;
		$dia_ini = $dia_fim - 2592000;
		$dia_ini = date('Y-m-d',$dia_ini);
		$dia_fim = date('Y-m-d',$dia_fim);

		$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

		foreach ($circuits as $cir)
		{
			$entityId = $cir->getEntity();
			$tech     = $cir->getTechnology();

	    	$fields = $em->createQuery(
				            	"SELECT d.cirIn, d.cirOut, d.day FROM CocarBundle:DailyPerformance d 
				            		WHERE (d.day >= :start 
				            			AND d.day <= :end)
	    								AND d.codeInterface = :code"
				        	)
	    					->setParameter('start', $dia_ini)
	    					->setParameter('end', $dia_fim)
	    					->setParameter('code', $cir->getId())
							->getResult();

			$i = 0;

			if($fields)
			{
				foreach ($fields as $field)
				{
					if($field['day']->format('d') >= 1)
					{
						if (($tech != "ETH") && ($field['cirOut'] < 2000))
						{
				    		$ydata3[$i] = $field['cirIn'];	//linhas com o cir
							$ydata4[$i] = $field['cirOut'];
						}
						else
						{
				    		$ydata3[$i] = 0; //linhas com o cir
							$ydata4[$i] = 0;
						}
						$i++;
					}
				}

				$this->createGraph(array("volume_in","volume_out","MBytes",1000,"volume","Volume - Ultimos 30 dias"),
								    array($ydata3, $ydata4, $dia_fim, $dia_ini, $cir->getId(), $fields));
				$this->createGraph(array("cir_in_rec","cir_out_rec","kbps",1,"cir_rec","Taxa = 95% amostras - 30 dias"), 
								    array($ydata3, $ydata4, $dia_fim, $dia_ini, $cir->getId(), $fields));
				
				$this->createGraph2(array("cir_out_rec","7_19_media_out","7_19_pico_out","kbps",1,"out_m95M","SAIDA APS (Ultimos 30 dias) - Media/Taxa95%/Max"),
									array($ydata3, $ydata4, $dia_fim, $dia_ini, $cir->getId(), $fields));

				$this->createGraph2(array("cir_in_rec","7_19_media_in","7_19_pico_in","kbps",1,"in_m95M", "ENTRADA APS (Ultimos 30 dias) - Media/Taxa95%/Max"),
									array($ydata3, $ydata4, $dia_fim, $dia_ini, $cir->getId(), $fields)); 

				$this->createGraphHMM(array("hmm_hour_in", "hmm_hour_out", "HMM"),
									array($ydata3, $ydata4, $dia_fim, $dia_ini, $cir->getId(), $fields));
				$this->createGraphHMM(array("hmm_hour_in_per", "hmm_hour_out_per", "HMMper"),
									array($ydata3, $ydata4, $dia_fim, $dia_ini, $cir->getId(), $fields));
			}
		}

        return new Response();
	}

	/**
	* @Route("/graphmonthlyperform", name="cocar_graph_monthlyperform")
	*/
	public function graphMonthlyPerfomAction()
	{	
		$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/";

        $em = $this->getDoctrine()->getManager();

		$circuits = $em->getRepository('CocarBundle:Circuits')->findAll();

		$mes_atual = date('Y-m-01');
		$mes_ini   = mktime (0, 0, 0, date("m")-7, date("d"),  date("Y"));
		$mes_ini   = date('Y-m-01', $mes_ini);

		foreach ($circuits as $cir)
		{
			$this->createGraphMonthly(array("volume_in","volume_out","GBytes",1,"volume","Volume Mensal"), 
							   array($cir->getId(), $mes_atual, $mes_ini));
			$this->createGraphMonthly(array("cir_in_rec","cir_out_rec","kbps",1,"cir_rec","Taxa = 95% amostras Mensal"), 
							   array($cir->getId(), $mes_atual, $mes_ini));
		}

		return new Response();
    }

	public function createGraph($params = array(), $params2 = array())
	{
		$em = $this->getDoctrine()->getManager();

		list($param1, $param2, $y_eixo, $k, $nome, $tipo) = $params;
		list($ydata3, $ydata4, $dia_fim, $dia_ini, $code, $circuit) = $params2;

    	$fields = $em->createQuery(
			            	"SELECT d.volumeIn, d.volumeOut, d.cirInRec, d.cirOutRec, d.day FROM CocarBundle:DailyPerformance d 
			            		WHERE (d.day >= :start 
			            			AND d.day <= :end)
    								AND d.codeInterface = :code"
			        	)
    					->setParameter('start', $dia_ini)
    					->setParameter('end', $dia_fim)
    					->setParameter('code', $code)
						->getResult();

		foreach ($fields as $f)
		{
			$p1 = ($param1 == 'volume_in') ? $f['volumeIn'] : $f['cirInRec'];
			$p2 = ($param2 == 'volume_out') ? $f['volumeOut'] : $f['cirOutRec'];

		    $data1y[] = $p1/$k;
		    $data2y[] = $p2/$k;

			$a[] 	  = $f['day']->format('d/m');
		}

		if(isset($data1y))
		{
			// Create the graph. These two calls are always required
			$graph = new \Graph(580, 280, "auto");
			$graph->SetScale("textlin");
			$graph->img->SetMargin(60, 10, 5, 60);

			// Create the bar plots
			$b1plot = new \BarPlot($data1y);
			$b1plot->SetFillColor("green:0.8");
			$b1plot->SetWeight(0);

			$b2plot = new \BarPlot($data2y);
			$b2plot->SetFillColor("blue");
			$b2plot->SetWeight(0);

			$graph->title->Set("$tipo");
			$graph->yaxis->title->Set($y_eixo);

			$graph->title->SetFont(FF_FONT1, FS_BOLD);
			$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
			$graph->xaxis->SetTickLabels($a);
			$graph->xaxis->SetLabelAngle(90);

			if ($tipo == "Taxa = 95% amostras - 30 dias"){
				//acrescenta linhas de cir
				$lineplot3 = new \LinePlot($ydata3);  
				$lineplot4 = new \LinePlot($ydata4); 

				$graph->Add($lineplot4);
				$graph->Add($lineplot3);

				$lineplot3->SetColor("green:0.8");
				$lineplot3->SetWeight(1);

				$lineplot4->SetColor("blue");
				$lineplot4->SetWeight(1);	
			}

			// Set the legends for the plots
			$b1plot->SetLegend("APS>");
			$b2plot->SetLegend(">APS");

			// Adjust the legend position
			$graph->legend->SetLayout(LEGEND_HOR);
			$graph->legend->Pos(0.02, 0.07, "right", "center");
			$graph->legend->SetFrameWeight(0);
			$graph->legend->SetShadow(0);

			// Create the grouped bar plot
			$gbplot = new \GroupBarPlot(array($b1plot,$b2plot));

			// ...and add it to the graPH
			$graph->Add($gbplot);

			//Display the graph
			$graph->SetFrame(false);

			$nome_graf = $this->dir."graficos/daily/".$code."_".$nome.".png";
			$graph->Stroke($nome_graf);
		}
	}

	public function createGraph2($params = array(), $params2 = array())
	{
		$em = $this->getDoctrine()->getManager();

		list($param1, $param2, $param3, $y_eixo, $k, $nome, $tipo) = $params;
		list($ydata3, $ydata4, $dia_fim, $dia_ini, $code, $circuit) = $params2;

    	$fields = $em->createQuery(
			            	"SELECT d._719MediaOut, d._719MediaIn, d._719PeakOut, d._719PeakIn, 
			            		d.cirInRec, d.cirOutRec, d.day FROM CocarBundle:DailyPerformance d 
			            		WHERE (d.day >= :start 
			            			AND d.day <= :end)
    								AND d.codeInterface = :code"
			        	)
    					->setParameter('start', $dia_ini)
    					->setParameter('end', $dia_fim)
    					->setParameter('code', $code)
						->getResult();

		$i = 0;
		foreach ($fields as $f)
		{
			$p1 = ($param1 == 'cir_out_rec') ? $f['cirOutRec'] : $f['cirInRec'];
			$p2 = ($param2 == '7_19_media_out') ? $f['_719MediaOut'] : $f['_719MediaIn'];
			$p3 = ($param3 == '7_19_pico_out') ? $f['_719PeakOut'] : $f['_719PeakIn'];

		    $ydata = ($param1 == 'cir_out_rec') ? $ydata4[$i] : $ydata3[$i];
		    $datay[] = $ydata/20 + $p1/$k;
		    $datay[] = $p1/$k;
		    $datay[] = $p2/$k;
		    $datay[] = $p3/$k;

			$a[]     = $f['day']->format('d/m');
			$i++;
		}

		// Create the graph. These two calls are always required
		$graph = new \Graph(580, 280, "auto");
		$graph->SetScale("textlin");
		$graph->img->SetMargin(60, 10, 5, 60);

		// Create the bar plots
		$b1plot = new \StockPlot($datay);

		$b1plot->SetWidth(9);

		$graph->title->Set("$tipo");
		$graph->yaxis->title->Set($y_eixo);

		$graph->title->SetFont(FF_FONT1, FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
		$graph->xaxis->SetTickLabels($a);
		$graph->xaxis->SetLabelAngle(90);

		//acrescenta linhas de cir
		$lineplot3 = new \LinePlot($ydata3);  
		$lineplot4 = new \LinePlot($ydata4);  
	
		$lineplot3->SetColor("red");
		$lineplot3->SetWeight(1);
		
		$lineplot4->SetColor("red");
		$lineplot4->SetWeight(1);	
		
		if ($tipo == "ENTRADA APS (últimos 30 dias) - Média/Taxa95%/Máx") {
			$graph->Add($lineplot4);
		}else{
			$graph->Add($lineplot3);
		}

		$b1plot->SetWeight(2);
		$b1plot->SetColor('blue','blue','orange','red');

		// ...and add it to the graPH
		$graph->Add($b1plot);

		//Display the graph
		$graph->SetFrame(false);

		$nome_graf = $this->dir."graficos/daily/".$code."_".$nome.".png";
		$graph->Stroke($nome_graf);
	}

	public function createGraphHMM($params = array(), $params2 = array())
	{
		$em = $this->getDoctrine()->getManager();

		list($param1, $param2, $nome) = $params;
		list($ydata3, $ydata4, $dia_fim, $dia_ini, $code, $circuit) = $params2;

    	$fields = $em->createQuery(
			            	"SELECT d.hmmHourIn, d.hmmHourOut, d.hmmHourInPer, d.hmmHourOutPer, d.day FROM CocarBundle:DailyPerformance d 
			            		WHERE (d.day >= :start 
			            			AND d.day <= :end)
    								AND d.codeInterface = :code"
			        	)
    					->setParameter('start', $dia_ini)
    					->setParameter('end', $dia_fim)
    					->setParameter('code', $code)
						->getResult();

		foreach ($fields as $f)
		{
			$p1 = ($param1 == 'hmm_hour_in') ? $f['hmmHourIn'] : $f['hmmHourOut'];
			$p2 = ($param2 == 'hmm_hour_in_per') ? $f['hmmHourInPer'] : $f['hmmHourOutPer'];

			$ydata[]  = $p1->format('H');
			$ydata2[] = $p2->format('H');
			$a[]      = $f['day']->format('d/m');
		}

		// Create the graph. These two calls are always required
		$graph = new \Graph(580, 280, "auto");
		$graph->SetScale("textlin");
		$graph->img->SetMargin(60, 10, 5, 60);

		$lineplot  = new \LinePlot($ydata);
		$lineplot2 = new \LinePlot($ydata2);  

		// Adiciona a linha ao grafico
		$graph->Add($lineplot);
		$graph->Add($lineplot2);

		$graph->title->SetFont(FF_FONT1, FS_BOLD);
		$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
		$graph->yaxis->title->Set("hora");
		$graph->xaxis->SetTickLabels($a); 
		$graph->xaxis->SetLabelAngle(90);

		$lineplot->SetColor("green:0.8");
		$lineplot->SetWeight(1);
		$lineplot->mark->SetType(MARK_SQUARE);

		$lineplot2->SetColor("blue");
		$lineplot2->SetWeight(1);
		$lineplot2->mark->SetType(MARK_SQUARE);
		$lineplot->mark->SetFillColor("green");

		$graph->title->Set("Horario inicial da HMM");

		// Set the legends for the plots
		$lineplot->SetLegend("->APS");
		$lineplot2->SetLegend("APS->");

		// Adjust the legend position
		$graph->legend->SetLayout(LEGEND_HOR);
		$graph->legend->Pos(0.02,0.06,"right","center");
		$graph->legend->SetFrameWeight(0);
		$graph->legend->SetShadow(0);

		//grava figura
		$graph->SetFrame(false);
		$nome_graf = $this->dir."graficos/daily/".$code."_".$nome.".png";
		$graph->Stroke($nome_graf);
	}

	public function createGraphMonthly($params = array(), $params2 = array())
	{
		$em = $this->getDoctrine()->getManager();

		list($param1, $param2, $y_eixo, $k, $nome, $tipo) = $params;
		list($code, $mes_atual, $mes_ini) = $params2;

    	$fields = $em->createQuery(
			            	"SELECT d.volumeIn, d.volumeOut, d.cirIn, d.cirOut, d.cirInRec, d.cirOutRec, d.date FROM CocarBundle:MonthlyPerformance d 
			            		WHERE (d.codeInterface = :code
			            			AND d.date < :currentMonth AND d.date > :startDate) ORDER BY d.date ASC"
			        	)
    					->setParameter('code', $code)
    					->setParameter('currentMonth', $mes_atual)
    					->setParameter('startDate', $mes_ini)
						->getResult();

		foreach ($fields as $f)
		{
			$p1 = ($param1 == 'volume_in') ? $f['volumeIn'] : $f['cirInRec'];
			$p2 = ($param2 == 'volume_out') ? $f['volumeOut'] : $f['cirOutRec'];

		    $data1y[] = $p1/$k;
		    $data2y[] = $p2/$k;

			$ydata3[] = $f['cirIn'];
			$ydata4[] = $f['cirOut'];

			$a[] 	  = $f['date']->format('m/Y');
		}

		if(isset($data1y))
		{
			// Create the graph. These two calls are always required
			$graph = new \Graph(580, 280, "auto");
			$graph->SetScale("textlin");
			$graph->img->SetMargin(60, 10, 5, 60);

			// Create the bar plots
			$b1plot = new \BarPlot($data1y);
			$b1plot->SetFillColor("green:0.8");
			$b1plot->SetWeight(0);

			$b2plot = new \BarPlot($data2y);
			$b2plot->SetFillColor("blue");
			$b2plot->SetWeight(0);

			$graph->title->Set("$tipo");
			$graph->yaxis->title->Set($y_eixo);

			$graph->title->SetFont(FF_FONT1, FS_BOLD);
			$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
			$graph->xaxis->SetTickLabels($a);
			$graph->xaxis->SetLabelAngle(90);

			// Set the legends for the plots
			$b1plot->SetLegend("APS>");
			$b2plot->SetLegend(">APS");

			// Adjust the legend position
			$graph->legend->SetLayout(LEGEND_HOR);
			$graph->legend->Pos(0.01,0.1,"right","center");
			$graph->legend->SetFrameWeight(0);
			$graph->legend->SetShadow(0);

			// Create the grouped bar plot
			$gbplot = new \GroupBarPlot(array($b1plot,$b2plot));

			// ...and add it to the graPH
			$graph->Add($gbplot);

			if ($tipo == "CIR Mensal recomendado"){
				//apanha o cir in e cir out
				$lineplot3 = new \LinePlot($ydata3);  
				$lineplot4 = new \LinePlot($ydata4);  
				$graph->Add($lineplot4);
				$graph->Add($lineplot3);
				$lineplot3->SetColor("green:0.8");
				$lineplot3->SetWeight(1);
				$lineplot4->SetColor("blue");
				$lineplot4->SetWeight(1);	
			}

			//Display the graph
			$graph->SetFrame(false);

			$nome_graf = $this->dir."graficos/monthly/".$code."_".$nome.".png";
			$graph->Stroke($nome_graf);
		}
	}

	public function getRrdToolGraphCommand($args = array(), $hrule = false)
	{
		extract($args, EXTR_PREFIX_SAME, "wddx");

        $com = "rrdtool graph $this->dir" . "graficos/" . $img .
                    " --start -". $period . " --end now --step ". $scales['step'] .
                    " --title='" . $name . " - " . $scales['scale'] . " (" . $scales['media'] . ")' ".
                    "--vertical-label 'Trafego em Bits/s' " .
                    "--width 480 --height 162 " .
                    "DEF:in=" . $this->dir . $arq . ":ds0:AVERAGE " .
                    "DEF:out=" . $this->dir . $arq . ":ds1:AVERAGE " .
                    "CDEF:bitIn=in,8,* " .
                    "CDEF:bitOut=out,8,* " .
					"COMMENT:'        ' ";

		if($hrule)
		{
			if($typeInterface == "circuito" && !preg_match("/ETH/i", $serial))
			{
				$com .= "HRULE:$cirBits#FF0000:'CIR = $cir  ' " .
			  			    "COMMENT:'\\n' " .
			  			    "COMMENT:'        ' ";
			}
		}

		$com .= "COMMENT:'        ' ".
                    "AREA:bitIn#00CC00:'Entrada ' " .
                    "LINE1:bitOut#0000FF:'Saida ' " .
					"COMMENT:'\\n' ".
					"COMMENT:'        ' ".
					"GPRINT:bitIn:MAX:'Maximo\\:%14.1lf %sbit/s' ".
					"GPRINT:bitOut:MAX:'%11.1lf %sbit/s' ".
					"COMMENT:'\\n' ".
					"COMMENT:'        ' ".
					"GPRINT:bitIn:AVERAGE:'Media\\:%15.1lf %sbit/s' ".
					"GPRINT:bitOut:AVERAGE:'%11.1lf %sbit/s' ".
					"COMMENT:'\\n' ".
					"COMMENT:'        ' ".
					"GPRINT:bitIn:LAST:'Ultima\\:%14.1lf %sbit/s' " .
					"GPRINT:bitOut:LAST:'%11.1lf %sbit/s' ";

		return $com;
	}

	private function getRlyToolGraphCommand($args = array())
	{
		extract($args, EXTR_PREFIX_SAME, "wddx");

		$com = "rrdtool graph $this->dir" . "../graficos/". $img .
			" --start -". $period . " --end now --step ". $scales['step'] .
			" --title='" . $name . "' ".
			"--vertical-label 'Confiabilidade' -w 480 -h 162 " .
			"DEF:myrly=" . $this->dir . $arq . ":rly:AVERAGE " .
			"CDEF:valor=myrly " .
			"CDEF:ideal=valor,255,EQ,valor,0,IF " .
			"CDEF:baixo=valor,255,EQ,0,valor,IF " .
			"HRULE:255#0000FF:'Valor Ideal = 255       ' " .
			"AREA:ideal#80FF80:'Normal       ' "  .
			"AREA:baixo#FE3C36:'Critico\\c' " .
			"COMMENT:'\\n' ".
			"COMMENT:'        ' ".
			"GPRINT:valor:MIN:'Valor Minimo = %10.0lf' " .
			"COMMENT:'\\n' ".
			"COMMENT:'        ' ".
			"GPRINT:valor:LAST:'Ultimo Valor = %10.0lf' ";

		return $com;
	}

    /**
    * @Route("/report/{id}", name="cocar_report")
    * @Template()
    */
    public function reportAction($id)
    {
		$em = $this->getDoctrine()->getManager();

		$circuit = $em->getRepository('CocarBundle:Circuits')->findByEntity($id);

	    $form = $this->reportForm($id);

		return array('circuit' => $circuit, 'form' => $form->createView());
    }

    /**
    * @Route("/reportshow", name="cocar_reportshow")
	* @Method("POST")
    */
    public function reportShowAction(Request $request)
    {
		$form = $this->reportForm($request->request->get('entity'));

        $form->bind($request);

        if ($form->isValid())
        {
        	$em = $this->getDoctrine()->getManager();

        	$data = $form->getData();

        	$circuit = $em->getRepository('CocarBundle:Circuits')->findByEntity($data['entity']);

        	switch ($data['type']) {
        		case 'taxa':
        			$name  = $request->request->get('circuit') . "_cir_rec.png";
        			$img[] = "monthly/" . $name;
        			$img[] = "daily/" . $name;
        			$type  = "Taxa = 95% amostras";
        			break;
        		case 'volume':
        			$name  = $request->request->get('circuit') . "_volume.png";
        			$img[] = "monthly/" . $name;
        			$img[] = "daily/" . $name;
        			$type  = "Volume";
        			break;
        		case 'hmm_day':
        			$name  = $request->request->get('circuit') . "_HMM.png";
        			$img[] = "daily/" . $name;
        			$type  = "HMM do dia";
        			break;
        		case 'hmm_per':
        			$name  = $request->request->get('circuit') . "_HMMper.png";
        			$img[] = "daily/" . $name;
        			$type  = "HMM do período";
        			break;
        		case 'med_in':
        			$name  = $request->request->get('circuit') . "_in_m95M.png";
        			$img[] = "daily/" . $name;
        			$type = "Med 95% Max - Entrada";
        			break;
        		case 'med_out':
        			$name  = $request->request->get('circuit') . "_out_m95M.png";
        			$img[] = "daily/" . $name;
        			$type  = "Med 95% Max - Saida";
        			break;

        		default:
        			$img[] = array();
        			break;
        	}
        }

        return $this->render('CocarBundle:Graph:report.html.twig', 
			  		array('form' => $form->createView(), 'circuit' => $circuit, 
			  			'img' => $img, 'type' => $type, 'id' => $request->request->get('circuit')));
    }

    /**
     * @param mixed $id the circuit id
     *
     * @return \Symfony\Component\Form\Form the form
     */
    private function reportForm($id)
    {
        return $this->createFormBuilder()
	        ->add('type', 'choice', array(
	                'choices' => array(
	                    'taxa'   => 'Taxa = 95% amostras',
	                    'volume'   => 'Volume',
	                    'hmm_day'     => 'HMM do dia',
	                    'hmm_per'  => 'HMM do período',
	                    'med_in' => 'Med 95% Max - Entrada',
	                    'med_out' => 'Med 95% Max - Saida',
	                ),
			        'required' => true,
	        ))
	        ->add('entity', 'hidden', array(
	        		'data' => $id
	        ))
	        ->add('send', 'submit', array('label' => 'Enviar' ))
	        ->getForm();
    }

    /**
    * Retorna gráfico do tráfego das máquinas
    */
	private function graphMachine($id, $period = '6hours')
	{
		$this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/machine/" . $id . "/";

		if(is_dir($this->dir))
		{
			if(!is_dir($this->dir . "graficos/"))
				mkdir($this->dir . "graficos/");

	        $em = $this->getDoctrine()->getManager();

	        $circuits = $em->getRepository('CocarBundle:Circuits')->find($id);

	        $machines = $em->createQuery(
					            	"SELECT DISTINCT  m.macAddress, m.ip FROM CocarBundle:Machine m 
					            		WHERE m.gateway = :gateway
					            		GROUP BY m.macAddress, m.ip, m.id"
					        	)
		    					->setParameter('gateway', $id)
								->getResult();

			$img = array();

	        foreach ($machines as $machine)
	        {
				$typeInterface = $circuits->getTypeInterface();
				$cirIn  = $circuits->getCirIn();
				$cirOut = $circuits->getCirOut();
				$serial = $circuits->getSerialBackbone();

				$ip     = $machine['ip'];
				$mcAddr = $machine['macAddress'];

		    	$name   = $circuits->getCodeInterface() . " ($ip) - $mcAddr";
				$arq    = str_replace(":", "", $mcAddr . ".rrd");

				if(!$this->rrdExists($arq)){
					return new Response("<H1>Erro: A Unidade: <FONT COLOR='red'>" . $name . "</FONT> não esta sendo monitorada.</H1>");
				}

				$cir = ($cirIn >= $cirOut) ? $cirIn : $cirOut;

				$cirBits = ($cir * 1000);

				$image = str_replace(":", "", $mcAddr) . "_concentradora_" . $period . "_" . time() . ".png";
				$scales = $this->scales(1, $period);

				$args = array(
							'img' => $image,
							'period' => $period,
							'scales' => $scales,
							'name' => $name,
							'arq'  => $arq,
							'dir'  => $this->dir,
							'typeInterface' => $typeInterface,
							'serial'  => $serial,
							'cir'     => $cir,
							'cirBits' => $cirBits,
						);

				shell_exec($this->getRrdToolGraphCommand($args, true));

				$img[] = "../machine/$id/graficos/$image";
			}

		return $img;
		}
		else
		{
			return new Response("<H1>Não existem máquinas cadastradas para esse circuito!</H1>");
		}
	}
}
