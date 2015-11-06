<?php

namespace Coshi\MediaBundle\Templating;

use Coshi\MediaBundle\Model\MediaInterface;
use Coshi\MediaBundle\FilesystemMap;
use Coshi\MediaBundle\Adapter\UrlInterface;
use Twig_Extension;

class MediaExtension extends Twig_Extension
{
    /**
     * @var array
     */
    private $filesystemMap;

    /**
     * @param array $options
     */
    public function __construct(FilesystemMap $filesystemMap)
    {
        $this->filesystemMap = $filesystemMap;
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
            new \Twig_SimpleFunction('coshi_media_url', [$this, 'getMediaUrl']),
        );
    }

    /**
     * @param MediaInterface $media
     * @param array $options 
     * @return string
     */
    public function getMediaUrl(MediaInterface $media = null, array $options = [])
    {
        if (!$media) {
            return false;
        }

        $storageName = $media->getStorage();
        $adapter = $this->filesystemMap->get($storageName)->getAdapter();

        if ($adapter instanceof UrlInterface) {
            return $adapter->getUrl($media->getPath(), $options);
        }

        return false;
    }
}

