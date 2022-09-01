-- ****************** SqlDBM: MySQL ******************;
-- ***************************************************;
CREATE DATABASE IF NOT EXISTS programm_zup;

use programm_zup;  

--  allow tables to dropped without any FOREIGN_KEY_CHECKS 
    SET FOREIGN_KEY_CHECKS = 0;  
     
-- **** Drop data tables *****************************;
    DROP TABLE IF EXISTS `Comments`;
    DROP TABLE IF EXISTS `Parts`;
    DROP TABLE IF EXISTS `Objects`;  
    DROP TABLE IF EXISTS `Members`; 
    
-- **** Drop code tables *****************************;
    DROP TABLE IF EXISTS `Otype`;
    DROP TABLE IF EXISTS `Csts`;
    DROP TABLE IF EXISTS `msts`;
    DROP TABLE IF EXISTS `mtype`;
    DROP TABLE IF EXISTS `Scope`;     
-- 
-- ****************************************************; 
  
--  Turn FOREIGN_KEY_CHECKS  baxk on
    SET FOREIGN_KEY_CHECKS = 1;    
    
             
-- *** Create Code Tables *****************************; 

-- Member Status codes ;
CREATE TABLE IF NOT EXISTS `Msts`
(
 `mbr_sts`   	    char(1)      NOT NULL ,
 `description`      varchar(255) NOT NULL ,
PRIMARY KEY (`mbr_sts`)
)
ENGINE = INNODB;

-- Member Type codes ;
CREATE TABLE IF NOT EXISTS `Mtype`
(
 `mbr_type`   	    char(1)          NOT NULL ,
 `description`      varchar(255)     NOT NULL ,
PRIMARY KEY (`mbr_type`)
)
ENGINE = INNODB;

-- Oject type codes ;
CREATE TABLE IF NOT EXISTS `Otype`
(
 `obj_type`   	    tinyint unsigned NOT NULL ,
 `description`      varchar(255)     NOT NULL ,
PRIMARY KEY (`obj_type`)
)
ENGINE = INNODB;

-- Comment status codes ;
CREATE TABLE IF NOT EXISTS `Csts`
(
 `com_sts`   	    char(1)      NOT NULL ,
 `description`      varchar(255) NOT NULL ,
PRIMARY KEY (`com_sts`)
)
ENGINE = INNODB;

-- Comment status codes ;
CREATE TABLE IF NOT EXISTS `Scope`
(
 `scope_code`       tinyint unsigned NOT NULL ,
 `description`      varchar(255) NOT NULL ,
PRIMARY KEY (`scope_code`)
)
ENGINE = INNODB;

-- 
-- ****************************************************;                   
-- *** Create main data Tables ************************; 

CREATE TABLE IF NOT EXISTS `Members`
(
 `mbr_id`  	      integer unsigned   NOT NULL AUTO_INCREMENT ,
 `user_id`  	  char(25)           NOT NULL,
 `password`   	  varchar(250),
 `name`           varchar(50)        NOT NULL, 
 `hash`      	  varchar(250)       COMMENT 'Used to verify emai respone when setting password',
 `email`	      varchar(250)       NOT NULL,
 `signup_dt`      datetime           NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `mbr_sts`        char(1)            NOT NULL DEFAULT 'h' COMMENT 'hold status for all new mwmbers',
 `mbr_type`       char(1)            NOT NULL DEFAULT 'm' COMMENT 'basic membership',
 `lst_updt`       datetime           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`mbr_id`),
UNIQUE KEY  `unique_user_id` (`user_id`),
INDEX `mbr_sts_index` (`mbr_sts`),
INDEX `mbr_type_index` (`mbr_type`),
FOREIGN KEY (`mbr_type`) REFERENCES `Mtype` (`mbr_type`),   
FOREIGN KEY (`mbr_sts`)  REFERENCES `Msts` (`mbr_sts`)
)
ENGINE = INNODB;

ALTER TABLE `Members` AUTO_INCREMENT=1000;

CREATE TABLE IF NOT EXISTS `Objects`
(
 `obj_id`          integer unsigned   NOT NULL AUTO_INCREMENT,
 `mbr_id`          integer unsigned   NOT NULL,
 `title`           varchar(255),
 `obj_type`        tinyint unsigned   NOT NULL,
 `scope_code`      tinyint unsigned   NOT NULL DEFAULT 1,
 `obj_description` text,
 `lst_updt`        datetime           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`obj_id`),
INDEX `obj_type_index`   (`obj_type`),
INDEX `mbr_id_index`     (`mbr_id`),
INDEX `scope_code_index` (`scope_code`),
FOREIGN KEY (`scope_code`) REFERENCES `Scope`   (`scope_code`),
FOREIGN KEY (`obj_type`)   REFERENCES `Otype`   (`obj_type`),
FOREIGN KEY (`mbr_id`)     REFERENCES `Members` (`mbr_id`)  ON DELETE CASCADE
)
ENGINE = INNODB;


CREATE TABLE IF NOT EXISTS `Parts` 
(
 `part_id`         integer unsigned   NOT NULL AUTO_INCREMENT,
 `obj_id`          integer unsigned   NOT NULL,
 `file_name`       varchar(250)       NOT NULL,
 `file_type`       char(10)           NOT NULL  COMMENT 'video, image, audio, or poster', 
 `mime_code`       varchar(20)        NOT NULL,
 `seq`             integer            NOT NULL DEFAULT 0,
 `lst_updt`        datetime           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`part_id`),
 INDEX `obj_id_index1` (`obj_id`),
 UNIQUE KEY `unique_part_seq`(`obj_id`, `file_type`, `seq`),
 FOREIGN KEY (`obj_id`)    REFERENCES `Objects` (`obj_id`) ON DELETE CASCADE
)
ENGINE = INNODB;


CREATE TABLE IF NOT EXISTS `Comments`
(
 `com_id`          integer unsigned   NOT NULL AUTO_INCREMENT ,
 `obj_id`          integer unsigned   NOT NULL,
 `com_by`          char(25), 
 `com_sts`         char(1)            NOT NULL,
 `lst_updt`        datetime           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`com_id`),
 INDEX `com_sts_index` (`com_sts`),
 INDEX `obj_id_index2` (`obj_id`),
 FOREIGN KEY (`com_sts`) REFERENCES `Csts` (`com_sts`),
 FOREIGN KEY (`obj_id`)  REFERENCES `Objects` (`obj_id`)  ON DELETE CASCADE
)
ENGINE = INNODB;