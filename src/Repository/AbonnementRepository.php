<?php

namespace App\Repository;

use App\Entity\Abonnement;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Abonnement>
 */
class AbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnement::class);
    }

     /**
     * Trouve l'abonnement actif d'un utilisateur (abonnement avec une date de fin dans le futur).
     *
     * @param User $user
     * @return Abonnement|null
     * @throws NonUniqueResultException
     */
    public function findActiveAbonnement(User $user): ?Abonnement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.abonne = :user')
            ->andWhere('a.date_fin > :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

     public function findSubscriptionsEndingIn(DateTime $endDate)
    {
        return $this->createQueryBuilder('a')
            ->where('a.date_fin = :endDate')
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

     public function findByEndDate(string $endDate): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.date_fin = :endDate')
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Abonnement[] Returns an array of Abonnement objects
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

//    public function findOneBySomeField($value): ?Abonnement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
