<?php

namespace App\Controller;

use App\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Document;
use App\Entity\Tender;


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
            $tenders= $entityManager->getRepository(Tender::class)->findAll();
         }elseif($this->isGranted('ROLE_RESPO')){
            $documents=$entityManager->getRepository(Document::class)->findDocs($user, 5);
            $calendars=$entityManager->getRepository(Calendar::class)->findUserCalendar($user, 5);
            $tenders= $entityManager->getRepository(Tender::class)->findBy(['responsable_id'=>$user]);
         }else{
            $tenders=[];
            $documents=[];
            $calendars=[];
         }
  
        foreach (ListService::$tender_status as $status) {
            $result=$entityManager->getRepository(Tender::class)->findBy(['status'=>$status]);
            $tender_status[$status] = count($result);
        }
   

        return $this->render('home/index.html.twig',[
            'user'=>$user,
            'tenders'=>$tenders,
            'statusTender'=>$tender_status,
            'documents'=> $documents,
            'calendars'=>$calendars,
            
        ]);
    }
}
