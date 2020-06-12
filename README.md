```sql
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
```