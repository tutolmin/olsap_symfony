<?php

namespace App\Message;

final class AwxAction
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $action;
    private $environment_id;

    
    public function __construct($message)
    {
        $this->action = $message['name'];
        $this->environment_id = array_key_exists('environment_id',$message)?strval($message['environment_id']):-1;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getEnvironmentId(): int
    {
        return $this->environment_id;
    }
}
