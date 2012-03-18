-- MySQL dump 10.13  Distrib 5.5.20, for Linux (x86_64)
--
-- Host: localhost    Database:
-- ------------------------------------------------------
-- Server version	5.5.20

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `Asenine_Media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_Media` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isEnabled` tinyint(1) NOT NULL DEFAULT '1',
  `timeCreated` int(10) unsigned NOT NULL,
  `timeModified` int(10) unsigned DEFAULT NULL,
  `fileHash` char(32) NOT NULL,
  `fileSize` int(12) unsigned DEFAULT NULL,
  `fileOriginalName` varchar(256) DEFAULT NULL,
  `mediaType` enum('image','audio','video','rotate') NOT NULL,
  `fileMimeType` varchar(64) DEFAULT NULL,
  `orientation` decimal(6,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `fileHash` (`fileHash`),
  KEY `timeCreated` (`timeCreated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Policies`
--

DROP TABLE IF EXISTS `Asenine_Policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_Policies` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `policy` varchar(64) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `policy` (`policy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserGroupPolicies`
--

DROP TABLE IF EXISTS `Asenine_UserGroupPolicies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_UserGroupPolicies` (
  `userGroupID` int(10) unsigned NOT NULL,
  `policyID` int(10) unsigned NOT NULL,
  UNIQUE KEY `userGroupID` (`userGroupID`,`policyID`),
  KEY `policyID` (`policyID`),
  CONSTRAINT `UserGroupPolicies_ibfk_1` FOREIGN KEY (`userGroupID`) REFERENCES `Asenine_UserGroups` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `UserGroupPolicies_ibfk_2` FOREIGN KEY (`policyID`) REFERENCES `Asenine_Policies` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserGroupUsers`
--

DROP TABLE IF EXISTS `Asenine_UserGroupUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_UserGroupUsers` (
  `userGroupID` int(10) unsigned NOT NULL,
  `userID` int(10) unsigned NOT NULL,
  UNIQUE KEY `userGroupID` (`userGroupID`,`userID`),
  KEY `userID` (`userID`),
  CONSTRAINT `UserGroupUsers_ibfk_1` FOREIGN KEY (`userGroupID`) REFERENCES `Asenine_UserGroups` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `UserGroupUsers_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `Asenine_Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserGroups`
--

DROP TABLE IF EXISTS `Asenine_UserGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_UserGroups` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timeCreated` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `label` varchar(32) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `isTaskAssignable` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserPolicies`
--

DROP TABLE IF EXISTS `Asenine_UserPolicies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_UserPolicies` (
  `userID` int(10) unsigned NOT NULL,
  `policyID` int(10) unsigned NOT NULL,
  UNIQUE KEY `userID` (`userID`,`policyID`),
  KEY `policyID` (`policyID`),
  CONSTRAINT `UserPolicies_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Asenine_Users` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `UserPolicies_ibfk_2` FOREIGN KEY (`policyID`) REFERENCES `Asenine_Policies` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserSecurityIPs`
--

DROP TABLE IF EXISTS `Asenine_UserSecurityIPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_UserSecurityIPs` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(10) unsigned NOT NULL,
  `policy` enum('allow','deny') NOT NULL DEFAULT 'deny',
  `spanStart` int(10) unsigned NOT NULL,
  `spanAppend` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `userID` (`userID`),
  CONSTRAINT `UserSecurityIPs_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Asenine_Users` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserSettings`
--

DROP TABLE IF EXISTS `Asenine_UserSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_UserSettings` (
  `userID` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` varchar(32) NOT NULL,
  UNIQUE KEY `userID` (`userID`,`name`),
  KEY `userID_2` (`userID`),
  KEY `name` (`name`),
  CONSTRAINT `UserSettings_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Asenine_Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Asenine_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asenine_Users` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isEnabled` tinyint(1) DEFAULT NULL,
  `timeCreated` int(10) unsigned NOT NULL,
  `timeModified` int(10) unsigned NOT NULL,
  `timeLastLogin` int(10) unsigned DEFAULT NULL,
  `timePasswordLastChange` int(10) unsigned DEFAULT NULL,
  `timeAuthtokenCreated` int(10) unsigned NOT NULL DEFAULT '0',
  `timeAutoLogout` int(10) unsigned DEFAULT NULL,
  `countLoginsSuccessful` int(10) unsigned NOT NULL DEFAULT '0',
  `countLoginsFailed` int(10) unsigned NOT NULL DEFAULT '0',
  `countLoginsFailedStreak` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `isAdministrator` tinyint(1) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `passwordHash` char(128) DEFAULT NULL,
  `passwordCrypto` char(128) DEFAULT NULL,
  `passwordAuthtoken` char(128) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `language` varchar(8) DEFAULT NULL,
  `preferences` text,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;