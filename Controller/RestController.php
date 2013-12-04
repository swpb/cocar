<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations as Rest;

use JMS\Serializer\SerializationContext;

class RestController extends Controller
{

    /**
     * GET Route annotation.
     * @Rest\Post("/snmp")
     */
    public function postSnmpAction()
    {
        $em  = $this->getDoctrine()->getManager();
        $dir = $this->get('kernel')->getRootDir() . "/../web/rrd/machine/";

        foreach ($_FILES as $file)
        {
            try
            {
                $machine = new \Cocar\CocarBundle\Entity\Machine;

                $file = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                        $file['tmp_name'], 
                        $file['name'], 
                        $file['type'],
                        $file['size'], 
                        $file['error']);

                $machine->setFile($file);
                $machine->preUpload();
                $machine->upload();

                $content = simplexml_load_file($machine->getAbsolutePath());

                if(isset($content->machine["gateway"]) && isset($content->machine->macaddress))
                {
                    $circuit = $em->getRepository('CocarBundle:Circuits')->findByIpBackbone($content->machine["gateway"]);
                    
                    foreach ($circuit as $c)
                    {
                        $id = $c->getId();
                        $circuit = $em->getRepository('CocarBundle:Circuits')->find($id);
                    }

                    $machine->setGateway($circuit);
                    $machine->setIp($content->machine->ip);
                    $machine->setMacAddress($content->machine->macaddress);

                    $em->persist($machine);
                    $em->flush();

                    $dir .= $id . "/";

                    if(!is_dir($dir))
                        mkdir($dir);
                    
                    $file = $dir . str_replace(":", "", $content->machine->macaddress) . '.rrd';

                    if (!file_exists($file))
                        $this->get('cocar_monitor')->createRrd($file);

                    foreach ($content->snmp->period as $value)
                    {
                        $this->get('cocar_monitor')->updateRrd($file, $value->in, 
                            $value->out, $value['date']);
                    }

                } else {
                    return new Response("Error in XML format");
                }

            } catch(\Exception $e) {
                return new Response ($e->getMessage());
                return new Response($this->get('jms_serializer')->serialize(array('result' => 'error'), 'json'));
            }
        }

        return new Response($this->get('jms_serializer')->serialize(array('result' => 'success'), 'json'));
    }

    /**
     * GET Route annotation.
     * @Rest\Get("/snmp/{slug}")
     */
    public function getSnmpAction($slug)
    {
        $entity = array('campo' => 'conteudo', 'campo2' => array('campo3' => 'conteudo2'));

        $context = new SerializationContext();

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($entity, 'json', $context));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * GET Route annotation.
     * @Rest\Put("/snmp/{slug}")
     */
    public function putSnmpAction($slug)
    {

    }

    /**
     * GET Route annotation.
     * @Rest\Delete("/snmp/{slug}")
     */
    public function deleteSnmpAction($slug)
    {

    }
}