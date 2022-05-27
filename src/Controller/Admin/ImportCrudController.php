<?php

namespace App\Controller\Admin;

use App\Entity\Import;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ImportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Import::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield ChoiceField::new('type')->setChoices([
            'cve' => 'CVE',
            'data' => 'DATA',
        ]);
        yield TextField::new('path')->setTemplatePath('easy_admin/file.html.twig')->onlyOnIndex();
        yield ImageField::new('path')
            ->setBasePath('uploads')
            ->setUploadDir('public/uploads')
            ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')->onlyWhenCreating();
    }
}
