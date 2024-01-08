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
    name: 'lxc:start',
    description: 'Starts a LXC object',
)]
class LxcStartCommand extends Command
{
    private $lxdService;
    private $lxdOperationBus;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( LxcManager $lxd, MessageBusInterface $lxdOperationBus)
    {
        parent::__construct();
        $this->lxdService = $lxd;
        $this->lxdOperationBus = $lxdOperationBus;
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
            $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "start", "name" => $name]));            
        } else {
            $io->note(sprintf('Starting LXC object: %s', $name));
            $this->lxdService->startObject($name);
        }

        return Command::SUCCESS;
    }
}
