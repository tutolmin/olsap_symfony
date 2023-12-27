<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LxcManager;
use App\Message\LxcOperation;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
            name: 'lxc:create',
            description: 'Creates a number of LXC instances for a specified instance type',
    )]
class LxcCreateCommand extends Command {

    // Doctrine EntityManager
    private $entityManager;
    private $lxd;
    private $lxdOperationBus;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager, LxcManager $lxd, MessageBusInterface $lxdOperationBus) {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->lxd = $lxd;
        $this->lxdOperationBus = $lxdOperationBus;
    }

    protected function configure(): void {
        $this
                ->addArgument('profile', InputArgument::REQUIRED, 'Hardware profile name')
                ->addArgument('os', InputArgument::REQUIRED, 'OS alias')
                ->addArgument('number', InputArgument::OPTIONAL, 'Number of instances to create')
                ->addOption('async', null, InputOption::VALUE_NONE, 'Asyncroneous execution')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $os_alias = $input->getArgument('os');
        $hp_name = $input->getArgument('profile');

        if ($os_alias && $hp_name) {
            $io->note(sprintf('You passed os alias: %s and profile name: %s', $os_alias, $hp_name));
        }
        // Check the number of instances requested
        $number = 1;
        if ($input->getArgument('number')) {
            $number = intval($input->getArgument('number'));
        }

        if ($input->getOption('async')) {
            $io->note(sprintf('Dispatching LXC command message(s)'));
            for ($i = 0; $i < $number; $i++) {
                $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "create", "os" => $os_alias, "hp" => $hp_name]));
            }
        } else {
            $io->note(sprintf('Creating new LXC object(s): %s %s', $os_alias, $hp_name));
            for ($i = 0; $i < $number; $i++) {
                $this->lxd->createInstance($os_alias, $hp_name);
            }
        }

        return Command::SUCCESS;
    }
}
