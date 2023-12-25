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
use App\Entity\InstanceTypes;
use App\Entity\TaskInstanceTypes;
use App\Entity\Tasks;

#[AsCommand(
    name: 'app:task-instance-types:populate',
    description: 'Populates TaskInstanseTypes table with supported items',
)]
class TaskInstanceTypesPopulateCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // HW profile repo
    private $tasksRepository;

    // OS repo
    private $itRepository;

    // TaskInstanceTypes repo
    private $ttRepository;

    // Dependency injection of the InstanceTypes entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        // get the repository
        $this->tasksRepository = $this->entityManager->getRepository( Tasks::class);

        // get the repository
        $this->itRepository = $this->entityManager->getRepository( InstanceTypes::class);

        // get the TaskInstanceTypes repository
        $this->ttRepository = $this->entityManager->getRepository( TaskInstanceTypes::class);
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
	  $this->ttRepository->deleteAll();
	}

	// look for *all* HW profiles objects
	$tasks = $this->tasksRepository->findAll();
//	$tasks = $this->tasksRepository->findBySupported(1);

	// look for *all* OSes objects
	$instanceTypes = $this->itRepository->findAll();
//	$instanceTypes = $this->itRepository->findBySupported(1);

	foreach ($tasks as &$task) {
            foreach ($instanceTypes as &$it) {

#	  $io->note(sprintf('HW: %s %s OS: %s %s', $hp->isType()?'VM':'Container', $hp->getDescription(), $os->getBreed(), $os->getRelease()));
                $io->note(sprintf('Task: %s, Instance type: %s', $task->getDescription(), $it));

                // Try to find existing Instance type
                $tt = $this->ttRepository->findBy(['task' => $task->getId(), 'instance_type' => $it->getId()]);

                if (count($tt) > 0) {

                    $io->warning(sprintf('Already exists, skipping addition'));
                } else {

                    $io->note(sprintf('Adding new record to the DB'));

                    // Populate new InstanceType object
                    $taskInstanceType = new TaskInstanceTypes();
                    $taskInstanceType->setTask($task);
                    $taskInstanceType->setInstanceType($it);

                    // tell Doctrine you want to (eventually) save the Product (no queries yet)
                    $this->entityManager->persist($taskInstanceType);

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
