<?php

namespace App\Repository;

use App\Entity\NomModele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NomModele|null find($id, $lockMode = null, $lockVersion = null)
 * @method NomModele|null findOneBy(array $criteria, array $orderBy = null)
 * @method NomModele[]    findAll()
 * @method NomModele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NomModeleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NomModele::class);
    }

//    /**
//     * @return NomModele[] Returns an array of NomModele objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NomModele
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
