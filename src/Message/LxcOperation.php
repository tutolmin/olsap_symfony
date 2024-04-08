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
    private $operating_system;
    private $hardware_profile;
    private $environment_id;
    private $status;
    
    private $instance_type_id;
    private $instance_id;

    public function __construct( $message)
    {
        $this->command = $message['command'];
        $this->name = array_key_exists('name',$message)?strval($message['name']):"";
        $this->operating_system = array_key_exists('os',$message)?strval($message['os']):"";
        $this->hardware_profile = array_key_exists('hp',$message)?strval($message['hp']):"";
        $this->environment_id = array_key_exists('env_id',$message)?intval($message['env_id']):-1;
        $this->status = array_key_exists('status', $message) ? intval($message['status']) : "";
        
        $this->instance_type_id = array_key_exists('instance_type_id',$message)?intval($message['instance_type_id']):-1;
        $this->instance_id = array_key_exists('instance_id',$message)?intval($message['instance_id']):-1;
    }

    public function getCommand(): string
    {
	return $this->command;
    }

    public function getName(): string
    {
	return $this->name;
    }

    public function getOS(): string
    {
	return $this->operating_system;
    }

    public function getHP(): string
    {
	return $this->hardware_profile;
    }

    public function getEnvironmentId(): int
    {
	return $this->environment_id;
    }
    
    public function getInstanceStatus(): string {
        return $this->status;
    }
    
    public function getInstanceTypeId(): int
    {
	return $this->instance_type_id;
    }

    public function getInstanceId(): int
    {
	return $this->instance_id;
    }
}
