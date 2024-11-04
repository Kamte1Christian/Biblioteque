<?php

namespace App\Repository;

use App\Entity\EmpruntExemplaire;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmpruntExemplaire>
 */
class EmpruntExemplaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmpruntExemplaire::class);
    }

    public function countActiveExemplairesForUser(User $user): int
    {
        return $this->createQueryBuilder('ee')
            ->select('COUNT(ee.id)')
            ->join('ee.emprunt', 'e')
            ->where('e.user = :user')
            ->andWhere('ee.dateRetour IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return EmpruntExemplaire[] Returns an array of EmpruntExemplaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmpruntExemplaire
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
