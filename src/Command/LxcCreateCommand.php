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
            name: 'lxc:create',
            description: 'Creates a number of LXC objects for a specified OS alias and Hardware Profile',
    )]
class LxcCreateCommand extends Command {

    private $lxd;
    private $lxdOperationBus;
    private $io;
    private $os_alias;
    private $hp_name;
    private $object_number;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(LxcManager $lxd, MessageBusInterface $lxdOperationBus) {
        parent::__construct();

        $this->lxd = $lxd;
        $this->lxdOperationBus = $lxdOperationBus;
    }

    protected function configure(): void {
        $this
                ->addArgument('profile', InputArgument::REQUIRED, 'Hardware profile name')
                ->addArgument('os', InputArgument::REQUIRED, 'Operating system alias')
                ->addArgument('number', InputArgument::OPTIONAL, 'Number of objects to create')
                ->addOption('async', null, InputOption::VALUE_NONE, 'Asyncroneous execution')
        ;
    }

    private function parseParams($input, $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->os_alias = $input->getArgument('os');
        $this->hp_name = $input->getArgument('profile');

        if ($this->os_alias && $this->hp_name) {
            $this->io->note(sprintf('You passed os alias: %s and profile name: %s', 
                    $this->os_alias, $this->hp_name));
        }
        // Check the number of objects requested
        $this->object_number = 1;
        if ($input->getArgument('number')) {
            $this->io->note(sprintf('You passed number of objects: %s', $this->object_number));
            $this->object_number = intval($input->getArgument('number'));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {

        $this->parseParams($input, $output);

        if ($input->getOption('async')) {
            $this->io->note(sprintf('Dispatching LXC command message(s)'));
            for ($i = 0; $i < $this->object_number; $i++) {
                $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "create",
                            "os" => $this->os_alias, "hp" => $this->hp_name]));
            }
        } else {
            $this->io->note(sprintf('Creating new LXC object(s): %s %s',
                            $this->os_alias, $this->hp_name));
            for ($i = 0; $i < $this->object_number; $i++) {
                if ($this->lxd->createObject($this->os_alias, $this->hp_name)) {
                    $this->io->note('Success!');
                }
            }
        }

        return Command::SUCCESS;
    }
}
