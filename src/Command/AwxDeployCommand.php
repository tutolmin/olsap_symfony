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
use App\Service\SessionManager;
use App\Entity\Environments;

#[AsCommand(
    name: 'awx:deploy',
    description: 'Deploys an environment',
)]
class AwxDeployCommand extends Command
{
    private $sessionManager;
  
    private $entityManager;
    private $envRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, SessionManager $sessionManager)
    {   
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->envRepository = $this->entityManager->getRepository( Environments::class);

	$this->sessionManager = $sessionManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('env_id', InputArgument::REQUIRED, 'Environment id to deploy')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $env_id = intval($input->getArgument('env_id'));

        if ($env_id) {
            $io->note(sprintf('You passed a env id: %s', $env_id));
        }

        // Check if the Environment exists
        if( $env = $this->envRepository->find($env_id)) {

            $io->note('Deploying: ' . $env);

            // Deploy an environment
            $deploy_result = $this->sessionManager->deployEnvironment($env);

            $io->note('... ' . ($deploy_result?'':'un').'successfully');

/*
	    if($task_id = $env->getTask()->getDeploy()) {

	      // Limit execution on single host only
	      $body["limit"] = $env->getInstance()->getName();
	
	      // return the the account api
	      $result = $this->awx->deploy($env->getTask()->getDeploy(), $body);

	      $io->success('Status: ' . $result->status);

	    } else {

	      $io->warning('Deploy job template with id `' . $task_id . '` was NOT found.');
	    }
*/
        } else {

            $io->warning('Env with id '.$env_id.' does NOT exist in the database');
        }

        return Command::SUCCESS;
    }
}
