<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Swpb\Bundle\CocarBundle\Entity\Printer;
use Swpb\Bundle\CocarBundle\Entity\PrinterCounter;
use Swpb\Bundle\CocarBundle\Entity\PrinterCounterRepository;
use Swpb\Bundle\CocarBundle\Form\PrinterType;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Writer\CsvWriter;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
/**
 * Printer controller.
 *
 * @Route("/printer")
 */
class PrinterController extends Controller
{

    private $em;

    public function __construct(EntityManager $em = null)
    {
        $this->em = $em;
    }

    /**
     * Lists all Printer entities.
     *
     * @Route("/", name="printer_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        ini_set('memory_limit', '1024M');
        gc_enable();

        $em = $this->getDoctrine()->getManager();

        $form = $request->query->get('form');

        if($form)
        {
            $start = new \DateTime($form['startDate']);

            $start = $start->format('U');

            $end = new \DateTime($form['endDate']);
            $end->setTime('23', '59', '59');
            $end = $end->format('U');
        }

        $start = isset($start) ? $start : (time() - ((60*60*24)*30));
        $end   = isset($end) ? $end : time();

        $data = new \DateTime();

        $printers = $em->getRepository('CocarBundle:PrinterCounter')->relatorioGeral($start, $end);

        return array(
            "printer" => $printers,
            //"printerCounter" => $pCounter,
            "form" => $this->createCalendarForm(0, new \DateTime(date("Y-m-d", $start)), new \DateTime(date("Y-m-d", $end)))->createView(),
            "data" => $data,
            "start" => $start,
            "end" => $end
        );

    }

    /**
     * Generate a CSV file
     *
     * @Route("/csv", name="printer_csv")
     *
     */
    public function csvAction(Request $request)
    {
        ini_set('memory_limit', '1024M');
        gc_enable();

        $data = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        $form = $request->query->get('form');

        if($form)
        {
            $start = new \DateTime($form['startDate']);
            $startcsv = $start->format('d/m/Y');
            $start = $start->format('U');

            $end = new \DateTime($form['endDate']);
            $end->setTime('23', '59', '59');
            $endcsv = $end->format('d/m/Y');
            $end = $end->format('U');
        }

        $start = isset($start) ? $start : (time() - ((60*60*24)*30));
        $end   = isset($end) ? $end : time();

        $printers = $em->getRepository('CocarBundle:PrinterCounter')->relatorioCsvGeral($start, $end);

        // Gera cabeçalho
        $cabecalho = array();
        foreach($printers as $elm) {
            array_push($cabecalho, array_keys($elm));
            break;
        }
        // Adiciona contagem no cabeçalho
        array_push($cabecalho[0], 'totalPrints');

        // Gera CSV
        $reader = new ArrayReader(array_merge($cabecalho, $printers));

        // Gera CSV
        //$reader = new ArrayReader($printers);

        // Create the workflow from the reader
        $workflow = new Workflow($reader);
        $data = new \DateTime();

        // Contador geral das impressorasa
        $converter = new CallbackItemConverter(function ($item) {
            if (array_key_exists('printsEnd', $item) && array_key_exists('printsStart', $item)) {
                $item['totalPrints'] = $item['printsEnd'] - $item['printsStart'];
            } else {
                $item['totalPrints'] = null;
            }
            return $item;
        });
        $workflow->addItemConverter($converter);

        // As you can see, the first names are not capitalized correctly. Let's fix
        // that with a value converter:
        //$converter = new CallbackValueConverter(function ($input) {
        //    return date('d/m/Y', $input);
        //});
        //$workflow->addValueConverter('endDate', $converter);
        //$workflow->addValueConverter('startDate', $converter);

        // Add the writer to the workflow
        $tmpfile = tempnam(sys_get_temp_dir(), 'impressoras');
        $file = new \SplFileObject($tmpfile, 'w');
        $writer = new CsvWriter($file);
        $writer->writeItem(array('','Relatório gerado em',$data->format('d/m/Y'),$data->format('H:i:s')));
        $writer->writeItem(array('','Data Inicial:',$startcsv,'00:00:00'));
        $writer->writeItem(array('','Data Final:',$endcsv,'23:59:59'));
        $writer->writeItem(array(''));
        $writer->writeItem(array('Id', 'Nome','Host','Serie','Local','Contador Inicial','Contador Final','Impressões'));
        $workflow->addWriter($writer);

        // Process the workflow
        $workflow->process();

        // Retorna o arquivo
        $response = new BinaryFileResponse($tmpfile);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="impressoras.csv"');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        return $response;
    }

