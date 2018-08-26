<?php

namespace App\Repository;

use App\Entity\Moto;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Moto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moto[]    findAll()
 * @method Moto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotoRepository extends ServiceEntityRepository
{
    /**
     * MotoRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Moto::class);
    }

    /**
     * Méthode permettant de faire une recherche sur les motos (marque, modele, cylindrée ou année) lors de la recherche de l'user
     * @param null|string $term
     * @return QueryBuilder
     */
    public function getWithSearchQueryBuilder(?string $term):QueryBuilder
    {
        $qb = $this->createQueryBuilder('moto')
            ->Join('moto.modele', 'modele')
            ->innerJoin('modele.marque', 'marque')
            ->innerJoin('modele.cylindree', 'cylindree')
            ->addSelect('moto');
        if ($term) {
            $qb->andWhere('marque.nom LIKE :term OR cylindree.valeur LIKE :term OR moto.annee LIKE :term OR modele.nom LIKE :term')
                ->setParameter('term', '%' . $term . '%')
            ;
        }
        return $qb
            ->orderBy('marque.nom', 'ASC')
            ->addOrderBy('modele.nom', 'ASC')
            ;
    }

    /**
     * Méthode permettant de trouver une moto si elle existe déjà
     * @param $modele
     * @param $annee
     * @return mixed
     */
    public function findIfExist($modele, $annee)
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.modele', 'mo')
            ->innerJoin('mo.marque', 'ma')
            ->innerJoin('mo.cylindree', 'c')
            ->where('m.annee = :annee')
                ->setParameter('annee', $annee)
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
//     * @return Moto[] Returns an array of Moto objects
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
    public function findOneBySomeField($value): ?Moto
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
