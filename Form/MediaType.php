<?php

namespace kp\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('file','file');
  //          ->add('filename')
  //          ->add('type')
  //          ->add('mediaurl')
  //          ->add('size')
  //          ->add('mimetype')
  //          ->add('creator_id')
  //          ->add('created_at')
  //          ->add('updated_at')
        ;
    }

    public function getName()
    {
        return 'kp_mediabundle_mediatype';
    }
}
