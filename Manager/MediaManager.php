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
     * @param bool $move
     * @param Event $eventCalled
     *
     * @return MediaInterface
     */
    public function create(UploadedFile $file, MediaInterface $entity = null, $withFlush = true, $keepOriginalFileName = false)
    {
        if (!$entity instanceof MediaInterface) {
            $entity = $this->getClassInstance();
        }

        $entity = $this->upload($file, $entity, $keepOriginalFileName);
        
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
    public function upload(UploadedFile $uploadedFile, MediaInterface $entity, $keepOriginalFileName = false)
    {
        $entity->setMimetype($uploadedFile->getMimeType());
        $entity->setSize($uploadedFile->getClientSize());
        $entity->setType(Media::UPLOADED_FILE);
        $entity->setOriginal($uploadedFile->getClientOriginalName());
        $entity->setPath($this->getUploadRootDir());
        
        if ($keepOriginalFileName) {
            $fileName = $entity->getOriginal();
            $entity->setFileName($entity->getOriginal());
        } else {
            $ext = $uploadedFile->guessExtension() ? $uploadedFile->guessExtension() : 'bin';
            $fileName = md5(rand(1, 9999999).time().$uploadedFile->getClientOriginalName()).'.'.$ext;
        }

        $entity->setFileName($fileName);

        $uploadedFile->move($this->getUploadRootDir(), $entity->getFileName());
        
        $webPath = sprintf('/%s/%s', $this->getUploadDir(), $entity->getFileName());
        $entity->setWebPath($webPath);

        return $entity;
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
