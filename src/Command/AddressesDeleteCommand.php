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
use App\Repository\AddressesRepository;
use App\Service\AddressesManager;

#[AsCommand(
    name: 'net:addresses:delete',
    description: 'Delete address subnet from the database',
)]
class AddressesDeleteCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
 
     /**
      * 
      * @var AddressesRepository
      */
    private $addressRepository;
//    private $portRepository;

    /**
     * 
     * @var AddressesManager
     */
    private $addressManager;
      
    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager,
            AddressesManager $addressManager)
    {
        parent::__construct();

        $this->addressManager = $addressManager;
        
        $this->entityManager = $entityManager;
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
//        $this->portRepository = $this->entityManager->getRepository( Ports::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('subnet', InputArgument::REQUIRED, '172.27.<subnet>.[0-255] number')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $subnet = is_numeric($input->getArgument('subnet')) ? intval($input->getArgument('subnet')) : -1;

        if ($subnet<0 || $subnet>254) {
            $io->error(sprintf('You passed invalid argument: %d', $subnet));
            return Command::FAILURE;
        }

        $io->success('Deleting addresses from 172.27.'.$subnet.'.[0-255]');

        // Itarate through all the possible addresses
        for($p=0;$p<=255;$p++) {

          // Check is the address already exists
          $address = $this->addressRepository->findOneByIp('172.27.'.$subnet.'.'.$p);
          if( $address) {

            $io->note('Deleting IP address 172.27.'.$subnet.'.'.$p.' from the database');
/*
            // Unbind address from the port
	    $address->setPort(null);

	    // Delete item from the DB
	    $this->entityManager->remove($address);
            $this->entityManager->flush();
*/
            $this->addressManager->removeAddress($address);
            
	  } else {

            $io->warning('Address 172.27.'.$subnet.'.'.$p.' does NOT exist in the database');
	  }
	}
	
        return Command::SUCCESS;
    }
}
