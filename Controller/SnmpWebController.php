<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Swpb\Bundle\CocarBundle\Controller\SnmpController;

class SnmpWebController extends Controller
{
	/**
	* @Route("/infosnmp", name="cocar_infosnmp")
    * @Method("POST")
	* @Template()
	*/
    public function snmpInfoAction(Request $request)
    {
        $form = $this->snmpForm();
        $form->submit($request);

        if ($form->isValid())
        {
            $data = $form->getData();

            $snmp = new SnmpController(
                $data['host'], 
                $data['community'], 
                null
            );
            
            $snmp->hostName();

            $snmp->printHost();
            $snmp->general();
            $snmp->hardware();
            $snmp->memoryFlash();
            $snmp->interfaces();
        }
        
    	return new Response();
    }

    /**
    * @Route("/snmpweb", name="cocar_snmpweb")
    * @Template()
    */
    public function snmpAction()
    {
        $form = $this->snmpForm();

        return array('form' => $form->createView());
    }

    /**
     * @param mixed $id the circuit id
     *
     * @return \Symfony\Component\Form\Form the form
     */
    private function snmpForm()
    {
        return $this->createFormBuilder()
                ->add('host', 'text')
                ->add('community', 'text')
                ->add('send', 'submit', array('label' => 'Enviar' ))
                ->getForm();
    }
}
