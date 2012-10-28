<?php

namespace Coshi\MediaBundle\Service;


use \Imagine\Image\Box;
use \Imagine\Image\Point;
use \Symfony\Component\HttpFoundation\File\File;

class Imager
{
    /**
     * @ var array
     * Service options like, imaggine backend
     */
    protected $options;


    public function __construct($options = array())
    {
        $this->options = $options['imager']['options'];

    }

    public function getOptions()
    {
        return $this->options;

    }

    public function thumbnail( $file )
    {

        $imagefile = new File($file);

        $DS = DIRECTORY_SEPARATOR;

        if(!$imagefile->isFile())
        {
            throw new RuntimeException(sprintf('File %s in directory %s DOES NOT EXISTS!',$imagefile->getFilename(),$imagefile->getPath()));

        }

        $image = $this->getImage();
        $image = $image->open($file);

        foreach ($this->options['thumbnails'] as $thumbnail)
        {

            $targetBox = new Box($thumbnail['width'],$thumbnail['height']);
            $image->thumbnail($targetBox)->save($imagefile->getPath().$DS.$thumbnail['dir'].$DS.$imagefile->getFilename() );

        }


    }

    /**
     * @Returns \Imagine\Image\ImageInteface
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
