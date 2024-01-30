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
use App\Service\AwxManager;
use App\Service\EnvironmentManager;
use App\Entity\Tasks;
use App\Entity\Instances;

#[AsCommand(
    name: 'awx:job:details',
    description: 'Show playbook execution details fetched from AWX',
)]
class AwxJobDetailsCommand extends Command
{
    private $awxService;
    private $envService;

    private $entityManager;
    private $taskRepository;
    private $instanceRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, 
            AwxManager $awxService, EnvironmentManager $envService)
    {   
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        
        $this->awxService = $awxService;
        $this->envService = $envService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('job_id', InputArgument::REQUIRED, 'Job ID to show')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $job_id = intval($input->getArgument('job_id'));

        if ($job_id) {
            $io->note(sprintf('You passed a Job id: %s', $job_id));
        }
        
        $job = $this->awxService->getJobById($job_id);

        // Handle exception 
        // In GuzzleHttpAdapter.php line 148:                        
        // Request not processed.  

        if (!$job) {
            $io->error('No such job found');
            return Command::FAILURE;
        }

        $io->note(sprintf('Name: %s, status: %s, limit: %s', 
                $job->name, $job->status, $job->limit));

        $instance = $this->instanceRepository->findOneByName($job->limit);

        $environment = $instance->getEnvs();

        if ($environment) {
            $io->note(sprintf('Environment: %s', $environment));
            $this->envService->setEnvironmentStatus($environment, 'Deployed');
        }
        /*
	$projects = $this->awx->getProjects();

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
*/
        return Command::SUCCESS;
    }
}
