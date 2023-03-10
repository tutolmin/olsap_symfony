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
use App\Entity\Environments;
use App\Service\SessionManager;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SessionAction;

#[AsCommand(
    name: 'app:environments:spare',
    description: 'Spares an environment for a specific task_id',
)]
class EnvironmentsSpareCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    private $taskRepository;
    private $environmentRepository;

    private $sessionBus;

    private $sessionManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, 
	SessionManager $sessionManager, MessageBusInterface $sessionBus)
    {
        parent::__construct();

        $this->sessionBus = $sessionBus;
        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
        $this->sessionManager = $sessionManager;
    }

    protected function configure(): void
    {
        $this
//            ->addArgument('task_id', InputArgument::REQUIRED, 'Task identificator')
//            ->addArgument('session_id', InputArgument::OPTIONAL, 'Session identificator')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
      $io = new SymfonyStyle($input, $output);

      // find all tasks
      $tasks = $this->taskRepository->findAll();

      foreach( $tasks as $task) {

        // TODO: make sure task exists, and there are not enough spare envs for a task
        $environments = $this->environmentRepository->findAllDeployed($task->getId());

        $io->note(sprintf("Specified task: " . $task . ", spare envs #: " . count($environments)));

	// Only add new envs if there are not enough
	if(count($environments) < $_ENV['APP_SPARE_ENVS'])

	for( $i=0; $i<$_ENV['APP_SPARE_ENVS']-count($environments);$i++) {

          $io->note(sprintf("Sending message to create a new spare environment"));

          $this->sessionBus->dispatch(new SessionAction(["action" => "createSpareEnvironment",
                "task_id" => $task->getId()]));

	}

      }

      return Command::SUCCESS;
    }
}
