<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Entity\Contact;
use App\Entity\ContactGroup;
use App\Entity\Document;
use App\Entity\Notification;
use App\Entity\Tender;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private EntityManagerInterface $entityManager
    ) {

    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $user=$this->getUser();
        $tenderRepository=$this->entityManager->getRepository(Tender::class);
        $statistiques=$tenderRepository->findAllStatistic();
                

        return $this->render('admin/index.html.twig', [
            'chart' => $chart,
            'user'=>$user,
            'tenders'=>$tenderRepository->findBy(['isArchived'=>'false'],['createdAt'=>'DESC'],10),
            'total_tenders'=>array_sum($statistiques),
            'total_tender_by_status'=>$statistiques,
            'documents'=>  $this->entityManager->getRepository(Document::class)->findBy([],['limitDate'=>'ASC'],5),
            'calendars'=>$this->entityManager->getRepository(Calendar::class)->findBy([],['beginAt'=>'ASC'],5),
            'notifications'=> $this->entityManager->getRepository(Notification::class)->findBy(['user'=>$user],['createdAt'=>'DESC'],10),
            'week_tenders'=>$tenderRepository->findAllTenderForThisWeek(),
            'expired_soumission_date'=>$tenderRepository->findAllExpiredTender(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tender Management');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Tenders');
       // yield MenuItem::linkToCrud('Tenders', 'fa fa-tags', Tender::class);
        //yield MenuItem::linkToCrud('Documents', 'fa fa-tags', Document::class);
       // yield MenuItem::linkToCrud('Allotissement', 'fa fa-file-text', Allotissement::class);

        yield MenuItem::section('Contacts');
        yield MenuItem::linkToCrud('Contacts', 'fa fa-tags', Contact::class);
        yield MenuItem::linkToCrud('Group', 'fa fa-file-text', ContactGroup::class);
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('User', 'fa fa-file-text', User::class);
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');



        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
