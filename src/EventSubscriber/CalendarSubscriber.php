<?php

namespace App\EventSubscriber;

use App\Repository\CalendarRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\SetDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CalendarRepository $calendarRepository,
        private readonly UrlGeneratorInterface $router
    ) {}

    public static function getSubscribedEvents():array
    {
        return [
            SetDataEvent::class => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(SetDataEvent $setDataEvent)
    {
        $start = $setDataEvent->getStart();
        $end = $setDataEvent->getEnd();
        $filters = $setDataEvent->getFilters();

        // Modify the query to fit to your entity and needs
        // Change calendar.beginAt by your start date property
        $calendars = $this->calendarRepository
            ->createQueryBuilder('calendar')
            ->where('calendar.beginAt BETWEEN :start and :end OR calendar.endAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($calendars as $calendar) {
            // this create the events with your data (here calendar data) to fill calendar
            $calendarEvent = new Event(
                $calendar->getTitle(),
                $calendar->getBeginAt(),
                $calendar->getEndAt() // If the end date is null or not defined, a all day event is created.
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

            // finally, add the event to the CalendarEvent to fill the calendar
            $setDataEvent->addEvent($calendarEvent);
        }
    }
}