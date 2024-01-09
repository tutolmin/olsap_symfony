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
use App\Message\LxcOperation;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'lxc:stop',
    description: 'Stops a LXC object',
)]
class LxcStopCommand extends Command
{
    private $lxcService;
    private $lxcOperationBus;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( LxcManager $lxcService, MessageBusInterface $lxcOperationBus)
    {
        parent::__construct();
        $this->lxcService = $lxcService;
        $this->lxcOperationBus = $lxcOperationBus;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'LXC object name')
            ->addOption('async', null, InputOption::VALUE_NONE, 'Asyncroneous execution')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

        if ($input->getOption('async')) {
            $io->note(sprintf('Dispatching LXC command message'));
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "stop", "name" => $name]));            
        } else {
            $io->note(sprintf('Stopping LXC object: %s', $name));
            if( !$this->lxcService->stopObject($name)){
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
