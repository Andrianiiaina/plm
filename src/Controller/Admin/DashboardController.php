<?php

namespace App\Controller\Admin;

use App\Entity\Allotissement;
use App\Entity\Calendar;
use App\Entity\Contact;
use App\Entity\ContactGroup;
use App\Entity\Document;
use App\Entity\Notification;
use App\Entity\Tender;
use App\Entity\TenderDate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
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

        return $this->redirectToRoute('admin_tender_index');
        /**
         * 
         *      $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
       * $chart->setData([
        *    'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        *    'datasets' => [
        *        [
        *            'label' => 'My First dataset',
        *            'backgroundColor' => 'rgb(255, 99, 132)',
        *            'borderColor' => 'rgb(255, 99, 132)',
        *            'data' => [0, 10, 5, 2, 20, 30, 45],
        *        ],
        *    ],
        *]);

        *$chart->setOptions([
        *    'scales' => [
        *        'y' => [
        *            'suggestedMin' => 0,
        *            'suggestedMax' => 100,
        *        ],
        *    ],
        *]);
         */
   




        $users= $this->entityManager->getRepository(User::class)->findAll();
        $new_users=[];
        
        $statistics=$this->entityManager->getRepository(Tender::class)->findAllStatistic();
        foreach($users as $user){
            if(!$user->getRoles()){
                $new_users[]=$user;
            }
        }



        return $this->render('admin/index.html.twig', [
            'chart' => $chart,
            'new_users'=>$new_users,
        ]);

    }

    
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tender Management');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::section('Tenders');
        yield MenuItem::linkToCrud('Tenders', 'fa fa-tags', Tender::class);
        yield MenuItem::linkToCrud('Tender_dates', 'fa fa-calendar-check-o', TenderDate::class);
        yield MenuItem::linkToCrud('Documents', 'fa fa-folder', Document::class);
        yield MenuItem::linkToCrud('Allotissement', 'fa fa-clone', Allotissement::class);

        yield MenuItem::section('Contacts');
        yield MenuItem::linkToCrud('Contacts', 'fa fa-address-book', Contact::class);
        yield MenuItem::linkToCrud('Group', 'fa fa-users', ContactGroup::class);
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class);
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');



        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
