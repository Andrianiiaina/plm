<?php

namespace App\Controller\Admin;

use App\Entity\Allotissement;
use App\Entity\Contact;
use App\Entity\ContactGroup;
use App\Entity\Document;
use App\Entity\Tender;
use App\Entity\User;
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
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
       

        return $this->render('admin/index.html.twig', [
            'chart' => $chart,
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
        yield MenuItem::linkToCrud('Tenders', 'fa fa-tags', Tender::class);
        yield MenuItem::linkToCrud('Documents', 'fa fa-tags', Document::class);
        yield MenuItem::linkToCrud('Allotissement', 'fa fa-file-text', Allotissement::class);

        yield MenuItem::section('Contacts');
        yield MenuItem::linkToCrud('Contacts', 'fa fa-tags', Contact::class);
        yield MenuItem::linkToCrud('Group', 'fa fa-file-text', ContactGroup::class);
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('User', 'fa fa-file-text', User::class);
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');



        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
