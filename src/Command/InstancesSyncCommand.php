<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
            name: 'app:instances:sync',
            description: 'Sync instances status with LXC',
    )]
class InstancesSyncCommand extends Command {

    private LxcManager $lxcService;
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
    private InstancesRepository $instanceRepository;

//    private $instanceStatusRepository;
    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager, LxcManager $lxcService) {
        parent::__construct();

        $this->entityManager = $entityManager;
//        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->lxcService = $lxcService;
//        $this->session = $session;
    }

    protected function configure(): void {
//        $this
//            ->addArgument('status', InputArgument::OPTIONAL, 'Filter certain status')
//            ->addOption('orphans', null, InputOption::VALUE_NONE, 'Show orphan records which does NOT have corresponding LXC objects')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        // look for a specific instance type object
        $instances = $this->instanceRepository->findAll();

        foreach ($instances as $instance) {
            $info = $this->lxcService->getObjectInfo($instance->getName());
            if ($info) {
                $this->lxcService->setInstanceStatus($instance->getName(), 
                        is_string($info['status']) ? $info['status'] : "");
            } else {
                $io->error(sprintf('Instance "%s" was not found in LXD, run app:instances:ls --orphans',
                                $instance->getName()));
            }
        }

        return Command::SUCCESS;
    }
}
