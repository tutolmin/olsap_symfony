<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
#use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;

#[AsCommand(
    name: 'net:addresses:count',
    description: 'Shows total number of configured addresses',
)]
class AddressesCountCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    private $addressRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
    }

    protected function configure(): void
    {
#        $this
#            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
#            ->addOption('used', null, InputOption::VALUE_NONE, 'Used addresses only')
#            ->addOption('unused', null, InputOption::VALUE_NONE, 'Unused addresses only')
#        ;
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
/*
        if ($input->getOption('used')) {

	  $io->note(sprintf('Number of used addresses: %d', count( $this->addressRepository->findAll()) - 
		count( $this->addressRepository->findBy(["address" => null]))));

        } else if ($input->getOption('unused')) {

	  $io->note(sprintf('Number of unused addresses: %d', count( $this->addressRepository->findBy(["address" => null]))));

        } else {

	  $io->note(sprintf('Total number of configured addresses: %d', count( $this->addressRepository->findAll())));

	}
*/
	$io->note(sprintf('Total number of configured addresses: %d', count( $this->addressRepository->findAll())));

        return Command::SUCCESS;
    }
}
