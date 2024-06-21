<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;
use App\Repository\AddressesRepository;

class AddressesCountCommandTest extends KernelTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
   
    /**
     * 
     * @var AddressesRepository
     */
    private $addressesRepository;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->addressesRepository = $this->entityManager->getRepository(Addresses::class);
    }

    public function testAddressesCountMatchesDatabase(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('net:addresses:count');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // pass arguments to the helper
//            'username' => 'Wouter',

            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
            // use brackets for testing array value,
            // e.g: '--some-option' => ['option_value'],
        ],
        [
            '-n',
            '--ansi'
        ]);

        $commandTester->assertCommandIsSuccessful();

        $addresses = $this->addressesRepository->findAll();
        
        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Total number of configured addresses: '.count($addresses), $output);

        // ...
    }
}