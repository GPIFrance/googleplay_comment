<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commentary;
use AppBundle\Entity\User;
use AppBundle\Form\CommentaryType;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('@App/Home/index.html.twig');
    }

    public function signupAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $encoder = $this->get('security.password_encoder');

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                    $em->persist($user);
                    $em->flush();

                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->get('security.token_storage')->setToken($token);
                    $this->get('session')->set('_security_main', serialize($token));

                    $this->addFlash('success', "Bienvenue sur GooglePlayComment !");

                } catch (\Exception $e) {
                    $this->addFlash('error', "Nom d'utilisateur non disponible");
                }
            } else {
                $this->addFlash('error', "Use erreur est survenue lors de la validation du formulaire");
            }
        }

        return $this->render('@App/home/signup.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function signinAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATE_REMEMBERED')) {
            return $this->redirectToRoute('app_app');
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('@App/home/signin.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ));
    }

    public function appsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $applications = $em->getRepository('AppBundle:Application')->findAll();

        return $this->render('@App/home/apps.html.twig', array(
            'applications' => $applications
        ));
    }

    public function appDetailsAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $application = $em->getRepository('AppBundle:Application')->findOneBy(array('name' => $name));

        return $this->render('@App/home/app.details.html.twig', array(
            'application' => $application
        ));
    }

    public function appCommentsAction(Request $request, $name)
    {
        /** @var User $user */
        $user = $this->getUser();
        $comment = new Commentary();

        // On test si l'utilisateur est identifié
        if (!$user) {
            $this->addFlash('notice', "Vous devez être identifié pour commenter une application");
            return $this->redirectToRoute('app_home_signin');
        }

        $em = $this->getDoctrine()->getManager();
        $application = $em->getRepository('AppBundle:Application')->findOneBy(array('name' => $name));
        $form = $this->get('form.factory')->createBuilder(CommentaryType::class, $comment)->getForm();

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $comment->setUser($user);
                    $comment->setApplication($application);
                    $em->persist($comment);
                    $em->flush();
                    $this->addFlash('success', "Votre commentaire a bien été enregistré");
                } catch (\Exception $e) {
                    $this->addFlash('error', "Une erreur est survenue lors de l'enregistrement de votre commentaire");
                }
            } else {
                $this->addFlash('error', "Une erreur est survenue lors de la validation du formulaire");
            }
        }

        $comments = $em->getRepository('AppBundle:Commentary')->findBy(array('application' => $application));

        return $this->render('@App/home/app.comments.html.twig', array(
            'application' => $application,
            'comments' => $comments,
            'form' => $form->createView()
        ));
    }
}
