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
            name: 'lxc:image:ls',
            description: 'Lists available LXC images',
    )]
class LxcImageLsCommand extends Command {

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
        $images = $this->lxcService->getImageList();

        #var_dump( $images);

        if ($images) {
            foreach ($images as &$value) {
                #        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
                #echo "sdfs";
                $info = $this->lxcService->getImageInfo($value);
//                var_dump( $info);
                $properties = is_array($info['properties']) ? $info['properties'] : null;
                if ($properties && is_string($properties['description'])) {
                    $io->note(sprintf('Name: %s', $properties['description']));
                }
            }
        }

#        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
