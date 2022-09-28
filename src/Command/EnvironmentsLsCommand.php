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
use App\Entity\Environments;
use App\Entity\EnvironmentStatuses;

#[AsCommand(
    name: 'app:environments:ls',
    description: 'List environments stored in the database',
)]
class EnvironmentsLsCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    private $environmentRepository;
    private $environmentStatusRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->environmentStatusRepository = $this->entityManager->getRepository( EnvironmentStatuses::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
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
            $environment_status = $this->environmentStatusRepository->findOneByStatus($status);

	    // Check if the specified environment status exists
	    if($environment_status) {

		$io->note(sprintf('Status "%s" exists, filter applied', $status));

		// look for a specific environment type object
		$environments = $this->environmentRepository->findByStatus($environment_status->getId());

	    } else {

		$io->warning(sprintf('Status "%s" does NOT exist, filter will NOT be applied', $status));

		// look for a specific environment type object
		$environments = $this->environmentRepository->findAll();
	    }

        } else {

            // look for a specific environment type object
            $environments = $this->environmentRepository->findAll();
	}

	foreach( $environments as $environment) {

            $io->note(sprintf('Environment: %s', $environment));
	}

        return Command::SUCCESS;
    }
}
