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
use App\Entity\Addresses;
use App\Entity\Ports;

#[AsCommand(
    name: 'net:addresses:add',
    description: 'Add subnet into the database',
)]
class AddressesAddCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    private $addressRepository;
    private $portRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        $this->portRepository = $this->entityManager->getRepository( Ports::class);
    }

    protected function configure(): void {
        $this->
                addArgument('subnet', InputArgument::REQUIRED, '172.27.<subnet>.[0-255] number')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $subnet = intval($input->getArgument('subnet'));

        if ($subnet<0 || $subnet>254) {
            $io->error(sprintf('You passed invalid argument: %d', $subnet));
            return Command::FAILURE;
        }

        $io->success('Adding addresses from 172.27.'.$subnet.'.[0-255]');

	// Itarate through all the possible addresses
	for($p=0;$p<=255;$p++) {
	  // Check is the address already exists
	  if( $this->addressRepository->findOneByIp('172.27.'.$subnet.'.'.$p)) {

            $io->note('IP address 172.27.'.$subnet.'.'.$p.' already exists in the database');

	  } else {

	    $address = new Addresses;
	    $address->setIp('172.27.'.$subnet.'.'.$p);
	    $address->setMac('aa:bb:cc:dd:'.str_pad(dechex($subnet), 2, '0', STR_PAD_LEFT).':'.str_pad(dechex($p), 2, '0', STR_PAD_LEFT));

	    $port = $this->portRepository->findOneByAddress(null);

	    // Unused port not found
	    if(!$port) {
            
	      $io->error('No more unused ports available');
	      break;

	    } else {

              $io->warning('Inserting 172.27.'.$subnet.'.'.$p.' with port number '.$port->getNumber().' into the database');

	      // Bind address to a port
	      $port->setAddress($address);

	      // Store item into the DB
	      $this->entityManager->persist($address);
	      $this->entityManager->flush();
	    }
	  }
	}
	
        return Command::SUCCESS;
    }
}
