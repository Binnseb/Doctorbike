<?php

namespace App\Form;

use App\Entity\Scenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ScenarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Le nom de votre scénario'
            ])
            ->add('motCle', CollectionType::class, [
                'mapped' => false,
                'entry_type' => MotCleType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true
             ])
            ->add('question', TextareaType::class, [
                'mapped' => false,
                'label' => 'La première question du scénario'
            ])
            ->add('aide', TextareaType::class, [
                'mapped' => false,
                'label' => 'Aide, explications...',
                'required' => false
            ])
            ->add('image', ImageType::class, [
                'mapped' => false,
                'label' => 'Image d\'aide',
                'required' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Scenario::class,
        ]);
    }
}
