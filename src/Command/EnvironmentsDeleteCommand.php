<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Environments;
use App\Service\EnvironmentManager;
use App\Repository\EnvironmentsRepository;

#[AsCommand(
            name: 'app:environments:delete',
            description: 'Deletes certain environments',
    )]
class EnvironmentsDeleteCommand extends Command {

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var EnvironmentsRepository
     */    
    private $environmentsRepository;
    
    /**
     * 
     * @var EnvironmentManager
     */
    private $environmentService;

    // Dependency injection of the EntityManagerInterface entity
    public function __construct(EntityManagerInterface $entityManager,
            EnvironmentManager $environmentService) {
        parent::__construct();

        $this->environmentService = $environmentService;

        $this->entityManager = $entityManager;

        // get the Environments repository
        $this->environmentsRepository = $this->entityManager->getRepository(Environments::class);
    }

    protected function configure(): void {
        $this
                ->addArgument('id', InputArgument::REQUIRED, 'Specify environment id to delete or <ALL>')
//            ->addOption('force', null, InputOption::VALUE_NONE, 'Forcefully stop the container before deletion')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $env_id = is_int($input->getArgument('id')) ? $input->getArgument('id') : -1;

        if ($env_id) {
            $io->note(sprintf('You passed an argument: %s', $env_id));
        }

        if ($env_id == "ALL") {

            $envs = $this->environmentsRepository->findAll();
            foreach ($envs as $environment) {
                $io->note(sprintf('Deleting "%s" from the database', $environment));
                $this->environmentService->deleteEnvironment($environment);            
            }
        } else {

            // look for a specific env object
            $environment = $this->environmentsRepository->find($env_id);

            if (!$environment) {
                $io->error(sprintf('No environment with Id "%s" found', $env_id));
                return Command::FAILURE;
            }
            $io->note(sprintf('Deleting "%s" from the database', $environment));
            $this->environmentService->deleteEnvironment($environment);
        }

        return Command::SUCCESS;
    }
}
