<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MessengerMessages;
use App\Repository\MessengerMessagesRepository;

class MessengerMessagesTest extends KernelTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var array<string>
     */
//    private $dummy = array('hash'=>'dummy');
    
    /**
     * 
     * @var MessengerMessagesRepository
     */
    private $messengerMessagesRepository;
   
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->messengerMessagesRepository = $this->entityManager->getRepository(MessengerMessages::class);
    }
    
    public function testMessengerMessagesListIsEmpty(): void {

        $messengerMessages = $this->messengerMessagesRepository->findAll();
        $this->assertEmpty($messengerMessages);
    }
/*
    public function testCanNotAddMessengerMessageWithoutMandatoryFields(): void {
        
        $this->assertFalse($this->messengerMessagesRepository->add(new MessengerMessages(), true));
    }
    

    public function testCanAddDummyMessengerMessage(): MessengerMessages {

        $messengerMessageStatus = $this->messengerMessagesStatusesRepository->findOneByStatus('New');
        $this->assertNotNull($messengerMessageStatus);

        $testee = $this->testeesRepository->findOneBy(array());
        $this->assertNotNull($testee);

        $messengerMessage = new MessengerMessages();
        $messengerMessage->setHash($this->dummy['hash']);
        $messengerMessage->setStatus($messengerMessageStatus);
        $messengerMessage->setTestee($testee);
        $messengerMessage->setCreatedAt(new \DateTimeImmutable('now'));
        
        $this->assertTrue($this->messengerMessagesRepository->add($messengerMessage, true));
        
        return $messengerMessage;
    }

    public function testCanRemoveAllMessengerMessages(array $messengerMessages): void { 

        foreach ($messengerMessages as $s) {
            
            $messengerMessage = $this->messengerMessagesRepository->findOneById($s);
            $this->assertNotNull($messengerMessage);
            $id = $messengerMessage->getId();

            $this->messengerMessageManager->removeMessengerMessage($messengerMessage);
            
            $removed_messengerMessage = $this->messengerMessagesRepository->findOneById($id);
            $this->assertNull($removed_messengerMessage);
        }
    }


    public function testCanNotAddDuplicateMessengerMessage( array $messengerMessages): void {
                
        $existing_record = $messengerMessages[0];

        $messengerMessageStatus = $this->messengerMessagesStatusesRepository->findOneById($existing_record->getStatus()->getId());
        $this->assertNotNull($messengerMessageStatus);
              
        $testee = $this->testeesRepository->findOneById($existing_record->getTestee()->getId());
        $this->assertNotNull($testee);
      
        $messengerMessage = new MessengerMessages();
        $messengerMessage->setHash($existing_record->getHash());
        $messengerMessage->setStatus($messengerMessageStatus);
        $messengerMessage->setTestee($testee);
        $messengerMessage->setCreatedAt(new \DateTimeImmutable('now'));
        
        $this->assertFalse($this->messengerMessagesRepository->add($messengerMessage, true));
    }
 * 
 */
}
