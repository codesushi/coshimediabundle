<?php

namespace Coshi\MediaBundle\Adapter;

use Gaufrette\Adapter\Local as BaseLocal;


class Local extends BaseLocal implements UrlInterface
{
    public function getUrl($key, array $options = array())
    {
        return sprintf('/%s/%s', $this->directory, $key);
    }
}