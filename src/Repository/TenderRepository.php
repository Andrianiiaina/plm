<?php

namespace App\Repository;

use App\Entity\Tender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;



/**
 * @extends ServiceEntityRepository<Tender>
 */
class TenderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tender::class);
    }

    public function findRespoTenders($user): array
       {
           return $this->createQueryBuilder('t')    
               ->andWhere('t.responsable = :user')
               ->andWhere('t.status != 3 or t.status !=4')
               ->setParameter('user', $user)
               ->orderBy('t.createdAt', 'DESC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }
       public function findRespoArchivedTenders($user): array
       {
           return $this->createQueryBuilder('t')    
               ->andWhere('t.responsable = :user')
               ->andWhere('t.status = 3 or t.status = 4')
               ->setParameter('user', $user)
               ->orderBy('t.createdAt', 'DESC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

    //    /**
    //     * @return Tender[] Returns an array of Tender objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tender
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
