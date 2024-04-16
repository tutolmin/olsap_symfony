<?php

namespace App\Tests;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Tools\DsnParser;
use PHPUnit\Framework\TestCase;
use App\Message\DummyMessage;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;

class MessengerTest extends TestCase
{
/*
    private \Doctrine\DBAL\Connection $driverConnection;
    private Connection $connection;

    protected function setUp(): void
    {
        $dsn = getenv('MESSENGER_DOCTRINE_DSN') ?: 'pdo-sqlite://:memory:';
        $params = class_exists(DsnParser::class) ? (new DsnParser())->parse($dsn) : ['url' => $dsn];
        $config = new Configuration();
        if (class_exists(DefaultSchemaManagerFactory::class)) {
            $config->setSchemaManagerFactory(new DefaultSchemaManagerFactory());
        }

        $this->driverConnection = DriverManager::getConnection($params, $config);
        $this->connection = new Connection([], $this->driverConnection);
    }

    protected function tearDown(): void
    {
        $this->driverConnection->close();
    }
        
    public function testConnectionSendAndGet()
    {
        $this->connection->send('{"message": "Hi"}', ['type' => DummyMessage::class]);
        $encoded = $this->connection->get();
        $this->assertEquals('{"message": "Hi"}', $encoded['body']);
        $this->assertEquals(['type' => DummyMessage::class], $encoded['headers']);
    }
*/
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }
}
