<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('@App/Home/index.html.twig');
    }

    public function appAction($app)
    {
        return $this->render('@App/home/app.html.twig');
    }
}
