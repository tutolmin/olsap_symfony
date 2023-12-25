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
use App\Entity\Tasks;
use App\Service\SessionManager;

#[AsCommand(
    name: 'app:task:instance-type',
    description: 'Selects first instance type for a given task',
)]
class TaskInstanceTypeCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    private $taskRepository;

    private $sessionManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, SessionManager $sessionManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->sessionManager = $sessionManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('task_id', InputArgument::REQUIRED, 'Task identificator')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $task_id = intval($input->getArgument('task_id'));

        if ($task_id) {
            $io->note(sprintf('You passed an argument: %s', $task_id));
        }

        // Check is the task exists
        $task = $this->taskRepository->find($task_id);
        if( $task) {

            $io->note('Task with id '.$task_id.' exists in the database');

	    if ($this->sessionManager->getFirstInstanceType($task)) {
                $io->note('First suitable instance type is "' . $this->sessionManager->getFirstInstanceType($task) . '"');
            } else {
                $io->warning('No suitable instance types are available for task id ' . $task_id);
            }
        } else {

            $io->warning('Task with id '.$task_id.' does NOT exist in the database');
        }

        return Command::SUCCESS;
    }
}
