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

class GridColumnInvoiceProductPrice extends AbstractGridColumn {
    /**
     * Permet d'afficher le prix d'achat du produit ou le prix de l'UV,
     * ds InvoiceAddEdit ET InvoiceDetail
     *
     * @access private
     *//**
     * Constructor
     *
     * @access protected
     */
    public function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
    }

    /*  object est de type ProductCommandItem ou InvoiceItem
	 *  selon si on est ds InvoiceAddEdit.php ou InvoiceDetail.php  */
    public function render($object) {
        require_once('FormatNumber.php');
        if ($object instanceof InvoiceItem) {
            $htTotalreal = $object->getTotalPriceHT();
            $Invoice = $object->getInvoice();
            $cur = $Invoice->getCurrency();
            $curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';
        }else{
            $qty = $object->getQuantity();
            $handing = $object->getHanding();
            $handingType = $object->HandingType();
            $basePrice = $object->getPriceHT();
            require_once('CalculatePriceHanding.php');
            $htTotalreal = calculatePriceHanding($handingType, $basePrice, $qty, $handing);
        }
        $htTotalreal = troncature($htTotalreal, 2);//I18N::formatNumber($htTotalreal);
        if ($object instanceof CommandItem) {
            // On est ds InvoiceAddEdit.php
            return sprintf('<input type="text" name="PTHT[]" size="7" ' .
                'class="ReadOnlyField" readonly="readonly" value="%s" />', $htTotalreal);
        } elseif ($object instanceof InvoiceItem) { // On est ds InvoiceDetail.php
            return sprintf("%s %s", I18N::formatNumber($htTotalreal), $curStr);
        } else {
            return _('N/A');
        }
    }
}

?>