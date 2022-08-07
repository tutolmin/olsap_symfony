<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'awx:me',
    description: 'Add a short description for your command',
)]
class AwxMeCommand extends Command
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


$awxVars = array (
    'clientId' => $_ENV["AWX_CLIENT_ID"], // The client ID assigned by AWX when you created the application
    'clientSecret' => $_ENV["AWX_CLIENT_SECRET"],
    'username' => $_ENV["AWX_USERNAME"], // The AWX username associated with the application
    'password' => $_ENV["AWX_PASSWORD"],
    'apiUrl' => $_ENV["AWX_API_URL"], // Ie. https://x.x.x.x/api
    'sslVerify' => false, //SSL verify can be false during development and true after public SSL certificates are obtained
    );

var_dump($awxVars);

// Create oauth2 object
$oauth2 = new \AwxV2\Oauth\Oauth2($awxVars);

// Get access and refresh tokens and expire time
$tokens = $oauth2->passCredGrant();

// Get access token
$accessToken = $tokens->getToken();

// create an adapter and add access token
$adapter = new \AwxV2\Adapter\GuzzleHttpAdapter($accessToken, $awxVars['sslVerify']);

// create an Awx object with the previous adapter
$awx = new \AwxV2\AwxV2($adapter, $awxVars['apiUrl']);

// return the the account api
$me = $awx->me();

// Get the info for the account
$userInformation = $me->getAll();

var_dump($userInformation);

// return the job template api
$jobTemplate = $awx->jobTemplate();

$runResult = $jobTemplate->launch(9,"");
#$runResult = $jobTemplate->getAll();

var_dump($runResult);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
