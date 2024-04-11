<?php

namespace App\Message;

final class EnvironmentAction {

    /**
     * 
     * @var string
     */
    private $action;

    /**
     * 
     * @var int
     */
    private $task_id;

    /**
     * 
     * @var int 
     */
    private $env_id;

    /**
     * 
     * @var int
     */
    private $session_id;

    /**
     * 
     * @var string
     */
    private $instance_name;

    /**
     * 
     * @param array<string, mixed> $message
     */
    public function __construct($message) {
        $this->action = $message['action'];
        $this->env_id = array_key_exists('env_id', $message) ? intval($message['env_id']) : -1;
        $this->instance_name = array_key_exists('instance_name', $message) ? strval($message['instance_name']) : "";
        $this->task_id = array_key_exists('task_id', $message) ? intval($message['task_id']) : -1;
        $this->session_id = array_key_exists('session_id', $message) ? intval($message['session_id']) : -1;
    }

    public function getAction(): string {
        return $this->action;
    }

    public function getTaskId(): int {
        return $this->task_id;
    }

    public function getEnvId(): int {
        return $this->env_id;
    }

    public function getInstanceName(): string {
        return $this->instance_name;
    }

    public function getSessionId(): int {
        return $this->session_id;
    }
}
