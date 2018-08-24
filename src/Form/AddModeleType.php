<?php

namespace App\Form;

use App\Entity\Modele;
use App\Repository\CylindreeRepository;
use App\Repository\MarqueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddModeleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('marque', EntityType::class, array(
                'class' => 'App\Entity\Marque',
                'choice_label' => 'nom',
                'query_builder' => function (MarqueRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.nom', 'ASC');
                }
            ))
            ->add('nom', TextType::class, array(
                'label' => 'Modele'
            ))
            ->add('cylindree', EntityType::class, array(
                'class' => 'App\Entity\Cylindree',
                'choice_label' => 'valeur',
                'query_builder' => function (CylindreeRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.valeur', 'ASC');
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Modele::class,
        ]);
    }
}
