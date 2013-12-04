<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
	/**
	* @Route("/index", name="cocar_index")
	* @Template()
	*/
    public function indexAction()
    {
        return array();
    }

	/**
	* @Route("/", name="cocar_map")
	* @Template()
	*/
    public function mapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $search = false;

        if($request->isMethod('POST'))
        {
            $circuit = $request->request->get('circuit');

            $searchResult = $em->createQuery(
                                "SELECT c.description, c.id, e.identifier FROM CocarBundle:Circuits c 
                                    LEFT JOIN CocarBundle:Entity e WITH c.entity = e.id
                                    WHERE c.description LIKE :description"
                            )
                            ->setParameter('description', "%$circuit%")
                            ->getResult();

            $search = true;
        }
            
        $highTraffic = $em->createQuery(
                            "SELECT c.id, c.codeInterface, c.description, e.description entity FROM CocarBundle:Circuits c 
                                LEFT JOIN CocarBundle:Entity e WITH c.entity = e.id
                                WHERE c.history LIKE :history
                                    AND c.manages <> :manage
                                    AND c.operStatus = :status"
                        )
                        ->setParameter('history', '%A%')
                        ->setParameter('manage', 'Entidades Externas')
                        ->setParameter('status', 'UP')
                        ->getResult();

        $withoutTraffic = $em->createQuery(
                            "SELECT c.id, c.codeInterface, c.description, e.description entity FROM CocarBundle:Circuits c 
                                LEFT JOIN CocarBundle:Entity e WITH c.entity = e.id
                                WHERE c.history LIKE :history
                                    AND c.manages <> :manage
                                    AND c.operStatus = :status"
                        )
                        ->setParameter('history', '%Z%')
                        ->setParameter('manage', 'Entidades Externas')
                        ->setParameter('status', 'UP')
                        ->getResult();

        $reliability = $em->createQuery(
                            "SELECT c.id, c.codeInterface, c.description, e.description entity FROM CocarBundle:Circuits c 
                                LEFT JOIN CocarBundle:Entity e WITH c.entity = e.id
                                WHERE (c.manages <> :manage1
                                    AND c.manages <> :manage2)
                                    AND c.operStatus = :status"
                        )
                        ->setParameter('manage1', 'Entidades Externas')
                        ->setParameter('manage2', 'Firewall')
                        ->setParameter('status', 'UP')
                        ->getResult();

        $total = $em->createQuery(
                            "SELECT count(c.id) total FROM CocarBundle:Circuits c 
                                WHERE c.manages <> :manage
                                    AND c.operStatus = :status"
                        )
                        ->setParameter('manage', 'Entidades Externas')
                        ->setParameter('status', 'UP')
                        ->getSingleResult();

        return array(
        			 'reliability' => $reliability,
        			 'high_traffic' => $highTraffic,
        			 'without_traffic' => $withoutTraffic,
                     'total' => $total['total'],
                     'search_result' => isset($searchResult) ? $searchResult : null,
                     'search' => $search
        			);
    }

    /**
    * @Route("/totalizer", name="cocar_totalizer")
    * @Template()
    */
    public function totalizerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entitys = $em->getRepository('CocarBundle:Entity')->findAll();

        foreach ($entitys as $entity)
        {
            $highTraffic = $em->createQuery(
                                "SELECT COUNT(c.id) total FROM CocarBundle:Circuits c 
                                    LEFT JOIN CocarBundle:Entity e WITH c.entity = e.id
                                    WHERE c.history LIKE :history
                                        AND c.manages <> :manage
                                        AND c.operStatus = :status
                                        AND e.id = :id"
                            )
                            ->setParameter('history', '%A%')
                            ->setParameter('manage', 'Entidades Externas')
                            ->setParameter('status', 'UP')
                            ->setParameter('id', $entity->getId())
                            ->getSingleResult();

            $withoutTraffic = $em->createQuery(
                                "SELECT COUNT(c.id) total FROM CocarBundle:Circuits c 
                                    LEFT JOIN CocarBundle:Entity e WITH c.entity = e.id
                                    WHERE c.history LIKE :history
                                        AND c.manages <> :manage
                                        AND c.operStatus = :status
                                        AND e.id = :id"
                            )
                            ->setParameter('history', '%Z%')
                            ->setParameter('manage', 'Entidades Externas')
                            ->setParameter('status', 'UP')
                            ->setParameter('id', $entity->getId())
                            ->getSingleResult();

            $reliability = $em->createQuery(
                                "SELECT COUNT(c.id) total FROM CocarBundle:Circuits c 
                                    LEFT JOIN CocarBundle:Entity e WITH c.entity = e.id
                                    WHERE (c.manages <> :manage1
                                        AND c.manages <> :manage2)
                                        AND c.operStatus = :status
                                        AND e.id = :id"
                            )
                            ->setParameter('manage1', 'Entidades Externas')
                            ->setParameter('manage2', 'Firewall')
                            ->setParameter('status', 'UP')
                            ->setParameter('id', $entity->getId())
                            ->getSingleResult();

            $total = $em->createQuery(
                                "SELECT count(c.id) total FROM CocarBundle:Circuits c 
                                    WHERE c.manages <> :manage
                                        AND c.operStatus = :status
                                        AND c.entity = :entity"
                            )
                            ->setParameter('manage', 'Entidades Externas')
                            ->setParameter('status', 'UP')
                            ->setParameter('entity', $entity->getId())
                            ->getSingleResult();

            $circuits[$entity->getId()] = array(
                'high' => $highTraffic['total'], 
                'without' => $withoutTraffic['total'], 
                'rly' => $reliability['total'],
                'description' => $entity->getDescription(),
                'totalCirc' => $highTraffic['total'] + $withoutTraffic['total'] + $reliability['total'],
                'total' => isset($total['total']) ? $total['total'] : 0
            );
        }   

        return array('circuits' => $circuits);
    }
}
