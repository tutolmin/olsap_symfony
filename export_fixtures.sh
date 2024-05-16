#!/bin/bash

docker-compose exec php bin/console app:addresses:export             
docker-compose exec php bin/console app:breeds:export                
docker-compose exec php bin/console app:domains:export               
docker-compose exec php bin/console app:environment-statuses:export  
docker-compose exec php bin/console app:environments:export          
docker-compose exec php bin/console app:hardware-profiles:export     
docker-compose exec php bin/console app:instance-statuses:export     
docker-compose exec php bin/console app:operating-systems:export     
docker-compose exec php bin/console app:ports:export                 
docker-compose exec php bin/console app:session-oses:export          
docker-compose exec php bin/console app:session-statuses:export      
docker-compose exec php bin/console app:session-techs:export         
docker-compose exec php bin/console app:sessions:export              
docker-compose exec php bin/console app:task-instance-types:export   
docker-compose exec php bin/console app:task-oses:export             
docker-compose exec php bin/console app:task-techs:export            
docker-compose exec php bin/console app:tasks:export                 
docker-compose exec php bin/console app:technologies:export          
docker-compose exec php bin/console app:testees:export               
