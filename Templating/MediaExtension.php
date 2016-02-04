<?php

namespace Coshi\MediaBundle\Templating;

use Coshi\MediaBundle\Entity\Media;
use Coshi\MediaBundle\Model\MediaInterface;
use Coshi\MediaBundle\FilesystemMap;
use Coshi\MediaBundle\Adapter\UrlInterface;
use Coshi\MediaBundle\Video\Processor;
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
        $this->processor = new Processor();
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
            new \Twig_SimpleFunction('coshi_media_video_thumbnail', [$this, 'getVideoThumbnail']),
            new \Twig_SimpleFunction('coshi_media_video_embed', [$this, 'getVideoEmbed']),
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

        if ($media->getType() == Media::EXTERNAL_VIDEO) {
             return $media->getMediaUrl();
        }

        $storageName = $media->getStorage();
        $adapter = $this->filesystemMap->get($storageName)->getAdapter();

        if ($adapter instanceof UrlInterface) {
            return $adapter->getUrl($media->getPath(), $options);
        }

        return false;
    }

    public function getVideoThumbnail(MediaInterface $media, $size = 'default')
    {
        if ($media->getType() != Media::EXTERNAL_VIDEO) {
            return false;
        }

        return $this->processor->getThumbnail($media, $size);
    }

    public function getVideoEmbed(MediaInterface $media)
    {
        if ($media->getType() != Media::EXTERNAL_VIDEO) {
            return false;
        }

        return $this->processor->getEmbed($media);
    }
}

