<?php

namespace App\Controller\Admin;

use App\Entity\FileField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FileFieldCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FileField::class;
    }
}
