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
use App\Repository\TasksRepository;
use App\Entity\Tasks;
use App\Entity\Environments;
use App\Service\EnvironmentManager;

#[AsCommand(
            name: 'app:environments:create',
            description: 'Creates an environment for a specific Task',
    )]
class EnvironmentsCreateCommand extends Command {

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
    private TasksRepository $taskRepository;
    private $environmentRepository;

    private $envs_number;
    private $environmentService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager,
            EnvironmentManager $environmentService) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
        $this->environmentService = $environmentService;
    }

    protected function configure(): void {
        $this
                ->addArgument('task', InputArgument::REQUIRED, 'Task identificator by path')
//                ->addArgument('session_id', InputArgument::OPTIONAL, 'Session identificator')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
                ->addArgument('number', InputArgument::OPTIONAL, 'Number of environments to create')
                ->addOption('async', null, InputOption::VALUE_NONE, 'Asyncroneous execution')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $task_path = $input->getArgument('task');
//       $session_id = intval($input->getArgument('session_id'));

        if ($task_path) {
            $io->note(sprintf('You passed a Task: %s', $task_path));
        }
        // Check the number of objects requested
        $this->envs_number = 1;
        if ($input->getArgument('number')) {
            $io->note(sprintf('You passed number of objects: %s', $this->envs_number));
            $this->envs_number = intval($input->getArgument('number'));
        }
        // Check if the task exists
        $task = $this->taskRepository->findOneByPath($task_path);
        
        if (!$task) {
            $io->note('Task `' . $task_path . '` was NOT found!');
            return Command::FAILURE;
        }
        
        $environments = $this->environmentRepository->findAllDeployed($task->getId());

	$io->note("Specified task: " . $task . ", spare envs #: " . count($environments));
	
        for ($i = 0; $i < $this->envs_number; $i++) {
            // Create an environment and underlying LXC instance
            $this->environmentService->createEnvironment($task->getId(),
                    null, $input->getOption('async'));
            $io->success('Environment creation initiated.');
        }
        // TODO: handle exception

        /*
          // Deploy an environment
          $deploy_result = $this->sessionManager->deployEnvironment($environment);

          $io->warning('... and deployed ' . ($deploy_result?'':'un').'successfully');
         */

        return Command::SUCCESS;
    }
}
