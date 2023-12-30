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
            name: 'lxc:delete',
            description: 'Delete an LXC object',
    )]
class LxcDeleteCommand extends Command {

    private $lxd;
    private $lxdOperationBus;
    private $io;
    private $name;
    private $force;
    private $async;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(LxcManager $lxd, MessageBusInterface $lxdOperationBus) {
        parent::__construct();
        $this->lxd = $lxd;
        $this->lxdOperationBus = $lxdOperationBus;
    }

    protected function configure(): void {
        $this
                ->addArgument('name', InputArgument::REQUIRED, 'Object name or <ALL> for all objects')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Forcefully stop the container before deletion')
                ->addOption('async', null, InputOption::VALUE_NONE, 'Asyncroneous execution')
        ;
    }

    private function parseParams($input, $output) {
        $this->io = new SymfonyStyle($input, $output);
        $this->name = $input->getArgument('name');
        $this->force = $input->getOption('force');
        $this->async = $input->getOption('async');

        if ($this->name) {
            $this->io->note(sprintf('You passed an argument: %s', $this->name));
        }

        if ($this->force) {
            $this->io->warning('You passed a force option');
        }
    }

    private function deleteAllObjects() {
        if ($this->async) {
            $this->io->note(sprintf('Dispatching LXC command message'));
            $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "deleteAll"]));
        } else {
            $this->io->warning('Deleting all LXC objects');
            if ($this->lxd->deleteAllObjects($this->force)) {
                $this->io->note('Success!');
            } else {
                $this->io->error('Failure! Check object statuses.');
            }
        }
    }

    private function deleteObject() {
        if ($this->async) {
            $this->io->note(sprintf('Dispatching LXC command message(s)'));
            $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "delete",
                        "name" => $this->name]));
        } else {
            $this->io->note(sprintf('Deleting LXC object: %s',
                            $this->name));
            if ($this->lxd->deleteObject($this->name, $this->force)) {
                $this->io->note('Success!');
            } else {
                $this->io->error('Failure!');
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->parseParams($input, $output);

        if ($this->name == "ALL") {
            $this->deleteAllObjects();
        } else {
            $this->deleteObject();
        }

        return Command::SUCCESS;
    }
}
