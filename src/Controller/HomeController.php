<?php

namespace App\Controller;

use App\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Document;
use App\Entity\Project;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, ): Response
    {   
        $this->denyAccessUnlessGranted('ROLE_RESPO');
        $user=$this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $documents=$entityManager->getRepository(Document::class)->findAll();
            $calendars=$entityManager->getRepository(Calendar::class)->findAll();
            $projects= $entityManager->getRepository(Project::class)->findAll();
         }elseif($this->isGranted('ROLE_RESPO')){
            $documents=$entityManager->getRepository(Document::class)->findDocs($user, 5);
            $calendars=$entityManager->getRepository(Calendar::class)->findUserCalendar($user, 5);
            $projects= $entityManager->getRepository(Project::class)->findBy(['responsable_id'=>$user]);
         }else{
            $projects=[];
            $documents=[];
            $calendars=[];
         }
        $grouped_projects_by_status = [];
        foreach ($projects as $project) {
            $status = $project->getStatus(); 
            $grouped_projects_by_status[$status][] = $project;
        }
        
        foreach (ListService::$project_status as $status) {
            $project_status[$status] = key_exists($status,$grouped_projects_by_status)? count($grouped_projects_by_status[$status]) : 0;
        }
   

        return $this->render('home/index.html.twig',[
            'user'=>$user,
            'projects'=>$grouped_projects_by_status,
            'statusProject'=>$project_status,
            'documents'=> $documents,
            'calendars'=>$calendars,
            
        ]);
    }
}
