<?php

namespace App\Repository;

use App\Entity\Abonnements;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Abonnements>
 */
class AbonnementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnements::class);
    }

     /**
     * Trouve l'abonnement actif d'un utilisateur (abonnement avec une date de fin dans le futur).
     *
     * @param User $user
     * @return Abonnement|null
     * @throws NonUniqueResultException
     */
    public function findActiveAbonnement(User $user): ?Abonnements
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.dateFin > :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Abonnements[] Returns an array of Abonnements objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Abonnements
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
