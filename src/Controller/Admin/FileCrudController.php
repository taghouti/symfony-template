<?php

namespace App\Controller\Admin;

use App\Entity\File;
use App\Entity\FileField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FileCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return File::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = $this->entityManager->getRepository(FileField::class)->findAll();
        foreach ($fields as $field) {
            yield TextField::new($field->getKey())
                ->setLabel($field->getLabel())
                ->setSortable(true)
                ->setTextAlign('center');
        }
    }
}
