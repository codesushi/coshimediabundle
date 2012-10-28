<?php

namespace Coshi\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('CoshiMediaBundle:Default:index.html.twig', array('name' => $name));
    }
}
