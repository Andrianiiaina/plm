<?php

namespace App\Controller;

use App\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Document;
use App\Entity\Notification;
use App\Entity\Tender;
use App\Repository\TenderRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user=$this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $documents=$entityManager->getRepository(Document::class)->findBy([],['createdAt'=>'DESC'],5);
            $calendars=$entityManager->getRepository(Calendar::class)->findBy([],['beginAt'=>'ASC'],5);
            $tenders= $entityManager->getRepository(Tender::class)->findBy([],['createdAt'=>'DESC'],12);
         }elseif($this->isGranted('ROLE_RESPO')){
            $documents=$entityManager->getRepository(Document::class)->findDocs($user, 5);
            $calendars=$entityManager->getRepository(Calendar::class)->findUserCalendar($user, 5);
            $tenders= $entityManager->getRepository(Tender::class)->findBy(['responsable'=>$user],['createdAt'=>'DESC'],10);
         }elseif($this->isGranted('ROLE_USER')){
            return $this->render('home/reader.html.twig');
         }
        
         $grouped_tenders_by_status = array_fill_keys(ListService::$tender_status, []);

         foreach ($tenders as $tender) {
             $grouped_tenders_by_status[$tender->getStatus()][] = $tender;
         }
  
        foreach ($grouped_tenders_by_status as $key=>$value) {
            $tender_status[$key] = count($value);
        }
 
        $notifications= $entityManager->getRepository(Notification::class)->findBy(['user'=>$user],['createdAt'=>'DESC'],10);
   

        return $this->render('home/index.html.twig',[
            'user'=>$user,
            'tenders'=>$tenders,
            'statusTender'=>$tender_status,
            'documents'=> $documents,
            'calendars'=>$calendars,
            'notifications'=>$notifications,
            
        ]);
    }

    #[Route('app_tender_status/{status}', name: 'app_tender_status', methods: ['GET'])]
    public function tender_status($status,TenderRepository $tenderRepository): Response
    {
        
        $tenders = $this->isGranted('ROLE_ADMIN')?
        $tenderRepository->findBy(['status'=>$status]):
        $tenderRepository->getTenderByStatus($this->getUser(),$status);
        
        return $this->render('tender/index.html.twig', [
            'tenders' =>  $tenders,
            'searchTerm' => ""
        ]);
    }
}
