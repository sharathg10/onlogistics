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

function GetTotalCaByActorAndDates($actorId, $dateStart, $dateEnd, $commandType, $currencyId, $WithRealCategory=false) {
    $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
	$totalSales = 0;
	$outpuStartDate = I18N::formatDate($dateStart, I18N::DATE_LONG);
	$outputEndDate = I18N::formatDate($dateEnd, I18N::DATE_LONG);
															 
	$CommandMapper = Mapper::singleton('Command');
	if (in_array($commandType, $takeSupplier)) {
		$FilterComponentArray[] = SearchTools::NewFilterComponent('Supplier', 'SupplierCustomer.Supplier', 'Equals', $actorId, 1);
	} else {
	    $FilterComponentArray[] = SearchTools::NewFilterComponent('Expeditor', '', 'Equals', $actorId, 1);
	}
    
    $FilterComponentArray[] = SearchTools::NewFilterComponent('CommandDate', '', 'LowerThanOrEquals', $dateEnd, 1);
	$FilterComponentArray[] = SearchTools::NewFilterComponent('CommandDate', '', 'GreaterThanOrEquals', $dateStart, 1);
	$FilterComponentArray[] = SearchTools::NewFilterComponent('Type', '', 'Equals', $commandType, 1);
    $FilterComponentArray[] = SearchTools::NewFilterComponent('Currency', '', 'Equals', $currencyId, 1);
    
    if ($WithRealCategory) {
	    $FilterComponentArray[] = SearchTools::NewFilterComponent('Category', 'Destinator.Category', 'GreaterThan', 0, 1);
	}
	$Filter = SearchTools::FilterAssembler($FilterComponentArray);
	
	$CommandCollection = $CommandMapper->loadCollection($Filter, array(), array('TotalPriceHT'));
	if (!Tools::isEmptyObject($CommandCollection)) {
	    for($i = 0; $i < $CommandCollection->getCount(); $i++) {
	    	$Command = $CommandCollection->getItem($i);
			$totalSales += $Command->getTotalPriceHT();
	    }
	}
    $currency = Object::load('Currency', $currencyId);
	$ca_str =  "<span style=\"font-size: 10pt;\">"._('Total turnover excl. VAT')." - " . _('From') . $outpuStartDate 
				. " "._('at')." " . $outputEndDate . ": <b>" . I18N::formatNumber($totalSales) 
				. " ".$currency->getName()." </b></span>";
	return array($totalSales, $ca_str);
}

// }}}
// function GetCategoryWithCommands() {{{

/**
 * Retourne le tableau des Id de Category pour lesquelles il existe des Command
 * dont le Destinator a une de ces Category
 * @access public
 * @param integer $actorId : Command.Expeditor
 * @param integer $dateStart : limite inf pour CommandDate
 * @param integer $dateEnd : limite sup pour CommandDate
 * @param integer $commandType : le type de commande
 * @param int $currencyId devise de la commande
 * @return array of integer  
 */
function GetCategoryWithCommands($actorId, $dateStart, $dateEnd, $commandType, $currencyId) {
	require_once('SQLRequest.php');
	$CategoryIdArray = Request_CategoryWithCommands($actorId, $dateStart, $dateEnd, $commandType, $currencyId);
	return $CategoryIdArray;
}

// }}}
// function GetCommercialsWithCommands() {{{

/**
 * Retourne le tableau des Id de Commerciaux (UserAccount) pour lesquels il existe des Command
 * (Command.Commercial)
 * @access public
 * @param integer $actorId : Command.Expeditor
 * @param integer $dateStart : limite inf pour CommandDate
 * @param integer $dateEnd : limite sup pour CommandDate
 * @param integer $commandType : le type de commande
 * @param int $currencyId devise de la commande
 * @return array of integer  
 */
function GetCommercialsWithCommands($actorId, $dateStart, $DateEnd, $commandType, $currencyId) {
	require_once('SQLRequest.php');
	$reqResponseArray = Request_CommercialsWithCommand($actorId, $dateStart, $DateEnd, $commandType, $currencyId);
	return $reqResponseArray;
}

// }}}
// function GetSuppliersWithCommands() {{{

/**
 * Retourne le tableau des Id de Supplier pour lesquels il existe des Command
 * dont les Product ont ete commandes
 * @access public
 * @param integer $actorId : Command.Expeditor
 * @param integer $dateStart : limite inf pour CommandDate
 * @param integer $dateEnd : limite sup pour CommandDate
 * @param integer $commandType : le type de commande
 * @param int $currency devise de la commande
 * @return array of integer  
 */
function GetSuppliersWithCommands($actorId, $dateStart, $dateEnd, $commandType, $currency) {
	require_once('SQLRequest.php');
	$reqResponseArray = Request_SuppliersWithCommand($actorId, $dateStart, $dateEnd, $commandType, $currency);
	return $reqResponseArray;
}

// }}}
// function GetCustomersWithCommands() {{{

