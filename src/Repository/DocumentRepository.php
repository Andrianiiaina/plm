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

    //Results: docs in project user have responsibilit + docs user is assigned for.
    public function findUserDocuments($responsable,$number_to_fetch=10): array
      {
          return $this->createQueryBuilder('d')
              ->join('d.tender', 'p')
              ->orWhere('d.responsable = :responsable')
              ->orWhere('p.responsable = :responsable')
              ->setParameter('responsable', $responsable)
              ->setMaxResults($number_to_fetch)
              ->orderBy('d.createdAt', 'DESC')
              ->getQuery()
              ->getResult()
          ;
    }

    public function findTenderDocuments($tender): array
      {
          return $this->createQueryBuilder('d')
              ->Where('d.tender = :tender')
              ->setParameter('tender', $tender)
              ->orderBy('d.createdAt', 'DESC')
              ->getQuery()
              ->getResult()
          ;
    }

    
}
