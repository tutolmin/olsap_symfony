<?php

namespace App\Message;

final class EnvironmentEvent {

    /**
     * 
     * @var string
     */
    private $event;

    /**
     * 
     * @var int
     */
    private $id;

    /**
     * 
     * @param array<string> $message
     */
    public function __construct($message) {
        $this->event = $message['event'];
        $this->id = array_key_exists('id', $message) ? intval($message['id']) : -1;
    }

    public function getEvent(): string {
        return $this->event;
    }

    public function getId(): int {
        return $this->id;
    }
}
