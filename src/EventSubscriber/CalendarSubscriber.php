<?php

namespace App\EventSubscriber;

use App\Repository\CalendarRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\SetDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CalendarRepository $calendarRepository,
        private readonly UrlGeneratorInterface $router,
        private readonly Security $security
    ){
    }

    public static function getSubscribedEvents():array
    {
        return [
            SetDataEvent::class => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(SetDataEvent $setDataEvent)
    {
        $user = $this->security->getUser();
        $start = $setDataEvent->getStart();
        $end = $setDataEvent->getEnd();
        $calendars = $this->calendarRepository
        ->createQueryBuilder('c')
        ->join('c.tender', 'p')
        ->where('p.responsable = :responsable')
        ->andWhere('c.beginAt BETWEEN :start and :end OR c.endAt BETWEEN :start and :end')
        ->setParameter('start', $start->format('Y-m-d H:i:s'))
        ->setParameter('end', $end->format('Y-m-d H:i:s'))
        ->setParameter('responsable', $user)
        ->orderBy('c.beginAt','ASC')
        ->getQuery()
        ->getResult()
    ;
    foreach ($calendars as $calendar) {
        $calendarEvent = new Event(
            $calendar->getTitle(),
            $calendar->getBeginAt(),
            $calendar->getEndAt() 
        );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             */
            $calendarEvent->setOptions([
                'backgroundColor' => 'purple',
                'borderColor' => 'purple',
            ]);
            $calendarEvent->addOption(
                'url',
                $this->router->generate('app_calendar_show', [
                    'id' => $calendar->getId(),
                ])
            );

            $setDataEvent->addEvent($calendarEvent);
        }
    }
}