<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

function executeSQL($SQL)
{
	return Database::connection()->execute($SQL);
}

// }}}
// executeSQLforCollection() {{{

/**
 * executeSQLforCollection()
 *
 * @param  $EntityName
 * @param  $SQL
 * @return array of integer
 */
function executeSQLforCollection($EntityName, $SQL)
{
	$rs = executeSQL($SQL); // execution de la requete
	$ArrayId = array();
	if (!(false === $rs)) {
		while (!$rs->EOF) {
			$ArrayId[] = (int)$rs->fields['_Id'];
			$rs->MoveNext();
		}
	} else { // retourne un id qui ne correspond a rien => Aucun élément à afficher
		$ArrayId[] = 0;
	}
	return $ArrayId;
}

// }}}

// request_StockProductRealandVirtualList() {{{

/*
 * Utilise pour recuperer la qte en stock
 * @param integer $ConnectedActorId
 * @param integer $ProfileId
 * @param boolean $withDate : si vaut true, on supprime la condition:
 *      HAVING (NOT (virtualQuantity=0 AND (isnull(qty) OR qty=0)))
 * @return string la requete SQL
 */
function request_StockProductRealandVirtualList(
    $ConnectedActorId, $ProfileId, $withDate = false)
{
    $locale = I18N::getLocaleCode();
    require_once('Objects/SellUnitType.const.php');
    $where = 'WHERE PDT._SellUnitType=SUT._Id AND PDT._ProductType=PTY._Id AND I1._Id=PDT._Name AND I2._Id=SUT._ShortName ';
    // XXX nécessaire ici ? je pense pas
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Product')) {
        $where .= 'AND PDT._DBId=' . DATABASE_ID . ' ';
    }
	// Traitement lie au Profile connecte
	switch ($ProfileId) {
		case UserAccount::PROFILE_SUPPLIER_CONSIGNE:
			$where .= 'AND LPQ._Location=LOC._Id AND LOC._Store=STO._Id AND '
			 		. 'STO._StockOwner=' . $ConnectedActorId . ' ';
			$addTable = ', Location LOC, Store STO ';
			break;
		case UserAccount::PROFILE_GESTIONNAIRE_STOCK:
		case UserAccount::PROFILE_OPERATOR:
			// Voient le stock dans lpq sur des sites dont son acteur est owner
			$where .= 'AND LPQ._Location=LOC._Id AND LOC._Store=STO._Id AND
        			STO._StorageSite=SIT._Id AND SIT._Owner=' . $ConnectedActorId . ' ';
			$addTable = ', Location LOC, Store STO, Site SIT ';
			break;
		case UserAccount::PROFILE_ADMIN_VENTES:
		case UserAccount::PROFILE_AERO_ADMIN_VENTES:
			// Voient le stock dans lpq sur des sites dont son acteur est sitowner
			// ou sur des stores dont son acteur est stockowner
			$where .= 'AND LPQ._Location=LOC._Id AND LOC._Store=STO._Id AND
        			STO._StorageSite=SIT._Id AND (SIT._Owner=' . $ConnectedActorId
			 		. ' OR STO._StockOwner=' . $ConnectedActorId . ') ';
			$addTable = ', Location LOC, Store STO, Site SIT ';
			break;
		case UserAccount::PROFILE_SUPPLIER:
			$where .= 'AND AP._Product = PDT._Id and AP._Actor =' . $ConnectedActorId . ' ';
			$addTable = ', ActorProduct AP ';
			break;
		default: // UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR
			$addTable = '';
	}
	// Traitement lie aux criteres de recherche saisis
    $baseReference = SearchTools::RequestOrSessionExist('BaseReference');
	if ($baseReference !== false && $baseReference != '') {
		$where .= 'AND PDT._BaseReference LIKE "'
		      . str_replace('*', "%", $baseReference) . '" ';
	}
    $name = SearchTools::RequestOrSessionExist('Name');
	if ($name !== false && $name != '') {
		$where .= 'AND I1._StringValue_'.$locale.' LIKE "' . str_replace('*', "%", $name) . '" ';
	}
	if (SearchTools::RequestOrSessionExist('Activated') !== false) {
		$where .= 'AND PDT._Activated=1 ';
	}
	if (SearchTools::RequestOrSessionExist('NotActivated') !== false) {
		$where .= 'AND PDT._Activated=0 ';
	}
	$owner = SearchTools::requestOrSessionExist('Owner');
	if ($owner !== false && $owner != '##') {
		$where .= 'AND PDT._Owner=' . $owner . ' ';
	}
	$where = ($where == 'WHERE ')?'':$where;

	$request = 'SELECT DISTINCT PDT._BaseReference as baseReference, ';
	$request .= 'PDT._SellUnitVirtualQuantity as virtualQuantity, ';
	$request .= 'PDT._SellUnitMinimumStoredQuantity as minimumStoredQuantity, ';
	$request .= 'I1._StringValue_'.$locale.' as pdtName, PDT._Id as pdtId, PDT._Category as category, ';
	$request .= 'PDT._Activated AS Activated, PTY._Name as productType, ';
	$request .= 'IF(SUT._Id>= ' . SELLUNITTYPE_KG .  ', I2._StringValue_'.$locale.',"") AS shortName, ';
    $request .= 'SUM(LPQ._RealQuantity) as qty ';
	$request .= 'FROM SellUnitType SUT, I18nString I1, I18nString I2, ProductType PTY, Product PDT LEFT JOIN LocationProductQuantities LPQ ON PDT._Id=LPQ._Product ';
	$request .= $addTable . $where;
	$request .= 'GROUP BY PDT._Id ';
	if ($withDate === false) {
        $request .= 'HAVING (NOT (virtualQuantity=0 AND (isnull(qty) OR qty=0))) ';
	}
	/*  $request .= 'HAVING (((Activated=0 AND (qty<>0 ';
 $request .= 'OR virtualQuantity<>0)) OR Activated=1) AND NOT (virtualQuantity=0 AND isnull(qty))) ';*/
	$request .= 'ORDER BY baseReference';

	return $request;
}

// }}}
// Request_StockProductAtDate() {{{

/*
 * Utilise pour recuperer la qte en stock a une date donnee
 * @param integer $ConnectedActorId
 * @param integer $ProfileId
 * @param boolean $withDate : si vaut true, on supprime la condition:
 *      HAVING (NOT (virtualQuantity=0 AND (isnull(qty) OR qty=0)))
 * @return string la requete SQL
 */
