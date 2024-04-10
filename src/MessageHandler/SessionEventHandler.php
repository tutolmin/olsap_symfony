<?php

namespace App\MessageHandler;

use App\Message\SessionEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'async', bus: 'session.event.bus')]
final class SessionEventHandler
{
    public function __invoke(SessionEvent $message): void
    {
        // do something with your message
    }
}
