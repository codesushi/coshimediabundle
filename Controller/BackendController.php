<?php

namespace Coshi\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Coshi\MediaBundle\Entity\Media;
use Coshi\MediaBundle\Form\MediaType;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

class BackendController extends Controller
{

    public function indexAction()
    {
        $mediaManager = $this->get('coshi_media.media_manager');
        $media = $mediaManager->getAll();
        $medium = $mediaManager->getClassInstance();

        $form = $this->createForm(new MediaType(),$medium);

        return $this->render('CoshiMediaBundle:Backend:index.html.twig',
            array('form'=>$form->createView(), 'media'=>$media));
    }

    public function deleteAction($id)
    {
        $medium = $this->get('coshi_media.media_manager')->get($id);

        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $mediaManager = $this->get('coshi_media.media_manager');
        $mediaManager->delete($medium);

        $this->get('session')
                    ->getFlashBag()
                    ->add('notice', 'Object has been deleted!');



        return $this->redirect($this->generateUrl('CoshiMediaBundle_index'));


    }

    public function uploadAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $mediaManager = $this->get('coshi_media.media_manager');
        $medium = $mediaManager->getClassInstance();

        $form = $this->createForm(new MediaType(),$medium);

        if ($this->getRequest()->getMethod() === 'POST')
        {
            $form->bindRequest($this->getRequest());
            if ($form->isValid())
            {
                $medium->setCreator($user);
                $mediaManager->create($medium);
                $this->get('session')
                    ->getFlashBag()
                    ->add('notice', 'Your changes were saved!');


               /* $medium->upload();
                $em = $this->entityManagergetDoctrine()->getEntityManager();

                $em->persist($medium);
                $em->flush();
                */
                return $this->redirect($this->generateUrl('CoshiMediaBundle_index'));
            }
        }

        //return array('form' => $form->createView());

        return $this->render('CoshiMediaBundle:Backend:upload.html.twig', array('form' => $form->createView()));
    }

}
