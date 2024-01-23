<?php

namespace App\MessageHandler;

use App\Message\AwxEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AwxEventHandler
{
    public function __invoke(AwxEvent $message)
    {
        // do something with your message
    }
}
