<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\LxcManager;

#[AsCommand(
    name: 'lxc:ls',
    description: 'Lists available LXC instances',
)]
class LxcLsCommand extends Command
{
    private $lxd;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( LxcManager $lxd)
    {   
        parent::__construct();
	$this->lxd = $lxd;
    }

    protected function configure(): void
    {
/*
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
*/
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

	// Use Lxc service method
        $containers = $this->lxd->getInstanceList();

        #var_dump( $containers);

	if($containers)
        foreach ($containers as &$value) {

          $info = $this->lxd->getInstanceInfo($value);

//          var_dump( $info);

          $io->note(sprintf('Name: %s', $info['name']));
        }

        return Command::SUCCESS;
    }
}
