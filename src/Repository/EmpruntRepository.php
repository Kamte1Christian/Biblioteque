<?php

namespace App\Repository;

use App\Entity\Emprunt;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function findActiveEmpruntByUser(User $user): int
    {
        // Requête pour trouver les emprunts actifs d'un utilisateur
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(ex.id)')
            ->join('e.Exemplaire', 'ex')
            ->where('e.user = :user')
            ->andWhere('e.isBacked = false') // Assurez-vous que l'entité a une colonne `returned` pour indiquer si l'exemplaire est rendu
            ->setParameter('user', $user);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return Emprunt[] Returns an array of Emprunt objects
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

//    public function findOneBySomeField($value): ?Emprunt
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
