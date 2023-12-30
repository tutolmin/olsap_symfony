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
use App\Entity\InstanceTypes;
use App\Entity\OperatingSystems;
use App\Entity\HardwareProfiles;
use App\Service\SessionManager;

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

    private $io;
    private $os_alias;
    private $hp_name;
    private $number;
    
    // OperatingSystems repo
    private $osRepository;

    // HardwareProfiles repo
    private $hpRepository;

    private $sessionManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, SessionManager $sessionManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->sessionManager = $sessionManager;

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
            ->addArgument('number', InputArgument::OPTIONAL, 'Number of instances to create')
        ;
    }
    
    private function parseParams($input, $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->os_alias = $input->getArgument('os');
        $this->hp_name = $input->getArgument('profile');

        if ($this->os_alias && $this->hp_name) {
            $this->io->note(sprintf('You passed os alias: %s and profile name: %s', 
                    $this->os_alias, $this->hp_name));
        }
        // Check the number of instances requested
        $this->number = 1;
        if ($input->getArgument('number')) {
            $this->io->note(sprintf('You passed number of instances: %s', $this->number));
            $this->number = intval($input->getArgument('number'));
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int {

        $this->parseParams($input, $output);

        // look for a specific OperatingSystems object
        $os = $this->osRepository->findOneByAlias($this->os_alias);

        // look for a specific HardwareProfiles object
        $hp = $this->hpRepository->findOneByName($this->hp_name);

        // Both OS and HW profile objects found
        if (!$os || !$hp) {
            $this->io->warning('OS alias or HW profile name is invalid. Check your input!');
            return Command::FAILURE;
        }
        
        // look for a specific instance type object
        $instance_type = $this->itRepository->findOneBy(array('os' => $os->getId(), 'hw_profile' => $hp->getId()));
        if (!$instance_type) {
            $this->io->error('Instance type id was not found in the database for valid OS and HW profile. Run `app:instance-types:populate` command.');
            return Command::FAILURE;
        } else {
            $this->io->note(sprintf('We are going to create %d instances', $this->number));

            for ($i = 0; $i < $this->number; $i++) {
                $this->io->note(sprintf('Creating new Instances: %s %s',
                                $this->os_alias, $this->hp_name));
                $this->sessionManager->createInstance($instance_type);
            }
        }

        return Command::SUCCESS;
    }
}
