<?php

namespace App\Service;

use App\Entity\Reminder;

class ListService
{
    public static array $listUserRoles = [
        "ROLE_SUPERADMIN" => "Super admin",
        "ROLE_ADMIN" => "Admin",
        "ROLE_USER" => "Simple utilisateur"
    ];

   public static array $tender_status=[
     "A résumer"=>0,
     "A soumettre"=>1,
     "Soumis"=>2,
     "Recalé"=>3,
     "Remporté"=>4,
   ];

   public static array $tender_type=[
        "Marché privé"=>0,
        "Marché public"=>1,
   ];
   public static array $document_status = [
        "A compléter"=>0,
        "A vérifier"=>1,
        "A signer"=>2,
        "A envoyer"=>3,
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
        "2 semaine avant"=>14,
        "3 semaine avant"=>21,
    ];

 public static array  $tender_date_type=[ 
        "soumissions"=>Reminder::DATE_TYPE_SUBMISSION,
        "négociation"=>Reminder::DATE_TYPE_NEGOCIATION,
        "réponse"=>Reminder::DATE_TYPE_RESPONSE,
        "attribution"=>Reminder::DATE_TYPE_ATTRIBUTION,
        "début"=>Reminder::DATE_TYPE_START,
        "fin"=>Reminder::DATE_TYPE_END,
    ];
        
}