<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('username', TextType::class, array(
            'attr' => array(
              'placeholder' => "Nom d'utilisateur"
            )
          ))
          ->add('password', PasswordType::class, array(
            'attr' => array(
              'placeholder' => "Mot de passe"
            )
          ))
          ->add('mail', TextType::class, array(
            'attr' => array(
              'placeholder' => "Email"
            )
          ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'data_class' => 'AppBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'app_bundle_user_form';
    }
}
