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
       function combine_calendars_and_tender_data($calendars, $tenders){

        $tender_date = $this->tenderRepository->findTendersForCalendar($tenders);
        $datas = array_merge($calendars , $tender_date);
        usort($datas, function ($a, $b) {
            return $a['beginAt'] <=> $b['beginAt'];
        });
        return $datas;
    }

    public function findRespoWeeklyCalendar($responsable){
        $startOfWeek = new \DateTime('monday this week');
        $calendars = $this->createQueryBuilder('c')
            ->select('c.title, c.beginAt')
            ->join('c.tender', 't')
            ->where('t.responsable = :responsable')
            ->andWhere('c.beginAt >= :date')
            ->setParameter('date', $startOfWeek)
            ->setParameter('responsable', $responsable)
            ->setMaxResults(15)
            ->getQuery()
            ->getResult()
        ;
        $tenders = $this->tenderRepository->findTendersByRespo($responsable);
        return $this->combine_calendars_and_tender_data($calendars,$tenders);
    }



    public function findRespoCalendar($responsable, $number_to_fetch=null, $term=''): array
    {
        $calendars = $this->createQueryBuilder('c')
            ->select('c.id, t.title as tender, c.title, c.beginAt, c.endAt')
            ->join('c.tender', 't')
            ->where('t.responsable = :responsable')
            ->andWhere('c.title LIKE :term')
            ->setParameter('responsable', $responsable)
            ->setParameter('term', '%' . $term . '%')
            ->setMaxResults($number_to_fetch)
            ->getQuery()
            ->getResult()
        ;
        $tenders = $this->tenderRepository->findTendersByRespo($responsable);
        return $this->combine_calendars_and_tender_data($calendars,$tenders);

    }
    public function findAdminCalendar($number_to_fetch=null, $term=''): array
    {
        $calendars = $this->createQueryBuilder('c')
            ->select('c.id, t.title as tender, c.title, c.beginAt, c.endAt')
            ->join('c.tender', 't')
            ->where('c.title LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->setMaxResults($number_to_fetch)
            ->orderBy('c.beginAt','ASC')
            ->getQuery()
            ->getResult()
        ;
        $tenders = $this->tenderRepository->findAdminTenders();
        return $this->combine_calendars_and_tender_data($calendars,$tenders);
    }

    public function findAdminWeeklyCalendar(){
        $startOfWeek = new \DateTime('monday this week');
        $calendars = $this->createQueryBuilder('c')
            ->select('c.title, c.beginAt')
            ->join('c.tender', 't')
            ->andWhere('c.beginAt >= :date')
            ->setParameter('date', $startOfWeek)
            ->setMaxResults(15)
            ->getQuery()
            ->getResult()
        ;
       
        $tenders = $this->tenderRepository->findAdminTenders();
        return $this->combine_calendars_and_tender_data($calendars,$tenders);
    }


    public function getCalendars($user, $is_admin, $search_term){
        if($search_term == ""){
           return $is_admin? $this->findAdminWeeklyCalendar() : $this->findRespoWeeklyCalendar($user);
        }else{
            return $is_admin? $this->findAdminCalendar(): $this->findRespoCalendar($user);
        }
    }
 
}
