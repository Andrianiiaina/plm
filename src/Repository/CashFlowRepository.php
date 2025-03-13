<?php

namespace App\Repository;

use App\Entity\CashFlow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CashFlow>
 */
class CashFlowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashFlow::class);
    }

public function getTotalProjectExpense(int $projetId): float | null
{
    return $this->createQueryBuilder('c')
        ->select('SUM(c.amount) as totalExpenses')
        ->where('c.project = :projetId')
        ->andWhere('c.is_expense = :type')
        ->setParameter('projetId', $projetId)
        ->setParameter('type', false)
        ->getQuery()
        ->getSingleScalarResult();
}


    //    /**
    //     * @return CashFlow[] Returns an array of CashFlow objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CashFlow
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
