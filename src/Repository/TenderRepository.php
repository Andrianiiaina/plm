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

    public function findRespoStatistic($user): array
    {
        $results=$this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as total' )
            ->andWhere('t.responsable = :user')
            ->groupBy('t.status')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
            $data = [];
            foreach ($results as $result) {
                $data[$result['status']] = $result['total'];
            }
            return $data;
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

    # tender qui devait etre résumer ou soumis.
    public function findExpiredTender($user){
        return $this->createQueryBuilder('t')    
        ->join('t.tenderDate','td')
        ->select('t.id, t.contract_number,  t.status,  td.submissionDate')
        ->andWhere('t.responsable = :user and t.isArchived = false')
        ->andWhere('td.submissionDate < :now and t.status = 1 or t.status=0')
        ->setParameter('user', $user)
        ->setParameter('now', new \DateTimeImmutable())
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult()
    ;
    }

    //Récuperer les dates et le type de date de chaque semaine
    public function filterTendersForThisWeek($tenders): array
    {
         // Appelle la requête précédente
        $filteredTenders = [];

        foreach ($tenders as $tender) {
            if ($tender->getTenderDate()->getSubmissionDate() >= new \DateTime('monday this week') && $tender->getTenderDate()->getSubmissionDate() <= new \DateTime('sunday this week 23:59:59')) {
                $filteredTenders[] = [
                    'id' => $tender->getId(),
                    'contract_number' => $tender->getContractNumber(),
                    'dateType' => 'Date de soumission',
                    'dateValue' => $tender->getTenderDate()->getSubmissionDate()
                ];
            }
            if ($tender->getTenderDate()->getResponseDate() >= new \DateTime('monday this week') && $tender->getTenderDate()->getResponseDate() <= new \DateTime('sunday this week 23:59:59')) {
                $filteredTenders[] = [
                    'id' => $tender->getId(),
                    'contract_number' => $tender->getContractNumber(),
                    'dateType' => 'Date de réponse',
                    'dateValue' => $tender->getTenderDate()->getResponseDate()
                ];
            }
            if ($tender->getTenderDate()->getAttributionDate() >= new \DateTime('monday this week') && $tender->getTenderDate()->getAttributionDate() <= new \DateTime('sunday this week 23:59:59')) {
                $filteredTenders[] = [
                    'id' => $tender->getId(),
                    'contract_number' => $tender->getContractNumber(),
                    'dateType' => "Date d'attribution",
                    'dateValue' => $tender->getTenderDate()->getAttributionDate()
                ];
            }
            if ($tender->getTenderDate()->getNegociationDate() >= new \DateTime('monday this week') && $tender->getTenderDate()->getNegociationDate() <= new \DateTime('sunday this week 23:59:59')) {
                $filteredTenders[] = [
                    'id' => $tender->getId(),
                    'contract_number' => $tender->getContractNumber(),
                    'dateType' => 'Date de négociation',
                    'dateValue' => $tender->getTenderDate()->getNegociationDate()
                ];
            }
        }
        usort($filteredTenders, function ($a, $b) {
            return $a['dateValue'] <=> $b['dateValue'];
        });

        return $filteredTenders;
    }
    public function findFilteredTendersForThisWeek($user):array{
        $startOfWeek = new \DateTime('monday this week');
        $endOfWeek = new \DateTime('sunday this week 23:59:59');
    
        $tenders= $this->createQueryBuilder('t')
            ->join('t.tenderDate','td')
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
        return $this->filterTendersForThisWeek($tenders);
    }

    ###############ADMIN SECTION######################### 

    public function findallStatistic()
    {
        $results=$this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as total' )
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();
            $data = [];
            foreach ($results as $result) {
                $data[$result['status']] = $result['total'];
            }
            return $data;
    }

        # tender qui devait etre résumer ou soumis.
        public function findAllExpiredTender(){
            return $this->createQueryBuilder('t')    
            ->join('t.tenderDate','td')
            ->select('t.id, t.contract_number,  t.status,  td.submissionDate')
            ->where('t.isArchived = false')
            ->andWhere('td.submissionDate < :now and t.status = 1 or t.status=0')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        }

        public function findAllTenderForThisWeek():array{
            $startOfWeek = new \DateTime('monday this week');
            $endOfWeek = new \DateTime('sunday this week 23:59:59');
        
            $tenders= $this->createQueryBuilder('t')
                ->join('t.tenderDate','td')
                ->where('td.submissionDate BETWEEN :start AND :end 
                OR td.responseDate BETWEEN :start AND :end 
                OR td.attributionDate BETWEEN :start AND :end
                OR td.negociationDate BETWEEN :start AND :end ')
                ->setParameter('start', $startOfWeek)
                ->setParameter('end', $endOfWeek)
                ->orderBy('t.contract_number', 'ASC')
                ->getQuery()
                ->getResult();
            return $this->filterTendersForThisWeek($tenders);
        }
    

    
}
