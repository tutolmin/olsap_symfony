<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sessions;
use App\Repository\SessionsRepository;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
//use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:sessions:export',
    description: 'Exports Sessions in CSV format',
)]
class SessionsExportCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    private string $filename = 'sessions.csv';

    /**
     *
     * @var SessionsRepository
     */
    private $sessionsRepository;
	
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
    }

    protected function configure(): void
    {
/*
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
*/
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $sessions = $this->sessionsRepository->findAll();

        $serializer = new Serializer(
               // [new ObjectNormalizer()],
                [new DateTimeNormalizer(array('datetime_format' => \DateTimeImmutable::ISO8601)), new GetSetMethodNormalizer()],
                [new CsvEncoder()]);

        $csvContent = $serializer->serialize($sessions, 'csv',
                [AbstractNormalizer::ATTRIBUTES =>
                    ['hash', 'createdAt', 'startedAt','finishedAt',
                        'status' => ['status'], 'testee' => ['oauthToken']]]);
        $io->note($csvContent);

        $filesystem = new Filesystem();

        try {
            $filepath = $filesystem->tempnam('/var/tmp', $this->filename);
        } catch (IOExceptionInterface $exception) {
            $io->error("An error occurred while creating temp file " .
                    $exception->getPath());
            return Command::FAILURE;
        }
        $io->note(sprintf('Temp file name: %s', $filepath));

        try {
            $filesystem->appendToFile($filepath, $csvContent);
        } catch (IOExceptionInterface $exception) {
            $io->error("An error occurred while writing to a temp file " .
                    $exception->getPath());
            return Command::FAILURE;
        }

        try {
            $filesystem->rename($filepath, '/var/tmp/' . $this->filename, true);
        } catch (IOExceptionInterface $exception) {
            $io->error("An error occurred while renaming temp file " .
                    $exception->getPath());
            return Command::FAILURE;
        }
        $io->note(sprintf('Moved to: %s', '/var/tmp/' . $this->filename));

        return Command::SUCCESS;
    }
}
