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
use App\Entity\InstanceStatuses;
use App\Service\LxcManager;

#[AsCommand(
    name: 'app:instances:restart',
    description: 'Restarts an instance',
)]
class InstancesRestartCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // Instances repo
    private $instancesRepository;
    private $instanceStatusRepository;

    private $lxd;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, LxcManager $lxd)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->lxd = $lxd;

        // get the Instances repository
        $this->instancesRepository = $this->entityManager->getRepository( Instances::class);
        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);

    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to restart')
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

	if($instance) {

            $io->note(sprintf('Instance "%s" has been found in the database with ID: %d', 
		$name, $instance->getId()));

	    $io->note(sprintf('Sending "restart" command to LXD for "%s"', $name));

	    $this->lxd->restartInstance($name);

	// TODO: verify return code and check the status	

	} else {

            $io->error(sprintf('Instance "%s" was not found', $name));
	}

        return Command::SUCCESS;
    }
}
