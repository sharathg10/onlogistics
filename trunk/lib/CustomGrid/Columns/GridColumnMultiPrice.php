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

class GridColumnMultiPrice extends AbstractGridColumn {
    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['Method'])) {
            $this->_method = $params['Method'];
        }
        if (isset($params['Currency'])) {
            $this->_currency = $params['Currency'];
        }
    }
    /**
     * Determine la methode à utiliser: getTotalPriceHT, getTotalPriceTTC ou
     * encore getToPay
     *
     * @var string $_method
     **/
    var $_method = 'getTotalPriceHT';

    /**
     * GridColumnCommandPrice::render()
     *
     * @param Object $object une commande ou une facture
     * @return string
     **/
    public function render($object) {
        $cur = 0;
        if ($object instanceof ToHave) {
            $cur = $object->getCurrency();
        } else if ($object instanceof Payment) {
            // on récupère via le premier item de la collection de
            // invoicepayment
            $invPaymentCol = $object->getInvoicePaymentCollection();
            if (count($invPaymentCol)) {
                $invPayment = $invPaymentCol->getItem(0);
                $inv = $invPayment->getInvoice();
                $cur = $inv->getCurrency();
            }
        } else if ($object instanceof InvoicePayment || $object instanceof InvoiceItem) {
            $inv = $object->getInvoice();
            $cur = $inv->getCurrency();
        } else if ($object instanceof CommandItem) {
            $cmd = $object->getCommand();
            $cur = $cmd->getCurrency();
        } else if ($object instanceof Command || $object instanceof Invoice) {
            $cur = $object->getCurrency();
        } else {
            // cas non géré
            return _('N/A');
        }
        $curStr = $cur instanceof Currency ? 
            $cur->getSymbol() : $this->_currency->getSymbol();
        $method = $this->_method;
        return I18N::formatCurrency($curStr, $object->$method());
    }
}

?>
