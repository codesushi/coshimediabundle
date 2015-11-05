<?php

namespace Coshi\MediaBundle;

use Gaufrette\Filesystem as BaseFilesystem;
use Gaufrette\Adapter;

class Filesystem extends BaseFilesystem
{
    protected $name;

    public function __construct(Adapter $adapter, $name)
    {
        parent::__construct($adapter);

        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFileRegister()
    {
        return $this->fileRegister;
    }
}