<?php

namespace Coshi\MediaBundle;

use Coshi\MediaBundle\Event\MediaEvent;

/**
 * class MediaEvents.php
 *
 * class description here
 *
 * @author  Krzysztof Ozog, <krzysztof.ozog@codesushi.co>
 */
class MediaEvents
{
    const CREATE_MEDIA = 'coshi_media.events.create_media';
    const UPDATE_MEDIA = 'coshi_media.events.update_media';
    const DELETE_MEDIA = 'coshi_media.events.delete_media';


    public static function dispatchCreate($dispatcher, $media)
    {
        $dispatcher->dispatch(self::CREATE_MEDIA, new MediaEvent($media));
    }

    public static function dispatchUpdate($dispatcher, $media)
    {
        $dispatcher->dispatch(self::UPDATE_MEDIA, new MediaEvent($media));
    }

    public static function dispatchDelete($dispatcher, $media)
    {
        $dispatcher->dispatch(self::DELETE_MEDIA, new MediaEvent($media));
    }
}

