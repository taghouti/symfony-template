<?php

namespace App\Controller\Admin;

use App\Entity\Cpe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CpeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cpe::class;
    }
}
