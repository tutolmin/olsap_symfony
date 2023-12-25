<?php

// src/Service/AwxManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use AwxV2\Oauth\Oauth2;
use AwxV2\Adapter\GuzzleHttpAdapter;
use AwxV2\AwxV2;
#use AwxV2\Entity\Me as MeEntity;
#use AwxV2\Entity\Project as ProjectEntity;

//use App\Entity\Tasks;
//use App\Entity\InstanceTypes;

class AwxManager
{
    private $logger;

    private $awx;

    private $entityManager;
//    private $taskRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;

        // get the task repository
//        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
    }

    public function getClient()#: MeEntity
    {
	$awxVars = array (
	    'clientId' => $_ENV["AWX_CLIENT_ID"], // The client ID assigned by AWX when you created the application
	    'clientSecret' => $_ENV["AWX_CLIENT_SECRET"],
	    'username' => $_ENV["AWX_USERNAME"], // The AWX username associated with the application
	    'password' => $_ENV["AWX_PASSWORD"],
	    'apiUrl' => $_ENV["AWX_API_URL"], // Ie. https://x.x.x.x/api
	    'sslVerify' => false, //SSL verify can be false during development and true after public SSL certificates are obtained
	    );

        $this->logger->debug(__METHOD__);

	// Create oauth2 object
	$oauth2 = new Oauth2($awxVars);

	// Get access and refresh tokens and expire time
	$tokens = $oauth2->passCredGrant();

	// Get access token
	$accessToken = $tokens->getToken();

	// create an adapter and add access token
	$adapter = new GuzzleHttpAdapter($accessToken, $awxVars['sslVerify']);

	// create an Awx object with the previous adapter
	$this->awx = new AwxV2($adapter, $awxVars['apiUrl']);

	return true;
    }

    public function me()#: MeEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->me();
    }

    public function template()#: TemplateEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->jobTemplate();
    }

    public function getTemplates()#: ProjectEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->jobTemplate()->getAll();
    }

    public function project()#: ProjectEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->project();
    }

    public function getProjects()#: ProjectEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->project()->getAll();
    }

    public function runJobTemplate($id, $body)#: MeEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	// return the job template api
	$jobTemplate = $this->awx->jobTemplate();

	$this->logger->debug( "Launching job tempate id: " 
		. $id . " with body: ". json_encode($body));
	$runResult = $jobTemplate->launch($id, $body);

	//var_dump($runResult->id);

	$job = $this->awx->Job();

	while(true) {

	  $jobResult = $job->getById($runResult->id);
	  $this->logger->debug( "Current job status: ".$jobResult->status);

	  if ($jobResult->status == "successful" ||
                    $jobResult->status == "failed") {
                break;
            }

            sleep( 1);
	}
		
	return $jobResult;
    }


/*
    public function getNextTask( Awx $session): Tasks
    {
        $this->logger->debug(__METHOD__);

        $tasks = $this->taskRepository->findAll();

        return $tasks[rand(0,count($tasks)-1)];
    }
*/

}

