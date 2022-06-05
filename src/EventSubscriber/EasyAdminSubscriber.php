<?php

namespace App\EventSubscriber;

use App\Entity\File;
use App\Entity\FileField;
use App\Entity\Import;
use App\Entity\Export;
use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use http\Exception\BadQueryStringException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    private $entityManager;
    private $passwordEncoder;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->parameterBag = $parameterBag;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['addMember'],
            BeforeEntityUpdatedEvent::class => ['updateMember'],
            BeforeEntityPersistedEvent::class => ['addImport'],
            BeforeEntityPersistedEvent::class => ['addExport'],
        ];
    }

    public function updateMember(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Member)) {
            return;
        }
        $this->setPassword($entity);
    }

    public function addMember(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Member)) {
            return;
        }
        $this->setPassword($entity);
    }

    public function addImport(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Import)) {
            return;
        }
        $this->setImportPath($entity);
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
     * @param Member $entity
     */
    public function setPassword(Member $entity): void
    {
        $pass = $entity->getPassword();

        $entity->setPassword(
            $this->passwordEncoder->encodePassword(
                $entity,
                $pass
            )
        );
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param Import $entity
     */
    public function setImportPath(Import $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        if (strtolower($entity->getType()) == 'data') $this->import($path);
    }

    /**
     * @param Export $entity
     */
    public function setExportPath(Export $entity): void
    {
        if (strtolower($entity->getType()) == 'data') $this->export($entity);
    }

    public function import($path)
    {
        $spreadsheet = IOFactory::load(
            $this->parameterBag->get('kernel.project_dir') . "/public$path"
        );
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $query = "INSERT INTO file VALUES ";
        foreach ($sheetData as $rowIndex => $currentRow) {
            if ($rowIndex == 1) continue;
            $query .= " (null";
            $fields = $this->entityManager->getRepository(FileField::class)->findAll();
            foreach ($fields as $index => $field) {
                $query .= ",'" . str_replace("'", "\'", $currentRow[chr($index + 65)]) . "'";
            }
            $query .= " ) ";
        }
        $this->entityManager->getConnection()->executeUpdate($query);
        $this->entityManager->flush();

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
        $sheet->setCellValue('A1', 'Hello World !');
        $fields = $this->entityManager->getRepository(FileField::class)->findAll();
        foreach ($fields as $index => $field) {
            $sheet->setCellValue(chr($index + 65) . "1", $field->getLabel());
        }
        $rows = $this->entityManager->getRepository(File::class)->findAll();
        foreach ($rows as $rowIndex => $row) {
            foreach ($fields as $index => $field) {
                $key = "get" . ucfirst($field->getKey());
                $key = str_replace('_', '', ucwords($key, '_'));
                $sheet->setCellValue(chr($index + 65) . ($rowIndex + 2), $row->$key());
            }
        }
        $sheet->setTitle("CPE LIST");
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