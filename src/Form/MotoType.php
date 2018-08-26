<?php

namespace App\Form;

use App\Repository\CylindreeRepository;
use App\Repository\MarqueRepository;
use App\Repository\ModeleRepository;
use App\Entity\Moto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('marque', EntityType::class, [
            'class' => 'App\Entity\Marque',
            'placeholder' => 'Sélectionnez la marque',
            'query_builder' => function (MarqueRepository $er) {
                return $er->createQueryBuilder('m')
                    ->orderBy('m.nom', 'ASC');
            },
            'mapped' => false,
        ])

        ->add('modele', EntityType::class,[
            'class' => 'App\Entity\Modele',
            'placeholder' =>'Sélectionnez le modèle',
            'choice_label' => 'nom',
            'query_builder' => function (ModeleRepository $er) {
                return $er->createQueryBuilder('m')
                    ->groupBy('m.nom')
                    ->orderBy('m.nom', 'ASC');
            },
            'mapped' => false,
        ])

        ->add('cylindree', EntityType::class, [
            'class' => 'App\Entity\Cylindree',
            'choice_label' => 'valeur',
            'query_builder' => function (CylindreeRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.valeur', 'ASC');
            },
            'placeholder' =>  'Sélectionnez la cylindrée',
            'mapped' => false,
        ])

        ->add('annee', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Moto::class
        ]);
    }
}