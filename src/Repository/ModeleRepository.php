<?php

namespace App\Repository;

use App\Entity\Modele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Modele|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modele|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modele[]    findAll()
 * @method Modele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Modele::class);
    }

    /**
     * Méthode permettant de rechercher un modèle (nom ou marque)
     * @param null|string $term
     * @return QueryBuilder
     */
    public function getWithSearchQueryBuilder(?string $term):QueryBuilder
    {
        $qb = $this->createQueryBuilder('modele')
            ->Join('modele.cylindree', 'cylindree')
            ->innerJoin('modele.marque', 'marque')
            ->addSelect('modele');
        if ($term) {
            $qb->andWhere('marque.nom LIKE :term OR modele.nom LIKE :term OR cylindree.valeur LIKE :term')
                ->setParameter('term', '%' . $term . '%')
            ;
        }
        return $qb
            ->orderBy('marque.nom', 'ASC')
            ->addOrderBy('modele.nom', 'ASC')
            ->addOrderBy('cylindree.valeur', 'DESC')
            ;
    }

    public function findIfExist($modele) {
        $qb = $this->createQueryBuilder('mo')
            ->innerJoin('mo.marque', 'ma')
            -> innerJoin('mo.cylindree', 'c')
            ->andWhere('ma.nom = :nomMarque')
                ->setParameter('nomMarque', $modele->getMarque()->getNom())
            ->andWhere('mo.nom = :nomModele')
                ->setParameter('nomModele', $modele->getNom())
            ->andWhere('c.valeur = :valeurCylindree')
                ->setParameter('valeurCylindree', $modele->getCylindree()->getValeur())
        ;

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Modele[] Returns an array of Modele objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Modele
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
