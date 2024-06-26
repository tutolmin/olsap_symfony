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
use App\Repository\TasksRepository;
use App\Service\AwxManager;
use App\Entity\Tasks;

#[AsCommand(
    name: 'awx:projects:list',
    description: 'Lists projects configured in AWX',
)]
class AwxProjectsListCommand extends Command
{
    /**
     * 
     * @var AwxManager
     */
    private $awxService;
    
    private EntityManagerInterface $entityManager;
    private TasksRepository $taskRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, AwxManager $awx)
    {   
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);

        $this->awxService = $awx;
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('env_id', InputArgument::REQUIRED, 'Environment id to deploy')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

	$projects = $this->awxService->getProjects();

	foreach($projects as $project) {

            $io->note('Project: ' . $project->name . ', branch: '. $project->scmBranch);

            $task = $this->taskRepository->findOneByPath($project->scmBranch);
	    if($task) {

		$task->setProject($project->id);

		// Store item into the DB
		$this->entityManager->persist($task);
		$this->entityManager->flush();
	    
	    } else {

                $io->warning('Task with SCM branch '.$project->scmBranch.' does NOT exist in the database');
	    }
	}

        return Command::SUCCESS;
    }
}
