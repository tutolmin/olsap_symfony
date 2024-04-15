<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
#use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\LxcManager;

#[AsCommand(
            name: 'lxc:profile:ls',
            description: 'Lists available LXC profiles',
    )]
class LxcProfileLsCommand extends Command {

    private LxcManager $lxcService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(LxcManager $lxcService) {
        parent::__construct();
        $this->lxcService = $lxcService;
    }

    protected function configure(): void {
        /*
          $this
          ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
          ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
          ;
         */
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        /*
          $arg1 = $input->getArgument('arg1');

          if ($arg1) {
          $io->note(sprintf('You passed an argument: %s', $arg1));
          }

          if ($input->getOption('option1')) {
          // ...
          }
         */
        $profiles = $this->lxcService->getProfileList();

        #var_dump( $profiles);

        if ($profiles) {
            foreach ($profiles as &$value) {
                #        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
                #echo "sdfs";
                $info = $this->lxcService->getProfileInfo($value);
//          var_dump( $info);
                $io->note(sprintf('Name: %s', is_string($info['name']) ? $info['name'] : ""));
            }
        }
        return Command::SUCCESS;
    }
}
