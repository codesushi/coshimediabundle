<?php

namespace Coshi\MediaBundle\Manager;

use Coshi\MediaBundle\Entity\Media;
use Coshi\MediaBundle\Entity\ProductMedia;
use Coshi\MediaBundle\Service\Imager;
use Coshi\MediaBundle\Model\MediaAttachableInteface;
use Coshi\MediaBundle\Model\MediaLinkInteface;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;


class MediaManager
{

    /**
     * class
     *
     * @var mixed
     * @access protected
     */
    protected $class;

    /**
     * kernel
     *
     * @var HttpKernelInterface
     * @access protected
     */
    protected $kernel;

    /**
     * container
     *
     * @var mixed
     * @access protected
     */
    protected $container;

    /**
     * em
     *
     * @var mixed
     * @access protected
     */
    protected $em ;

    /**
     * repository
     *
     * @var EntityRepository
     * @access protected
     */
    protected $repository;

    /**
     * Imager Service instance
     *
     * @var Imager
     * @access protected
     */
    protected $imagerService;


    /**
     * options
     *
     * @var array
     * @access protected
     */
    protected $options;


    /**
     * __construct
     *
     * @param EntityManager $em
     * @param Imager $imager
     * @access public
     * @return void
     */
    public function __construct(
        EntityManager $em,
        Imager $imager,
        $options = null
    )
    {
        $this->entityManager = $em;
        $this->imagerService = $imager;
        $this->options = $options;

        $this->class = $options['media_class'];

        $this->repository = $this
            ->entityManager
            ->getRepository($this->class)
        ;

    }

    /**
     * getClassInstance
     *
     * @access public
     * @return void
     */
    public function getClassInstance()
    {
        $class = $this->class;
        return new $class();
    }

    /**
     * create
     *
     * @param bool $entity
     * @param bool $withFlush
     * @access public
     * @return void
     */
    public function create($entity = null, $withFlush = true)
    {
        if (!$entity instanceof MediaInterface) {
            $entity = $this->getClassInstance();
        }

        if (null !== $entity->file) {
            $this->upload($entity);
        }

        $this->entityManager->persist($entity);
        if ($withFlush) {
            $this->entityManager->flush();
        }

    }

    public function update(Media $entity, $withFlush = true)
    {

        if (null !== $entity->file) {
            $this->upload($entity);
        }

        $this->entityManager->persist($entity);

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }




    public function attach(
        MediaAttachableInterface $object,
        MediaInterface $medium
    )
    {
        $linkObj = $object->getMediaLink();
        $linkObj->setObject($object);
        $linkObj->setMedium($medium);

        $this->entityManager->persist($linkObj);
        $this->entityManager->flush();

        return $linkObj;

    }


    public function upload(Media $entity)
    {

        $file = $entity->file->getClientOriginalName();

        $entity->setMimetype($entity->file->getMimeType());
        $entity->setSize($entity->file->getClientSize());
        $ext = $entity->file->guessExtension() ?
            $entity->file->guessExtension() : 'bin';
        $entity->setType(Media::UPLOADED_FILE);
        $entity->setOriginal(
            $entity->file->getClientOriginalName()
        );
        $entity->setFilename(
            md5(
                rand(1, 9999999).
                time().
                $entity->file->getClientOriginalName()
            )
            .'.'.$ext
        );
        $entity->setPath($this->getUploadRootDir());

        $entity->file->move(
            $this->getUploadRootDir(),
            $entity->getFilename()
        );



        /*
         * Thumbnail is not part of this bundle
         */
         /* if(strpos($entity->getMimetype(),'image')!== false) {
            $this->thumbnail($entity);
         }*/

        return $entity;


    }

    public function delete(Media $entity,$withFlush=true)
    {
        // unlink file
        if (!unlink($entity->getPath().'/'.$entity->getFilename())) {
            throw new RuntimeException('Cannot delete file');
        }

        // bye thumbnails

        /*foreach ($this->options['imager']['options']['thumbnails'] as $k =>$v)
        {
            unlink($entity->getPath().'/'.$v['dir'].'/'.$entity->getFilename());
        }*/

        $this->entityManager->remove($entity);
        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    /*public function thumbnail(Media $entity)
    {
        $this->imagerService->thumbnail($entity->getPath().DIRECTORY_SEPARATOR.$entity->getFilename());
    }*/

    public function getUploadDir()
    {
        return $this->options['uploader']['media_path'];
    }

    public function getUploadRootDir()
    {
        $basepath = $this->kernel->getRootDir().
            '/../'.
            $this->options['uploader']['www_root'].
            '/'.
            $this->options['uploader']['media_path'];
        return $basepath;
    }
    /**
     * getClass
     *
     * @access public
     * @return void
     */
    public function getClass()
    {
        return $this->class;
    }

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getRepository()
    {
        return $this->repository;
    }

}
