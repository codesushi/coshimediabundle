<?php

namespace Coshi\MediaBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Coshi\MediaBundle\Model\MediaInterface;

/**
 * class CreateMediaEvent
 *
 * class description here
 *
 * @author  Krzysztof Ozog, <krzysztof.ozog@codesushi.co>
 */
class MediaEvent extends Event
{
    /**
     * @var MediaInterface
     */
    protected $media;

    /**
     * @var Event
     */
    protected $previousEvent;

    /**
     * @param MediaInterface $media
     * @param Event $previousEvent
     */
    public function __construct(MediaInterface $media, Event $previousEvent = null)
    {
        $this->media = $media;
        $this->previousEvent = $previousEvent;
    }

    /**
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @return Event
     */
    public function getPrevious()
    {
        return $this->$previousEvent;
    }
}


