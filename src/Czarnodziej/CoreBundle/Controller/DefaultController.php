<?php

namespace Czarnodziej\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CzarnodziejCoreBundle:Default:index.html.twig');
    }
}
