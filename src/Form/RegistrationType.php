<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Votre pseudo'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse mail'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Votre mot de passe'
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmation du mot de passe'
            ])
            ->add('dateDeNaissance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Votre date de naissance'
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
