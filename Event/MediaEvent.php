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
    protected $media;

    public function __construct(MediaInterface $media)
    {
        $this->media = $media;
    }

    public function getMedia()
    {
        return $this->media;
    }

}


