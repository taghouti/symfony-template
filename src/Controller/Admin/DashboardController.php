<?php

namespace App\Controller\Admin;

use App\Entity\CpeList;
use App\Entity\File;
use App\Entity\FileField;
use App\Entity\Import;
use App\Entity\Export;
use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('NVD GENERATOR');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Files', 'fa fa-file', File::class);
        yield MenuItem::linkToCrud('CPE', 'fa fa-key', CpeList::class);
        yield MenuItem::linkToCrud('Fields', 'fa fa-keyboard', FileField::class);
        yield MenuItem::linkToCrud('Import', 'fa fa-arrow-up', Import::class);
        yield MenuItem::linkToCrud('Export', 'fa fa-arrow-down', Export::class);
        yield MenuItem::linkToCrud('Members', 'fa fa-users', Member::class);
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');

    }


}
