<?php

namespace kp\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('kpMediaBundle:Default:index.html.twig', array('name' => $name));
    }
}
