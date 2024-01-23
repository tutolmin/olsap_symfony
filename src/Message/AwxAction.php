<?php

namespace App\Message;

final class AwxAction
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
        $this->environment_id = array_key_exists('environment_id',$message)?strval($message['environment_id']):"";
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
