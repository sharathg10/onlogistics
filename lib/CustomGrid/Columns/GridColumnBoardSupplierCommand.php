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

require_once('PromoHandingTool.php');
require_once('SQLRequest.php');

class GridColumnBoardSupplierCommand extends AbstractGridColumn {

    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnBoardSupplierCommand($title = '', $params = array()) {
        parent::__construct($title, $params);
        $this->req = $params['req'];
        $this->date_start = $params['start'];
 		$this->date_end = $params['end'];
 		$this->commandType = $params['commandType'];
 		$this->currency = $params['currency'];
    } 

    function Render($object) {
    	$totalPriceHT = 0.00;
    	$Auth = Auth::Singleton();
    	$actId = $Auth->getActorId(); 
    	$supplierId = $object->getId();
        $result = Request_ActorCommandsBySupplier($actId, $supplierId, 
            $this->date_start, $this->date_end, $this->commandType, 
            $this->currency);
    	$doCalculate = false;
    	if ($this->req == 'ca_par_sup')
    		$doCalculate = true;
    	$numrows = 0;
    	if ($result) {
    		$numrows = $result->RecordCount();
    		if ($doCalculate) {
    			while(!$result->EOF){
    				$PriceHT = 0.00;
	            	$Quantity = $result->fields['_Quantity'];
	            	$PriceHT = $result->fields['_PriceHT'];
	            	$Promotion = $result->fields['_Promotion'];
	            	$aciHanding = $result->fields['_aciHanding'];
	            	$cHanding = $result->fields['_cHanding'];
	            	$CustomerRemExcep = $result->fields['_CustomerRemExcep'];
	            	$PromotionParsedArr = PromoHandingTool::parseCommandItemPromotion($Promotion);
	            	$aciHandingParsedArr = PromoHandingTool::parseCommandItemHanding($aciHanding);
	            	$cHandingParsedArr = PromoHandingTool::parseCommandHanding($cHanding);
	            	$CustomerRemExcepParsedArr = PromoHandingTool::parseCommandCustomerRemExcep($CustomerRemExcep);
	            	// PT.L.HTPT.L.HT = [[(Prix Unitaire HT * Promo) * Remise client] * Remise Ligne] * remise globale * qté UV 
	            	// let's go
	            	// 1st => Promotion
	            	if ($PromotionParsedArr['type'] == 'percent') {
	            		$PriceHT -= $PriceHT / 100 * $PromotionParsedArr['value'];
	            	} else if ($PromotionParsedArr['type'] == 'amount') {
	            		$PriceHT -= $PromotionParsedArr['value'];
	            	}
	            	// 2nd => Remise client
	            	if ($CustomerRemExcepParsedArr['type'] == 'percent') {
	            		$PriceHT -= $PriceHT / 100 * $CustomerRemExcepParsedArr['value'];
	            	}
	            	// 3rd => Remise Ligne
	            	if ($aciHandingParsedArr['type'] == 'percent') {
	            		$PriceHT -= $PriceHT / 100 * $aciHandingParsedArr['value'];
	            	} else if ($aciHandingParsedArr['type'] == 'amount') {
	            		$PriceHT -= $aciHandingParsedArr['value'];
	            	} 
	            	// 4th => Remise global
	            	if ($cHandingParsedArr['type'] == 'percent') {
	            		$PriceHT -= $PriceHT / 100 * $cHandingParsedArr['value'];
	            	}
	            	// 5 => qté
	            	$PriceHT *= $Quantity;
	            	$totalPriceHT += $PriceHT;
	            	$result->MoveNext();
            	}
            	
    		}
    	}
    	if($this->req == 'command_num')
    		return $numrows;
    	else if ($this->req == 'ca_par_sup') {
    		return I18N::formatNumber($totalPriceHT);
    		// $totalPriceHTStr = sprintf("%.2f", $totalPriceHT);
    		// return $totalPriceHTStr;
    		
    	}
    } 
}
?>
