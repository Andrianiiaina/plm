<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\Reminder;
use App\Entity\Tender;

class ListService
{
    public static array $listUserRoles = [
        "ROLE_SUPERADMIN" => "Super admin",
        "ROLE_ADMIN" => "Admin",
        "ROLE_USER" => "Simple utilisateur"
    ];

   public static array $tender_status=[
     "A résumer"=>Tender::TO_RESUMED,
     "A soumettre"=>Tender::TO_SUBMITED,
     "Soumis"=>Tender::SUBMITED,
     "Recalé"=>Tender::LOST,
     "Remporté"=>Tender::WON,
   ];

   public static array $tender_type=[
        "Marché privé"=>0,
        "Marché public"=>1,
   ];
   public static array $document_status = [
        "A compléter"=>Document::TO_FILL,
        "A vérifier"=>Document::TO_VERIFY,
        "A signer"=>Document::TO_SIGN,
        "A envoyer"=>Document::TO_SEND,
    ];

    public static array $notification_type = [
        "0"=>"Un projet vous a été attribué.",
        "1"=>'Un tender vous a été attribué',
        "2"=>'Un document vous a été attribué',
    ];
    public static array $history_type = [
        "0"=>"Tender",
        "1"=>"Document",
    ];
    public static array  $reminders=[ 
        "1 jour avant"=>1,
        "2 jours avant"=>2,
        "3 jours avant"=>3,
        "4 jours avant"=>4,
        "5 jours avant"=>5,
        "1 semaine avant"=>7,
        "2 semaines avant"=>14,
        "3 semaines avant"=>21,
        "1 mois avant"=>30,
    ];

 public static array  $tender_date_type=[ 
        "Soumission"=>Reminder::DATE_TYPE_SUBMISSION,
        "Négociation"=>Reminder::DATE_TYPE_NEGOCIATION,
        "Réponse"=>Reminder::DATE_TYPE_RESPONSE,
        "Attribution"=>Reminder::DATE_TYPE_ATTRIBUTION,
        "Début"=>Reminder::DATE_TYPE_START,
        "Fin"=>Reminder::DATE_TYPE_END,
    ];
        
}