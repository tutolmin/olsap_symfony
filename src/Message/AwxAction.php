<?php

namespace App\Message;

final class AwxAction {

    /**
     * 
     * @var string
     */
    private $action;

    /**
     * 
     * @var int
     */
    private $environment_id;

    /**
     * 
     * @param array<string, string|int|null> $message
     */
    public function __construct(array $message) {
        $this->action = strval($message['name']);
        $this->environment_id = array_key_exists('environment_id', $message) ? intval($message['environment_id']) : -1;
    }
   
    public function __toString() {
        return "AWX " . $this->action . " action" .
                ($this->environment_id !== -1 ? ", Environment ID: " . $this->environment_id : "") .
            "";
    }

    public function getAction(): string {
        return $this->action;
    }

    public function getEnvironmentId(): int {
        return $this->environment_id;
    }
}
