<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MessengerMessages;
use App\Repository\MessengerMessagesRepository;

use App\Message\LxcOperation;
use App\Message\LxcEvent;
use App\Message\AwxAction;
use App\Message\AwxEvent;
use App\Message\EnvironmentAction;
use App\Message\EnvironmentEvent;
use App\Message\SessionAction;
use App\Message\SessionEvent;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use App\Service\MessengerMessagesManager;


#[AsCommand(
    name: 'app:messenger:dump-messages',
    description: 'Dumps messages stored in Doctrine transport at the moment.',
)]
class MessengerDumpMessagesCommand extends Command
{
        
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    private MessengerMessagesRepository $messengerRepository;

    private MessengerMessagesManager $messengerManager;
    
    public function __construct(EntityManagerInterface $entityManager,
            MessengerMessagesManager $messengerManager) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->messengerRepository = $this->entityManager->getRepository(MessengerMessages::class);
        $this->messengerManager = $messengerManager;
    }

    protected function configure(): void {
        /*
          $this
          ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
          ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
          ;
         * 
         */
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        // Get all the messages from the DB
        $messages = $this->messengerRepository->findAll();
        if (empty($messages)) {
            
            $io->warning('No messages in the DB.');
            return Command::FAILURE;
        }
        
        // Iterate through all the messages
        foreach ($messages as $message) {
            
            $object = $this->messengerManager->parseBody($message);
            if (is_string($object)) {
                $io->note($object);
            }

            /*
            $stamps = [];
            $headers = json_decode($message->getHeaders() ?? "", true);
            if (is_array($headers) && isset($headers['stamps'])) {
                $stamps = unserialize($headers['stamps']);
            }

            if (!is_array($stamps)) {
                
                $io->error('Envelope has no stamps. Skipping message.');
                continue;
            }

            $body = json_decode($message->getBody() ?? "", true);
            if (!is_array($body)) {
                
                $io->error('Message body is empty. Skipping message.');
                continue;
            }

            foreach ($stamps as $stamp) {
                if ($stamp instanceof BusNameStamp) {
                    switch ($stamp->getBusName()):
                        case 'lxc.operation.bus':
                            $io->note(new LxcOperation($body));
                            break;
                        case 'lxc.event.bus':
                            $io->note(new LxcEvent($body));
                            break;
                        case 'awx.action.bus':
                            $io->note(new AwxAction($body));
                            break;
                        case 'awx.event.bus':
                            $io->note(new AwxEvent($body));
                            break;
                        case 'environment.action.bus':
                            $io->note(new EnvironmentAction($body));
                            break;
                        case 'environment.event.bus':
                            $io->note(new EnvironmentEvent($body));
                            break;
                        case 'session.action.bus':
                            $io->note(new SessionAction($body));
                            break;
                        case 'session.event.bus':
                            $io->note(new SessionEvent($body));
                            break;
                    endswitch;
                }
            }
 * 
 */
        }

        return Command::SUCCESS;
    }
}
