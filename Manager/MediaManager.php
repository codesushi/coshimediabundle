<?php

namespace Coshi\MediaBundle\Manager;

use Coshi\MediaBundle\Entity\Media;
use Coshi\MediaBundle\Event\MediaEvent;
use Coshi\MediaBundle\FilesystemMap;
use Coshi\MediaBundle\MediaEvents;
use Coshi\MediaBundle\Model\MediaInterface;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class MediaManager
{
    /**
     * @var string
     */
    protected $class;

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

    protected $filesystemMap;

    protected $uploadPath;
    /**
     * @var array
     */
    protected $options;

    public function __construct(
        EntityManager $em,
        EventDispatcherInterface $eventDispatcher,
        FilesystemMap $filesystemMap,
        $mediaClass
    )
    {
        $this->entityManager = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystemMap = $filesystemMap;
        $this->class = $mediaClass;

        $this->repository = $this
            ->entityManager
            ->getRepository($this->class)
        ;
    }

    public function getClassInstance()
    {
        $class = $this->class;
        return new $class();
    }

    public function create(UploadedFile $file, MediaInterface $entity = null, Filesystem $filesystem = null, $withFlush = false, $keepOriginalFileName = false)
    {
        if (!$entity instanceof MediaInterface) {
            $entity = $this->getClassInstance();
        }

        $entity = $this->upload($file, $entity, $filesystem, $keepOriginalFileName);

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
     * @param bool $keepFile should we remove old file?
     * @return MediaInterface
     */
    public function update(UploadedFile $file, MediaInterface $entity, $withFlush = false, $keepFile = false)
    {
        if (null !== $file) {
            if (!$keepFile) {
                $this->filesystemMap->get($entity->getStorage())->delete($entity->getPath());
            }
            $entity = $this->upload($file, $entity);
        }

        if ($withFlush) {
            $this->entityManager->flush();
        }

        $this->eventDispatcher->dispatch(MediaEvents::UPDATE_MEDIA, new MediaEvent($entity));

        return $entity;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param MediaInterface $entity
     * @param bool $move
     * @return MediaInterface
     */
    public function upload(UploadedFile $uploadedFile, MediaInterface $entity, Filesystem $filesystem = null, $keepOriginalFileName = false)
    {
        $entity->setMimetype($uploadedFile->getMimeType());
        $entity->setSize($uploadedFile->getClientSize());
        $entity->setType(Media::UPLOADED_FILE);
        $entity->setOriginal($uploadedFile->getClientOriginalName());
        $entity->setFileName($this->getFilename($uploadedFile, $keepOriginalFileName));

        if (!$filesystem) {
            $filesystem = $this->filesystemMap->getDefault();
        }

        $storagePath = sprintf('%s/%s', $this->getUploadPath(), $entity->getFilename());

        $entity->setStorage($filesystem->getName());
        $entity->setPath($storagePath);

        //Upload file to storage
        if (!$filesystem->has($entity->getFileName())) {
            $filesystem->write($entity->getPath(), file_get_contents($uploadedFile->getPathname()));
        }

        return $entity;
    }

    /**
     * @param MediaInterface $entity
     * @param bool $withFlush
     * @throws \RuntimeException
     */
    public function delete(MediaInterface $entity, $withFlush = false)
    {

        $this->eventDispatcher->dispatch(MediaEvents::DELETE_MEDIA, new MediaEvent($entity));

        $filesystem = $this->filesystemMap->get($entity->getStorage());
        $filesystem->delete($entity->getPath());

        $this->entityManager->remove($entity);

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    public function getFilename(UploadedFile $uploadedFile, $keepOriginalFileName)
    {
        if ($keepOriginalFileName) {
            $fileName = $uploadedFile->getClientOriginalName();
        } else {
            $ext = $uploadedFile->guessExtension() ? $uploadedFile->guessExtension() : 'bin';
            $fileName = md5(rand(1, 9999999).time().$uploadedFile->getClientOriginalName()).'.'.$ext;
        }

        return $fileName;
    }


    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
