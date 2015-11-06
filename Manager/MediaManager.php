<?php

namespace Coshi\MediaBundle\Manager;

use Coshi\MediaBundle\Entity\Media;
use Coshi\MediaBundle\Event\MediaEvent;
use Coshi\MediaBundle\MediaEvents;
use Coshi\MediaBundle\Model\MediaInterface;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Coshi\MediaBundle\FilesystemMap;

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

    /**
     * @param EntityManager $em
     * @param KernelInterface $kernel
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $options
     */
    public function __construct(
        EntityManager $em,
        EventDispatcherInterface $eventDispatcher,
        FilesystemMap $filesystemMap,
        $options
    )
    {
        $this->entityManager = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->options = $options;
        $this->filesystemMap = $filesystemMap;

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
     * @param bool $move
     * @param Event $eventCalled
     *
     * @return MediaInterface
     */
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
     * @return MediaInterface
     */
    public function update(UploadedFile $file, MediaInterface $entity, $withFlush = false)
    {
        if (null !== $file) {
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

        $entity->setWebPath(
            $filesystem->getAdapter()->getUrl($entity->getPath())
        );

        //Upload file to storage
        if (!$filesystem->has($entity->getFileName())) {
            $filesystem->write($entity->getPath(), file_get_contents($uploadedFile->getPathname()));
        }

        return $entity;
    }

    /**
     * Set uploaded file path. Path must be realtaive to upload direcotry/bucket.
     * In path string can't be used '..'
     * 
     * @param [type] $path [description]
     */
    public function setUploadPath($path)
    {   
        //remove doubled slashes
        $path = preg_replace('#/+#','/',$path);
        $path = ltrim($path, '/');
        $path = rtrim($path, '/');

        $this->uploadPath = $path;

        return $this;
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    /**
     * @param MediaInterface $entity
     * @param bool $withFlush
     * @throws \RuntimeException
     */
    public function delete(MediaInterface $entity, $withFlush = false)
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
