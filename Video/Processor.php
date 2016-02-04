<?php

namespace Coshi\MediaBundle\Video;

use Coshi\MediaBundle\Entity\Media;
use Coshi\MediaBundle\Model\MediaInterface;

/**
 * Processor
 *
 * Process external videos
 *
 * @category Video
 * @package  MediaBundle
 * @author   Krzysztof Ozog <krzysztof.ozog@codesushi.co>
 * @license  MIT
 */
class Processor
{
    private $supportedServices = [
        'vimeo' => 'vimeo',
        'youtube' => 'youtube',
        'youtu.be' => 'youtube',
    ];

    public function getService(MediaInterface $media)
    {
        if ($media->getType() != Media::EXTERNAL_VIDEO) {
            return false;
        }

        $urlInfo = parse_url($media->getMediaUrl());


        if (!$urlInfo) {
             return false;
        }

        foreach ($this->supportedServices as $key => $service) {
            if (stripos($media->getMediaUrl(), $key) !== false) {
                return $service;
            }
        }

        return false;
    }

    public function getThumbnail(MediaInterface $media, $size = 'default')
    {
        switch ($this->getService($media)) {
            case 'youtube':
                return $this->getYoutubeThumbnail($media, $size);

            case 'vimeo':
                return $this->getVimeoThumbnail($media, $size);

            default:
                throw new Exception('Not supported service');
                break;
        }
    }

    public function getEmbed(MediaInterface $media)
    {
        switch ($this->getService($media)) {
            case 'youtube':
                return $this->getYoutubeEmbed($media);
            case 'vimeo':
                return $this->getVimeoEmbed($media);

            default:
                throw new Exception('Not supported service');
        }
    }

    private function getYoutubeEmbed(MediaInterface $media)
    {
        $template = '<iframe width="560" height="315" src="https://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>';

        return sprintf($template, $this->getYoutubeId($media));
    }

    private function getYoutubeThumbnail(MediaInterface $media, $size = 'default')
    {
        $sizes = [
            'original' => 'http://img.youtube.com/vi/%s/0.jpg',
            'default' => 'http://img.youtube.com/vi/%s/default.jpg'
        ];

        if (isset($sizes[$size])) {
            return sprintf($sizes[$size], $this->getYoutubeId($media));
        }

        return sprintf($sizes['default'], $this->getYoutubeId($media));
    }

    private function getYoutubeId(MediaInterface $media)
    {
        $matches = [];
        $regexp = '/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=))([\w-]{10,12})/';

        preg_match($regexp, $media->getMediaUrl(), $matches);


        if (count($matches) > 1) {
             return $matches[1];
        }

        return false;
    }

    private function getVimeoId(MediaInterface $media)
    {
        return (int) substr(parse_url($media->getMediaUrl(), PHP_URL_PATH), 1);
    }

    private function getVimeoEmbed(MediaInterface $media)
    {
        $template = '<iframe width="560" height="315" src="https://player.vimeo.com/video/%d" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

        return sprintf($template, $this->getVimeoId($media));
    }

    private function getVimeoThumbnail(MediaInterface $media, $size = 'default')
    {
        $sizes = [
            'original' => 'thumbnail_large',
            'default'  => 'thumbnail_medium',
        ];

        $id = $this->getVimeoId($media);
        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$id.php"));

        if (isset($sizes[$size])) {
            return $hash[0][$sizes[$size]];
        }

        return $hash[0][$sizes['default']];
    }

}
