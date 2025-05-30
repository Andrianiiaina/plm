<?php

namespace App\EventSubscriber;

use App\Entity\History;
use App\Event\HistoryEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HistorySubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em, private readonly Security $security)
    {
        $this->em=$em;
    }

    public function onTenderOperation($event): void
    {
        $history = new History();
        $history->setActorId( $event->getActorId());
        $history->setActions($event->getAction());
        $history->setType($event->getType());
        $history->setTypeId($event->getTypeId());
        
        $this->em->persist($history);
        $this->em->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HistoryEvent::class => 'onTenderOperation',
        ];
    }
}
