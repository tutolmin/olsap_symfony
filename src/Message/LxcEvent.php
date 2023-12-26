<?php

namespace App\Message;

final class LxcEvent
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $event;

    private $name;
    
     public function __construct( $message)
     {
        $this->event = $message['event'];
        $this->name = array_key_exists('name',$message)?strval($message['name']):"";
     }
     
    public function getEvent(): string
    {
	return $this->event;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
