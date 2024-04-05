<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Service\LxcManager;

#[AsCommand(
    name: 'app:instances:create',
    description: 'Creates a number of instances for a specified instance type',
)]
class InstancesCreateCommand extends Command
{
    private $io;
    private $os_alias;
    private $hp_name;
    private $number;
    private LxcManager $lxcService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( LxcManager $lxcService)
    {
        parent::__construct();

        $this->lxcService = $lxcService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('profile', InputArgument::REQUIRED, 'Hardware profile name')
            ->addArgument('os', InputArgument::REQUIRED, 'OS alias')
            ->addArgument('number', InputArgument::OPTIONAL, 'Number of instances to create')
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
        // Check the number of instances requested
        $this->number = 1;
        if ($input->getArgument('number')) {
            $this->io->note(sprintf('You passed number of instances: %s', $this->number));
            $this->number = intval($input->getArgument('number'));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {

        $this->parseParams($input, $output);

        $this->io->note(sprintf('Creating new Instance(s): %s %s',
                        $this->os_alias, $this->hp_name));
        for ($i = 0; $i < $this->number; $i++) {
            if ($this->lxcService->create($this->os_alias, $this->hp_name)) {
                $this->io->success('Instance created successfully!');
            } else {
                $this->io->error('Object creation failure!');
            }
        }

        return Command::SUCCESS;
    }
}
