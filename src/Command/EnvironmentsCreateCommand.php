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
//use App\Entity\Sessions;
use App\Service\SessionManager;

#[AsCommand(
            name: 'app:environments:create',
            description: 'Creates an environment for a specific Task',
    )]
class EnvironmentsCreateCommand extends Command {

    // Doctrine EntityManager
    private $entityManager;
    private $taskRepository;
//    private $sessionRepository;

    private $sessionManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager,
            SessionManager $sessionManager) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
//        $this->sessionRepository = $this->entityManager->getRepository( Sessions::class);
        $this->sessionManager = $sessionManager;
    }

    protected function configure(): void {
        $this
            ->addArgument('task', InputArgument::REQUIRED, 'Task identificator')
//                ->addArgument('session_id', InputArgument::OPTIONAL, 'Session identificator')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
                ->addOption('spare', null, InputOption::VALUE_NONE, 'Create spare environments')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $task_path = $input->getArgument('task');
//       $session_id = intval($input->getArgument('session_id'));

        if ($task_path) {
            $io->note(sprintf('You passed a Task: %s', $task_path));
        }

        // Check if the task exists
        $task = $this->taskRepository->findOneByPath($task_path);
        if (!$task) {
            $io->note('Task `' . $task_path . '` was NOT found!');
            return Command::FAILURE;
        }

        // Create an environment and undirlying LXC instance
        $environment = $this->sessionManager->createEnvironment($task);

        // TODO: handle exception

        $io->note('Environment `' . $environment . '` was created.');
        /*
          // Deploy an environment
          $deploy_result = $this->sessionManager->deployEnvironment($environment);

          $io->warning('... and deployed ' . ($deploy_result?'':'un').'successfully');
         */

        return Command::SUCCESS;
    }
}
