<?php

namespace App\Controller\Admin;

use App\Entity\Cve;
use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
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
        foreach ($fields as $index => $field) {
            yield TextField::new($field->getName())
                ->setLabel($field->getLabel())
                ->setSortable(false)
                ->setTextAlign('center');
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
