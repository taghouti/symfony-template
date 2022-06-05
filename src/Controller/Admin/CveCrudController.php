<?php

namespace App\Controller\Admin;

use App\Entity\Cve;
use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CveCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public static function getEntityFqcn(): string
    {
        return Cve::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = $this->entityManager->getRepository(Field::class)->findAll();
        foreach ($fields as $field) {
            yield TextField::new($field->getKey())
                ->setLabel($field->getLabel())
                ->setSortable(true)
                ->setTextAlign('center');
        }
    }
}
