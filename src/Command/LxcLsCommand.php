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
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
            name: 'lxc:ls',
            description: 'Lists available LXC instances',
    )]
class LxcLsCommand extends Command {

    private $lxdService;
    private $io;
    
    // Doctrine EntityManager
    private $entityManager;
    private $instanceRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager, LxcManager $lxd) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->lxdService = $lxd;
    }

    protected function configure(): void {

        $this
                ->addOption('orphans', null, InputOption::VALUE_NONE, 'Show orphan objects which does NOT have corresponding instances')
        ;
    }

    private function listItems(array $objects): void {
        if ($objects) {
            foreach ($objects as $object) {
                $info = $this->lxdService->getObjectInfo($object);
                $this->io->note(sprintf('Name: %s, status: %s', $info['name'], $info['status']));
            }
        }
    }

    private function listOrphanItems(array $objects): void {
        if ($objects) {
            foreach ($objects as $object) {
                $info = $this->lxdService->getObjectInfo($object);
                
                // look for a specific instance type object
                $obj = $this->instanceRepository->findOneByName($info['name']);
                
                if (!$obj) {
                    $this->io->note(sprintf('Name: %s, status: %s',
                        $info['name'], $info['status']));
                }
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->io = new SymfonyStyle($input, $output);

        // Use Lxc service method
        $objects = $this->lxdService->getObjectList();

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