    /**
     * Lists all Printer entities.
     *
     * @Route("/detalhado", name="printer_index_detalhado")
     * @Method("GET")
     * @Template()
     */
    public function indexDetalhadoAction(Request $request)
    {
        ini_set('memory_limit', '1024M');
        gc_enable();

        $em = $this->getDoctrine()->getManager();

        $form = $request->query->get('form');

        if($form)
        {
            $start = new \DateTime($form['startDate']);
            $start = $start->format('U');

            $end = new \DateTime($form['endDate']);
            $end->setTime('23', '59', '59');
            $end = $end->format('U');
        }

        $start = isset($start) ? $start : (time() - ((60*60*24)*30));
        $end   = isset($end) ? $end : time();


        $displayAll = true;

        $printers = $em->getRepository('CocarBundle:PrinterCounter')->relatorioGeral($start, $end);
        if(!$displayAll)
        {
            $paginator = $this->get('knp_paginator');
            $printers  = $paginator->paginate($printers, $this->get('request')->query->get('page', 1), 10);
        }

        return array(
            "printer" => $printers,
            //"printerCounter" => $pCounter,
            "form" => $this->createCalendarForm(0, new \DateTime(date("Y-m-d", $start)), new \DateTime(date("Y-m-d", $end)))->createView(),
            "start" => $start,
            "end" => $end,
            "displayAll" => $displayAll
        );

    }

    /**
     * Generate a CSV file
     *
     * @Route("/detalhado/csv", name="printer_csv_detalhado")
     *
     */
    public function csvDetalhadoAction(Request $request)
    {
        ini_set('memory_limit', '1024M');
        gc_enable();

        $em = $this->getDoctrine()->getManager();

        $form = $request->query->get('form');

        if($form)
        {
            $start = new \DateTime($form['startDate']);
            $start = $start->format('U');

            $end = new \DateTime($form['endDate']);
            $end->setTime('23', '59', '59');
            $end = $end->format('U');

            $this->get('logger')->debug("Relatório CSV Detalhado. Início = " . $form['startDate'] . " Fim = " . $form['endDate']);
        }

        $start = isset($start) ? $start : (time() - ((60*60*24)*30));
        $end   = isset($end) ? $end : time();

        $printers = $em->getRepository('CocarBundle:PrinterCounter')->relatorioCsvGeralDetalhado($start, $end);

        // Gera cabeçalho
        $cabecalho = array();
        foreach($printers as $elm) {
            array_push($cabecalho, array_keys($elm));
            break;
        }
        // Adiciona contagem no cabeçalho
        array_push($cabecalho[0], 'totalPrints');

        // Gera CSV
        $reader = new ArrayReader(array_merge($cabecalho, $printers));

        // Gera CSV
        //$reader = new ArrayReader($printers);

        // Create the workflow from the reader
        $workflow = new Workflow($reader);

        // Contador geral das impressorasa
        $converter = new CallbackItemConverter(function ($item) {
            if (array_key_exists('printsEnd', $item) && array_key_exists('printsStart', $item)) {
                $item['totalPrints'] = $item['printsEnd'] - $item['printsStart'];
            } else {
                $item['totalPrints'] = null;
            }
            return $item;
        });
        $workflow->addItemConverter($converter);

        // As you can see, the first names are not capitalized correctly. Let's fix
        // that with a value converter:
        $converter = new CallbackValueConverter(function ($input) {
            return date('d/m/Y', $input);
        });
        $workflow->addValueConverter('endDate', $converter);
        $workflow->addValueConverter('startDate', $converter);

        // Add the writer to the workflow
        $tmpfile = tempnam(sys_get_temp_dir(), 'impressoras');
        $file = new \SplFileObject($tmpfile, 'w');
        $writer = new CsvWriter($file);
        $workflow->addWriter($writer);

        // Process the workflow
        $workflow->process();

        // Retorna o arquivo
        $response = new BinaryFileResponse($tmpfile);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="impressoras.csv"');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        return $response;
    }

