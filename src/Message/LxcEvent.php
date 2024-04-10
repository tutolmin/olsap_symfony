<?php

namespace App\Message;

final class LxcEvent {

    /**
     * 
     * @var string
     */
    private $event;

    /**
     * 
     * @var string
     */
    private $name;

    /**
     * 
     * @param array<string> $message
     */
    public function __construct($message) {
        $this->event = $message['event'];
        $this->name = array_key_exists('name', $message) ? strval($message['name']) : "";
    }

    public function getEvent(): string {
        return $this->event;
    }

    public function getName(): string {
        return $this->name;
    }
}