function Request_StockProductAtDate($ConnectedActorId, $ProfileId, $addWhere = '')
{
    $locale = I18N::getLocaleCode();
    require_once('Objects/SellUnitType.const.php');
	// Traitement lie au Profile connecte
	switch ($ProfileId) {
		case UserAccount::PROFILE_SUPPLIER_CONSIGNE:
			$where = 'WHERE LEM._Location=LOC._Id AND LOC._Store=STO._Id AND STO._StockOwner='
			 		. $ConnectedActorId . ' AND ';
			$addTable = ', Location LOC, Store STO ';
			break;
		case UserAccount::PROFILE_GESTIONNAIRE_STOCK:
		case UserAccount::PROFILE_OPERATOR:
			// Voient le stock dans lpq sur des sites dont son acteur est owner
			$where = 'WHERE LEM._Location=LOC._Id AND LOC._Store=STO._Id AND
        			STO._StorageSite=SIT._Id AND SIT._Owner=' . $ConnectedActorId . ' AND ';
			$addTable = ', Location LOC, Store STO, Site SIT ';
			break;
		case UserAccount::PROFILE_ADMIN_VENTES:
		case UserAccount::PROFILE_AERO_ADMIN_VENTES:
			// Voient le stock dans lpq sur des sites dont son acteur est sitowner
			// ou sur des stores dont son acteur est stockowner
			$where = 'WHERE LOC._Location=LOC._Id AND LOC._Store=STO._Id AND
        			STO._StorageSite=SIT._Id AND (SIT._Owner=' . $ConnectedActorId
			 		. ' OR STO._StockOwner=' . $ConnectedActorId . ') AND ';
			$addTable = ', Location LOC, Store STO, Site SIT ';
			break;
		case UserAccount::PROFILE_SUPPLIER:
			$where = 'WHERE AP._Product=PDT._Id and AP._Actor=' . $ConnectedActorId . ' AND ';
			$addTable = ', ActorProduct AP ';
			break;
		default: // UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR
			$where = 'WHERE ';
			$addTable = ' ';
	}
    // XXX nécessaire ici ? je pense pas
    if (defined('DATABASE_ID') && !Object::isPublicEntity('LocationExecutedMovement')) {
        $where .= 'LEM._DBId=' . DATABASE_ID . ' AND ';
    }
	// Traitement lie aux criteres de recherche saisis
    $baseReference = SearchTools::RequestOrSessionExist('BaseReference');
	if ($baseReference !== false && $baseReference != '') {
		$where .= 'PDT._BaseReference LIKE "'
		 		. str_replace('*', "%", $baseReference) . '" AND ';
	}
    $name = SearchTools::RequestOrSessionExist('Name');
	if ($name !== false && $name != '') {
		$where .= 'I1._StringValue_'.$locale.' LIKE "' . str_replace('*', "%", $name) . '" AND ';
	}
	if (SearchTools::RequestOrSessionExist('Activated') !== false) {
		$where .= 'PDT._Activated=1  AND ';
	}
	if (SearchTools::RequestOrSessionExist('NotActivated') !== false) {
		$where .= 'PDT._Activated=0 AND ';
	}
	$owner = SearchTools::requestOrSessionExist('Owner');
	if ($owner !== false && $owner != '##') {
		$where .= 'PDT._Owner=' . $owner . ' AND ';
	}
    $d = (isset($_REQUEST['Date']))?$_REQUEST['Date']:$_SESSION['Date'];
    $date = sprintf("%d-%02d-%02d 23:59:59", $d['Y'], $d['m'], $d['d']);

	$request = 'SELECT LEM._Product as pdtId, MVTT._EntrieExit as entryExit, ';
	$request .= 'LEM._Quantity as qty, LEM._CancelledMovement as cancelledMvt, ';
	$request .= 'IF(SUT._Id>= ' . SELLUNITTYPE_KG .  ',I2._StringValue_'.$locale.',"") AS shortName ';
	$request .= 'FROM SellUnitType SUT, I18nString I1, I18nString I2, LocationExecutedMovement LEM, ExecutedMovement EXM, ';
	$request .= 'MovementType MVTT, Product PDT ' . $addTable;
	$request .= $where . $addWhere;
	$request .= 'I1._Id=PDT._Name AND I2._Id=SUT._ShortName AND PDT._SellUnitType=SUT._Id AND ';
	$request .= 'LEM._Product=PDT._Id AND LEM._ExecutedMovement=EXM._Id  AND EXM._Type=MVTT._Id ';
	$request .= ' AND LEM._Date > "' . $date . '" ';
	$request .= 'ORDER BY PDT._BaseReference;';
	return $request;
}

// }}}
// request_stockACMQuantity() {{{

function request_stockACMQuantity($pdtID, $entries=true, $withDate=false) {
    $states = array(
        ActivatedMovement::CREE,
        ActivatedMovement::ACM_EN_COURS,
        ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT,
        ActivatedMovement::BLOQUE
    );
    $types = $entries ? array(ENTREE_NORMALE, ENTREE_INTERNE) :
        array(SORTIE_NORMALE, SORTIE_INTERNE);

    $sql = 'SELECT SUM(ACM._Quantity) as quantity FROM ActivatedMovement ACM '
        . 'WHERE ACM._State IN ('.implode(',', $states).') AND '
        . 'ACM._Type IN ('.implode(',', $types).') AND ACM._Product='.$pdtID;
    if ($withDate) {
	    $d = SearchTools::requestOrSessionExist('Date');
	    if ($d && !empty($d)) {
            $d = sprintf("%d-%02d-%02d 23:59:59", $d['Y'], $d['m'], $d['d']);
            $sql .= ' AND ACM._StartDate <= "' . $d . '";';
	    }
    }
    return executeSQL($sql);
}

// }}}
// request_StockAccountingQty() {{{

/*
 * Utilise pour recuperer la qte en stock, pour la compta matiere
 * @return string la requete SQL
 */
function request_StockAccountingQty()
{
    $select = $from = $orderBy = $groupBy = '';
    switch($_REQUEST['unitType']){
        case 1: // UV
            $qtyType = 'LPQ._RealQuantity';
            break;
        case 2: // KG
            $qtyType = 'LPQ._RealQuantity * PDT._SellUnitWeight';
            break;
/*        case 3: // M3
            $qtyType = 'LPQ._RealQuantity * PDT._Volume';
            break;*/
        default: // L // Completement contradictoire avec le case precedent....
            $qtyType = 'LPQ._RealQuantity * PDT._Volume';
    } // switch

    $sqlParts = getSelectFromWhere();
    $select .= $sqlParts['select'];
    $from .= $sqlParts['from'];
    $where = $sqlParts['where'];
    $groupBy .= $sqlParts['groupBy'];
    $orderBy .= $sqlParts['orderBy'];

	$request = 'SELECT STO._Name as storeName ' . $select
	         . ', SUM(' . $qtyType . ') as qty '
             . 'FROM LocationProductQuantities LPQ, Location LOC, Store STO, Product PDT '
             . $from
             . 'WHERE LPQ._Location=LOC._Id AND LOC._Store=STO._Id AND '
             . 'LOC._Store IN (' . implode(", ", $_REQUEST['Store']) . ') '
             . 'AND LPQ._Product=PDT._Id AND PDT._ProductType='
             . $_REQUEST['ProductType'] . ' AND PDT._Owner='
             . $_REQUEST['ProductOwner'] . ' ' . $where
             . 'GROUP BY STO._Id' . $groupBy . ' '
             . 'ORDER BY STO._Name' . $orderBy . ';';
	return $request;
}

// }}}
// getSelectFromWhere() {{{

/*
 * Utilise pour construire dynamiquement les req SQL, pour la compta matiere
 *
 * @param string $context 'LPQ' ou 'LEM'
 * @return array of string
 */
function getSelectFromWhere($context='LPQ') {
    require_once('Objects/Property.inc.php');
    require_once('Objects/Product.php');
    $select = $from = $where = $orderBy = $groupBy = '';
    // Nom de la colonne de PropertyValue qui est concernee
	$property = Object::load('Property', $_REQUEST['Property']);
	$ppvColumnName = '_' . getPropertyTypeColumn($property->getType());

	// Selon si la Property est dynamique ou non, on va chercher l'info
    // dans Product oubien PropertyValue
    if ($property->isDynamic()) {
        $where .= 'AND ' . $context . '._Product = PPV._Product ';
        $where .= 'AND PPV._Property=' . $_REQUEST['Property'] . ' ';

        if ($property->getType() == Property::OBJECT_TYPE) {
            // Recuperation du toString, pour ne pas afficher des id
            $FKClassName = $property->getName();
            require_once('Objects/' . $FKClassName . '.php');
            $FKTableName = call_user_func(array($FKClassName, 'getTableName'));
            // getToStringAttribute() n'est pas static...
            $aFKClassNameInstance = new $FKClassName();
            $FKToString = $aFKClassNameInstance->getToStringAttribute();
            
            // Correctement gere seulement si toString() 'classique'
            if (is_string($FKToString)) {
                // Gestion du cas ou TYPE_I18N_STRING:
                $FKTableProps = call_user_func(array($FKClassName, 'getProperties'));
                if ($FKTableProps[$FKToString] == Object::TYPE_I18N_STRING) {
                    $select .= ', IST._StringValue_' . I18N::getLocaleCode() . ' AS propertyValue ';
                    $from .= ', I18nString IST ';
                    $where .= 'AND FKN._' . $FKToString . '=IST._Id ';
                    $orderBy .= ', IST._StringValue_' . I18N::getLocaleCode();
                }
                else {
                    $select .= ', FKN._' . $FKToString . ' AS propertyValue ';
                    $orderBy .= ', FKN._' . $FKToString;
                }
            }
            else {  // is_array()
                $select .= ', FKN._' . $FKToString[0] . ' AS propertyValue ';
                $orderBy .= ', FKN._' . $FKToString;
            }
            $from .= ', PropertyValue PPV, ' . $FKTableName . ' FKN ';
            $where .= 'AND PPV._IntValue=FKN._Id ';
            $groupBy .= ', FKN._' . $FKToString;
        }
        else {
            $select .= ', PPV.' . $ppvColumnName . ' AS propertyValue ';
            $from .= ', PropertyValue PPV ';
            $groupBy .= ', PPV.' . $ppvColumnName;
            $orderBy .= ', PPV.' . $ppvColumnName;
        }
    }

    else { // Info dans Product directement
        if ($_REQUEST['unitType'] == 1) {  // Besoin de cette jointure en plus
///            $from .= ', Product PDT ';
            $where .= 'AND ' . $context . '._Product=PDT._Id ';
        }
        if ($property->getType() == Property::OBJECT_TYPE) {
            // Recuperation du toString, pour ne pas afficher des id
            $pdtProperties = Product::getPropertiesByContext();
            $FKClassName = $pdtProperties[$property->getName()];
            require_once('Objects/' . $FKClassName . '.php');
            $FKTableName = call_user_func(array($FKClassName, 'getTableName'));
            // getToStringAttribute() n'est pas static...
            $aFKClassNameInstance = new $FKClassName();
            $FKToString = $aFKClassNameInstance->getToStringAttribute();
            // Correctement gere seulement si toString() 'classique'
            if (is_string($FKToString)) {
                $select .= ', FKN._' . $FKToString . ' AS propertyValue ';
            }
            else {
                $select .= ', FKN._' . $FKToString[0] . ' AS propertyValue ';
            }
            $from .= ', ' . $FKTableName . ' FKN ';
            $where .= 'AND PDT._' . $property->getName() . '=FKN._Id ';
            $groupBy .= ', FKN._' . $FKToString;
            $orderBy .= ', FKN._' . $FKToString;
        }
        else {
            $select .= ', PDT._' . $property->getName() . ' AS propertyValue ';
            $groupBy .= ', PDT._' . $property->getName();
            $orderBy .= ', PDT._' . $property->getName();
        }
    }
    $cls = $context=='LPQ' ? 'LocationProductQuantities' : 'LocationExecutedMovement';
    if (defined('DATABASE_ID') && !Object::isPublicEntity($cls)) {
        $where .= 'AND ' . $context . '._DBId=' . DATABASE_ID . ' ';
    }
    return array('select' => $select, 'from' => $from, 'where' => $where,
                'groupBy' => $groupBy, 'orderBy' => $orderBy);
}

// }}}
// request_StockAccountingLEM() {{{

/*
 * Utilise pour recuperer la qte en stock a une date donnee, pour la compta matiere
 *
 * @param string $startDate : debut de creneau
 * @param string $endDate : fin de creneau : utile seulement pour les E/S
 * @return string la requete SQL
 */
function request_StockAccountingLEM($startDate, $endDate='')
{
    $select = $from = $orderBy = $groupBy = '';
    $startDate .= ($endDate == '')?' 23:59:59':' 00:00:00';

    switch($_REQUEST['unitType']){
        case 1: // UV
            $qtyType = 'LEM._Quantity';
            break;
        case 2: // KG
            $qtyType = 'LEM._Quantity * PDT._SellUnitWeight';
            break;
/*        case 3: // M3
            $qtyType = 'LEM._Quantity * PDT._Volume';
            break;*/
        default: // L // Completement contradictoire avec le case precedent....
            $qtyType = 'LEM._Quantity * PDT._Volume';
    } // switch

    $sqlParts = getSelectFromWhere('LEM');
    $select .= $sqlParts['select'];
    $from .= $sqlParts['from'];
    $where = $sqlParts['where'];
    $groupBy .= $sqlParts['groupBy'];
    $orderBy .= $sqlParts['orderBy'];

	$request = 'SELECT STO._Name as storeName, MVTT._EntrieExit as entryExit, ';
	$request .= 'EXM._Type as mvtType, ';
	$request .= $qtyType . ' AS qty, LEM._CancelledMovement as cancelledMvt ';
    $request .= $select;
	$request .= 'FROM LocationExecutedMovement LEM, ExecutedMovement EXM ';
    $request .= ', Product PDT' . $from;
	$request .= ', MovementType MVTT, Location LOC, Store STO ';
	$request .= 'WHERE LEM._Location=LOC._Id AND LOC._Store=STO._Id AND ';
    $request .= 'LOC._Store IN (' . implode(", ", $_REQUEST['Store']) . ') ';
    $request .= 'AND LEM._Product=PDT._Id AND PDT._ProductType=';
    $request .= $_REQUEST['ProductType'] . ' AND PDT._Owner=';
    $request .= $_REQUEST['ProductOwner'] . ' '. $where;
	$request .= 'AND LEM._ExecutedMovement=EXM._Id  AND EXM._Type=MVTT._Id ';
	// Pour calcul Qtes a la fin de periode
    if ($endDate == '') {
        $request .= ' AND LEM._Date > "' . $startDate . '" ';
	}
	// Pour calcul des E/S pendant la periode
    else {
        $endDate .= ' 23:59:59';
        $request .= ' AND LEM._Date < "' . $endDate . '" ';
        $request .= ' AND LEM._Date >= "' . $startDate . '" ';
        // LEM pas annulateur de type annulation et
        // pas mvt non prevu annulé
        $request .= ' AND (LEM._Cancelled != 1 AND NOT(LEM._Cancelled = -1 AND MVTT._Foreseeable != 1)) ';
        $request .= ' AND EXM._Type NOT IN (' . SORTIE_DEPLACEMT . ', ' . ENTREE_DEPLACEMT . ') ';
	}
    $request .= 'ORDER BY STO._Name' . $orderBy . ';';
	return $request;
}

// }}}
// Request_WorkOrderList() {{{

function Request_WorkOrderList($ConnectedActorId)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	return 'SELECT DISTINCT WKO._Id as wkoId FROM WorkOrder WKO,
            ActivatedChainOperation ACO, ActivatedChain ACH,
            CommandItem ACI, Command ABC, UserAccount UAC
            WHERE WKO._Id = ACO._OwnerWorkerOrder AND ACO._ActivatedChain = ACH._Id
            AND ACH._Id = ACI._ActivatedChain AND ACI._Command = ABC._Id
            AND ABC._Customer = ' . $ConnectedActorId . '
            ORDER BY wkoId';
}

// }}}
// Request_ActivatedChainList() {{{

function Request_ActivatedChainList($ConnectedActorId, $ProfileId)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	$addForActor1 = ($ProfileId == UserAccount::PROFILE_ACTOR)?'ActivatedChainOperation ACO, ':'';
	$addForActor2 = ($ProfileId == UserAccount::PROFILE_ACTOR)?'AND ACH._Id = ACO._ActivatedChain ':'';
	$addForActor3 = ($ProfileId == UserAccount::PROFILE_ACTOR)?' OR (ACO._Actor = ' . $ConnectedActorId . ')':'';
	return "SELECT DISTINCT ACH._Id as FROM ActivatedChain ACH,
            $addForActor1 CommandItem ACI, Command ABC
            WHERE ACH._Id = ACI._ActivatedChain
                AND ACI._Command = ABC._Id $addForActor2
                AND (ACH._Owner = $ConnectedActorId OR
                        (ABC._Customer = $ConnectedActorId OR
                         ABC._Expeditor = $ConnectedActorId OR
                         ABC._Destinator = $ConnectedActorId)
                     $addForActor3)
            ORDER BY ACH._Id";
}

// }}}
// Request_LocationExecutedMovementList() {{{