    /**
     * Creates a new Printer entity.
     *
     * @Route("/", name="printer_create")
     * @Method("POST")
     * @Template("CocarBundle:Printer:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $logger = $this->get('logger');
        $entity = new Printer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $cacic = $this->container->get('kernel')->getBundle('CacicCommonBundle');
        $logger->debug("Registro do Bundle do CACIC: ".$cacic);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            // Se o Cacic existir, preenche o local automaticamente
            $local = $entity->getLocal();
            if (!empty($cacic) && empty($local)) {
                $rede = $em->getRepository('CacicCommonBundle:Rede')->getDadosRedePreColeta(
                    $entity->getHost()
                );
                $entity->setLocal($rede->getNmRede());
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('printer_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'cacic' => $cacic,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Printer entity.
    *
    * @param Printer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Printer $entity)
    {
        $form = $this->createForm(new PrinterType(), $entity, array(
            'action' => $this->generateUrl('printer_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Printer entity.
     *
     * @Route("/new", name="printer_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Printer();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Printer entity.
     *
     * @Route("/{id}", name="printer_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Printer')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Printer entity.');
        }

        // Show printer log
        $log = $em->getRepository('CocarBundle:PrinterCounter')->findBy(array('printer' => $id), array('date' => 'desc'));

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'log'         => $log
        );
    }

    /**
     * Displays a form to edit an existing Printer entity.
     *
     * @Route("/{id}/edit", name="printer_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $cacic = $this->container->get('kernel')->getBundle('CacicCommonBundle');

        $entity = $em->getRepository('CocarBundle:Printer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Printer entity.');
        }

        // Se o Cacic existir, preenche o local automaticamente
        $local = $entity->getLocal();
        if (!empty($cacic) && empty($local)) {
            $rede = $em->getRepository('CacicCommonBundle:Rede')->getDadosRedePreColeta(
                $entity->getHost()
            );
            $logger->debug("Valor default para o campo local: ".$rede->getNmRede());
            $entity->setLocal($rede->getNmRede());
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );
    }

    /**
    * Creates a form to edit a Printer entity.
    *
    * @param Printer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Printer $entity)
    {
        $form = $this->createForm(new PrinterType(), $entity, array(
            'action' => $this->generateUrl('printer_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edit an existing Printer entity.
     *
     * @Route("/{id}", name="printer_update")
     * @Method("PUT")
     * @Template("CocarBundle:Printer:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Printer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Printer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('printer_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Printer entity.
     *
     * @Route("/{id}", name="printer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CocarBundle:Printer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Printer entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('printer'));
    }

    /**
     * Creates a form to delete a Printer entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('printer_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
    * @Route("/reports/{id}", name="cocar_printer_reports")
    * @Template()
    */
    public function reportsAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $request->request->get('form');

        if($request->request->get('form'))
        {
            $start = new \DateTime($form['startDate']);
            $start = $start->format('U');

            $end = new \DateTime($form['endDate']);
            $end = $end->format('U');
        }

        $start = isset($start) ? $start : (time() - ((60*60*24)*30));
        $end   = isset($end) ? $end : time();

        $printerCounter = $em->createQuery(
                        "SELECT pc.id, pc.prints, pc.blackInk, pc.coloredInk FROM CocarBundle:PrinterCounter pc
                            WHERE (pc.date >= :start AND pc.date <= :end) AND (pc.printer = :id)
                            ORDER BY pc.id ASC"
                    )
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->setParameter('id', $id)
                    ->getResult();

        $pCounter = array();

        $size = sizeof($printerCounter)-1;

        foreach ($printerCounter as $counter)
        {
            $pCounter['prints'] = $printerCounter[$size]['prints'] - $printerCounter[0]['prints'];
            $pCounter['blackInk'] = $counter['blackInk'];
            $pCounter['coloredInk'] = $counter['coloredInk'];
        }

        $printer = $em->getRepository('CocarBundle:Printer')->find($id);

