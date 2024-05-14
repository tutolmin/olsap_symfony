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
     * @param array<string, string|int|null> $message
     */
    public function __construct($message) {
        $this->event = strval($message['event']);
        $this->name = array_key_exists('name', $message) ? strval($message['name']) : "";
    }
    
    public function __toString() {
        return "LXC " . $this->event . " event" .
                (strlen($this->name) > 0 ? " for " . $this->name : "") .
                "";
    }

    public function getEvent(): string {
        return $this->event;
    }

    public function getName(): string {
        return $this->name;
    }
}
