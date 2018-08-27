<?php

namespace App\Form;

use App\Entity\Historique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VoteOuiType extends AbstractType
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