function Request_LocationExecutedMovementList($ConnectedActorId)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	return 'SELECT DISTINCT LEM._Id as lemId
         FROM LocationExecutedMovement LEM, ExecutedMovement EXM,
         ActivatedMovement ACM, CommandItem ACI, Command ABC,
         ActivatedChainOperation ACO, ActivatedChain ACH
         WHERE LEM._ExecutedMovement = EXM._Id
             AND EXM._ActivatedMovement = ACM._Id
             AND ACM._ProductCommandItem = ACI._Id
             AND ACI._Command = ABC._Id
             AND ACI._ActivatedChain = ACH._Id
             AND (ABC._Expeditor = ' . $ConnectedActorId . '
                 OR ABC._Destinator = ' . $ConnectedActorId . '
                 OR ABC._Customer = ' . $ConnectedActorId . '
                 OR ACH._Owner = ' . $ConnectedActorId . ')
         ORDER BY lemId;';
}

// }}}
// request_jobsWhitchHasActors() {{{

// Retourne les ids des Jobs lies a au moins un Actor
function request_jobsWhitchHasActors() {
	return executeSQL('SELECT distinct(_ToJob) as job FROM actJob;');
}

// }}}
// Request_ProductListForRPCClient() {{{

function Request_ProductListForRPCClient()
{
    $locale = I18N::getLocaleCode();
    $sql  = 'SELECT P._Id, P._BaseReference, P._TracingMode, I._StringValue_'.$locale.' AS _Name '
          . 'FROM Product P, I18nString I WHERE _Activated=1 AND I._Id=P._Name';
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Product')) {
        $sql .= ' AND P._DBId=' . DATABASE_ID;
    }
    $sql .= ' ORDER BY P._BaseReference ASC';
	return ExecuteSQL($sql);
}

// }}}
// Request_ConcreteProductListForRPCClient() {{{

function Request_ConcreteProductListForRPCClient($pdtID)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
    return ExecuteSQL('SELECT _SerialNumber FROM ConcreteProduct
        WHERE _Product=' . $pdtID . ' ORDER BY _SerialNumber ASC');
}

// }}}
// Request_ActorListForRPCClient() {{{

function Request_ActorListForRPCClient()
{
    $request = 'SELECT t0._Id as actId, t0._Name as actName,
	 t1._Name as siteName, t1._Phone as sitePhone,
     t1._StreetType as adrStreetType, t1._StreetNumber as adrStreetNumber,
     t1._StreetName as adrStreetName, t1._StreetAddons as adrStreetAddons,
     t1._Cedex as adrCedex, t4._Code as zipCode, t5._Name as ctnName,
     t6._Name as ctrName, IFNULL((SELECT t7._Name
        FROM Contact t7 WHERE t7._Id=(SELECT MIN(t8._ToContact)
            FROM sitContact t8 WHERE t8._FromSite=t1._Id
            GROUP BY t8._FromSite)), "") as contactName
     FROM Actor t0, Site t1,
          CountryCity t3, Zip t4, CityName t5, Country t6
     WHERE
         t0._Active = 1
         AND t0._Generic = 0
         AND t0._Id = t1._Owner
         AND t3._Id = t1._CountryCity
         AND t4._Id = t3._Zip
         AND t5._Id = t3._CityName
         AND t6._Id = t3._Country';
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Actor')) {
        $request .= ' AND t0._DBId = ' . DATABASE_ID;
    }
    $request .= ' ORDER BY actName ASC';
	return ExecuteSQL($request);
}

// }}}
// Request_StorageSiteListForRPCClient() {{{

function Request_StorageSiteListForRPCClient($ownerId)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	$request = "SELECT SIT._Id as sitId, SIT._Name as sitName, STO._Id as stoId,
     STO._Name as stoName, LOC._Id as locId, LOC._Name as locName
     FROM Site SIT, Store STO, Location LOC
     WHERE
         SIT._ClassName='StorageSite' AND SIT._Owner=%s
             AND STO._StorageSite = SIT._Id AND LOC._Store = STO._Id
     ORDER BY locName ASC";
	return ExecuteSQL(sprintf($request, $ownerId));
}

// }}}
// request_CommandIsCompatibleWithUserForExecution() {{{

function request_CommandIsCompatibleWithUserForExecution($activatedChainID, $actorID, $operationID)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	$request = "SELECT DISTINCT count(*) FROM ActivatedChainOperation T0
     WHERE T0._ActivatedChain = '%s' AND T0._Actor = '%s'
     AND T0._Operation = '%s'";
	$request = sprintf($request, $activatedChainID, $actorID, $operationID);
	return ExecuteSQL($request);
}

// }}}
// request_ProductByLocationList() {{{

function request_ProductByLocationList($StoreId)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
    $addWhere = $addFrom = $addJoin = '';
    $locationName = SearchTools::requestOrSessionExist('Location');
	if ($locationName !== false && $locationName != '') {
        $addWhere = ' AND LOC._Name LIKE "'
                . str_replace('*', "%", $locationName) . '" ';
    }
    $baseReference = SearchTools::requestOrSessionExist('BaseReference');
	if ($baseReference !== false && $baseReference != '') {
        $addFrom = ', Product PDT ';
        $addJoin = ' AND LPQ._Product=PDT._Id ';
        $addWhere .= ' AND PDT._BaseReference LIKE "'
                . str_replace('*', "%", $baseReference) . '" ';
    }
    $pdtName = SearchTools::requestOrSessionExist('PdtName');
	if ($pdtName !== false && $pdtName != '') {
        $locale = I18N::getLocaleCode();
        $addFrom = ', Product PDT, I18nString I1 ';
        $addJoin = ' AND LPQ._Product=PDT._Id AND I1._Id=PDT._Name ';
        $addWhere .= ' AND I1._StringValue_'.$locale.' LIKE "'
            . str_replace('*', "%", $pdtName) . '" ';
    }
    $ownerId = SearchTools::requestOrSessionExist('Owner');
	if ($ownerId !== false && $ownerId != '##') {
        $addFrom = ', Product PDT ';
        $addJoin = ' AND LPQ._Product=PDT._Id ';
        $addWhere .= ' AND PDT._Owner=' . $ownerId;
    }
	$SQLRequest = 'SELECT LPQ._Location as lpqLocation, LOC._Name as locName,
        SUM( LPQ._RealQuantity ) AS Qty
        FROM Location LOC, LocationProductQuantities LPQ' . $addFrom .  '
        WHERE LPQ._Location = LOC._Id
        AND LOC._Store = ' . $StoreId . $addJoin . $addWhere . '
        GROUP BY LPQ._Location
        HAVING SUM( LPQ._RealQuantity ) > 0
        ORDER BY LOC._Name';
	return $SQLRequest;
}

// }}}
// request_LocationByProductList() {{{

function request_LocationByProductList($StoreId)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
    $addWhere = '';
    $locationName = SearchTools::requestOrSessionExist('Location');
	if ($locationName !== false && $locationName != '') {
        $addWhere = ' AND LOC._Name LIKE "'
                . str_replace('*', "%", $locationName) . '" ';
    }
    $baseReference = SearchTools::requestOrSessionExist('BaseReference');
	if ($baseReference !== false && $baseReference != '') {
        $addWhere .= ' AND PDT._BaseReference LIKE "'
                . str_replace('*', "%", $baseReference) . '" ';
    }
    $pdtName = SearchTools::requestOrSessionExist('PdtName');
	if ($pdtName !== false && $pdtName != '') {
        $locale = I18N::getLocaleCode();
        $addWhere .= ' AND I1._Id=PDT._Name AND I1._StringValue_'.$locale.' LIKE "'
                . str_replace('*', "%", $pdtName) . '" ';
    }

    $ownerId = SearchTools::requestOrSessionExist('Owner');
	if ($ownerId !== false && $ownerId != '##') {
        $addWhere .= ' AND PDT._Owner=' . $ownerId;
    }
	$SQLRequest = 'SELECT DISTINCT PDT._BaseReference as pdtBaseReference,
             LPQ._Product as lpqProduct
             FROM LocationProductQuantities LPQ, Product PDT, I18nString I1, Location LOC
             WHERE LPQ._Product = PDT._Id AND LPQ._Location = LOC._Id
             AND LOC._Store = ' . $StoreId . $addWhere
             . ' ORDER BY PDT._Basereference ';
	return $SQLRequest;
}

// }}}
// Request_GridColumnProductByLocationList() {{{

function Request_GridColumnProductByLocationList($param)
{
    $locale = I18N::getLocaleCode();
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
    return "SELECT PDT._BaseReference as pdtBaseReference,
         LPQ._RealQuantity as lpqRealQuantity, I1._StringValue_".$locale." as unity,
         PDT._SellUnitType as sutId
         FROM Product PDT, LocationProductQuantities LPQ, SellUnitType SUT, I18nString I1
         WHERE LPQ._Product = PDT._Id AND PDT._SellUnitType=SUT._Id AND I1._Id=SUT._ShortName
         AND LPQ._Location = $param";
}

// }}}
// Request_GridColumnLocationByProductList() {{{

function Request_GridColumnLocationByProductList($param, $param2)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	return "SELECT LOC._Name as locName, LPQ._RealQuantity as lpqRealQuantity
         FROM Location LOC, LocationProductQuantities LPQ WHERE
         LPQ._Location = LOC._Id AND LPQ._Product = $param AND LOC._Store = $param2";
}

// }}}
// request_AlertPaymentDate() {{{

function request_AlertPaymentDate($ActorId)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	return "SELECT CMD._CommandNo as abcCommandNo, CMD._Id as abcId,
     ADC._DocumentNo as adcDocumentNo, ADC._PaymentDate as adcPaymentDate,
     CMD._State as abcState
     FROM Command CMD, AbstractDocument ADC
     WHERE ADC._Command = CMD._Id AND ADC._ClassName = 'Invoice' AND
     ADC._PaymentDate < CURRENT_DATE AND ADC._PaymentDate != 0 AND
     CMD._Destinator = $ActorId";
}

// }}}
// request_Get_Interceptor() {{{

require_once('Objects/Property.inc.php');

function request_Get_Interceptor($propertyName, $product)
{
    // inutile de tester le DATABASE_ID puisqu'on passe un ID
	$SQLRequest = "SELECT PPT._Type, PPV._StringValue, PPV._IntValue, " . "PPV._FloatValue, PPV._DateValue ";
	$SQLRequest .= "FROM PropertyValue PPV, Property PPT WHERE PPT._Name = '%s' " . "AND PPV._Product = %s ";
	$SQLRequest .= "AND PPV._Property = PPT._Id";
	$SQLRequest = sprintf($SQLRequest, $propertyName, $product->getId());
	$result = executeSQL($SQLRequest);

	if (is_array($result->fields) && array_key_exists("_Type", $result->fields)) {
		$type = "_" . getPropertyTypeColumn($result->fields["_Type"]);
		if ($result->fields["_Type"] == Property::OBJECT_TYPE) {
			$ptype = $product->getProductType();

			if ($ptype instanceof ProductType) {
				$properties = array_change_key_case($ptype->getPropertyArray());
				if (array_key_exists($propertyName, $properties)) {
					$prop = $properties[$propertyName];
					return $prop->getValue($product->getId());
				}
			}
		}
		if (array_key_exists($type, $result->fields)) {
			return empty($result->fields[$type])?"N/A":$result->fields[$type];
		}
	}
	return "N/A";
}

// }}}
// request_DynamicProperties_Search() {{{

/**
 *
 * @access public
 * @return void
 */
function request_DynamicProperties_Search($Attribute, $Type, $Operator, $Value)
{
	$SQL  = "SELECT P2._Product FROM Property P1, PropertyValue P2 ";
	$SQL .= "WHERE P1._Name='%s' AND P1._Id=P2._Property AND P2._%s%s'%s'";
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Property')) {
        $SQL .= ' AND P1._DBId=' . DATABASE_ID;
    }
	$SQL = sprintf($SQL, $Attribute, $Type, $Operator, $Value);
	return ExecuteSQL($SQL);
}

// }}}
// getSupplierCollectionIds() {{{

function getSupplierCollectionIds($ActorId)
{
	$sql = "SELECT _Supplier as spcSupplier FROM SupplierCustomer WHERE _Customer = $ActorId";
	$result = ExecuteSQL($sql);
	if (false === $result) {
        return new PEAR_Error($db->ErrorMsg());
	}
	$IdArray = array();
	while (!$result->EOF) {
        $IdArray[] = $result->fields["spcSupplier"];
        $result->MoveNext();
	}
	$result->Close();
	return $IdArray;
}

