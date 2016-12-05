<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IniController extends Controller
{
    public function dataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('username' => 'admin'));
        $encoder = $this->get('security.password_encoder');

        if (!$user) {
            try {
                $user = new User();
                $user
                    ->setUsername('admin')
                    ->setMail('gpi.craponne@gmail.com')
                    ->setRoles(array('ROLE_ADMIN'))
                    ->setPassword($encoder->encodePassword($user, 'gpi21400'));
                $em->persist($user);
                $em->flush();

                $this->addFlash('notice', "Utilisateur par défaut créé");
            } catch (\Exception $e) {
                $this->addFlash('error', "Une erreur est survenue lors de la création de l'utilisateur par défaut<br>" . $e->getMessage());
            }
        } else {
            $this->addFlash('notice', "Utilisateur par défaut déjà existant");
        }

        return $this->redirectToRoute('app_home_signin');
    }
}
