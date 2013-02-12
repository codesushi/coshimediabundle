<?php

namespace Coshi\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file','file');
        ;
    }

    public function getName()
    {
        return 'coshi_mediabundle_mediatype';
    }
}
