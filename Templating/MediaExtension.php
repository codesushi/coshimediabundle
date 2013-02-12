<?php

namespace Coshi\MediaBundle\Templating;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Function_Method;
use Coshi\MediaBundle\Entity\Media;

class MediaExtension extends Twig_Extension
{

    private $options ;

    public function setOptions($o)
    {
        $this->options = $o;
    }

    public function getName()
    {
        return 'coshi_media_ext';
    }

    public function getFunctions()
    {
        return array(
            'coshi_media_url' => new Twig_Function_Method($this, 'getMediaUrl')
        );
    }


    public function getMediaUrl($media)
    {
        $mediadir = $this->options['uploader']['media_path'];
        return sprintf('/%s/%s', $mediadir, $media->getFilename());
    }

}

