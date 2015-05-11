<?php

namespace Coshi\MediaBundle\Templating;

use Coshi\MediaBundle\Model\MediaInterface;
use Twig_Extension;

class MediaExtension extends Twig_Extension
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coshi_media_ext';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'coshi_media_url' => new \Twig_SimpleFunction($this, 'getMediaUrl')
        );
    }

    /**
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getMediaUrl(MediaInterface $media)
    {
        $mediaDir = $this->options['uploader']['media_path'];

        return sprintf('/%s/%s', $mediaDir, $media->getFilename());
    }
}

