<?php
/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 14/10/14
 * Time: 01:36
 */

namespace Swpb\Bundle\CocarBundle\Controller;

use Swpb\Bundle\CocarBundle\Entity\PingComputador;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swpb\Bundle\CocarBundle\Entity\PrinterCounter;
use Swpb\Bundle\CocarBundle\Entity\Printer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swpb\Bundle\CocarBundle\Entity\Computador;


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
     * @param $serie
     * @Route("/printer/{serie}", name="printer_counter_update")
     * @Method("POST")
     */
    public function printerAction($serie, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        $status = $request->getContent();
        $dados = json_decode($status, true);

        if (empty($dados)) {
            $logger->error("JSON INVÁLIDO!!!!!!!!!!!!!!!!!!! Erro no envio das informações da impressora $serie");
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

        $logger->debug("Atualizando informações para a impressora com IP = $serie\n".$status);

        $printer = $em->getRepository('CocarBundle:Printer')->findOneBy(array('serie' => $serie));
        $ip_addr = $dados['ip_address'];
        if (empty($printer)) {
            $logger->error("COLETA: Impressora não cadastrada: $serie. Inserindo....");

            // Insere impressora que não estiver cadastrada
            $printer = new Printer();

            // FIXME: Deve ser retornado pelo Cocar
            $data = new \DateTime();
            $printer->setCommunitySnmpPrinter('public');
            $printer->setHost($ip_addr);
            $printer->setSerie($serie);
            $printer->setDescription('Impressora detectada automaticamente em '.$data->format('d/m/Y'));
            $printer->setName("Impressora $serie");

        }

        $counter = new PrinterCounter();

        // Atualiza impressora sempre que alterar o serial
        $printer->setSerieSimpress(substr($serie, 0, 14));

        if (!empty($dados['model'])) {
            $printer->setName($dados['model']);
        }
        if (!empty($dados['description'])) {
            $printer->setDescription($dados['description']);
        }
        if (!empty($dados['local'])) {
            $printer->setLocal($dados['local']);
        }
        if (!empty($dados['netmask'])) {
            $printer->setNetmask($dados['netmask']);
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
            $logger->error("Entrada repetida para impressora ".$serie . "na data ".$dados['counter_time']);
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

        $printer = $em->getRepository('CocarBundle:Printer')->getAllByModel();

        $dados = json_encode(array(
                'printers'=> $printer,
                'result_count'=>count($printer)
            ),
            true);

        $logger->debug("Enviando lista de impressoras \n".$dados);

        $response = new JsonResponse();
        $response->setStatusCode('200');
        $response->setContent($dados);
        return $response;

    }

    /**
     * Return printer list
     *
     * @Route("/networks", name="networks_list")
     * @Method("GET")
     */
    public function networkListAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        $cacic = $this->container->get('kernel')->getBundle('CacicCommonBundle');

        $response = new JsonResponse();


        if (empty($cacic)) {
            $logger->error("O CACIC não está instalado. Não é possível fornecer lista de subredes");
            $response->setStatusCode(404);

            $error_msg = '{
                "message": "CACIC nao esta instalado",
                "codigo": 1
            }';
            $response->setContent($error_msg);

            return $response;
        }

        $redes = $em->getRepository('CacicCommonBundle:Rede')->findAll();

        $saida = array();
        foreach ($redes as $elm) {
            array_push($saida, array(
                'ip_network' => $elm->getTeIpRede(),
                'netmask' => $elm->getTeMascaraRede(),
                'name' => $elm->getNmRede()
            ));
        }

        $dados = json_encode(array(
                'networks'=> $saida
            ),
            true
        );

        $response->setStatusCode(200);
        $response->setContent($dados);

        return $response;

    }

    /**
     * Update computador. JSON Recebido:
     *
     * {
     *       "host": "127.0.0.1",
     *       "mac_address": "00:00:00:00:00:00",
     *       "ping_date": "2015-12-03 10:44:55.614790",
     *       "network_ip": "10.209.111.0",
     *       "local": "DEPEX",
     *       "netmask": "255.255.255.0",
     *       "so_name": "Microsoft Windows Vista SP0 - SP2, Server 2008, or Windows 7 Ultimate",
     *       "so_version": "Microsoft Windows Vista SP0 - SP2, Server 2008, or Windows 7 Ultimate",
     *       "accuracy": "100",
     *       "so_vendor": "Microsoft",
     *       "so_os_family": "Windows",
     *       "so_type": "general purpose",
     *       "so_cpe": ""
     * }
     *
     * @param $host
     * @Route("/computador/{host}", name="computador_ping")
     * @Method("POST")
     *
     */
    public function computadorPingAction($host, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        $status = $request->getContent();
        $dados = json_decode($status, true);

        if (empty($dados)) {
            $logger->error("JSON INVÁLIDO!!!!!!!!!!!!!!!!!!! Erro no envio das informações do computador $host");
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

        $logger->debug("Atualizando informações para o computador com IP = $host\n".$status);


        // Primeiro procura computador pelo MAC
        if (!empty($dados['mac_address'])) {
            $computador = $em->getRepository("CocarBundle:Computador")->findOneBy(array(
                'mac_address' => $dados['mac_address']
            ));

            // Ve se ja existe um de mesmo IP cadastrado
            $computador_ip = $em->getRepository("CocarBundle:Computador")->findOneBy(array(
                'host' => $dados['host']
            ));

            if (empty($computador) && empty($computador_ip)) {
                // Nao achei nem pelo MAC nem pelo IP. Cria um computador
                $logger->debug("Adicionando computador com IP = " . $dados['host'] . " e MAC = " . $dados['mac_address']);
                $computador = new Computador();
            } elseif (empty($computador) && !empty($computador_ip)) {
                // Nao achei pelo MAC, mas achei pelo IP. Considera esse
                $computador = $computador_ip;
            } elseif (!empty($computador_ip))  {
                // Aqui o MAC não é vazio nem o IP. Verifica se é uma atualização de MAC
                $ip_old = $computador_ip->getHost();
                $ip = $computador->getHost();
                if ($ip != $ip_old) {
                    $logger->debug("Atualização de IP. Adicionando MAC ". $dados['mac_address']." para o IP $ip. Removendo MAc do IP $ip_old");
                    // O MAC mudou de IP. O IP velho fica sem MAC
                    $computador_ip->setMacAddress(null);
                    $em->persist($computador_ip);
                }
            } else {
                // Para todos os outros casos considero somente o MAC atual. Não precisa fazer nada
            }

        } else {
            $computador = $em->getRepository("CocarBundle:Computador")->findOneBy(array(
                'host' => $dados['host']
            ));

            if (empty($computador)) {
                // Cria o computador
                $computador = new Computador();
            }
        }

        // Adiciona informações da coleta
        $computador->setHost($dados['host']);
        $computador->setMacAddress($dados['mac_address']);
        $computador->setLocal($dados['local']);
        $computador->setName($dados['name']);
        $computador->setNetmask($dados['netmask']);
        $computador->setAccuracy($dados['accuracy']);
        $computador->setSoCpe($dados['so_cpe']);
        $computador->setSoName($dados['so_name']);
        $computador->setSoOsFamily($dados['so_os_family']);
        $computador->setSoType($dados['so_type']);
        $computador->setSoVendor($dados['so_vendor']);
        $computador->setSoVersion($dados['so_version']);

        // Verifica se o Cacic esta instalado e procura o computador
        $cacic = $this->container->get('kernel')->getBundle('CacicCommonBundle');
        if (!empty($cacic)) {
            $cacic_id = $computador->getCacicId();
            if (empty($cacic_id)) {
                // Procura primeiro pelo MAC
                $cacic_comp = $em->getRepository("CacicCommonBundle:Computador")->findOneBy(array(
                    'teNodeAddress' => $computador->getMacAddress()
                ));

                if (empty($cacic_comp)) {
                    $cacic_comp = $em->getRepository("CacicCommonBundle:Computador")->findOneBy(array(
                        'teIpComputador' => $computador->getHost()
                    ));
                }

                if (!empty($cacic_comp)) {
                    // Vê se o computador é considerado ativo no Cacic
                    $computador->setCacicId($cacic_comp->getIdComputador());
                    $computador->setActive($cacic_comp->getAtivo());
                }
            } else {
                $cacic_comp = $em->getRepository("CacicCommonBundle:Computador")->find($cacic_id);
                $computador->setActive($cacic_comp->getAtivo());
            }
        }

        // Registra o ping
        $ping = new PingComputador();
        $ping->setComputador($computador);
        $ping_date = new \DateTime($dados['ping_date']);
        $ping->setDate($ping_date);

        // Persiste as informações
        try {
            $em->persist($computador);
            $em->flush();
            $em->persist($ping);
            $em->flush();
        } catch (\Exception $e) {
            // Ainda assim retorna como sucesso
            $logger->error("Entrada repetida para computador ".$dados['host'] . " na data ".$dados['ping_date']);
            $logger->error($e->getMessage());
        }

        // Se tudo deu certo, retorna
        $response = new JsonResponse();
        $response->setStatusCode('200');
        return $response;
    }

} 