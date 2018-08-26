<?php

namespace App\Form;

use App\Entity\Historique;
use App\Repository\MotoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('VoteOui', SubmitType::class, [
                'attr' => ['class' => "btn btn-success btn-block col-sm-4",
                            'style' => "display: block; float: left;  width: 50%;",
                            'required' => false],
                'label' => "Oui"
            ])

            ->add('VoteNon', SubmitType::class, [
                'attr' => ['class' => "btn btn-danger btn-block col-sm-4",
                            'style' => "display: block; float: right;  width: 50%;",
                            'required' => false],
                'label' => "Non"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Historique::class,
            $resolver->setRequired(['id'])
        ]);
    }
}
