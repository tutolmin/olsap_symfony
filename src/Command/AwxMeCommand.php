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
use App\Service\AwxManager;

#[AsCommand(
    name: 'awx:me',
    description: 'Show user information in AWX',
)]
class AwxMeCommand extends Command
{
    private $awx;
    
//    private $entityManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( 
//            EntityManagerInterface $entityManager, 
            AwxManager $awx)
    {   
        parent::__construct();

//        $this->entityManager = $entityManager;

        $this->awx = $awx;
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

	// return the the account api
	$me = $this->awx->me();

	// Get the info for the account
	$userInformation = $me->getAll();

//	var_dump($userInformation);
        $io->success('AWX username: '.$userInformation[0]->username);
/*
// return the job template api
$jobTemplate = $this->awx->jobTemplate();

$runResult = $jobTemplate->launch(9,"");
#$runResult = $jobTemplate->getAll();

var_dump($runResult->id);

$job = $this->awx->Job();

while(true) {
$jobResult = $job->getById($runResult->id);
var_dump($jobResult->status);

if($jobResult->status == "successfull") break;
sleep( 1);
}
*/
/*
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

*/
        return Command::SUCCESS;
    }
}
