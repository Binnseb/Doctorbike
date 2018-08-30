<?php

namespace App\Form;

use App\Entity\QuestionReponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitQuestionReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];

        $builder
            ->add('ChoixQuestion', ChoixQuestionType::class, [
                'id' => $id,
                'mapped' => false,
                'label' => false
            ])

            ->add('QuestionSiOui', QuestionReponseType::class, [
                'id' => $id,
                'mapped' => false,
                'required' => true
            ])

            ->add('QuestionSiNon', QuestionReponseType::class, [
                'id' => $id,
                'mapped' => false,
                'required' => true
            ])

            ->add('Suivant', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary btn-pill btn-block']
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionReponse::class,
            $resolver->setRequired(['id'])
        ]);
    }
}
