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

#[AsCommand(
    name: 'app:environments:delete',
    description: 'Deletes certain environments',
)]
class EnvironmentsDeleteCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // Environments repo
    private $envRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        // get the Environments repository
        $this->envRepository = $this->entityManager->getRepository( Environments::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Specify environment id to delete or <ALL>')
//            ->addOption('force', null, InputOption::VALUE_NONE, 'Forcefully stop the container before deletion')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $env_id = $input->getArgument('id');

        if ($env_id) {
            $io->note(sprintf('You passed an argument: %s', $env_id));
        }

        if ($env_id == "ALL") {

	  $envs = $this->envRepository->findAll();
	  foreach($envs as $env) {

	    $io->note(sprintf('Deleting "%s" from the database', $env));

            // Fetch linked Instances and release them
  	    $env->setInstance(null);
	    $this->entityManager->flush();

	    // Delete item from the DB
	    $this->entityManager->remove($env);
	    $this->entityManager->flush();
	  }
 
	} else { 

	  // look for a specific env object
	  $env = $this->envRepository->find($env_id);

	  if($env) {

	    $io->note(sprintf('Deleting "%s" from the database', $env));

	    // Fetch linked Instances and release them
	    $env->setInstance(null);
	    $this->entityManager->flush();

	    // Delete item from the DB
	    $this->entityManager->remove($env);
	    $this->entityManager->flush();

	  } else {

	    $io->error(sprintf('No environment with Id "%s" found', $env_id));
	  }

	}

        return Command::SUCCESS;
    }
}
