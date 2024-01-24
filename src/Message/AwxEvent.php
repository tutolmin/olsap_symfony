<?php

namespace App\Message;

final class AwxEvent
{
     private $event;
     private $id;

     public function __construct( $message)
     {
         $this->event = $message['event'];
         $this->id = array_key_exists('id',$message)?strval($message['id']):"";
      }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getId(): string
    {
        return $this->id;
    }
    
}