// }}}
// getCustomerCollectionIds() {{{

function getCustomerCollectionIds($ActorId)
{
	$sql = "SELECT _Customer as spcCustomer FROM SupplierCustomer WHERE _Supplier = $ActorId";
	$result = ExecuteSQL($sql);
	if (false === $result) {
        return new PEAR_Error($db->ErrorMsg());
	}
	$IdArray = array();
	while (!$result->EOF) {
        $IdArray[] = $result->fields["spcCustomer"];
        $result->MoveNext();
	}
	$result->Close();
	return $IdArray;
}

// }}}
// getOrderedQtyPerWeekForSupplier() {{{

/*
* Les fonctions suivantes sont utilisees pour l'optimisation des appro (xmlrpc)
**/
/**
 * Retourne les qtes de Product pour un Supplier donne, impliques dans les
 * ProductCommand client (sorties normales) pour les bons crenaux de dates,
 * t. q. ProductCommand.Expeditor=[actor_connecté]
 *
 * @param integer $ActorId: id de l'actor connecte
 * @return Object resultset
 */
function getOrderedQtyPerWeekForSupplier($ActorId, $SupplierId, $TimeStampBegin,
        $TimeStampEnd)
{
    require_once('Objects/MovementType.const.php');
	$sql = 'SELECT CMD._IsEstimate as isEstimate, PDT._Id as pdtId, SUM(ACM._Quantity) AS Qty
         FROM Command CMD, Product PDT, ActorProduct AP, ActivatedMovement ACM
         WHERE ACM._ProductCommand=CMD._Id AND ACM._Product=PDT._Id
         AND AP._Product=PDT._Id AND AP._Actor=' . $SupplierId . '
         AND AP._Priority=1 ';
    if (Preferences::get('EstimateBehaviour', 0) != 1) {
        $sql .= 'AND CMD._IsEstimate=0 ';
    }
    $sql .= 'AND CMD._Expeditor=' . $ActorId . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)<' . $TimeStampEnd . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)>' . $TimeStampBegin . '
         AND PDT._Activated = 1 AND ACM._Type=' . SORTIE_NORMALE . '
         GROUP BY CMD._IsEstimate, PDT._Id;';
	return executeSQL($sql);
}

// }}}
// getInternalActivatedMovementPerWeek() {{{

/**
 * TODO: filtre sur Actor connecté??: pas pour l'instant
 * Retourne les qtes de Product pour un Supplier donne, impliques dans les
 * ACM internes pour les bons crenaux de dates
 * Pour les entrees internes, on ne regarde que ceux non totalemt executes
 * Si $week<1 => on ne recupere que les sorties internes (toutes: livrees ou non)
 * (sert a faire les stats par les moyennes glissantes(?))
 * Si $week>=1 => on recupere les entrees et sorties internes *non encore livrees*
 *
 * @param week: indice de la semaine:
 * @return Object resultset
 */
function getInternalActivatedMovementPerWeek($SupplierId, $TimeStampBegin, $TimeStampEnd, $week)
{
    require_once('Objects/MovementType.const.php');
    require_once('Objects/ActivatedMovement.php');
    if ($week < 1) {
        $sql = '
            SELECT PDT._Id as pdtId, SUM(-ACM._Quantity) AS qty
            FROM ActivatedMovement ACM, Product PDT, ActorProduct AP
            WHERE ACM._Product=PDT._Id
            AND AP._Product=PDT._Id AND AP._Actor=' . $SupplierId . '
            AND AP._Priority=1
            AND ACM._Type= ' . SORTIE_INTERNE . '
            AND UNIX_TIMESTAMP(ACM._StartDate)<' . $TimeStampEnd . '
            AND UNIX_TIMESTAMP(ACM._StartDate)>' . $TimeStampBegin . '
            AND PDT._Activated = 1
            GROUP BY PDT._Id;';
    }
    else {
        $sql = '
            (SELECT PDT._Id as pdtId,
            SUM(ACM._Quantity - IFNULL(EXM._RealQuantity, 0)) AS qty
            FROM ActivatedMovement ACM
            LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement,
            Product PDT, ActorProduct AP
            WHERE ACM._Product=PDT._Id
            AND AP._Product=PDT._Id AND AP._Actor=' . $SupplierId . '
            AND AP._Priority=1
            AND ACM._Type=' . ENTREE_INTERNE . '
            AND ACM._State!=' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
            AND UNIX_TIMESTAMP(ACM._StartDate)<' . $TimeStampEnd . '
            AND UNIX_TIMESTAMP(ACM._StartDate)>' . $TimeStampBegin . '
            AND PDT._Activated = 1
            GROUP BY PDT._Id)
            UNION
            (SELECT PDT._Id as pdtId,
            SUM(IFNULL(EXM._RealQuantity, 0) - ACM._Quantity) AS qty
            FROM ActivatedMovement ACM
            LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement,
            Product PDT, ActorProduct AP
            WHERE ACM._Product=PDT._Id
            AND AP._Product=PDT._Id AND AP._Actor=' . $SupplierId . '
            AND AP._Priority=1
            AND ACM._Type=' . SORTIE_INTERNE . '
            AND ACM._State!=' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
            AND UNIX_TIMESTAMP(ACM._StartDate)<' . $TimeStampEnd . '
            AND UNIX_TIMESTAMP(ACM._StartDate)>' . $TimeStampBegin . '
            AND PDT._Activated = 1
            GROUP BY PDT._Id)
            ;';
    }
	return executeSQL($sql);
}

// }}}
// getLateInternalActivatedMovement() {{{

/**
 * TODO: filtre sur Actor connecté??: pas pour l'instant
 * Retourne les qtes de Product pour un Supplier donne, impliques dans les
 * ACM internes en retard ou prevus pour la semaine 0 et non executes en entier
 *
 * @param mixed $ProductIdArray array of integer : Ids de Product
 * @param integer $TimeStampEnd timestamp: limite sup
 * @return Object resultset
 */
function getInternalMvtQtyBeforeEndOfWeek($ProductIdArray, $TimeStampEnd)
{
    require_once('Objects/MovementType.const.php');
    require_once('Objects/ActivatedMovement.php');
	$ProductIds = implode(',', $ProductIdArray);
	$sql = '
        (SELECT ACM._Product as pdtId,
        SUM(ACM._Quantity - IFNULL(EXM._RealQuantity, 0)) AS qty
        FROM ActivatedMovement ACM
        LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
        WHERE ACM._Product IN ('.$ProductIds.')
        AND ACM._Type=' . ENTREE_INTERNE . '
        AND ACM._State !=' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
        AND UNIX_TIMESTAMP(ACM._StartDate)<' . $TimeStampEnd . '
        GROUP BY ACM._Product)
        UNION
        (SELECT ACM._Product as pdtId,
        SUM(-(ACM._Quantity - IFNULL(EXM._RealQuantity, 0))) AS qty
        FROM ActivatedMovement ACM
        LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
        WHERE ACM._Product IN ('.$ProductIds.')
        AND ACM._Type=' . SORTIE_INTERNE . '
        AND ACM._State !=' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
        AND UNIX_TIMESTAMP(ACM._StartDate)<' . $TimeStampEnd . '
        GROUP BY ACM._Product)
        ;';
	return executeSQL($sql);
}
// Algo a conserver: f(x) = -2x + 1 => f(1)=-1 (sorties), et f(0)=1 (entrees)

// }}}
// getInternalQtyAtEndOfWeek() {{{

/**
 * TODO: filtre sur Actor connecté??: pas pour l'instant
 * Retourne les qtes de Product pour un Supplier donne, impliques dans les
 * ACM internes prevus pour la semaine 0 et non executes en entier
 * f(x) = -2x + 1 => f(1)=-1 (sorties), et f(0)=1 (entrees)
 * Permet d'additionner les enrees et retrancher les sorties
 * @param mixed $ProductIdArray array of integer : Ids de Product
 * @param integer $TimeStampBegin timestamp: limite inf
 * @param integer $TimeStampEnd timestamp: limite sup
 * @return Object resultset

function getInternalQtyAtEndOfWeek($ProductIdArray, $TimeStampBegin, $TimeStampEnd) {
    require_once('Objects/MovementType.const.php');
    require_once('Objects/ActivatedMovement.php');
	$ProductIds = implode(',', $ProductIdArray);
	$sql = '
    SELECT ACM._Product as pdtId,
    SUM((-2 * MVT._EntrieExit + 1) * (ACM._Quantity - IFNULL(EXM._RealQuantity, 0))) AS qty
    FROM MovementType MVT, ActivatedMovement ACM
    LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
    WHERE ACM._Type=MVT._Id AND ACM._Product IN ('.$ProductIds.')
    AND ACM._Type IN (' . ENTREE_INTERNE . ', ' . SORTIE_INTERNE . ')
    AND ACM._State !=' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
    AND UNIX_TIMESTAMP(ACM._StartDate)<' . $TimeStampEnd . '
    AND UNIX_TIMESTAMP(ACM._StartDate)>' . $TimeStampBegin . '
    GROUP BY ACM._Product';
	return executeSQL($sql);
}*/

// }}}
// getProductPromoImpactPerWeek() {{{

/**
 * Retourne pour un Product donne, pour les semaines a traiter
 * la Promotion qui, chaque semaine, a le ApproIpactRate le plus fort:
 * On ne tiendra compte que de celles-la pour faire les stats
 *
 * @param mixed $ProductIdArray array of integer : Ids de Product
 * @return array
 */
function getProductPromoImpactPerWeek($ProductIdArray)
 {
	$ApproImpactRateArray = array(); // Contiendra les resultats
	$ProductIds = implode(',', $ProductIdArray);
	$timeStampArray = getWeekTimeStamps();

	for ($j = - $_SESSION['passedWeekNumber'] + 1;$j <= $_SESSION['futureWeekNumber'];$j++) {
        foreach($ProductIdArray as $pdtId) {
            $ApproImpactRateArray[$pdtId][$j] = 100;  // Initialisation
		}
		// Parmi les promotions qui sont affectees a chaque Product, on recupere
        // le ApproImpactRate le plus fort
		$sql = 'SELECT PP._ToProduct AS pdtId, MAX(PRM._ApproImpactRate) AS Impact
            FROM Promotion PRM, prmProduct PP
            WHERE PP._FromPromotion=PRM._Id AND PP._ToProduct IN ('.$ProductIds.')
            AND UNIX_TIMESTAMP(_EndDate) > ' . $timeStampArray[$j] . '
            AND UNIX_TIMESTAMP(_StartDate) < ' . $timeStampArray[$j + 1] . '
            GROUP BY PP._ToProduct;';

		$rs = executeSQL($sql);
		while ($rs && !$rs->EOF) {
            $ApproImpactRateArray[$rs->fields['pdtId']][$j] +=
                    doubleval($rs->fields['Impact']);
            $rs->MoveNext();
		}
	}
	return $ApproImpactRateArray;
}

// }}}
// getPromoSaisonalityImpactPerWeek() {{{

