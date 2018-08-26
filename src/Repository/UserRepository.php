<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Méthode permettant de vérifier que l'utilisateur a bien confirmé son email avant de se connecter
     * @param string $username
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email AND u.confirmedAt IS NOT NULL')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Méthode permettant de rechercher un user (par son nom ou email) lors de la recherche de l'user
     * @param null|string $term
     * @return QueryBuilder
     */
    public function getWithSearchQueryBuilder(?string $term): QueryBuilder
    {
        $qb = $this->createQueryBuilder('user')
            ->addSelect('user');
        if ($term) {
            $qb->andWhere('user.email LIKE :term OR user.username LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb
            ->orderBy('user.username', 'ASC');

    }

    /**
     * Méthode permettant de trouver tous les scénarios d'un user pour l'affichage dans la gestion du compte
     * @param null|string $term
     * @param $id
     * @return QueryBuilder
     */
    public function getScenarioFromUser(?string $term, $id): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('u.scenarios');
        if ($term) {
            $qb->andWhere('scenario.nom LIKE :term')
                ->setParameter('term', '%' . $term . '%')
                ->andWhere('u.id = :id')
                ->setParameter('id', $id);
        }
        return $qb
            ->orderBy('u.scenarios.nom', 'ASC');
    }


//    /**
//     * @return User[] Returns an array of User objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
