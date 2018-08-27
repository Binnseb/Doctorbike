<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use App\Entity\QuestionReponse;
use App\Repository\QuestionReponseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoixQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];

        $builder

            //Le champ permettant d'afficher toutes les questions qui seraient encore sans réponses
            ->add('listeDesQuestionsSansReponses', EntityType::class, [
                'class' => 'App\Entity\QuestionReponse',
                'query_builder' => function (QuestionReponseRepository $repo) use ($id) {
                    return $repo->createQueryBuilder('qr')
                        ->Join('qr.scenario', 's')
                        ->where('s.id = :sid')
                        ->setParameter('sid', $id)
                        ->andWhere('qr.estSolution = false AND qr.idQuestionSiOui IS NULL AND qr.idQuestionSiNon IS NULL')
                        ->orderBy('qr.id', 'ASC');
                },
                'choice_label' => 'question',
                'mapped' => false,
                'label' => 'Liste des questions sans réponses :',
                'placeholder' => 'Sélectionnez la question pour laquelle vous désirez écrire les réponses'
            ])

            //Le champ permettant de visualiser toutes les solutions existantes
            ->add('ListeDesSolutions', EntityType::class, [
                'class' => 'App\Entity\QuestionReponse',
                'query_builder' => function (QuestionReponseRepository $repo) use ($id) {
                    return $repo->createQueryBuilder('qr')
                        ->Join('qr.scenario', 's')
                        ->where('s.id = :sid')
                        ->setParameter('sid', $id)
                        ->andWhere('qr.estSolution = true')
                        ->orderBy('qr.id', 'ASC');
                },
                'choice_label' => 'question',
                'required' => false,
                'mapped' => false,
                'label' => 'Liste des solutions',
                'placeholder' => 'Veuillez ouvrir la liste pour visualiser les solutions'
            ])

            //Le champ permettant d'afficher toutes les questions existantes du scénario
            ->add('listeDesQuestionsExistantes', EntityType::class, [
                'class' => 'App\Entity\QuestionReponse',
                'placeholder' => 'Veuillez ouvrir la liste pour visualiser les questions',
                'query_builder' => function (QuestionReponseRepository $repo) use ($id) {
                    return $repo->createQueryBuilder('qr')
                        ->Join('qr.scenario', 's')
                        ->where('s.id = :sid')
                        ->andWhere('qr.estSolution = false')
                        ->setParameter('sid', $id)
                        ->orderBy('qr.id', 'ASC');
                },
                'choice_label' => 'question',
                'mapped' => false,
                'label' => 'Liste des questions existantes :',
                'required' => false,
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
