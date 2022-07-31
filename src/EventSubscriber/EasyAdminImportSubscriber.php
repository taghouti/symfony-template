<?php

namespace App\EventSubscriber;

use App\Entity\Field;
use App\Entity\Import;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EasyAdminImportSubscriber implements EventSubscriberInterface
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
            BeforeEntityPersistedEvent::class => ['addImport'],
        ];
    }

    public function addImport(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Import)) {
            return;
        }
        try {
            $this->setImportPath($entity);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * @param Import $entity
     * @throws Exception
     */
    public function setImportPath(Import $entity): void
    {
        if (strtolower($entity->getType()) == 'cve') $this->import($entity);
    }

    /**
     * @throws Exception
     */
    public function import($entity)
    {
        $file = "/uploads/" . $entity->getPath();
        $spreadsheet = IOFactory::load(
            $this->parameterBag->get('kernel.project_dir') . "/public$file"
        );
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $deleteQuery = "";
        $query = "INSERT INTO cve VALUES ";
        $queries = [];
        foreach ($sheetData as $rowIndex => $currentRow)
        {
            if ($rowIndex == 1) continue;
            $currentQuery = " (null";
            $fields = $this->entityManager->getRepository(Field::class)->findAll();
            foreach ($fields as $index => $field) {
                $currentQuery .= ",'" . str_replace("'", "\'", $currentRow[chr($index + 65)]) . "'";
            }
            $currentQuery .= " ) ";
            $queries[] = $currentQuery;
            $deleteQuery .= "DELETE FROM cve WHERE cve='" . str_replace("'", "\'", $currentRow[chr(  65)]) . "';";
        }
        $query .= join(',', $queries);
        $this->entityManager->getConnection()->executeUpdate($deleteQuery);
        $this->entityManager->flush();
        $this->entityManager->getConnection()->executeUpdate($query);
        $this->entityManager->flush();
        $entity->setPath($file);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

    }

}