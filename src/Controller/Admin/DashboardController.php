<?php

namespace App\Controller\Admin;

use App\Entity\Allotissement;
use App\Entity\Calendar;
use App\Entity\Contact;
use App\Entity\ContactGroup;
use App\Entity\Document;
use App\Entity\History;
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

    }

    
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tender Management');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToUrl('Home', 'fa fa-home', '/');
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
        yield MenuItem::section('');
        yield MenuItem::linkToCrud('History', 'fa fa-folder',History::class);
    }
}
