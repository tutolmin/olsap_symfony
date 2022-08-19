<?php

namespace App\MessageHandler;

use App\Message\SessionAction;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SessionActionHandler implements MessageHandlerInterface
{
    public function __invoke(SessionAction $message)
    {
        // do something with your message
    }
}
