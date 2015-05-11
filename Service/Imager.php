<?php

namespace Coshi\MediaBundle\Service;

use \Imagine\Image\Box;
use \Imagine\Image\Point;
use \Symfony\Component\HttpFoundation\File\File;

class Imager
{
    /**
     * @var array
     * Service options like imagine backend
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->options = $options['imager']['options'];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $file
     * @throws \RuntimeException
     */
    public function thumbnail($file)
    {
        $imageFile = new File($file);
        $DS = DIRECTORY_SEPARATOR;

        if (!$imageFile->isFile()) {
            throw new \RuntimeException(sprintf('File %s in directory %s DOES NOT EXISTS!', $imageFile->getFilename(), $imageFile->getPath()));
        }

        $image = $this->getImage();
        $image = $image->open($file);

        foreach ($this->options['thumbnails'] as $thumbnail) {
            $targetBox = new Box($thumbnail['width'], $thumbnail['height']);
            $image->thumbnail($targetBox)->save($imageFile->getPath().$DS.$thumbnail['dir'].$DS.$imageFile->getFilename());
        }
    }

    /**
     * @returns \Imagine\Image\ImageInterface
     */
    protected function getImage()
    {
        switch ($this->options['lib'])
        {
            case 'gd':
                return new \Imagine\Gd\Imagine();
                break;
            case 'imagick':
                return new \Imagine\Imagick\Imagine();
                break;
            default:
                return new \Imagine\Gd\Imagine();
        }
    }
}
