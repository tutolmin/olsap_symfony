<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
use App\Repository\InstanceTypesRepository;

use App\Entity\InstanceTypes;
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
    name: 'app:instances:spare',
    description: 'Creates spare instances for all instance types',
)]
class InstancesSpareCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var InstanceTypesRepository
     */
    private $instanceTypeRepository;
    private InstancesRepository $instanceRepository;

    /**
     * 
     * @var SymfonyStyle
     */
    private $io;
    
    /**
     * 
     * @var int
     */
    private $spare_instances;
    
    private LxcManager $lxcService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( int $spare_instances, 
            EntityManagerInterface $entityManager, LxcManager $lxcService)
    {
        parent::__construct();

        $this->spare_instances = $spare_instances;
        $this->entityManager = $entityManager;

        $this->lxcService = $lxcService;

        // get the InstanceTypes repository
        $this->instanceTypeRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
    }

    protected function configure(): void
    {
//        $this
                /*
            ->addArgument('profile', InputArgument::REQUIRED, 'Hardware profile name')
            ->addArgument('os', InputArgument::REQUIRED, 'OS alias')
            ->addArgument('number', InputArgument::OPTIONAL, 'Number of instances to create')
            ->addOption('spare', null, InputOption::VALUE_NONE, 'Create spare instances')
                 * 
                 */
//        ;
    }

    /**
     * 
     * @param InstanceTypes $instanceType
     * @return void
     */
    private function createSpareInstances($instanceType): void {

        $spare_instances = $this->instanceRepository->findAllSpare($instanceType->getId());

        $instances_num = $spare_instances ? count($spare_instances) : 0;
        
        // Only add new envs if there are not enough
        if ($instances_num < $this->spare_instances) {

            for ($i = 0; $i < $this->spare_instances - $instances_num; $i++) {

                $alias = $instanceType->getOs()->getAlias() ? $instanceType->getOs()->getAlias() : '';

                $profile = $instanceType->getHwProfile()->getName() ? $instanceType->getHwProfile()->getName() : '';

                $this->io->note(sprintf('Creating new Instance(s): %s %s', $alias, $profile));

                $this->lxcService->create($alias, $profile);

                $this->io->success('Instance created successfully!');
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->io = new SymfonyStyle($input, $output);

        // find all Instance Types
        $instanceTypes = $this->instanceTypeRepository->findAll();

        if (count($instanceTypes) == 0) {
            $this->io->error('No instances to create spares for!');
            return Command::FAILURE;
        }

        foreach ($instanceTypes as $instanceType) {
            $this->createSpareInstances($instanceType);
        }

        return Command::SUCCESS;
    }
}
