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
    name: 'awx:templates:list',
    description: 'Lists templates configured in AWX',
)]
class AwxTemplatesListCommand extends Command
{
    /**
     * 
     * @var AwxManager
     */
    private $awx;
    
    private EntityManagerInterface $entityManager;
    private TasksRepository $taskRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, AwxManager $awx)
    {   
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);

        $this->awx = $awx;
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

	$templates = $this->awx->getTemplates();

	foreach($templates as $template) {

            $io->note('Template: ' . $template->name . ', playbook: '. $template->playbook);

            $task = $this->taskRepository->findOneByProject($template->project);
            if($task) {

		switch($template->playbook) {

		case "deploy.yml":

                    $task->setDeploy($template->id);
		    break;

		case "solve.yml":

                    $task->setSolve($template->id);
		    break;

		case "verify.yml":

                    $task->setVerify($template->id);
		    break;

		default:
		    break;
		}

                // Store item into the DB
                $this->entityManager->persist($task);
                $this->entityManager->flush();

            } else {

                $io->warning('Task with project id '.$template->project.' does NOT exist in the database');
            }

	}

        return Command::SUCCESS;
    }
}
