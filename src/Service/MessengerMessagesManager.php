<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
//use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MessengerMessages;
//use App\Repository\MessengerMessagesRepository;
use App\Message\LxcOperation;
use App\Message\LxcEvent;
use App\Message\AwxAction;
use App\Message\AwxEvent;
use App\Message\EnvironmentAction;
use App\Message\EnvironmentEvent;
use App\Message\SessionAction;
use App\Message\SessionEvent;
use Symfony\Component\Messenger\Stamp\BusNameStamp;

class MessengerMessagesManager {

    private LoggerInterface $logger;
//    private EntityManagerInterface $entityManager;
//    private MessengerMessagesRepository $messengerMessagesRepository;
    
    public function __construct(
            LoggerInterface $logger, 
//            EntityManagerInterface $em,
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

//        $this->entityManager = $em;
//        $this->messengerMessagesRepository = $this->entityManager->getRepository(MessengerMessages::class);
    }

    /**
     * 
     * @param MessengerMessages $message
     * @return array<mixed>
     */
    private function getStamps($message) {

        $stamps = [];
        $headers = json_decode($message->getHeaders() ?? "", true);

        if (is_array($headers) && isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        if (!is_array($stamps)) {
            $this->logger->debug('Envelope has no stamps.');
            return [];
        }
        
        return $stamps;
    }

    /**
     * 
     * @param MessengerMessages $message
     * @return null|mixed
     */
    public function parseBody($message) {

        $stamps = $this->getStamps($message);

        $body = json_decode($message->getBody() ?? "", true);
        if (!is_array($body)) {

            $this->logger->debug('Message body is empty.');
            return null;
        }

        $object = null;
        foreach ($stamps as $stamp) {

            // Check stamp type
            if (! $stamp instanceof BusNameStamp) {
                continue;
            }

            // Parse body
            switch ($stamp->getBusName()):
                case 'lxc.operation.bus':
                    $object = new LxcOperation($body);
                    break;
                case 'lxc.event.bus':
                    $object = new LxcEvent($body);
                    break;
                case 'awx.action.bus':
                    $object = new AwxAction($body);
                    break;
                case 'awx.event.bus':
                    $object = new AwxEvent($body);
                    break;
                case 'environment.action.bus':
                    $object = new EnvironmentAction($body);
                    break;
                case 'environment.event.bus':
                    $object = new EnvironmentEvent($body);
                    break;
                case 'session.action.bus':
                    $object = new SessionAction($body);
                    break;
                case 'session.event.bus':
                    $object = new SessionEvent($body);
                    break;
            endswitch;
        }
        
        return $object;
    }
}
