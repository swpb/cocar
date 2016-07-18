<?php
/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 04/12/15
 * Time: 10:07
 */

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * Printer controller.
 *
 * @Route("/computador")
 */
class ComputadorController extends Controller
{
    /**
     * Lists all computador entities.
     *
     * @Route("/", name="computador_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        ini_set('memory_limit', '1024M');
        gc_enable();

        $em = $this->getDoctrine()->getManager();

        $form = $request->query->get('form');

        if($form)
        {
            $start = new \DateTime($form['startDate']);

            $end = new \DateTime($form['endDate']);
            $end->setTime('23', '59', '59');
        } else {

                $start = isset($start) ? $start : (time() - ((60*60*24)*30));
                $start = new \DateTime(date("Y-m-d", $start));
                $end   = isset($end) ? $end : time();
                $end = new \DateTime(date("Y-m-d", $end));
        }
        $data = new \DateTime();

        $ping = $em->getRepository('CocarBundle:PingComputador')->relatorioGeral($start, $end);

        return array(
            "ping" => $ping,
            "form" => $this->createCalendarForm(0, $start, $end)->createView(),
            "data" => $data,
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

}
