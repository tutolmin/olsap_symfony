<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SessionsRepository;
use App\Entity\Sessions;
use App\Service\SessionManager;

#[AsCommand(
    name: 'app:session:next-task',
    description: 'Selects the next task for a given session',
)]
class SessionNextTaskCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var SessionsRepository
     */
    private $sessionRepository;

    /**
     * 
     * @var SessionManager
     */
    private $sessionManager;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct( EntityManagerInterface $entityManager, SessionManager $sessionManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->sessionRepository = $this->entityManager->getRepository( Sessions::class);
	$this->sessionManager = $sessionManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('session_id', InputArgument::REQUIRED, 'Session identificator')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $session_id = is_numeric($input->getArgument('session_id')) ? $input->getArgument('session_id'): -1;

        if ($session_id) {
            $io->note(sprintf('You passed an argument: %s', $session_id));
        }

        // Check is the session exists
        $session = $this->sessionRepository->find($session_id);
        if( $session) {

            $io->note('Session with id '.$session_id.' exists in the database');

            $io->note('Next suitable task is "'.$this->sessionManager->getNextTask($session).'"');

	} else {

            $io->warning('Session with id '.$session_id.' does NOT exist in the database');
	}

/*
        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
*/
        return Command::SUCCESS;
    }
}
