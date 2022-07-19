<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;

#[AsCommand(
    name: 'lxc:image:ls',
    description: 'Add a short description for your command',
)]
class LxcImageLsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

#            $io->note(sprintf('Cert path: %s', __DIR__.'/client.pem'));

	$config = [
	    'verify' => false,
	    'cert' => [
		__DIR__.'/client.pem',
		''
	    ]
	];

	$guzzle = new GuzzleClient($config);
	$adapter = new GuzzleAdapter($guzzle);

	$lxd = new \Opensaucesystems\Lxd\Client($adapter);

	$lxd->setUrl('https://172.27.72.4:8443');

	#$certificates = $lxd->certificates->all();
	#$fingerprint = $lxd->certificates->add(file_get_contents(__DIR__.'/client.pem'), 'ins3Cure');

	#$info = $lxd->host->info();
	#var_dump( $info);
	/*
	if ($lxd->host->trusted()) {
	    echo 'trusted';
	} else {
	    echo 'not trusted';
	}
	*/
	#$$images = array();
	#$containers = $lxd->containers->all();
	$images = $lxd->images->all();

	#var_dump( $images);

	foreach ($images as &$value) {
	#        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
	#echo "sdfs";
  	  $info = $lxd->images->info($value);
	//var_dump( $info);
		    $io->note(sprintf('Name: %s', $info['properties']['description']));
	}

#        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
