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
    name: 'lxc:delete',
    description: 'Delete an LXC instance',
)]
class LxcDeleteCommand extends Command
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
            ->addArgument('name', InputArgument::REQUIRED, 'Instance name or <ALL> for all instances')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forcefully stop the container before deletion')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $force = $input->getOption('force');

	if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

        if ($force) {
            $io->warning('You passed a force option');
        }

        if ($name == "ALL") {

          $io->warning('Deleting all instances');
          if ($this->lxd->deleteAllInstances($force)) {
                $io->note('Success!');
            } else {
                $io->error('Failure! Check object statuses.');
            }
        } else {

            if ($this->lxd->deleteInstance($name, $force)) {
                $io->note('Success!');
            } else {
                $io->error('Failure! Check object statuses.');
            }
        }

        return Command::SUCCESS;
    }
}
