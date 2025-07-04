<?php

namespace App\Repository;

use App\Entity\Reminder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reminder>
 */
class ReminderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reminder::class);
    }

    public function findRemindersForTodayByResponsable($responsable): array
    {
        $today = new \DateTime();
        $startOfDay = (clone $today)->setTime(0, 0, 0); 
        $endOfDay = (clone $today)->setTime(23, 59, 59);  

        return $this->createQueryBuilder('r')
            ->join('r.tenderDate', 'td')
            ->join('td.tender', 'tender')
            ->where('tender.responsable = :responsable')
            ->andWhere('r.reminder_date < :endOfDay')
            ->setParameter('responsable', $responsable)
            ->setParameter('endOfDay', $endOfDay)
            ->getQuery()
            ->getResult();
    }
    //for admin
    public function findAllRemindersForToday(): array
    {
        $today = new \DateTime();
        $startOfDay = (clone $today)->setTime(0, 0, 0); 
        $endOfDay = (clone $today)->setTime(23, 59, 59); 

        return $this->createQueryBuilder('r')
            ->where('r.reminder_date < :endOfDay')
            ->setParameter('endOfDay', $endOfDay)
            ->getQuery()
            ->getResult();
    }

}
