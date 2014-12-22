<?php
/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 22/12/14
 * Time: 14:13
 */

namespace Swpb\Bundle\CocarBundle\Controller;

use Swpb\Bundle\CocarBundle\Entity\PrinterModels;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swpb\Bundle\CocarBundle\Form\PrinterModelsType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Printer models controller.
 *
 * @Route("/printer_models")
 */
class PrinterModelsController extends Controller {

    /**
     * Lists all Printer entities.
     *
     * @Route("/", name="printer_models_index")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        $selected = $em->getRepository('CocarBundle:PrinterModels')->findAll();
        $models = $em->getRepository('CocarBundle:Printer')->getModelsList();

        $form = $this->createForm(new PrinterModelsType($em));

        if ($request->getMethod() == 'POST') {
            $data = $request->get('swpb_bundle_cocarbundle_printer_models');

            // Primeiro remove todos os modelos inseridos
            foreach($selected as $model) {
                $logger->debug("Removendo modelo ".$model->getModel());
                $em->remove($model);
            }
            $em->flush();

            foreach($data['model'] as $elm) {
                $logger->debug("Adicionando modelo ".$elm);
                $model = new PrinterModels();
                $model->setModel($elm);
                $em->persist($model);
            }
            $em->flush();

            // Gera listas novamente pra interface funcionar bem
            $selected = $em->getRepository('CocarBundle:PrinterModels')->findAll();
            $models = $em->getRepository('CocarBundle:Printer')->getModelsList();

        }

        return array(
            'form' => $form->createView(),
            'models' => $models,
            'selected' => $selected
        );
    }

}