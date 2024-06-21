<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;
use App\Repository\AddressesRepository;

class AddressesDeleteCommandTest extends KernelTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
   
    /**
     * 
     * @var string
     */
    private $subnet = '160';
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

    public function testAddressesDeleteSubnet(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('net:addresses:delete');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // pass arguments to the helper
            'subnet' => $this->subnet,

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

        $addresses = $this->addressesRepository->findOneBy(['ip' => '172.27.'.$this->subnet.'.1']);
  
        $this->assertEmpty($addresses);
        // the output of the command in the console
//        $output = $commandTester->getDisplay();
//        $this->assertStringContainsString('Total number of configured addresses: '.count($addresses), $output);

        // ...
    }
}