<?php

namespace Coshi\MediaBundle\Entity;

use Coshi\UserBundle\Entity\User;
use Coshi\MediaBundle\Model\MediaInterface;
use Coshi\MediaBundle\Model\MediaLinkInterface;

/**
 * Coshi\MediaBundle\Entity\Media
 *
 */
class Media implements MediaInterface
{

    const UPLOADED_FILE = 1;
    const EXTERNAL_MEDIA = 2;
    const YT_VIDEO = 3;


    protected  $id;

    /**
     * @var string $filename
     *
     */
    protected $filename;

    /**
     * @var string $original
     */
    protected $original;

    /**
     * @var string $path
     *
     */
    protected $path;

    /**
     * @var smallint $type
     *
     */
    protected $type;

    /**
     * @var string $mediaurl
     *
     */
    protected $mediaurl;

    /**
     * @var bigint $size
     *
     */
    protected $size;

    /**
     * @var string $mimetype
     *
     */
    protected $mimetype;

    /**
     * webPath
     *
     * @var string
     * @access protected
     */
    protected $webPath;

    /**
     * @var datetime $created_at
     */
    protected $created;

    /**
     * @var datetime $updated_at
     */
    protected $updated;

    /**
     * @var file
     * holder for uploaded file
     */
    public $file;


    public function onPrePersist()
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
    }

    public function onPreUpdate()
    {
        $this->updated = new \DateTime('now');
    }


    /**
     * Set filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set type
     *
     * @param smallint $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return smallint
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set mediaurl
     *
     * @param string $mediaurl
     */
    public function setMediaurl($mediaurl)
    {
        $this->mediaurl = $mediaurl;
    }

    /**
     * Get mediaurl
     *
     * @return string
     */
    public function getMediaurl()
    {
        return $this->mediaurl;
    }

    /**
     * Set size
     *
     * @param bigint $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Get size
     *
     * @return bigint
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set mimetype
     *
     * @param string $mimetype
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     * Get mimetype
     *
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }


    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }


    /*
     * Getter for
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /*
     * Setter for
     */
    public function setOriginal($original)
    {
        $this->original = $original;
        return $this;
    }


    /**
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Getter for webPath
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * Setter for webPath
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;
        return $this;
    }
}
