<?php

namespace Coshi\MediaBundle\Adapter;

interface UrlInterface
{
    public function getUrl($key, array $options);
}