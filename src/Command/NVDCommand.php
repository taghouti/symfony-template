<?php

namespace App\Command;

use App\Controller\Admin\NVDController;
use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class NVDCommand extends Command
{
    protected static $defaultName = 'app:nvd:check';
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $parameterBag;
    private string $nvdFilesPath;
    private SessionInterface $session;
    private array $configs;

    public function __construct(SessionInterface $session, ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        set_time_limit(3600);
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->nvdFilesPath = $this->parameterBag->get('kernel.project_dir') . "/nvdlib/main.py";
        $this->session = $session;
        $this->configs = $this->entityManager->getRepository(Config::class)->findAll();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Run NVD checks and send emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $NVDController = new NVDController($this->session, $this->parameterBag, $this->entityManager);
        $io->success($NVDController->check(true));
        $io->success('Database updates check completed.');
        return 0;
    }
}