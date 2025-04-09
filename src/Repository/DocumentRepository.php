<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    //Results: docs in project user have responsibility + docs user is assigned for.
    public function search(string $term,$responsable): array
    {
        return $this->createQueryBuilder('d')
        ->join('d.tender', 'p')
        ->orWhere('d.responsable = :responsable')
        ->orWhere('p.responsable = :responsable')
        ->andWhere('d.name LIKE :term OR d.status LIKE :term OR p.title LIKE :term OR p.contract_number LIKE :term' )
        ->andWhere('p.isArchived = False and d.isArchived= False')
        ->setParameter('term', '%' . $term . '%')
        ->setParameter('responsable', $responsable)
        ->orderBy('d.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }


    public function findWeeklyUserDocuments($responsable,): array
    {
        $startOfWeek = new \DateTime('monday this week');
        $endOfWeek = new \DateTime('sunday this week 23:59:59');
        return $this->createQueryBuilder('d')
            ->join('d.tender', 'p')
            ->orWhere('d.responsable = :responsable')
            ->orWhere('p.responsable = :responsable')
            ->andWhere('p.isArchived = False and p.status != 2 and p.status != 3 and d.isArchived=False')
            ->andWhere('d.limitDate BETWEEN :start AND :end')
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->setParameter('responsable', $responsable)
            ->orderBy('d.limitDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
  }


    public function findTenderDocuments($tender): array
      {
          return $this->createQueryBuilder('d')
              ->Where('d.tender = :tender')
              ->andWhere('d.isArchived=False')
              ->setParameter('tender', $tender)
              ->orderBy('d.createdAt', 'DESC')
              ->getQuery()
              ->getResult()
          ;
    }

    
}
