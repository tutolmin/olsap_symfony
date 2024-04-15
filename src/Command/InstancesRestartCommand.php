<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
    name: 'app:instances:restart',
    description: 'Restarts certain instance',
)]
class InstancesRestartCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
    // Instances repo
    private InstancesRepository $instanceRepository;
    private LxcManager $lxcService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager,
            LxcManager $lxcService) {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->lxcService = $lxcService;

        // get the Instances repository
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
    }

    protected function configure(): void {
        $this
                ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to restart')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = is_string($input->getArgument('name')) ? strval($input->getArgument('name')) : "";

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

        // look for a specific instance object
        $instance = $this->instanceRepository->findOneByName($name);

        if (!$instance) {

            $io->error('Instance "%s" was NOT found in the database.');
            return Command::FAILURE;
        }

        $io->note(sprintf('Sending "restart" command for "%s"', $name));

        $this->lxcService->restart($instance);

        $io->success('Success!');
        return Command::SUCCESS;
    }
}
