<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    public function userAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->get('form.factory')->createBuilder(UserType::class, $user)->getForm();
        $encoder = $this->get('security.password_encoder');

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', "L'utilisateur {$user->getUsername()} a bien été ajouté");
                    return $this->redirectToRoute('app_admin_users');
                } catch (\Exception $e) {
                    $this->addFlash('notice', "Utilisateur {$user->getUsername()} déjà existant");
                }
            } else {
                $this->addFlash('error', "Une erreur c'est produite lors de la validation du formulaire");
            }
        }

        return $this->render('@App/admin/user.add.html.twig', array(
          'form' => $form->createView()
        ));
    }

    public function userUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        $encoder = $this->get('security.password_encoder');
        $password = $user->getPassword();
        $userUpdate = new User();
        $form = $this->get('form.factory')->createBuilder(UserType::class, $user)->getForm();

        if ($user->getRoles()[0] == 'ROLE_ADMIN') {
            $this->addFlash('notice', "L'utilisateur admin ne peut pas être modifié");
            return $this->redirectToRoute('app_admin_users');
        }

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $userUpdate = $user;
                    if ($form->get('password') == '') {
                        $userUpdate->setPassword($password);
                    } else {
                        $userUpdate->setPassword($encoder->encodePassword($userUpdate, $password));
                    }
                    $em->persist($userUpdate);
                    $em->flush();
                    $this->addFlash('success', "L'utilisateur {$userUpdate->getUsername()} a été mise à jour");
                    return $this->redirectToRoute('app_admin_users');
                } catch (\Exception $e) {
                    $this->addFlash('error', "Une erreur est survenue lors de la mise à jour de l'utilisateur {$userUpdate->getUsername()}");
                }
            } else {
                $this->addFlash('error', "Une erreur est survenue lors de la validation du formulaire");
            }
        }

        return $this->render('@App/admin/user.update.html.twig', array(
          'user' => $user,
          'form' => $form->createView()
        ));
    }

    public function userDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);

        if (!$user) {
            $this->addFlash('error', "Utilisateur introuvable en base de données");
            return $this->redirectToRoute('app_admin_users');
        }

        if ($user->getRoles()[0] == 'ROLE_ADMIN') {
            $this->addFlash('notice', "L'utilisateur admin ne peut être supprimé");
            return $this->redirectToRoute('app_admin_users');
        }

        try {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', "L'utilisateur {$user->getUsername()} a bien été supprimé");
        } catch (\Exception $e) {
            $this->addFlash('error', "Une erreur est survenue lors de la supression de l'utilisateur <b>{$user->getUsername()}</b><br>{$e->getMessage()}");
        }

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
