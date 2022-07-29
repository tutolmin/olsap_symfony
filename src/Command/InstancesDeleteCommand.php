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

use App\Message\LxcOperation;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:instances:delete',
    description: 'Delete certain container',
)]
class InstancesDeleteCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // Instances repo
    private $instancesRepository;

    // Message bus
    private $bus;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager,
        MessageBusInterface $bus)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->bus = $bus;

        // get the Instances repository
        $this->instancesRepository = $this->entityManager->getRepository( Instances::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to delete')
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

	    if($instance->getStatus() == "Stopped") {

              $io->note(sprintf('Sending "delete" command to LXD for "%s"', $name));

              $this->bus->dispatch(new LxcOperation(["command" => "delete",
                "environment_id" => null, "instance_type_id" => null, 
		"instance_id" => $instance->getId()]));

	    } else { 

              $io->error(sprintf('Instance "%s" is NOT in "Stopped" state', $name));

	    }
	
	} else {

            $io->error(sprintf('Instance "%s" was not found', $name));
	}

        return Command::SUCCESS;
    }
}
