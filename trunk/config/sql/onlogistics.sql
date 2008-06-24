
--
-- Table structure for IdHashTable
--
DROP TABLE IF EXISTS IdHashTable;
CREATE TABLE IdHashTable (
  _Table varchar(255) default NULL,
  _Id int(11) NOT NULL default '0',
  UNIQUE KEY _Table (_Table)
) TYPE=InnoDB;


--
-- Table structure for AbstractDocument
--
DROP TABLE IF EXISTS AbstractDocument;
CREATE TABLE AbstractDocument (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _DocumentNo VARCHAR(255) DEFAULT NULL,
  _EditionDate DATETIME DEFAULT NULL,
  _Command INT(11) NOT NULL DEFAULT 0,
  _CommandType INT(3) NOT NULL DEFAULT 0,
  _DocumentModel INT(11) NOT NULL DEFAULT 0,
  _SupplierCustomer INT(11) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  _AccountingTypeActor INT(11) NOT NULL DEFAULT 0,
  _PDFDocument INT(11) NOT NULL DEFAULT 0,
  _Locale VARCHAR(10) DEFAULT NULL,
  _Type INT(3) DEFAULT NULL,
  _TotalPriceHT DECIMAL(10,2) DEFAULT NULL,
  _TotalPriceTTC DECIMAL(10,2) DEFAULT NULL,
  _RemainingTTC DECIMAL(10,2) DEFAULT NULL,
  _Comment TEXT DEFAULT NULL,
  _TVA INT(11) NOT NULL DEFAULT 0,
  _Box INT(11) NOT NULL DEFAULT 0,
  _BeginDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _Port DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Packing DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Insurance DECIMAL(10,2) NOT NULL DEFAULT 0,
  _GlobalHanding DECIMAL(10,2) NOT NULL DEFAULT 0,
  _PaymentCondition VARCHAR(255) DEFAULT '0',
  _ToPay DECIMAL(10,2) NOT NULL DEFAULT 0,
  _PaymentDate DATETIME DEFAULT NULL,
  _TvaSurtaxRate DECIMAL(10,2) NOT NULL DEFAULT 0,
  _FodecTaxRate DECIMAL(10,2) NOT NULL DEFAULT 0,
  _TaxStamp DECIMAL(10,2) NOT NULL DEFAULT 0,
  _DestinatorSite INT(11) NOT NULL DEFAULT 0,
  _CommandNo VARCHAR(255) DEFAULT '',
  _Transporter INT(11) NOT NULL DEFAULT 0,
  _ConveyorArrivalSite INT(11) NOT NULL DEFAULT 0,
  _ConveyorDepartureSite INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Command ON AbstractDocument (_Command);
CREATE INDEX _DocumentModel ON AbstractDocument (_DocumentModel);
CREATE INDEX _SupplierCustomer ON AbstractDocument (_SupplierCustomer);
CREATE INDEX _Currency ON AbstractDocument (_Currency);
CREATE INDEX _AccountingTypeActor ON AbstractDocument (_AccountingTypeActor);
CREATE INDEX _PDFDocument ON AbstractDocument (_PDFDocument);
CREATE INDEX _TVA ON AbstractDocument (_TVA);
CREATE INDEX _Box ON AbstractDocument (_Box);
CREATE INDEX _DestinatorSite ON AbstractDocument (_DestinatorSite);
CREATE INDEX _Transporter ON AbstractDocument (_Transporter);
CREATE INDEX _ConveyorArrivalSite ON AbstractDocument (_ConveyorArrivalSite);
CREATE INDEX _ConveyorDepartureSite ON AbstractDocument (_ConveyorDepartureSite);

--
-- Table structure for AbstractInstant
--
DROP TABLE IF EXISTS AbstractInstant;
CREATE TABLE AbstractInstant (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _Date DATETIME DEFAULT NULL,
  _Time TIME DEFAULT NULL,
  _Day INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Account
--
DROP TABLE IF EXISTS Account;
CREATE TABLE Account (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Number VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  _Currency INT(11) NOT NULL DEFAULT 0,
  _BreakdownType INT(3) NOT NULL DEFAULT 0,
  _TVA INT(11) NOT NULL DEFAULT 0,
  _ActorBankDetail INT(11) NOT NULL DEFAULT 0,
  _Comment TEXT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Currency ON Account (_Currency);
CREATE INDEX _TVA ON Account (_TVA);
CREATE INDEX _ActorBankDetail ON Account (_ActorBankDetail);

--
-- Table structure for AccountingType
--
DROP TABLE IF EXISTS AccountingType;
CREATE TABLE AccountingType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Type VARCHAR(255) DEFAULT NULL,
  _MainModel INT(1) DEFAULT 0,
  _DistributionKey FLOAT DEFAULT NULL,
  _ActorBankDetail INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Type ON AccountingType (_Type);
CREATE INDEX _ActorBankDetail ON AccountingType (_ActorBankDetail);

--
-- Table structure for Action
--
DROP TABLE IF EXISTS Action;
CREATE TABLE Action (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Commercial INT(11) NOT NULL DEFAULT 0,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _FormModel INT(11) NOT NULL DEFAULT 0,
  _WishedDate DATETIME DEFAULT '0',
  _ActionDate DATETIME DEFAULT NULL,
  _Type INT(11) NOT NULL DEFAULT 0,
  _State INT(3) NOT NULL DEFAULT 0,
  _Comment TEXT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Commercial ON Action (_Commercial);
CREATE INDEX _Actor ON Action (_Actor);
CREATE INDEX _FormModel ON Action (_FormModel);

--
-- Table structure for ActivatedChain
--
DROP TABLE IF EXISTS ActivatedChain;
CREATE TABLE ActivatedChain (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Reference VARCHAR(255) DEFAULT NULL,
  _Description VARCHAR(255) DEFAULT NULL,
  _DescriptionCatalog VARCHAR(255) DEFAULT NULL,
  _Owner INT(11) NOT NULL DEFAULT 0,
  _SiteTransition INT(11) NOT NULL DEFAULT 0,
  _PivotTask INT(11) NOT NULL DEFAULT 0,
  _PivotDateType INT(3) NOT NULL DEFAULT 0,
  _BeginDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _OwnerWorkerOrder INT(11) NOT NULL DEFAULT 0,
  _ExecutionSequence INT(11) NOT NULL DEFAULT 0,
  _BarCodeType INT(11) NOT NULL DEFAULT 0,
  _Type INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Owner ON ActivatedChain (_Owner);
CREATE INDEX _SiteTransition ON ActivatedChain (_SiteTransition);
CREATE INDEX _PivotTask ON ActivatedChain (_PivotTask);
CREATE INDEX _OwnerWorkerOrder ON ActivatedChain (_OwnerWorkerOrder);
CREATE INDEX _BarCodeType ON ActivatedChain (_BarCodeType);

--
-- Table structure for ActivatedChainOperation
--
DROP TABLE IF EXISTS ActivatedChainOperation;
CREATE TABLE ActivatedChainOperation (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Operation INT(11) NOT NULL DEFAULT 0,
  _Ghost INT(11) NOT NULL DEFAULT 0,
  _ActivatedChain INT(11) NOT NULL DEFAULT 0,
  _OwnerWorkerOrder INT(11) NOT NULL DEFAULT 0,
  _RealActor INT(11) NOT NULL DEFAULT 0,
  _ConcreteProduct INT(11) NOT NULL DEFAULT 0,
  _RealConcreteProduct INT(11) NOT NULL DEFAULT 0,
  _FirstTask INT(11) NOT NULL DEFAULT 0,
  _LastTask INT(11) NOT NULL DEFAULT 0,
  _Order INT(11) NOT NULL DEFAULT 0,
  _OrderInWorkOrder INT(11) NOT NULL DEFAULT 0,
  _TaskCount INT(11) NOT NULL DEFAULT 0,
  _Massified INT(11) NOT NULL DEFAULT 0,
  _State INT(11) NOT NULL DEFAULT 0,
  _PrestationFactured INT(3) NOT NULL DEFAULT 0,
  _PrestationCommandDate DATETIME DEFAULT NULL,
  _InvoiceItem INT(11) NOT NULL DEFAULT 0,
  _InvoicePrestation INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Actor ON ActivatedChainOperation (_Actor);
CREATE INDEX _Operation ON ActivatedChainOperation (_Operation);
CREATE INDEX _Ghost ON ActivatedChainOperation (_Ghost);
CREATE INDEX _ActivatedChain ON ActivatedChainOperation (_ActivatedChain);
CREATE INDEX _OwnerWorkerOrder ON ActivatedChainOperation (_OwnerWorkerOrder);
CREATE INDEX _RealActor ON ActivatedChainOperation (_RealActor);
CREATE INDEX _ConcreteProduct ON ActivatedChainOperation (_ConcreteProduct);
CREATE INDEX _RealConcreteProduct ON ActivatedChainOperation (_RealConcreteProduct);
CREATE INDEX _FirstTask ON ActivatedChainOperation (_FirstTask);
CREATE INDEX _LastTask ON ActivatedChainOperation (_LastTask);
CREATE INDEX _InvoiceItem ON ActivatedChainOperation (_InvoiceItem);
CREATE INDEX _InvoicePrestation ON ActivatedChainOperation (_InvoicePrestation);

--
-- Table structure for ActivatedChainTask
--
DROP TABLE IF EXISTS ActivatedChainTask;
CREATE TABLE ActivatedChainTask (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Order INT(11) NOT NULL DEFAULT 0,
  _Ghost INT(11) NOT NULL DEFAULT 0,
  _Interuptible INT(11) NOT NULL DEFAULT 0,
  _RawDuration FLOAT NOT NULL DEFAULT 0,
  _DurationType INT(3) NOT NULL DEFAULT 0,
  _KilometerNumber FLOAT NOT NULL DEFAULT 0,
  _TriggerMode INT(3) NOT NULL DEFAULT 0,
  _TriggerDelta INT(11) NOT NULL DEFAULT 0,
  _RawCost DECIMAL(10,2) NOT NULL DEFAULT 0,
  _CostType INT(3) NOT NULL DEFAULT 0,
  _Duration FLOAT NOT NULL DEFAULT 0,
  _Cost DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Instructions VARCHAR(255) DEFAULT NULL,
  _Task INT(11) NOT NULL DEFAULT 0,
  _ActorSiteTransition INT(11) NOT NULL DEFAULT 0,
  _DepartureInstant INT(11) NOT NULL DEFAULT 0,
  _ArrivalInstant INT(11) NOT NULL DEFAULT 0,
  _Begin DATETIME DEFAULT NULL,
  _End DATETIME DEFAULT NULL,
  _InterruptionDate DATETIME DEFAULT NULL,
  _RestartDate DATETIME DEFAULT NULL,
  _RealBegin DATETIME DEFAULT NULL,
  _RealEnd DATETIME DEFAULT NULL,
  _RealDuration FLOAT NOT NULL DEFAULT 0,
  _RealQuantity DECIMAL(10,3) DEFAULT NULL,
  _RealCost DECIMAL(10,2) DEFAULT NULL,
  _ActivatedOperation INT(11) NOT NULL DEFAULT 0,
  _ValidationUser INT(11) NOT NULL DEFAULT 0,
  _OwnerWorkerOrder INT(11) NOT NULL DEFAULT 0,
  _ActivatedChainTaskDetail INT(11) NOT NULL DEFAULT 0,
  _Massified INT(11) NOT NULL DEFAULT 0,
  _DataProvider INT(11) NOT NULL DEFAULT 0,
  _WithForecast INT(11) NOT NULL DEFAULT 0,
  _State INT(3) NOT NULL DEFAULT 0,
  _ProductCommandType INT(11) NOT NULL DEFAULT 0,
  _DepartureActor INT(11) NOT NULL DEFAULT 0,
  _DepartureSite INT(11) NOT NULL DEFAULT 0,
  _ArrivalActor INT(11) NOT NULL DEFAULT 0,
  _ArrivalSite INT(11) NOT NULL DEFAULT 0,
  _WishedDateType INT(3) NOT NULL DEFAULT 0,
  _Delta INT(11) NOT NULL DEFAULT 0,
  _ComponentQuantityRatio INT(1) DEFAULT 0,
  _ActivationPerSupplier INT(1) DEFAULT 0,
  _AssembledQuantity DECIMAL(10,3) DEFAULT NULL,
  _AssembledRealQuantity DECIMAL(10,3) DEFAULT NULL,
  _Component INT(11) NOT NULL DEFAULT 0,
  _ChainToActivate INT(11) NOT NULL DEFAULT 0,
  _RessourceGroup INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Ghost ON ActivatedChainTask (_Ghost);
CREATE INDEX _Task ON ActivatedChainTask (_Task);
CREATE INDEX _ActorSiteTransition ON ActivatedChainTask (_ActorSiteTransition);
CREATE INDEX _DepartureInstant ON ActivatedChainTask (_DepartureInstant);
CREATE INDEX _ArrivalInstant ON ActivatedChainTask (_ArrivalInstant);
CREATE INDEX _ActivatedOperation ON ActivatedChainTask (_ActivatedOperation);
CREATE INDEX _ValidationUser ON ActivatedChainTask (_ValidationUser);
CREATE INDEX _OwnerWorkerOrder ON ActivatedChainTask (_OwnerWorkerOrder);
CREATE INDEX _ActivatedChainTaskDetail ON ActivatedChainTask (_ActivatedChainTaskDetail);
CREATE INDEX _DataProvider ON ActivatedChainTask (_DataProvider);
CREATE INDEX _DepartureActor ON ActivatedChainTask (_DepartureActor);
CREATE INDEX _DepartureSite ON ActivatedChainTask (_DepartureSite);
CREATE INDEX _ArrivalActor ON ActivatedChainTask (_ArrivalActor);
CREATE INDEX _ArrivalSite ON ActivatedChainTask (_ArrivalSite);
CREATE INDEX _Component ON ActivatedChainTask (_Component);
CREATE INDEX _ChainToActivate ON ActivatedChainTask (_ChainToActivate);
CREATE INDEX _RessourceGroup ON ActivatedChainTask (_RessourceGroup);

--
-- Table structure for ActivatedChainTaskDetail
--
DROP TABLE IF EXISTS ActivatedChainTaskDetail;
CREATE TABLE ActivatedChainTaskDetail (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _OilAdded DECIMAL(10,2) DEFAULT NULL,
  _CarburantRest DECIMAL(10,2) DEFAULT NULL,
  _CarburantAdded DECIMAL(10,2) DEFAULT NULL,
  _CarburantTotal DECIMAL(10,2) DEFAULT NULL,
  _CarburantUsed DECIMAL(10,2) DEFAULT NULL,
  _Comment TEXT DEFAULT NULL,
  _InstructorSeat INT(3) NOT NULL DEFAULT 0,
  _CustomerSeat INT(3) NOT NULL DEFAULT 0,
  _CycleEngine1N1 DECIMAL(10,2) DEFAULT NULL,
  _CycleEngine1N2 DECIMAL(10,2) DEFAULT NULL,
  _CycleEngine1 DECIMAL(10,2) DEFAULT NULL,
  _CycleEngine2N1 DECIMAL(10,2) DEFAULT NULL,
  _CycleEngine2N2 DECIMAL(10,2) DEFAULT NULL,
  _CycleEngine2 DECIMAL(10,2) DEFAULT NULL,
  _CycleCellule INT(11) DEFAULT NULL,
  _CycleTreuillage INT(11) DEFAULT NULL,
  _CycleCharge INT(11) DEFAULT NULL,
  _RealCommercialDuration DECIMAL(10,2) DEFAULT NULL,
  _EngineOn DATETIME DEFAULT NULL,
  _EngineOff DATETIME DEFAULT NULL,
  _TakeOff DATETIME DEFAULT NULL,
  _Landing DATETIME DEFAULT NULL,
  _TechnicalHour DECIMAL(10,2) DEFAULT NULL,
  _CelluleHour DECIMAL(10,2) DEFAULT NULL,
  _Nature INT(3) NOT NULL DEFAULT 0,
  _IFRLanding INT(11) NOT NULL DEFAULT 0,
  _PilotHours INT(11) NOT NULL DEFAULT 0,
  _PilotHoursBiEngine INT(11) NOT NULL DEFAULT 0,
  _CoPilotHours INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursBiEngine INT(11) NOT NULL DEFAULT 0,
  _PilotHoursNight INT(11) NOT NULL DEFAULT 0,
  _PilotHoursBiEngineNight INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursNight INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursBiEngineNight INT(11) NOT NULL DEFAULT 0,
  _PilotHoursIFR INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursIFR INT(11) NOT NULL DEFAULT 0,
  _PublicHours INT(11) NOT NULL DEFAULT 0,
  _VLAEHours INT(11) NOT NULL DEFAULT 0,
  _TakeOffNumber INT(11) NOT NULL DEFAULT 0,
  _LandingNumber INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for ActivatedMovement
--
DROP TABLE IF EXISTS ActivatedMovement;
CREATE TABLE ActivatedMovement (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _StartDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _State INT(3) NOT NULL DEFAULT 0,
  _HasBeenFactured INT(3) NOT NULL DEFAULT 0,
  _Quantity DECIMAL(10,3) DEFAULT NULL,
  _Type INT(11) NOT NULL DEFAULT 0,
  _ProductCommandItem INT(11) NOT NULL DEFAULT 0,
  _ActivatedChainTask INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _ProductCommand INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Type ON ActivatedMovement (_Type);
CREATE INDEX _ProductCommandItem ON ActivatedMovement (_ProductCommandItem);
CREATE INDEX _ActivatedChainTask ON ActivatedMovement (_ActivatedChainTask);
CREATE INDEX _Product ON ActivatedMovement (_Product);
CREATE INDEX _ProductCommand ON ActivatedMovement (_ProductCommand);

--
-- Table structure for Actor
--
DROP TABLE IF EXISTS Actor;
CREATE TABLE Actor (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  _DatabaseOwner INT(1) DEFAULT 0,
  _Quality INT(3) DEFAULT NULL,
  _Code VARCHAR(255) DEFAULT NULL,
  _Siret VARCHAR(255) DEFAULT NULL,
  _IATA VARCHAR(255) DEFAULT NULL,
  _Logo TEXT DEFAULT NULL,
  _Slogan VARCHAR(255) DEFAULT NULL,
  _TVA VARCHAR(255) DEFAULT NULL,
  _RCS VARCHAR(255) DEFAULT NULL,
  _Role VARCHAR(255) DEFAULT NULL,
  _Active INT(11) NOT NULL DEFAULT 1,
  _PaymentCondition INT(11) NOT NULL DEFAULT 0,
  _Incoterm INT(11) NOT NULL DEFAULT 0,
  _PackageCondition INT(11) DEFAULT NULL,
  _Commercial INT(11) NOT NULL DEFAULT 0,
  _PlanningComment VARCHAR(255) DEFAULT NULL,
  _MainSite INT(11) NOT NULL DEFAULT 0,
  _Category INT(11) NOT NULL DEFAULT 0,
  _RemExcep FLOAT NOT NULL DEFAULT 0,
  _Generic INT(11) NOT NULL DEFAULT 0,
  _GenericActor INT(11) NOT NULL DEFAULT 0,
  _Trademark VARCHAR(255) DEFAULT NULL,
  _CompanyType VARCHAR(255) DEFAULT NULL,
  _CreationDate DATETIME DEFAULT NULL,
  _Currency INT(11) NOT NULL DEFAULT 0,
  _PricingZone INT(11) NOT NULL DEFAULT 0,
  _AccountingType INT(11) NOT NULL DEFAULT 0,
  _CustomerProperties INT(11) NOT NULL DEFAULT 0,
  _ActorDetail INT(11) NOT NULL DEFAULT 0,
  _OnlogisticsAccount VARCHAR(255) DEFAULT 'null',
  _Weight FLOAT NOT NULL DEFAULT 0,
  _Cost FLOAT NOT NULL DEFAULT 0,
  _IFRLanding INT(11) NOT NULL DEFAULT 0,
  _PilotHours INT(11) NOT NULL DEFAULT 0,
  _PilotHoursBiEngine INT(11) NOT NULL DEFAULT 0,
  _CoPilotHours INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursBiEngine INT(11) NOT NULL DEFAULT 0,
  _PilotHoursNight INT(11) NOT NULL DEFAULT 0,
  _PilotHoursBiEngineNight INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursNight INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursBiEngineNight INT(11) NOT NULL DEFAULT 0,
  _PilotHoursIFR INT(11) NOT NULL DEFAULT 0,
  _CoPilotHoursIFR INT(11) NOT NULL DEFAULT 0,
  _StudentHours INT(11) NOT NULL DEFAULT 0,
  _InstructorHours INT(11) NOT NULL DEFAULT 0,
  _PublicHours INT(11) NOT NULL DEFAULT 0,
  _CommercialHours INT(11) NOT NULL DEFAULT 0,
  _VLAEHours INT(11) NOT NULL DEFAULT 0,
  _Trainee INT(1) DEFAULT 0,
  _SoloFly INT(1) DEFAULT 0,
  _LastFlyDate DATETIME DEFAULT NULL,
  _Instructor INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON Actor (_Name);
CREATE INDEX _Incoterm ON Actor (_Incoterm);
CREATE INDEX _Commercial ON Actor (_Commercial);
CREATE INDEX _MainSite ON Actor (_MainSite);
CREATE INDEX _Category ON Actor (_Category);
CREATE INDEX _GenericActor ON Actor (_GenericActor);
CREATE INDEX _Currency ON Actor (_Currency);
CREATE INDEX _PricingZone ON Actor (_PricingZone);
CREATE INDEX _AccountingType ON Actor (_AccountingType);
CREATE INDEX _CustomerProperties ON Actor (_CustomerProperties);
CREATE INDEX _ActorDetail ON Actor (_ActorDetail);
CREATE INDEX _Instructor ON Actor (_Instructor);

--
-- Table structure for ActorBankDetail
--
DROP TABLE IF EXISTS ActorBankDetail;
CREATE TABLE ActorBankDetail (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Iban VARCHAR(255) DEFAULT NULL,
  _BankName VARCHAR(255) DEFAULT NULL,
  _Swift VARCHAR(255) DEFAULT NULL,
  _BankAddressNo VARCHAR(255) DEFAULT NULL,
  _BankAddressStreetType INT(3) NOT NULL DEFAULT 0,
  _BankAddressStreet VARCHAR(255) DEFAULT NULL,
  _BankAddressAdd VARCHAR(255) DEFAULT NULL,
  _BankAddressCity VARCHAR(255) DEFAULT NULL,
  _BankAddressZipCode VARCHAR(255) DEFAULT NULL,
  _BankAddressCountry VARCHAR(255) DEFAULT NULL,
  _AccountNumber VARCHAR(255) DEFAULT NULL,
  _Amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  _LastUpdate DATE DEFAULT NULL,
  _Active INT(1) DEFAULT 1,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Actor ON ActorBankDetail (_Actor);
CREATE INDEX _Currency ON ActorBankDetail (_Currency);

--
-- Table structure for ActorDetail
--
DROP TABLE IF EXISTS ActorDetail;
CREATE TABLE ActorDetail (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _IsInternalAffectation INT(1) DEFAULT 1,
  _InternalAffectation INT(11) NOT NULL DEFAULT 0,
  _Signatory INT(11) NOT NULL DEFAULT 0,
  _BusinessProvider INT(11) NOT NULL DEFAULT 0,
  _Actor INT(None) NOT NULL DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _InternalAffectation ON ActorDetail (_InternalAffectation);
CREATE INDEX _Signatory ON ActorDetail (_Signatory);
CREATE INDEX _BusinessProvider ON ActorDetail (_BusinessProvider);

--
-- Table structure for ActorProduct
--
DROP TABLE IF EXISTS ActorProduct;
CREATE TABLE ActorProduct (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _AssociatedProductReference VARCHAR(255) DEFAULT NULL,
  _BuyUnitQuantity DECIMAL(10,3) DEFAULT NULL,
  _BuyUnitType INT(11) NOT NULL DEFAULT 0,
  _Priority INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Actor ON ActorProduct (_Actor);
CREATE INDEX _Product ON ActorProduct (_Product);
CREATE INDEX _BuyUnitType ON ActorProduct (_BuyUnitType);

--
-- Table structure for ActorSiteTransition
--
DROP TABLE IF EXISTS ActorSiteTransition;
CREATE TABLE ActorSiteTransition (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _DepartureZone INT(11) NOT NULL DEFAULT 0,
  _DepartureActor INT(11) NOT NULL DEFAULT 0,
  _DepartureSite INT(11) NOT NULL DEFAULT 0,
  _ArrivalZone INT(11) NOT NULL DEFAULT 0,
  _ArrivalActor INT(11) NOT NULL DEFAULT 0,
  _ArrivalSite INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _DepartureZone ON ActorSiteTransition (_DepartureZone);
CREATE INDEX _DepartureActor ON ActorSiteTransition (_DepartureActor);
CREATE INDEX _DepartureSite ON ActorSiteTransition (_DepartureSite);
CREATE INDEX _ArrivalZone ON ActorSiteTransition (_ArrivalZone);
CREATE INDEX _ArrivalActor ON ActorSiteTransition (_ArrivalActor);
CREATE INDEX _ArrivalSite ON ActorSiteTransition (_ArrivalSite);

--
-- Table structure for Alert
--
DROP TABLE IF EXISTS Alert;
CREATE TABLE Alert (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _BodyAddon LONGTEXT DEFAULT NULL,
  _Template VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for AnnualTurnoverDiscount
--
DROP TABLE IF EXISTS AnnualTurnoverDiscount;
CREATE TABLE AnnualTurnoverDiscount (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Amount DECIMAL(10,2) DEFAULT NULL,
  _Year INT(4) DEFAULT NULL,
  _SupplierCustomer INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _SupplierCustomer ON AnnualTurnoverDiscount (_SupplierCustomer);

--
-- Table structure for AnnualTurnoverDiscountPercent
--
DROP TABLE IF EXISTS AnnualTurnoverDiscountPercent;
CREATE TABLE AnnualTurnoverDiscountPercent (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Amount DECIMAL(10,2) DEFAULT NULL,
  _Date DATETIME DEFAULT NULL,
  _Category INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Category ON AnnualTurnoverDiscountPercent (_Category);

--
-- Table structure for AnswerModel
--
DROP TABLE IF EXISTS AnswerModel;
CREATE TABLE AnswerModel (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Value VARCHAR(255) DEFAULT NULL,
  _Alert INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Value ON AnswerModel (_Value);
CREATE INDEX _Alert ON AnswerModel (_Alert);

--
-- Table structure for BarCodeType
--
DROP TABLE IF EXISTS BarCodeType;
CREATE TABLE BarCodeType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Code VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Box
--
DROP TABLE IF EXISTS Box;
CREATE TABLE Box (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Reference VARCHAR(255) DEFAULT NULL,
  _Level INT(11) DEFAULT NULL,
  _Comment TEXT DEFAULT NULL,
  _Dimensions TEXT DEFAULT NULL,
  _Date DATETIME DEFAULT NULL,
  _Weight FLOAT NOT NULL DEFAULT 0,
  _Volume FLOAT NOT NULL DEFAULT 0,
  _PrestationFactured INT(1) DEFAULT 0,
  _InvoicePrestation INT(11) NOT NULL DEFAULT 0,
  _ParentBox INT(11) NOT NULL DEFAULT 0,
  _ActivatedChain INT(11) NOT NULL DEFAULT 0,
  _CommandItem INT(11) NOT NULL DEFAULT 0,
  _LocationExecutedMovement INT(11) NOT NULL DEFAULT 0,
  _CoverType INT(11) NOT NULL DEFAULT 0,
  _Expeditor INT(11) NOT NULL DEFAULT 0,
  _ExpeditorSite INT(11) NOT NULL DEFAULT 0,
  _Destinator INT(11) NOT NULL DEFAULT 0,
  _DestinatorSite INT(11) NOT NULL DEFAULT 0,
  _PackingList INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _InvoicePrestation ON Box (_InvoicePrestation);
CREATE INDEX _ParentBox ON Box (_ParentBox);
CREATE INDEX _ActivatedChain ON Box (_ActivatedChain);
CREATE INDEX _CommandItem ON Box (_CommandItem);
CREATE INDEX _LocationExecutedMovement ON Box (_LocationExecutedMovement);
CREATE INDEX _CoverType ON Box (_CoverType);
CREATE INDEX _Expeditor ON Box (_Expeditor);
CREATE INDEX _ExpeditorSite ON Box (_ExpeditorSite);
CREATE INDEX _Destinator ON Box (_Destinator);
CREATE INDEX _DestinatorSite ON Box (_DestinatorSite);
CREATE INDEX _PackingList ON Box (_PackingList);

--
-- Table structure for CacheData
--
DROP TABLE IF EXISTS CacheData;
CREATE TABLE CacheData (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Data LONGTEXT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Catalog
--
DROP TABLE IF EXISTS Catalog;
CREATE TABLE Catalog (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _ItemPerPage INT(11) NOT NULL DEFAULT 0,
  _Page VARCHAR(255) DEFAULT NULL,
  _CadencedOrder INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for CatalogCriteria
--
DROP TABLE IF EXISTS CatalogCriteria;
CREATE TABLE CatalogCriteria (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Property INT(11) NOT NULL DEFAULT 0,
  _DisplayName VARCHAR(255) DEFAULT NULL,
  _Index INT(11) NOT NULL DEFAULT 0,
  _Displayable INT(11) NOT NULL DEFAULT 0,
  _Searchable INT(11) NOT NULL DEFAULT 0,
  _SearchIndex INT(11) NOT NULL DEFAULT 0,
  _Catalog INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Property ON CatalogCriteria (_Property);
CREATE INDEX _Catalog ON CatalogCriteria (_Catalog);

--
-- Table structure for Category
--
DROP TABLE IF EXISTS Category;
CREATE TABLE Category (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Attractivity INT(11) NOT NULL DEFAULT 0,
  _Description VARCHAR(255) DEFAULT NULL,
  _LastModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON Category (_Name);
CREATE INDEX _Attractivity ON Category (_Attractivity);

--
-- Table structure for Chain
--
DROP TABLE IF EXISTS Chain;
CREATE TABLE Chain (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Reference VARCHAR(255) DEFAULT NULL,
  _Owner INT(11) NOT NULL DEFAULT 0,
  _Description VARCHAR(255) DEFAULT NULL,
  _DescriptionCatalog VARCHAR(255) DEFAULT NULL,
  _Type INT(3) NOT NULL DEFAULT 0,
  _AutoAssignTo INT(3) NOT NULL DEFAULT 0,
  _BarCodeType INT(11) NOT NULL DEFAULT 0,
  _SiteTransition INT(11) NOT NULL DEFAULT 0,
  _PivotTask INT(11) NOT NULL DEFAULT 0,
  _PivotDateType INT(11) NOT NULL DEFAULT 0,
  _CommandSequence INT(11) NOT NULL DEFAULT 0,
  _CreatedDate DATETIME DEFAULT NULL,
  _State INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Reference ON Chain (_Reference);
CREATE INDEX _Owner ON Chain (_Owner);
CREATE INDEX _BarCodeType ON Chain (_BarCodeType);
CREATE INDEX _SiteTransition ON Chain (_SiteTransition);
CREATE INDEX _PivotTask ON Chain (_PivotTask);

--
-- Table structure for ChainOperation
--
DROP TABLE IF EXISTS ChainOperation;
CREATE TABLE ChainOperation (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Operation INT(11) NOT NULL DEFAULT 0,
  _Chain INT(11) NOT NULL DEFAULT 0,
  _Order INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Actor ON ChainOperation (_Actor);
CREATE INDEX _Operation ON ChainOperation (_Operation);
CREATE INDEX _Chain ON ChainOperation (_Chain);

--
-- Table structure for ChainTask
--
DROP TABLE IF EXISTS ChainTask;
CREATE TABLE ChainTask (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Order INT(11) NOT NULL DEFAULT 0,
  _Interuptible INT(11) NOT NULL DEFAULT 0,
  _Duration FLOAT NOT NULL DEFAULT 0,
  _DurationType INT(3) NOT NULL DEFAULT 0,
  _KilometerNumber FLOAT NOT NULL DEFAULT 0,
  _Cost DECIMAL(10,2) NOT NULL DEFAULT 0,
  _CostType INT(3) NOT NULL DEFAULT 0,
  _Instructions VARCHAR(255) DEFAULT NULL,
  _TriggerMode INT(3) NOT NULL DEFAULT 0,
  _TriggerDelta FLOAT NOT NULL DEFAULT 0,
  _Task INT(11) NOT NULL DEFAULT 0,
  _ActorSiteTransition INT(11) NOT NULL DEFAULT 0,
  _DepartureInstant INT(11) NOT NULL DEFAULT 0,
  _ArrivalInstant INT(11) NOT NULL DEFAULT 0,
  _Operation INT(11) NOT NULL DEFAULT 0,
  _AutoAlert INT(11) DEFAULT NULL,
  _ProductCommandType INT(11) NOT NULL DEFAULT 0,
  _DepartureActor INT(11) NOT NULL DEFAULT 0,
  _DepartureSite INT(11) NOT NULL DEFAULT 0,
  _ArrivalActor INT(11) NOT NULL DEFAULT 0,
  _ArrivalSite INT(11) NOT NULL DEFAULT 0,
  _WishedDateType INT(3) NOT NULL DEFAULT 0,
  _Delta INT(11) NOT NULL DEFAULT 0,
  _ComponentQuantityRatio INT(1) DEFAULT 0,
  _ActivationPerSupplier INT(1) DEFAULT 0,
  _Component INT(11) NOT NULL DEFAULT 0,
  _ChainToActivate INT(11) NOT NULL DEFAULT 0,
  _RessourceGroup INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Task ON ChainTask (_Task);
CREATE INDEX _ActorSiteTransition ON ChainTask (_ActorSiteTransition);
CREATE INDEX _DepartureInstant ON ChainTask (_DepartureInstant);
CREATE INDEX _ArrivalInstant ON ChainTask (_ArrivalInstant);
CREATE INDEX _Operation ON ChainTask (_Operation);
CREATE INDEX _DepartureActor ON ChainTask (_DepartureActor);
CREATE INDEX _DepartureSite ON ChainTask (_DepartureSite);
CREATE INDEX _ArrivalActor ON ChainTask (_ArrivalActor);
CREATE INDEX _ArrivalSite ON ChainTask (_ArrivalSite);
CREATE INDEX _Component ON ChainTask (_Component);
CREATE INDEX _ChainToActivate ON ChainTask (_ChainToActivate);
CREATE INDEX _RessourceGroup ON ChainTask (_RessourceGroup);

--
-- Table structure for CityName
--
DROP TABLE IF EXISTS CityName;
CREATE TABLE CityName (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Department INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Department ON CityName (_Department);

--
-- Table structure for Command
--
DROP TABLE IF EXISTS Command;
CREATE TABLE Command (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _CommandNo VARCHAR(255) DEFAULT NULL,
  _Type INT(3) NOT NULL DEFAULT 0,
  _CommandDate DATETIME DEFAULT NULL,
  _MerchandiseValue DECIMAL(10,2) NOT NULL DEFAULT 0,
  _AdditionnalGaranties INT(11) DEFAULT NULL,
  _Incoterm INT(11) NOT NULL DEFAULT 0,
  _WishedStartDate DATETIME DEFAULT NULL,
  _WishedEndDate DATETIME DEFAULT NULL,
  _Comment VARCHAR(255) DEFAULT NULL,
  _Expeditor INT(11) NOT NULL DEFAULT 0,
  _Destinator INT(11) NOT NULL DEFAULT 0,
  _ExpeditorSite INT(11) NOT NULL DEFAULT 0,
  _DestinatorSite INT(11) NOT NULL DEFAULT 0,
  _Customer INT(11) NOT NULL DEFAULT 0,
  _SupplierCustomer INT(11) NOT NULL DEFAULT 0,
  _Commercial INT(11) NOT NULL DEFAULT 0,
  _State INT(3) NOT NULL DEFAULT 0,
  _Handing DECIMAL(10,2) NOT NULL DEFAULT 0,
  _HandingByRangePercent DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Port DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Packing DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Insurance DECIMAL(10,2) NOT NULL DEFAULT 0,
  _TotalPriceHT DECIMAL(10,2) NOT NULL DEFAULT 0,
  _TotalPriceTTC DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Processed INT(11) NOT NULL DEFAULT 0,
  _Installment DECIMAL(10,2) NOT NULL DEFAULT 0,
  _CustomerRemExcep DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Duration TIME DEFAULT NULL,
  _Cadenced INT(1) DEFAULT 0,
  _Closed INT(1) DEFAULT 0,
  _IsEstimate INT(1) DEFAULT 0,
  _CommandExpeditionDetail INT(11) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  _ActorBankDetail INT(11) NOT NULL DEFAULT 0,
  _InstallmentBank INT(11) NOT NULL DEFAULT 0,
  _Command INT(11) NOT NULL DEFAULT 0,
  _ParentCommand INT(11) NOT NULL DEFAULT 0,
  _ProjectManager INT(11) NOT NULL DEFAULT 0,
  _WarrantyEndDate DATETIME DEFAULT NULL,
  _SoloFly INT(1) DEFAULT 0,
  _IsWishedInstructor INT(1) DEFAULT 0,
  _Instructor INT(11) NOT NULL DEFAULT 0,
  _AeroConcreteProduct INT(11) NOT NULL DEFAULT 0,
  _FlyType INT(11) NOT NULL DEFAULT 0,
  _InputationNo VARCHAR(255) DEFAULT NULL,
  _DeliveryPayment DECIMAL(10,2) DEFAULT NULL,
  _DateType INT(11) NOT NULL DEFAULT 0,
  _Chain INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Incoterm ON Command (_Incoterm);
CREATE INDEX _Expeditor ON Command (_Expeditor);
CREATE INDEX _Destinator ON Command (_Destinator);
CREATE INDEX _ExpeditorSite ON Command (_ExpeditorSite);
CREATE INDEX _DestinatorSite ON Command (_DestinatorSite);
CREATE INDEX _Customer ON Command (_Customer);
CREATE INDEX _SupplierCustomer ON Command (_SupplierCustomer);
CREATE INDEX _Commercial ON Command (_Commercial);
CREATE INDEX _CommandExpeditionDetail ON Command (_CommandExpeditionDetail);
CREATE INDEX _Currency ON Command (_Currency);
CREATE INDEX _ActorBankDetail ON Command (_ActorBankDetail);
CREATE INDEX _InstallmentBank ON Command (_InstallmentBank);
CREATE INDEX _Command ON Command (_Command);
CREATE INDEX _ParentCommand ON Command (_ParentCommand);
CREATE INDEX _ProjectManager ON Command (_ProjectManager);
CREATE INDEX _Instructor ON Command (_Instructor);
CREATE INDEX _AeroConcreteProduct ON Command (_AeroConcreteProduct);
CREATE INDEX _FlyType ON Command (_FlyType);
CREATE INDEX _Chain ON Command (_Chain);

--
-- Table structure for CommandExpeditionDetail
--
DROP TABLE IF EXISTS CommandExpeditionDetail;
CREATE TABLE CommandExpeditionDetail (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _LoadingPort VARCHAR(255) DEFAULT NULL,
  _Shipment INT(3) DEFAULT NULL,
  _CustomerCommandNo VARCHAR(255) DEFAULT NULL,
  _DestinatorStore VARCHAR(255) DEFAULT NULL,
  _DestinatorRange VARCHAR(255) DEFAULT NULL,
  _ReservationNo VARCHAR(255) DEFAULT NULL,
  _Season VARCHAR(255) DEFAULT NULL,
  _Comment TEXT DEFAULT NULL,
  _Deal VARCHAR(255) DEFAULT NULL,
  _AirwayBill VARCHAR(255) DEFAULT NULL,
  _PackingList VARCHAR(255) DEFAULT NULL,
  _SupplierCode VARCHAR(255) DEFAULT NULL,
  _Weight DECIMAL(10,2) NOT NULL DEFAULT 0,
  _NumberOfContainer INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for CommandItem
--
DROP TABLE IF EXISTS CommandItem;
CREATE TABLE CommandItem (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _ActivatedChain INT(11) NOT NULL DEFAULT 0,
  _Command INT(11) NOT NULL DEFAULT 0,
  _Width FLOAT NOT NULL DEFAULT 0,
  _Height FLOAT NOT NULL DEFAULT 0,
  _Length FLOAT NOT NULL DEFAULT 0,
  _Weight FLOAT NOT NULL DEFAULT 0,
  _Quantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _Gerbability INT(11) NOT NULL DEFAULT 0,
  _MasterDimension INT(3) NOT NULL DEFAULT 0,
  _Comment VARCHAR(255) DEFAULT NULL,
  _Handing VARCHAR(10) DEFAULT '0',
  _TVA INT(11) NOT NULL DEFAULT 0,
  _PriceHT DECIMAL(10,2) NOT NULL DEFAULT 0,
  _WishedDate DATETIME DEFAULT NULL,
  _Product INT(11) NOT NULL DEFAULT 0,
  _PackagingUnitQuantity INT(11) DEFAULT NULL,
  _ActivatedMovement INT(11) NOT NULL DEFAULT 0,
  _Promotion INT(11) NOT NULL DEFAULT 0,
  _Prestation INT(11) NOT NULL DEFAULT 0,
  _UnitPriceHT DECIMAL(10,3) NOT NULL DEFAULT 0,
  _QuantityForPrestationCost DECIMAL(10,2) NOT NULL DEFAULT 0,
  _CostType INT(11) DEFAULT NULL,
  _CoverType INT(11) NOT NULL DEFAULT 0,
  _ProductType INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ActivatedChain ON CommandItem (_ActivatedChain);
CREATE INDEX _Command ON CommandItem (_Command);
CREATE INDEX _TVA ON CommandItem (_TVA);
CREATE INDEX _Product ON CommandItem (_Product);
CREATE INDEX _ActivatedMovement ON CommandItem (_ActivatedMovement);
CREATE INDEX _Promotion ON CommandItem (_Promotion);
CREATE INDEX _Prestation ON CommandItem (_Prestation);
CREATE INDEX _CoverType ON CommandItem (_CoverType);
CREATE INDEX _ProductType ON CommandItem (_ProductType);

--
-- Table structure for CommunicationModality
--
DROP TABLE IF EXISTS CommunicationModality;
CREATE TABLE CommunicationModality (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Phone VARCHAR(255) DEFAULT NULL,
  _Fax VARCHAR(255) DEFAULT NULL,
  _Email VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Component
--
DROP TABLE IF EXISTS Component;
CREATE TABLE Component (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Level INT(11) DEFAULT NULL,
  _Quantity DECIMAL(10,3) DEFAULT NULL,
  _PercentWasted DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Nomenclature INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Parent INT(11) NOT NULL DEFAULT 0,
  _ComponentGroup INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Nomenclature ON Component (_Nomenclature);
CREATE INDEX _Product ON Component (_Product);
CREATE INDEX _Parent ON Component (_Parent);
CREATE INDEX _ComponentGroup ON Component (_ComponentGroup);

--
-- Table structure for ComponentGroup
--
DROP TABLE IF EXISTS ComponentGroup;
CREATE TABLE ComponentGroup (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Nomenclature INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Nomenclature ON ComponentGroup (_Nomenclature);

--
-- Table structure for ConcreteComponent
--
DROP TABLE IF EXISTS ConcreteComponent;
CREATE TABLE ConcreteComponent (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Quantity DECIMAL(10,3) NOT NULL DEFAULT 1,
  _Parent INT(11) NOT NULL DEFAULT 0,
  _ConcreteProduct INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Parent ON ConcreteComponent (_Parent);
CREATE INDEX _ConcreteProduct ON ConcreteComponent (_ConcreteProduct);

--
-- Table structure for ConcreteProduct
--
DROP TABLE IF EXISTS ConcreteProduct;
CREATE TABLE ConcreteProduct (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _Immatriculation VARCHAR(255) DEFAULT NULL,
  _SerialNumber VARCHAR(255) DEFAULT NULL,
  _Weight DECIMAL(10,2) NOT NULL DEFAULT 0,
  _BirthDate DATETIME DEFAULT NULL,
  _OnServiceDate DATETIME DEFAULT NULL,
  _EndOfLifeDate DATETIME DEFAULT NULL,
  _Owner INT(11) NOT NULL DEFAULT 0,
  _OnCondition INT(1) DEFAULT 0,
  _WarrantyBeginDate DATETIME DEFAULT NULL,
  _WarrantyEndDate DATETIME DEFAULT NULL,
  _BuyingPriceHT DECIMAL(10,2) NOT NULL DEFAULT 0,
  _SellingPriceHT DECIMAL(10,2) NOT NULL DEFAULT 0,
  _State INT(3) NOT NULL DEFAULT 0,
  _ConformityNumber VARCHAR(255) DEFAULT NULL,
  _FME INT(1) DEFAULT 0,
  _RealHourSinceNew DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealHourSinceOverall DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealHourSinceRepared DECIMAL(10,2) NOT NULL DEFAULT 0,
  _VirtualHourSinceNew DECIMAL(10,2) NOT NULL DEFAULT 0,
  _VirtualHourSinceOverall DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Active INT(1) DEFAULT 1,
  _Component INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _WeeklyPlanning INT(11) NOT NULL DEFAULT 0,
  _MaxWeightOnTakeOff DECIMAL(10,2) NOT NULL DEFAULT 0,
  _MaxWeightBySeat DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealLandingSinceNew DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealLandingSinceOverall DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealLandingSinceRepared DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealCycleSinceNew DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealCycleSinceOverall DECIMAL(10,2) NOT NULL DEFAULT 0,
  _RealCycleSinceRepared DECIMAL(10,2) NOT NULL DEFAULT 0,
  _TankCapacity DECIMAL(10,2) NOT NULL DEFAULT 0,
  _TankUnitType INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Owner ON ConcreteProduct (_Owner);
CREATE INDEX _Component ON ConcreteProduct (_Component);
CREATE INDEX _Product ON ConcreteProduct (_Product);
CREATE INDEX _WeeklyPlanning ON ConcreteProduct (_WeeklyPlanning);

--
-- Table structure for Contact
--
DROP TABLE IF EXISTS Contact;
CREATE TABLE Contact (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Phone VARCHAR(255) DEFAULT NULL,
  _Fax VARCHAR(255) DEFAULT NULL,
  _Mobile VARCHAR(255) DEFAULT NULL,
  _Email VARCHAR(255) DEFAULT NULL,
  _CommunicationModality INT(11) NOT NULL DEFAULT 0,
  _Role INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _CommunicationModality ON Contact (_CommunicationModality);
CREATE INDEX _Role ON Contact (_Role);

--
-- Table structure for ContactRole
--
DROP TABLE IF EXISTS ContactRole;
CREATE TABLE ContactRole (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Container
--
DROP TABLE IF EXISTS Container;
CREATE TABLE Container (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Reference VARCHAR(255) DEFAULT NULL,
  _SupplierReference VARCHAR(255) DEFAULT NULL,
  _CoverType INT(11) NOT NULL DEFAULT 0,
  _CoverProperty INT(3) NOT NULL DEFAULT 0,
  _CoverKind INT(3) NOT NULL DEFAULT 0,
  _CoverGroup INT(3) NOT NULL DEFAULT 0,
  _MaxAuthorizedWeight FLOAT NOT NULL DEFAULT 0,
  _Weight FLOAT NOT NULL DEFAULT 0,
  _ExternalLength FLOAT NOT NULL DEFAULT 0,
  _ExternalWidth FLOAT NOT NULL DEFAULT 0,
  _ExternalHeight FLOAT NOT NULL DEFAULT 0,
  _InternalLength FLOAT NOT NULL DEFAULT 0,
  _InternalWidth FLOAT NOT NULL DEFAULT 0,
  _InternalHeight FLOAT NOT NULL DEFAULT 0,
  _Volume FLOAT NOT NULL DEFAULT 0,
  _RecipientWeight FLOAT NOT NULL DEFAULT 0,
  _AssemblyKind INT(11) NOT NULL DEFAULT 0,
  _ExternalContainer VARCHAR(255) DEFAULT NULL,
  _InternalContainer VARCHAR(255) DEFAULT NULL,
  _Protection VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _CoverType ON Container (_CoverType);

--
-- Table structure for CostRange
--
DROP TABLE IF EXISTS CostRange;
CREATE TABLE CostRange (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Cost DECIMAL(10,3) NOT NULL DEFAULT 0,
  _CostType INT(3) NOT NULL DEFAULT 0,
  _BeginRange DECIMAL(10,2) NOT NULL DEFAULT 0,
  _EndRange DECIMAL(10,2) NOT NULL DEFAULT 0,
  _DepartureZone INT(11) NOT NULL DEFAULT 0,
  _ArrivalZone INT(11) NOT NULL DEFAULT 0,
  _Store INT(11) NOT NULL DEFAULT 0,
  _ProductType INT(11) NOT NULL DEFAULT 0,
  _UnitType INT(11) NOT NULL DEFAULT 0,
  _Prestation INT(11) NOT NULL DEFAULT 0,
  _PrestationCost INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _DepartureZone ON CostRange (_DepartureZone);
CREATE INDEX _ArrivalZone ON CostRange (_ArrivalZone);
CREATE INDEX _Store ON CostRange (_Store);
CREATE INDEX _ProductType ON CostRange (_ProductType);
CREATE INDEX _UnitType ON CostRange (_UnitType);
CREATE INDEX _Prestation ON CostRange (_Prestation);
CREATE INDEX _PrestationCost ON CostRange (_PrestationCost);

--
-- Table structure for Country
--
DROP TABLE IF EXISTS Country;
CREATE TABLE Country (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _InterCountryCode VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for CountryCity
--
DROP TABLE IF EXISTS CountryCity;
CREATE TABLE CountryCity (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Zip INT(11) NOT NULL DEFAULT 0,
  _Country INT(11) NOT NULL DEFAULT 0,
  _CityName INT(11) NOT NULL DEFAULT 0,
  _Zone INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Zip ON CountryCity (_Zip);
CREATE INDEX _Country ON CountryCity (_Country);
CREATE INDEX _CityName ON CountryCity (_CityName);
CREATE INDEX _Zone ON CountryCity (_Zone);

--
-- Table structure for CoverType
--
DROP TABLE IF EXISTS CoverType;
CREATE TABLE CoverType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _UnitType INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _UnitType ON CoverType (_UnitType);

--
-- Table structure for CronTask
--
DROP TABLE IF EXISTS CronTask;
CREATE TABLE CronTask (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _ScriptName VARCHAR(255) DEFAULT NULL,
  _DayOfMonth INT(2) NOT NULL DEFAULT 0,
  _DayOfWeek INT(2) NOT NULL DEFAULT -1,
  _HourOfDay INT(2) NOT NULL DEFAULT 0,
  _Active INT(1) DEFAULT 1,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Currency
--
DROP TABLE IF EXISTS Currency;
CREATE TABLE Currency (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(50) DEFAULT NULL,
  _ShortName VARCHAR(10) DEFAULT NULL,
  _Symbol VARCHAR(10) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for CurrencyConverter
--
DROP TABLE IF EXISTS CurrencyConverter;
CREATE TABLE CurrencyConverter (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _FromCurrency INT(11) NOT NULL DEFAULT 0,
  _ToCurrency INT(11) NOT NULL DEFAULT 0,
  _BeginDate DATE DEFAULT NULL,
  _EndDate DATE DEFAULT NULL,
  _Rate DECIMAL(10,6) NOT NULL DEFAULT 1,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _FromCurrency ON CurrencyConverter (_FromCurrency);
CREATE INDEX _ToCurrency ON CurrencyConverter (_ToCurrency);

--
-- Table structure for CustomerAttractivity
--
DROP TABLE IF EXISTS CustomerAttractivity;
CREATE TABLE CustomerAttractivity (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Level INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for CustomerFrequency
--
DROP TABLE IF EXISTS CustomerFrequency;
CREATE TABLE CustomerFrequency (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Frequency INT(11) DEFAULT NULL,
  _Type INT(3) NOT NULL DEFAULT 1,
  _BeginDate DATE DEFAULT NULL,
  _EndDate DATE DEFAULT NULL,
  _Attractivity INT(11) NOT NULL DEFAULT 0,
  _Potential INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Attractivity ON CustomerFrequency (_Attractivity);
CREATE INDEX _Potential ON CustomerFrequency (_Potential);

--
-- Table structure for CustomerPotential
--
DROP TABLE IF EXISTS CustomerPotential;
CREATE TABLE CustomerPotential (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _UnitType INT(3) NOT NULL DEFAULT 1,
  _MinValue FLOAT DEFAULT NULL,
  _MaxValue FLOAT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for CustomerProperties
--
DROP TABLE IF EXISTS CustomerProperties;
CREATE TABLE CustomerProperties (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _NAFCode VARCHAR(255) DEFAULT NULL,
  _PriorityLevel INT(3) NOT NULL DEFAULT 1,
  _Potential INT(11) NOT NULL DEFAULT 0,
  _Situation INT(11) NOT NULL DEFAULT 0,
  _PersonalFrequency INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Potential ON CustomerProperties (_Potential);
CREATE INDEX _Situation ON CustomerProperties (_Situation);
CREATE INDEX _PersonalFrequency ON CustomerProperties (_PersonalFrequency);

--
-- Table structure for CustomerSituation
--
DROP TABLE IF EXISTS CustomerSituation;
CREATE TABLE CustomerSituation (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Type INT(3) NOT NULL DEFAULT 1,
  _InactivityDelay INT(11) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for DailyPlanning
--
DROP TABLE IF EXISTS DailyPlanning;
CREATE TABLE DailyPlanning (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Start TIME DEFAULT NULL,
  _Pause TIME DEFAULT NULL,
  _Restart TIME DEFAULT NULL,
  _End TIME DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for DangerousProductGroup
--
DROP TABLE IF EXISTS DangerousProductGroup;
CREATE TABLE DangerousProductGroup (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Code VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for DangerousProductLetter
--
DROP TABLE IF EXISTS DangerousProductLetter;
CREATE TABLE DangerousProductLetter (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Code VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for DangerousProductType
--
DROP TABLE IF EXISTS DangerousProductType;
CREATE TABLE DangerousProductType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Class INT(3) DEFAULT NULL,
  _Letter INT(11) NOT NULL DEFAULT 0,
  _Group INT(11) NOT NULL DEFAULT 0,
  _Number INT(3) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Letter ON DangerousProductType (_Letter);
CREATE INDEX _Group ON DangerousProductType (_Group);

--
-- Table structure for Department
--
DROP TABLE IF EXISTS Department;
CREATE TABLE Department (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Number VARCHAR(255) DEFAULT NULL,
  _State INT(11) NOT NULL DEFAULT 0,
  _Country INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _State ON Department (_State);
CREATE INDEX _Country ON Department (_Country);

--
-- Table structure for DocumentModel
--
DROP TABLE IF EXISTS DocumentModel;
CREATE TABLE DocumentModel (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Footer TEXT DEFAULT NULL,
  _LogoType INT(3) DEFAULT NULL,
  _DocType VARCHAR(255) DEFAULT NULL,
  _Default INT(1) DEFAULT 0,
  _DisplayDuplicata INT(1) DEFAULT 1,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Number INT(11) NOT NULL DEFAULT 1,
  _DisplayTotalWeight INT(1) DEFAULT 1,
  _DisplayProductDetail INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Actor ON DocumentModel (_Actor);

--
-- Table structure for DocumentModelProperty
--
DROP TABLE IF EXISTS DocumentModelProperty;
CREATE TABLE DocumentModelProperty (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _PropertyType INT(3) NOT NULL DEFAULT 0,
  _Property INT(11) NOT NULL DEFAULT 0,
  _Order INT(11) NOT NULL DEFAULT 0,
  _DocumentModel INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Property ON DocumentModelProperty (_Property);
CREATE INDEX _DocumentModel ON DocumentModelProperty (_DocumentModel);

--
-- Table structure for Entity
--
DROP TABLE IF EXISTS Entity;
CREATE TABLE Entity (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Public INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for ExecutedMovement
--
DROP TABLE IF EXISTS ExecutedMovement;
CREATE TABLE ExecutedMovement (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _StartDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _Type INT(11) NOT NULL DEFAULT 0,
  _State INT(3) NOT NULL DEFAULT 0,
  _Comment VARCHAR(255) DEFAULT NULL,
  _RealProduct INT(11) NOT NULL DEFAULT 0,
  _RealQuantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _ActivatedMovement INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Type ON ExecutedMovement (_Type);
CREATE INDEX _RealProduct ON ExecutedMovement (_RealProduct);
CREATE INDEX _ActivatedMovement ON ExecutedMovement (_ActivatedMovement);

--
-- Table structure for Flow
--
DROP TABLE IF EXISTS Flow;
CREATE TABLE Flow (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Handing VARCHAR(10) DEFAULT '0',
  _Number VARCHAR(255) DEFAULT NULL,
  _PieceNo INT(11) DEFAULT NULL,
  _FlowType INT(11) NOT NULL DEFAULT 0,
  _TotalTTC DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Paid DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  _PaymentDate DATETIME DEFAULT NULL,
  _EditionDate DATETIME DEFAULT NULL,
  _ActorBankDetail INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _FlowType ON Flow (_FlowType);
CREATE INDEX _Currency ON Flow (_Currency);
CREATE INDEX _ActorBankDetail ON Flow (_ActorBankDetail);

--
-- Table structure for FlowCategory
--
DROP TABLE IF EXISTS FlowCategory;
CREATE TABLE FlowCategory (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Parent INT(11) NOT NULL DEFAULT 0,
  _DisplayOrder INT(11) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON FlowCategory (_Name);
CREATE INDEX _Parent ON FlowCategory (_Parent);

--
-- Table structure for FlowItem
--
DROP TABLE IF EXISTS FlowItem;
CREATE TABLE FlowItem (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _TotalHT DECIMAL(10,2) NOT NULL DEFAULT 0,
  _TVA INT(11) NOT NULL DEFAULT 0,
  _Handing VARCHAR(10) DEFAULT '0',
  _Flow INT(11) NOT NULL DEFAULT 0,
  _Type INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _TVA ON FlowItem (_TVA);
CREATE INDEX _Flow ON FlowItem (_Flow);
CREATE INDEX _Type ON FlowItem (_Type);

--
-- Table structure for FlowType
--
DROP TABLE IF EXISTS FlowType;
CREATE TABLE FlowType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _ActorBankDetail INT(11) NOT NULL DEFAULT 0,
  _Type INT(3) NOT NULL DEFAULT 0,
  _FlowCategory INT(11) NOT NULL DEFAULT 0,
  _AccountingType INT(11) NOT NULL DEFAULT 0,
  _ThirdParty INT(11) NOT NULL DEFAULT 0,
  _InvoiceType INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ActorBankDetail ON FlowType (_ActorBankDetail);
CREATE INDEX _FlowCategory ON FlowType (_FlowCategory);
CREATE INDEX _AccountingType ON FlowType (_AccountingType);
CREATE INDEX _ThirdParty ON FlowType (_ThirdParty);

--
-- Table structure for FlowTypeItem
--
DROP TABLE IF EXISTS FlowTypeItem;
CREATE TABLE FlowTypeItem (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _TVA INT(11) NOT NULL DEFAULT 0,
  _FlowType INT(11) NOT NULL DEFAULT 0,
  _BreakdownPart INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _TVA ON FlowTypeItem (_TVA);
CREATE INDEX _FlowType ON FlowTypeItem (_FlowType);

--
-- Table structure for FlyType
--
DROP TABLE IF EXISTS FlyType;
CREATE TABLE FlyType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON FlyType (_Name);

--
-- Table structure for ForecastFlow
--
DROP TABLE IF EXISTS ForecastFlow;
CREATE TABLE ForecastFlow (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Description VARCHAR(255) DEFAULT NULL,
  _Amount DECIMAL(10,2) DEFAULT NULL,
  _BeginDate DATE DEFAULT NULL,
  _EndDate DATE DEFAULT NULL,
  _Active INT(1) DEFAULT 1,
  _Currency INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Currency ON ForecastFlow (_Currency);

--
-- Table structure for FormModel
--
DROP TABLE IF EXISTS FormModel;
CREATE TABLE FormModel (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Activ INT(1) DEFAULT 1,
  _ActionType INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for ForwardingFormPacking
--
DROP TABLE IF EXISTS ForwardingFormPacking;
CREATE TABLE ForwardingFormPacking (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _ForwardingForm INT(11) NOT NULL DEFAULT 0,
  _CoverType INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Quantity INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ForwardingForm ON ForwardingFormPacking (_ForwardingForm);
CREATE INDEX _CoverType ON ForwardingFormPacking (_CoverType);
CREATE INDEX _Product ON ForwardingFormPacking (_Product);

--
-- Table structure for HandingByRange
--
DROP TABLE IF EXISTS HandingByRange;
CREATE TABLE HandingByRange (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Percent DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Minimum DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Maximum DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  _Category INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Currency ON HandingByRange (_Currency);
CREATE INDEX _Category ON HandingByRange (_Category);

--
-- Table structure for HelpPage
--
DROP TABLE IF EXISTS HelpPage;
CREATE TABLE HelpPage (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _FileName VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  _Body LONGTEXT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for I18nString
--
DROP TABLE IF EXISTS I18nString;
CREATE TABLE I18nString (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _StringValue_en_GB TEXT DEFAULT NULL,
  _StringValue_fr_FR TEXT DEFAULT NULL,
  _StringValue_de_DE TEXT DEFAULT NULL,
  _StringValue_nl_NL TEXT DEFAULT NULL,
  _StringValue_tr_TR TEXT DEFAULT NULL,
  _StringValue_pl_PL TEXT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Incoterm
--
DROP TABLE IF EXISTS Incoterm;
CREATE TABLE Incoterm (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Code VARCHAR(5) DEFAULT NULL,
  _Label VARCHAR(255) DEFAULT NULL,
  _Description TEXT DEFAULT NULL,
  _TransportType INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Inventory
--
DROP TABLE IF EXISTS Inventory;
CREATE TABLE Inventory (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _BeginDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _UserAccount INT(11) NOT NULL DEFAULT 0,
  _UserName VARCHAR(255) DEFAULT NULL,
  _StorageSite INT(11) NOT NULL DEFAULT 0,
  _StorageSiteName VARCHAR(255) DEFAULT NULL,
  _Store INT(11) NOT NULL DEFAULT 0,
  _StoreName VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _UserAccount ON Inventory (_UserAccount);
CREATE INDEX _StorageSite ON Inventory (_StorageSite);
CREATE INDEX _Store ON Inventory (_Store);

--
-- Table structure for InventoryDetail
--
DROP TABLE IF EXISTS InventoryDetail;
CREATE TABLE InventoryDetail (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _ProductReference VARCHAR(255) DEFAULT NULL,
  _Currency VARCHAR(255) DEFAULT NULL,
  _BuyingPriceHT DECIMAL(10,2) DEFAULT NULL,
  _Location INT(11) NOT NULL DEFAULT 0,
  _LocationName VARCHAR(255) DEFAULT NULL,
  _Quantity DECIMAL(10,3) DEFAULT NULL,
  _Inventory INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Product ON InventoryDetail (_Product);
CREATE INDEX _Location ON InventoryDetail (_Location);
CREATE INDEX _Inventory ON InventoryDetail (_Inventory);

--
-- Table structure for InvoiceItem
--
DROP TABLE IF EXISTS InvoiceItem;
CREATE TABLE InvoiceItem (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Reference VARCHAR(255) DEFAULT NULL,
  _AssociatedReference VARCHAR(255) DEFAULT NULL,
  _Handing VARCHAR(255) DEFAULT NULL,
  _Quantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _TVA INT(11) NOT NULL DEFAULT 0,
  _UnitPriceHT DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Invoice INT(11) NOT NULL DEFAULT 0,
  _ActivatedMovement INT(11) NOT NULL DEFAULT 0,
  _Prestation INT(11) NOT NULL DEFAULT 0,
  _PrestationPeriodicity INT(11) NOT NULL DEFAULT 0,
  _PrestationCost DECIMAL(10,3) NOT NULL DEFAULT 0,
  _QuantityForPrestationCost DECIMAL(10,2) NOT NULL DEFAULT 0,
  _CostType INT(11) NOT NULL DEFAULT -1,
  _ProductType INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _TVA ON InvoiceItem (_TVA);
CREATE INDEX _Invoice ON InvoiceItem (_Invoice);
CREATE INDEX _ActivatedMovement ON InvoiceItem (_ActivatedMovement);
CREATE INDEX _Prestation ON InvoiceItem (_Prestation);
CREATE INDEX _ProductType ON InvoiceItem (_ProductType);

--
-- Table structure for InvoicePayment
--
DROP TABLE IF EXISTS InvoicePayment;
CREATE TABLE InvoicePayment (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _PriceTTC DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Invoice INT(11) NOT NULL DEFAULT 0,
  _Payment INT(11) NOT NULL DEFAULT 0,
  _ToHave INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Invoice ON InvoicePayment (_Invoice);
CREATE INDEX _Payment ON InvoicePayment (_Payment);
CREATE INDEX _ToHave ON InvoicePayment (_ToHave);

--
-- Table structure for Job
--
DROP TABLE IF EXISTS Job;
CREATE TABLE Job (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for LEMConcreteProduct
--
DROP TABLE IF EXISTS LEMConcreteProduct;
CREATE TABLE LEMConcreteProduct (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _LocationExecutedMovement INT(11) NOT NULL DEFAULT 0,
  _ConcreteProduct INT(11) NOT NULL DEFAULT 0,
  _Quantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _Cancelled INT(3) NOT NULL DEFAULT 0,
  _CancelledLEMConcreteProduct INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _LocationExecutedMovement ON LEMConcreteProduct (_LocationExecutedMovement);
CREATE INDEX _ConcreteProduct ON LEMConcreteProduct (_ConcreteProduct);
CREATE INDEX _CancelledLEMConcreteProduct ON LEMConcreteProduct (_CancelledLEMConcreteProduct);

--
-- Table structure for Licence
--
DROP TABLE IF EXISTS Licence;
CREATE TABLE Licence (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Number VARCHAR(255) DEFAULT NULL,
  _BeginDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _Duration INT(3) NOT NULL DEFAULT 0,
  _DurationType INT(1) NOT NULL DEFAULT 0,
  _AlertDateType INT(11) NOT NULL DEFAULT 0,
  _DelayForAlert INT(3) NOT NULL DEFAULT 0,
  _ToBeChecked INT(3) NOT NULL DEFAULT 0,
  _LicenceType INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _LicenceType ON Licence (_LicenceType);

--
-- Table structure for LicenceType
--
DROP TABLE IF EXISTS LicenceType;
CREATE TABLE LicenceType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON LicenceType (_Name);

--
-- Table structure for LinkQuestionAnswerModel
--
DROP TABLE IF EXISTS LinkQuestionAnswerModel;
CREATE TABLE LinkQuestionAnswerModel (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _AnswerOrder INT(11) DEFAULT NULL,
  _AnswerModel INT(11) NOT NULL DEFAULT 0,
  _Question INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _AnswerModel ON LinkQuestionAnswerModel (_AnswerModel);
CREATE INDEX _Question ON LinkQuestionAnswerModel (_Question);

--
-- Table structure for LinkFormModelParagraphModel
--
DROP TABLE IF EXISTS LinkFormModelParagraphModel;
CREATE TABLE LinkFormModelParagraphModel (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _ParagraphOrder INT(11) DEFAULT NULL,
  _ParagraphModel INT(11) NOT NULL DEFAULT 0,
  _FormModel INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ParagraphModel ON LinkFormModelParagraphModel (_ParagraphModel);
CREATE INDEX _FormModel ON LinkFormModelParagraphModel (_FormModel);

--
-- Table structure for LinkParagraphModelQuestion
--
DROP TABLE IF EXISTS LinkParagraphModelQuestion;
CREATE TABLE LinkParagraphModelQuestion (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _QuestionOrder INT(11) DEFAULT NULL,
  _ParagraphModel INT(11) NOT NULL DEFAULT 0,
  _Question INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ParagraphModel ON LinkParagraphModelQuestion (_ParagraphModel);
CREATE INDEX _Question ON LinkParagraphModelQuestion (_Question);

--
-- Table structure for Location
--
DROP TABLE IF EXISTS Location;
CREATE TABLE Location (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Customs INT(1) DEFAULT 0,
  _Store INT(11) NOT NULL DEFAULT 0,
  _Activated INT(1) DEFAULT 1,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Store ON Location (_Store);

--
-- Table structure for LocationConcreteProduct
--
DROP TABLE IF EXISTS LocationConcreteProduct;
CREATE TABLE LocationConcreteProduct (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _ConcreteProduct INT(11) NOT NULL DEFAULT 0,
  _Location INT(11) NOT NULL DEFAULT 0,
  _Quantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ConcreteProduct ON LocationConcreteProduct (_ConcreteProduct);
CREATE INDEX _Location ON LocationConcreteProduct (_Location);

--
-- Table structure for LocationExecutedMovement
--
DROP TABLE IF EXISTS LocationExecutedMovement;
CREATE TABLE LocationExecutedMovement (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Quantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _PrestationFactured INT(1) DEFAULT 0,
  _TransportPrestationFactured INT(1) DEFAULT 0,
  _InvoicePrestation INT(11) NOT NULL DEFAULT 0,
  _Date DATETIME DEFAULT NULL,
  _ExecutedMovement INT(11) NOT NULL DEFAULT 0,
  _Location INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Cancelled INT(3) NOT NULL DEFAULT 0,
  _CancelledMovement INT(11) NOT NULL DEFAULT 0,
  _ForwardingForm INT(11) NOT NULL DEFAULT 0,
  _InvoiceItem INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _InvoicePrestation ON LocationExecutedMovement (_InvoicePrestation);
CREATE INDEX _ExecutedMovement ON LocationExecutedMovement (_ExecutedMovement);
CREATE INDEX _Location ON LocationExecutedMovement (_Location);
CREATE INDEX _Product ON LocationExecutedMovement (_Product);
CREATE INDEX _CancelledMovement ON LocationExecutedMovement (_CancelledMovement);
CREATE INDEX _ForwardingForm ON LocationExecutedMovement (_ForwardingForm);
CREATE INDEX _InvoiceItem ON LocationExecutedMovement (_InvoiceItem);

--
-- Table structure for LocationProductQuantities
--
DROP TABLE IF EXISTS LocationProductQuantities;
CREATE TABLE LocationProductQuantities (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Location INT(11) NOT NULL DEFAULT 0,
  _RealQuantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _Activated INT(1) DEFAULT 1,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Product ON LocationProductQuantities (_Product);
CREATE INDEX _Location ON LocationProductQuantities (_Location);

--
-- Table structure for Manual
--
DROP TABLE IF EXISTS Manual;
CREATE TABLE Manual (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _FrFile VARCHAR(255) DEFAULT NULL,
  _EnFile VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for MimeType
--
DROP TABLE IF EXISTS MimeType;
CREATE TABLE MimeType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Extension VARCHAR(255) DEFAULT NULL,
  _ContentType VARCHAR(255) DEFAULT NULL,
  _DisplayName VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for MiniAmountToOrder
--
DROP TABLE IF EXISTS MiniAmountToOrder;
CREATE TABLE MiniAmountToOrder (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Category INT(11) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Category ON MiniAmountToOrder (_Category);
CREATE INDEX _Currency ON MiniAmountToOrder (_Currency);

--
-- Table structure for MovementType
--
DROP TABLE IF EXISTS MovementType;
CREATE TABLE MovementType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Foreseeable INT(1) NOT NULL DEFAULT 0,
  _EntrieExit INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Nomenclature
--
DROP TABLE IF EXISTS Nomenclature;
CREATE TABLE Nomenclature (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Version VARCHAR(255) DEFAULT NULL,
  _BeginDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _Buildable INT(1) DEFAULT 1,
  _Product INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Product ON Nomenclature (_Product);

--
-- Table structure for OccupiedLocation
--
DROP TABLE IF EXISTS OccupiedLocation;
CREATE TABLE OccupiedLocation (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _CreationDate DATE DEFAULT NULL,
  _ValidityDate DATE DEFAULT NULL,
  _InvoiceItem INT(11) NOT NULL DEFAULT 0,
  _Location INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Quantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _InvoiceItem ON OccupiedLocation (_InvoiceItem);
CREATE INDEX _Location ON OccupiedLocation (_Location);
CREATE INDEX _Product ON OccupiedLocation (_Product);

--
-- Table structure for Operation
--
DROP TABLE IF EXISTS Operation;
CREATE TABLE Operation (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Symbol VARCHAR(255) DEFAULT NULL,
  _FrontTolerance TIME DEFAULT NULL,
  _EndTolerance TIME DEFAULT NULL,
  _TotalTolerance TIME DEFAULT NULL,
  _IsConcreteProductNeeded INT(1) DEFAULT 0,
  _Type INT(3) NOT NULL DEFAULT 0,
  _Prestation INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Symbol ON Operation (_Symbol);
CREATE INDEX _Prestation ON Operation (_Prestation);

--
-- Table structure for ParagraphModel
--
DROP TABLE IF EXISTS ParagraphModel;
CREATE TABLE ParagraphModel (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Title VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Title ON ParagraphModel (_Title);

--
-- Table structure for Payment
--
DROP TABLE IF EXISTS Payment;
CREATE TABLE Payment (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Date DATETIME DEFAULT NULL,
  _Modality INT(11) NOT NULL DEFAULT 0,
  _Reference VARCHAR(255) DEFAULT NULL,
  _TotalPriceTTC DECIMAL(10,2) NOT NULL DEFAULT 0,
  _CancellationDate DATETIME DEFAULT NULL,
  _ActorBankDetail INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ActorBankDetail ON Payment (_ActorBankDetail);

--
-- Table structure for PDFDocument
--
DROP TABLE IF EXISTS PDFDocument;
CREATE TABLE PDFDocument (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Data LONGTEXT DEFAULT NULL,
  _AbstractDocument INT(None) NOT NULL DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Prestation
--
DROP TABLE IF EXISTS Prestation;
CREATE TABLE Prestation (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Type INT(3) NOT NULL DEFAULT 0,
  _Operation INT(11) NOT NULL DEFAULT 0,
  _TVA INT(11) NOT NULL DEFAULT 0,
  _Facturable INT(1) DEFAULT true,
  _Active INT(1) DEFAULT true,
  _Periodicity INT(3) NOT NULL DEFAULT 1,
  _Potential DECIMAL(10,2) NOT NULL DEFAULT 0,
  _PotentialDate DATETIME DEFAULT NULL,
  _Tolerance INT(11) NOT NULL DEFAULT 0,
  _ToleranceType INT(3) NOT NULL DEFAULT 0,
  _FreePeriod INT(11) NOT NULL DEFAULT 0,
  _Comment TEXT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON Prestation (_Name);
CREATE INDEX _Operation ON Prestation (_Operation);
CREATE INDEX _TVA ON Prestation (_TVA);

--
-- Table structure for PrestationCost
--
DROP TABLE IF EXISTS PrestationCost;
CREATE TABLE PrestationCost (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Prestation INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Prestation ON PrestationCost (_Prestation);

--
-- Table structure for PrestationCustomer
--
DROP TABLE IF EXISTS PrestationCustomer;
CREATE TABLE PrestationCustomer (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Prestation INT(11) NOT NULL DEFAULT 0,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Prestation ON PrestationCustomer (_Prestation);
CREATE INDEX _Actor ON PrestationCustomer (_Actor);

--
-- Table structure for PriceByCurrency
--
DROP TABLE IF EXISTS PriceByCurrency;
CREATE TABLE PriceByCurrency (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _RecommendedPrice DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Price DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _ActorProduct INT(11) NOT NULL DEFAULT 0,
  _PricingZone INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Currency ON PriceByCurrency (_Currency);
CREATE INDEX _Product ON PriceByCurrency (_Product);
CREATE INDEX _ActorProduct ON PriceByCurrency (_ActorProduct);
CREATE INDEX _PricingZone ON PriceByCurrency (_PricingZone);

--
-- Table structure for PricingZone
--
DROP TABLE IF EXISTS PricingZone;
CREATE TABLE PricingZone (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Product
--
DROP TABLE IF EXISTS Product;
CREATE TABLE Product (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  _BaseReference VARCHAR(255) DEFAULT NULL,
  _Volume FLOAT NOT NULL DEFAULT 0,
  _CustomsNaming VARCHAR(255) DEFAULT NULL,
  _Category INT(11) NOT NULL DEFAULT 0,
  _Activated INT(1) DEFAULT 1,
  _Affected INT(1) DEFAULT 0,
  _ProductType INT(11) NOT NULL DEFAULT 0,
  _TracingMode INT(3) NOT NULL DEFAULT 0,
  _TracingModeBeginRange INT(11) DEFAULT NULL,
  _TracingModeEndRange INT(11) DEFAULT NULL,
  _SellUnitType INT(11) NOT NULL DEFAULT 0,
  _FirstRankParcelNumber INT(11) NOT NULL DEFAULT 0,
  _SellUnitQuantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _SellUnitVirtualQuantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _SellUnitMinimumStoredQuantity DECIMAL(10,3) NOT NULL DEFAULT 0,
  _SellUnitLength FLOAT NOT NULL DEFAULT 0,
  _SellUnitWidth FLOAT NOT NULL DEFAULT 0,
  _SellUnitHeight FLOAT NOT NULL DEFAULT 0,
  _SellUnitWeight FLOAT NOT NULL DEFAULT 0,
  _SellUnitMasterDimension INT(11) NOT NULL DEFAULT 0,
  _SellUnitGerbability INT(11) NOT NULL DEFAULT 0,
  _SellUnitTypeInContainer INT(11) NOT NULL DEFAULT 0,
  _Turnable INT(11) DEFAULT NULL,
  _TVA INT(11) NOT NULL DEFAULT 0,
  _ConditioningRecommended INT(11) NOT NULL DEFAULT 0,
  _UnitNumberInConditioning INT(11) NOT NULL DEFAULT 0,
  _ConditionedProductReference VARCHAR(255) DEFAULT NULL,
  _ConditioningGerbability INT(11) NOT NULL DEFAULT 0,
  _ConditioningMasterDimension INT(11) NOT NULL DEFAULT 0,
  _PackagingRecommended INT(11) NOT NULL DEFAULT 0,
  _UnitNumberInPackaging INT(11) NOT NULL DEFAULT 0,
  _PackagedProductReference VARCHAR(255) DEFAULT NULL,
  _PackagingGerbability INT(11) NOT NULL DEFAULT 0,
  _PackagingMasterDimension INT(11) NOT NULL DEFAULT 0,
  _GroupingRecommended INT(11) NOT NULL DEFAULT 0,
  _UnitNumberInGrouping INT(11) NOT NULL DEFAULT 0,
  _GroupedProductReference VARCHAR(255) DEFAULT NULL,
  _GroupingGerbability INT(11) NOT NULL DEFAULT 0,
  _GroupingMasterDimension INT(11) NOT NULL DEFAULT 0,
  _LicenceName VARCHAR(255) DEFAULT NULL,
  _LicenceYear INT(4) DEFAULT NULL,
  _Description VARCHAR(255) DEFAULT NULL,
  _Image VARCHAR(255) DEFAULT NULL,
  _Owner INT(11) NOT NULL DEFAULT 0,
  _Model INT(11) NOT NULL DEFAULT 0,
  _Size INT(11) NOT NULL DEFAULT 0,
  _CommercialName VARCHAR(255) DEFAULT NULL,
  _MaterialType INT(3) NOT NULL DEFAULT 0,
  _Color INT(11) NOT NULL DEFAULT 0,
  _Origin VARCHAR(255) DEFAULT NULL,
  _FlyType INT(11) NOT NULL DEFAULT 0,
  _LastModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _BaseReference ON Product (_BaseReference);
CREATE INDEX _ProductType ON Product (_ProductType);
CREATE INDEX _SellUnitType ON Product (_SellUnitType);
CREATE INDEX _SellUnitTypeInContainer ON Product (_SellUnitTypeInContainer);
CREATE INDEX _TVA ON Product (_TVA);
CREATE INDEX _ConditioningRecommended ON Product (_ConditioningRecommended);
CREATE INDEX _PackagingRecommended ON Product (_PackagingRecommended);
CREATE INDEX _GroupingRecommended ON Product (_GroupingRecommended);
CREATE INDEX _Owner ON Product (_Owner);
CREATE INDEX _Model ON Product (_Model);
CREATE INDEX _Size ON Product (_Size);
CREATE INDEX _Color ON Product (_Color);
CREATE INDEX _FlyType ON Product (_FlyType);

--
-- Table structure for ProductChainLink
--
DROP TABLE IF EXISTS ProductChainLink;
CREATE TABLE ProductChainLink (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Chain INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Product ON ProductChainLink (_Product);
CREATE INDEX _Chain ON ProductChainLink (_Chain);

--
-- Table structure for ProductHandingByCategory
--
DROP TABLE IF EXISTS ProductHandingByCategory;
CREATE TABLE ProductHandingByCategory (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _UpdateDate DATETIME DEFAULT NULL,
  _Handing DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Type INT(3) NOT NULL DEFAULT 1,
  _Currency INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Product ON ProductHandingByCategory (_Product);
CREATE INDEX _Currency ON ProductHandingByCategory (_Currency);

--
-- Table structure for ProductKind
--
DROP TABLE IF EXISTS ProductKind;
CREATE TABLE ProductKind (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _ProductType INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _ProductType ON ProductKind (_ProductType);

--
-- Table structure for ProductQuantityByCategory
--
DROP TABLE IF EXISTS ProductQuantityByCategory;
CREATE TABLE ProductQuantityByCategory (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _MinimumQuantity DECIMAL(10,3) DEFAULT NULL,
  _MinimumQuantityType INT(3) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Category INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Product ON ProductQuantityByCategory (_Product);
CREATE INDEX _Category ON ProductQuantityByCategory (_Category);

--
-- Table structure for ProductSubstitution
--
DROP TABLE IF EXISTS ProductSubstitution;
CREATE TABLE ProductSubstitution (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _FromProduct INT(11) NOT NULL DEFAULT 0,
  _ByProduct INT(11) NOT NULL DEFAULT 0,
  _Interchangeable INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _FromProduct ON ProductSubstitution (_FromProduct);
CREATE INDEX _ByProduct ON ProductSubstitution (_ByProduct);

--
-- Table structure for ProductType
--
DROP TABLE IF EXISTS ProductType;
CREATE TABLE ProductType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _GenericProductType INT(11) NOT NULL DEFAULT 0,
  _Generic INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _GenericProductType ON ProductType (_GenericProductType);

--
-- Table structure for Promotion
--
DROP TABLE IF EXISTS Promotion;
CREATE TABLE Promotion (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _StartDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _Rate DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Type INT(3) NOT NULL DEFAULT 0,
  _ApproImpactRate DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Currency INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Currency ON Promotion (_Currency);

--
-- Table structure for Property
--
DROP TABLE IF EXISTS Property;
CREATE TABLE Property (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _DisplayName VARCHAR(255) DEFAULT NULL,
  _Type INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for PropertyValue
--
DROP TABLE IF EXISTS PropertyValue;
CREATE TABLE PropertyValue (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  _Property INT(11) NOT NULL DEFAULT 0,
  _StringValue VARCHAR(255) DEFAULT NULL,
  _IntValue INT(11) NOT NULL DEFAULT 0,
  _FloatValue FLOAT NOT NULL DEFAULT 0,
  _DateValue DATETIME DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Product ON PropertyValue (_Product);
CREATE INDEX _Property ON PropertyValue (_Property);

--
-- Table structure for Question
--
DROP TABLE IF EXISTS Question;
CREATE TABLE Question (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Theme INT(11) NOT NULL DEFAULT 0,
  _Text VARCHAR(255) DEFAULT NULL,
  _AnswerType INT(3) NOT NULL DEFAULT 0,
  _Alert INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Theme ON Question (_Theme);
CREATE UNIQUE INDEX _Text ON Question (_Text);
CREATE INDEX _Alert ON Question (_Alert);

--
-- Table structure for Rating
--
DROP TABLE IF EXISTS Rating;
CREATE TABLE Rating (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _BeginDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _Duration INT(3) NOT NULL DEFAULT 0,
  _DurationType INT(1) NOT NULL DEFAULT 0,
  _Type INT(11) NOT NULL DEFAULT 0,
  _FlyType INT(11) NOT NULL DEFAULT 0,
  _Licence INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Type ON Rating (_Type);
CREATE INDEX _FlyType ON Rating (_FlyType);
CREATE INDEX _Licence ON Rating (_Licence);

--
-- Table structure for RatingType
--
DROP TABLE IF EXISTS RatingType;
CREATE TABLE RatingType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for RealAnswer
--
DROP TABLE IF EXISTS RealAnswer;
CREATE TABLE RealAnswer (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Action INT(11) NOT NULL DEFAULT 0,
  _AnswerModel INT(11) NOT NULL DEFAULT 0,
  _Question INT(11) NOT NULL DEFAULT 0,
  _Value VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Action ON RealAnswer (_Action);
CREATE INDEX _AnswerModel ON RealAnswer (_AnswerModel);
CREATE INDEX _Question ON RealAnswer (_Question);

--
-- Table structure for RealBox
--
DROP TABLE IF EXISTS RealBox;
CREATE TABLE RealBox (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Grouping INT(11) NOT NULL DEFAULT 0,
  _ActivatedChainTask INT(11) NOT NULL DEFAULT 0,
  _PN VARCHAR(255) DEFAULT NULL,
  _SN VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Grouping ON RealBox (_Grouping);
CREATE INDEX _ActivatedChainTask ON RealBox (_ActivatedChainTask);

--
-- Table structure for Ressource
--
DROP TABLE IF EXISTS Ressource;
CREATE TABLE Ressource (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Type INT(3) NOT NULL DEFAULT 0,
  _Cost DECIMAL(10,2) NOT NULL DEFAULT 0,
  _Quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
  _CostType INT(3) NOT NULL DEFAULT 0,
  _Product INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON Ressource (_Name);
CREATE INDEX _Product ON Ressource (_Product);

--
-- Table structure for RessourceGroup
--
DROP TABLE IF EXISTS RessourceGroup;
CREATE TABLE RessourceGroup (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Active INT(1) DEFAULT 1,
  _AddNomenclatureCosts INT(1) DEFAULT 1,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON RessourceGroup (_Name);

--
-- Table structure for RessourceRessourceGroup
--
DROP TABLE IF EXISTS RessourceRessourceGroup;
CREATE TABLE RessourceRessourceGroup (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Ressource INT(11) NOT NULL DEFAULT 0,
  _RessourceGroup INT(11) NOT NULL DEFAULT 0,
  _Rate DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Ressource ON RessourceRessourceGroup (_Ressource);
CREATE INDEX _RessourceGroup ON RessourceRessourceGroup (_RessourceGroup);

--
-- Table structure for Saisonality
--
DROP TABLE IF EXISTS Saisonality;
CREATE TABLE Saisonality (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _StartDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _Rate DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for SellUnitType
--
DROP TABLE IF EXISTS SellUnitType;
CREATE TABLE SellUnitType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _ShortName VARCHAR(255) DEFAULT NULL,
  _LongName VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Site
--
DROP TABLE IF EXISTS Site;
CREATE TABLE Site (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  _Email VARCHAR(255) DEFAULT NULL,
  _Fax VARCHAR(255) DEFAULT NULL,
  _Phone VARCHAR(255) DEFAULT NULL,
  _Mobile VARCHAR(255) DEFAULT NULL,
  _PreferedCommunicationMode INT(11) DEFAULT NULL,
  _StreetNumber VARCHAR(255) DEFAULT NULL,
  _StreetType INT(3) NOT NULL DEFAULT 0,
  _StreetName VARCHAR(255) DEFAULT NULL,
  _StreetAddons VARCHAR(255) DEFAULT NULL,
  _Cedex VARCHAR(255) DEFAULT NULL,
  _GPS VARCHAR(255) DEFAULT NULL,
  _CountryCity INT(11) NOT NULL DEFAULT 0,
  _Zone INT(11) NOT NULL DEFAULT 0,
  _Planning INT(11) NOT NULL DEFAULT 0,
  _CommunicationModality INT(11) NOT NULL DEFAULT 0,
  _Owner INT(11) NOT NULL DEFAULT 0,
  _Type INT(3) NOT NULL DEFAULT 0,
  _Customs INT(1) DEFAULT 0,
  _StockOwner INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _CountryCity ON Site (_CountryCity);
CREATE INDEX _Zone ON Site (_Zone);
CREATE INDEX _Planning ON Site (_Planning);
CREATE INDEX _CommunicationModality ON Site (_CommunicationModality);
CREATE INDEX _Owner ON Site (_Owner);
CREATE INDEX _StockOwner ON Site (_StockOwner);

--
-- Table structure for SpreadSheet
--
DROP TABLE IF EXISTS SpreadSheet;
CREATE TABLE SpreadSheet (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Entity INT(11) NOT NULL DEFAULT 0,
  _Active INT(1) DEFAULT 1,
  _LastModified DATETIME DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON SpreadSheet (_Name);
CREATE INDEX _Entity ON SpreadSheet (_Entity);

--
-- Table structure for SpreadSheetColumn
--
DROP TABLE IF EXISTS SpreadSheetColumn;
CREATE TABLE SpreadSheetColumn (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _PropertyName VARCHAR(50) DEFAULT NULL,
  _FkeyPropertyName VARCHAR(50) DEFAULT NULL,
  _PropertyType INT(3) DEFAULT NULL,
  _PropertyClass VARCHAR(50) DEFAULT NULL,
  _Order INT(3) NOT NULL DEFAULT 0,
  _Comment TEXT DEFAULT NULL,
  _Default VARCHAR(255) DEFAULT NULL,
  _Width INT(5) NOT NULL DEFAULT 0,
  _Required INT(1) DEFAULT 0,
  _SpreadSheet INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _SpreadSheet ON SpreadSheetColumn (_SpreadSheet);

--
-- Table structure for State
--
DROP TABLE IF EXISTS State;
CREATE TABLE State (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Number INT(11) DEFAULT NULL,
  _Country INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Country ON State (_Country);

--
-- Table structure for Store
--
DROP TABLE IF EXISTS Store;
CREATE TABLE Store (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Customs INT(1) DEFAULT 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Activated INT(1) DEFAULT 1,
  _StockOwner INT(11) NOT NULL DEFAULT 0,
  _StorageSite INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _StockOwner ON Store (_StockOwner);
CREATE INDEX _StorageSite ON Store (_StorageSite);

--
-- Table structure for SupplierCustomer
--
DROP TABLE IF EXISTS SupplierCustomer;
CREATE TABLE SupplierCustomer (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _MaxIncur DECIMAL(10,2) DEFAULT NULL,
  _UpdateIncur DECIMAL(10,2) DEFAULT NULL,
  _ToHaveTTC DECIMAL(10,2) DEFAULT NULL,
  _Option INT(3) NOT NULL DEFAULT 0,
  _TotalDays INT(11) DEFAULT NULL,
  _Modality INT(3) DEFAULT NULL,
  _InvoiceByMail INT(3) NOT NULL DEFAULT 0,
  _CustomerProductCommandBehaviour INT(3) NOT NULL DEFAULT 0,
  _Supplier INT(11) NOT NULL DEFAULT 0,
  _Customer INT(11) NOT NULL DEFAULT 0,
  _MaxDeliveryDay INT(11) DEFAULT NULL,
  _TotalDeliveryDay INT(11) DEFAULT NULL,
  _DeliveryType INT(11) DEFAULT NULL,
  _HasTVA INT(1) DEFAULT 0,
  _HasTvaSurtax INT(1) DEFAULT 0,
  _HasFodecTax INT(1) DEFAULT 0,
  _HasTaxStamp INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Supplier ON SupplierCustomer (_Supplier);
CREATE INDEX _Customer ON SupplierCustomer (_Customer);

--
-- Table structure for Task
--
DROP TABLE IF EXISTS Task;
CREATE TABLE Task (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Symbol VARCHAR(255) DEFAULT NULL,
  _Instructions VARCHAR(255) DEFAULT NULL,
  _Duration FLOAT NOT NULL DEFAULT 0,
  _Cost FLOAT NOT NULL DEFAULT 0,
  _ToBeValidated INT(1) DEFAULT 0,
  _Type INT(11) NOT NULL DEFAULT 0,
  _IsBoxCreator INT(1) DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Symbol ON Task (_Symbol);

--
-- Table structure for Theme
--
DROP TABLE IF EXISTS Theme;
CREATE TABLE Theme (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON Theme (_Name);

--
-- Table structure for TVA
--
DROP TABLE IF EXISTS TVA;
CREATE TABLE TVA (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Category VARCHAR(255) DEFAULT NULL,
  _Type INT(3) DEFAULT NULL,
  _Rate DECIMAL(10,2) NOT NULL DEFAULT 0,
  _LastModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Type ON TVA (_Type);

--
-- Table structure for Unavailability
--
DROP TABLE IF EXISTS Unavailability;
CREATE TABLE Unavailability (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Purpose VARCHAR(255) DEFAULT NULL,
  _BeginDate DATETIME DEFAULT NULL,
  _EndDate DATETIME DEFAULT NULL,
  _WeeklyPlanning INT(11) NOT NULL DEFAULT 0,
  _Command INT(11) NOT NULL DEFAULT 0,
  _ActivatedChainOperation INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _WeeklyPlanning ON Unavailability (_WeeklyPlanning);
CREATE INDEX _Command ON Unavailability (_Command);
CREATE INDEX _ActivatedChainOperation ON Unavailability (_ActivatedChainOperation);

--
-- Table structure for UploadedDocument
--
DROP TABLE IF EXISTS UploadedDocument;
CREATE TABLE UploadedDocument (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Type INT(11) NOT NULL DEFAULT 0,
  _Comment TEXT DEFAULT NULL,
  _MimeType INT(11) NOT NULL DEFAULT 0,
  _Customer INT(11) NOT NULL DEFAULT 0,
  _ActivatedChainTask INT(11) NOT NULL DEFAULT 0,
  _UserAccount INT(11) NOT NULL DEFAULT 0,
  _CreationDate DATETIME DEFAULT NULL,
  _LastModificationDate DATETIME DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Type ON UploadedDocument (_Type);
CREATE INDEX _MimeType ON UploadedDocument (_MimeType);
CREATE INDEX _Customer ON UploadedDocument (_Customer);
CREATE INDEX _ActivatedChainTask ON UploadedDocument (_ActivatedChainTask);
CREATE INDEX _UserAccount ON UploadedDocument (_UserAccount);

--
-- Table structure for UploadedDocumentType
--
DROP TABLE IF EXISTS UploadedDocumentType;
CREATE TABLE UploadedDocumentType (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _Active INT(1) DEFAULT 1,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE UNIQUE INDEX _Name ON UploadedDocumentType (_Name);

--
-- Table structure for UserAccount
--
DROP TABLE IF EXISTS UserAccount;
CREATE TABLE UserAccount (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Identity VARCHAR(255) DEFAULT NULL,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Login VARCHAR(255) DEFAULT NULL,
  _Password VARCHAR(40) DEFAULT NULL,
  _Phone VARCHAR(255) DEFAULT NULL,
  _Fax VARCHAR(255) DEFAULT NULL,
  _Email VARCHAR(255) DEFAULT NULL,
  _Profile INT(3) DEFAULT NULL,
  _Catalog INT(11) NOT NULL DEFAULT 0,
  _SupplierCatalog INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Actor ON UserAccount (_Actor);
CREATE INDEX _Catalog ON UserAccount (_Catalog);
CREATE INDEX _SupplierCatalog ON UserAccount (_SupplierCatalog);

--
-- Table structure for WeeklyPlanning
--
DROP TABLE IF EXISTS WeeklyPlanning;
CREATE TABLE WeeklyPlanning (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Monday INT(11) NOT NULL DEFAULT 0,
  _Tuesday INT(11) NOT NULL DEFAULT 0,
  _Wednesday INT(11) NOT NULL DEFAULT 0,
  _Thursday INT(11) NOT NULL DEFAULT 0,
  _Friday INT(11) NOT NULL DEFAULT 0,
  _Saturday INT(11) NOT NULL DEFAULT 0,
  _Sunday INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Monday ON WeeklyPlanning (_Monday);
CREATE INDEX _Tuesday ON WeeklyPlanning (_Tuesday);
CREATE INDEX _Wednesday ON WeeklyPlanning (_Wednesday);
CREATE INDEX _Thursday ON WeeklyPlanning (_Thursday);
CREATE INDEX _Friday ON WeeklyPlanning (_Friday);
CREATE INDEX _Saturday ON WeeklyPlanning (_Saturday);
CREATE INDEX _Sunday ON WeeklyPlanning (_Sunday);

--
-- Table structure for WorkOrder
--
DROP TABLE IF EXISTS WorkOrder;
CREATE TABLE WorkOrder (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  _ValidityStart DATETIME DEFAULT NULL,
  _ValidityEnd DATETIME DEFAULT NULL,
  _Actor INT(11) NOT NULL DEFAULT 0,
  _Comment VARCHAR(255) DEFAULT NULL,
  _MaxVolume FLOAT NOT NULL DEFAULT 0,
  _MaxLM FLOAT NOT NULL DEFAULT 0,
  _MaxWeigth FLOAT NOT NULL DEFAULT 0,
  _MaxDistance FLOAT NOT NULL DEFAULT 0,
  _MaxDuration TIME DEFAULT '00:00',
  _State INT(3) NOT NULL DEFAULT 0,
  _ClotureDate DATETIME DEFAULT NULL,
  _Massified INT(11) NOT NULL DEFAULT 0,
  _DepartureDate DATETIME DEFAULT NULL,
  _ArrivalDate DATETIME DEFAULT NULL,
  _DepartureKm FLOAT NOT NULL DEFAULT 0,
  _ArrivalKm FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Actor ON WorkOrder (_Actor);

--
-- Table structure for Zip
--
DROP TABLE IF EXISTS Zip;
CREATE TABLE Zip (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Code VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for Zone
--
DROP TABLE IF EXISTS Zone;
CREATE TABLE Zone (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Name VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for RTWModel
--
DROP TABLE IF EXISTS RTWModel;
CREATE TABLE RTWModel (
  _Id int(11) unsigned NOT NULL default 0,
  _DBId int(11) default 0,
  _Season INT(11) NOT NULL DEFAULT 0,
  _Shape INT(11) NOT NULL DEFAULT 0,
  _PressName INT(11) NOT NULL DEFAULT 0,
  _StyleNumber VARCHAR(255) DEFAULT NULL,
  _Description VARCHAR(255) DEFAULT NULL,
  _Manufacturer INT(11) NOT NULL DEFAULT 0,
  _ConstructionType INT(11) NOT NULL DEFAULT 0,
  _ConstructionCode INT(11) NOT NULL DEFAULT 0,
  _Label INT(11) NOT NULL DEFAULT 0,
  _HeelHeight INT(11) NOT NULL DEFAULT 0,
  _HeelReference INT(11) NOT NULL DEFAULT 0,
  _HeelReferenceQuantity DECIMAL(10,3) DEFAULT NULL,
  _Sole INT(11) NOT NULL DEFAULT 0,
  _SoleQuantity DECIMAL(10,3) DEFAULT NULL,
  _Box INT(11) NOT NULL DEFAULT 0,
  _BoxQuantity DECIMAL(10,3) DEFAULT NULL,
  _HandBag INT(11) NOT NULL DEFAULT 0,
  _HandBagQuantity DECIMAL(10,3) DEFAULT NULL,
  _Material1 INT(11) NOT NULL DEFAULT 0,
  _Material1Quantity DECIMAL(10,3) DEFAULT NULL,
  _Material2 INT(11) NOT NULL DEFAULT 0,
  _Material2Quantity DECIMAL(10,3) DEFAULT NULL,
  _Accessory1 INT(11) NOT NULL DEFAULT 0,
  _Accessory1Quantity DECIMAL(10,3) DEFAULT NULL,
  _Accessory2 INT(11) NOT NULL DEFAULT 0,
  _Accessory2Quantity DECIMAL(10,3) DEFAULT NULL,
  _Lining INT(11) NOT NULL DEFAULT 0,
  _LiningQuantity DECIMAL(10,3) DEFAULT NULL,
  _Insole INT(11) NOT NULL DEFAULT 0,
  _InsoleQuantity DECIMAL(10,3) DEFAULT NULL,
  _UnderSole INT(11) NOT NULL DEFAULT 0,
  _UnderSoleQuantity DECIMAL(10,3) DEFAULT NULL,
  _MediaPlanta INT(11) NOT NULL DEFAULT 0,
  _MediaPlantaQuantity DECIMAL(10,3) DEFAULT NULL,
  _Lagrima INT(11) NOT NULL DEFAULT 0,
  _LagrimaQuantity DECIMAL(10,3) DEFAULT NULL,
  _HeelCovering INT(11) NOT NULL DEFAULT 0,
  _HeelCoveringQuantity DECIMAL(10,3) DEFAULT NULL,
  _Selvedge INT(11) NOT NULL DEFAULT 0,
  _SelvedgeQuantity DECIMAL(10,3) DEFAULT NULL,
  _Thread1 INT(11) NOT NULL DEFAULT 0,
  _Thread2 INT(11) NOT NULL DEFAULT 0,
  _Bamboo INT(11) NOT NULL DEFAULT 0,
  _BambooQuantity DECIMAL(10,3) DEFAULT NULL,
  _Image VARCHAR(255) DEFAULT NULL,
  _ColorImage VARCHAR(255) DEFAULT NULL,
  _Comment TEXT DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;

CREATE INDEX _Season ON RTWModel (_Season);
CREATE INDEX _Shape ON RTWModel (_Shape);
CREATE INDEX _PressName ON RTWModel (_PressName);
CREATE INDEX _Manufacturer ON RTWModel (_Manufacturer);
CREATE INDEX _ConstructionType ON RTWModel (_ConstructionType);
CREATE INDEX _ConstructionCode ON RTWModel (_ConstructionCode);
CREATE INDEX _Label ON RTWModel (_Label);
CREATE INDEX _HeelHeight ON RTWModel (_HeelHeight);
CREATE INDEX _HeelReference ON RTWModel (_HeelReference);
CREATE INDEX _Sole ON RTWModel (_Sole);
CREATE INDEX _Box ON RTWModel (_Box);
CREATE INDEX _HandBag ON RTWModel (_HandBag);
CREATE INDEX _Material1 ON RTWModel (_Material1);
CREATE INDEX _Material2 ON RTWModel (_Material2);
CREATE INDEX _Accessory1 ON RTWModel (_Accessory1);
CREATE INDEX _Accessory2 ON RTWModel (_Accessory2);
CREATE INDEX _Lining ON RTWModel (_Lining);
CREATE INDEX _Insole ON RTWModel (_Insole);
CREATE INDEX _UnderSole ON RTWModel (_UnderSole);
CREATE INDEX _MediaPlanta ON RTWModel (_MediaPlanta);
CREATE INDEX _Lagrima ON RTWModel (_Lagrima);
CREATE INDEX _HeelCovering ON RTWModel (_HeelCovering);
CREATE INDEX _Selvedge ON RTWModel (_Selvedge);
CREATE INDEX _Thread1 ON RTWModel (_Thread1);
CREATE INDEX _Thread2 ON RTWModel (_Thread2);
CREATE INDEX _Bamboo ON RTWModel (_Bamboo);

--
-- Table structure for RTWElement
--
DROP TABLE IF EXISTS RTWElement;
CREATE TABLE RTWElement (
  _Id int(11) unsigned NOT NULL default '0',
  _DBId int(11) default 0,
  _ClassName VARCHAR(255) DEFAULT NULL,
  _Name VARCHAR(255) DEFAULT NULL,
  _SupplierReference VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (_Id)
) TYPE=InnoDB CHARSET=latin1;


--
-- Table structure for siteUserAccount
--
DROP TABLE IF EXISTS siteUserAccount;
CREATE TABLE siteUserAccount (
  _ToUserAccount int(11) unsigned NOT NULL default '0',
  _FromSite int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_ToUserAccount, _FromSite)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for saiProduct
--
DROP TABLE IF EXISTS saiProduct;
CREATE TABLE saiProduct (
  _FromSaisonality int(11) unsigned NOT NULL default '0',
  _ToProduct int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromSaisonality, _ToProduct)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for oprTask
--
DROP TABLE IF EXISTS oprTask;
CREATE TABLE oprTask (
  _FromOperation int(11) unsigned NOT NULL default '0',
  _ToTask int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromOperation, _ToTask)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for accountAccountingType
--
DROP TABLE IF EXISTS accountAccountingType;
CREATE TABLE accountAccountingType (
  _FromAccount int(11) unsigned NOT NULL default '0',
  _ToAccountingType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromAccount, _ToAccountingType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for prsToMvtType
--
DROP TABLE IF EXISTS prsToMvtType;
CREATE TABLE prsToMvtType (
  _FromPrestation int(11) unsigned NOT NULL default '0',
  _ToMovementType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromPrestation, _ToMovementType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for prmProduct
--
DROP TABLE IF EXISTS prmProduct;
CREATE TABLE prmProduct (
  _FromPromotion int(11) unsigned NOT NULL default '0',
  _ToProduct int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromPromotion, _ToProduct)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for accountFlowType
--
DROP TABLE IF EXISTS accountFlowType;
CREATE TABLE accountFlowType (
  _FromAccount int(11) unsigned NOT NULL default '0',
  _ToFlowType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromAccount, _ToFlowType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for chtUserAccount
--
DROP TABLE IF EXISTS chtUserAccount;
CREATE TABLE chtUserAccount (
  _FromChainTask int(11) unsigned NOT NULL default '0',
  _ToUserAccount int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromChainTask, _ToUserAccount)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for spcDocumentModel
--
DROP TABLE IF EXISTS spcDocumentModel;
CREATE TABLE spcDocumentModel (
  _FromSupplierCustomer int(11) unsigned NOT NULL default '0',
  _ToDocumentModel int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromSupplierCustomer, _ToDocumentModel)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for phcCategory
--
DROP TABLE IF EXISTS phcCategory;
CREATE TABLE phcCategory (
  _ToCategory int(11) unsigned NOT NULL default '0',
  _FromProductHandingByCategory int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_ToCategory, _FromProductHandingByCategory)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for sitContact
--
DROP TABLE IF EXISTS sitContact;
CREATE TABLE sitContact (
  _ToContact int(11) unsigned NOT NULL default '0',
  _FromSite int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_ToContact, _FromSite)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for achProductType
--
DROP TABLE IF EXISTS achProductType;
CREATE TABLE achProductType (
  _FromActivatedChain int(11) unsigned NOT NULL default '0',
  _ToProductType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActivatedChain, _ToProductType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for chnProductType
--
DROP TABLE IF EXISTS chnProductType;
CREATE TABLE chnProductType (
  _FromChain int(11) unsigned NOT NULL default '0',
  _ToProductType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromChain, _ToProductType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for boxActivatedChainTask
--
DROP TABLE IF EXISTS boxActivatedChainTask;
CREATE TABLE boxActivatedChainTask (
  _FromBox int(11) unsigned NOT NULL default '0',
  _ToActivatedChainTask int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromBox, _ToActivatedChainTask)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for aacFlyType
--
DROP TABLE IF EXISTS aacFlyType;
CREATE TABLE aacFlyType (
  _ToAeroActor int(11) unsigned NOT NULL default '0',
  _FromFlyType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_ToAeroActor, _FromFlyType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for pdtProperty
--
DROP TABLE IF EXISTS pdtProperty;
CREATE TABLE pdtProperty (
  _FromProductType int(11) unsigned NOT NULL default '0',
  _ToProperty int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromProductType, _ToProperty)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for actJob
--
DROP TABLE IF EXISTS actJob;
CREATE TABLE actJob (
  _FromActor int(11) unsigned NOT NULL default '0',
  _ToJob int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActor, _ToJob)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for chtComponent
--
DROP TABLE IF EXISTS chtComponent;
CREATE TABLE chtComponent (
  _FromChainTask int(11) unsigned NOT NULL default '0',
  _ToComponent int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromChainTask, _ToComponent)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for acmLocation
--
DROP TABLE IF EXISTS acmLocation;
CREATE TABLE acmLocation (
  _FromActivatedMovement int(11) unsigned NOT NULL default '0',
  _ToLocation int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActivatedMovement, _ToLocation)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for accountFlowTypeItem
--
DROP TABLE IF EXISTS accountFlowTypeItem;
CREATE TABLE accountFlowTypeItem (
  _FromAccount int(11) unsigned NOT NULL default '0',
  _ToFlowTypeItem int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromAccount, _ToFlowTypeItem)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for ackUserAccount
--
DROP TABLE IF EXISTS ackUserAccount;
CREATE TABLE ackUserAccount (
  _FromActivatedChainTask int(11) unsigned NOT NULL default '0',
  _ToUserAccount int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActivatedChainTask, _ToUserAccount)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for questionToCategory
--
DROP TABLE IF EXISTS questionToCategory;
CREATE TABLE questionToCategory (
  _FromQuestion int(11) unsigned NOT NULL default '0',
  _ToCategory int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromQuestion, _ToCategory)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for saiProductKind
--
DROP TABLE IF EXISTS saiProductKind;
CREATE TABLE saiProductKind (
  _FromSaisonality int(11) unsigned NOT NULL default '0',
  _ToProductKind int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromSaisonality, _ToProductKind)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for actOperation
--
DROP TABLE IF EXISTS actOperation;
CREATE TABLE actOperation (
  _FromActor int(11) unsigned NOT NULL default '0',
  _ToOperation int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActor, _ToOperation)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for ackConcreteComponent
--
DROP TABLE IF EXISTS ackConcreteComponent;
CREATE TABLE ackConcreteComponent (
  _ToActivatedChainTask int(11) unsigned NOT NULL default '0',
  _FromConcreteComponent int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_ToActivatedChainTask, _FromConcreteComponent)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for ctgProductType
--
DROP TABLE IF EXISTS ctgProductType;
CREATE TABLE ctgProductType (
  _FromCatalog int(11) unsigned NOT NULL default '0',
  _ToProductType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromCatalog, _ToProductType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for achProduct
--
DROP TABLE IF EXISTS achProduct;
CREATE TABLE achProduct (
  _FromActivatedChain int(11) unsigned NOT NULL default '0',
  _ToProduct int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActivatedChain, _ToProduct)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for ackComponent
--
DROP TABLE IF EXISTS ackComponent;
CREATE TABLE ackComponent (
  _FromActivatedChainTask int(11) unsigned NOT NULL default '0',
  _ToComponent int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActivatedChainTask, _ToComponent)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for alertUserAccount
--
DROP TABLE IF EXISTS alertUserAccount;
CREATE TABLE alertUserAccount (
  _FromAlert int(11) unsigned NOT NULL default '0',
  _ToUserAccount int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromAlert, _ToUserAccount)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for achDangerousProductType
--
DROP TABLE IF EXISTS achDangerousProductType;
CREATE TABLE achDangerousProductType (
  _FromActivatedChain int(11) unsigned NOT NULL default '0',
  _ToDangerousProductType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromActivatedChain, _ToDangerousProductType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for chnDangerousProductType
--
DROP TABLE IF EXISTS chnDangerousProductType;
CREATE TABLE chnDangerousProductType (
  _FromChain int(11) unsigned NOT NULL default '0',
  _ToDangerousProductType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromChain, _ToDangerousProductType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for prmCategory
--
DROP TABLE IF EXISTS prmCategory;
CREATE TABLE prmCategory (
  _ToCategory int(11) unsigned NOT NULL default '0',
  _FromPromotion int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_ToCategory, _FromPromotion)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for cptHead
--
DROP TABLE IF EXISTS cptHead;
CREATE TABLE cptHead (
  _Head int(11) unsigned NOT NULL default '0',
  _ConcreteProduct int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_Head, _ConcreteProduct)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for aacLicence
--
DROP TABLE IF EXISTS aacLicence;
CREATE TABLE aacLicence (
  _ToAeroActor int(11) unsigned NOT NULL default '0',
  _FromLicence int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_ToAeroActor, _FromLicence)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for rTWModelRTWSize
--
DROP TABLE IF EXISTS rTWModelRTWSize;
CREATE TABLE rTWModelRTWSize (
  _FromRTWModel int(11) unsigned NOT NULL default '0',
  _ToRTWSize int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromRTWModel, _ToRTWSize)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for adcExecutedMovement
--
DROP TABLE IF EXISTS adcExecutedMovement;
CREATE TABLE adcExecutedMovement (
  _FromDeliveryOrder int(11) unsigned NOT NULL default '0',
  _ToExecutedMovement int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromDeliveryOrder, _ToExecutedMovement)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for ForecastFlowFlowTypeItem
--
DROP TABLE IF EXISTS ForecastFlowFlowTypeItem;
CREATE TABLE ForecastFlowFlowTypeItem (
  _FromForecastFlow int(11) unsigned NOT NULL default '0',
  _ToFlowTypeItem int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromForecastFlow, _ToFlowTypeItem)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for invoicesListToInvoice
--
DROP TABLE IF EXISTS invoicesListToInvoice;
CREATE TABLE invoicesListToInvoice (
  _FromInvoicesList int(11) unsigned NOT NULL default '0',
  _ToInvoice int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromInvoicesList, _ToInvoice)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for cppcConcreteProduct
--
DROP TABLE IF EXISTS cppcConcreteProduct;
CREATE TABLE cppcConcreteProduct (
  _FromConcreteProductPrestationCost int(11) unsigned NOT NULL default '0',
  _ToConcreteProduct int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromConcreteProductPrestationCost, _ToConcreteProduct)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for fltpcFlyType
--
DROP TABLE IF EXISTS fltpcFlyType;
CREATE TABLE fltpcFlyType (
  _FromFlyTypePrestationCost int(11) unsigned NOT NULL default '0',
  _ToFlyType int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromFlyTypePrestationCost, _ToFlyType)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for ppcProduct
--
DROP TABLE IF EXISTS ppcProduct;
CREATE TABLE ppcProduct (
  _FromProductPrestationCost int(11) unsigned NOT NULL default '0',
  _ToProduct int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromProductPrestationCost, _ToProduct)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for FromInvoiceItemToACO
--
DROP TABLE IF EXISTS FromInvoiceItemToACO;
CREATE TABLE FromInvoiceItemToACO (
  _FromInvoiceItem int(11) unsigned NOT NULL default '0',
  _ToACO int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromInvoiceItem, _ToACO)
) TYPE=InnoDB CHARSET=latin1;

--
-- Table structure for locProduct
--
DROP TABLE IF EXISTS locProduct;
CREATE TABLE locProduct (
  _FromLocation int(11) unsigned NOT NULL default '0',
  _ToProduct int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (_FromLocation, _ToProduct)
) TYPE=InnoDB CHARSET=latin1;