/**
 * Retourne pour un Product donne, pour les semaines a traiter,
 * la somme: (Impacts Promotions + Impacts Saisonalites + 100)
 * c'est l'impact attendu sur la consommation
 *
 * @param  $ProductIdArray array of integer : Ids de Product
 * @param array $impactPerWeek array of decimal: impact des Promos par semaine
 * @return array of decimal
 */
 function getPromoSaisonalityImpactPerWeek($ProductIdArray, $impactPerWeek)
{
	$ProductIds = implode(',', $ProductIdArray);
	$TimeStampArray = getWeekTimeStamps();
	$sqlBegin = 'SELECT DISTINCT(PDT._Id) AS pdtId, SAI._Rate AS saiRate
          FROM Saisonality SAI, saiProduct SPD, saiProductKind SPK, Product PDT,
          Property PPT, PropertyValue PPV
          WHERE ((SPD._ToProduct=PDT._Id AND SPD._FromSaisonality=SAI._Id)
          OR (PPV._Product=PDT._Id AND PPV._Property=PPT._Id AND PPT._Name=\'ProductKind\'
          AND PPV._IntValue=SPK._ToProductKind AND SPK._FromSaisonality=SAI._Id))
		  AND UNIX_TIMESTAMP(SAI._StartDate)<';
	for ($j = - $_SESSION['passedWeekNumber'] + 1;$j <= $_SESSION['futureWeekNumber'];$j++) {
		$sql = $sqlBegin . $TimeStampArray[$j + 1] . '
          AND UNIX_TIMESTAMP(SAI._EndDate)>' . $TimeStampArray[$j] . '
          AND PDT._Id IN (' . $ProductIds . ') ';
		$sql.= 'GROUP BY PDT._Id;';

		$rs = executeSQL($sql);
		while ($rs && !$rs->EOF) {
			$impactPerWeek[$rs->fields['pdtId']][$j] +=
                    doubleval($rs->fields['saiRate']);
			$rs->MoveNext();
		}
		//  1 seule Saisonality possible a une date donnee, pour un Product donne
    }
	return $impactPerWeek;
}

// }}}
// getDeliveredQtyPerWeekForSupplier() {{{

// Qtes commandees (cmdes client) et deja en partie ou totalemt sorties de stock
function getDeliveredQtyPerWeekForSupplier($ActorId, $SupplierId, $TimeStampBegin, $TimeStampEnd)
{
	require_once('Objects/ActivatedMovement.php');
    require_once('Objects/MovementType.const.php');
	$sql = 'SELECT PDT._Id as pdtId, SUM(EXM._RealQuantity) as realQty
         FROM Command CMD, Product PDT, ActorProduct AP, ActivatedMovement ACM
         LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
         WHERE ACM._ProductCommand=CMD._Id AND ACM._Product=PDT._Id
         AND AP._Product=PDT._Id AND AP._Actor=' . $SupplierId . '
         AND AP._Priority=1 AND CMD._IsEstimate=0 AND CMD._Expeditor=' . $ActorId . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)<' . $TimeStampEnd . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)>' . $TimeStampBegin . '
         AND PDT._Activated = 1 AND ACM._Type=' . SORTIE_NORMALE . '
         AND ACM._State NOT IN (' . ActivatedMovement::CREE . ', ' . ActivatedMovement::BLOQUE . ')
         GROUP BY PDT._Id;';
	return executeSQL($sql);
}

// }}}
// getWaitedQtyPerWeek() {{{

/**
 * Retourne les quantites (par produit) de produits commandes pour reappro, pour
 * un creneau de dates, et qui ne sont pas encore livres.
 * Si le param $TimeStampBegin n'est pas fourni, on ignore la borne inferieure
 *
 * @return object
 */
function getWaitedQtyPerWeek($ProductIdArray, $ActorId, $TimeStampEnd, $TimeStampBegin='')
{
    require_once('Objects/MovementType.const.php');
	$sql = 'SELECT PDT._Id as pdtId,
	     SUM(ACM._Quantity - IFNULL(EXM._RealQuantity, 0)) as qty
         FROM Command CMD, Product PDT, ActorProduct AP, ActivatedMovement ACM
         LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
         WHERE ACM._ProductCommand=CMD._Id AND ACM._Product=PDT._Id
         AND AP._Product=PDT._Id AND AP._Actor=CMD._Expeditor ';
    if (Preferences::get('EstimateBehaviour', 0) != 1) {
        $sql .= 'AND CMD._IsEstimate=0 ';
    }
    $sql .= 'AND CMD._Destinator=' . $ActorId . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)<' . $TimeStampEnd;
    if ($TimeStampBegin != '') {
        $sql .= ' AND UNIX_TIMESTAMP(CMD._WishedStartDate)>=' . $TimeStampBegin;
    }
    $sql .= ' AND PDT._Id IN (' . implode(",", $ProductIdArray) . ')
         AND ACM._State <> ' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
         AND ACM._Type=' . ENTREE_NORMALE . '
         GROUP BY pdtId;';
	return executeSQL($sql);
}

// }}}
// getLateOrderedQty() {{{

/**
 * Retourne les quantites (par produit) de produits commandes (sorties normales)
 * qui auraient du etre livres, mais qui ne le sont pas, et celles à livrer
 * d'ici a fin de semaine courante
 *
 * @return object
 */
function getLateOrderedQty($SupplierId, $ActorId, $TimeStampEnd)
{
	require_once('Objects/ActivatedMovement.php');
    require_once('Objects/MovementType.const.php');
	$sql = 'SELECT PDT._Id as pdtId,
	     SUM(ACM._Quantity - IFNULL(EXM._RealQuantity, 0)) as qty
         FROM Command CMD, Product PDT, ActorProduct AP, ActivatedMovement ACM
         LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
         WHERE ACM._ProductCommand=CMD._Id AND ACM._Product=PDT._Id
         AND AP._Product=PDT._Id AND AP._Actor=' . $SupplierId . ' ';
    if (Preferences::get('EstimateBehaviour', 0) != 1) {
        $sql .= 'AND CMD._IsEstimate=0 ';
    }
    $sql .= 'AND CMD._Expeditor=' . $ActorId . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)<' . $TimeStampEnd . '
         AND ACM._State <> ' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
         AND ACM._Type=' . SORTIE_NORMALE . '
         GROUP BY pdtId;';
	return executeSQL($sql);
}

// }}}
// getOrderedQtyPerWeekForSupplier() {{{

function getOrderedQtyAtEndOfWeek($ProductIdArray, $ActorId, $TimeStampBegin, $TimeStampEnd)
{
	require_once('Objects/ActivatedMovement.php');
    require_once('Objects/MovementType.const.php');
	$sql = 'SELECT DISTINCT PDT._Id as pdtId,
         SUM(ACM._Quantity - IFNULL(EXM._RealQuantity, 0)) as qty
         FROM Command CMD, Product PDT, ActivatedMovement ACM
         LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
         WHERE ACM._ProductCommand=CMD._Id AND ACM._Product=PDT._Id ';
    if (Preferences::get('EstimateBehaviour', 0) != 1) {
        $sql .= 'AND CMD._IsEstimate=0 ';
    }
    $sql .= 'AND CMD._Expeditor= ' . $ActorId . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)<' . $TimeStampEnd . '
         AND UNIX_TIMESTAMP(CMD._WishedStartDate)>=' . $TimeStampBegin . '
         AND PDT._Id IN (' . implode(',', $ProductIdArray) . ')
         AND ACM._State <> ' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT . '
         AND ACM._Type=' . SORTIE_NORMALE . '
         GROUP BY pdtId;';
	return executeSQL($sql);
}

// }}}
// getProductInfoList() {{{

// $SupplierId est necessaire dans le cas où n ActorProduct pour 1 Product
function getProductInfoList($ProductIdArray, $SupplierId)
{
    $locale = I18N::getLocaleCode();
	$sql = 'SELECT DISTINCT P._Id AS pdtId, P._BaseReference AS pdtBaseReference,
         I._StringValue_'.$locale.' AS pdtName, AP._AssociatedProductReference AS supplierRef,
         AP._BuyUnitQuantity AS BuyUnitQty,
         P._SellUnitMinimumStoredQuantity AS MiniStoredQty, P._SellUnitVirtualQuantity as QV,
         SUM(LPQ._RealQuantity) AS QR
         FROM ActorProduct AP, I18nString I, Product P LEFT JOIN LocationProductQuantities LPQ ON P._Id = LPQ._Product
         WHERE P._Id  IN (' . implode(",", $ProductIdArray) . ')
         AND I._Id=P._Name AND AP._Product=P._Id AND AP._Actor=' . $SupplierId . '
         GROUP BY P._Id
         ORDER BY _BaseReference;';
	$result = executeSQL($sql);
	return $result;
}

// }}}
// stockProductArrayForSelect() {{{

/*
* Recupere le tableau array(Id=>BaseReference) des produits en stock si $Activated=-1,
* et de tous les produits actives en stock si $Activated=1
* et de tous les produits desactives en stock si $Activated=0
* @param $Activated integer
* @param $SitOwnerId integer: si >0, on filtre en plus par proprietaire du site
* de stockage contenant les Location ds lesquels sont les Product stockes
* @param $lpqActivated integer: si =1, seulement les LPQ et Location actives
* @return array
**/
function stockProductArrayForSelect($Activated = 1, $SitOwnerId = 0, $lpqActivated = 0, $field='BaseReference')
{
    // gestion de Name I18niser
    $otherTables=$otherConditions='';
    if($field == 'Name') {
        $otherTables = 'I18nString I1, ';
        $otherConditions = ' AND PDT._'.$field.'=I1._Id';
        $field = '_' . $field . ', I1._StringValue_' . I18n::getLocaleCode();
    } else {
	    $field = '_' . $field;
    }

	$ProductArray = array();
	$Activated = ($Activated >= 0)?' AND PDT._Activated=' . $Activated:'';
	$lpqActivated = ($lpqActivated == 1)?'AND LPQ._Activated=1 AND LOC._Activated=1':'';
	$otherTables .= ($SitOwnerId > 0)?'Store STO, Site SIT, ':'';
	$otherConditions .= ($SitOwnerId > 0)?
            ' AND LOC._Store = STO._Id AND STO._StorageSite = SIT._Id AND SIT._Owner='
            . $SitOwnerId:'';

	$sql = 'SELECT DISTINCT PDT._Id as pdtId, PDT.'.$field.' as pdtBaseReference,
         SUM(LPQ._RealQuantity) as qty
         FROM ' . $otherTables . 'Location LOC, Product PDT
         LEFT JOIN LocationProductQuantities LPQ ON PDT._Id=LPQ._Product
         WHERE LPQ._Location = LOC._Id ' . $lpqActivated . $otherConditions;
	$sql .= $Activated;
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Product')) {
        $sql .= ' AND PDT._DBId=' . DATABASE_ID;
    }
	$sql .= ' GROUP BY pdtId HAVING (qty>0)';
	$sql .= ' ORDER BY pdtBaseReference;';
	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$ProductArray[$rs->fields['pdtId']] = $rs->fields['pdtBaseReference'];
			$rs->MoveNext();
		}
	}
	return $ProductArray;
}

// }}}
// ProductArrayForSelect() {{{

/*
* Recupere le tableau array(Id=>BaseReference) de tous les produits
* @return array
**/
function ProductArrayForSelect($field='BaseReference')
{
    $otherTables=$otherConditions='';
    if($field == 'Name') {
        $otherTables = ', I18nString I1';
        $otherConditions = ' PDT._'.$field.'=I1._Id';
        $field = '_' . $field . ', I1._StringValue_' . I18n::getLocaleCode();
    } else {
        $field = '_' . $field;
    }
	$ProductArray = array();
    $sql  = 'SELECT DISTINCT PDT._Id as pdtId, PDT.'.$field.' as pdtBaseReference FROM Product PDT';
    $sql .= $otherTables;
    $padding = ' WHERE';
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Product')) {
        $sql .= ' WHERE PDT._DBId=' . DATABASE_ID;
        $padding = ' AND';
    }
    if($otherConditions != '') {
        $sql .= $padding . $otherConditions;
    }
    $sql .= ' ORDER BY pdtBaseReference;';
	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$ProductArray[$rs->fields['pdtId']] = $rs->fields['pdtBaseReference'];
			$rs->MoveNext();
		}
	}
	return $ProductArray;
}

// }}}
// Request_ActorCityNameList() {{{

/**
 *
 * @access public
 * @return void
 */
function Request_ActorCityNameList()
{
	$sql = 'SELECT ACT._Id as actId, CTN._Name as ctnName
            FROM CityName CTN, CountryCity CTC, Site SIT, Actor ACT
            WHERE CTN._Id = CTC._CityName AND
                  CTC._Id = SIT._CountryCity AND
                  SIT._Id = ACT._MainSite AND
                  ACT._Active = 1';
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Actor')) {
        $sql .= ' AND ACT._DBId=' . DATABASE_ID . ' ';
    }
    return executeSQL($sql);
}

