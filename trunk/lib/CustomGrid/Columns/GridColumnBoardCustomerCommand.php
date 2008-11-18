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

class GridColumnBoardCustomerCommand extends AbstractGridColumn {
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnBoardCustomerCommand($title = '', $params = array()) {
        parent::__construct($title, $params);
        if(isset($params['totalCa'])) {
			$this->totalCa = $params['totalCa'];
		}
        $this->req = $params['req'];
        $this->date_start = $params['start'];
 		$this->date_end = $params['end'];
 		$this->commandType = $params['commandType'];
        $this->currency = $params['currency'];
        $this->season = $params['season'];
        $this->factor = $params['factor'];
    } 

    function Render($object) {
        $takeSupplier = array(Command::TYPE_TRANSPORT, Command::TYPE_COURSE, Command::TYPE_PRESTATION);
    	$Auth = Auth::Singleton();
    	$commandMapper = Mapper::singleton('Command');
        $FilterComponentArray = array();
        if(in_array($this->commandType, $takeSupplier)) {
            $FilterComponentArray[] = SearchTools::NewFilterComponent('Supplier', 'SupplierCustomer.Supplier', 'Equals', $Auth->getActorId(), 1);
        } else {
            $FilterComponentArray[] = SearchTools::NewFilterComponent('Expeditor', "", 'Equals', $Auth->getActorId(), 1);
        }
        if (!$this->commandType) {
            // devis
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('IsEstimate', "", 'Equals', 1, 1);
        } else if ($this->req == 'estimate_num' || $this->req == 'ca_par_cli_estimates') {
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('IsEstimate', "", 'Equals', 1, 1);
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('Type', '', 'Equals', $this->commandType, 1);
        } else {
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('IsEstimate', "", 'Equals', 0, 1);
		    $FilterComponentArray[] = SearchTools::NewFilterComponent('Type', '', 'Equals', $this->commandType, 1);
        }
		$FilterComponentArray[] = SearchTools::NewFilterComponent('Destinator', "", 'Equals', $object->getId(), 1);
		$FilterComponentArray[] = SearchTools::NewFilterComponent('CommandDate', '', 'GreaterThanOrEquals', $this->date_start, 1);
		$FilterComponentArray[] = SearchTools::NewFilterComponent('CommandDate', '', 'LowerThanOrEquals', $this->date_end, 1);
		$FilterComponentArray[] = SearchTools::NewFilterComponent('Currency', '', 'Equals', $this->currency, 1);
        if ($this->season !== false) {
            $FilterComponentArray[] = SearchTools::NewFilterComponent('Season',
                'CommandItem()@ProductCommandItem.Product@RTWProduct.Model.Season.Id',
                'Equals',
                $this->season,
                1,
                'Command');
        }
        if ($this->req == 'billed_amount') {
            $FilterComponentArray[] = SearchTools::NewFilterComponent('State', '', 'In',
                array(Command::FACT_PARTIELLE, Command::FACT_COMPLETE,
                      Command::REGLEMT_PARTIEL, Command::REGLEMT_TOTAL), 1);
        }
        $filter = SearchTools::FilterAssembler($FilterComponentArray);
        $commandCollection = $commandMapper->loadCollection($filter, array(), array('TotalPriceHT', 'TotalPriceTTC'));
        
		if($this->req == 'command_num' || $this->req == 'estimate_num') {
        	return $commandCollection->getCount();
		} else {
        	$count = $commandCollection->getCount();
        	$htForThisCustomer = 0;
        	$ttcForThisCustomer = 0;
        	for ($i=0; $i<$count; $i++) {
        	 	$item = $commandCollection->getItem($i);
        	 	$htForThisCustomer += $item->getTotalPriceHT();
        	 	$ttcForThisCustomer += $item->getTotalPriceTTC();
        	}
			if($this->req == 'ca_par_cli' || $this->req == 'ca_par_cli_estimates') {
        		return I18N::formatNumber($htForThisCustomer);
		    } else if ($this->req == 'billed_amount') {
        		return I18N::formatNumber($ttcForThisCustomer);
        	} else if($this->req == 'ca_percent' && isset($this->totalCa)) {
        		if($this->totalCa == 0) { // NO DIIV BY ZERO
        			return _('N/A');
        		}
        		$percent = $htForThisCustomer / $this->totalCa * 100 ;
        		return I18N::formatNumber($percent) . "%";
        	}
        }
    } 
}
?>
