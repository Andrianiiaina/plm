<?php

namespace App\Service;

class ListService
{
    public static array $listUserRoles = [
        "ROLE_SUPERADMIN" => "Super admin",
        "ROLE_ADMIN" => "Admin",
        "ROLE_USER" => "Simple utilisateur"
    ];

   public static array $project_status=[
     "En préparation"=>0,
     "En rédaction"=>1,
     "En attente de réponse"=>2,
     "Remporté"=>3,
     "Perdu"=>4,
   ];

   public static array $project_type=[
        "It Infrastructure"=>0,
        "MULTICLOUD & AUTOMATION"=>1,
        "IoT & EMBEDDED SYSTEMS"=>2,
        "MULTICLOUD & AUTOMATION"=>3,
        "HPC-AI"=>4,
   ];
   public static array $file_status = [
        "A compléter"=>0,
        "A vérifier"=>1,
        "A signer"=>2,
        "Signer et valider"=>3,
    ];


}