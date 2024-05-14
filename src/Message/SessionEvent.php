<?php

namespace App\Message;

final class SessionEvent
{
    /**
     * 
     * @var string
     */
    private $event;

    /**
     * 
     * @param array<string, string|int|null> $message
     */
    public function __construct($message) {
        $this->event = strval($message['event']);
    }
    
    public function __toString() {
        return "Session " . $this->event . " action" .
            "";
    }

    public function getEvent(): string {
        return $this->event;
    }
}
