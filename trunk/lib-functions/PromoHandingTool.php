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

require_once('Objects/Promotion.php');

class PromoHandingTool {
	
	function parseCommandItemHanding($theCihStr) {
		// CommandItem._Handing
		// can be:
		// 0 => 0
		// 8.31% => percent
		// 1/7 => fraction (converted to %)
		// 10E 10EUROS 10Whatever => valeurs
		// on retourne une array ("type" => "OPTYPE", "value=" => VALUE)
		// avec optype = none ou percent ou numval
		$theCihStr = trim($theCihStr);
		if (!$theCihStr)
			return array("type" => "none", "value" => 0);
		else if(strpos($theCihStr, '%')) 
			return array("type" => "percent", "value" => floatval($theCihStr));
		else if(strpos($theCihStr, '/')) {
			list ($top, $bottom) = explode("/", $theCihStr);
			$percent = $top / $bottom * 100;
			$percentStr = sprintf("%.2F", $percent);
			return array("type" => "percent", "value" => floatval($percentStr));
		} else { // remise en valeur absolue
			return array("type" => "amount", "value" => floatval($theCihStr));
		}
	}
	
	function parseCommandItemPromotion($PromotionId) {
		$PromotionId = trim($PromotionId);
		if(!$PromotionId)
			return array("type" => "none", "value" => 0);
		$Promotion = Object::load("Promotion", $PromotionId);
		if($Promotion->getType() == Promotion::PROMO_TYPE_MONTANT) {
			$type = "percent";
		} else if ($Promotion->getType() == Promotion::PROMO_TYPE_PERCENT) {
			$type = "amount";
		}
		return array("type" => $type, "value" => $Promotion->getRate());
	}

	function parseCommandHanding($theChStr) {
		// toujours sous la forme 100.00 et c'est uniquement des %'
		$theChfl = floatval(trim($theChStr));
		if(!$theChfl)
			return array("type" => "none", "value=" => 0);
		return array("type" => "percent", "value" => $theChfl);
	}
	
	function parseCommandCustomerRemExcep($theCcrehStr) {
		$theCcrehfl = floatval(trim($theCcrehStr));
		if(!$theCcrehfl)
			return array("type" => "none", "value" => 0);
		// toujours sous la forme 100.00 et c'est uniquement des %'
		return array("type" => "percent", "value" => $theCcrehfl);
	}
}

?>
