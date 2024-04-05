<?php

namespace App\Message;

final class SessionAction
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private $action;
     private $task_id;
     private $session_id;
     private $environment_id;

     public function __construct($message)
     {
        $this->action = $message['action'];
        $this->task_id = array_key_exists('task_id',$message)?strval($message['task_id']):-1;
        $this->session_id = array_key_exists('session_id',$message)?strval($message['session_id']):-1;
        $this->environment_id = array_key_exists('environment_id',$message)?strval($message['environment_id']):-1;
     }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getTaskId(): string
    {
        return $this->task_id;
    }

    public function getSessionId(): int
    {
        return $this->session_id;
    }

    public function getEnvironmentId(): int
    {
        return $this->environment_id;
    }
}
