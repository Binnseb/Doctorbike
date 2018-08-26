<?php

namespace App\Form;

use App\Entity\QuestionReponse;
use Symfony\Component\Form\CallbackTransformer;
use App\Repository\QuestionReponseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];

        $builder
            //Le champ permettant de sélectionner si la question est une solution, une nouvelle question ou repointe vers une question existante
            ->add('choixNouvelleRepOuQuestionAnt', EntityType::class, [
                'class' => 'App\Entity\QuestionReponse',
                'query_builder' => function(QuestionReponseRepository $repo) use ($id){
                            return $repo->createQueryBuilder('qr')
                                ->Join('qr.scenario', 's')
                                ->where('s.id = :sid')
                                ->setParameter('sid', $id)
                                ->orderBy('qr.id', 'ASC');
                },
                'choice_label' => 'question',
                'label' => 'La question est :',
                'required' => false,
                'placeholder' => 'Est une nouvelle question',
                'mapped' => false
            ])

            ->add('estSolution', CheckboxType::class, [
                'mapped' => false,
                'required' => false
            ])

            ->add('question', TextareaType::class, [
                'label' => 'La question :',
                'attr' => array('placeholder' => 'Insérez ici votre réponse...',
                                'style' => 'height: 80px'),
                'required' => false
            ])

            ->add('aide', TextareaType::class, [
                'label' => 'Texte d\'aide :',
                'attr' => array('placeholder' => 'Insérez ici un éventuel texte d\'aide (conseils, indications,...) pour aider l\'utilisateur à répondre à votre question',
                                'style' => 'height: 110px' ),
                'required' => false
            ])

            ->add('image', ImageType::class, [
                'mapped' => false,
                'label' => 'Image d\'aide :',
                'required' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionReponse::class,
            $resolver->setRequired(['id'])
        ]);
    }
}
