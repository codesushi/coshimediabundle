<?php

namespace Coshi\MediaBundle\Manager;

use Coshi\MediaBundle\Entity\Media;
use Coshi\MediaBundle\Event\MediaEvent;
use Coshi\MediaBundle\MediaEvents;
use Coshi\MediaBundle\Model\MediaAttachableInterface;
use Coshi\MediaBundle\Model\MediaInterface;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class MediaManager
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param EntityManager $em
     * @param KernelInterface $kernel
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $options
     */
    public function __construct(
        EntityManager $em,
        KernelInterface $kernel,
        EventDispatcherInterface $eventDispatcher,
        array $options
    )
    {
        $this->entityManager = $em;
        $this->kernel = $kernel;
        $this->eventDispatcher = $eventDispatcher;
        $this->options = $options;

        $this->class = $options['media_class'];

        $this->repository = $this
            ->entityManager
            ->getRepository($this->class)
        ;
    }

    /**
     * @return object
     */
    public function getClassInstance()
    {
        $class = $this->class;
        return new $class();
    }

    /**
     * @param UploadedFile $file
     * @param MediaInterface $entity
     * @param bool $withFlush
     *
     * @return MediaInterface
     */
    public function create(UploadedFile $file, MediaInterface $entity = null, $withFlush = true)
    {
        if (!$entity instanceof MediaInterface) {
            $entity = $this->getClassInstance();
        }

        if (null !== $file) {
            $entity = $this->upload($file, $entity);
        }
        $this->entityManager->persist($entity);

        if ($withFlush) {
            $this->entityManager->flush();
        }
        $this->eventDispatcher->dispatch(MediaEvents::CREATE_MEDIA, new MediaEvent($entity));

        return $entity;
    }

    /**
     * @param UploadedFile $file
     * @param MediaInterface $entity
     * @param bool $withFlush
     * @return MediaInterface
     */
    public function update(
        UploadedFile $file,
        MediaInterface $entity,
        $withFlush = true
    )
    {
        if (null !== $file) {
            $entity = $this->upload($file, $entity);
        }
        $this->entityManager->persist($entity);

        if ($withFlush) {
            $this->entityManager->flush();
        }
        $this->eventDispatcher->dispatch(MediaEvents::UPDATE_MEDIA, new MediaEvent($entity));

        return $entity;
    }

    /**
     * @param MediaAttachableInterface $object
     * @param MediaInterface $medium
     * @return mixed
     */
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

    /**
     * @param UploadedFile $uploadedFile
     * @param MediaInterface $entity
     * @param bool $move
     * @return MediaInterface
     */
    public function upload(UploadedFile $uploadedFile, MediaInterface $entity, $move = true)
    {
        $entity->setMimetype($uploadedFile->getMimeType());
        $entity->setSize($uploadedFile->getClientSize());

        $entity->setType(Media::UPLOADED_FILE);

        $entity->setOriginal(
            $uploadedFile->getClientOriginalName()
        );

        $entity->setPath($this->getUploadRootDir());

        if ($move) {
            $ext = $uploadedFile->guessExtension() ?
                $uploadedFile->guessExtension() : 'bin';
            
            $entity->setFileName(
                md5(
                    rand(1, 9999999).
                    time().
                    $uploadedFile->getClientOriginalName()
                )
                .'.'.$ext
            );
            $uploadedFile->move(
                $this->getUploadRootDir(),
                $entity->getFileName()
            );
        } else {
            $entity->setFileName($entity->getOriginal());
        }

        $entity->setWebPath(
            '/'.
            $this->getUploadDir().
            '/'.
            $entity->getFileName()
        );

        return $entity;
    }

    /**
     * @param MediaInterface $entity
     * @param bool $withFlush
     * @throws \RuntimeException
     */
    public function delete(MediaInterface $entity, $withFlush=true)
    {
        if (!unlink($entity->getPath() . '/' . $entity->getFileName())) {
            throw new \RuntimeException('Cannot delete file');
        }

        $this->eventDispatcher->dispatch(MediaEvents::DELETE_MEDIA, new MediaEvent($entity));
        $this->entityManager->remove($entity);

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return $this->options['uploader']['media_path'];
    }

    /**
     * @return string
     */
    public function getUploadRootDir()
    {
        $basePath = sprintf('%s/../%s/%s',
            $this->kernel->getRootDir(),
            $this->options['uploader']['www_root'],
            $this->options['uploader']['media_path']
        );

        return $basePath;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
