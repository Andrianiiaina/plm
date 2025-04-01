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

        switch (true) {
            case $this->isGranted('ROLE_ADMIN'):
                $documents = $entityManager->getRepository(Document::class)->findBy([],['createdAt'=>'DESC'],5);
                $tenders = $entityManager->getRepository(Tender::class)->findBy([],['createdAt'=>'DESC'],12);
                break;
            case $this->isGranted('ROLE_RESPO'):
                $documents = $entityManager->getRepository(Document::class)->findUserDocuments($user, 5);
                $tenders = $entityManager->getRepository(Tender::class)->findBy(['responsable' => $user], ['createdAt' => 'DESC'], 10);
                break;
            case $this->isGranted('ROLE_USER'):
                return $this->render('home/reader.html.twig');
        }

        
         $grouped_tender_by_status = array_fill_keys(ListService::$tender_status, []);
         
        foreach ($tenders as $tender) {
             $grouped_tender_by_status[$tender->getStatus()][] = $tender;
        }

        $total_tender_by_status = [];
        foreach ($grouped_tender_by_status as $key=>$value) {
            $total_tender_by_status[$key] = count($value);
        }

    
        return $this->render('home/index.html.twig',[
            'user'=>$user,
            'tenders'=>$tenders,
            'total_tender_by_status'=>$total_tender_by_status,
            'documents'=> $documents,
            'calendars'=>$entityManager->getRepository(Calendar::class)->findBy([],['beginAt'=>'ASC'],5),
            'notifications'=> $entityManager->getRepository(Notification::class)->findBy(['user'=>$user],['createdAt'=>'DESC'],10),
            'week_tenders'=>$entityManager->getRepository(Tender::class)->findFilteredTendersForThisWeek($user),
            
        ]);
    }

    #[Route('/tender_lists/by/status/{status}', name: 'app_tender_status', methods: ['GET'])]
    public function tender_lists_by_status($status,TenderRepository $tenderRepository): Response
    {
        
        return $this->render('tender/index.html.twig', [
            'tenders' =>  $this->isGranted('ROLE_ADMIN')?
                $tenderRepository->findBy(['status'=>$status]):
                $tenderRepository->getTenderByStatus($this->getUser(),$status),
            'searchTerm' => ""
        ]);
    }
}
