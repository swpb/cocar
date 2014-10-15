<?php
/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 14/10/14
 * Time: 01:36
 */

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swpb\Bundle\CocarBundle\Entity\PrinterCounter;
use Swpb\Bundle\CocarBundle\Entity\Printer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class ApiController
 * @package Swpb\Bundle\CocarBundle\Controller
 *
 * @Route("/api")
 */
class ApiController extends Controller {

    /**
     * @Route("/login", name="api_login")
     * @Method("POST")
     * Faz login do agente do Cocar
     */
    public function loginAction(Request $request)
    {
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $data = $request->getContent();

        $session = $request->getSession();
        $session->start();

        $chavecrip = '123456';

        $usuario = $this->get('security.context')->getToken()->getUser();
        $logger->debug("Usuario encontrado: ".$usuario->getUserName());

        $auth = new JsonResponse();
        $auth->setContent(json_encode(array(
            'session' => $session->getId(),
            'chavecrip' => $usuario->getApiKey()
        )));

        return $auth;
    }

    /**
     * @param $ip_addr
     * @Route("/printer/{ip_addr}", name="printer_counter_update")
     * @Method("POST")
     */
    public function printerAction($ip_addr, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        $status = $request->getContent();
        $dados = json_decode($status, true);

        if (empty($dados)) {
            $logger->error("JSON INVÁLIDO!!!!!!!!!!!!!!!!!!! Erro no envio das informações da impressora $ip_addr");
            // Retorna erro se o JSON for inválido
            $error_msg = '{
                "message": "JSON Inválido",
                "codigo": 1
            }';


            $response = new JsonResponse();
            $response->setStatusCode('500');
            $response->setContent($error_msg);
            return $response;
        }

        $logger->debug("Atualizando informações para a impressora com IP = $ip_addr\n".$status);

        $printer = $em->getRepository('CocarBundle:Printer')->findOneBy(array('host' => $ip_addr));
        if (empty($printer)) {
            $logger->error("COLETA: Impressora não cadastrada: $ip_addr. Inserindo....");


            // Insere impressora que não estiver cadastrada
            $printer = new Printer();

            // FIXME: Deve ser retornado pelo Cocar
            $data = new \DateTime();
            $printer->setCommunitySnmpPrinter('public');
            $printer->setHost($ip_addr);
            $printer->setDescription('Impressora detectada automaticamente em '.$data->format('d/m/Y'));
            $printer->setName("Impressora $ip_addr");

        }

        $counter = new PrinterCounter();

        // Atualiza impressora sempre que alterar o serial
        if (!empty($dados['model'])) {
            $printer->setName($dados['model']);
        }
        if (!empty($dados['serial'])) {
            $printer->setSerie($dados['serial']);
        }
        if (!empty($dados['description'])) {
            $printer->setDescription($dados['description']);
        }

        // Grava o contador
        $counter->setPrinter($printer);
        $counter->setPrints($dados['counter']);
        $counter->setDate($dados['counter_time']);

        try {
            $em->persist($printer);
            $em->flush();
            $em->persist($counter);
            $em->flush();
        } catch (\Exception $e) {
            // Ainda assim retorna como sucesso
            $logger->error("Entrada repetida para impressora ".$printer->getHost() . "na data ".$dados['counter_time']);
        }


        $response = new JsonResponse();
        $response->setStatusCode('200');
        return $response;
    }

    /**
     * @Route("/printer", name="printer_list")
     * @Method("GET")
     */
    public function printerListAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        $printer = $em->getRepository('CocarBundle:Printer')->findAll();

        $teste = array();
        foreach($printer as $elm) {
            $saida = array(
                'network_ip' => $elm->getHost(),
                'community' => $elm->getCommunitySnmpPrinter()
            );
            array_push($teste, $saida);
        }

        $dados = json_encode(array(
            'printers'=> $teste
            ),
            true);

        $logger->debug("Enviando lista de impressoras \n".$dados);

        $response = new JsonResponse();
        $response->setStatusCode('200');
        $response->setContent($dados);
        return $response;

    }

} 