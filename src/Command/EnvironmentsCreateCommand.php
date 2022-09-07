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
use App\Entity\Tasks;
use App\Entity\Sessions;
use App\Service\SessionManager;

#[AsCommand(
    name: 'app:environments:create',
    description: 'Creates an environment for a specific task_id',
)]
class EnvironmentsCreateCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    private $taskRepository;
    private $sessionRepository;

    private $sessionManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, 
	SessionManager $sessionManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->sessionRepository = $this->entityManager->getRepository( Sessions::class);
        $this->sessionManager = $sessionManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('task_id', InputArgument::REQUIRED, 'Task identificator')
            ->addArgument('session_id', InputArgument::OPTIONAL, 'Session identificator')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       $io = new SymfonyStyle($input, $output);
       $task_id = intval($input->getArgument('task_id'));
       $session_id = intval($input->getArgument('session_id'));

        if ($task_id) {
            $io->note(sprintf('You passed a task id: %s', $task_id));
        }

        // Check if the task exists
        if( $task = $this->taskRepository->find($task_id)) {

            $io->note('Task with id '.$task_id.' exists in the database');

	    $session = null;

	    // Check if the session exists
	    if( $session_id)
	    if( $session = $this->sessionRepository->find($session_id)) {

              $io->note('Session with id '.$session_id.' exists in the database');

	    } else {

	      $io->warning('Session with id '.$session_id.' does NOT exist in the database');
	    }

	    // Create an environment and undirlying LXC instance
	    $environment=$this->sessionManager->createEnvironment($task, $session);

	    // TODO: handle exception
	
            $io->note('Environment `' . $environment . '` was created.');

	    // Deploy an environment
	    $deploy_result = $this->sessionManager->deployEnvironment($environment);

            $io->note('... and deployed ' . ($deploy_result?'':'un').'successfully');

        } else {

            $io->warning('Task with id '.$task_id.' does NOT exist in the database');
        }

        return Command::SUCCESS;
    }
}
