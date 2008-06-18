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

class GridColumnProductPriceWithDiscount extends AbstractGridColumn {
    /**
     * Destine a contenir la remise propre au client, pour le calcul du prix qui doit en tenir compte
     * 
     * @access private 
     */
    var $_DiscountCus;
    var $_DiscountCat;
    var $tab_price = array();
    /**
     * Constructor
     * 
     * @access protected 
     */

    function GridColumnProductPriceWithDiscount($title = '', $params = array()) {
        parent::__construct($title, $params);

        $this->_DiscountCus = 0;

        if (isset($params['DiscountCus'])) {
            $this->_DiscountCus = $params['DiscountCus'];
        } 
        if (isset($params['DiscountCat'])) {
            $this->_DiscountCat = $params['DiscountCat'];
        } 
    } 

    function Render($object) {
        $price = Tools::getValueFromMacro($object, '%Price%'); // recuperation du prix
        $productId = Tools::getValueFromMacro($object, '%Id%');

        $session = Session::Singleton();
        $session->register('tab_price', 3); // tableau associatif contenant idProduct => Prix_Calcule 
        $promotion = $object->getPromotion(); // on récupère la promotion du produit
        /*	if ($_POST['monSelect'] == 0) {
		    return 'N/A';
			exit;
		}*/ 
        // si une promo sur le product existe et est valide
        if ($promotion instanceof Promotion && ($isvalid = $promotion->isValid())) {
            $promoPrice = Tools::getValueFromMacro($object, '%Promotion.NewPrice%');
            $_SESSION['tab_price'][$productId] = $promoPrice;
            return sprintf("%s &euro;", I18N::formatNumber($promoPrice));
        } else {
            if ($price > 0 && $this->_DiscountCus >= 0) {
                $pricewithdiscount = $price * (1 - (($this->_DiscountCus + $this->_DiscountCat) / 100));
                $_SESSION['tab_price'][$productId] = $pricewithdiscount;
                return sprintf("%s &euro;", I18N::formatNumber($pricewithdiscount));
            } 
        } 
    } 
} 

?>

