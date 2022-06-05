<?php

namespace App\EventSubscriber;

use App\Entity\Cve;
use App\Entity\Export;
use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use http\Exception\BadQueryStringException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EasyAdminExportSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->parameterBag = $parameterBag;
    }

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addExport'],
        ];
    }

    public function addExport(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Export)) {
            return;
        }
        $this->setExportPath($entity);
    }

    /**
     * @param Export $entity
     */
    public function setExportPath(Export $entity): void
    {
        if (strtolower($entity->getType()) == 'cve') $this->export($entity);
    }

    public function export(Export $entity)
    {
        $spreadsheet = new Spreadsheet();
        $postfix = uniqid();
        $file = '/uploads/' . $entity->getName() . '-' .
            $postfix . '.xlsx';
        $path = $this->parameterBag->get('kernel.project_dir') .
            '/public/uploads/' . $entity->getName() . '-' .
            $postfix . '.xlsx';
        /* @var $sheet Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $fields = $this->entityManager->getRepository(Field::class)->findAll();
        foreach ($fields as $index => $field) {
            $sheet->setCellValue(chr($index + 65) . "1", $field->getLabel());
        }
        $rows = $this->entityManager->getRepository(Cve::class)->findAll();
        foreach ($rows as $rowIndex => $row) {
            foreach ($fields as $index => $field) {
                $name = "get" . ucfirst($field->getName());
                $name = str_replace('_', '', ucwords($name, '_'));
                $sheet->setCellValue(chr($index + 65) . ($rowIndex + 2), $row->$name());
            }
        }
        $sheet->setTitle("CVE LIST");
        $writer = new Xlsx($spreadsheet);
        try {
            $writer->save($path);
            $entity->setPath($file);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new BadQueryStringException($e->getMessage());
        }

    }


}