// }}}
// Request_ActorCommandsBySupplier() {{{

/*
* Utilisé par les fichiers BoardXOXOXO.php
* calcule la somme la depense pour un acteur pour un fournisseur
*
* @param $actorId id de l'acteur
* @param $supplierId id supplier
* @param $start_date mysql datetime date debut du calcul
* @param $end_date mysql datetime date fin du calcul
* @param $commandType le type de la commande
* @return array
**/
function Request_ActorCommandsBySupplier($expeditorId, $supplierId, $dateStart, $dateEnd, $commandType, $currency=false)
{
    $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
    if (in_array($commandType, $takeSupplier)) {
    	$sql = "SELECT ACI._PriceHT, ACI._Promotion, " .
	    "ACI._Handing as _aciHanding, C._Handing as _cHanding, " .
	    "ACI._Quantity, C._CustomerRemExcep " .
	    "FROM Product P, CommandItem ACI, Command C, SupplierCustomer SC " .
	    "WHERE P._Id=ACI._Product AND ACI._Command=C._Id  " .
	    "AND C._ClassName='ProductCommand' " .
	    "AND SC._Id=C._SupplierCustomer " .
	    "AND SC._Supplier=$expeditorId AND P._Supplier=$supplierId " .
	    "AND C._CommandDate<='$dateEnd' AND C._CommandDate>='$dateStart' " .
	    "AND C._IsEstimate=0 AND C._Type=$commandType";
    } else {
	    $sql = "SELECT ACI._PriceHT, ACI._Promotion, " .
	    "ACI._Handing as _aciHanding, C._Handing as _cHanding, " .
	    "ACI._Quantity, C._CustomerRemExcep " .
	    "FROM Product P, CommandItem ACI, Command C " .
	    "WHERE P._Id=ACI._Product AND ACI._Command=C._Id  " .
	    "AND C._ClassName='ProductCommand' " .
	    "AND C._Expeditor=$expeditorId AND P._Supplier=$supplierId " .
	    "AND C._CommandDate<='$dateEnd' AND C._CommandDate>='$dateStart' " .
	    "AND C._IsEstimate=0 AND C._Type=$commandType";
    }
    if($currency) {
        $sql .= " AND C._Currency=$currency";
    }
	return ExecuteSQL($sql);
}

// }}}
// Request_CustomersWithCommand() {{{

/*
 * retourne les ids des clients ayant passe au moins une commande
 */
function Request_CustomersWithCommand($actorId, $start_date, $end_date, $commandType, $currency)
{
	$takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
    $cliIdsArray = array();
    if(in_array($commandType, $takeSupplier)) {
        $sql = "SELECT DISTINCT CMD._Destinator FROM Command CMD, SupplierCustomer SC ";
        $sql .= "WHERE CMD._SupplierCustomer=SC._Id ";
        $sql .= "AND SC._Supplier='$actorId' ";
	    $sql .= "AND CMD._CommandDate<='$end_date' AND CMD._CommandDate>='$start_date' ";
	    $sql .= "AND CMD._IsEstimate=0 AND CMD._Type=" . $commandType;
    } else {
        $sql = "SELECT DISTINCT CMD._Destinator FROM Command CMD WHERE CMD._Expeditor='$actorId' ";
	    $sql .= "AND CMD._CommandDate<='$end_date' AND CMD._CommandDate>='$start_date' ";
	    $sql .= "AND CMD._IsEstimate=0 AND CMD._Type=" . $commandType;
    }
    $sql .= " AND CMD._Currency=$currency";
	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$cliIdsArray[] = $rs->fields['_Destinator'];
			$rs->MoveNext();
		}
	}
	return $cliIdsArray;
}

// }}}
// Request_CommercialsWithCommand() {{{

/*
 * Retourne les Ids des commerciaux ayant au moins 1 commande
 */
function Request_CommercialsWithCommand($actorId, $start_date, $end_date, $commandType, $currencyId)
{
	$cliIdsArray = array();
	$takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
	if (in_array($commandType, $takeSupplier)) {
		$sql = "SELECT DISTINCT CMD._Commercial FROM Command CMD, SupplierCustomer SC ";
		$sql .= "WHERE CMD._SupplierCustomer=SC._Id ";
		$sql .= "AND SC._Supplier='$actorId' ";
    	$sql .= "AND CMD._CommandDate<='$end_date' AND CMD._CommandDate>='$start_date' ";
    	$sql .= "AND CMD._IsEstimate=0 AND CMD._Type='$commandType'";
	} else {
    	$sql = "SELECT DISTINCT CMD._Commercial FROM Command CMD WHERE CMD._Expeditor='$actorId' ";
    	$sql .= "AND CMD._CommandDate<='$end_date' AND CMD._CommandDate>='$start_date' ";
    	$sql .= "AND CMD._IsEstimate=0 AND CMD._Type='$commandType'";
    }
    $sql .= " AND CMD._Currency=$currencyId";
	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$cliIdsArray[] = $rs->fields['_Commercial'];
			$rs->MoveNext();
		}
	}
	return $cliIdsArray;
}

// }}}
// Request_SuppliersWithCommand() {{{

/*
 * Retourne les Ids des supppliers dont au moins 1 prod a ete commandé
 */
function Request_SuppliersWithCommand ($actorId, $start_date, $end_date, $commandType, $currency)
{
    $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
	$supIdsArray = array();

	if (in_array($commandType, $takeSupplier)) {
		$sql = "SELECT DISTINCT P._Supplier FROM Product P, CommandItem ACI, Command C " .
		"SupplierCustomer SC " .
	    "WHERE P._Id=ACI._Product AND ACI._Command=C._Id  " .
	    "AND C._ClassName='ProductCommand' AND SC._Supplier=$actorId " .
	    "AND SC._Id=C._SupplierCustomer " .
	    "AND C._CommandDate<='$end_date' AND C._CommandDate>='$start_date' ".
	    "AND C._IsEstimate=0 AND C._Type=$commandType";
	} else {
	    $sql = "SELECT DISTINCT P._Supplier FROM Product P, CommandItem ACI, Command C " .
	    "WHERE P._Id=ACI._Product AND ACI._Command=C._Id  " .
	    "AND C._ClassName='ProductCommand' AND C._Expeditor=$actorId " .
	    "AND C._CommandDate<='$end_date' AND C._CommandDate>='$start_date' ".
	    "AND C._IsEstimate=0 AND C._Type=$commandType";
    }
    $sql .= " AND C._Currency=$currency";

	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$supIdsArray[] = $rs->fields['_Supplier'];
			$rs->MoveNext();
		}
	}
	return $supIdsArray;
}

// }}}
// Request_GetSuppliersWithOrders() {{{

function Request_GetSuppliersWithOrders($actorId, $dateStart, $DateEnd, $commandType, $currency)
{
    $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
	$supIdsArray = array();

	if(in_array($commandType, $takeSupplier)) {
	    $sql = "SELECT DISTINCT C._Expeditor FROM Command C, Actor A, " .
	    "SupplierCustomer SC " .
	    "WHERE C._SupplierCustomer=SC._Id AND SC._Supplier=$actorId " .
	    "AND A._Id=C._Expeditor " .
	    "AND A._ClassName IN ('Supplier', 'AeroSupplier')" .
	    "AND C._CommandDate<='$DateEnd' AND C._CommandDate>='$dateStart' " .
	    "AND C._IsEstimate=0 AND C._Type=$commandType";
	} else {
	    $sql = "SELECT DISTINCT C._Expeditor FROM Command C, Actor A " .
	    "WHERE C._Destinator=$actorId AND A._Id=C._Expeditor " .
	    "AND A._ClassName IN ('Supplier', 'AeroSupplier')" .
	    "AND C._CommandDate<='$DateEnd' AND C._CommandDate>='$dateStart' " .
	    "AND C._IsEstimate=0 AND C._Type=$commandType";
    }
    $sql .= " AND _Currency=$currency";

	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$supIdsArray[] = $rs->fields['_Expeditor'];
			$rs->MoveNext();
		}
	}
	return $supIdsArray;
}

// }}}
// Request_CategoryWithCommands() {{{

function Request_CategoryWithCommands($actorId, $dateStart, $DateEnd, $commandType, $currency)
{
    $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
	$supIdsArray = array();

	if (in_array($commandType, $takeSupplier)) {
	    $sql = "SELECT DISTINCT A._Category FROM Command C, Actor A, SupplierCustomer SC " .
        "WHERE C._SupplierCustomer=SC._Id AND SC._Supplier=$actorId ".
        "AND C._Destinator=A._Id " .
        "AND C._CommandDate<='$DateEnd' AND C._CommandDate>='$dateStart' " .
        "AND C._IsEstimate=0 AND A._Category > 0 AND C._Type=$commandType";
	} else {
	    $sql = "SELECT DISTINCT A._Category FROM Command C, Actor A " .
	    "WHERE C._Expeditor=$actorId AND C._Destinator=A._Id " .
	    "AND C._CommandDate<='$DateEnd' AND C._CommandDate>='$dateStart' " .
	    "AND C._IsEstimate=0 AND A._Category > 0 AND C._Type=$commandType";
	}
    $sql .= " AND CMD._Currency=$currency";

	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$supIdsArray[] = $rs->fields['_Category'];
			$rs->MoveNext();
		}
	}
	return $supIdsArray;
}

// }}}
// Request_ProductWithCommand() {{{

function Request_ProductWithCommand ($actorId, $dateStart, $DateEnd, $commandType, $currency)
{
    $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
	$supIdsArray = array();

	if (in_array($commandType, $takeSupplier)) {
		$sql = "SELECT DISTINCT ACI._Product FROM Command C, CommandItem ACI " .
		"SupplierCustomer SC " .
	    "WHERE C._SupplierCustomer=SC._Id AND SC._Supplier=$actorId " .
	    "AND ACI._Command=C._Id " .
	    "AND C._CommandDate<='$DateEnd' AND C._CommandDate>='$dateStart' ".
	    "AND C._IsEstimate=0 AND C._Type='$commandType'";
	} else {
	    $sql = "SELECT DISTINCT ACI._Product FROM Command C, CommandItem ACI " .
	    "WHERE C._Expeditor=$actorId AND ACI._Command=C._Id " .
	    "AND C._CommandDate<='$DateEnd' AND C._CommandDate>='$dateStart' ".
	    "AND C._IsEstimate=0 AND C._Type='$commandType'";
    }
    $sql .= " AND C._Currency=$currency";

	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$supIdsArray[] = $rs->fields['_Product'];
			$rs->MoveNext();
		}
	}
	return $supIdsArray;
}

// }}}
// request_getAreoPartNumberDict() {{{

function request_getAreoPartNumberDict()
{
	$partsDict = array();
	$sql = "SELECT _Id, _BaseReference FROM Product WHERE _ClassName='AeroProduct'";
    if (defined('DATABASE_ID') && !Object::isPublicEntity('AeroProduct')) {
        $sql .= ' AND _DBId=' . DATABASE_ID;
    }
	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$partsDict[$rs->fields['_Id']] = $rs->fields['_BaseReference'];
			$rs->MoveNext();
		}
	}
	return $partsDict;
}

// }}}
// request_getAreoSerialNumberDict() {{{

function request_getAreoSerialNumberDict()
{
	$partsDict = array();
	$sql = "SELECT _Id, _BaseReference FROM Product WHERE _ClassName='AeroProduct'";
    if (defined('DATABASE_ID') && !Object::isPublicEntity('AeroProduct')) {
        $sql .= ' AND _DBId=' . DATABASE_ID;
    }
	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$partsDict[$rs->fields['_Id']] = $rs->fields['_BaseReference'];
			$rs->MoveNext();
		}
	}
	return $partsDict;
}

// }}}
// request_getConcreteProductSerialNumberDict() {{{

