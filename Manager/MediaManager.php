<?php

namespace Coshi\MediaBundle\Manager;

use \Coshi\MediaBundle\Entity\Media;
use \Coshi\MediaBundle\Entity\ProductMedia;
use \Coshi\MediaBundle\Service\Imager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use ReflectionClass;

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


    /* public __construct(EntityManager $em, Imager $imager) {{{ */
    /**
     * __construct
     *
     * @param EntityManager $em
     * @param Imager $imager
     * @access public
     * @return void
     */
    public function __construct(EntityManager $em, Imager $imager, $options=null)
    {
        $this->entityManager = $em;
        $this->imagerService = $imager;
        $this->repository = $this->entityManager->getRepository('\Coshi\MediaBundle\Entity\Media');
        $this->options = $options;

    }
    /* }}} */

    /* public getClassInstance() {{{ */
    /**
     * getClassInstance
     *
     * @access public
     * @return void
     */
    public function getClassInstance()
    {
        return new Media();
    }
    /* }}} */

    /* public create($entity = null, $withFlush = true) {{{ */
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
        if(!$entity instanceof Media){
            $entity = $this->getClassInstance();
        }

        if(null !== $entity->file){
            $this->upload($entity);
        }

        $this->entityManager->persist($entity);
        if($withFlush) {
            $this->entityManager->flush();
        }

    }
    /* }}} */
    public function update(Media $entity, $withFlush = true)
    {

        if(null !== $entity->file){
            $this->upload($entity);
        }

        $this->entityManager->persist($entity);
        if($withFlush)
        {
            $this->entityManager->flush();
        }
    }

    protected function getLinkClass($class_name)
    {
        $linkmap = $this->options['linkmap'];

        if( array_key_exists($class_name, $linkmap))
        {
            $class = $linkmap[$class_name];

            $obj = new $class;

            $rfl = new ReflectionClass($obj);

            foreach($rfl->getInterfaceNames() as $i) {
                if (stripos($i, 'MediaLinkInterface')!== false)
                {
                    return $obj;
                }
            }

            throw RuntimeException ('Link class does not implements interface MediaLinkInterface!');
        }
        throw new RuntimeException('Unsupported or unconfigured object type');
    }



    public function attach($object, $medium)
    {
        $refl = new ReflectionClass($object);
        $linkObj = $this->getLinkClass($refl->getName());

        $linkObj->setObject($object);
        $linkObj->setMedium($medium);
        $linkObj->setIsDefault(false);

        $this->entityManager->persist($linkObj);
        $this->entityManager->flush();

        return $linkObj;

    }

 //   public function get

    public function getAll()
    {
        return $this->repository->findAll();
    }
    public function get($id)
    {
        return $this->repository->find($id);
    }
    public function getBaseQueryBuilder()
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('m')
            ->from($this->getFQNMedia(), 'm')
            ->orderBy('m.created_at', 'DESC');

        return $qb;
    }

    public function getFQNMedia()
    {
        // to be more meanigful when in inherited bundle

        return 'Coshi\MediaBundle\Entity\Media';
    }

    public function upload(Media $entity)
    {

        $file = $entity->file->getClientOriginalName();

        $entity->setMimetype($entity->file->getMimeType());
        $entity->setSize($entity->file->getClientSize()) ;
        $ext = $entity->file->guessExtension()? $entity->file->guessExtension(): 'bin';
        $entity->setType( Media::UPLOADED_FILE );
        $entity->setFilename( md5( rand(1, 99999).$entity->file->getClientOriginalName() ).'.'.$ext);
        $entity->setPath($this->getUploadRootDir());



        $entity->file->move($this->getUploadRootDir(),$entity->getFilename());



        if(strpos($entity->getMimetype(),'image')!== false) {
            $this->thumbnail($entity);
        }


    }
    public function delete(Media $entity,$withFlush=true)
    {
        // unlink file
        if(!unlink($entity->getPath().'/'.$entity->getFilename())){
            throw new RuntimeException('Cannot delete file');
        }

        // bye thumbnails

        foreach ($this->options['imager']['options']['thumbnails'] as $k =>$v)
        {
            unlink($entity->getPath().'/'.$v['dir'].'/'.$entity->getFilename());
        }

        $this->entityManager->remove($entity);
        if($withFlush){
            $this->entityManager->flush();
        }
    }

    public function thumbnail(Media $entity)
    {
        $this->imagerService->thumbnail($entity->getPath().DIRECTORY_SEPARATOR.$entity->getFilename());
    }

    public function getUploadDir()
    {
       return $this->options['uploader']['media_path'];
    }

    public function getUploadRootDir()
    {
       $basepath = $this->kernel->getRootDir().'/../'.DIRECTORY_SEPARATOR
            .$this->options['uploader']['www_root'].'/'.$this->options['uploader']['media_path'];
       return $basepath;
    }
    /* public getClass() {{{ */
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
    /* }}} */
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
