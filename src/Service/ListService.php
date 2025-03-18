<?php

namespace App\Service;

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
        "Archiver"=>4
    ];
    public static array $task_status = [
        "A faire"=>0,
        "En cours"=>1,
        "A valider"=>2,
        "Terminé"=>3,
        "Annulé"=>4,
    ];

    public static array $project_status = [
        "Non commencé"=>0,
        "En cours"=>1,
        "A valider"=>2,
        "Terminé"=>3,
        "Suspendu"=>4,
        "Annulé"=>5,
    ];

    public static array $devise = [
        "Euro"=>"EUR",
        "Dollar"=>'USD',
        "Ariary"=>'MGA',
    ];
    public static array $notification_type = [
        "0"=>"Un projet vous a été attribué.",
        "1"=>'Un tender vous a été attribué',
        "2"=>'Un document vous a été attribué',
    ];

}