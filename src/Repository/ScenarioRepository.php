<?php

namespace App\Repository;

use App\Entity\Scenario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Scenario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scenario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scenario[]    findAll()
 * @method Scenario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScenarioRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Scenario::class);
    }

    /**
     * Méthode permettant de faire une recherche sur les scénario (nom ou mots clé)
     * @param null|string $term
     * @return QueryBuilder
     */
    public function getWithSearchQueryBuilder(?string $term):QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->Join('s.motCle', 'm')
            ->addSelect('s');
        if ($term) {
            $qb->andWhere('s.nom LIKE :term OR m.nom LIKE :term')
                ->setParameter('term', '%' . $term . '%')
            ;
        }
        return $qb
            ->orderBy('s.nom', 'ASC')
            ;
    }



//    /**
//     * @return Scenario[] Returns an array of Scenario objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Scenario
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
