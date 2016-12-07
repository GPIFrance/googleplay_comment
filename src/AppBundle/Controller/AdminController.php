<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\User;
use AppBundle\Form\ApplicationType;
use AppBundle\Form\CommentaryType;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
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

    public function appAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $app = new Application();
        $form = $this->get('form.factory')->createBuilder(ApplicationType::class, $app)->getForm();

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $em->persist($app);
                    $em->flush();
                    $this->addFlash('success', "L'application {$app->getName()} en version {$app->getVersion()} a été ajouté");
                    return $this->redirectToRoute('app_admin_apps');
                } catch (\Exception $e) {
                    $this->addFlash('error', "Une erreur est survenue lors de l'ajout de l'application<br>{$e->getMessage()}");
                }
            } else {
                $this->addFlash('error', "Une erreur est survenue lors de la validation du formulaire");
            }
        }

        return $this->render('@App/admin/app.add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function appUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $app = $em->getRepository('AppBundle:Application')->find($id);
        $form = $this->get('form.factory')->createBuilder(ApplicationType::class, $app)->getForm();

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $em->persist($app);
                    $em->flush();
                    $this->addFlash('succes', "L'application {$app->getName()} a été mise à jour");
                } catch (\Exception $e) {
                    $this->addFlash('error', "Une erreur est survenue lors de la suppression de l'application");
                }
            } else {
                $this->addFlash('error', "Une erreur est survenue lors de la validation du formulaire");
            }
        }

        return $this->render('@App/admin/app.update.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function appDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $app = $em->getRepository('AppBundle:Application')->find($id);

        if (!$app) {
            $this->addFlash('notice', "Application introuvable dans la base de données");
        } else {
            try {
                $appName = $app->getName();
                $em->remove($app);
                $em->flush();
                $this->addFlash('success', "L'application $appName a bien été supprimée");
            } catch (\Exception $e) {
                $this->addFlash('error', "Une erreur est survenue lors de la suppression de l'application");
            }
        }

        return $this->redirectToRoute('app_admin_apps');
    }

    public function commentsAction($app_name, $user_username)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = null;
        $application = null;
        $user = null;
        $users = $em->getRepository('AppBundle:User')->findAll();
        $applications = $em->getRepository('AppBundle:Application')->findAll();

        if (!is_null($app_name) && is_null($user_username)) {
            $application = $em->getRepository('AppBundle:Application')->findOneBy(array('name' => $app_name));
            if (!$application) $this->addFlash('notice', "L'application $app_name n'existe pas dans la base de données");
            $comments = $em->getRepository('AppBundle:Commentary')->findBy(array('application' => $application));
        } else if (!is_null($app_name) && !is_null($user_username)) {
            $application = $em->getRepository('AppBundle:Application')->findOneBy(array('name' => $app_name));
            if (!$application) $this->addFlash('notice', "L'application $app_name n'existe pas dans la base de données");
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('username' => $user_username));
            if (!$user) $this->addFlash('notice', "L'utilisateur $user_username n'existe pas dans la base de données");
            $comments = $em->getRepository('AppBundle:Commentary')->findBy(array('user' => $user, 'application' => $application));
        } else {
            $comments = $em->getRepository('AppBundle:Commentary')->findAll();
        }

        return $this->render('@App/admin/comments.html.twig', array(
            'comments' => $comments,
            'users' => $users,
            'applications' => $applications
        ));
    }

    public function commentUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Commentary')->find($id);
        $form = $this->get('form.factory')->createBuilder(CommentaryType::class, $comment)->getForm();

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $em->persist($comment);
                    $em->flush();
                    $this->addFlash('success', "Le commentaire a bien été mise à jour");
                } catch (Exception $e) {
                    $this->addFlash('error', "Une erreur est survenue lors de la mise à jour du commentaire");
                }
            } else {
                $this->addFlash('error', "Une erreur est survenue lors de la validation du formulaire");
            }
        }

        return $this->render('@App/admin/comment.update.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView()
        ));
    }

    public function commentDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Commentary')->find($id);

        if (!$comment) {
            $this->addFlash('notice', "Le commentaire n'existe pas dans la base de données");
            return $this->redirectToRoute('app_admin_comments');
        }

        try {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', "Le commentaire a bien été supprimé");
            return $this->redirect($request->server->get('HTTP_REFERER'));
        } catch (\Exception $e) {
            $this->addFlash('error', "Une erreur est survenue lors de la suppression du commentaire");
        }

        return $this->redirectToRoute('app_admin_comments');
    }
}
