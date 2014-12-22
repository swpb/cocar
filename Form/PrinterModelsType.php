<?php

namespace Swpb\Bundle\CocarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Doctrine\ORM\EntityManager;

class PrinterModelsType extends AbstractType
{
    private $em;

    public function __construct(EntityManager $em = null)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'model', 'choice',
            array(
                'label' => 'Modelos',
                'mapped' => false,
                'expanded' => true,
                'multiple' => true
            )
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swpb\Bundle\CocarBundle\Entity\PrinterModels'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swpb_bundle_cocarbundle_printer_models';
    }
}
