<?php

namespace Coshi\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProductMediaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('is_default')
            ->add('product')
            ->add('media')
        ;
    }

    public function getName()
    {
        return 'coshi_mediabundle_productmediatype';
    }
}
