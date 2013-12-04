<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CircuitsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codeInterface')
            ->add('entity', 'entity', 
                array(
                    'class' => 'CocarBundle:Entity', 
                    'property' => 'identifier'
                )
            )
            ->add('description')
            ->add('manages')
            ->add('ipBackbone')
            ->add('communitySnmpBackbone')
            ->add('serialBackbone')
            ->add('technology')
            ->add('typeInterface', 'choice', 
                array(
                    'choices'  => array('circuito' => 'Circuito', 'porta' => 'Porta'),
                    'expanded' => TRUE,
                    'preferred_choices' => array('circuito'),
                )
            )
            ->add('numSnmpInterface')
            ->add('ipSerialInterface')
            ->add('registerCircuit')
            ->add('speed')
            ->add('cirIn')
            ->add('cirOut')
            ->add('serialRouterTip')
            ->add('snmpPortTip')
            ->add('communitySnmpRouterTip')
            ->add('ipSerialRouterTip');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GerenciadorRedes\Bundle\CocarBundle\Entity\Circuits'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cocar_cocarbundle_circuitstype';
    }
}
