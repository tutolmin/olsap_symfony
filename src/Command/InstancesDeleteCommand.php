<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
    name: 'app:instances:delete',
    description: 'Delete certain instance',
)]
class InstancesDeleteCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // Instances repo
    private $instancesRepository;
    private $lxcService;

    private $io;
    private $name;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager,
            LxcManager $lxcService)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->lxcService = $lxcService;

        // get the Instances repository
        $this->instancesRepository = $this->entityManager->getRepository( Instances::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to delete or <ALL>')
//            ->addOption('force', null, InputOption::VALUE_NONE, 'Forcefully stop the container before deletion')
        ;
    }

    private function parseParams($input, $output) {
        $this->io = new SymfonyStyle($input, $output);
        $this->name = $input->getArgument('name');
//        $this->force = $input->getOption('force');

        if ($this->name) {
            $this->io->note(sprintf('You passed an argument: %s', $this->name));
        }
/*
        if ($this->force) {
            $this->io->warning('You passed a force option');
        }
 * 
 */
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->parseParams($input, $output);

        if ($this->name == "ALL") {
            $this->lxcService->deleteAllInstances();
        } else {
            // look for a specific instance object
            $instance = $this->instancesRepository->findOneByName($this->name);
            if (!$instance) {
                $this->io->error(sprintf('Instance "%s" was not found', $this->name));
            }
            $this->lxcService->deleteInstance($instance);
        }

        return Command::SUCCESS;
    }
}
