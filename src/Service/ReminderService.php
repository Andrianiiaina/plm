<?php
namespace App\Service;

use App\Entity\Reminder;
use App\Entity\TenderDate;
use Doctrine\ORM\EntityManagerInterface;

class ReminderService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function reminderExists(TenderDate $tenderDate, Reminder $reminder): bool
    {
        $existingReminder = $this->entityManager->getRepository(Reminder::class)->findOneBy([
            'tenderDate' => $tenderDate,
            'date_type' => $reminder->getDateType(),
            'day_before' => $reminder->getDayBefore(),
        ]);

        return $existingReminder !== null;
    }

    
    public function getDateByType(TenderDate $tenderDate, int $dateType): ?\DateTime
    {
        switch ($dateType) {
            case Reminder::DATE_TYPE_SUBMISSION:
                return $tenderDate->getSubmissionDate();
            case Reminder::DATE_TYPE_NEGOCIATION:
                return $tenderDate->getNegociationDate();
            case Reminder::DATE_TYPE_RESPONSE:
                return $tenderDate->getResponseDate();
            case Reminder::DATE_TYPE_ATTRIBUTION:
                return $tenderDate->getAttributionDate();
            case Reminder::DATE_TYPE_START:
                return $tenderDate->getStartDate();
            case Reminder::DATE_TYPE_END:
                return $tenderDate->getEndDate();
            default:
                return null;
        }
    }

   public function update_reminder(TenderDate $tenderDate): bool
    {
        $reminders = $this->entityManager->getRepository(Reminder::class)->findBy(['tenderDate' => $tenderDate]);

        foreach ($reminders as $reminder) {
            $date_tender = $this->getDateByType($tenderDate, $reminder->getDateType());
            
            if ($date_tender === null) {
                $this->entityManager->remove($reminder);
            } else {
                // Cloner la date pour ne pas la modifier directement
                $new_date = clone $date_tender;
                
                $new_date = $new_date->modify("-" . $reminder->getDayBefore() . " days");
                
                // Si la date a changé, on met à jour le rappel
                if ($reminder->getReminderDate() != $new_date) {
                    $reminder->setReminderDate($new_date);
                    $this->entityManager->persist($reminder);
                }
            }
        }
        $this->entityManager->flush();
        return true;
    }



    
    public function createReminder(TenderDate $tenderDate, Reminder $reminder): bool
    {
        $date = $this->getDateByType($tenderDate, $reminder->getDateType());

        if ($date === null) {
            return false;"La date pour ce type de rappel est vide. Enregistrez d'abord la date.";
        }

        $reminderDate = $date->modify("-".$reminder->getDayBefore()." days");
        $reminder->setTenderDate($tenderDate);
        $reminder->setReminderDate($reminderDate);

        $this->entityManager->persist($reminder);
        $this->entityManager->flush();
        return true;
    }
}
