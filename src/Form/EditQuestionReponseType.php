<?php

namespace App\Form;

use App\Entity\QuestionReponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditQuestionReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', TextareaType::class, [
                'label' => 'La question :',
                'required' => false
            ])
            ->add('aide', TextareaType::class, [
                'label' => 'Aide :',
                'required' => false
            ])
            ->add('image', ImageType::class, [
                'label' => 'Image d\'aide :',
                'required' => false
            ])
            ->add('Modifier', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionReponse::class,
        ]);
    }
}
