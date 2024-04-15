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

    private LxcManager $lxcService;
    
    /**
     * 
     * @var MessageBusInterface
     */
    private $lxcOperationBus;
    
    /**
     * 
     * @var SymfonyStyle
     */
    private $io;
    
    /**
     * 
     * @var string
     */
    private $name;
    
    /**
     * 
     * @var bool
     */   
    private $force;
    
    /**
     * 
     * @var bool
     */
    private $async;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(LxcManager $lxcService, MessageBusInterface $lxcOperationBus) {
        parent::__construct();
        $this->lxcService = $lxcService;
        $this->lxcOperationBus = $lxcOperationBus;
    }

    protected function configure(): void {
        $this
                ->addArgument('name', InputArgument::REQUIRED, 'Object name or <ALL> for all objects')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Forcefully stop the container before deletion')
                ->addOption('async', null, InputOption::VALUE_NONE, 'Asyncroneous execution')
        ;
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function parseParams(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->name = is_string($input->getArgument('name')) ? $input->getArgument('name') : "";
        $this->force = is_bool($input->getOption('force')) ? $input->getOption('force') : true;
        $this->async = is_bool($input->getOption('async')) ? $input->getOption('async') : true;

        if ($this->name) {
            $this->io->note(sprintf('You passed an argument: %s', $this->name));
        }

        if ($this->force) {
            $this->io->warning('You passed a force option');
        }
    }

    /**
     * 
     * @return void
     */
    private function deleteAllObjects(): void {
        if ($this->async) {
            $this->io->note(sprintf('Dispatching LXC command message'));
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "deleteAll"]));
        } else {
            $this->io->warning('Deleting all LXC objects');
            if ($this->lxcService->deleteAllObjects($this->force)) {
                $this->io->success('Success!');
            } else {
                $this->io->error('Failure! Check object statuses.');
            }
        }
    }

    /**
     * 
     * @return void
     */
    private function deleteObject(): void {
        if ($this->async) {
            $this->io->note(sprintf('Dispatching LXC command message(s)'));
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "delete",
                        "name" => $this->name]));
        } else {
            $this->io->note(sprintf('Deleting LXC object: %s',
                            $this->name));
            if ($this->lxcService->deleteObject($this->name, $this->force)) {
                $this->io->success('Success!');
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
