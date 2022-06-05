<?php

namespace App\Controller\Admin;

use App\Entity\Field;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FieldCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Field::class;
    }

}
