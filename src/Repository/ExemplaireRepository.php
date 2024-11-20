<?php

namespace App\Repository;

use App\Entity\Emprunts;
use App\Entity\Exemplaire;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exemplaire>
 */
class ExemplaireRepository extends ServiceEntityRepository
{
    private EmpruntRepository $emprunt;
    public function __construct(ManagerRegistry $registry, EmpruntRepository $emprunt)
    {
        parent::__construct($registry, Exemplaire::class);
        $this->emprunt=$emprunt;
    }

     public function countActiveExemplaireForUser(User $user): int
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
//     * @return Exemplaire[] Returns an array of Exemplaire objects
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

//    public function findOneBySomeField($value): ?Exemplaire
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
