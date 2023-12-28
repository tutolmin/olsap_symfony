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
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Service\LxcManager;


#[AsCommand(
    name: 'app:instances:ls',
    description: 'List instances stored in the database',
)]
class InstancesLsCommand extends Command
{
    private $lxd;
    private $io;
    
    // Doctrine EntityManager
    private $entityManager;

    private $instanceRepository;
    private $instanceStatusRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, LxcManager $lxd)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
	$this->lxd = $lxd;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('status', InputArgument::OPTIONAL, 'Filter certain status')
            ->addOption('orphans', null, InputOption::VALUE_NONE, 'Show orphan records which does NOT have corresponding LXC objects')
        ;
    }

    private function listItems(array $instances): void {
        if ($instances) {
            foreach ($instances as $instance) {
                $this->io->note(sprintf('Name: %s, port: %s, status: %s',
                    $instance->getName(), $instance->getAddresses()[0]->getPort(), 
                        $instance->getStatus()));
            }
        }
    }

    private function listOrphanItems(array $instances): void {
        if ($instances) {
            foreach ($instances as $instance) {
                $info = $this->lxd->getInstanceInfo($instance->getName());
                if (!$info) {
                    $this->io->note(sprintf('Name: %s, port: %s, status: %s',
                        $instance->getName(), $instance->getAddresses()[0]->getPort(), 
                            $instance->getStatus()));
                }
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $status = $input->getArgument('status');
        
	if ($status) {

            $this->io->note(sprintf('You passed an argument: %s', $status));
            $instance_status = $this->instanceStatusRepository->findOneByStatus($status);

            // Check if the specified instance status exists
            if (!$instance_status) {
                $this->io->warning(sprintf('Status "%s" does NOT exist. Check your input!', $status));
                return Command::FAILURE;
            }
            $this->io->note(sprintf('Status filter "%s" applied', $status));

            // look for a specific instance type object
            $instances = $this->instanceRepository->findByStatus($instance_status->getId());

        } else {

            // look for a specific instance type object
            $instances = $this->instanceRepository->findAll();
        }

        if ($input->getOption('orphans')) {
            $this->listOrphanItems($instances);
        } else {
            $this->listItems($instances);
        }

        return Command::SUCCESS;
    }
}
