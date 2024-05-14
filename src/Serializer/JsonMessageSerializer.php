<?php

namespace App\Serializer;

use App\Message\LxcOperation;
use App\Message\LxcEvent;
use App\Message\AwxAction;
use App\Message\AwxEvent;
use App\Message\EnvironmentAction;
use App\Message\EnvironmentEvent;
use App\Message\SessionAction;
use App\Message\SessionEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
//use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class JsonMessageSerializer implements SerializerInterface {

    /**
     * 
     * @param array <mixed> $encodedEnvelope
     * @return Envelope
     */
    public function decode(array $encodedEnvelope): Envelope {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        $data = "";
        if(is_string($body)){
            $data = json_decode($body, true);
        }
        $message = new \stdClass();
        
        // in case of redelivery, unserialize any stamps
        /**
         * @var array<StampInterface> $stamps
         */
        $stamps = [];
        if (is_array($headers) && isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        if (!is_array($stamps) || !is_array($data)) {

            return new Envelope($message, []);
        }
            
        foreach ($stamps as $stamp) {
            if ($stamp instanceof BusNameStamp) {
                switch ($stamp->getBusName()):
                    case 'lxc.operation.bus':
                        $message = new LxcOperation($data);
                        break;
                    case 'lxc.event.bus':
                        $message = new LxcEvent($data);
                        break;
                    case 'awx.action.bus':
                        $message = new AwxAction($data);
                        break;
                    case 'awx.event.bus':
                        $message = new AwxEvent($data);
                        break;
                    case 'environment.action.bus':
                        $message = new EnvironmentAction($data);
                        break;
                    case 'environment.event.bus':
                        $message = new EnvironmentEvent($data);
                        break;
                    case 'session.action.bus':
                        $message = new SessionAction($data);
                        break;
                    case 'session.event.bus':
                        $message = new SessionEvent($data);
                        break;
                endswitch;
            }
        }
        return new Envelope($message, $stamps);
    }

    /**
     * 
     * @param Envelope $envelope
     * @return array<mixed> 
     * @throws \Exception
     */
    public function encode(Envelope $envelope): array {
        // this is called if a message is redelivered for "retry"
        $message = $envelope->getMessage();

        // expand this logic later if you handle more than
        // just one message class
        if ($message instanceof LxcOperation) {
            // recreate what the data originally looked like
            $data = [
                'command' => $message->getCommand(),
                'name' => $message->getName(),
                'env_id' => $message->getEnvironmentId(),
                'os' => $message->getOS(),
                'hp' => $message->getHP(),
                'status' => $message->getInstanceStatus(),
                'instance_type_id' => $message->getInstanceTypeId(),
                'instance_id' => $message->getInstanceId(),
            ];
        } elseif ($message instanceof LxcEvent) {
            $data = [
                'event' => $message->getEvent(),
                'name' => $message->getName(),
            ];        
        } elseif ($message instanceof AwxAction) {
            $data = [
                'action' => $message->getAction(),
                'environment_id' => $message->getEnvironmentId(),
            ];
        } elseif ($message instanceof AwxEvent) {
            $data = [
                'event' => $message->getEvent(),
                'id' => $message->getId(),
            ];
        } elseif ($message instanceof EnvironmentAction) {
            $data = [
                'action' => $message->getAction(),
                'env_id' => $message->getEnvId(),
                'instance_name' => $message->getInstanceName(),
                'task_id' => $message->getTaskId(),
                'session_id' => $message->getSessionId(),
            ];
        } elseif ($message instanceof EnvironmentEvent) {
            $data = [
                'event' => $message->getEvent(),
                'id' => $message->getId(),
            ];
        } elseif ($message instanceof SessionAction) {
            $data = [
                'action' => $message->getAction(),
                'environment_id' => $message->getEnvironmentId(),
                'task_id' => $message->getTaskId(),
                'session_id' => $message->getSessionId(),
            ];
        } elseif ($message instanceof SessionEvent) {
            $data = [
                'event' => $message->getEvent(),
            ];
        } else {
            throw new \Exception('Unsupported message class');
        }

        $allStamps = [];
        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, $stamps);
        }

        return [
            'body' => json_encode($data),
            'headers' => [
                // store stamps as a header - to be read in decode()
                'stamps' => serialize($allStamps)
            ],
        ];
    }
}