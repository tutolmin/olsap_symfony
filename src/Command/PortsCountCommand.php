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
use App\Entity\Ports;
use App\Repository\PortsRepository;

#[AsCommand(
    name: 'net:ports:count',
    description: 'Shows total number of configured ports',
)]
class PortsCountCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var PortsRepository
     */
    private $portRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->portRepository = $this->entityManager->getRepository( Ports::class);
    }

    protected function configure(): void
    {
        $this
#            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('used', null, InputOption::VALUE_NONE, 'Used ports only')
            ->addOption('unused', null, InputOption::VALUE_NONE, 'Unused ports only')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
/*
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
*/
        if ($input->getOption('used')) {

	  $io->note(sprintf('Number of used ports: %d', count( $this->portRepository->findAll()) - 
		count( $this->portRepository->findBy(["address" => null]))));

        } else if ($input->getOption('unused')) {

	  $io->note(sprintf('Number of unused ports: %d', count( $this->portRepository->findBy(["address" => null]))));

        } else {

	  $io->note(sprintf('Total number of configured ports: %d', count( $this->portRepository->findAll())));

	}

        return Command::SUCCESS;
    }
}
