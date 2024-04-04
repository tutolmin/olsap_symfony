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
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

#use Symfony\Component\Filesystem\Path;

#[AsCommand(
            name: 'net:mikrotik:export',
            description: 'Exports file for a Mikrotik router',
    )]
class NetMikrotikExportCommand extends Command {

    // Doctrine EntityManager
    private $entityManager;
    private $addressRepository;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->addressRepository = $this->entityManager->getRepository(Addresses::class);
    }

    protected function configure(): void {
//        $this
//          ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//          ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();

        try {
            $filepath = $filesystem->tempnam('/tmp', 'mikrotik_');
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating temp file " . $exception->getPath();
            return Command::FAILURE;
        }
        $io->note(sprintf('Writing to: %s', $filepath));

        $filesystem->appendToFile($filepath, "/ip firewall nat\nremove [find]\n");

        // Get all addresses
        $addresses = $this->addressRepository->findAll();

        foreach ($addresses as $address) {

            $io->note($address->getIp());

            try {
                $filesystem->appendToFile($filepath, "add action=dst-nat chain=dstnat dst-port=" .
                        $address->getPort() . " protocol=tcp to-addresses=" . $address->getIp() . " to-ports=22\n");
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while writing to a temp file " . $exception->getPath();
            }
        }

        try {
            $filesystem->appendToFile($filepath, "/ip arp\nremove [find]\n");
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while writing to a temp file " . $exception->getPath();
            return Command::FAILURE;
        }

        foreach ($addresses as $address) {

            $io->note($address->getMac());

            try {
                $filesystem->appendToFile($filepath, "add address=" . $address->getIp() . " mac-address=" .
                        $address->getMac() . " interface=bridge1\n");
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while writing to a temp file " . $exception->getPath();
                return Command::FAILURE;
            }
        }

        try {
            $filesystem->rename($filepath, '/tmp/mikrotik.rsc', true);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while renaming temp file " . $exception->getPath();
            return Command::FAILURE;
        }



        return Command::SUCCESS;
    }
}
