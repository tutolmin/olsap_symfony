<?php

namespace App\Message;

final class LxcOperation
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $command;

    private $name;
    
    private $environment_id;

    private $instance_type_id;

    private $instance_id;

    public function __construct( $message)
    {
        $this->command = $message['command'];
        $this->name = array_key_exists('name',$message)?strval($message['name']):"";
        $this->environment_id = array_key_exists('environment_id',$message)?strval($message['environment_id']):"";
        $this->instance_type_id = array_key_exists('instance_type_id',$message)?strval($message['instance_type_id']):"";
        $this->instance_id = array_key_exists('instance_id',$message)?strval($message['instance_id']):"";
    }

    public function getCommand(): string
    {
	return $this->command;
    }

    public function getName(): string
    {
	return $this->name;
    }

    public function getEnvironmentId(): string
    {
	return $this->environment_id;
    }

    public function getInstanceTypeId(): string
    {
	return $this->instance_type_id;
    }

    public function getInstanceId(): string
    {
	return $this->instance_id;
    }
}
