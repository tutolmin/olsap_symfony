<?php

namespace App\Message;

final class AwxEvent {

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
     * @param array<string, string|int|null> $message
     */
    public function __construct(array $message) {
        $this->event = strval($message['event']);
        $this->id = array_key_exists('id', $message) ? intval($message['id']) : -1;
    }

    public function getEvent(): string {
        return $this->event;
    }

    public function getId(): int {
        return $this->id;
    }
}
