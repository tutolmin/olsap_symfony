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
     * @param array<string, int|string|null> $message
     */
    public function __construct($message) {
        $this->action = strval($message['action']);
        $this->env_id = array_key_exists('env_id', $message) ? intval($message['env_id']) : -1;
        $this->instance_name = array_key_exists('instance_name', $message) ? strval($message['instance_name']) : "";
        $this->task_id = array_key_exists('task_id', $message) ? intval($message['task_id']) : -1;
        $this->session_id = array_key_exists('session_id', $message) ? intval($message['session_id']) : -1;
    }
    
    public function __toString() {
        return "Environment " . $this->action . " action" .
                (strlen($this->instance_name) > 0 ? " for " . $this->instance_name : "") .
                ($this->env_id !== -1 ? ", Environment ID: " . $this->env_id : "") .
                ($this->task_id !== -1 ? ", Task ID: " . $this->task_id : "") .
                ($this->session_id !== -1 ? ", Session ID: " . $this->session_id : "") .
            "";
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
