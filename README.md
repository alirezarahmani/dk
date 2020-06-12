Introduction:
---
I this project I use no PHP framework and only use symfony component in my `composer.json`. I use docker to simplify set up. In this project I imagine it is sunny day :-). and I just implement one server and hard code it but system is capable to adding multi servers, just remove hard code section and add other servers.

System Design:
---
In this system Memcached is first layer of data access and mysql work as second layer and back up. If hit on memcached failed system will check mysql to find related data. Also, redis used for queueing purpose and redis helped us to queue unsent messages again by allowing them to be add to queue.
I use eventual consistency for report section, report should be updated by 20 mins by running it in background processing. No framework is used and every thing is simple enough for a test. This system designed with test purposed in a sunny day.
sms provider is just a mock which randomly send or not send (return error) for better testing purpose. 

How to use:
---

in order to use, you need to follow:

- run `./start.sh` 
- check if your docker containers are running (docker ps)
- run `docker-compose exec worker composer install`
- run query which is at end of this document on db (migration and seed are missing according to lack of time).
- goto `http://0.0.0.0:8080/sms/send?number=09122222222&body=hi` to add new sms request
- run `docker-compose exec worker php console sync:report`  command to generate and update reports
- goto `http://0.0.0.0:8080/` to see report results, it has very simple html page.
- run `docker-compose exec worker php console resend:msg` to resend unsent messages 


> note: as you see you should add commands to cron job to automatically do their jobs 

Final Consideration:
---
this is just a test and of course needs thousand improvement and fixes, but it design as simple as possible. Any more complicated design with details we can talk about it: feel free to send Email: alirezarahmani@live.com

SQL
---
```sql
CREATE DATABASE dk;
CREATE TABLE `dk`.`notifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mobile` VARCHAR(45) NULL,
  `server` VARCHAR(45) NULL,
  `port` VARCHAR(45) NULL,
  `synced` INT NULL,
  `status` INT(1) NULL,
  `type` INT(1) NULL,
  `created_at` DATETIME NULL,
  `body` VARCHAR(145) NULL,
  PRIMARY KEY (`id`));

INSERT INTO `notifications` (`id`,`mobile`,`server`,`port`,`synced`,`status`,`created_at`,`body`,`type`) VALUES (1,'09122222222','0.0.0.0','80',0,1,'2020-06-12 19:12:15','hi',1);
```

Not Working?
---
feel free to send email, maybe something is missing alirezarahmani@live.com
