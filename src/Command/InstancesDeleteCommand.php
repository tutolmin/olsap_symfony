<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Service\LxcManager;

#[AsCommand(
    name: 'app:instances:delete',
    description: 'Delete certain container',
)]
class InstancesDeleteCommand extends Command
{
    // Doctrine EntityManager
    private $entityManager;

    // Instances repo
    private $instancesRepository;

    private $lxd;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, LxcManager $lxd)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->lxd = $lxd;

        // get the Instances repository
        $this->instancesRepository = $this->entityManager->getRepository( Instances::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Specify instance name to delete or <ALL>')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forcefully stop the container before deletion')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $force = $input->getOption('force');

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

        if ($force) {
            $io->warning('You passed a force option');
        }

        if ($name == "ALL") {

	  $instances = $this->instancesRepository->findAll();
	  foreach($instances as $instance) {

	    $io->note(sprintf('Deleting "%s" from the database', $instance->getName()));

	    if ($this->lxd->deleteInstance($instance->getName(), $force)) {

                    // Fetch all linked Addresses and release them
                    $addresses = $instance->getAddresses();
                    foreach ($addresses as $address) {
                        $address->setInstance(null);
                        $this->entityManager->flush();
                    }

                    // Delete item from the DB
                    $this->entityManager->remove($instance);
                    $this->entityManager->flush();

                    $io->note('Success!');
                } else {
                    $io->error('Failure!');
                }
            }
 
	} else { 

	  // look for a specific instance object
	  $instance = $this->instancesRepository->findOneByName($name);

	  // Check if instance is present in the DB
	  if($instance) {

	      $io->note(sprintf('Deleting "%s" from the database', $name));

              if ($this->lxd->deleteInstance($name, $force)) {

                    // Fetch all linked Addresses and release them
                    $addresses = $instance->getAddresses();
                    foreach ($addresses as $address) {

                        $address->setInstance(null);
                        $this->entityManager->flush();
                    }

                    // Delete item from the DB
                    $this->entityManager->remove($instance);
                    $this->entityManager->flush();

                    $io->note('Success!');
                } else {
                    $io->error('Failure!');
                }
            } else {

	      $io->error(sprintf('Instance "%s" was not found', $name));
	  }
	}

        return Command::SUCCESS;
    }
}
