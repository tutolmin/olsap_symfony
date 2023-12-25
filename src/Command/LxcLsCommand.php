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
class LxcLsCommand extends Command
{
    private $lxd;
    
    // Doctrine EntityManager
    private $entityManager;

    private $instanceRepository;
    
    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, LxcManager $lxd)
    {   
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
	$this->lxd = $lxd;
    }

    protected function configure(): void
    {

        $this
            ->addOption('orphans', null, InputOption::VALUE_NONE, 'Show orphan objects which does NOT have corresponding instances')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $check_orphans = false;
        
        if($input->getOption('orphans')) {

  	  $check_orphans = true;
	}
        
	// Use Lxc service method
        $containers = $this->lxd->getInstanceList();

        #var_dump( $containers);

        if($containers){

            foreach ($containers as &$value) {

                $info = $this->lxd->getInstanceInfo($value);

    //          var_dump( $info);
                if ($check_orphans){

                    // look for a specific instance type object
                    $instance = $this->instanceRepository->findOneByName($info['name']);

                    if(!$instance){
                       $io->note(sprintf('Name: %s', $info['name']));
                    }

                } else {

                    $io->note(sprintf('Name: %s', $info['name']));
                }


            }

        }

        return Command::SUCCESS;
    }
}
