<?php

namespace Coshi\MediaBundle\Model;


interface MediaLinkInterface
{

    public function getObject();

    public function setObject($object);

    public function getMedium();

    public function setMedium(MediaInterface $medium);
}
