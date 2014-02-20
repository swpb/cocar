<?php

namespace Swpb\Bundle\CocarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PrinterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('communitySnmpPrinter')
            ->add('host')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swpb\Bundle\CocarBundle\Entity\Printer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swpb_bundle_cocarbundle_printer';
    }
}
