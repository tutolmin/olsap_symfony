<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Service\LxcManager;
use App\Service\SessionManager;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:instances:stop',
    description: 'Stops certain instance',
)]
class InstancesStopCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // Instances repo
    private $instancesRepository;
    private $instanceStatusRepository;

    private $lxd;
    private $bus;
    private $sessionManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, LxcManager $lxd,
	SessionManager $sessionManager, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->lxd = $lxd;
        $this->bus = $bus;
        $this->sessionManager = $sessionManager;

        // get the Instances repository
        $this->instancesRepository = $this->entityManager->getRepository( Instances::class);
        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to stop')
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

            $io->note(sprintf('Instance "%s" has been found in the database', $name));

	    if($instance->getStatus() != "Stopped") {

              $io->note(sprintf('Sending "stop" command to LXD for "%s"', $name));

	      $this->sessionManager->stopInstance($instance);
/*
	      $this->lxd->stopInstance($name);
	
	      // Store item into the DB
	      $instance->setStatus($this->instanceStatusRepository->findOneByStatus("Stopped"));
	      $this->entityManager->persist($instance);
	      $this->entityManager->flush();
*/
	    } else { 

              $io->error(sprintf('Instance "%s" is already in "Stopped" state', $name));

	    }
	
	} else {

            $io->error(sprintf('Instance "%s" was not found', $name));
	}

        return Command::SUCCESS;
    }
}
