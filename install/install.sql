create database if not exists ##dbname##;
use ##dbname##;

CREATE TABLE `##prefix##user` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `userType` varchar(30) NOT NULL,
  PRIMARY KEY (`userID`)
);

CREATE TABLE  `##prefix##modules` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `name` VARCHAR( 30 ) NOT NULL ,
 `folder` VARCHAR( 30 ) NOT NULL ,
 `description` VARCHAR( 500 ) NULL
);

CREATE TABLE  `##prefix##dashboard` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `userId` INT NOT NULL ,
 `moduleId` INT NOT NULL ,
 `bdCAMid` INT NOT NULL ,
 `widgetconf` VARCHAR( 500 ) NULL,
 `pos` INT NOT NULL 
);

CREATE TABLE  `##prefix##ddbb` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `host` VARCHAR( 30 ) NOT NULL ,
 `name` VARCHAR( 30 ) NOT NULL ,
 `user` VARCHAR( 30 ) NOT NULL ,
 `pass` VARCHAR( 30 ) NOT NULL ,
 `filters` VARCHAR( 500 ) NOT NULL ,
 `description` VARCHAR( 500 ) NULL
);

CREATE TABLE  `##prefix##permision`(
`userType` varchar(30) NOT NULL PRIMARY KEY ,
`userViewLevel` INT NOT NULL,
`userModifyPermision` BINARY NOT NULL,
`userTypeChange` BINARY NOT NULL,
`moduleInstall` BINARY NOT NULL,
`importView` BINARY NOT NULL,
`varSettings` BINARY NOT NULL,
`addBBDDCAM` BINARY NOT NULL,
`download` BINARY NOT NULL,
`viewUser` BINARY NOT NULL,
`viewSuggest` BINARY NOT NULL
);
INSERT INTO  `##prefix##permision` (`userType`,`userViewLevel`,`userModifyPermision`,`userTypeChange`,
`moduleInstall`,`importView`,`varSettings`,`addBBDDCAM`,`download`,`viewUser`,`viewSuggest`)VALUES
('admin','4','1','1','1','1','1','1','1','1','1'),
('instructor','3','0','0','0','1','0','1','1','1','1'),
('observer',  '2','0','0','0','0','0','0','1','0','1'),
('student',  '1', '0','0','0','0','0','0','1','0','1');


CREATE TABLE  `##prefix##settings` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `userId` INT NOT NULL,
 `ddbbId` INT NOT NULL,
 `dbcol` INT NOT NULL
);

CREATE TABLE  `##prefix##myview` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `name` VARCHAR( 60 ) NOT NULL ,
 `userid` INT NOT NULL ,
 `moduleId` INT NOT NULL ,
 `bdCAMid` INT NOT NULL ,
 `pos` INT NOT NULL ,
 `widgetconf` VARCHAR (500) NULL ,
 `description` VARCHAR(500) NULL ,
 `filters` TEXT NOT NULL
);

INSERT INTO `##prefix##user` (`name`, `userType`) VALUES 
('admin', 'admin');

INSERT INTO `##prefix##settings` (`userId`, `dbcol`)
SELECT `userId`, 2
FROM `##prefix##user`
WHERE `name` = 'admin';

