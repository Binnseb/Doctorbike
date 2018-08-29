<?php

namespace App\Repository;

use App\Entity\Historique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Historique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Historique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Historique[]    findAll()
 * @method Historique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Historique::class);
    }

    public function findVoteByUserMotoScenarioSolution($user, $moto, $scenario, $solution)
    {
        $qb = $this->createQueryBuilder('h')
            ->innerJoin('h.user', 'u')
            ->innerJoin('h.moto', 'm')
            ->innerJoin('h.scenario', 's')
            ->innerJoin('h.solution', 'sol')
            ->where('h.user = :user')
            ->setParameter('user', $user->getId())
            ->andWhere('h.moto = :moto')
            ->setParameter('moto', $moto->getId())
            ->andWhere('h.scenario = :scenario')
            ->setParameter('scenario', $scenario->getId())
            ->andWhere('h.solution = :solution')
            ->setParameter('solution', $solution->getId())
        ;

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Historique[] Returns an array of Historique objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Historique
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
