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
     * @Method("PUT")
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

        try {
            $printer = $em->getRepository('CocarBundle:Printer')->findOneBy(array('host' => $ip_addr));
        }
        catch(\Doctrine\ORM\NoResultException $e) {
            $logger->error("COLETA: Impressora não cadastrada: $ip_addr \n$e");
            $error_msg = '{
                "message": "Impressora não cadastrada",
                "codigo": 2
            }';


            $response = new JsonResponse();
            $response->setStatusCode('500');
            $response->setContent($error_msg);
            return $response;
        }


        $counter = $this->getDoctrine()->getManager()->getRepository('CocarBundle:PrinterCounter')->findBy(array(
            'printer' => $printer,
            'date' => $dados['counter_time']
        ));

        if(empty($counter)) {
            $counter = new PrinterCounter;
        } else {
            $this->get('logger')->error("Entrada repetida para impressora $printer e data ".$dados['counter_time']);
            return true;
        }

        // Atualiza impressora sempre que alterar o serial
        $printer->setName($dados['model']);
        $printer->setSerie($dados['serial']);
        $printer->setDescription($dados['description']);

        // Grava o contador
        $counter->setPrinter($printer);
        $counter->setPrints($dados['counter']);
        $counter->setDate($dados['counter_time']);

        $em->persist($printer);
        $em->persist($counter);
        $em->flush();

        $response = new JsonResponse();
        $response->setStatusCode('200');
        return $response;
    }

} 