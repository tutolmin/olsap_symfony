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
use App\Entity\Testees;
use App\Repository\TesteesRepository;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
//use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:testees:export',
    description: 'Exports Testees in CSV format',
)]
class TesteesExportCommand extends Command
{
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    private string $filename = 'testees.csv';

    /**
     *
     * @var TesteesRepository
     */
    private $testeesRepository;
	
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->testeesRepository = $this->entityManager->getRepository(Testees::class);
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

        $testees = $this->testeesRepository->findAll();

        // all callback parameters are optional (you can omit the ones you don't use)
        $dateCallback = function (object $innerObject, object $outerObject, string $attributeName, ?string $format = null, array $context = []): string {
            return $innerObject instanceof \DateTimeImmutable ? $innerObject->format(\DateTimeImmutable::ISO8601) : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'registeredAt' => $dateCallback,
            ],
        ];

        $normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [new CsvEncoder()]);
//        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        foreach ($testees as $testee) {
            $io->note($testee->getOauthToken());
        }
        
        // registeredAt is an object 
        // https://symfony.com/doc/6.4/components/serializer.html#using-callbacks-to-serialize-properties-with-object-instances
        //
        $csvContent = $serializer->serialize($testees, 'csv', 
                [AbstractNormalizer::ATTRIBUTES => ['id','email','oauthToken','registeredAt']]);
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
