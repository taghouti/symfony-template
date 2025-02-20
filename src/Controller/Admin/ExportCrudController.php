<?php

namespace App\Controller\Admin;

use App\Entity\Export;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Export::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield ChoiceField::new('type')->setChoices([
            'cve' => 'CVE',
        ]);
        yield TextField::new('path')->setTemplatePath('easy_admin/cve.html.twig')->onlyOnIndex();
    }
}
