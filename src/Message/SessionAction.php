<?php

namespace App\Message;

final class SessionAction {

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
    private $session_id;

    /**
     * 
     * @var int
     */
    private $environment_id;

    /**
     * 
     * @param array<string, int|string|null> $message
     */
    public function __construct($message) {
        $this->action = strval($message['action']);
        $this->task_id = array_key_exists('task_id', $message) ? intval($message['task_id']) : -1;
        $this->session_id = array_key_exists('session_id', $message) ? intval($message['session_id']) : -1;
        $this->environment_id = array_key_exists('environment_id', $message) ? intval($message['environment_id']) : -1;
    }
    
    public function __toString() {
        return "Session " . $this->action . " action" .
                ($this->environment_id !== -1 ? ", Environment ID: " . $this->environment_id : "") .
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

    public function getSessionId(): int {
        return $this->session_id;
    }

    public function getEnvironmentId(): int {
        return $this->environment_id;
    }
}
