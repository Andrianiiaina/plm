<?php

namespace App\Repository;

use App\Repository\TenderRepository;
use App\Entity\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calendar>
 */
class CalendarRepository extends ServiceEntityRepository
{
    public $tenderRepository;
    public function __construct(ManagerRegistry $registry, TenderRepository $tenderRepository)
    {
        parent::__construct($registry, Calendar::class);
        $this->tenderRepository = $tenderRepository;
    }

    public function findUserCalendar($responsable, $number_to_fetch=10, $term=''): array
    {
        $calendars = $this->createQueryBuilder('c')
            ->select('c.id, t.title as tender, c.title, c.beginAt, c.endAt')
            ->join('c.tender', 't')
            ->where('t.responsable = :responsable')
            ->andWhere('c.title LIKE :term')
            ->setParameter('responsable', $responsable)
            ->setParameter('term', '%' . $term . '%')
            ->setMaxResults($number_to_fetch)
            ->orderBy('c.beginAt','ASC')
            ->getQuery()
            ->getResult()
        ;
        $tenders = $this->tenderRepository->findTendersByRespo($responsable);
        $tender_date = $this->tenderRepository->findTendersForCalendar($tenders);
        $datas = array_merge($calendars , $tender_date);
        usort($datas, function ($a, $b) {
            return $a['beginAt'] <=> $b['beginAt'];
        });
        

        return $datas;
    }
    public function findAdminCalendar($number_to_fetch=10, $term=''): array
    {
        $date = new \DateTime();
        $date->modify('-10 days'); 
        $calendars = $this->createQueryBuilder('c')
            ->select('c.id, t.title as tender, c.title, c.beginAt')
            ->join('c.tender', 't')
            ->where('c.title LIKE :term')
            ->andWhere('c.beginAt >= :date')
            ->orderBy('c.beginAt', 'ASC') 
            ->setParameter('date', $date)
            ->setParameter('term', '%' . $term . '%')
            ->setMaxResults($number_to_fetch)
            ->orderBy('c.beginAt','ASC')
            ->getQuery()
            ->getResult()
        ;
        $tenders = $this->tenderRepository->findAdminTenders("");
        $tender_date = $this->tenderRepository->findTendersForCalendar($tenders);
        $datas = array_merge($calendars , $tender_date);
        usort($datas, function ($a, $b) {
            return $a['beginAt'] <=> $b['beginAt'];
        });
        return $datas;
    }
}
