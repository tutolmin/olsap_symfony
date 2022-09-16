<?php

namespace App\Message;

final class LxcOperation
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $command;

    private $environment_id;

    private $instance_type_id;

    private $instance_id;

    public function __construct( $message)
    {
        $this->command = $message['command'];
        $this->environment_id = array_key_exists('environment_id',$message)?$message['environment_id']:"";
        $this->instance_type_id = array_key_exists('instance_type_id',$message)?$message['instance_type_id']:"";
        $this->instance_id = array_key_exists('instance_id',$message)?$message['instance_id']:"";
    }

    public function getCommand(): string
    {
	return $this->command;
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
