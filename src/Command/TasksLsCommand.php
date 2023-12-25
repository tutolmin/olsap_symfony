<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
#use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tasks;

#[AsCommand(
    name: 'app:tasks:ls',
    description: 'List tasks stored in the database',
)]
class TasksLsCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    private $taskRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
    }

    protected function configure(): void
    {
        $this
//            ->addArgument('status', InputArgument::OPTIONAL, 'Filter certain status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // look for a specific task type object
        $tasks = $this->taskRepository->findAll();

	foreach( $tasks as $task) {

            $io->note(sprintf('Task: %s', $task));
	}

        return Command::SUCCESS;
    }
}
