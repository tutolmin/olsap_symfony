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
     * @param array<string, mixed> $message
     */
    public function __construct(array $message) {
        $this->action = $message['name'];
        $this->environment_id = array_key_exists('environment_id', $message) ? intval($message['environment_id']) : -1;
    }

    public function getAction(): string {
        return $this->action;
    }

    public function getEnvironmentId(): int {
        return $this->environment_id;
    }
}
