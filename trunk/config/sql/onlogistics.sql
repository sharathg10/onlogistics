-- MySQL dump 10.11
--
-- Host: localhost    Database: prod_ol_maloles
-- ------------------------------------------------------
-- Server version	5.0.51a-24+lenny1-log

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

--
-- Table structure for table `AbstractDocument`
--

DROP TABLE IF EXISTS `AbstractDocument`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AbstractDocument` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_DocumentNo` varchar(255) default NULL,
  `_EditionDate` datetime default NULL,
  `_Command` int(11) NOT NULL default '0',
  `_CommandType` int(11) NOT NULL default '0',
  `_DocumentModel` int(11) NOT NULL default '0',
  `_Port` decimal(10,2) NOT NULL default '0.00',
  `_Packing` decimal(10,2) NOT NULL default '0.00',
  `_Insurance` decimal(10,2) NOT NULL default '0.00',
  `_GlobalHanding` decimal(10,2) NOT NULL default '0.00',
  `_TotalPriceHT` decimal(10,2) NOT NULL default '0.00',
  `_TotalPriceTTC` decimal(10,2) NOT NULL default '0.00',
  `_ToPay` decimal(10,2) NOT NULL default '0.00',
  `_PaymentDate` datetime default NULL,
  `_Comment` text,
  `_Invoice` int(11) NOT NULL default '0',
  `_Type` int(11) default '0',
  `_TVA` int(11) NOT NULL default '0',
  `_TvaSurtaxRate` decimal(10,2) NOT NULL default '0.00',
  `_FodecTaxRate` decimal(10,2) NOT NULL default '0.00',
  `_TaxStamp` decimal(10,2) NOT NULL default '0.00',
  `_CommercialCommissionPercent` decimal(10,2) NOT NULL default '0.00',
  `_CommercialCommissionAmount` decimal(10,2) NOT NULL default '0.00',
  `_RemainingTTC` decimal(10,2) default '0.00',
  `_SupplierCustomer` int(11) NOT NULL default '0',
  `_Currency` int(11) NOT NULL default '0',
  `_AccountingTypeActor` int(11) NOT NULL default '0',
  `_Locale` varchar(10) default NULL,
  `_BeginDate` date default NULL,
  `_EndDate` date default NULL,
  `_DestinatorSite` int(11) default NULL,
  `_CommandNo` varchar(30) default '',
  `_Transporter` int(11) NOT NULL default '0',
  `_PDFDocument` int(11) NOT NULL default '0',
  `_ConveyorArrivalSite` int(11) NOT NULL default '0',
  `_ConveyorDepartureSite` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Command` (`_Command`),
  KEY `_DocumentModel` (`_DocumentModel`),
  KEY `_Invoice` (`_Invoice`),
  KEY `_SupplierCustomer` (`_SupplierCustomer`),
  KEY `_Currency` (`_Currency`),
  KEY `_TVA` (`_TVA`),
  KEY `_AccountingTypeActor` (`_AccountingTypeActor`),
  KEY `_PDFDocument` (`_PDFDocument`),
  KEY `_ConveyorArrivalSite` (`_ConveyorArrivalSite`),
  KEY `_ConveyorDepartureSite` (`_ConveyorDepartureSite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `AbstractInstant`
--

