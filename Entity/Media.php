<?php

namespace Coshi\MediaBundle\Entity;

use Coshi\MediaBundle\Model\MediaInterface;

class Media implements MediaInterface
{
    const UPLOADED_FILE = 1;
    const EXTERNAL_MEDIA = 2;
    const YT_VIDEO = 3;

    /**
     * @var int
     */
    protected  $id;

    /**
     * @var string $fileName
     */
    protected $fileName;

    /**
     * @var string $original
     */
    protected $original;

    /**
     * @var string $path
     */
    protected $path;

    /**
     * @var int $type
     */
    protected $type;

    /**
     * @var string $mediaUrl
     */
    protected $mediaUrl;

    /**
     * @var int $size
     */
    protected $size;

    /**
     * @var string $mimeType
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $webPath;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * Storage identifiter
     * @var string $storage
     */
    protected $storage;

    /**
     * @var mixed
     * holder for uploaded file
     */
    public $file;

    public function onPrePersist()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Set storage identifier
     */
    public function setStorage($name)
    {
        $this->storage = $name;

        return $this;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set mediaUrl
     *
     * @param string $mediaUrl
     *
     * @return $this
     */
    public function setMediaUrl($mediaUrl)
    {
        $this->mediaUrl = $mediaUrl;
        return $this;
    }

    /**
     * Get mediaUrl
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->mediaUrl;
    }

    /**
     * Set size
     *
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * 
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * 
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * 
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set original
     *
     * @param string $original
     * 
     * @return $this
     */
    public function setOriginal($original)
    {
        $this->original = $original;
        return $this;
    }

    /**
     * Get original
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set path
     *
     * @param string $path
     * 
     * @return $this
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
     * Set webPath
     * 
     * @param string $webPath
     * 
     * @return $this
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;
        return $this;
    }

    /**
     * Get webPath
     * 
     * @return string
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return $this->checkMime('image');
    }

    /**
     * @return bool
     */
    public function isPdf()
    {
        return $this->checkMime('pdf');
    }

    protected function checkMime($mime)
    {
        return stripos($this->getMimeType(), strtolower($mime)) !== false;
    }
}
