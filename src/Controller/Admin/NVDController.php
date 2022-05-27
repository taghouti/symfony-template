<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class NVDController extends AbstractController
{
    #[Route('/nvd', name: 'nvd')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/nvd/check', name: 'nvd_check')]
    public function check(): RedirectResponse
    {
        die('checking');
    }

    #[Route('/nvd/update', name: 'nvd_update')]
    public function update(): RedirectResponse
    {
        die('update');
    }
}
