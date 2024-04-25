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
use App\Entity\Domains;
use App\Repository\DomainsRepository;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:domains:export',
    description: 'Exports domains in CSV format',
)]
class DomainsExportCommand extends Command
{

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    /**
     *
     * @var DomainsRepository
     */
    private $domainsRepository;
	
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->domainsRepository = $this->entityManager->getRepository(Domains::class);
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

        $filesystem = new Filesystem();

        try {
            $filepath = $filesystem->tempnam('/tmp', 'domains_');
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating temp file " . $exception->getPath();
            return Command::FAILURE;
        }
        $io->note(sprintf('Writing to: %s', $filepath));

        // Get all domains
        $domains = $this->domainsRepository->findAll();

$encoders = [new CsvEncoder()];
$normalizers = [new ObjectNormalizer()];

$serializer = new Serializer($normalizers, $encoders);

//        foreach ($domains as $domain) {

$csvContent = $serializer->serialize($domains, 'csv', [AbstractNormalizer::ATTRIBUTES => ['id','name','description']]);
            $io->note($csvContent);

            try {
                $filesystem->appendToFile($filepath, $csvContent . "\n");
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while writing to a temp file " . $exception->getPath();
                return Command::FAILURE;
            }
//        }

        try {
            $filesystem->rename($filepath, '/tmp/domains.csv', true);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while renaming temp file " . $exception->getPath();
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
