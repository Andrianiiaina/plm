<?php

namespace App\Controller;

use App\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Document;
use App\Entity\Notification;
use App\Entity\Tender;
use App\Repository\TenderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user=$this->getUser();
        $tenderRepository=$entityManager->getRepository(Tender::class);
      
        switch (true) {
            case $this->isGranted('ROLE_ADMIN'):
                $statistiques=$tenderRepository->findAllStatistic();
                $tenders=$tenderRepository->findBy(['isArchived'=>false],['createdAt'=>'DESC'],10);               ;
                $weekly_tenders=$tenderRepository->findAllTenderForThisWeek();
                $expiration=$tenderRepository->findAllExpiredTender();
                break;
            case $this->isGranted('ROLE_RESPO'):
                $statistiques=$tenderRepository->findRespoStatistic($user);
                $tenders=$tenderRepository->findBy(['responsable' => $user], ['createdAt' => 'DESC'],10);
                $weekly_tenders=$tenderRepository->findFilteredTendersForThisWeek($user);
                $expiration=$tenderRepository->findExpiredTender($user);
                break;
            
            default:
                return $this->render('home/reader.html.twig');
                break;
        }
        


        return $this->render('home/index.html.twig',[
            'user'=>$user,
            'tenders'=>$tenders,
            'total_tenders'=>array_sum($statistiques),
            'total_tender_by_status'=>$statistiques,
            'documents'=> $entityManager->getRepository(Document::class)->findWeeklyRespoDocuments($user),
            'calendars'=>$entityManager->getRepository(Calendar::class)->findBy([],['beginAt'=>'ASC'],5),
            'notifications'=> $entityManager->getRepository(Notification::class)->findBy(['user'=>$user],['createdAt'=>'DESC'],10),
            'week_tenders'=>$weekly_tenders,
            'expired_soumission_date'=>$expiration,
        ]);
    }

    #[Route('/tender_lists/by/status/{status}', name: 'app_tender_status', methods: ['GET'])]
    public function tender_lists_by_status(Request $request, $status,TenderRepository $tenderRepository, PaginatorInterface $paginator): Response
    {
        $tenders=$this->isGranted('ROLE_ADMIN')?
        $tenderRepository->findBy(['status'=>$status]):
        $tenderRepository->getTenderByStatus($this->getUser(),$status);
        $pagination = $paginator->paginate($tenders, $request->query->getInt('page', 1), 10);

        return $this->render('components/tender_search_result.html.twig', [
            'tenders' =>  $pagination,
            'status' => $status
        ]);
    }
}
