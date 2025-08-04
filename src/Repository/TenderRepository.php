<?php

namespace App\Repository;

use App\Entity\Tender;
use App\Service\ListService;
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

   

    public function findTendersByRespo($user, $is_archived=false, string $term=""): array
    {
        return $this->createQueryBuilder('t')
        ->where('t.responsable = :user')
        ->andWhere('t.title LIKE :term OR t.contract_number LIKE :term')
        ->andWhere('t.isArchived = :isArchived')
        ->setParameter('isArchived', $is_archived)
        ->setParameter('term', '%' . $term . '%')
        ->setParameter('user', $user)
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function findTenderStatisticByRespo($user): array
    {
        $results=$this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as total' )
            ->andWhere('t.responsable = :user')
            ->andWhere('t.isArchived = false')
            ->groupBy('t.status')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

            $data["0"]=0; $data["1"]=0;$data["2"]=0; $data["3"]=0;$data["4"]=0;
            foreach ($results as $result) {
                $data[$result['status']] = $result['total'];
            }
            return $data;
    }


    public function findTenderStatusByRespo($user,$status): array
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
    public function findExpiredTendersByRespo($user){
        return $this->createQueryBuilder('t')    
        ->join('t.tenderDate','td')
        ->select('t.id, t.contract_number,  t.status,  td.submissionDate')
        ->andWhere('t.responsable = :user and t.isArchived = false')
        ->andWhere('t.status = 1 or t.status=0')
        ->andWhere('td.submissionDate < :now ')
        ->setParameter('user', $user)
        ->setParameter('now', new \DateTimeImmutable())
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult()
    ;
    }

    //Récuperer les dates et le type de date à partir de la semaine
    public function findTendersForCalendar($tenders): array
    {
        $calendar_tenders = [];
        foreach ($tenders as $tender) {
            if ($tender->getTenderDate()->getSubmissionDate() >= new \DateTime('monday this week')) {
                $calendar_tenders[] = [
                    'title' => 'D. Soumission '.$tender->getContractNumber(),
                    'beginAt' => $tender->getTenderDate()->getSubmissionDate(),
                    'endAt' => $tender->getTenderDate()->getSubmissionDate(),
                    'id' => $tender->getId(),
                    'type' => "tender",
                ];
            }
            if ($tender->getTenderDate()->getResponseDate() >= new \DateTime('monday this week')) {
                $calendar_tenders[] = [
                    'title' => 'D. Réponse '.$tender->getContractNumber(),
                    'beginAt' => $tender->getTenderDate()->getResponseDate(),
                    'endAt' => $tender->getTenderDate()->getResponseDate(),
                    'id' => $tender->getId(),
                    'type' => "tender",
                ];
            }
            if ($tender->getTenderDate()->getAttributionDate() >= new \DateTime('monday this week')) {
                $calendar_tenders[] = [
                    'title' => "D. Attribution ".$tender->getContractNumber(),
                    'beginAt' => $tender->getTenderDate()->getAttributionDate(),
                    'endAt' => $tender->getTenderDate()->getAttributionDate(),
                    'id' => $tender->getId(),
                    'type' => "tender",
                ];
            }
            if ($tender->getTenderDate()->getNegociationDate() >= new \DateTime('monday this week')) {
                $calendar_tenders[] = [
                    'title' => 'D. Négociation '.$tender->getContractNumber(),
                    'beginAt' => $tender->getTenderDate()->getNegociationDate(),
                    'endAt' => $tender->getTenderDate()->getNegociationDate(),
                    'id' => $tender->getId(),
                    'type' => "tender",
                ];
            }
        }

        return $calendar_tenders;
    }

    ###############ADMIN SECTION######################### 

    public function findAdminTenders(string $term="", $is_archived=false): array
    {
        return $this->createQueryBuilder('t')
        ->where('t.title LIKE :term OR t.contract_number LIKE :term')
        ->andWhere('t.isArchived = :isArchived')
        ->setParameter('isArchived', $is_archived)
        ->setParameter('term', '%' . $term . '%')
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function findAdminTenderStatistics()
    {
        $results=$this->createQueryBuilder('t')
            ->where('t.isArchived = false')
            ->select('t.status, COUNT(t.id) as total' )
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();
            
            $data["0"]=0; $data["1"]=0;$data["2"]=0; $data["3"]=0;$data["4"]=0;
            foreach ($results as $result) {
                $data[$result['status']] = $result['total'];
            }
            return $data;
    }

    # tender qui devait etre résumer ou soumis.
    public function findAdminExpiredTenders(){
        return $this->createQueryBuilder('t')    
        ->join('t.tenderDate','td')
        ->select('t.id, t.contract_number,  t.status,  td.submissionDate')
        ->where('t.isArchived = false')
        ->andWhere('t.status = 1 or t.status=0')
        ->andWhere('td.submissionDate < :now')
        ->setParameter('now', new \DateTimeImmutable())
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult()
    ;
    }
}
