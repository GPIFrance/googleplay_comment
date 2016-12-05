<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('');
    }

    public function usersAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render('@App/admin/users.html.twig', array(
            'users' => $users
        ));
    }

    public function userAddAction()
    {
        return $this->render('@App/admin/user.add.html.twig');
    }

    public function userUpdateAction($id)
    {
        return $this->render('@App/admin/user.update.html.twig');
    }

    public function userDeleteAction($id)
    {
        return $this->redirectToRoute('app_admin_users');
    }

    public function appsAction()
    {
        $apps = $this->getDoctrine()->getRepository('AppBundle:Application')->findAll();

        return $this->render('@App/admin/apps.html.twig', array(
            'apps' => $apps
        ));
    }

}
