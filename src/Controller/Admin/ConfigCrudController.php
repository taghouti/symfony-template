<?php

namespace App\Controller\Admin;

use App\Entity\Config;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Config::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('configName');
        yield TextField::new('configValue');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }
}