/**
 * Retourne le tableau des Id de Customer pour lesquels il existe des Command
 * @access public
 * @param integer $actorId : Command.Expeditor
 * @param integer $dateStart : limite inf pour CommandDate
 * @param integer $dateEnd : limite sup pour CommandDate
 * @param integer $commandType : le type de commande
 * @param int $currencyId devise de la commande
 * @return array of integer  
 */
function GetCustomersWithCommands($actorId, $dateStart, $dateEnd, $commandType, $currencyId) {
	require_once('SQLRequest.php');
	$reqResponseArray = Request_CustomersWithCommand($actorId, $dateStart, $dateEnd, $commandType, $currencyId);
	return $reqResponseArray;
}

// }}}
// function GetProductsWithCommands() {{{

/**
 * Retourne le tableau des Id de Product pour lesquels il existe des Command
 * @access public
 * @param integer $actorId : Command.Expeditor
 * @param integer $dateStart : limite inf pour CommandDate
 * @param integer $dateEnd : limite sup pour CommandDate
 * @param integer $commandType : le type de commande
 * @param int $currency devise de la commande
 * @return array of integer  
 */
function GetProductsWithCommands($actorId, $dateStart, $dateEnd, $commandType, $currency) {
	require_once('SQLRequest.php');
	$reqResponseArray = Request_ProductWithCommand($actorId, $dateStart, $dateEnd, $commandType, $currency);
	return $reqResponseArray;
}

// }}}
// function GetSuppliersWithOrders() {{{

/**
 * Retourne le tableau des Id de Supplier pour lesquels il existe des Command
 * dont ils sont l'Expeditor
 * @access public
 * @param integer $actorId : Command.Destinator
 * @param integer $dateStart : limite inf pour CommandDate
 * @param integer $dateEnd : limite sup pour CommandDate
 * @param integer $commandType : le type de commande
 * @param int $currency devise de la commande
 * @return array of integer  
 */
function GetSuppliersWithOrders($actorId, $dateStart, $dateEnd, $commandType, $currency) {
	require_once('SQLRequest.php');
	$reqResponseArray = Request_GetSuppliersWithOrders($actorId, $dateStart, $dateEnd, $commandType, $currency);
	return $reqResponseArray;
}

// }}}
// function GetCoast() {{{

/**
 * Retourne le montant total des depenses des Command
 * dont ils sont l'Expeditor
 * @access public
 * @param integer $actorId : Command.Destinator
 * @param integer $dateStart : limite inf pour CommandDate
 * @param integer $dateEnd : limite sup pour CommandDate
 * @param integer $commandType : le type de commande
 * @param int $currency devise de la commande
 * @return array of integer  
 */
function GetCoast($actorId, $dateStart, $dateEnd, $commandType, $currency) {
    $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
	$totalCoast = 0;
	$outpuStartDate = I18N::formatDate($dateStart, I18N::DATE_LONG);
	$outputEndDate = I18N::formatDate($dateEnd, I18N::DATE_LONG);
	
	$CommandMapper = Mapper::singleton('Command');
	if (in_array($commandType, $takeSupplier)) {
		$FilterComponentArray[] = SearchTools::NewFilterComponent('Supplier', 'SupplierCustomer.Supplier', 'Equals', $actorId, 1);
	} else {
	    $FilterComponentArray[] = SearchTools::NewFilterComponent('Destinator', '', 'Equals', $actorId, 1);
	}
	$FilterComponentArray[] = SearchTools::NewFilterComponent('ExpeditorClassName', 'Expeditor.ClassName', 
												 'In', array('Supplier', 'AeroSupplier'), 1);
	$FilterComponentArray[] = SearchTools::NewFilterComponent('CommandDate', '', 'LowerThanOrEquals', $dateEnd, 1);
	$FilterComponentArray[] = SearchTools::NewFilterComponent('CommandDate', '', 'GreaterThanOrEquals', $dateStart, 1);
	$FilterComponentArray[] = SearchTools::NewFilterComponent('Type', '', 'Equals', $commandType, 1);
	$FilterComponentArray[] = SearchTools::NewFilterComponent('Currency', '', 'Equals', $currency, 1);
	$Filter = SearchTools::FilterAssembler($FilterComponentArray);
	
	$CommandCollection = $CommandMapper->loadCollection($Filter, array(), array('TotalPriceHT'));
	if (!Tools::isEmptyObject($CommandCollection)) {
	    for($i = 0; $i < $CommandCollection->getCount(); $i++) {
	    	$Command = $CommandCollection->getItem($i);
			$totalCoast += $Command->getTotalPriceHT();
	    }
	}
	
	$coast_str =  "<span style=\"font-size: 10pt;\">"._('Total expenses') 
    . " - " . _('From') . ' '  
	. $outpuStartDate . " " . _('at') . " " . $outputEndDate . ": <b>" 
	. I18N::formatNumber($totalCoast) . _('Euros') . " </b></span>";
	return array($totalCoast, $coast_str);
}

// }}}
?>
