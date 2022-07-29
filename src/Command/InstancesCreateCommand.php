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
use App\Entity\InstanceTypes;
use App\Entity\OperatingSystems;
use App\Entity\HardwareProfiles;

use App\Message\LxcOperation;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:instances:create',
    description: 'Creates a number of instances for a specified instance type',
)]
class InstancesCreateCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // InstanceTypes repo
    private $itRepository;

    // OperatingSystems repo
    private $osRepository;

    // HardwareProfiles repo
    private $hpRepository;

    // Message bus
    private $bus;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager,
	MessageBusInterface $bus)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->bus = $bus;

        // get the InstanceTypes repository
        $this->itRepository = $this->entityManager->getRepository( InstanceTypes::class);

        // get the OperatingSystems repository
        $this->osRepository = $this->entityManager->getRepository( OperatingSystems::class);

        // get the HardwareProfiles repository
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('profile', InputArgument::REQUIRED, 'Hardware profile name')
            ->addArgument('os', InputArgument::REQUIRED, 'OS alias')
            ->addArgument('instance_number', InputArgument::OPTIONAL, 'Number of instances to create')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

	$os_alias = $input->getArgument('os');
	$hw_name = $input->getArgument('profile');

        if ($os_alias && $hw_name)
            $io->note(sprintf('You passed os alias: %s and profile name: %s', $os_alias, $hw_name));

        // look for a specific OperatingSystems object
#        $os = $this->osRepository->findOneBy(array('alias' => $os_alias));
        $os = $this->osRepository->findOneByAlias($os_alias);

        // look for a specific HardwareProfiles object
#        $hp = $this->hpRepository->findOneBy(array('name' => $hw_name));
        $hp = $this->hpRepository->findOneByName($hw_name);

	// Both OS and HW profile objects found
	if( $os && $hp) {

	  // look for a specific instance type object
	  $instance_type = $this->itRepository->findOneBy(array('os' => $os->getId(), 'hw_profile' => $hp->getId()));

	  // Instance type found
	  if( $instance_type) {
	  
  //          $io->success('Found!');

	    // Check the number of instances requested
	    $number = 1;
	    if ($input->getArgument('instance_number')) {

	      $number = intval( $input->getArgument('instance_number'));
	    }
	    $io->note(sprintf('We are going to create %d instances', $number));

	    for($i=0; $i<$number; $i++)
	      $this->bus->dispatch(new LxcOperation(["command" => "create", 
		"environment_id" => null, "instance_id" => null, 
		"instance_type_id" => $instance_type->getId()]));

	  } else

	    $io->error('Instance type id was not found in the database for valid OS and HW profile. Run `app:populate-instance-types` command.');

	} else 

          $io->warning('OS alias or HW profile name is invalid. Check your input!');

        return Command::SUCCESS;
    }
}
