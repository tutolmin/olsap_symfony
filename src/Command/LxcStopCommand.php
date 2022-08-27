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
    name: 'lxc:stop',
    description: 'Stops a LXC instance',
)]
class LxcStopCommand extends Command
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
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Instance name')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

/*
        if ($input->getOption('option1')) {
            // ...
        }
*/
        $this->lxd->stopInstance($name);

        return Command::SUCCESS;
    }
}
