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

class GridColumnProductCommandPromotion extends AbstractGridColumn {
    /**
     * Object Customer
     *
     * @access protected
     */
    private $_Customer;

    /**
     * Vaut 1 si pour la facturation, et 0 pour le passage de commande
     *
     * @access protected
     */
    private $_forInvoice = 0;

    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['customer'])) {
            $this->_Customer = $params['customer'];
        }
        if (isset($params['forInvoice'])) {
            $this->_forInvoice = $params['forInvoice'];
        }
    }

    /*
	 * $object est un Product  si on est ds ProductCommand.php ou
     * ProductCommandSupplier.php ou bien est une ProductCommandItem si dans
     * InvoiceAddEdit.php
	 */
    public function render($object) {
        $Customer = $this->_Customer;
        if ($this->_forInvoice == 1) {
            $Product = $object->getProduct();
            $Promotion = $object->getPromotion();
        } else {
            $Product = $object;
            $Promotion = $Product->getPromotion($Customer);
        }
        /*  Selon si on est ds ProductCommand ou InvoiceAddEdit  */
        $CommandDate = ($this->_forInvoice == 1)?
            Tools::getValueFromMacro($object, '%Command.CommandDate%'):'';
        $unitHTForCustomerPrice = $Product->getUnitHTForCustomerPrice(
            $this->_Customer, $CommandDate);

        $cur = $Customer->getCurrency();
        $curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';
        if (Tools::isEmptyObject($Promotion)) { // Pas de promo
            $result = I18N::formatNumber(0) . ' ' . $curStr .
                '<input type="hidden" name="PromotionId[]" value="0" />';
        } else {
            $result = $Promotion->getDisplayedRate($curStr);
            $result .= '<input type="hidden" name="PromotionId[]" value="' .
                $Promotion->getId() . '" />';
        }

        return $result;
    }
}

?>