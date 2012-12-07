<?php

namespace Coshi\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaType extends AbstractType
{

    protected $class;

    public function __construct($className)
    {
        $this->class = $className;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file');
        ;
    }

    public function setDefaultOptions(OptionsResolverInterace $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->class
            )
        );
    }




    public function getName()
    {
        return 'coshi_mediabundle_mediatype';
    }


}
