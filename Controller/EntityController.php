<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use GerenciadorRedes\Bundle\CocarBundle\Entity\Entity;
use GerenciadorRedes\Bundle\CocarBundle\Form\EntityType;

/**
 * Entity controller.
 *
 * @Route("/entity")
 */
class EntityController extends Controller
{

    /**
     * Lists all Entity entities.
     *
     * @Route("/", name="entity")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CocarBundle:Entity')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Entity entity.
     *
     * @Route("/", name="entity_create")
     * @Method("POST")
     * @Template("CocarBundle:Entity:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Entity();
        $form = $this->createForm(new EntityType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('entity_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Entity entity.
     *
     * @Route("/new", name="entity_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Entity();
        $form   = $this->createForm(new EntityType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Entity entity.
     *
     * @Route("/{id}", name="entity_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Entity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Entity entity.
     *
     * @Route("/{id}/edit", name="entity_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Entity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity entity.');
        }

        $editForm = $this->createForm(new EntityType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Entity entity.
     *
     * @Route("/{id}", name="entity_update")
     * @Method("PUT")
     * @Template("CocarBundle:Entity:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CocarBundle:Entity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EntityType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('entity_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Entity entity.
     *
     * @Route("/{id}", name="entity_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CocarBundle:Entity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Entity entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('entity'));
    }

    /**
     * Creates a form to delete a Entity entity by id.
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
