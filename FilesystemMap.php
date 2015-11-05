<?php

namespace Coshi\MediaBundle;

use Gaufrette\FilesystemMap as BaseFilesystemMap;

class FilesystemMap extends BaseFilesystemMap
{
    
    /**
     * Name of the default files system
     * @var string
     */
    protected $default;

    public function __construct(array $map = null, $defaultFilesystem = null)
    {   
        //Set filesystems
        foreach ($map as $domain => $filesystem) {
            $this->set($domain, $filesystem);
        }

        $this->setDefault($defaultFilesystem);
    }

    public function setDefault($key)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException(
                sprintf('Default file system can not be set. \'%s\' domain dosent exists', $key)
            );
        }

        $this->default = $key;

        return $this;    
    }

    public function getDefault()
    {
        if (is_null($this->default)) {
            $filesystemKeys = array_keys($this->all());
            $defaultKey = reset($filesystemKeys);
            
            if (!$defaultKey) {
                throw new \LogicException('Can not get default filesystem key');
            }

            $this->setDefault($defaultKey);
        }

        return $this->get($this->default);
    }
}