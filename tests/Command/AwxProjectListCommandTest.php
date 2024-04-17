<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AwxProjectListCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
/*
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('awx:project:list');
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
*/
        // ...
    }
}