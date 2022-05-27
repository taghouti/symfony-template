<?php

namespace App\Controller\Admin;

use App\Entity\File;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return File::class;
    }

    public function uploadFile(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();

        try {
            $this->addFlash('notice', 'Hello');
        } catch (Exception $e) {
            $this->addFlash('error', $this->translator->trans('flash.error', ['message' => $e->getMessage()]));
        }

        $url = $this->crudUrlGenerator->build()
            ->setController(FileCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl()
        ;

        return $this->redirect($url);
    }

}
