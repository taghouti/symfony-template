<?php

namespace App\Controller\Admin;

use App\Entity\Field;
use App\Entity\Config;
use App\Entity\Cpe;
use App\Entity\Cve;
use App\Entity\Export;
use App\Entity\Import;
use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('R-MAX')->renderSidebarMinimized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('CVEs', 'fa fa-bug', Cve::class);
        yield MenuItem::linkToCrud('CPEs', 'fa fa-store', Cpe::class);
        yield MenuItem::linkToCrud('Fields', 'fa fa-table', Field::class);
        yield MenuItem::linkToCrud('Import', 'fa fa-arrow-up', Import::class);
        yield MenuItem::linkToCrud('Export', 'fa fa-arrow-down', Export::class);
        yield MenuItem::linkToCrud('Members', 'fa fa-users', Member::class);
        yield MenuItem::linkToCrud('Configs', 'fa fa-cog', Config::class);
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');

    }


}
