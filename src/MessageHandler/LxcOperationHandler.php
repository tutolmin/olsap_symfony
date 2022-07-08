<?php

namespace App\MessageHandler;

use App\Message\LxcOperation;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class LxcOperationHandler implements MessageHandlerInterface
{
    public function __invoke(LxcOperation $message)
    {
        // do something with your message
    }
}
