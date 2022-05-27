<?php

namespace App\EventSubscriber;

use App\Entity\File;
use App\Entity\FileField;
use App\Entity\FilePath;
use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            BeforeEntityPersistedEvent::class => ['addFilePath'],
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

    public function addFilePath(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof FilePath)) {
            return;
        }
        $this->setPath($entity);
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
     * @param FilePath $entity
     */
    public function setPath(FilePath $entity): void
    {
        $path = $entity->getPath();
        $entity->setPath($path);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        if (strtolower($entity->getType()) == 'data') $this->import($path);
    }

    public function import($path) {
        $spreadsheet = IOFactory::load(
            $this->parameterBag->get('kernel.project_dir') . "/public$path"
        );
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $query = "INSERT INTO file VALUES ";
        foreach ($sheetData as $rowIndex => $currentRow)
        {
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

}