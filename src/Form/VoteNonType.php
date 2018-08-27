<?php

namespace App\Form;

use App\Entity\Historique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VoteNonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('VoteNon', SubmitType::class, [
                'attr' => ['class' => "btn btn-danger btn-block col-sm-4",
                    'style' => "display: block; float: right;  width: 50%;",
                    'required' => false],
                'label' => "Non"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Historique::class,
            $resolver->setRequired(['id'])
        ]);
    }
}