function request_getConcreteProductSerialNumberDict()
{
	$partsDict = array();
	$sql = "SELECT _Id, _SerialNumber FROM ConcreteProduct";
    if (defined('DATABASE_ID') && !Object::isPublicEntity('ConcreteProduct')) {
        $sql .= ' WHERE _DBId=' . DATABASE_ID;
    }
	$rs = ExecuteSQL($sql);
	if (false != $rs) {
		while (!$rs->EOF) {
			$partsDict[$rs->fields['_Id']] = $rs->fields['_SerialNumber'];
			$rs->MoveNext();
		}
	}
	return $partsDict;
}

// }}}
// request_actorIsDeletable() {{{

/**
 * Retourne true si l'acteur peut-être supprimé.
 *
 * On aurait pu faire tout ça en une seule requête, mais je n'arrive pas à
 * l'optimiser... elle plante mysql en beauté, avis aux amateurs donc...
 * Pour des raisons de temps/perfs donc on execute les requêtes une par une
 * en s'arrêtant à la première qui renvoie un résultat.
 *
 * Note: pour une explication sur %1\$s voir le manuel de php:
 * http://www.php.net/sprintf
 *
 * @param int $actorID l'id de l'acteur
 * @access public
 * @return boolean
 */
function request_actorIsDeletable($actorID)
{
	$queries = array();
	// verif si acteur est impliqué dans une commande (expéditeur, destinataire
	// ou Customer de cette commande)
	$queries[] = "select distinct(_Id) from Command
     where _Destinator=%1\$s or _Expeditor=%1\$s or _Customer=%1\$s limit 1";
	// verif si acteur est impliqué dans une opération ou une chaine
	$queries[] = "select distinct(_Id) from ChainOperation where _Actor=%s
     limit 1";
	// verif si acteur est acteur de départ ou de fin d'une chaine
	$queries[] = "select distinct(Chain._Id) from ActorSiteTransition, Chain
     where (_DepartureActor=%1\$s or _ArrivalActor=%1\$s) and
     Chain._SiteTransition=ActorSiteTransition._Id limit 1";
	// verif si acteur est "owner" d'une chaine
	$queries[] = "select distinct(_Id) from Chain where _Owner=%s limit 1";
	// verif si acteur est supplier d'un produit
	$queries[] = "select distinct(_Id) from ActorProduct where _Actor=%s limit 1";
	// verif si acteur est impliqué dans une ActivatedChainOperation
	// (ActivatedChainOperation.Actor ou ActivatedChainOperation.RealActor)
	$queries[] = "select distinct(_Id) from ActivatedChainOperation
     where _Actor=%1\$s or _RealActor=%1\$s limit 1";
	// verif si acteur est impliqué dans un WorkOrder
	$queries[] = "select distinct(_Id) from WorkOrder where _Actor=%s limit 1";
	// verif si acteur dont un UserAccount est impliqué dans une
	// ActivatedChainTask.
	$queries[] = "select distinct(UserAccount._Id)
     from UserAccount, ActivatedChainTask
         where UserAccount._Actor=%s and
         ActivatedChainTask._ValidationUser=UserAccount._Id limit 1";
	// verif si cteur (Actor ou AeroActor) dont l’utilisateur (UserAccount) est
	// commercial (Actor.commercial) d’un autre acteur (Actor ou AeroActor).
	$queries[] = "select distinct(Actor._Id) from UserAccount, Actor
         where UserAccount._Actor=%s and
         Actor._Commercial=UserAccount._Id limit 1";
	// verif si acteur est Instructeur d’un autre AeroActor.
	$queries[] = "select distinct(_Id) from Actor where _Instructor=%s limit 1";
	// on parcours les requêtes et on les execute, si une renvoie un recordset
	// non vide on retourne false, l'acteur n'est pas supprimable.
	foreach($queries as $query) {
		$rs = executeSQL(sprintf($query, $actorID));
		if (false != $rs && !$rs->EOF) {
			return false;
		}
	}
	return true;
}

// }}}
// request_ProductHandingByCategoryAlreadyExists() {{{

/**
 * Retourne false s'il n'existe pas un ProductHandingByCategory pour le produit
 * avec l'id passé en paramètre et une ou plusieurs des catégories avec les ids
 * passés en paramètre et un tableau ID_categorie Nom_Categorie sinon.
 *
 * @param int $pdtID l'id du produit
 * @param array $catIDs le tableau d'ids des catégories
 * @access public
 * @return mixed false ou le tableau
 */
function request_ProductHandingByCategoryAlreadyExists($phcID, $pdtID, $catIDs)
{
	if (empty($catIDs)) {
		return true;
	}
	$catIDs = implode(', ', $catIDs);
	$rs = ExecuteSQL(sprintf("
     select PHC._Id as AID, C._Id as CID, C._Name as CNAME
     from ProductHandingByCategory PHC, phcCategory B, Category C
     where PHC._Id <> %s and PHC._Id = B._FromProductHandingByCategory
     and PHC._Product = %s and B._ToCategory in (%s)
     and C._Id = B._ToCategory;",
					$phcID, $pdtID, $catIDs));
	if ($rs != false && !$rs->EOF) {
		while (!$rs->EOF) {
			$ret[$rs->fields['CID']] = $rs->fields['CNAME'];
			$rs->moveNext();
		}
		return $ret;
	}
	return false;
}

// }}}
// request_ProductHandingByCategory() {{{

/**
 * Requête renvoyant la remise en fonction d'un produit et d'une catégorie
 *
 * @access public
 * @param  $pdtID l'id du produit
 * @param  $catID l'id de la catégorie
 * @return mixed un float si la remise existe, false sinon
 */
function request_ProductHandingByCategory($pdtID, $catID)
{
	$sql = "SELECT A._Handing
         FROM ProductHandingByCategory A, phcCategory B
         WHERE A._Product=%s AND B._ToCategory=%s and
             B._FromProductHandingByCategory=A._Id";
	$rs = ExecuteSQL(sprintf($sql, $pdtID, $catID));
	if (!$rs || $rs->EOF) {
		return false;
	}
	$ret = $rs->fields['_Handing'];
	$rs->close();
	return $ret;
}

// }}}
// getQuantities() {{{

/*
* Utilise pour corriger les quantites virtuelles des Product
* en fonction de leur quantite en stock est des Mvts prevus
**/
function getQuantities()
{
	$sql = 'SELECT PDT._Id as pdtId, ACI._Quantity as aciQty,
         EXM._RealQuantity as deliveredQty, M._Id as mvtId
         FROM CommandItem ACI, Product PDT, MovementType M,
         ActivatedMovement ACM
         LEFT JOIN ExecutedMovement EXM ON ACM._Id=EXM._ActivatedMovement
         WHERE ACI._Product=PDT._Id
         AND ACM._ProductCommandItem=ACI._Id
         AND ACM._Type = M._Id
         AND ACM._State <> ' . ActivatedMovement::ACM_EXECUTE_TOTALEMENT;
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Product')) {
        $sql .= ' AND PDT._DBId=' . DATABASE_ID;
    }
    $sql .= ' ORDER BY PDT._BaseReference;';
	return ExecuteSQL($sql);
}

// }}}
// lastModifiedSpreadSheetDate() {{{

/**
 * Retourne la date de modification la plus récente de la table SpreadSheet
 *
 * @return object
 */
function lastModifiedSpreadSheetDate() {
    $sql = 'SELECT MAX(_LastModified) FROM SpreadSheet WHERE _Active=1';
    if (defined('DATABASE_ID') && !Object::isPublicEntity('SpreadSheet')) {
        $sql .= ' AND _DBId=' . DATABASE_ID;
    }
    return Database::connection()->execute($sql);
}

// }}}
// request_groupableBoxCount() {{{

/**
 * Retourne le nombre de box à regrouper pour la chaîne et le niveau de box
 * passés en paramètres.
 *
 * @param $achID l'id de la chaîne
 * @param $currentBoxLevel le niveau en cours de box
 * @return object
 */
function request_groupableBoxCount($achID, $ackID, $currentBoxLevel) {
    $presql = sprintf(
            'SELECT _FromBox FROM boxActivatedChainTask
            WHERE _ToActivatedChainTask = %s',
            $ackID
    );
    $ret = Database::connection()->execute($presql);
    $boxId = array();
    while ($ret && !$ret->EOF) {
        $boxId[] = $ret->fields[0];
        $ret->MoveNext();
    }

    $sql = sprintf(
        'SELECT COUNT(T0._Id) FROM Box T0
        WHERE (
            (T0._Level = %s)
            AND (T0._ParentBox = 0)
            AND (
                (T0._ActivatedChain = %s)
                OR (T0._Id IN (%s))
            )
        )',
        $currentBoxLevel-1,
        $achID,
        count($boxId)>0?implode(',', $boxId):0
    );
    return Database::connection()->execute($sql);
}

// }}}
// request_commandForCashBalance() {{{

/**
 * Récupère les commande à prendre en compte pour
 * la trésorerie prévisionnelle
 *
 * @return Object
 */
function request_commandForCashBalance($cmdType, $currency, $startDate, $endDate) {
    $request = 'SELECT DISTINCT(CMD._Id) as cmdId, CMD._Type as cmdType,
        IF(CMD._SupplierCustomer=0, CMD._WishedStartDate,
            (CASE WHEN SC._Option=0 THEN ADDDATE(CMD._WishedStartDate, SC._TotalDays)
                  WHEN SC._Option=1 THEN ADDDATE(LAST_DAY(CMD._WishedStartDate), SC._TotalDays)
                  WHEN SC._Option=2 THEN
                      ADDDATE(
                          ADDDATE(LAST_DAY(CMD._WishedStartDate), INTERVAL 1 MONTH), SC._TotalDays)
            END)) as estimatedPaymentDate,
        CMD._TotalPriceTTC as CmdTotalTTC,
        IF((select count(_Id) from AbstractDocument where _ClassName="Invoice" and _Command=CMD._Id),
            (select sum(_ToPay) from AbstractDocument where _ClassName="Invoice" and _Command=CMD._Id),
            0) as CmdPayed';
    if($cmdType==Command::TYPE_TRANSPORT) {
        $request .= ', IF((select count(aco._Id) from ActivatedChainOperation aco, CommandItem cmi where cmi._Command=CMD._Id and cmi._ActivatedChain=aco._ActivatedChain and aco._PrestationFactured=0), 0, 1) as prsFactured';
    }
    $request .= ' FROM Command CMD, SupplierCustomer SC
        WHERE (CMD._SupplierCustomer = SC._Id OR CMD._SupplierCustomer=0)
            AND CMD._Type = ' . $cmdType;
    if($currency) {
        $request .= ' AND CMD._Currency = ' . $currency;
    }
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Product')) {
        $request .= ' AND CMD._DBId=' . DATABASE_ID;
    }
    $request .= ' GROUP BY CMD._ID
        HAVING CmdTotalTTC - CmdPayed > 0 ' .
        //AND estimatedPaymentDate > NOW()
        'AND estimatedPaymentDate < "'.$endDate.'" 
        AND estimatedPaymentDate > "'.$startDate.'"';
    if($cmdType==Command::TYPE_TRANSPORT) {
        $request .= ' AND prsFactured=0';
    }
    $request .= ';';

    return Database::connection()->execute($request);
}

// }}}
// getProductArrayForFFPSelect() {{{

/**
 * Pour construire le select des CountryCity dans ZoneAddEdit.php
 * @access public
 * @return void
 */
function request_CountryCityForSelect($country=0, $state=0, $department=0)
{
    $return = array();
    $whereAddon = $fromAddon = '';
    if ($country != 0) {
        $whereAddon .= ' AND CTC._Country = ' . $country;
    }
    if ($state != 0) {
        $fromAddon .= ', Department DEP';
        $whereAddon .= ' AND CTN._Department = DEP._Id AND DEP._State = ' . $state;
    }
    if ($department != 0) {
        $whereAddon .= ' AND CTN._Department = ' . $department;
    }
    $sql = 'SELECT CTC._Id AS Id, CONCAT(CTN._Name, " (", Zip._Code, ")") as Name
     FROM CityName CTN, CountryCity CTC, Zip ' . $fromAddon . '
     WHERE (CTN._Id = CTC._CityName) AND (CTC._Zip = Zip._Id)
     AND CTC._Zone = 0 ' . $whereAddon . '
     ORDER BY Name;';
     $rs = executeSQL($sql);
     while (!$rs->EOF) {
		$return[] = array($rs->fields['Id'], utf8_encode($rs->fields['Name']));
		$rs->MoveNext();
	}
    return $return;
}

