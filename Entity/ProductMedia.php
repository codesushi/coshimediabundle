<?php
namespace Coshi\MediaBundle\Entity;
use Sylius\Sandbox\Bundle\AssortmentBundle\Entity\Product as Product;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Coshi\MediaBundle\Model\MediaLinkInterface;
use Coshi\MediaBundle\Model\MediaInterface;

/**
 * Coshi\MediaBundle\Entity\ProductMedia
 *
 * @ORM\Table(name="product_media")
 * @ORM\Entity()
 */
class ProductMedia implements MediaLinkInterface
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Sandbox\Bundle\AssortmentBundle\Entity\Product", inversedBy="product_media")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

     /**
     * @ORM\OneToOne(targetEntity="\Coshi\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $media;

    /**
     * @ORM\Column(name="is_default", type="boolean", nullable=false)
     */
    protected $is_default;

    /* interface methods */

    public function getObject()
    {
        return $this->product;
    }

    public function setObject($product)
    {
        if ($product instanceof Product){

            $this->product = $product;
        } else {
            throw new \RuntimeException('Object must be instance of Product');
        }

    }
    public function getMedium()
    {
        return $this->media;
    }
    public function setMedium(MediaInterface $medium)
    {
        $this->media = $medium;
    }
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
     * Set is_default
     *
     * @param boolean $isDefault
     * @return ProductMedia
     */
    public function setIsDefault($isDefault)
    {
        $this->is_default = $isDefault;
        return $this;
    }

    /**
     * Get is_default
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->is_default;
    }

    /**
     * Set product
     *
     * @param Sylius\Sandbox\Bundle\AssortmentBundle\Entity\Product $product
     * @return ProductMedia
     */
    public function setProduct(\Sylius\Sandbox\Bundle\AssortmentBundle\Entity\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return Sylius\Sandbox\Bundle\AssortmentBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set media
     *
     * @param Coshi\MediaBundle\Entity\Media $media
     * @return ProductMedia
     */
    public function setMedia(\Coshi\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Get media
     *
     * @return Coshi\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
