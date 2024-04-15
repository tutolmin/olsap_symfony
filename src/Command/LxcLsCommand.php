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
use App\Repository\InstancesRepository;
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
            name: 'lxc:ls',
            description: 'Lists available LXC instances',
    )]
class LxcLsCommand extends Command {

    private LxcManager $lxcService;
    
    /**
     * 
     * @var SymfonyStyle
     */
    private $io;
    
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
    private InstancesRepository $instanceRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager, LxcManager $lxd) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->lxcService = $lxd;
    }

    protected function configure(): void {

        $this
                ->addOption('orphans', null, InputOption::VALUE_NONE, 'Show orphan objects which does NOT have corresponding instances')
        ;
    }

    /**
     * 
     * @param array<string> $objects
     * @return void
     */
    private function listItems($objects): void {
        foreach ($objects as $object) {
            $info = $this->lxcService->getObjectInfo($object);
            if ($info && is_string($info['name']) && is_string($info['status'])) {
                $this->io->note(sprintf('Name: %s, status: %s', $info['name'], $info['status']));
            }
        }
    }

    /**
     * 
     * @param array<string> $objects
     * @return void
     */
    private function listOrphanItems($objects): void {
        foreach ($objects as $object) {
            $info = $this->lxcService->getObjectInfo($object);
            if ($info && is_array($info)) {
                $this->showOrphanItem($info);
            }
        }
    }
    
    /**
     * 
     * @param array<string> $info
     */
    private function showOrphanItem(array $info): void {
        // look for a specific instance type object
        $obj = $this->instanceRepository->findOneByName($info['name']);
        if (!$obj) {
            $this->io->note(sprintf('Name: %s, status: %s',
                            $info['name'], $info['status']));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->io = new SymfonyStyle($input, $output);

        // Use Lxc service method
        $objects = $this->lxcService->getObjectList();

        if (!$objects) {
            return Command::FAILURE;
        }

        if ($input->getOption('orphans')) {
            $this->listOrphanItems($objects);
        } else {
            $this->listItems($objects);
        }

        return Command::SUCCESS;
    }
}
