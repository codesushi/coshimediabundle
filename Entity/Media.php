<?php

namespace Coshi\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Coshi\UserBundle\Entity\User;
use Coshi\MediaBundle\Model\MediaInterface;
use Coshi\MediaBundle\Model\MediaLinkInterface;

/**
 * Coshi\MediaBundle\Entity\Media
 *
 * @ORM\Table(name="coshi_media")
 * @ORM\Entity(repositoryClass="Coshi\MediaBundle\Entity\MediaRepository")
 */
class Media implements MediaInterface
{

    const UPLOADED_FILE = 1;
    const EXTERNAL_MEDIA = 2;
    const YT_VIDEO = 3;


    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true )
     */
    private $filename;

    /**
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true )
     */
    private $path;

    /**
     * @var smallint $type
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var string $mediaurl
     *
     * @ORM\Column(name="mediaurl", type="string", length=255,nullable=true)
     */
    private $mediaurl;

    /**
     * @var bigint $size
     *
     * @ORM\Column(name="size", type="bigint",  nullable=true)
     */
    private $size;

    /**
     * @var string $mimetype
     *
     * @ORM\Column(name="mimetype", type="string", length=64, nullable=true)
     */
    private $mimetype;

    /**
     * @var Coshi\UserBunlde\Entity\User $creator
     *
     * @ORM\ManyToOne(targetEntity="\Coshi\UserBundle\Entity\User", inversedBy="created_media")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     *
     */
    private $creator;

    /**
     * @var datetime $created_at
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var datetime $updated_at
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated_at;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set creator
     *
     * @param Coshi\UserBundle\Entity\User $creator
     * @return Media
     */
    public function setCreator(\Coshi\UserBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * Get creator
     *
     * @return Coshi\UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
