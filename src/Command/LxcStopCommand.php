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
    description: 'Stops a LXC object',
)]
class LxcStopCommand extends Command
{
    private $lxcService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( LxcManager $lxcService)
    {
        parent::__construct();
        $this->lxcService = $lxcService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'LXC object name')
            ->addOption('async', null, InputOption::VALUE_NONE, 'Asyncroneous execution')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

        $io->note(sprintf('Stopping LXC object: %s', $name));
        
        if (!$this->lxcService->stop($name, false, $input->getOption('async'))) {
            $io->error('Failure! Check object name and status.');
            return Command::FAILURE;
        }

        $io->success('Success!');
        return Command::SUCCESS;
    }
}
