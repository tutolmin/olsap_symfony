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
use App\Repository\InstancesRepository;
use App\Repository\InstanceStatusesRepository;

use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Service\LxcManager;


#[AsCommand(
    name: 'app:instances:ls',
    description: 'List instances stored in the database',
)]
class InstancesLsCommand extends Command
{
    /**
     * 
     * @var LxcManager
     */
    private $lxdService;
    
    /**
     * 
     * @var SymfonyStyle
     */
    private $io;
    
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    private InstancesRepository $instanceRepository;
    
    /**
     * 
     * @var InstanceStatusesRepository
     */
    private $instanceStatusRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, LxcManager $lxd)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
	$this->lxdService = $lxd;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('status', InputArgument::OPTIONAL, 'Filter certain status')
            ->addOption('orphans', null, InputOption::VALUE_NONE, 'Show orphan records which does NOT have corresponding LXC objects')
        ;
    }

    /**
     * 
     * @param array<int, Instances> $instances
     * @return void
     */
    private function listItems(array $instances): void {
        if ($instances) {
            foreach ($instances as $instance) {
                $addresses = $instance->getAddresses();
                $environments = $instance->getEnvs();
                /*
                  $addresses->forAll(function ($key, $value) {
                  $this->io->note(sprintf('address(es): %s %s', $addresses , $value->getMac()));
                  });
                 */
                $this->io->note(sprintf('Name: %s, port: %s, status: %s, MAC: %s, env: %s',
                                $instance->getName(), $addresses->current() ? $addresses->current()->getPort() : '',
                                $instance->getStatus(), $addresses->current() ? $addresses->current()->getMac() : '',
                                $environments ? $environments->getHash() : ''));
            }
        }
    }

    /**
     * 
     * @param array<int, Instances> $instances
     * @return void
     */
    private function listOrphanItems(array $instances): void {
        if ($instances) {
            foreach ($instances as $instance) {
                $info = $this->lxdService->getObjectInfo($instance->getName());
                if (!$info) {
                    $this->io->note(sprintf('Name: %s, port: %s, status: %s',
                                    $instance->getName(),
                                    $instance->getAddresses()->current() ? $instance->getAddresses()->current()->getPort() : '',
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
