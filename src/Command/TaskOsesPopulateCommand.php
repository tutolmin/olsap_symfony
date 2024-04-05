<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
#use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TasksRepository;
use App\Entity\OperatingSystems;
use App\Entity\Tasks;
use App\Entity\TaskOses;

#[AsCommand(
    name: 'app:task-oses:populate',
    description: 'Populates TaskOses table with values',
)]
class TaskOsesPopulateCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    // Task profile repo
    private TasksRepository $taskRepository;

    // OS repo
    private $osRepository;

    // TaskOses repo
    private $toRepository;

    // Dependency injection of the OperatingSystems entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        // get the HW profile repository
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);

        // get the OS repository
        $this->osRepository = $this->entityManager->getRepository( OperatingSystems::class);

        // get the InstanceTypes repository
        $this->toRepository = $this->entityManager->getRepository( TaskOses::class);
    }

    protected function configure(): void
    {
        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Purge the table first')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

	if($input->getOption('purge')) {

  	  // Truncate the InstanceType table first
	  $this->toRepository->deleteAll();
	}

	// look for *all* tasks objects
	$tasks = $this->taskRepository->findAll();
//	$tasks = $this->taskRepository->findBySupported(1);

	// look for *all* OSes objects
//	$oses = $this->osRepository->findAll();
	$oses = $this->osRepository->findBySupported(1);

	foreach ($tasks as &$task) {
            foreach ($oses as &$os) {

#	  $io->note(sprintf('HW: %s %s OS: %s %s', $hp->isType()?'VM':'Container', $hp->getDescription(), $os->getBreed(), $os->getRelease()));
                $io->note(sprintf('Task: %s OS: %s %s', $task->getDescription(), $os->getBreed(), $os->getRelease()));

                // Try to find existing record
                $to = $this->toRepository->findBy(['os' => $os->getId(), 'task' => $task->getId()]);

                if (count($to) > 0) {

                    $io->warning(sprintf('Already exists, skipping addition'));
                } else {

                    $io->note(sprintf('Adding new record to the DB'));

                    // Populate new TaskOses object
                    $taskOS = new TaskOses();
                    $taskOS->setTask($task);
                    $taskOS->setOs($os);

                    // tell Doctrine you want to (eventually) save the Product (no queries yet)
                    $this->entityManager->persist($taskOS);

                    // actually executes the queries (i.e. the INSERT query)
                    $this->entityManager->flush();
                }
            }
        }
        /*
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
*/

        return Command::SUCCESS;
    }
}
