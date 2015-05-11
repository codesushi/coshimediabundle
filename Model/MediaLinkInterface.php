<?php

namespace Coshi\MediaBundle\Model;

interface MediaLinkInterface
{
    /**
     * @return object
     */
    public function getObject();

    /**
     * @param object $object
     */
    public function setObject($object);

    /**
     * @return MediaInterface
     */
    public function getMedium();

    /**
     * @param MediaInterface $medium
     */
    public function setMedium(MediaInterface $medium);
}
