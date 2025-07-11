<?php

namespace App\EventSubscriber;

use App\Entity\History;
use App\Entity\Notification;
use App\Event\UserAssignedToEntityEvent;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserAssignedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        
    }

    public function onUserAssignedEvent($event): void
    {
        

        $assigned_user = $event->getUser();
        $id = $event->getIdType();
        $type = $event->getType();

        $notification = new Notification();
        $notification->setUser($assigned_user);
        $notification->setType($type);
        $notification->setTypeId($id);
        $notification->setMessage(ListService::$notification_type[$type]);
        $notification->setIsRead(false);

        $this->em->persist($notification);
        $this->em->flush();
    }

   
    public static function getSubscribedEvents(): array
    {
        return [
            UserAssignedToEntityEvent::class => 'onUserAssignedEvent', 
        ];
    }
}
