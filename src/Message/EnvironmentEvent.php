<?php

namespace App\Message;

final class EnvironmentEvent {
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $event;
    private $id;

    public function __construct($message) {
        $this->event = $message['event'];
        $this->id = array_key_exists('id', $message) ? intval($message['id']) : "";
    }

    public function getEvent(): string {
        return $this->event;
    }

    public function getId(): string {
        return $this->id;
    }
}
