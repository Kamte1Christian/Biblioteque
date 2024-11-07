<?php

namespace App\Repository;

use App\Entity\Emprunts;
use App\Entity\Exemplaires;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exemplaires>
 */
class ExemplairesRepository extends ServiceEntityRepository
{
    private EmpruntsRepository $emprunt;
    public function __construct(ManagerRegistry $registry, EmpruntsRepository $emprunt)
    {
        parent::__construct($registry, Exemplaires::class);
        $this->emprunt=$emprunt;
    }

     public function countActiveExemplairesForUser(User $user): int
    {
        $emprunt= $this->emprunt->findBy(['user'=>$user]);
        return $this->createQueryBuilder('ee')
            ->select('COUNT(ee.id)')
            ->join('ee.emprunt', 'e')
            ->where('e.emprunt = :emprunt')
            ->andWhere('ee.state ='.false)
            ->setParameter('emprunt', $emprunt)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Exemplaires[] Returns an array of Exemplaires objects
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

//    public function findOneBySomeField($value): ?Exemplaires
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
