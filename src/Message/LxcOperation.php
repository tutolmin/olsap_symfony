<?php

namespace App\Message;

final class LxcOperation {

    /**
     * 
     * @var string
     */
    private $command;

    /**
     * 
     * @var string
     */
    private $name;

    /**
     * 
     * @var string
     */
    private $operating_system;

    /**
     * 
     * @var string
     */
    private $hardware_profile;

    /**
     * 
     * @var int
     */
    private $environment_id;

    /**
     * 
     * @var string
     */
    private $status;

    /**
     * 
     * @var int
     */
    private $instance_type_id;

    /**
     * 
     * @var int
     */
    private $instance_id;

    /**
     * 
     * @param array<string, int|string|null> $message
     */
    public function __construct($message) {
        $this->command = strval($message['command']);
        $this->name = array_key_exists('name', $message) ? strval($message['name']) : "";
        $this->operating_system = array_key_exists('os', $message) ? strval($message['os']) : "";
        $this->hardware_profile = array_key_exists('hp', $message) ? strval($message['hp']) : "";
        $this->environment_id = array_key_exists('env_id', $message) ? intval($message['env_id']) : -1;
        $this->status = array_key_exists('status', $message) ? strval($message['status']) : "New";
        $this->instance_type_id = array_key_exists('instance_type_id', $message) ? intval($message['instance_type_id']) : -1;
        $this->instance_id = array_key_exists('instance_id', $message) ? intval($message['instance_id']) : -1;
    }

    public function __toString() {
        return "LXC " . $this->command . " operation" .
                (strlen($this->name) > 0 ? " for " . $this->name : "") .
                (strlen($this->operating_system) > 0 ? ", OS: " . $this->operating_system : "") .
                (strlen($this->hardware_profile) > 0 ? ", HW profile: " . $this->hardware_profile : "") .
                ($this->environment_id !== -1 ? ", Environment ID: " . $this->environment_id : "") .
                (strlen($this->status) > 0 ? ", Status: " . $this->status : "") .
                ($this->instance_type_id !== -1 ? ", Instance Type ID: " . $this->instance_type_id : "") .
                ($this->instance_id !== -1 ? ", Instance ID: " . $this->instance_id : "") .
            "";
    }

    public function getCommand(): string {
        return $this->command;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getOS(): string {
        return $this->operating_system;
    }

    public function getHP(): string {
        return $this->hardware_profile;
    }

    public function getEnvironmentId(): int {
        return $this->environment_id;
    }

    public function getInstanceStatus(): string {
        return $this->status;
    }

    public function getInstanceTypeId(): int {
        return $this->instance_type_id;
    }

    public function getInstanceId(): int {
        return $this->instance_id;
    }
}
