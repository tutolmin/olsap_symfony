<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AwxMeCommandTest extends KernelTestCase
{
    public function testAwxMeIsSymfony(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('awx:me');
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

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] AWX username: symfony', $output);

        // ...
    }
}