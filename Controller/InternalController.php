<?php

namespace Coshi\MediaBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class InternalController extends ContainerAware
{
    protected $em;

    protected $mediaService;

    public function listAction($element, Request $request)
    {
        if('_internal' !== $request->attributes->get('_route'))
        {
            //throw new \RuntimeException('This is ment only for internal requests!');
        }

        $qb = $this->mediaService->getBaseQueryBuilder();

        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);


        $pager->setCurrentPage($request->query->get('page', 1), true, true);
        $pager->setMaxPerPage($this->container->get('coshi_core.config')->get('items_per_page',9));

        return $this->container->get('templating')->renderResponse(
            'CoshiMediaBundle:Internal:list.html.twig',
            array(
                'media' => $pager->getCurrentPageResults(),
                'pager' => $pager,
                'element' => $element,
            )

        );

    }
    public function setMediaService($service)
    {
        $this->mediaService = $service;
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

}
