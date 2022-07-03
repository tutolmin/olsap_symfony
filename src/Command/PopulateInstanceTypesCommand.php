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
use App\Entity\HardwareProfiles;
use App\Entity\OperatingSystems;
use App\Entity\InstanceTypes;

#[AsCommand(
    name: 'app:populate-instance-types',
    description: 'Populates InstanseTypes table on new OS or HW profile addition',
)]
class PopulateInstanceTypesCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // HW profile repo
    private $hpRepository;

    // OS repo
    private $osRepository;

    // InstanceTypes repo
    private $itRepository;

    // Dependency injection of the OperatingSystems entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        // get the HW profile repository
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);

        // get the OS repository
        $this->osRepository = $this->entityManager->getRepository( OperatingSystems::class);

        // get the InstanceTypes repository
        $this->itRepository = $this->entityManager->getRepository( InstanceTypes::class);
    }

    protected function configure(): void
    {
/*
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
*/
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

	// Truncate the InstanceType table first
	$this->itRepository->deleteAll();

	// look for *all* HW profiles objects
	$hwProfiles = $this->hpRepository->findAll();

	// look for *all* OSes objects
	$oses = $this->osRepository->findAll();

	foreach( $hwProfiles as &$hp)
	foreach( $oses as &$os) {

	  $io->note(sprintf('HW: %s %s OS: %s %s', $hp->isType()?'VM':'Container', $hp->getDescription(), $os->getBreed(), $os->getRelease()));

	  // Populate new InstanceType object
	  $instanceType = new InstanceTypes();
	  $instanceType->setHwProfile( $hp);
	  $instanceType->setOs( $os);

	  // tell Doctrine you want to (eventually) save the Product (no queries yet)
	  $this->entityManager->persist($instanceType);

	  // actually executes the queries (i.e. the INSERT query)
	  $this->entityManager->flush();
	}
/*
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
*/
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
