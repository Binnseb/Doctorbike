<?php

namespace App\Repository;

use App\Entity\QuestionReponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method QuestionReponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionReponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionReponse[]    findAll()
 * @method QuestionReponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionReponseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QuestionReponse::class);
    }

    /**
     * Méthode permettant de trouver toutes les questions du scénario triées par ordre d'ajout
     * @param $id
     * @return mixed
     */
    public function findAllQuestions($id)
    {
        $qb = $this->createQueryBuilder('qr')
            ->innerJoin('qr.scenario', 's')
            ->where('s.id = :sid')
            ->setParameter('sid', $id)
            ->orderBy('qr.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Méthode permettant de trouver toutes les questions du scénario qui sont sans réponses (si idQuestionSi... est null)
     * @param $id
     * @return mixed
     */
    public function findAllQuestionWithoutAnswer($id)
    {
        $qb = $this->createQueryBuilder('qr')
            ->innerJoin('qr.scenario', 's')
            ->where('s.id = :sid')
            ->setParameter('sid', $id)
            ->andWhere('qr.estSolution = false AND qr.idQuestionSiOui IS NULL AND qr.idQuestionSiNon IS NULL')
            ->orderBy('qr.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Méthode permettant de retrouver toutes les questions du scénario qui ne sont pas des solutions
     * (Ces questions seront affichées dans la liste déroule de chaque formulaire)
     * @param $id
     * @return mixed
     */
    public function findAllQuestionsAreNotSolution($id)
    {
        $qb = $this->createQueryBuilder('qr')
            ->innerJoin('qr.scenario', 's')
            ->where('s.id = :sid')
            ->setParameter('sid', $id)
            ->andWhere('qr.estSolution = false')
            ->orderBy('qr.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Méthode permettant de retrouver la première question d'un scénario pour le jouer
     * @param $id
     * @return mixed
     */
    public function findFirstQuestion($id)
    {
        $qb = $this->createQueryBuilder('qr')
            ->innerJoin('qr.scenario', 's')
            ->where('s.id = :sid')
            ->setParameter('sid', $id)
            ->andWhere('qr.estPremiereQuestion = true')
            ->orderBy('qr.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return QuestionReponse[] Returns an array of QuestionReponse objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuestionReponse
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