        return array(
            "printer" => $printer,
            "printerCounter" => $pCounter,
            "form" => $this->createCalendarForm($id, new \DateTime(date("Y-m-d", $start)), new \DateTime(date("Y-m-d", $end)))->createView(),
            "start" => $start,
            "end" => $end
        );
    }

    /**
     * Creates a form to reports
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCalendarForm($id, $start, $end)
    {
        return $this->createFormBuilder()
            ->add('startDate', 'date', array('widget' => 'single_text', 'data' => $start))
            ->add('endDate', 'date', array('widget' => 'single_text', 'data' => $end))
            ->add('printer', 'hidden', array(
                    'data' => $id
            ))
            ->getForm();
    }

    /**
     * Totalize results by limit and offset parameters
     *
     * @return Response
     */
    public function totalizer()
    {
        $n_printers = $this->em->createQuery('SELECT count(p) FROM CocarBundle:Printer p')->getSingleScalarResult();

        $limit = 20;
        $iterations = (int)($n_printers / $limit);
        $iterations = $iterations + 1;

        $i = 0;
        while ($i < $iterations) {
            // O objetivo é executar a coleta a cada $limit impressoras
            $offset = $limit * $i;
            $this->selectPrinters($limit, $offset);
            $i = $i + 1;
        }


        return new Response();
    }

    /**
     * Select printers by offset and limit
     */
    private function selectPrinters($limit, $offset) {
        ini_set('memory_limit', '1024M');
        gc_enable();

        $printers = $this->em
            ->createQuery('SELECT p FROM CocarBundle:Printer p ORDER BY p.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();

        //$printers = $this->em->getRepository('CocarBundle:Printer')->findAll();

        echo "Executando coleta de $limit impressoras começando no número $offset\n";

        $memory_start = memory_get_usage();
        foreach($printers as $printer)
        {
            try
            {
                $community = $printer->getCommunitySnmpPrinter();
                $host = $printer->getHost();

				$this->get('logger')->info("Coletando impressora $host | ID ".$printer->getId());
				$arrBundle = $this->get('kernel')->getBundles();
				$rootDir = $arrBundle['CocarBundle']->getPath();
				$script = $rootDir . "/Resources/scripts/timeout3";

                $com = "$script snmpwalk -O qv -v 1 -c $community $host 1.3.6.1.2.1.43.10.2.1.4.1.1";

                if($outPut = shell_exec($com))
                {
                    $this->updateCounter($printer, $outPut);
                    #$this->createOrUpdateGraph($printer, $outPut);
                }
            }
            catch(Exception $e)
            {
                #return new Response($e->getMessage());
                $this->get('logger')->error("Erro na coleta da impressora $host \n".$e->getMessage());
            }
        }
        echo "$limit impressoras lidas iniciando em $offset \t -- Memory: " . round((memory_get_usage()-$memory_start)/1024/1024, 2) ." mb\n";

        $this->em->flush();
    }

    /**
    * Update table by id
    */
    private function updateCounter($printer, $prints)
    {
        try
        {
            // First try to find printer on the same date
            $time = time();
            $counter = $this->getDoctrine()->getManager()->getRepository('CocarBundle:PrinterCounter')->findBy(array(
                'printer' => $printer,
                'date' => $time
            ));

            if(empty($counter)) {
                $counter = new PrinterCounter;
            } else {
                $this->get('logger')->error("Entrada repetida para impressora $printer e data $time");
                return true;
            }

            $counter->setPrinter($printer);
            $counter->setPrints($prints);
            $counter->setDate($time);

            $this->em->persist($counter);
        }
        catch(\Exception $e)
        {
            return false;
        }
        return true;
    }

    /**
    * Update graphic counter by id
    */
    private function createOrUpdateGraph($printer, $prints)
    {
        try
        {
            $this->dir = $this->get('kernel')->getRootDir() . "/../web/rrd/graficos/";

            $arqRrd = $this->dir . "printer_" . $printer->getId() . '.rrd';

            if (!file_exists($arqRrd))
                $this->createRrd($arqRrd);
            $this->updateRrd($arqRrd, $prints);

        }
        catch(\Exception $e)
        {
            return false;
        }
        return true;
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
                    echo $create;die;
        shell_exec($create);
    }

    public function updateRrd($arqRrd, $prints, $date = null)
    {
        $date = empty($date) ? date('U') : $date;
        shell_exec("rrdtool update $arqRrd $date:$prints:0");
    }
}
