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
            name: 'app:instances:stop',
            description: 'Stops certain instance',
    )]
class InstancesStopCommand extends Command {

    // Doctrine EntityManager
    private $entityManager;
    // Instances repo
    private $instancesRepository;
    private $lxdService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager,
            LxcManager $lxcManager) {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->lxdService = $lxcManager;

        // get the Instances repository
        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);
    }

    protected function configure(): void {
        $this
                ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to stop')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

        // look for a specific instance object
        $instance = $this->instancesRepository->findOneByName($name);

        if ($instance) {

            $io->note(sprintf('Instance "%s" has been found in the database with ID: %d',
                            $name, $instance->getId()));
            
            if ($instance->getStatus() != "Stopped" && $instance->getStatus() != "Sleeping") {

                $io->note(sprintf('Sending "stop" command for "%s"', $name));

                $this->lxdService->stopInstance($instance);
            } else {

                $io->error(sprintf('Instance "%s" is NOT started', $name));
            }
        } else {

            $io->error(sprintf('Instance "%s" was not found', $name));
        }

        return Command::SUCCESS;
    }
}
