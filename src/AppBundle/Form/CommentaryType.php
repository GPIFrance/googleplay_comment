<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CommentaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, array(
                'attr' => array(
                    'placeholder' => 'Commentaire'
                )
            ))
            ->add('dtCreation', DateTimeType::class, array(
                'attr' => array(
                    'readonly' => true
                ),
                'format' => DateTimeType::HTML5_FORMAT,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commentary'
        ));
    }

    public function getName()
    {
        return 'app_bundle_commentary_type';
    }
}
