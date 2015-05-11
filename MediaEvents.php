<?php

namespace Coshi\MediaBundle;

/**
 * class MediaEvents
 *
 * Defines kinds of events associated with media
 *
 * @author  Krzysztof Ozog, <krzysztof.ozog@codesushi.co>
 */
final class MediaEvents
{
    const CREATE_MEDIA = 'coshi_media.events.create_media';

    const UPDATE_MEDIA = 'coshi_media.events.update_media';

    const DELETE_MEDIA = 'coshi_media.events.delete_media';
}

