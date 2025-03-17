<?php

namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Event\UserAssignedToProjectEvent;
use App\Event\UserAssignedToTenderEvent;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserAssignedToProjectSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onUserAssignedToProjectEvent($event): void
    {
        $user = $event->getUser();
        $project = $event->getProject();
        $type = $event->getType();

        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setTypeId($project);
        $notification->setMessage(ListService::$notification_type[$type]);
        $notification->setIsRead(false);

        $this->em->persist($notification);
        $this->em->flush();
    }

   
    public static function getSubscribedEvents(): array
    {
        return [
            UserAssignedToProjectEvent::class => 'onUserAssignedToProjectEvent', 
        ];
    }
}
