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

#[AsCommand(
    name: 'app:instances:ls',
    description: 'List instances stored in the database',
)]
class InstancesLsCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    private $instanceRepository;
    private $instanceStatusRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('status', InputArgument::OPTIONAL, 'Filter certain status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $status = $input->getArgument('status');

	if ($status) {

            $io->note(sprintf('You passed an argument: %s', $status));
            $instance_status = $this->instanceStatusRepository->findOneByStatus($status);

	    // Check if the specified instance status exists
	    if($instance_status) {

		$io->note(sprintf('Status "%s" exists, filter applied', $status));

		// look for a specific instance type object
		$instances = $this->instanceRepository->findByStatus($instance_status->getId());

	    } else {

		$io->warning(sprintf('Status "%s" does NOT exist, filter will NOT be applied', $status));

		// look for a specific instance type object
		$instances = $this->instanceRepository->findAll();
	    }

        } else {

            // look for a specific instance type object
            $instances = $this->instanceRepository->findAll();
	}

	foreach( $instances as $instance) {

            $io->note(sprintf('Name: %s, port: %d, status: %s', 
		$instance->getName(), $instance->getPort(), $instance->getStatus()));
	}

        return Command::SUCCESS;
    }
}
