<?php

namespace Coshi\MediaBundle\Model;

interface MediaInterface
{
    public function setFileName($fileName);

    public function getFileName();

    public function setType($type);

    public function getType();

    public function setMediaUrl($mediaUrl);

    public function getMediaUrl();

    public function setSize($size);

    public function getSize();

    public function setMimeType($mimeType);

    public function getMimeType();

    public function setOriginal($original);

    public function getOriginal();

    public function setPath($path);

    public function getPath();

    public function setWebPath($webPath);

    public function getWebPath();
}
