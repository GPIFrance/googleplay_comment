<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATE_REMEMBERED')){
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

    public function appAction($app)
    {
        return $this->render('@App/home/app.html.twig');
    }
}
