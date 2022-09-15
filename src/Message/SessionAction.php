<?php

namespace App\Message;

final class SessionAction
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private $action;
     private $session_id;

     public function __construct($message)
     {
        $this->action = $message['action'];
        $this->session_id = $message['session_id']?$message['session_id']:-1;
     }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getSessionId(): int
    {
        return $this->session_id;
    }
}