// }}}
// request_cleanPropertyValues() {{{

/**
 * Supprime les PropertyValue résiduelles après un changement du type de
 * produit sur un product.
 *
 * @access public
 * @param  int $pdtID l'id du produit
 * @param  array of int $pdtTypeIdArray l'id du type de produit et de son 
 * pdtType generique si existe
 */
function request_cleanPropertyValues($pdtID, $pdtTypeIdArray) {
    return executeSQL(sprintf(
        'DELETE FROM PropertyValue USING PropertyValue, pdtProperty
        WHERE pdtProperty._FromProductType NOT IN (' . implode(',', $pdtTypeIdArray) . ') 
        AND PropertyValue._Property=pdtProperty._ToProperty
        AND PropertyValue._Product=%s',
        $pdtID
    ));
}

// }}}
// request_customerWithoutOrderSinceThirtyDays() {{{

/**
 * Utilisé dans crons/AlertCustomerWithoutOrderSinceThirtyDays.php
 *
 * Retourne la liste des acteurs de type "Customer" qui ne sont pas
 * destinataires d'une commande client de produits (ProductCommand.Type
 * customer) depuis plus de 30 jours.
 *
 * @return object an adodb recordset
 */
function request_customerWithoutOrderSinceThirtyDays() {
    return executeSQL(
        'SELECT a._Name as ActorName FROM Actor a
        LEFT JOIN Command c ON c._Destinator = a._Id
        AND c._CommandDate > DATE_SUB(NOW(), INTERVAL 30 DAY)
        AND c._ClassName = "ProductCommand" AND c._Type = '
        . Command::TYPE_CUSTOMER . ' WHERE a._ClassName = "Customer"
        AND c._CommandNo IS NULL ORDER BY ActorName ASC'
    );
}
// }}}
// request_flowLastPieceNo() {{{

/**
 * request_flowLastPieceNo
 *
 * retourne le max PieceNo de Flow
 *
 * @access public
 * @return void
 */
function request_flowLastPieceNo() {
    $sql = 'SELECT MAX(_PieceNo) from Flow';
    if (defined('DATABASE_ID') && !Object::isPublicEntity('Flow')) {
        $sql .= 'WHERE _DBId=' . DATABASE_ID;
    }
    return executeSQL($sql);
}

// }}}
// request_flowLastPieceNo() {{{

/**
 * request_productSubstitutionds
 *
 * retourne les ids des ProductSubstitutions
 *
 * @param integer $supplierId si >0 on passe via ActorProduct, sinon, jointure 
 * sur Product directement
 * @return void
 */
function request_productSubstitutionds($pdtId, $supplierId=0) {
    if (defined('DATABASE_ID') && !Object::isPublicEntity('ProductSubstitution')) {
        $supplierClause .= ' and PS._DBId=' . DATABASE_ID;
    }
    // On passe par ActorProduct dans ce cas
    if ($supplierId > 0) {
        $sql = 'select PS._Id from ProductSubstitution PS, ActorProduct AP
            where (
            ((PS._FromProduct='.$pdtId.') and (PS._ByProduct=AP._Product))
            or
            ((PS._ByProduct='.$pdtId.') and (PS._Interchangeable=1) and (PS._FromProduct=AP._Product))
             '.'and (AP._Actor='.$supplierId.')'.
            ');';
    }
    else {
        $sql = 'select PS._Id from ProductSubstitution PS, Product P
            where (
            ((PS._FromProduct='.$pdtId.') and (PS._ByProduct=P._Id))
            or
            ((PS._ByProduct='.$pdtId.') and (PS._Interchangeable=1) and (PS._FromProduct=P._Id))
            );';
    }
    return executeSQL($sql);
}

// }}}
// request_flowLastPieceNo() {{{

/**
 * request_usedProductKindIds
 *
 * Retourne les ids des ProductKind utilises: soit en tant que Property (de Product), 
 * soit par une Saisonality
 *
 * @param integer $supplierId
 * @return object resultset
 */
function request_usedProductKindIds() {
    $sql = '(select distinct(SPK._ToProductKind) as pkId from saiProductKind SPK) 
            union
            (select distinct(PPV._IntValue) as pkId from PropertyValue PPV, Property PPT where 
            PPV._Property=PPT._Id and PPT._Name="ProductKind");';
    return executeSQL($sql);
}

// }}}
// request_prestation_findACO() {{{

/**
 *
 * Retourne les ids des ACO a facturer
 *
 * @param mixed $opeIds array of integer
 * @param string $begin borne inf pour ACO.Firstask.Begin
 * @param string $end borne sup pour ACO.Firstask.Begin
 * @param integer $actorId id d'actor
 * @return object resultset
 */
function request_prestation_findACO($opeIds, $begin, $end, $actorId) {
    $sql = 'SELECT ACO._Id AS acoId FROM ActivatedChainOperation ACO, ActivatedChainTask ACK, 
        CommandItem CMI,Command C 
        WHERE ACO._FirstTask = ACK._Id AND ACO._ActivatedChain = CMI._ActivatedChain 
        AND CMI._Command = C._Id  
        AND ACO._Operation IN (' . implode(',', $opeIds) . ') 
        AND ACO._PrestationFactured != ' . ActivatedChainOperation::FACTURED_FULL . ' 
        AND (ACK._Begin>="' . $begin . '") AND (ACK._Begin <="' . $end . '") 
        AND 
        (
          ((C._Destinator = ' . $actorId . ' OR C._Expeditor = ' . $actorId . ') AND C._ClassName != "CHAINCOMMAND") 
          OR
          (C._Customer = ' . $actorId . ' AND C._ClassName = "CHAINCOMMAND")
        )';
    return executeSQL($sql);
}

// }}}

// request_prestation_findOL() {{{

/**
 *
 * Retourne les ids des OccupiedLocation a facturer
 * OL.InviuoiceItem = 0 et OL.CreationDate > OL.ValidityDate
 *
 * @param integer $customerId Le customer selectionne, a facturer
 * @param mixed $storeIds array of integer
 * @param mixed $productOwnerIds array of integer
 * @param mixed $productIds array of integer
 * @param string $begin borne inf pour ACO.Firstask.Begin
 * @param string $end borne sup pour ACO.Firstask.Begin
 * @return object resultset
 */
function request_prestation_findOLIds($customerId, $storeIds, $productOwnerIds, 
        $productIds, $begin, $end) {
    $whereAddon = '';
    
    if (!empty($storeIds)) {
        $whereAddon .= 'AND LOC._Store IN (' . implode(',', $storeIds) . ') ';
    }else {
        $whereAddon .= 'AND S._StockOwner = ' . $customerId . ' ';
    }
    if(empty($productOwnerIds)) {
        $whereAddon .= 'AND P._Owner = ' . $customerId . ' ';
    } elseif(empty($productIds)) {
        $whereAddon .= 'AND P._Owner IN (' 
                . implode(',', $productOwnerIds) . ') ';
    } else {
        $whereAddon .= 'AND OL._Product IN (' . implode(',', $productIds) . ') ';
    }
    $begin = substr($begin, 0, 10);
    $end = substr($end, 0, 10);

    $sql = 'SELECT OL._Id AS olId 
        FROM OccupiedLocation OL, Location LOC, Store S, Product P 
        WHERE OL._Location = LOC._Id AND LOC._Store = S._Id AND OL._Product = P._Id ' 
        . $whereAddon . ' 
        AND OL._CreationDate>="' . $begin . '" AND OL._CreationDate <="' . $end . '" 
        AND OL._InvoiceItem = 0 AND OL._CreationDate >= OL._ValidityDate;';
    return executeSQL($sql);
}

// }}}
// request_prestation_stockage() {{{

/**
 *
 * Retourne les infos necessaire pour facturer des OccupiedLocation 
 *
 * @param mixed $olIds array of integer of OccupiedLocations
 * @param string $groupBy vaut 'storeId' ou 'pdtId'
 * @return object resultset
 */
function request_prestation_stockage($olIds, $groupBy) {
    if ($groupBy == 'storeId') {
        $select = 'COUNT(OL._Id) as olNumber, ';
        $groupBy = 'storeId, pdtTypeId';
    }else {
        $select = 'SUM(OL._Quantity) as qty,  P._Id as pdtId, ';
        $groupBy = 'pdtId, storeId';
    }
    
    $sql = 'SELECT ' . $select . 'LOC._Store as storeId, P._ProductType as pdtTypeId 
        FROM OccupiedLocation OL, Location LOC, Product P 
        WHERE OL._Location = LOC._Id AND OL._Product = P._Id 
        AND OL._Id IN (' . implode(',', $olIds) . ')  
        GROUP BY ' . $groupBy . ';';
    return executeSQL($sql);
}

// }}}
// request_prestation_findOL() {{{

/**
 *
 * Permet d'optimiser un peu les perfs pour facturer des OccupiedLocation 
 *
 * @param integer $prestationId
 * @param integer $productId
 * @return object resultset
 */
function request_getProductPrestationCost($prestationId, $productId) {
    $sql = 'SELECT PC._ID as pcId 
        FROM PrestationCost PC, ppcProduct PPCP  
        WHERE PC._Id = PPCP._FromProductPrestationCost 
        AND PC._ClassName = "ProductPrestationCost" AND 
        PPCP._ToProduct = ' . $productId . ' AND PC._Prestation = ' . $prestationId . ';';
    return executeSQL($sql);
}

// }}}

// request_getLastDayOLs() {{{

/**
 *
 * Utilise par scripts/RepairOccupiedLocation.php
 *
 * @param integer $prestationId
 * @param integer $productId
 * @return object resultset
 */
function request_getLastDayOLs($day) {
    $sql = 'SELECT _Location as locId, _Product as pdtId, _Quantity as qty 
        FROM OccupiedLocation 
        WHERE _CreationDate="' . $day . '" ORDER BY _Location, _Product;';
    return executeSQL($sql);
}

// }}}
// request_tvaIsDeletable() {{{

/**
 * Retourne true si la TVA peut-être supprimée et lève une exception dans le 
 * cas contraire.
 *
 * @param int $tvaID l'id de la tva
 * @access public
 * @return boolean
 * @throws Exception si la TVA ne peut être supprimée
 */
function request_TVAIsDeletable($tvaID)
{
    $tables = array(
        'InvoiceItem'      => array('_Id', '%Invoice.DocumentNo%', _('an item of invoice "%s"')),
        'Account'          => array('_Name', null, _('the account "%s"')),
        'CommandItem'      => array('_Id', '%Command.CommandNo%', _('an item of command "%s"')),
        'FlowItem'         => array('_Id', null, _('an expenses and receipts item (FlowItem id: %s)')),
        'FlowTypeItem'     => array('_Id', null, _('an item of a model of expenses and receipts (FlowTypeItem id: %s)')),
        'Prestation'       => array('_Name', null, _('the service "%s"')),
        'Product'          => array('_Name', null, _('the product "%s"')),
        'AbstractDocument' => array('_DocumentNo', null, _('the document "%s"'))
    );
    foreach ($tables as $tbname => $info) {
        $rs = executeSQL(
            "SELECT $info[0] FROM $tbname WHERE _TVA=$tvaID LIMIT 0,1"
        );
        if ($rs && !$rs->EOF) {
            if (!is_null($info[1])) {
                $obj = Object::load($tbname, 
                    array(substr($info[0], 1)=>$rs->fields["$info[0]"]));
                $value = Tools::getValueFromMacro($obj, $info[1]);
            } else {
                $value = $rs->fields[$info[0]];
            }
            throw new Exception(
                _('Selected VAT cannot be deleted because it is used by ')
                . sprintf($info[2], $value)
            );
        }
    }
	return true;
}

// }}}
?>
