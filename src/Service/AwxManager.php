<?php

// src/Service/AwxManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
#use Doctrine\ORM\EntityManagerInterface;
use AwxV2\Oauth\Oauth2;
use AwxV2\Adapter\GuzzleHttpAdapter;
use AwxV2\AwxV2;
use AwxV2\Api\Me;
use AwxV2\Api\JobTemplate;
use AwxV2\Api\Project;
//use AwxV2\Api\Job;
use AwxV2\Entity\JobTemplate as JobTemplateEntity;
use AwxV2\Entity\Project as ProjectEntity;
use AwxV2\Entity\Job as JobEntity;

//use App\Entity\Tasks;
//use App\Entity\InstanceTypes;
#use Symfony\Component\Messenger\MessageBusInterface;

class AwxManager
{    
    /**
     * 
     * @var array<string>  
     */
    private array $params;

    private LoggerInterface $logger;

    private AwxV2 $awx;

#    private EntityManagerInterface $entityManager;
//    private TasksRepository $taskRepository;

    public function __construct( string $awx_client_id, string $awx_client_secret,
            string $awx_username, string $awx_password, string $awx_api_url,
            LoggerInterface $logger
            #, EntityManagerInterface $em
            )
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

//        $this->entityManager = $em;

        $this->params['awx_client_id']      = $awx_client_id;
        $this->params['awx_client_secret']  = $awx_client_secret;
        $this->params['awx_username']       = $awx_username;
        $this->params['awx_password']       = $awx_password;
        $this->params['awx_api_url']        = $awx_api_url;
        
        // get the task repository
//        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
    }

    public function getClient(): void
    {
	$awxVars = array (
	    'clientId'      => $this->params['awx_client_id'], // The client ID assigned by AWX when you created the application
	    'clientSecret'  => $this->params['awx_client_secret'],
	    'username'      => $this->params['awx_username'], // The AWX username associated with the application
	    'password'      => $this->params['awx_password'],
	    'apiUrl'        => $this->params['awx_api_url'], // Ie. https://x.x.x.x/api
	    'sslVerify'     => false, //SSL verify can be false during development and true after public SSL certificates are obtained
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
    }

    public function me(): Me
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->me();
    }

    public function template(): JobTemplate
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->jobTemplate();
    }

    /**
     * 
     * @return array<JobTemplateEntity>
     */
    public function getTemplates()#: ProjectEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->jobTemplate()->getAll();
    }

    /**
     * 
     * @param int $id
     * @return JobTemplateEntity
     */
    public function getTemplateById(int $id)#: ProjectEntity
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->jobTemplate()->getById($id);
    }
    
    public function project(): Project
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->project();
    }

    /**
     * 
     * @return array<ProjectEntity>
     */
    public function getProjects()
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->project()->getAll();
    }

    /**
     * 
     * @param int $id
     * @return ProjectEntity
     */
    public function getProjectById(int $id)
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->project()->getById($id);
    }
    
    /**
     * 
     * @param int $id
     * @return JobEntity
     */
    public function getJobById(int $id)
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	return $this->awx->job()->getById($id);
    }

    /**
     * 
     * @param array<string> $body
     */
    public function deployTestUser($body): void
    {
        // Hardcoded ID for user creation
        // It can be defferent for PROM
        $this->runJobTemplate(55, $body);
    }
        
    public function updateInventory(): void
    {
        // Hardcoded ID for inventory update
        // It can be defferent for PROM
//        $this->awxService->runJobTemplate(55, $body);
    }
    
    /**
     * 
     * @param int $id
     * @param array<string> $body
     * @return JobTemplateEntity
     */
    public function runJobTemplate($id, $body)
    {
        $this->logger->debug(__METHOD__);

	$this->getClient();

	// return the job template api
	$jobTemplate = $this->awx->jobTemplate();

	$this->logger->debug( "Launching job tempate id: " 
		. $id . " with body: ". json_encode($body));
	$runResult = $jobTemplate->launch($id, $body);

	//var_dump($runResult->id);

//	$job = $this->awx->Job();
/*
	while(true) {

	  $jobResult = $job->getById($runResult->id);
	  $this->logger->debug( "Current job status: ".$jobResult->status);

	  if ($jobResult->status == "successful" ||
                    $jobResult->status == "failed") {
                break;
            }

            sleep( 1);
	}
*/		
	return $runResult;
    }
}

