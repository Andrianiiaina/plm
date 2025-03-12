<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }


    public function getProjectsWithExpense()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p.id, p.title, p.budget, p.deadline ,respo.email as responsable, 
        SUM(c.amount) as expense')
        ->from('App\Entity\Project', 'p')
        ->leftJoin('p.cashFlows', 'c')
        ->leftJoin('p.milestones', 'm')
        ->join('p.responsable','respo')
        ->groupBy('p.id');

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Project[] Returns an array of Project objects
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

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
