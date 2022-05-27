<?php

namespace App\Controller\Admin;

use App\Entity\CpeList;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CpeListCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CpeList::class;
    }
}
