<?php

namespace App\Message;

final class EnvironmentAction
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private $action;
     private $task_id;
     private $env_id;
     private $session_id;
     private $instance_name;

     public function __construct($message)
     {
        $this->action = $message['action'];
        $this->env_id = array_key_exists('env_id',$message)?strval($message['env_id']):-1;
        $this->instance_name = array_key_exists('instance_name',$message)?strval($message['instance_name']):"";        
        $this->task_id = array_key_exists('task_id',$message)?strval($message['task_id']):-1;
        $this->session_id = array_key_exists('session_id',$message)?strval($message['session_id']):-1;
     }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getTaskId(): string
    {
        return $this->task_id;
    }

    public function getEnvId(): string
    {
        return $this->env_id;
    }

    public function getInstanceName(): string
    {
        return $this->instance_name;
    }

    public function getSessionId(): string
    {
        return $this->session_id;
    }
}
