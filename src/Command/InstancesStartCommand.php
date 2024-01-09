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
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
            name: 'app:instances:start',
            description: 'Starts certain instance',
    )]
class InstancesStartCommand extends Command {

    // Doctrine EntityManager
    private $entityManager;
    // Instances repo
    private $instancesRepository;
    private $lxcService;
    
    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager,
            LxcManager $lxcService) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->lxcService = $lxcService;

        // get the Instances repository
        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);
    }

    protected function configure(): void {
        $this
                ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to start')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int 
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

        // look for a specific instance object
        $instance = $this->instancesRepository->findOneByName($name);

        if (!$instance) {
            $io->error(sprintf('Instance "%s" was not found', $name));
            return Command::FAILURE;
        }

        $io->note(sprintf('Sending "start" command for "%s"', $name));

        $this->lxcService->start($instance->getName());
            
        $io->success('Success!');
        return Command::SUCCESS;
    }
}
