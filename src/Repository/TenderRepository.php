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

    public function findRespoTenders($user, $is_archived=false): array
       {
           return $this->createQueryBuilder('t')    
               ->andWhere('t.responsable = :user')
               ->andWhere('t.isArchived = :isArchived')
               ->setParameter('isArchived', $is_archived)
               ->setParameter('user', $user)
               ->orderBy('t.createdAt', 'DESC')
               ->getQuery()
               ->getResult()
           ;
       }

    public function searchTenderRespo(string $term,$user): array
    {
        return $this->createQueryBuilder('t')
        ->where('t.responsable = :user')
        ->andWhere('t.title LIKE :term OR t.contract_number LIKE :term')
        ->setParameter('term', '%' . $term . '%')
        ->setParameter('user', $user)
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function search(string $term): array
    {
        return $this->createQueryBuilder('t')
        ->where('t.title LIKE :term OR t.contract_number LIKE :term')
        ->setParameter('term', '%' . $term . '%')
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }
    public function getTenderByStatus($user,$status): array
    {
    return $this->createQueryBuilder('t')    
            ->andWhere('t.responsable = :user')
            ->andWhere('t.isArchived = false')
            ->andWhere('t.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTendersForThisWeek($user): array
    {
        $startOfWeek = new \DateTime('monday this week');
        $endOfWeek = new \DateTime('sunday this week 23:59:59');
    
        return $this->createQueryBuilder('t')
        
            ->where('t.responsable = :user')
            ->andWhere('t.submissionDate BETWEEN :start AND :end 
            OR t.responseDate BETWEEN :start AND :end 
            OR t.attributionDate BETWEEN :start AND :end
            OR t.negociationDate BETWEEN :start AND :end ')
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->setParameter('user', $user)
            ->orderBy('t.contract_number', 'ASC')
            ->getQuery()
            ->getResult();
            
    }

    //Récuperer les dates et le type de date de chaque semaine
    public function findFilteredTendersForThisWeek($user): array
    {
    $tenders = $this->findTendersForThisWeek($user); // Appelle la requête précédente
    $filteredTenders = [];

    foreach ($tenders as $tender) {
        if ($tender->getSubmissionDate() >= new \DateTime('monday this week') && $tender->getSubmissionDate() <= new \DateTime('sunday this week 23:59:59')) {
            $filteredTenders[] = [
                'id' => $tender->getId(),
                'contract_number' => $tender->getContractNumber(),
                'dateType' => 'Date de soumission',
                'dateValue' => $tender->getSubmissionDate()
            ];
        }
        if ($tender->getResponseDate() >= new \DateTime('monday this week') && $tender->getResponseDate() <= new \DateTime('sunday this week 23:59:59')) {
            $filteredTenders[] = [
                'id' => $tender->getId(),
                'contract_number' => $tender->getContractNumber(),
                'dateType' => 'Date de réponse',
                'dateValue' => $tender->getResponseDate()
            ];
        }
        if ($tender->getAttributionDate() >= new \DateTime('monday this week') && $tender->getAttributionDate() <= new \DateTime('sunday this week 23:59:59')) {
            $filteredTenders[] = [
                'id' => $tender->getId(),
                'contract_number' => $tender->getContractNumber(),
                'dateType' => "Date d'attribution",
                'dateValue' => $tender->getAttributionDate()
            ];
        }
        if ($tender->getNegociationDate() >= new \DateTime('monday this week') && $tender->getNegociationDate() <= new \DateTime('sunday this week 23:59:59')) {
            $filteredTenders[] = [
                'id' => $tender->getId(),
                'contract_number' => $tender->getContractNumber(),
                'dateType' => 'Date de négociation',
                'dateValue' => $tender->getNegociationDate()
            ];
        }
    }

    return $filteredTenders;
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
