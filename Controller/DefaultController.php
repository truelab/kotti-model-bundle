<?php

namespace Truelab\KottiModelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TruelabKottiModelBundle:Default:index.html.twig', array('name' => $name));
    }
}
