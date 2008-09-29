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

class GridColumnBoardProductCommand extends AbstractGridColumn {
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnBoardProductCommand($title = '', $params = array()) {
        parent::__construct($title, $params);
        if(isset($params['totalCa'])) {
			$this->totalCa = $params['totalCa'];
		}
       
        $this->req = $params['req'];
        $this->date_start = $params['start'];
 		$this->date_end = $params['end'];
 		$this->commandType = $params['commandType'];
        $this->currency = $params['currency'];
    } 

    function Render($object) {
    	$takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
    	$Auth = Auth::Singleton();
    	$cmdItemMapper = Mapper::singleton('CommandItem');
        $FilterComponentArray = array();
        if (in_array($this->commandType, $takeSupplier)) {
        	$FilterComponentArray[] = SearchTools::NewFilterComponent('Supplier', 'Command.SupplierCustomer.Supplier', 'Equals', $Auth->getActorId(), 1);
        } else {
            $FilterComponentArray[] = SearchTools::NewFilterComponent('Expeditor', 'Command.Expeditor', 'Equals', $Auth->getActorId(), 1);
        }
        if (!$this->commandType) {
            // devis
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('IsEstimate', 'Command.IsEstimate', 'Equals', 1, 1);
        } else {
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('IsEstimate', 'Command.IsEstimate', 'Equals', 0, 1);
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('Type', 'Command.Type', 'Equals', $this->commandType, 1);
        }
		$FilterComponentArray[] = SearchTools::NewFilterComponent('Product', '', 'Equals', $object->getId(), 1);
		$FilterComponentArray[] = SearchTools::NewFilterComponent('start', 'Command.CommandDate', 'GreaterThanOrEquals', $this->date_start, 1);
		$FilterComponentArray[] = SearchTools::NewFilterComponent('end', 'Command.CommandDate', 'LowerThanOrEquals', $this->date_end, 1);
		$FilterComponentArray[] = SearchTools::NewFilterComponent('Currency', 'Command.Currency', 'Equals', $this->currency, 1);
        $filter = SearchTools::FilterAssembler($FilterComponentArray);
        $CommandItemCollection = $cmdItemMapper->loadCollection($filter, array(), 
																		   array('Quantity', 'PriceHT'));
        
        $count = $CommandItemCollection->getCount();
		if($this->req == 'command_num') {
			// quantité de produits par ligne
        	$ttQteForThisProd = 0;
        	for ($i=0; $i<$count; $i++) {
        	 	$item = $CommandItemCollection->getItem($i);
        	 	$ttQteForThisProd += $item->getQuantity();
        	}
        	return $ttQteForThisProd;
		} else if($this->req == 'ca_par_prod') {
			$ttAmountForThisProd = 0;
			for ($i=0; $i<$count; $i++) {
        	 	$item = $CommandItemCollection->getItem($i);
        	 	$amountForOneElem = $item->getPriceHT();
        	 	$qteOfElemsInThisLine = $item->getQuantity();
        	 	$ttAmountForThisProd += $amountForOneElem * $qteOfElemsInThisLine;
        	}
        	return I18N::formatNumber($ttAmountForThisProd);
        } else if($this->req == 'ca_percent' && isset($this->totalCa)) {
        	if($this->totalCa == 0) { // NO DIIV BY ZERO
        		return 'N/A';
        	}

        	$ttAmountForThisProd = 0;
			for ($i=0; $i<$count; $i++) {
        	 	$item = $CommandItemCollection->getItem($i);
        	 	$amountForOneElem = $item->getPriceHT();
        	 	$qteOfElemsInThisLine = $item->getQuantity();
        	 	$ttAmountForThisProd += $amountForOneElem * $qteOfElemsInThisLine;
        	}
        	$percent = $ttAmountForThisProd / $this->totalCa * 100 ;
        	return I18N::formatNumber($percent) . "%";
        }
    } 
}
?>
