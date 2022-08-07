<?php

namespace App\Message;

final class RunPlaybook
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $name;
    private $environment_id;

    public function __construct($message)
    {
        $this->name = $message['name'];
        $this->environment_id = $message['environment_id']?$message['environment_id']:"";
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEnvironmentId(): string
    {
        return $this->environment_id;
    }
}
