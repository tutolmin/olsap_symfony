<?php

namespace App\MessageHandler;

use App\Message\EnvironmentAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'async', bus: 'environment.action.bus')]
final class EnvironmentActionHandler
{
    public function __invoke(EnvironmentAction $message)
    {
        // do something with your message
    }
}
