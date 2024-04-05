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
use App\Entity\Ports;

#[AsCommand(
    name: 'net:ports:delete',
    description: 'Delete port range into the database',
)]
class PortsDeleteCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

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
            ->addArgument('min', InputArgument::REQUIRED, 'Port range min value')
            ->addArgument('max', InputArgument::REQUIRED, 'Port range max value')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $min = intval($input->getArgument('min'));
        $max = intval($input->getArgument('max'));

        if ($min<0 || $min>$max) {
            $io->error(sprintf('You passed invalid arguments: %d and %d', $min, $max));
            return Command::FAILURE;
        }

        $io->success('Delete ports from '.$min.' to '.$max);

	// Itarate through all the ports
	for($p=$min;$p<=$max;$p++) {

	  // Check is the port already exists
          $port = $this->portRepository->findOneByNumber($p);
	  if( $port) {

            $io->note('Deleting '.$p.' from the database');

	    // Delete item into the DB
	    $this->entityManager->remove($port);
            $this->entityManager->flush();

	  } else {

            $io->warning('Port '.$p.' does NOT exist in the database');
	  }
	}
	
        return Command::SUCCESS;
    }
}
