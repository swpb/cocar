<?php

namespace Swpb\Bundle\CocarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Swpb\Bundle\CocarBundle\Entity\Circuits;
use Swpb\Bundle\CocarBundle\Form\CircuitsType;

/**
 * Circuits controller.
 *
 * @Route("/circuits")
 */
class CircuitsController extends Controller
{

    /**
     * Lists all Circuits entities.
     *
     * @Route("/", name="circuits")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Circuits();

        $entities = $em->getRepository('Swpb\Bundle\CocarBundle\Entity\Circuits')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Circuits entity.
     *
     * @Route("/", name="circuits_create")
     * @Method("POST")
     * @Template("CocarBundle:Circuits:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Circuits();
        $form = $this->createForm(new CircuitsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('circuits_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Circuits entity.
     *
     * @Route("/new", name="circuits_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Circuits();
        $form   = $this->createForm(new CircuitsType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Circuits entity.
     *
     * @Route("/{id}", name="circuits_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Circuits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Circuits entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Circuits entity.
     *
     * @Route("/{id}/edit", name="circuits_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Circuits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Circuits entity.');
        }

        $editForm = $this->createForm(new CircuitsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Circuits entity.
     *
     * @Route("/{id}", name="circuits_update")
     * @Method("PUT")
     * @Template("CocarBundle:Circuits:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Circuits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Circuits entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CircuitsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('circuits_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Circuits entity.
     *
     * @Route("/{id}", name="circuits_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CocarBundle:Circuits')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Circuits entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('circuits'));
    }

    /**
     * Creates a form to delete a Circuits entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
