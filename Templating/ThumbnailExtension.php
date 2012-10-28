<?php

namespace Coshi\MediaBundle\Templating;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Function_Method;
use Coshi\MediaBundle\Entity\Media;

class ThumbnailExtension extends Twig_Extension
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
            'thumbnail'=> new Twig_Function_Method($this, 'getThumbnail')
        );
    }

    /* public getThumbnail($media, $type='small') {{{ */
    /**
     * getThumbnail
     *
     * @param Coshi\MediaBundle\Entity\Media $media
     * @param string $type
     * @access public
     * @return string
     */
    public function getThumbnail($media, $type='small')
    {



        if(!$media and !$type)
        {
            return 'http://placehold.it/320';

        }

        if(!$media)
        {
            return 'http://placehold.it/128';

        }
        if($type == false) {

            $mediadir = $this->options['uploader']['media_path'];
            return '/'.$mediadir.'/'.$media->getFilename();

        }
        $thumbnail_dir = $this->options['imager']['options']['thumbnails'][$type]['dir'];

        $mediadir = $this->options['uploader']['media_path'];

        return '/'.$mediadir.'/'.$thumbnail_dir.'/'.$media->getFilename();
    }
    /* }}} */

}