DROP TABLE IF EXISTS `AbstractInstant`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AbstractInstant` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_Time` time default NULL,
  `_Day` int(11) NOT NULL default '0',
  `_Date` datetime default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Account`
--

DROP TABLE IF EXISTS `Account`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Account` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Number` varchar(255) NOT NULL default '',
  `_Name` varchar(255) default NULL,
  `_Comment` text,
  `_Currency` int(11) NOT NULL default '0',
  `_TVA` int(11) default NULL,
  `_BreakdownType` int(1) NOT NULL default '0',
  `_ActorBankDetail` int(11) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_Currency` (`_Currency`),
  KEY `_TVA` (`_TVA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `AccountingType`
--

DROP TABLE IF EXISTS `AccountingType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AccountingType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Type` varchar(255) default NULL,
  `_ActorBankDetail` int(11) NOT NULL default '0',
  `_DistributionKey` decimal(10,2) NOT NULL default '0.00',
  `_MainModel` int(1) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ActorBankDetail` (`_ActorBankDetail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Action`
--

DROP TABLE IF EXISTS `Action`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Action` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Commercial` int(11) NOT NULL default '0',
  `_Actor` int(11) NOT NULL default '0',
  `_FormModel` int(11) NOT NULL default '0',
  `_WishedDate` datetime default NULL,
  `_ActionDate` datetime default NULL,
  `_Type` int(3) NOT NULL default '0',
  `_State` int(3) NOT NULL default '0',
  `_Comment` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_Commercial` (`_Commercial`),
  KEY `_FormModel` (`_FormModel`),
  KEY `_Actor` (`_Actor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActivatedChain`
--

DROP TABLE IF EXISTS `ActivatedChain`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActivatedChain` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Reference` varchar(255) default NULL,
  `_Description` varchar(255) default NULL,
  `_DescriptionCatalog` varchar(255) default NULL,
  `_Owner` int(11) NOT NULL default '0',
  `_SiteTransition` int(11) NOT NULL default '0',
  `_PivotTask` int(11) NOT NULL default '0',
  `_PivotDateType` int(11) NOT NULL default '0',
  `_BeginDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_OwnerWorkerOrder` int(11) NOT NULL default '0',
  `_ExecutionSequence` int(11) NOT NULL default '0',
  `_BarCodeType` int(11) NOT NULL default '0',
  `_Type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Owner` (`_Owner`),
  KEY `_SiteTransition` (`_SiteTransition`),
  KEY `_PivotTask` (`_PivotTask`),
  KEY `_OwnerWorkerOrder` (`_OwnerWorkerOrder`),
  KEY `_BarCodeType` (`_BarCodeType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActivatedChainOperation`
--

DROP TABLE IF EXISTS `ActivatedChainOperation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActivatedChainOperation` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Actor` int(11) NOT NULL default '0',
  `_Operation` int(11) NOT NULL default '0',
  `_Ghost` int(11) NOT NULL default '0',
  `_ActivatedChain` int(11) NOT NULL default '0',
  `_OwnerWorkerOrder` int(11) NOT NULL default '0',
  `_RealActor` int(11) NOT NULL default '0',
  `_ConcreteProduct` int(11) NOT NULL default '0',
  `_RealConcreteProduct` int(11) NOT NULL default '0',
  `_FirstTask` int(11) NOT NULL default '0',
  `_LastTask` int(11) NOT NULL default '0',
  `_Order` int(11) NOT NULL default '0',
  `_OrderInWorkOrder` int(11) NOT NULL default '0',
  `_TaskCount` int(11) NOT NULL default '0',
  `_Massified` int(11) NOT NULL default '0',
  `_State` int(11) NOT NULL default '0',
  `_PrestationFactured` int(1) NOT NULL default '0',
  `_PrestationCommandDate` datetime default NULL,
  `_InvoiceItem` int(11) NOT NULL default '0',
  `_InvoicePrestation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Actor` (`_Actor`),
  KEY `_Operation` (`_Operation`),
  KEY `_Ghost` (`_Ghost`),
  KEY `_ActivatedChain` (`_ActivatedChain`),
  KEY `_OwnerWorkerOrder` (`_OwnerWorkerOrder`),
  KEY `_RealActor` (`_RealActor`),
  KEY `_ConcreteProduct` (`_ConcreteProduct`),
  KEY `_RealConcreteProduct` (`_RealConcreteProduct`),
  KEY `_FirstTask` (`_FirstTask`),
  KEY `_LastTask` (`_LastTask`),
  KEY `_InvoicePrestation` (`_InvoicePrestation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActivatedChainTask`
--

DROP TABLE IF EXISTS `ActivatedChainTask`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActivatedChainTask` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Order` int(11) NOT NULL default '0',
  `_Ghost` int(11) NOT NULL default '0',
  `_Interuptible` int(11) NOT NULL default '0',
  `_RawDuration` float NOT NULL default '0',
  `_DurationType` int(3) NOT NULL default '0',
  `_KilometerNumber` float NOT NULL default '0',
  `_TriggerMode` int(11) NOT NULL default '0',
  `_TriggerDelta` int(11) NOT NULL default '0',
  `_RawCost` decimal(10,2) NOT NULL default '0.00',
  `_CostType` int(3) NOT NULL default '0',
  `_Duration` float NOT NULL default '0',
  `_Cost` decimal(10,2) NOT NULL default '0.00',
  `_Instructions` varchar(255) default NULL,
  `_Task` int(11) NOT NULL default '0',
  `_ActorSiteTransition` int(11) NOT NULL default '0',
  `_DepartureInstant` int(11) NOT NULL default '0',
  `_ArrivalInstant` int(11) NOT NULL default '0',
  `_Begin` datetime default NULL,
  `_End` datetime default NULL,
  `_InterruptionDate` datetime default NULL,
  `_RestartDate` datetime default NULL,
  `_RealBegin` datetime default NULL,
  `_RealDuration` float NOT NULL default '0',
  `_RealCost` decimal(10,2) default NULL,
  `_RealQuantity` decimal(10,3) default '0.000',
  `_RealEnd` datetime default NULL,
  `_ActivatedOperation` int(11) NOT NULL default '0',
  `_ValidationUser` int(11) NOT NULL default '0',
  `_OwnerWorkerOrder` int(11) NOT NULL default '0',
  `_ActivatedChainTaskDetail` int(11) NOT NULL default '0',
  `_Massified` int(11) NOT NULL default '0',
  `_DataProvider` int(11) NOT NULL default '0',
  `_WithForecast` int(11) NOT NULL default '0',
  `_State` int(11) NOT NULL default '0',
  `_ProductCommandType` int(1) NOT NULL default '0',
  `_DepartureActor` int(11) NOT NULL default '0',
  `_DepartureSite` int(11) NOT NULL default '0',
  `_ArrivalActor` int(11) NOT NULL default '0',
  `_ArrivalSite` int(11) NOT NULL default '0',
  `_WishedDateType` int(11) NOT NULL default '0',
  `_Delta` int(11) NOT NULL default '0',
  `_ComponentQuantityRatio` int(1) default '0',
  `_ActivationPerSupplier` int(1) default '0',
  `_AssembledQuantity` decimal(10,3) default NULL,
  `_AssembledRealQuantity` decimal(10,3) default NULL,
  `_ChainToActivate` int(11) NOT NULL default '0',
  `_RessourceGroup` int(11) NOT NULL default '0',
  `_Component` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Ghost` (`_Ghost`),
  KEY `_Task` (`_Task`),
  KEY `_ActorSiteTransition` (`_ActorSiteTransition`),
  KEY `_DepartureInstant` (`_DepartureInstant`),
  KEY `_ArrivalInstant` (`_ArrivalInstant`),
  KEY `_ActivatedOperation` (`_ActivatedOperation`),
  KEY `_ValidationUser` (`_ValidationUser`),
  KEY `_OwnerWorkerOrder` (`_OwnerWorkerOrder`),
  KEY `_ActivatedChainTaskDetail` (`_ActivatedChainTaskDetail`),
  KEY `_DataProvider` (`_DataProvider`),
  KEY `_DepartureActor` (`_DepartureActor`),
  KEY `_DepartureSite` (`_DepartureSite`),
  KEY `_ArrivalActor` (`_ArrivalActor`),
  KEY `_ArrivalSite` (`_ArrivalSite`),
  KEY `_ChainToActivate` (`_ChainToActivate`),
  KEY `_RessourceGroup` (`_RessourceGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActivatedChainTaskDetail`
--

DROP TABLE IF EXISTS `ActivatedChainTaskDetail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActivatedChainTaskDetail` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_OilAdded` decimal(10,2) default NULL,
  `_CarburantRest` decimal(10,2) default NULL,
  `_CarburantAdded` decimal(10,2) default NULL,
  `_CarburantTotal` decimal(10,2) default NULL,
  `_CarburantUsed` decimal(10,2) default NULL,
  `_Comment` text,
  `_InstructorSeat` int(11) NOT NULL default '0',
  `_CustomerSeat` int(11) NOT NULL default '0',
  `_CycleEngine1N1` decimal(10,2) default NULL,
  `_CycleEngine1N2` decimal(10,2) default NULL,
  `_CycleEngine1` decimal(10,2) default NULL,
  `_CycleEngine2N1` decimal(10,2) default NULL,
  `_CycleEngine2N2` decimal(10,2) default NULL,
  `_CycleEngine2` decimal(10,2) default NULL,
  `_CycleCellule` int(11) default NULL,
  `_CycleTreuillage` int(11) default NULL,
  `_CycleCharge` int(11) default NULL,
  `_RealCommercialDuration` decimal(10,2) default NULL,
  `_EngineOn` datetime default NULL,
  `_EngineOff` datetime default NULL,
  `_TakeOff` datetime default NULL,
  `_Landing` datetime default NULL,
  `_TechnicalHour` decimal(10,2) default NULL,
  `_CelluleHour` decimal(10,2) default NULL,
  `_Nature` int(11) NOT NULL default '0',
  `_IFRLanding` int(11) NOT NULL default '0',
  `_PilotHours` int(11) NOT NULL default '0',
  `_PilotHoursBiEngine` int(11) NOT NULL default '0',
  `_CoPilotHours` int(11) NOT NULL default '0',
  `_CoPilotHoursBiEngine` int(11) NOT NULL default '0',
  `_PilotHoursNight` int(11) NOT NULL default '0',
  `_PilotHoursBiEngineNight` int(11) NOT NULL default '0',
  `_CoPilotHoursNight` int(11) NOT NULL default '0',
  `_CoPilotHoursBiEngineNight` int(11) NOT NULL default '0',
  `_PilotHoursIFR` int(11) NOT NULL default '0',
  `_CoPilotHoursIFR` int(11) NOT NULL default '0',
  `_StudentHours` int(11) NOT NULL default '0',
  `_PublicHours` int(11) NOT NULL default '0',
  `_VLAEHours` int(11) NOT NULL default '0',
  `_TakeOffNumber` int(11) NOT NULL default '0',
  `_LandingNumber` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActivatedMovement`
--

DROP TABLE IF EXISTS `ActivatedMovement`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActivatedMovement` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_StartDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_State` int(11) NOT NULL default '0',
  `_Quantity` decimal(10,3) default NULL,
  `_Type` int(11) NOT NULL default '0',
  `_ProductCommandItem` int(11) NOT NULL default '0',
  `_HasBeenFactured` int(11) NOT NULL default '0',
  `_ActivatedChainTask` int(11) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  `_ProductCommand` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Type` (`_Type`),
  KEY `_ProductCommandItem` (`_ProductCommandItem`),
  KEY `_ActivatedChainTask` (`_ActivatedChainTask`),
  KEY `_Product` (`_Product`),
  KEY `_ProductCommand` (`_ProductCommand`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Actor`
--

DROP TABLE IF EXISTS `Actor`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Actor` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_Name` varchar(255) default NULL,
  `_DatabaseOwner` int(1) NOT NULL default '0',
  `_Quality` int(1) NOT NULL default '0',
  `_Code` varchar(255) default NULL,
  `_Siret` varchar(255) default NULL,
  `_IATA` varchar(255) default NULL,
  `_Logo` text,
  `_Slogan` int(11) NOT NULL default '0',
  `_TVA` varchar(255) default NULL,
  `_RCS` varchar(255) default NULL,
  `_Role` varchar(255) default NULL,
  `_OnlogisticsAccount` varchar(255) default NULL,
  `_Active` int(11) NOT NULL default '1',
  `_Incoterm` int(11) NOT NULL default '0',
  `_PackageCondition` int(11) default NULL,
  `_Commercial` int(11) NOT NULL default '0',
  `_PlanningComment` varchar(255) default NULL,
  `_MainSite` int(11) NOT NULL default '0',
  `_Category` int(11) NOT NULL default '0',
  `_RemExcep` float NOT NULL default '0',
  `_Generic` int(11) NOT NULL default '0',
  `_GenericActor` int(11) NOT NULL default '0',
  `_Trademark` varchar(255) default NULL,
  `_CompanyType` varchar(255) default NULL,
  `_CreationDate` datetime default NULL,
  `_Currency` int(11) NOT NULL default '0',
  `_PricingZone` int(11) NOT NULL default '0',
  `_Weight` float NOT NULL default '0',
  `_Cost` float NOT NULL default '0',
  `_IFRLanding` int(11) NOT NULL default '0',
  `_PilotHours` int(11) NOT NULL default '0',
  `_PilotHoursBiEngine` int(11) NOT NULL default '0',
  `_CoPilotHours` int(11) NOT NULL default '0',
  `_CoPilotHoursBiEngine` int(11) NOT NULL default '0',
  `_PilotHoursNight` int(11) NOT NULL default '0',
  `_PilotHoursBiEngineNight` int(11) NOT NULL default '0',
  `_CoPilotHoursNight` int(11) NOT NULL default '0',
  `_CoPilotHoursBiEngineNight` int(11) NOT NULL default '0',
  `_PilotHoursIFR` int(11) NOT NULL default '0',
  `_CoPilotHoursIFR` int(11) NOT NULL default '0',
  `_StudentHours` int(11) NOT NULL default '0',
  `_InstructorHours` int(11) NOT NULL default '0',
  `_PublicHours` int(11) NOT NULL default '0',
  `_CommercialHours` int(11) NOT NULL default '0',
  `_VLAEHours` int(11) NOT NULL default '0',
  `_Trainee` int(1) default '0',
  `_SoloFly` int(1) default '0',
  `_LastFlyDate` datetime default NULL,
  `_Instructor` int(11) NOT NULL default '0',
  `_AccountingType` int(11) default '0',
  `_CustomerProperties` int(11) NOT NULL default '0',
  `_ActorDetail` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_Code` (`_Code`),
  KEY `_Incoterm` (`_Incoterm`),
  KEY `_Commercial` (`_Commercial`),
  KEY `_MainSite` (`_MainSite`),
  KEY `_Category` (`_Category`),
  KEY `_GenericActor` (`_GenericActor`),
  KEY `_Instructor` (`_Instructor`),
  KEY `_Currency` (`_Currency`),
  KEY `_CustomerProperties` (`_CustomerProperties`),
  KEY `_PricingZone` (`_PricingZone`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActorBankDetail`
--

DROP TABLE IF EXISTS `ActorBankDetail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActorBankDetail` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Iban` varchar(255) default NULL,
  `_BankName` varchar(255) default NULL,
  `_Swift` varchar(255) default NULL,
  `_BankAddressNo` varchar(255) default NULL,
  `_BankAddressStreetType` int(3) NOT NULL default '0',
  `_BankAddressStreet` varchar(255) default NULL,
  `_BankAddressAdd` varchar(255) default NULL,
  `_BankAddressCity` varchar(255) default NULL,
  `_BankAddressZipCode` varchar(255) default NULL,
  `_BankAddressCountry` varchar(255) default NULL,
  `_AccountNumber` varchar(255) default NULL,
  `_Amount` decimal(10,2) NOT NULL default '0.00',
  `_LastUpdate` date default NULL,
  `_Active` int(1) NOT NULL default '1',
  `_Actor` int(11) NOT NULL default '0',
  `_Currency` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Actor` (`_Actor`),
  KEY `_Currency` (`_Currency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActorDetail`
--

DROP TABLE IF EXISTS `ActorDetail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActorDetail` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_IsInternalAffectation` int(1) default '1',
  `_InternalAffectation` int(11) NOT NULL default '0',
  `_Signatory` int(11) NOT NULL default '0',
  `_BusinessProvider` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_InternalAffectation` (`_InternalAffectation`),
  KEY `_Signatory` (`_Signatory`),
  KEY `_BusinessProvider` (`_BusinessProvider`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActorProduct`
--

DROP TABLE IF EXISTS `ActorProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActorProduct` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_AssociatedProductReference` varchar(255) default NULL,
  `_Actor` int(11) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  `_BuyUnitQuantity` decimal(10,3) default NULL,
  `_BuyUnitType` int(11) NOT NULL default '0',
  `_Priority` int(1) default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Actor` (`_Actor`),
  KEY `_Product` (`_Product`),
  KEY `_BuyUnitType` (`_BuyUnitType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ActorSiteTransition`
--

DROP TABLE IF EXISTS `ActorSiteTransition`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ActorSiteTransition` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_DepartureZone` int(11) NOT NULL default '0',
  `_DepartureActor` int(11) NOT NULL default '0',
  `_DepartureSite` int(11) NOT NULL default '0',
  `_ArrivalZone` int(11) NOT NULL default '0',
  `_ArrivalActor` int(11) NOT NULL default '0',
  `_ArrivalSite` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_DepartureZone` (`_DepartureZone`),
  KEY `_DepartureActor` (`_DepartureActor`),
  KEY `_DepartureSite` (`_DepartureSite`),
  KEY `_ArrivalZone` (`_ArrivalZone`),
  KEY `_ArrivalActor` (`_ArrivalActor`),
  KEY `_ArrivalSite` (`_ArrivalSite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Alert`
--

DROP TABLE IF EXISTS `Alert`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Alert` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_Template` varchar(255) default NULL,
  `_BodyAddon` longtext,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `AnnualTurnoverDiscount`
--

DROP TABLE IF EXISTS `AnnualTurnoverDiscount`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AnnualTurnoverDiscount` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Amount` decimal(10,2) default NULL,
  `_Year` int(4) default NULL,
  `_SupplierCustomer` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_SupplierCustomer` (`_SupplierCustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `AnnualTurnoverDiscountPercent`
--

DROP TABLE IF EXISTS `AnnualTurnoverDiscountPercent`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AnnualTurnoverDiscountPercent` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Amount` decimal(10,2) default NULL,
  `_Date` datetime default NULL,
  `_Category` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Category` (`_Category`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `AnswerModel`
--

DROP TABLE IF EXISTS `AnswerModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AnswerModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Value` varchar(255) default NULL,
  `_Alert` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Alert` (`_Alert`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `BarCodeType`
--

DROP TABLE IF EXISTS `BarCodeType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `BarCodeType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` varchar(255) default NULL,
  `_Code` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Box`
--

DROP TABLE IF EXISTS `Box`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Box` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Reference` varchar(255) default NULL,
  `_Level` int(11) default NULL,
  `_Comment` text,
  `_Dimensions` varchar(255) NOT NULL,
  `_Date` datetime default NULL,
  `_Weight` float NOT NULL default '0',
  `_Volume` float NOT NULL default '0',
  `_ParentBox` int(11) NOT NULL default '0',
  `_ActivatedChain` int(11) NOT NULL default '0',
  `_CommandItem` int(11) NOT NULL default '0',
  `_CoverType` int(11) NOT NULL default '0',
  `_Expeditor` int(11) NOT NULL default '0',
  `_ExpeditorSite` int(11) NOT NULL default '0',
  `_Destinator` int(11) NOT NULL default '0',
  `_DestinatorSite` int(11) NOT NULL default '0',
  `_PackingList` int(11) NOT NULL default '0',
  `_LocationExecutedMovement` int(11) NOT NULL default '0',
  `_PrestationFactured` int(1) NOT NULL default '0',
  `_InvoicePrestation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ParentBox` (`_ParentBox`),
  KEY `_ActivatedChain` (`_ActivatedChain`),
  KEY `_CommandItem` (`_CommandItem`),
  KEY `_CoverType` (`_CoverType`),
  KEY `_Expeditor` (`_Expeditor`),
  KEY `_ExpeditorSite` (`_ExpeditorSite`),
  KEY `_Destinator` (`_Destinator`),
  KEY `_DestinatorSite` (`_DestinatorSite`),
  KEY `_PackingList` (`_PackingList`),
  KEY `_LocationExecutedMovement` (`_LocationExecutedMovement`),
  KEY `_InvoicePrestation` (`_InvoicePrestation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CacheData`
--

DROP TABLE IF EXISTS `CacheData`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CacheData` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_Data` longtext,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Catalog`
--

DROP TABLE IF EXISTS `Catalog`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Catalog` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_ItemPerPage` int(11) NOT NULL default '0',
  `_Page` varchar(255) default NULL,
  `_CadencedOrder` int(1) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CatalogCriteria`
--

DROP TABLE IF EXISTS `CatalogCriteria`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CatalogCriteria` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Property` int(11) NOT NULL default '0',
  `_DisplayName` varchar(255) default NULL,
  `_Index` int(11) NOT NULL default '0',
  `_Displayable` int(11) NOT NULL default '0',
  `_Searchable` int(11) NOT NULL default '0',
  `_SearchIndex` int(11) NOT NULL default '0',
  `_Catalog` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Property` (`_Property`),
  KEY `_Catalog` (`_Catalog`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Category` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Attractivity` int(11) NOT NULL default '0',
  `_Description` varchar(255) default NULL,
  `_LastModified` datetime default NULL,
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_Name` (`_Name`),
  KEY `_Attractivity` (`_Attractivity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Chain`
--

DROP TABLE IF EXISTS `Chain`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Chain` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Reference` varchar(255) default NULL,
  `_Description` varchar(255) default NULL,
  `_DescriptionCatalog` varchar(255) default NULL,
  `_State` int(11) NOT NULL default '0',
  `_Owner` int(11) NOT NULL default '0',
  `_SiteTransition` int(11) NOT NULL default '0',
  `_PivotTask` int(11) NOT NULL default '0',
  `_PivotDateType` int(11) NOT NULL default '0',
  `_CommandSequence` int(11) NOT NULL default '0',
  `_CreatedDate` datetime default NULL,
  `_BarCodeType` int(11) NOT NULL default '0',
  `_Type` int(11) NOT NULL default '0',
  `_AutoAssignTo` int(3) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Owner` (`_Owner`),
  KEY `_SiteTransition` (`_SiteTransition`),
  KEY `_PivotTask` (`_PivotTask`),
  KEY `_BarCodeType` (`_BarCodeType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ChainOperation`
--

DROP TABLE IF EXISTS `ChainOperation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ChainOperation` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Actor` int(11) NOT NULL default '0',
  `_Operation` int(11) NOT NULL default '0',
  `_Chain` int(11) NOT NULL default '0',
  `_Order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Actor` (`_Actor`),
  KEY `_Operation` (`_Operation`),
  KEY `_Chain` (`_Chain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ChainTask`
--

DROP TABLE IF EXISTS `ChainTask`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ChainTask` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Order` int(11) NOT NULL default '0',
  `_Interuptible` int(11) NOT NULL default '0',
  `_Duration` float NOT NULL default '0',
  `_DurationType` int(11) NOT NULL default '0',
  `_KilometerNumber` float NOT NULL default '0',
  `_Cost` decimal(10,2) NOT NULL default '0.00',
  `_CostType` int(11) NOT NULL default '0',
  `_Instructions` varchar(255) default NULL,
  `_TriggerMode` int(11) NOT NULL default '0',
  `_TriggerDelta` float NOT NULL default '0',
  `_Task` int(11) NOT NULL default '0',
  `_ActorSiteTransition` int(11) NOT NULL default '0',
  `_DepartureInstant` int(11) NOT NULL default '0',
  `_ArrivalInstant` int(11) NOT NULL default '0',
  `_Operation` int(11) NOT NULL default '0',
  `_AutoAlert` int(11) default NULL,
  `_ProductCommandType` int(1) NOT NULL default '0',
  `_DepartureActor` int(11) NOT NULL default '0',
  `_DepartureSite` int(11) NOT NULL default '0',
  `_ArrivalActor` int(11) NOT NULL default '0',
  `_ArrivalSite` int(11) NOT NULL default '0',
  `_WishedDateType` int(11) NOT NULL default '0',
  `_Delta` int(11) NOT NULL default '0',
  `_ComponentQuantityRatio` int(1) default '0',
  `_ActivationPerSupplier` int(1) default '0',
  `_ChainToActivate` int(11) NOT NULL default '0',
  `_RessourceGroup` int(11) NOT NULL default '0',
  `_Component` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Task` (`_Task`),
  KEY `_ActorSiteTransition` (`_ActorSiteTransition`),
  KEY `_DepartureInstant` (`_DepartureInstant`),
  KEY `_ArrivalInstant` (`_ArrivalInstant`),
  KEY `_Operation` (`_Operation`),
  KEY `_DepartureActor` (`_DepartureActor`),
  KEY `_DepartureSite` (`_DepartureSite`),
  KEY `_ArrivalActor` (`_ArrivalActor`),
  KEY `_ArrivalSite` (`_ArrivalSite`),
  KEY `_ChainToActivate` (`_ChainToActivate`),
  KEY `_RessourceGroup` (`_RessourceGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CityName`
--

DROP TABLE IF EXISTS `CityName`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CityName` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` varchar(255) default NULL,
  `_Department` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Department` (`_Department`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Command`
--

DROP TABLE IF EXISTS `Command`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Command` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_CommandNo` varchar(255) default NULL,
  `_Type` int(11) NOT NULL default '0',
  `_CommandDate` datetime default NULL,
  `_MerchandiseValue` decimal(10,2) NOT NULL default '0.00',
  `_AdditionnalGaranties` int(11) default NULL,
  `_Incoterm` int(11) NOT NULL default '0',
  `_WishedStartDate` datetime default NULL,
  `_WishedEndDate` datetime default NULL,
  `_Comment` varchar(255) default NULL,
  `_Expeditor` int(11) NOT NULL default '0',
  `_Destinator` int(11) NOT NULL default '0',
  `_ExpeditorSite` int(11) NOT NULL default '0',
  `_DestinatorSite` int(11) NOT NULL default '0',
  `_Customer` int(11) NOT NULL default '0',
  `_SupplierCustomer` int(11) NOT NULL default '0',
  `_Commercial` int(11) NOT NULL default '0',
  `_State` int(11) NOT NULL default '0',
  `_Handing` decimal(10,2) NOT NULL default '0.00',
  `_HandingByRangePercent` decimal(10,2) NOT NULL default '0.00',
  `_Port` decimal(10,2) NOT NULL default '0.00',
  `_Packing` decimal(10,2) NOT NULL default '0.00',
  `_Insurance` decimal(10,2) NOT NULL default '0.00',
  `_TotalPriceHT` decimal(10,2) NOT NULL default '0.00',
  `_TotalPriceTTC` decimal(10,2) NOT NULL default '0.00',
  `_Processed` int(11) NOT NULL default '0',
  `_Installment` decimal(10,2) NOT NULL default '0.00',
  `_CustomerRemExcep` decimal(10,2) NOT NULL default '0.00',
  `_Duration` time default NULL,
  `_Cadenced` int(1) NOT NULL default '0',
  `_Closed` int(1) NOT NULL default '0',
  `_IsEstimate` int(1) default '0',
  `_CommandExpeditionDetail` int(11) NOT NULL default '0',
  `_Currency` int(11) NOT NULL default '0',
  `_WarrantyEndDate` datetime default NULL,
  `_SoloFly` int(1) default '0',
  `_IsWishedInstructor` int(1) default '0',
  `_Instructor` int(11) NOT NULL default '0',
  `_AeroConcreteProduct` int(11) NOT NULL default '0',
  `_FlyType` int(11) NOT NULL default '0',
  `_InputationNo` varchar(255) default NULL,
  `_DeliveryPayment` decimal(10,2) default NULL,
  `_DateType` int(11) NOT NULL default '0',
  `_ActorBankDetail` int(11) NOT NULL default '0',
  `_InstallmentBank` int(11) NOT NULL default '0',
  `_Command` int(11) NOT NULL default '0',
  `_ParentCommand` int(11) NOT NULL default '0',
  `_ProjectManager` int(11) NOT NULL default '0',
  `_Chain` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Incoterm` (`_Incoterm`),
  KEY `_Expeditor` (`_Expeditor`),
  KEY `_Destinator` (`_Destinator`),
  KEY `_ExpeditorSite` (`_ExpeditorSite`),
  KEY `_DestinatorSite` (`_DestinatorSite`),
  KEY `_Customer` (`_Customer`),
  KEY `_Commercial` (`_Commercial`),
  KEY `_Instructor` (`_Instructor`),
  KEY `_FlyType` (`_FlyType`),
  KEY `_CommandExpeditionDetail` (`_CommandExpeditionDetail`),
  KEY `_Currency` (`_Currency`),
  KEY `_AeroConcreteProduct` (`_AeroConcreteProduct`),
  KEY `_SupplierCustomer` (`_SupplierCustomer`),
  KEY `_ActorBankDetail` (`_ActorBankDetail`),
  KEY `_InstallmentBank` (`_InstallmentBank`),
  KEY `_ProjectManager` (`_ProjectManager`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CommandEvent`
--

DROP TABLE IF EXISTS `CommandEvent`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CommandEvent` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_Date` datetime default NULL,
  `_Amount` float default NULL,
  `_Type` int(11) unsigned NOT NULL default '0',
  `_Command` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Command` (`_Command`),
  KEY `_Type` (`_Type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CommandEventType`
--

DROP TABLE IF EXISTS `CommandEventType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CommandEventType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_ShortName` varchar(255) default NULL,
  `_LongName` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `Id` (`_Id`),
  KEY `Id_2` (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CommandExpeditionDetail`
--

DROP TABLE IF EXISTS `CommandExpeditionDetail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CommandExpeditionDetail` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_LoadingPort` varchar(255) default NULL,
  `_Shipment` int(11) default NULL,
  `_CustomerCommandNo` varchar(255) default NULL,
  `_DestinatorStore` varchar(255) default NULL,
  `_DestinatorRange` varchar(255) default NULL,
  `_ReservationNo` varchar(255) default NULL,
  `_Season` varchar(255) default NULL,
  `_Comment` text,
  `_Deal` varchar(255) default NULL,
  `_AirwayBill` varchar(255) default NULL,
  `_PackingList` varchar(255) default NULL,
  `_SupplierCode` varchar(255) default NULL,
  `_Weight` decimal(10,2) default '0.00',
  `_NumberOfContainer` int(11) default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CommandItem`
--

DROP TABLE IF EXISTS `CommandItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CommandItem` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_ActivatedChain` int(11) NOT NULL default '0',
  `_Command` int(11) NOT NULL default '0',
  `_Width` float NOT NULL default '0',
  `_Height` float NOT NULL default '0',
  `_Length` float NOT NULL default '0',
  `_Weight` float NOT NULL default '0',
  `_Quantity` decimal(10,3) NOT NULL default '0.000',
  `_Gerbability` int(11) NOT NULL default '0',
  `_MasterDimension` int(11) NOT NULL default '0',
  `_Comment` varchar(255) default NULL,
  `_Handing` varchar(10) default NULL,
  `_TVA` int(11) NOT NULL default '0',
  `_PriceHT` decimal(10,2) NOT NULL default '0.00',
  `_WishedDate` datetime default NULL,
  `_Product` int(11) NOT NULL default '0',
  `_ActivatedMovement` int(11) NOT NULL default '0',
  `_Promotion` int(11) NOT NULL default '0',
  `_CoverType` int(11) NOT NULL default '0',
  `_ProductType` int(11) NOT NULL default '0',
  `_Prestation` int(11) NOT NULL default '0',
  `_UnitPriceHT` decimal(10,3) NOT NULL default '0.000',
  `_CostType` int(2) NOT NULL default '-1',
  `_QuantityForPrestationCost` decimal(10,3) default '0.000',
  `_PackagingUnitQuantity` int(11) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_ActivatedChain` (`_ActivatedChain`),
  KEY `_Command` (`_Command`),
  KEY `_Product` (`_Product`),
  KEY `_ActivatedMovement` (`_ActivatedMovement`),
  KEY `_Promotion` (`_Promotion`),
  KEY `_CoverType` (`_CoverType`),
  KEY `_ProductType` (`_ProductType`),
  KEY `_Prestation` (`_Prestation`),
  KEY `_TVA` (`_TVA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CommunicationModality`
--

DROP TABLE IF EXISTS `CommunicationModality`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CommunicationModality` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Phone` varchar(255) default NULL,
  `_Fax` varchar(255) default NULL,
  `_Email` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Component`
--

DROP TABLE IF EXISTS `Component`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Component` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Level` int(11) default NULL,
  `_Quantity` decimal(10,3) default NULL,
  `_PercentWasted` decimal(10,2) NOT NULL default '0.00',
  `_Nomenclature` int(11) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  `_Parent` int(11) NOT NULL default '0',
  `_ComponentGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Nomenclature` (`_Nomenclature`),
  KEY `_Product` (`_Product`),
  KEY `_Parent` (`_Parent`),
  KEY `_ComponentGroup` (`_ComponentGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ComponentGroup`
--

DROP TABLE IF EXISTS `ComponentGroup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ComponentGroup` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Nomenclature` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Nomenclature` (`_Nomenclature`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ConcreteComponent`
--

DROP TABLE IF EXISTS `ConcreteComponent`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ConcreteComponent` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Quantity` decimal(10,3) NOT NULL default '1.000',
  `_ConcreteProduct` int(11) NOT NULL default '0',
  `_Parent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ConcreteProduct` (`_ConcreteProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ConcreteProduct`
--

DROP TABLE IF EXISTS `ConcreteProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ConcreteProduct` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_SerialNumber` varchar(255) default NULL,
  `_Immatriculation` varchar(255) default NULL,
  `_MaxWeightOnTakeOff` decimal(10,2) NOT NULL default '0.00',
  `_MaxWeightBySeat` decimal(10,2) NOT NULL default '0.00',
  `_Weight` decimal(10,2) NOT NULL default '0.00',
  `_BirthDate` datetime default NULL,
  `_OnServiceDate` datetime default NULL,
  `_EndOfLifeDate` datetime default NULL,
  `_Owner` int(11) NOT NULL default '0',
  `_OnCondition` int(1) default '0',
  `_WarrantyBeginDate` datetime default NULL,
  `_WarrantyEndDate` datetime default NULL,
  `_BuyingPriceHT` decimal(10,2) NOT NULL default '0.00',
  `_SellingPriceHT` decimal(10,2) NOT NULL default '0.00',
  `_State` int(11) NOT NULL default '0',
  `_ConformityNumber` varchar(255) default NULL,
  `_FME` int(1) default '0',
  `_RealHourSinceNew` decimal(10,2) NOT NULL default '0.00',
  `_RealHourSinceOverall` decimal(10,2) NOT NULL default '0.00',
  `_RealHourSinceRepared` decimal(10,2) NOT NULL default '0.00',
  `_VirtualHourSinceNew` decimal(10,2) NOT NULL default '0.00',
  `_VirtualHourSinceOverall` decimal(10,2) NOT NULL default '0.00',
  `_Active` int(1) default '1',
  `_RealLandingSinceNew` decimal(10,2) NOT NULL default '0.00',
  `_RealLandingSinceOverall` decimal(10,2) NOT NULL default '0.00',
  `_RealLandingSinceRepared` decimal(10,2) NOT NULL default '0.00',
  `_RealCycleSinceNew` decimal(10,2) NOT NULL default '0.00',
  `_RealCycleSinceOverall` decimal(10,2) NOT NULL default '0.00',
  `_RealCycleSinceRepared` decimal(10,2) NOT NULL default '0.00',
  `_TankCapacity` decimal(10,2) default NULL,
  `_TankUnitType` int(11) default NULL,
  `_Product` int(11) NOT NULL default '0',
  `_WeeklyPlanning` int(11) NOT NULL default '0',
  `_ClassName` varchar(255) default NULL,
  `_Component` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Owner` (`_Owner`),
  KEY `_Product` (`_Product`),
  KEY `_WeeklyPlanning` (`_WeeklyPlanning`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Contact`
--

DROP TABLE IF EXISTS `Contact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Contact` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Phone` varchar(255) default NULL,
  `_Fax` varchar(255) default NULL,
  `_Mobile` varchar(255) default NULL,
  `_Email` varchar(255) default NULL,
  `_CommunicationModality` int(11) NOT NULL default '0',
  `_Role` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_CommunicationModality` (`_CommunicationModality`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ContactRole`
--

DROP TABLE IF EXISTS `ContactRole`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ContactRole` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Container`
--

DROP TABLE IF EXISTS `Container`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Container` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Reference` varchar(255) default NULL,
  `_SupplierReference` varchar(255) default NULL,
  `_CoverType` int(11) NOT NULL default '0',
  `_CoverKind` int(11) NOT NULL default '0',
  `_CoverProperty` int(11) NOT NULL default '0',
  `_CoverGroup` int(11) NOT NULL default '0',
  `_MaxAuthorizedWeight` float NOT NULL default '0',
  `_Weight` float NOT NULL default '0',
  `_ExternalLength` float NOT NULL default '0',
  `_ExternalWidth` float NOT NULL default '0',
  `_ExternalHeight` float NOT NULL default '0',
  `_InternalLength` float NOT NULL default '0',
  `_InternalWidth` float NOT NULL default '0',
  `_InternalHeight` float NOT NULL default '0',
  `_Volume` float NOT NULL default '0',
  `_RecipientWeight` float NOT NULL default '0',
  `_AssemblyKind` int(11) NOT NULL default '0',
  `_ExternalContainer` varchar(255) default NULL,
  `_InternalContainer` varchar(255) default NULL,
  `_Protection` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_CoverType` (`_CoverType`),
  KEY `_CoverKind` (`_CoverKind`),
  KEY `_CoverProperty` (`_CoverProperty`),
  KEY `_CoverGroup` (`_CoverGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CostRange`
--

DROP TABLE IF EXISTS `CostRange`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CostRange` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_CostType` int(11) NOT NULL default '0',
  `_Cost` decimal(10,3) default '0.000',
  `_BeginRange` decimal(10,2) default '0.00',
  `_EndRange` decimal(10,2) default '0.00',
  `_DepartureZone` int(11) NOT NULL default '0',
  `_ArrivalZone` int(11) NOT NULL default '0',
  `_Store` int(11) NOT NULL default '0',
  `_ProductType` int(11) NOT NULL default '0',
  `_Prestation` int(11) NOT NULL default '0',
  `_PrestationCost` int(11) NOT NULL default '0',
  `_UnitType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_DepartureZone` (`_DepartureZone`),
  KEY `_ArrivalZone` (`_ArrivalZone`),
  KEY `_UnitType` (`_UnitType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Country`
--

DROP TABLE IF EXISTS `Country`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Country` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` varchar(255) default NULL,
  `_InterCountryCode` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CountryCity`
--

DROP TABLE IF EXISTS `CountryCity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CountryCity` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Zip` int(11) NOT NULL default '0',
  `_Country` int(11) NOT NULL default '0',
  `_CityName` int(11) NOT NULL default '0',
  `_Zone` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Zip` (`_Zip`),
  KEY `_Country` (`_Country`),
  KEY `_CityName` (`_CityName`),
  KEY `_Zone` (`_Zone`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CoverType`
--

DROP TABLE IF EXISTS `CoverType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CoverType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_UnitType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_UnitType` (`_UnitType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CronTask`
--

DROP TABLE IF EXISTS `CronTask`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CronTask` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_ScriptName` varchar(255) default NULL,
  `_DayOfMonth` int(2) NOT NULL default '0',
  `_DayOfWeek` int(2) NOT NULL default '-1',
  `_HourOfDay` int(2) NOT NULL default '0',
  `_Active` int(1) default '1',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Currency`
--

DROP TABLE IF EXISTS `Currency`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Currency` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_ShortName` varchar(10) default NULL,
  `_Symbol` varchar(10) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CurrencyConverter`
--

DROP TABLE IF EXISTS `CurrencyConverter`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CurrencyConverter` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_FromCurrency` int(11) NOT NULL default '0',
  `_ToCurrency` int(11) NOT NULL default '0',
  `_BeginDate` date default NULL,
  `_EndDate` date default NULL,
  `_Rate` decimal(10,6) NOT NULL default '1.000000',
  PRIMARY KEY  (`_Id`),
  KEY `_FromCurrency` (`_FromCurrency`),
  KEY `_ToCurrency` (`_ToCurrency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CustomerAttractivity`
--

DROP TABLE IF EXISTS `CustomerAttractivity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CustomerAttractivity` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Level` int(11) NOT NULL default '1',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CustomerFrequency`
--

DROP TABLE IF EXISTS `CustomerFrequency`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CustomerFrequency` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Frequency` int(11) NOT NULL default '0',
  `_Type` int(3) NOT NULL default '1',
  `_BeginDate` date default NULL,
  `_EndDate` date default NULL,
  `_Attractivity` int(11) NOT NULL default '0',
  `_Potential` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Attractivity` (`_Attractivity`),
  KEY `_Potential` (`_Potential`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CustomerPotential`
--

DROP TABLE IF EXISTS `CustomerPotential`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CustomerPotential` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_MaxValue` float default NULL,
  `_MinValue` float default NULL,
  `_UnitType` int(3) NOT NULL default '1',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CustomerProperties`
--

DROP TABLE IF EXISTS `CustomerProperties`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CustomerProperties` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_NAFCode` varchar(255) default NULL,
  `_PriorityLevel` int(11) NOT NULL default '1',
  `_Potential` int(11) NOT NULL default '0',
  `_Situation` int(11) NOT NULL default '0',
  `_PersonalFrequency` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Potential` (`_Potential`),
  KEY `_Situation` (`_Situation`),
  KEY `_PersonalFrequency` (`_PersonalFrequency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `CustomerSituation`
--

DROP TABLE IF EXISTS `CustomerSituation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `CustomerSituation` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Type` int(3) NOT NULL default '1',
  `_InactivityDelay` int(11) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `DailyPlanning`
--

DROP TABLE IF EXISTS `DailyPlanning`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `DailyPlanning` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Start` time default NULL,
  `_Pause` time default NULL,
  `_Restart` time default NULL,
  `_End` time default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `DangerousProductGroup`
--

DROP TABLE IF EXISTS `DangerousProductGroup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `DangerousProductGroup` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Code` varchar(255) default NULL,
  `_Name` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `DangerousProductLetter`
--

DROP TABLE IF EXISTS `DangerousProductLetter`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `DangerousProductLetter` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Code` varchar(255) default NULL,
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `DangerousProductType`
--

DROP TABLE IF EXISTS `DangerousProductType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `DangerousProductType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Class` int(11) NOT NULL default '0',
  `_Letter` int(11) NOT NULL default '0',
  `_Group` int(11) NOT NULL default '0',
  `_Number` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Class` (`_Class`),
  KEY `_Letter` (`_Letter`),
  KEY `_Group` (`_Group`),
  KEY `_Number` (`_Number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Department`
--

DROP TABLE IF EXISTS `Department`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Department` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` varchar(255) default NULL,
  `_Number` varchar(255) default NULL,
  `_State` int(11) NOT NULL default '0',
  `_Country` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_State` (`_State`),
  KEY `_Country` (`_Country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `DocumentAppendix`
--

DROP TABLE IF EXISTS `DocumentAppendix`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `DocumentAppendix` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Code` varchar(255) default NULL,
  `_Title` varchar(255) default NULL,
  `_Body` text,
  `_Image` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_Code` (`_Code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `DocumentModel`
--

DROP TABLE IF EXISTS `DocumentModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `DocumentModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Footer` text,
  `_LogoType` int(11) default NULL,
  `_DocType` varchar(255) default NULL,
  `_Default` int(1) default '0',
  `_Actor` int(11) NOT NULL default '0',
  `_DisplayDuplicata` int(1) NOT NULL default '1',
  `_Number` int(2) NOT NULL default '1',
  `_DisplayTotalWeight` int(1) default '1',
  `_DisplayProductDetail` int(1) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Actor` (`_Actor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `DocumentModelProperty`
--

DROP TABLE IF EXISTS `DocumentModelProperty`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `DocumentModelProperty` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_PropertyType` int(2) default '0',
  `_Property` int(11) NOT NULL default '0',
  `_Order` int(11) NOT NULL default '0',
  `_DocumentModel` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Entity`
--

DROP TABLE IF EXISTS `Entity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Entity` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Public` int(1) default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ExecutedMovement`
--

DROP TABLE IF EXISTS `ExecutedMovement`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ExecutedMovement` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_StartDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_Type` int(11) NOT NULL default '0',
  `_State` int(11) NOT NULL default '0',
  `_Comment` varchar(255) default NULL,
  `_RealProduct` int(11) NOT NULL default '0',
  `_RealQuantity` decimal(10,3) NOT NULL default '0.000',
  `_ActivatedMovement` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Type` (`_Type`),
  KEY `_RealProduct` (`_RealProduct`),
  KEY `_ActivatedMovement` (`_ActivatedMovement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FW_Preferences`
--

DROP TABLE IF EXISTS `FW_Preferences`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FW_Preferences` (
  `name` varchar(80) NOT NULL default '',
  `dbid` int(11) default '0',
  `type` varchar(10) NOT NULL default 'string',
  `string_value` varchar(255) default NULL,
  `bool_value` int(1) default '0',
  `int_value` varchar(11) default '0',
  `float_value` decimal(10,4) default '0.0000',
  `array_value` longtext,
  `text_value` longtext,
  PRIMARY KEY  (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FW_UploadedFiles`
--

DROP TABLE IF EXISTS `FW_UploadedFiles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FW_UploadedFiles` (
  `_DBId` int(11) default NULL,
  `_DataB64` longtext,
  `_DataBLOB` longblob,
  `_Name` varchar(255) NOT NULL default '',
  `_UUID` varchar(255) default NULL,
  `_MimeType` varchar(255) default NULL,
  PRIMARY KEY  (`_Name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Flow`
--

DROP TABLE IF EXISTS `Flow`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Flow` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) NOT NULL default '',
  `_Number` varchar(255) NOT NULL default '',
  `_FlowType` int(11) NOT NULL default '0',
  `_TotalTTC` decimal(10,2) NOT NULL default '0.00',
  `_Paid` decimal(10,2) NOT NULL default '0.00',
  `_Currency` int(11) NOT NULL default '0',
  `_PaymentDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `_EditionDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `_ActorBankDetail` int(11) NOT NULL default '0',
  `_Handing` varchar(10) default '0',
  `_PieceNo` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_FlowType` (`_FlowType`),
  KEY `_Currency` (`_Currency`),
  KEY `_ActorBankDetail` (`_ActorBankDetail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FlowCategory`
--

DROP TABLE IF EXISTS `FlowCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FlowCategory` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Parent` int(11) NOT NULL default '0',
  `_DisplayOrder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Parent` (`_Parent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FlowItem`
--

DROP TABLE IF EXISTS `FlowItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FlowItem` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Type` int(11) NOT NULL default '0',
  `_TotalHT` decimal(10,2) NOT NULL default '0.00',
  `_TVA` int(11) default NULL,
  `_Flow` int(11) NOT NULL default '0',
  `_FlowType` int(11) NOT NULL default '0',
  `_Handing` varchar(10) default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FlowType`
--

DROP TABLE IF EXISTS `FlowType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FlowType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Type` int(3) NOT NULL default '0',
  `_Name` varchar(255) NOT NULL default '',
  `_ActorBankDetail` int(11) NOT NULL default '0',
  `_InvoiceType` int(1) default '0',
  `_FlowCategory` int(11) NOT NULL default '0',
  `_AccountingType` int(11) NOT NULL default '0',
  `_ThirdParty` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ActorBankDetail` (`_ActorBankDetail`),
  KEY `_FlowCategory` (`_FlowCategory`),
  KEY `_AccountingType` (`_AccountingType`),
  KEY `_ThirdParty` (`_ThirdParty`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FlowTypeItem`
--

DROP TABLE IF EXISTS `FlowTypeItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FlowTypeItem` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) NOT NULL default '',
  `_TVA` int(11) default NULL,
  `_FlowType` int(11) NOT NULL default '0',
  `_BreakdownPart` int(1) default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FlyType`
--

DROP TABLE IF EXISTS `FlyType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FlyType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_Name` (`_Name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ForecastFlow`
--

DROP TABLE IF EXISTS `ForecastFlow`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ForecastFlow` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Description` varchar(255) NOT NULL default '',
  `_BeginDate` date default NULL,
  `_EndDate` date default NULL,
  `_Amount` decimal(10,2) default NULL,
  `_Currency` int(11) NOT NULL default '0',
  `_Active` int(1) NOT NULL default '1',
  PRIMARY KEY  (`_Id`),
  KEY `_Currency` (`_Currency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ForecastFlowFlowTypeItem`
--

DROP TABLE IF EXISTS `ForecastFlowFlowTypeItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ForecastFlowFlowTypeItem` (
  `_FromForecastFlow` int(11) unsigned NOT NULL default '0',
  `_ToFlowTypeItem` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromForecastFlow`,`_ToFlowTypeItem`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FormModel`
--

DROP TABLE IF EXISTS `FormModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FormModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Activ` int(1) default '1',
  `_ActionType` int(3) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ForwardingFormPacking`
--

DROP TABLE IF EXISTS `ForwardingFormPacking`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ForwardingFormPacking` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ForwardingForm` int(11) NOT NULL default '0',
  `_CoverType` int(11) NOT NULL default '0',
  `_Quantity` int(11) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ForwardingForm` (`_ForwardingForm`),
  KEY `_CoverType` (`_CoverType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FromInvoiceItemToACO`
--

DROP TABLE IF EXISTS `FromInvoiceItemToACO`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FromInvoiceItemToACO` (
  `_FromInvoiceItem` int(11) unsigned NOT NULL default '0',
  `_ToACO` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromInvoiceItem`,`_ToACO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `HandingByRange`
--

DROP TABLE IF EXISTS `HandingByRange`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `HandingByRange` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Percent` decimal(10,2) NOT NULL default '0.00',
  `_Minimum` decimal(10,2) NOT NULL default '0.00',
  `_Maximum` decimal(10,2) NOT NULL default '0.00',
  `_Currency` int(11) NOT NULL default '0',
  `_Category` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Currency` (`_Currency`),
  KEY `_Category` (`_Category`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `HelpPage`
--

DROP TABLE IF EXISTS `HelpPage`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `HelpPage` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_FileName` varchar(255) default NULL,
  `_Body` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `I18nString`
--

DROP TABLE IF EXISTS `I18nString`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `I18nString` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_StringValue_en_GB` text,
  `_StringValue_fr_FR` text,
  `_StringValue_de_DE` text,
  `_StringValue_nl_NL` text,
  `_StringValue_tr_TR` text,
  `_StringValue_pl_PL` text,
  `_StringValue_es_ES` text,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `IdHashTable`
--

DROP TABLE IF EXISTS `IdHashTable`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `IdHashTable` (
  `_Table` varchar(255) default NULL,
  `_Id` int(11) NOT NULL default '0',
  UNIQUE KEY `_Table` (`_Table`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Incoterm`
--

DROP TABLE IF EXISTS `Incoterm`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Incoterm` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Code` varchar(5) default NULL,
  `_Label` varchar(255) default NULL,
  `_Description` int(11) NOT NULL default '0',
  `_TransportType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Inventory`
--

DROP TABLE IF EXISTS `Inventory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Inventory` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_BeginDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_UserAccount` int(11) NOT NULL default '0',
  `_UserName` varchar(255) default NULL,
  `_StorageSite` int(11) NOT NULL default '0',
  `_StorageSiteName` varchar(255) default NULL,
  `_Store` int(11) NOT NULL default '0',
  `_StoreName` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_UserAccount` (`_UserAccount`),
  KEY `_StorageSite` (`_StorageSite`),
  KEY `_Store` (`_Store`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `InventoryDetail`
--

DROP TABLE IF EXISTS `InventoryDetail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `InventoryDetail` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Product` int(11) NOT NULL default '0',
  `_ProductReference` varchar(255) default NULL,
  `_Currency` varchar(10) default NULL,
  `_BuyingPriceHT` decimal(10,2) default NULL,
  `_Location` int(11) NOT NULL default '0',
  `_LocationName` varchar(255) default NULL,
  `_Quantity` decimal(10,3) default NULL,
  `_Inventory` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Product` (`_Product`),
  KEY `_Location` (`_Location`),
  KEY `_Inventory` (`_Inventory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `InvoiceItem`
--

DROP TABLE IF EXISTS `InvoiceItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `InvoiceItem` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Reference` varchar(255) default NULL,
  `_AssociatedReference` varchar(255) default NULL,
  `_Handing` varchar(255) default NULL,
  `_Quantity` decimal(10,3) NOT NULL default '0.000',
  `_TVA` int(11) NOT NULL default '0',
  `_UnitPriceHT` decimal(10,2) NOT NULL default '0.00',
  `_PrestationCost` decimal(10,3) NOT NULL default '0.000',
  `_Invoice` int(11) NOT NULL default '0',
  `_ActivatedMovement` int(11) NOT NULL default '0',
  `_Prestation` int(11) NOT NULL default '0',
  `_PrestationPeriodicity` int(1) NOT NULL default '0',
  `_CostType` int(2) NOT NULL default '-1',
  `_QuantityForPrestationCost` decimal(10,3) default '0.000',
  `_ProductType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Invoice` (`_Invoice`),
  KEY `_ActivatedMovement` (`_ActivatedMovement`),
  KEY `_Prestation` (`_Prestation`),
  KEY `_TVA` (`_TVA`),
  KEY `_ProductType` (`_ProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `InvoicePayment`
--

DROP TABLE IF EXISTS `InvoicePayment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `InvoicePayment` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_PriceTTC` decimal(10,2) NOT NULL default '0.00',
  `_Invoice` int(11) NOT NULL default '0',
  `_Payment` int(11) NOT NULL default '0',
  `_ToHave` int(11) default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Invoice` (`_Invoice`),
  KEY `_Payment` (`_Payment`),
  KEY `_ToHave` (`_ToHave`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Job`
--

DROP TABLE IF EXISTS `Job`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Job` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LEMConcreteProduct`
--

DROP TABLE IF EXISTS `LEMConcreteProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LEMConcreteProduct` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_LocationExecutedMovement` int(11) NOT NULL default '0',
  `_ConcreteProduct` int(11) NOT NULL default '0',
  `_Quantity` decimal(10,3) NOT NULL default '0.000',
  `_Cancelled` int(11) default '0',
  `_CancelledLEMConcreteProduct` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_LocationExecutedMovement` (`_LocationExecutedMovement`),
  KEY `_ConcreteProduct` (`_ConcreteProduct`),
  KEY `_CancelledLEMConcreteProduct` (`_CancelledLEMConcreteProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Language`
--

DROP TABLE IF EXISTS `Language`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Language` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_Name` varchar(255) default 'Français',
  `_Code` varchar(10) default 'fr_FR',
  `_ShortCode` char(2) default 'fr',
  `_Encoding` varchar(20) default 'ISO-8859-1',
  `_DateFormat` varchar(10) default 'dMY',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Licence`
--

DROP TABLE IF EXISTS `Licence`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Licence` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Number` varchar(255) default NULL,
  `_BeginDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_Duration` int(3) NOT NULL default '0',
  `_DurationType` int(1) NOT NULL default '0',
  `_AlertDateType` int(11) NOT NULL default '0',
  `_DelayForAlert` int(3) NOT NULL default '0',
  `_ToBeChecked` int(3) NOT NULL default '0',
  `_LicenceType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_LicenceType` (`_LicenceType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LicenceType`
--

DROP TABLE IF EXISTS `LicenceType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LicenceType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LinkFormModelParagraphModel`
--

DROP TABLE IF EXISTS `LinkFormModelParagraphModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LinkFormModelParagraphModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ParagraphOrder` int(11) default NULL,
  `_ParagraphModel` int(11) NOT NULL default '0',
  `_FormModel` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ParagraphModel` (`_ParagraphModel`),
  KEY `_FormModel` (`_FormModel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LinkParagraphModelQuestion`
--

DROP TABLE IF EXISTS `LinkParagraphModelQuestion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LinkParagraphModelQuestion` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_QuestionOrder` int(11) default NULL,
  `_ParagraphModel` int(11) NOT NULL default '0',
  `_Question` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ParagraphModel` (`_ParagraphModel`),
  KEY `_Question` (`_Question`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LinkQuestionAnswerModel`
--

DROP TABLE IF EXISTS `LinkQuestionAnswerModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LinkQuestionAnswerModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_AnswerOrder` int(11) default NULL,
  `_AnswerModel` int(11) NOT NULL default '0',
  `_Question` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_AnswerModel` (`_AnswerModel`),
  KEY `_Question` (`_Question`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Location`
--

DROP TABLE IF EXISTS `Location`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Location` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Customs` int(1) default '0',
  `_Store` int(11) NOT NULL default '0',
  `_Activated` int(1) default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Store` (`_Store`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LocationConcreteProduct`
--

DROP TABLE IF EXISTS `LocationConcreteProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LocationConcreteProduct` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ConcreteProduct` int(11) NOT NULL default '0',
  `_Location` int(11) NOT NULL default '0',
  `_Quantity` decimal(10,3) NOT NULL default '0.000',
  PRIMARY KEY  (`_Id`),
  KEY `_ConcreteProduct` (`_ConcreteProduct`),
  KEY `_Location` (`_Location`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LocationExecutedMovement`
--

DROP TABLE IF EXISTS `LocationExecutedMovement`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LocationExecutedMovement` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Quantity` decimal(10,3) NOT NULL default '0.000',
  `_isFactured` int(1) NOT NULL default '0',
  `_Date` datetime default NULL,
  `_ExecutedMovement` int(11) NOT NULL default '0',
  `_Location` int(11) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  `_Cancelled` int(1) default '0',
  `_CancelledMovement` int(11) NOT NULL default '0',
  `_PrestationFactured` int(1) NOT NULL default '0',
  `_TransportPrestationFactured` int(1) NOT NULL default '0',
  `_ForwardingForm` int(11) default NULL,
  `_InvoiceItem` int(11) NOT NULL default '0',
  `_InvoicePrestation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ExecutedMovement` (`_ExecutedMovement`),
  KEY `_Location` (`_Location`),
  KEY `_Product` (`_Product`),
  KEY `_CancelledMovement` (`_CancelledMovement`),
  KEY `_ForwardingForm` (`_ForwardingForm`),
  KEY `_InvoiceItem` (`_InvoiceItem`),
  KEY `_InvoicePrestation` (`_InvoicePrestation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `LocationProductQuantities`
--

DROP TABLE IF EXISTS `LocationProductQuantities`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `LocationProductQuantities` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Product` int(11) NOT NULL default '0',
  `_Location` int(11) NOT NULL default '0',
  `_RealQuantity` decimal(10,3) NOT NULL default '0.000',
  `_Activated` int(1) default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Product` (`_Product`),
  KEY `_Location` (`_Location`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Manual`
--

DROP TABLE IF EXISTS `Manual`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Manual` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_FrFile` varchar(255) default NULL,
  `_EnFile` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `MimeType`
--

DROP TABLE IF EXISTS `MimeType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `MimeType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Extension` varchar(255) default NULL,
  `_ContentType` varchar(255) default NULL,
  `_DisplayName` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `MiniAmountToOrder`
--

DROP TABLE IF EXISTS `MiniAmountToOrder`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `MiniAmountToOrder` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Amount` decimal(10,2) NOT NULL default '0.00',
  `_Category` int(11) NOT NULL default '0',
  `_Currency` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Category` (`_Category`),
  KEY `_Currency` (`_Currency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `MovementType`
--

DROP TABLE IF EXISTS `MovementType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `MovementType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_Foreseeable` int(1) default '0',
  `_EntrieExit` int(3) NOT NULL default '0',
  `_ConstName` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Nomenclature`
--

DROP TABLE IF EXISTS `Nomenclature`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Nomenclature` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Version` varchar(255) default NULL,
  `_BeginDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_Buildable` int(1) NOT NULL default '1',
  `_Product` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Product` (`_Product`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `OccupiedLocation`
--

DROP TABLE IF EXISTS `OccupiedLocation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `OccupiedLocation` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_CreationDate` date default NULL,
  `_ValidityDate` date default NULL,
  `_Location` int(11) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  `_InvoiceItem` int(11) NOT NULL default '0',
  `_Quantity` decimal(10,3) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_InvoiceItem` (`_InvoiceItem`),
  KEY `_Location` (`_Location`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Operation`
--

DROP TABLE IF EXISTS `Operation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Operation` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_Symbol` varchar(255) default NULL,
  `_FrontTolerance` time default NULL,
  `_EndTolerance` time default NULL,
  `_TotalTolerance` time default NULL,
  `_IsConcreteProductNeeded` int(1) default '0',
  `_Prestation` int(11) NOT NULL default '0',
  `_Type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_Symbol` (`_Symbol`),
  KEY `_Prestation` (`_Prestation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `PDFDocument`
--

DROP TABLE IF EXISTS `PDFDocument`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `PDFDocument` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Data` longtext,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ParagraphModel`
--

DROP TABLE IF EXISTS `ParagraphModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ParagraphModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Title` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Payment`
--

DROP TABLE IF EXISTS `Payment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Payment` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Date` datetime default NULL,
  `_Modality` int(11) NOT NULL default '0',
  `_Reference` varchar(255) default NULL,
  `_TotalPriceTTC` decimal(10,2) NOT NULL default '0.00',
  `_CancellationDate` datetime default NULL,
  `_ActorBankDetail` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ActorBankDetail` (`_ActorBankDetail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Preference`
--

DROP TABLE IF EXISTS `Preference`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Preference` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_SupQuantityAuthorized` int(1) default '0',
  `_WSActorGenericActor` int(11) default '0',
  `_WSActorCommercial` int(11) default '0',
  `_WSActorCategory` int(11) default '0',
  `_WSActorAccountingType` int(11) default '0',
  `_CommandActivateMultipleChains` int(1) default '0',
  `_MercurialForClient` int(1) default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Prestation`
--

DROP TABLE IF EXISTS `Prestation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Prestation` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Type` int(11) NOT NULL default '0',
  `_Potential` decimal(10,2) NOT NULL default '0.00',
  `_PotentialDate` datetime default NULL,
  `_Tolerance` int(11) NOT NULL default '0',
  `_ToleranceType` int(11) NOT NULL default '0',
  `_Comment` text,
  `_TVA` int(11) NOT NULL default '0',
  `_Facturable` int(1) default '0',
  `_Active` int(1) default '0',
  `_Operation` int(11) NOT NULL default '0',
  `_FreePeriod` int(5) NOT NULL default '1',
  `_Periodicity` int(1) NOT NULL default '1',
  PRIMARY KEY  (`_Id`),
  KEY `_TVA` (`_TVA`),
  KEY `_Operation` (`_Operation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `PrestationCost`
--

DROP TABLE IF EXISTS `PrestationCost`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `PrestationCost` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_Prestation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Prestation` (`_Prestation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `PrestationCustomer`
--

DROP TABLE IF EXISTS `PrestationCustomer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `PrestationCustomer` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Prestation` int(11) NOT NULL default '0',
  `_Actor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Prestation` (`_Prestation`),
  KEY `_Actor` (`_Actor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `PriceByCurrency`
--

DROP TABLE IF EXISTS `PriceByCurrency`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `PriceByCurrency` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_RecommendedPrice` decimal(10,2) NOT NULL default '0.00',
  `_Price` decimal(10,2) NOT NULL default '0.00',
  `_Currency` int(11) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  `_ActorProduct` int(11) NOT NULL default '0',
  `_PricingZone` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Currency` (`_Currency`),
  KEY `_Product` (`_Product`),
  KEY `_ActorProduct` (`_ActorProduct`),
  KEY `_PricingZone` (`_PricingZone`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `PricingZone`
--

DROP TABLE IF EXISTS `PricingZone`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `PricingZone` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Product`
--

DROP TABLE IF EXISTS `Product`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Product` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_BaseReference` varchar(255) default NULL,
  `_Volume` float NOT NULL default '0',
  `_CustomsNaming` varchar(255) default NULL,
  `_Category` int(11) NOT NULL default '0',
  `_Activated` int(1) default '1',
  `_Affected` int(1) default '0',
  `_ProductType` int(11) NOT NULL default '0',
  `_TracingMode` int(3) NOT NULL default '0',
  `_TracingModeBeginRange` int(11) default NULL,
  `_TracingModeEndRange` int(11) default NULL,
  `_SellUnitType` int(11) NOT NULL default '0',
  `_FirstRankParcelNumber` int(11) NOT NULL default '0',
  `_SellUnitQuantity` decimal(10,3) NOT NULL default '0.000',
  `_SellUnitVirtualQuantity` decimal(10,3) NOT NULL default '0.000',
  `_SellUnitMinimumStoredQuantity` decimal(10,3) NOT NULL default '0.000',
  `_SellUnitLength` float NOT NULL default '0',
  `_SellUnitWidth` float NOT NULL default '0',
  `_SellUnitHeight` float NOT NULL default '0',
  `_SellUnitWeight` float NOT NULL default '0',
  `_SellUnitMasterDimension` int(11) NOT NULL default '0',
  `_SellUnitGerbability` int(11) NOT NULL default '0',
  `_SellUnitTypeInContainer` int(11) NOT NULL default '0',
  `_Turnable` int(11) default NULL,
  `_TVA` int(11) NOT NULL default '0',
  `_ConditioningRecommended` int(11) NOT NULL default '0',
  `_UnitNumberInConditioning` int(11) NOT NULL default '0',
  `_ConditionedProductReference` varchar(255) default NULL,
  `_ConditioningGerbability` int(11) NOT NULL default '0',
  `_ConditioningMasterDimension` int(11) NOT NULL default '0',
  `_PackagingRecommended` int(11) NOT NULL default '0',
  `_UnitNumberInPackaging` int(11) NOT NULL default '0',
  `_PackagedProductReference` varchar(255) default NULL,
  `_PackagingGerbability` int(11) NOT NULL default '0',
  `_PackagingMasterDimension` int(11) NOT NULL default '0',
  `_GroupingRecommended` int(11) NOT NULL default '0',
  `_UnitNumberInGrouping` int(11) NOT NULL default '0',
  `_GroupedProductReference` varchar(255) default NULL,
  `_GroupingGerbability` int(11) NOT NULL default '0',
  `_GroupingMasterDimension` int(11) NOT NULL default '0',
  `_LicenceName` varchar(255) default NULL,
  `_LicenceYear` int(4) default NULL,
  `_Description` varchar(255) default NULL,
  `_Image` varchar(255) default NULL,
  `_Owner` int(11) NOT NULL default '0',
  `_ProductModel` int(11) NOT NULL default '0',
  `_Model` int(11) NOT NULL default '0',
  `_Size` int(11) NOT NULL default '0',
  `_ScientificName` varchar(255) default NULL,
  `_MaterialType` int(3) NOT NULL default '0',
  `_Origin` varchar(255) default NULL,
  `_Color` int(11) NOT NULL default '0',
  `_FlyType` int(11) NOT NULL default '0',
  `_LastModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_BaseReference` (`_BaseReference`),
  KEY `_ProductType` (`_ProductType`),
  KEY `_SellUnitType` (`_SellUnitType`),
  KEY `_SellUnitTypeInContainer` (`_SellUnitTypeInContainer`),
  KEY `_TVA` (`_TVA`),
  KEY `_ConditioningRecommended` (`_ConditioningRecommended`),
  KEY `_PackagingRecommended` (`_PackagingRecommended`),
  KEY `_GroupingRecommended` (`_GroupingRecommended`),
  KEY `_Owner` (`_Owner`),
  KEY `_Model` (`_Model`),
  KEY `_Color` (`_Color`),
  KEY `_FlyType` (`_FlyType`),
  KEY `_ProductModel` (`_ProductModel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ProductChainLink`
--

DROP TABLE IF EXISTS `ProductChainLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ProductChainLink` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Product` int(11) NOT NULL default '0',
  `_Chain` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Product` (`_Product`),
  KEY `_Chain` (`_Chain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ProductHandingByCategory`
--

DROP TABLE IF EXISTS `ProductHandingByCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ProductHandingByCategory` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_UpdateDate` datetime default NULL,
  `_Handing` decimal(10,2) NOT NULL default '0.00',
  `_Product` int(11) NOT NULL default '0',
  `_Currency` int(11) default '0',
  `_Type` int(1) default '1',
  PRIMARY KEY  (`_Id`),
  KEY `_Product` (`_Product`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ProductKind`
--

DROP TABLE IF EXISTS `ProductKind`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ProductKind` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` int(11) NOT NULL default '0',
  `_ProductType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_ProductType` (`_ProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ProductModel`
--

DROP TABLE IF EXISTS `ProductModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ProductModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_BaseReference` varchar(255) default NULL,
  `_ProductType` int(11) NOT NULL default '0',
  `_Owner` int(11) NOT NULL default '0',
  `_Manufacturer` int(11) NOT NULL default '0',
  `_TVA` int(11) NOT NULL default '0',
  `_Description` text,
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_BaseReference` (`_BaseReference`),
  KEY `_ProductType` (`_ProductType`),
  KEY `_Owner` (`_Owner`),
  KEY `_Manufacturer` (`_Manufacturer`),
  KEY `_TVA` (`_TVA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ProductQuantityByCategory`
--

DROP TABLE IF EXISTS `ProductQuantityByCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ProductQuantityByCategory` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_MinimumQuantity` decimal(10,3) default NULL,
  `_MinimumQuantityType` int(1) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  `_Category` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_ProductCategory` (`_Product`,`_Category`),
  KEY `_Product` (`_Product`),
  KEY `_Category` (`_Category`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ProductSubstitution`
--

DROP TABLE IF EXISTS `ProductSubstitution`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ProductSubstitution` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_FromProduct` int(11) NOT NULL default '0',
  `_ByProduct` int(11) NOT NULL default '0',
  `_Interchangeable` int(1) default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_FromProduct` (`_FromProduct`),
  KEY `_ByProduct` (`_ByProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ProductType`
--

DROP TABLE IF EXISTS `ProductType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ProductType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` varchar(255) default NULL,
  `_GenericProductType` int(11) NOT NULL default '0',
  `_Generic` int(1) default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_GenericProductType` (`_GenericProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Promotion`
--

DROP TABLE IF EXISTS `Promotion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Promotion` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_StartDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_Rate` decimal(10,2) NOT NULL default '0.00',
  `_Type` int(11) NOT NULL default '0',
  `_ApproImpactRate` decimal(10,2) NOT NULL default '0.00',
  `_Currency` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Currency` (`_Currency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Property`
--

DROP TABLE IF EXISTS `Property`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Property` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` varchar(255) default NULL,
  `_DisplayName` varchar(255) default NULL,
  `_Type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `PropertyValue`
--

DROP TABLE IF EXISTS `PropertyValue`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `PropertyValue` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Product` int(11) NOT NULL default '0',
  `_Property` int(11) NOT NULL default '0',
  `_StringValue` varchar(255) default NULL,
  `_IntValue` int(11) NOT NULL default '0',
  `_FloatValue` float NOT NULL default '0',
  `_DateValue` datetime default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_Product` (`_Product`),
  KEY `_Property` (`_Property`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Question`
--

DROP TABLE IF EXISTS `Question`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Question` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Text` varchar(255) default NULL,
  `_Theme` int(11) NOT NULL default '0',
  `_AnswerType` int(3) NOT NULL default '0',
  `_Alert` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Theme` (`_Theme`),
  KEY `_Alert` (`_Alert`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RTWElement`
--

DROP TABLE IF EXISTS `RTWElement`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RTWElement` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_SupplierReference` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RTWModel`
--

DROP TABLE IF EXISTS `RTWModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RTWModel` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Season` int(11) NOT NULL default '0',
  `_Shape` int(11) NOT NULL default '0',
  `_PressName` int(11) NOT NULL default '0',
  `_StyleNumber` varchar(255) default NULL,
  `_ConstructionType` int(11) NOT NULL default '0',
  `_ConstructionCode` int(11) NOT NULL default '0',
  `_Description` varchar(255) default NULL,
  `_Manufacturer` int(11) NOT NULL default '0',
  `_Label` int(11) NOT NULL default '0',
  `_HeelHeight` int(11) NOT NULL default '0',
  `_HeelReference` int(11) NOT NULL default '0',
  `_HeelReferenceQuantity` decimal(10,3) default NULL,
  `_HeelReferenceNomenclature` int(1) default '1',
  `_Sole` int(11) NOT NULL default '0',
  `_SoleQuantity` decimal(10,3) default NULL,
  `_SoleNomenclature` int(1) default '1',
  `_Box` int(11) NOT NULL default '0',
  `_BoxQuantity` decimal(10,3) default NULL,
  `_BoxNomenclature` int(1) default '1',
  `_HandBag` int(11) NOT NULL default '0',
  `_HandBagQuantity` decimal(10,3) default NULL,
  `_HandBagNomenclature` int(1) default '1',
  `_Material1` int(11) NOT NULL default '0',
  `_Material1Quantity` decimal(10,3) default NULL,
  `_Material1Nomenclature` int(1) default '1',
  `_Material2` int(11) NOT NULL default '0',
  `_Material2Quantity` decimal(10,3) default NULL,
  `_Material2Nomenclature` int(1) default '1',
  `_Material3` int(11) NOT NULL default '0',
  `_Material3Quantity` decimal(10,3) default NULL,
  `_Material3Nomenclature` int(1) default '1',
  `_Accessory1` int(11) NOT NULL default '0',
  `_Accessory1Quantity` decimal(10,3) default NULL,
  `_Accessory1Nomenclature` int(1) default '1',
  `_Accessory2` int(11) NOT NULL default '0',
  `_Accessory2Quantity` decimal(10,3) default NULL,
  `_Accessory2Nomenclature` int(1) default '1',
  `_Accessory3` int(11) NOT NULL default '0',
  `_Accessory3Quantity` decimal(10,3) default NULL,
  `_Accessory3Nomenclature` int(1) default '1',
  `_Lining` int(11) NOT NULL default '0',
  `_LiningQuantity` decimal(10,3) default NULL,
  `_LiningNomenclature` int(1) default '1',
  `_Insole` int(11) NOT NULL default '0',
  `_InsoleQuantity` decimal(10,3) default NULL,
  `_InsoleNomenclature` int(1) default '1',
  `_UnderSole` int(11) NOT NULL default '0',
  `_UnderSoleQuantity` decimal(10,3) default NULL,
  `_UnderSoleNomenclature` int(1) default '1',
  `_MediaPlanta` int(11) NOT NULL default '0',
  `_MediaPlantaQuantity` decimal(10,3) default NULL,
  `_MediaPlantaNomenclature` int(1) default '1',
  `_Lagrima` int(11) NOT NULL default '0',
  `_LagrimaQuantity` decimal(10,3) default NULL,
  `_LagrimaNomenclature` int(1) default '1',
  `_HeelCovering` int(11) NOT NULL default '0',
  `_HeelCoveringQuantity` decimal(10,3) default NULL,
  `_HeelCoveringNomenclature` int(1) default '1',
  `_Selvedge` int(11) NOT NULL default '0',
  `_SelvedgeQuantity` decimal(10,3) default NULL,
  `_SelvedgeNomenclature` int(1) default '1',
  `_Thread1` int(11) NOT NULL default '0',
  `_Thread2` int(11) NOT NULL default '0',
  `_Bamboo` int(11) NOT NULL default '0',
  `_BambooQuantity` decimal(10,3) default NULL,
  `_BambooNomenclature` int(1) default '1',
  `_Image` varchar(255) default NULL,
  `_ColorImage` varchar(255) default NULL,
  `_Comment` text,
  PRIMARY KEY  (`_Id`),
  KEY `_Season` (`_Season`),
  KEY `_Shape` (`_Shape`),
  KEY `_PressName` (`_PressName`),
  KEY `_ConstructionType` (`_ConstructionType`),
  KEY `_ConstructionCode` (`_ConstructionCode`),
  KEY `_Manufacturer` (`_Manufacturer`),
  KEY `_Label` (`_Label`),
  KEY `_HeelHeight` (`_HeelHeight`),
  KEY `_HeelReference` (`_HeelReference`),
  KEY `_Sole` (`_Sole`),
  KEY `_Box` (`_Box`),
  KEY `_HandBag` (`_HandBag`),
  KEY `_Material1` (`_Material1`),
  KEY `_Material2` (`_Material2`),
  KEY `_Accessory1` (`_Accessory1`),
  KEY `_Accessory2` (`_Accessory2`),
  KEY `_Lining` (`_Lining`),
  KEY `_Insole` (`_Insole`),
  KEY `_UnderSole` (`_UnderSole`),
  KEY `_Lagrima` (`_Lagrima`),
  KEY `_HeelCovering` (`_HeelCovering`),
  KEY `_Selvedge` (`_Selvedge`),
  KEY `_Thread1` (`_Thread1`),
  KEY `_Thread2` (`_Thread2`),
  KEY `_Bamboo` (`_Bamboo`),
  KEY `_Material3` (`_Material3`),
  KEY `_Accessory3` (`_Accessory3`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RTWOption`
--

DROP TABLE IF EXISTS `RTWOption`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RTWOption` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_OptionType` int(11) NOT NULL default '0',
  `_Name` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_OptionType` (`_OptionType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Rating`
--

DROP TABLE IF EXISTS `Rating`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Rating` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_BeginDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_Duration` int(3) NOT NULL default '0',
  `_DurationType` int(1) NOT NULL default '0',
  `_Type` int(11) NOT NULL default '0',
  `_FlyType` int(11) NOT NULL default '0',
  `_Licence` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Type` (`_Type`),
  KEY `_FlyType` (`_FlyType`),
  KEY `_FlyLicence` (`_Licence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RatingType`
--

DROP TABLE IF EXISTS `RatingType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RatingType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RealAnswer`
--

DROP TABLE IF EXISTS `RealAnswer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RealAnswer` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_AnswerModel` int(11) NOT NULL default '0',
  `_Question` int(11) NOT NULL default '0',
  `_Action` int(11) NOT NULL default '0',
  `_Value` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_AnswerModel` (`_AnswerModel`),
  KEY `_Question` (`_Question`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RealBox`
--

DROP TABLE IF EXISTS `RealBox`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RealBox` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Grouping` int(11) NOT NULL default '0',
  `_ActivatedChainTask` int(11) NOT NULL default '0',
  `_PN` varchar(255) default NULL,
  `_SN` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_Grouping` (`_Grouping`),
  KEY `_ActivatedChainTask` (`_ActivatedChainTask`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Ressource`
--

DROP TABLE IF EXISTS `Ressource`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Ressource` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Type` int(3) NOT NULL default '0',
  `_Cost` decimal(10,2) default NULL,
  `_Quantity` decimal(10,2) NOT NULL default '0.00',
  `_CostType` int(3) NOT NULL default '0',
  `_Product` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Product` (`_Product`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RessourceGroup`
--

DROP TABLE IF EXISTS `RessourceGroup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RessourceGroup` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Active` int(1) default '1',
  `_AddNomenclatureCosts` int(1) default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RessourceRessourceGroup`
--

DROP TABLE IF EXISTS `RessourceRessourceGroup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RessourceRessourceGroup` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Ressource` int(11) NOT NULL default '0',
  `_RessourceGroup` int(11) NOT NULL default '0',
  `_Rate` decimal(10,2) default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_Ressource` (`_Ressource`),
  KEY `_RessourceGroup` (`_RessourceGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Saisonality`
--

DROP TABLE IF EXISTS `Saisonality`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Saisonality` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_StartDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_Rate` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `SellUnitType`
--

DROP TABLE IF EXISTS `SellUnitType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SellUnitType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_ShortName` int(11) NOT NULL default '0',
  `_LongName` int(11) NOT NULL default '0',
  `_ConstName` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Site`
--

DROP TABLE IF EXISTS `Site`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Site` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_ClassName` varchar(255) default NULL,
  `_Name` varchar(255) default NULL,
  `_Email` varchar(255) default NULL,
  `_Fax` varchar(255) default NULL,
  `_Phone` varchar(255) default NULL,
  `_Mobile` varchar(255) default NULL,
  `_PreferedCommunicationMode` int(11) default NULL,
  `_StreetNumber` varchar(255) default NULL,
  `_StreetType` int(3) NOT NULL default '0',
  `_StreetName` varchar(255) default NULL,
  `_StreetAddons` varchar(255) default NULL,
  `_Cedex` varchar(255) default NULL,
  `_GPS` varchar(255) default NULL,
  `_CountryCity` int(11) NOT NULL default '0',
  `_Zone` int(11) NOT NULL default '0',
  `_Planning` int(11) NOT NULL default '0',
  `_CommunicationModality` int(11) NOT NULL default '0',
  `_Owner` int(11) NOT NULL default '0',
  `_Type` int(11) NOT NULL default '0',
  `_Customs` int(1) default '0',
  `_StockOwner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Planning` (`_Planning`),
  KEY `_CommunicationModality` (`_CommunicationModality`),
  KEY `_Owner` (`_Owner`),
  KEY `_StockOwner` (`_StockOwner`),
  KEY `_Zone` (`_Zone`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `SpreadSheet`
--

DROP TABLE IF EXISTS `SpreadSheet`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SpreadSheet` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` int(11) NOT NULL default '0',
  `_Active` int(1) default '1',
  `_LastModified` datetime default NULL,
  `_Entity` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `SpreadSheetColumn`
--

DROP TABLE IF EXISTS `SpreadSheetColumn`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SpreadSheetColumn` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_PropertyName` varchar(50) default NULL,
  `_FkeyPropertyName` varchar(50) default NULL,
  `_PropertyType` int(3) NOT NULL default '0',
  `_PropertyClass` varchar(50) default NULL,
  `_Order` int(3) NOT NULL default '0',
  `_Comment` text,
  `_Default` varchar(255) default NULL,
  `_Width` int(5) NOT NULL default '0',
  `_Required` int(1) default '0',
  `_SpreadSheet` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_SpreadSheet` (`_SpreadSheet`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `State`
--

DROP TABLE IF EXISTS `State`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `State` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` varchar(255) default NULL,
  `_Number` int(11) default NULL,
  `_Country` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Country` (`_Country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Store`
--

DROP TABLE IF EXISTS `Store`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Store` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Customs` int(1) default '0',
  `_Name` varchar(255) default NULL,
  `_Activated` int(1) NOT NULL default '1',
  `_StockOwner` int(11) NOT NULL default '0',
  `_StorageSite` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_StockOwner` (`_StockOwner`),
  KEY `_StorageSite` (`_StorageSite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `SupplierCustomer`
--

DROP TABLE IF EXISTS `SupplierCustomer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SupplierCustomer` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_MaxIncur` decimal(10,2) default NULL,
  `_UpdateIncur` decimal(10,2) default NULL,
  `_Supplier` int(11) NOT NULL default '0',
  `_Customer` int(11) NOT NULL default '0',
  `_TermsOfPayment` int(11) NOT NULL default '0',
  `_MaxDeliveryDay` int(11) default NULL,
  `_TotalDeliveryDay` int(11) default NULL,
  `_DeliveryType` int(11) default NULL,
  `_HasTVA` int(1) default '0',
  `_HasTvaSurtax` int(1) NOT NULL default '0',
  `_HasFodecTax` int(1) NOT NULL default '0',
  `_HasTaxStamp` decimal(10,2) NOT NULL default '0.00',
  `_ToHaveTTC` decimal(10,2) default '0.00',
  `_InvoiceByMail` int(1) NOT NULL default '0',
  `_CustomerProductCommandBehaviour` int(1) NOT NULL default '0',
  `_Factor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Supplier` (`_Supplier`),
  KEY `_Customer` (`_Customer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `TVA`
--

DROP TABLE IF EXISTS `TVA`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TVA` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Category` int(11) NOT NULL default '0',
  `_Rate` decimal(10,2) NOT NULL default '0.00',
  `_Type` int(11) NOT NULL default '0',
  `_LastModified` datetime default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Task`
--

DROP TABLE IF EXISTS `Task`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Task` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Name` int(11) NOT NULL default '0',
  `_Instructions` varchar(255) default NULL,
  `_Duration` float NOT NULL default '0',
  `_Cost` float NOT NULL default '0',
  `_ToBeValidated` int(1) default '0',
  `_Type` int(11) NOT NULL default '0',
  `_IsBoxCreator` int(1) NOT NULL default '0',
  `_Symbol` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_Symbol` (`_Symbol`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `TermsOfPayment`
--

DROP TABLE IF EXISTS `TermsOfPayment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TermsOfPayment` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `TermsOfPaymentItem`
--

DROP TABLE IF EXISTS `TermsOfPaymentItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TermsOfPaymentItem` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_PercentOfTotal` decimal(10,2) default NULL,
  `_PaymentDelay` int(11) NOT NULL default '0',
  `_PaymentOption` int(3) NOT NULL default '0',
  `_PaymentEvent` int(3) NOT NULL default '0',
  `_PaymentModality` int(3) NOT NULL default '0',
  `_TermsOfPayment` int(11) NOT NULL default '0',
  `_Supplier` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_TermsOfPayment` (`_TermsOfPayment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Theme`
--

DROP TABLE IF EXISTS `Theme`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Theme` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Unavailability`
--

DROP TABLE IF EXISTS `Unavailability`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Unavailability` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Purpose` varchar(255) default NULL,
  `_BeginDate` datetime default NULL,
  `_EndDate` datetime default NULL,
  `_WeeklyPlanning` int(11) NOT NULL default '0',
  `_Command` int(11) NOT NULL default '0',
  `_ActivatedChainOperation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_WeeklyPlanning` (`_WeeklyPlanning`),
  KEY `_Command` (`_Command`),
  KEY `_ActivatedChainOperation` (`_ActivatedChainOperation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `UploadedDocument`
--

DROP TABLE IF EXISTS `UploadedDocument`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `UploadedDocument` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_Type` int(11) NOT NULL default '0',
  `_Comment` text,
  `_MimeType` int(11) NOT NULL default '0',
  `_Customer` int(11) NOT NULL default '0',
  `_ActivatedChainTask` int(11) NOT NULL default '0',
  `_UserAccount` int(11) NOT NULL default '0',
  `_CreationDate` datetime default NULL,
  `_LastModificationDate` datetime default NULL,
  PRIMARY KEY  (`_Id`),
  KEY `_MimeType` (`_MimeType`),
  KEY `_Customer` (`_Customer`),
  KEY `_ActivatedChainTask` (`_ActivatedChainTask`),
  KEY `_UserAccount` (`_UserAccount`),
  KEY `_Type` (`_Type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `UploadedDocumentType`
--

DROP TABLE IF EXISTS `UploadedDocumentType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `UploadedDocumentType` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` int(11) NOT NULL default '0',
  `_Active` int(1) default '1',
  PRIMARY KEY  (`_Id`),
  UNIQUE KEY `_Name` (`_Name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `UserAccount`
--

DROP TABLE IF EXISTS `UserAccount`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `UserAccount` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Login` varchar(255) default NULL,
  `_Password` varchar(40) default NULL,
  `_Identity` varchar(255) default NULL,
  `_Phone` varchar(255) default NULL,
  `_Fax` varchar(255) default NULL,
  `_Email` varchar(255) default NULL,
  `_Actor` int(11) NOT NULL default '0',
  `_Profile` int(3) NOT NULL default '0',
  `_Catalog` int(11) NOT NULL default '0',
  `_SupplierCatalog` int(11) NOT NULL default '0',
  `_CommissionPercent` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`_Id`),
  KEY `_Actor` (`_Actor`),
  KEY `_Profile` (`_Profile`),
  KEY `_Catalog` (`_Catalog`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;


--
-- Dumping data for table `UserAccount`
--

LOCK TABLES `UserAccount` WRITE;
/*!40000 ALTER TABLE `UserAccount` DISABLE KEYS */;
INSERT INTO `UserAccount` VALUES (1,0,'root','435b41068e8665513a20070c033b08b9c66e4332','ATEOR','','','moi@example.com',1,1,1,3,'0.00');
/*!40000 ALTER TABLE `UserAccount` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `WeeklyPlanning`
--

DROP TABLE IF EXISTS `WeeklyPlanning`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WeeklyPlanning` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Monday` int(11) NOT NULL default '0',
  `_Tuesday` int(11) NOT NULL default '0',
  `_Wednesday` int(11) NOT NULL default '0',
  `_Thursday` int(11) NOT NULL default '0',
  `_Friday` int(11) NOT NULL default '0',
  `_Saturday` int(11) NOT NULL default '0',
  `_Sunday` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Monday` (`_Monday`),
  KEY `_Tuesday` (`_Tuesday`),
  KEY `_Wednesday` (`_Wednesday`),
  KEY `_Thursday` (`_Thursday`),
  KEY `_Friday` (`_Friday`),
  KEY `_Saturday` (`_Saturday`),
  KEY `_Sunday` (`_Sunday`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `WorkOrder`
--

DROP TABLE IF EXISTS `WorkOrder`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WorkOrder` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  `_ValidityStart` datetime default NULL,
  `_ValidityEnd` datetime default NULL,
  `_Actor` int(11) NOT NULL default '0',
  `_Comment` varchar(255) default NULL,
  `_MaxVolume` float NOT NULL default '0',
  `_MaxLM` float NOT NULL default '0',
  `_MaxWeigth` float NOT NULL default '0',
  `_MaxDistance` float NOT NULL default '0',
  `_MaxDuration` time default NULL,
  `_State` int(11) NOT NULL default '0',
  `_ClotureDate` datetime default NULL,
  `_Massified` int(11) NOT NULL default '0',
  `_DepartureDate` datetime default NULL,
  `_ArrivalDate` datetime default NULL,
  `_DepartureKm` float NOT NULL default '0',
  `_ArrivalKm` float NOT NULL default '0',
  PRIMARY KEY  (`_Id`),
  KEY `_Actor` (`_Actor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Zip`
--

DROP TABLE IF EXISTS `Zip`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Zip` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default NULL,
  `_Code` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Zone`
--

DROP TABLE IF EXISTS `Zone`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Zone` (
  `_Id` int(11) unsigned NOT NULL default '0',
  `_DBId` int(11) default '0',
  `_Name` varchar(255) default NULL,
  PRIMARY KEY  (`_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aacFlyType`
--

DROP TABLE IF EXISTS `aacFlyType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aacFlyType` (
  `_ToAeroActor` int(11) unsigned NOT NULL default '0',
  `_FromFlyType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_ToAeroActor`,`_FromFlyType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aacLicence`
--

DROP TABLE IF EXISTS `aacLicence`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aacLicence` (
  `_ToAeroActor` int(11) unsigned NOT NULL default '0',
  `_FromLicence` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_ToAeroActor`,`_FromLicence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `accountAccountingType`
--

DROP TABLE IF EXISTS `accountAccountingType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `accountAccountingType` (
  `_FromAccount` int(11) unsigned NOT NULL default '0',
  `_ToAccountingType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromAccount`,`_ToAccountingType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `accountFlowType`
--

DROP TABLE IF EXISTS `accountFlowType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `accountFlowType` (
  `_FromAccount` int(11) unsigned NOT NULL default '0',
  `_ToFlowType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromAccount`,`_ToFlowType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `accountFlowTypeItem`
--

DROP TABLE IF EXISTS `accountFlowTypeItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `accountFlowTypeItem` (
  `_FromAccount` int(11) unsigned NOT NULL default '0',
  `_ToFlowTypeItem` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromAccount`,`_ToFlowTypeItem`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `achDangerousProductType`
--

DROP TABLE IF EXISTS `achDangerousProductType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `achDangerousProductType` (
  `_FromActivatedChain` int(11) unsigned NOT NULL default '0',
  `_ToDangerousProductType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActivatedChain`,`_ToDangerousProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `achProduct`
--

DROP TABLE IF EXISTS `achProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `achProduct` (
  `_FromActivatedChain` int(11) unsigned NOT NULL default '0',
  `_ToProduct` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActivatedChain`,`_ToProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `achProductType`
--

DROP TABLE IF EXISTS `achProductType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `achProductType` (
  `_FromActivatedChain` int(11) unsigned NOT NULL default '0',
  `_ToProductType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActivatedChain`,`_ToProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ackComponent`
--

DROP TABLE IF EXISTS `ackComponent`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ackComponent` (
  `_FromActivatedChainTask` int(11) unsigned NOT NULL default '0',
  `_ToComponent` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActivatedChainTask`,`_ToComponent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ackConcreteComponent`
--

DROP TABLE IF EXISTS `ackConcreteComponent`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ackConcreteComponent` (
  `_FromConcreteComponent` int(11) unsigned NOT NULL default '0',
  `_ToActivatedChainTask` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromConcreteComponent`,`_ToActivatedChainTask`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ackUserAccount`
--

DROP TABLE IF EXISTS `ackUserAccount`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ackUserAccount` (
  `_FromActivatedChainTask` int(11) unsigned NOT NULL default '0',
  `_ToUserAccount` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActivatedChainTask`,`_ToUserAccount`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `acmLocation`
--

DROP TABLE IF EXISTS `acmLocation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `acmLocation` (
  `_FromActivatedMovement` int(11) unsigned NOT NULL default '0',
  `_ToLocation` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActivatedMovement`,`_ToLocation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `actDocumentAppendix`
--

DROP TABLE IF EXISTS `actDocumentAppendix`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `actDocumentAppendix` (
  `_FromActor` int(11) unsigned NOT NULL default '0',
  `_ToDocumentAppendix` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActor`,`_ToDocumentAppendix`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `actJob`
--

DROP TABLE IF EXISTS `actJob`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `actJob` (
  `_FromActor` int(11) unsigned NOT NULL default '0',
  `_ToJob` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActor`,`_ToJob`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `actOperation`
--

DROP TABLE IF EXISTS `actOperation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `actOperation` (
  `_FromActor` int(11) unsigned NOT NULL default '0',
  `_ToOperation` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromActor`,`_ToOperation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `adcExecutedMovement`
--

DROP TABLE IF EXISTS `adcExecutedMovement`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `adcExecutedMovement` (
  `_FromDeliveryOrder` int(11) unsigned NOT NULL default '0',
  `_ToExecutedMovement` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromDeliveryOrder`,`_ToExecutedMovement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `alertUserAccount`
--

DROP TABLE IF EXISTS `alertUserAccount`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `alertUserAccount` (
  `_FromAlert` int(11) NOT NULL default '0',
  `_ToUserAccount` int(11) NOT NULL default '0',
  KEY `_Alert` (`_FromAlert`),
  KEY `_UserAccount` (`_ToUserAccount`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `boxActivatedChainTask`
--

DROP TABLE IF EXISTS `boxActivatedChainTask`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `boxActivatedChainTask` (
  `_FromBox` int(11) unsigned NOT NULL default '0',
  `_ToActivatedChainTask` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromBox`,`_ToActivatedChainTask`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `chnDangerousProductType`
--

DROP TABLE IF EXISTS `chnDangerousProductType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `chnDangerousProductType` (
  `_FromChain` int(11) unsigned NOT NULL default '0',
  `_ToDangerousProductType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromChain`,`_ToDangerousProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `chnProductType`
--

DROP TABLE IF EXISTS `chnProductType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `chnProductType` (
  `_FromChain` int(11) unsigned NOT NULL default '0',
  `_ToProductType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromChain`,`_ToProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `chtComponent`
--

DROP TABLE IF EXISTS `chtComponent`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `chtComponent` (
  `_FromChainTask` int(11) unsigned NOT NULL default '0',
  `_ToComponent` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromChainTask`,`_ToComponent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `chtUserAccount`
--

DROP TABLE IF EXISTS `chtUserAccount`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `chtUserAccount` (
  `_FromChainTask` int(11) unsigned NOT NULL default '0',
  `_ToUserAccount` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromChainTask`,`_ToUserAccount`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cppcConcreteProduct`
--

DROP TABLE IF EXISTS `cppcConcreteProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cppcConcreteProduct` (
  `_FromConcreteProductPrestationCost` int(11) unsigned NOT NULL default '0',
  `_ToConcreteProduct` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromConcreteProductPrestationCost`,`_ToConcreteProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cptHead`
--

DROP TABLE IF EXISTS `cptHead`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cptHead` (
  `_ConcreteProduct` int(11) unsigned NOT NULL default '0',
  `_Head` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_ConcreteProduct`,`_Head`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ctgProductType`
--

DROP TABLE IF EXISTS `ctgProductType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ctgProductType` (
  `_FromCatalog` int(11) unsigned NOT NULL default '0',
  `_ToProductType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromCatalog`,`_ToProductType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `exmLocation`
--

DROP TABLE IF EXISTS `exmLocation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `exmLocation` (
  `_FromExecutedMovement` int(11) NOT NULL default '0',
  `_ToLocation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`_FromExecutedMovement`,`_ToLocation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `fllFlyType`
--

DROP TABLE IF EXISTS `fllFlyType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `fllFlyType` (
  `_ToFlyLicence` int(11) unsigned NOT NULL default '0',
  `_FromFlyType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_ToFlyLicence`,`_FromFlyType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `fltpcFlyType`
--

DROP TABLE IF EXISTS `fltpcFlyType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `fltpcFlyType` (
  `_FromFlyTypePrestationCost` int(11) unsigned NOT NULL default '0',
  `_ToFlyType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromFlyTypePrestationCost`,`_ToFlyType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `locProduct`
--

DROP TABLE IF EXISTS `locProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `locProduct` (
  `_FromLocation` int(11) unsigned NOT NULL default '0',
  `_ToProduct` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromLocation`,`_ToProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `oprTask`
--

DROP TABLE IF EXISTS `oprTask`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `oprTask` (
  `_FromOperation` int(11) unsigned NOT NULL default '0',
  `_ToTask` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromOperation`,`_ToTask`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pdtProperty`
--

DROP TABLE IF EXISTS `pdtProperty`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pdtProperty` (
  `_FromProductType` int(11) unsigned NOT NULL default '0',
  `_ToProperty` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromProductType`,`_ToProperty`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `phcCategory`
--

DROP TABLE IF EXISTS `phcCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phcCategory` (
  `_FromProductHandingByCategory` int(11) unsigned NOT NULL default '0',
  `_ToCategory` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromProductHandingByCategory`,`_ToCategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ppcProduct`
--

DROP TABLE IF EXISTS `ppcProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ppcProduct` (
  `_FromProductPrestationCost` int(11) unsigned NOT NULL default '0',
  `_ToProduct` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromProductPrestationCost`,`_ToProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `prmCategory`
--

DROP TABLE IF EXISTS `prmCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `prmCategory` (
  `_FromPromotion` int(11) unsigned NOT NULL default '0',
  `_ToCategory` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromPromotion`,`_ToCategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `prmProduct`
--

DROP TABLE IF EXISTS `prmProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `prmProduct` (
  `_FromPromotion` int(11) unsigned NOT NULL default '0',
  `_ToProduct` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromPromotion`,`_ToProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `productModelRTWSize`
--

DROP TABLE IF EXISTS `productModelRTWSize`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `productModelRTWSize` (
  `_FromProductModel` int(11) unsigned NOT NULL default '0',
  `_ToRTWSize` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromProductModel`,`_ToRTWSize`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `prsToMvtType`
--

DROP TABLE IF EXISTS `prsToMvtType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `prsToMvtType` (
  `_FromPrestation` int(11) unsigned NOT NULL default '0',
  `_ToMovementType` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromPrestation`,`_ToMovementType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `questionToCategory`
--

DROP TABLE IF EXISTS `questionToCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `questionToCategory` (
  `_FromQuestion` int(11) unsigned NOT NULL default '0',
  `_ToCategory` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromQuestion`,`_ToCategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `rTWModelRTWSize`
--

DROP TABLE IF EXISTS `rTWModelRTWSize`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `rTWModelRTWSize` (
  `_ToRTWSize` int(11) unsigned NOT NULL default '0',
  `_FromRTWModel` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_ToRTWSize`,`_FromRTWModel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `saiProduct`
--

DROP TABLE IF EXISTS `saiProduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `saiProduct` (
  `_FromSaisonality` int(11) unsigned NOT NULL default '0',
  `_ToProduct` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromSaisonality`,`_ToProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `saiProductKind`
--

DROP TABLE IF EXISTS `saiProductKind`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `saiProductKind` (
  `_FromSaisonality` int(11) unsigned NOT NULL default '0',
  `_ToProductKind` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromSaisonality`,`_ToProductKind`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `sitContact`
--

DROP TABLE IF EXISTS `sitContact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sitContact` (
  `_ToContact` int(11) unsigned NOT NULL default '0',
  `_FromSite` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_ToContact`,`_FromSite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `siteUserAccount`
--

DROP TABLE IF EXISTS `siteUserAccount`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `siteUserAccount` (
  `_ToUserAccount` int(11) unsigned NOT NULL default '0',
  `_FromSite` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_ToUserAccount`,`_FromSite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `spcDocumentModel`
--

DROP TABLE IF EXISTS `spcDocumentModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `spcDocumentModel` (
  `_FromSupplierCustomer` int(11) unsigned NOT NULL default '0',
  `_ToDocumentModel` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`_FromSupplierCustomer`,`_ToDocumentModel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-06-09 23:16:38
