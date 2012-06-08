create database if not exists glass;
use glass;

CREATE TABLE `glass_user` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `userType` varchar(30) NOT NULL,
  PRIMARY KEY (`userID`)
);

CREATE TABLE  `glass_modules` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `name` VARCHAR( 30 ) NOT NULL ,
 `folder` VARCHAR( 30 ) NOT NULL ,
 `description` VARCHAR( 500 ) NULL
);

CREATE TABLE  `glass_dashboard` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `userId` INT NOT NULL ,
 `moduleId` INT NOT NULL ,
 `bdCAMid` INT NOT NULL ,
 `widgetconf` VARCHAR( 500 ) NULL,
 `pos` INT NOT NULL 
);

CREATE TABLE  `glass_ddbb` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `host` VARCHAR( 30 ) NOT NULL ,
 `name` VARCHAR( 30 ) NOT NULL ,
 `user` VARCHAR( 30 ) NOT NULL ,
 `pass` VARCHAR( 30 ) NOT NULL ,
 `filters` VARCHAR( 500 ) NOT NULL ,
 `description` VARCHAR( 500 ) NULL
);

CREATE TABLE  `glass_permision`(
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
INSERT INTO  `glass_permision` (`userType`,`userViewLevel`,`userModifyPermision`,`userTypeChange`,
`moduleInstall`,`importView`,`varSettings`,`addBBDDCAM`,`download`,`viewUser`,`viewSuggest`)VALUES
('admin','4','1','1','1','1','1','1','1','1','1'),
('instructor','3','0','0','0','1','0','1','1','1','1'),
('observer',  '2','0','0','0','0','0','0','1','0','1'),
('student',  '1', '0','0','0','0','0','0','1','0','1');


CREATE TABLE  `glass_settings` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `userId` INT NOT NULL,
 `ddbbId` INT NOT NULL,
 `dbcol` INT NOT NULL
);

CREATE TABLE  `glass_myview` (
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

INSERT INTO `glass_user` (`userID`, `name`, `userType`) VALUES 
(NULL, 'admin', 'admin');

