<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Nom'
                )
            ))
            ->add('version', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Version'
                )
            ))
            ->add('comment', TextareaType::class, array(
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Commentaire'
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application'
        ));
    }

    public function getName()
    {
        return 'app_bundle_application_type';
    }
}
