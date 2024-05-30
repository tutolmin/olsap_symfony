<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LuckyNumberControllerTest extends WebTestCase
{
    public function testLuckyNumber(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/lucky/number');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Your lucky number is');
    }
}
