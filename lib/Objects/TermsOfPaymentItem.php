<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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
 * @version   SVN: $Id: SiteAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * TermsOfPaymentItem class
 *
 * Class containing addon methods.
 */
class TermsOfPaymentItem extends _TermsOfPaymentItem {
    // Constructeur {{{

    /**
     * TermsOfPaymentItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // getDateAndAmountForOrder() {{{

    /**
     * Return the date and amount of the payment for the given order as a tuple 
     * array of two elements.
     *
     * @param Command The order
     *
     * @access public
     * @return array
     */
    public function getDateAndAmountForOrder($order)
    {
        // calculate date
        $event = $this->getPaymentEvent();
        if ($event == TermsOfPaymentItem::ORDER) {
            $date = $order->getCommandDate();
        } else {
            $date = $order->getWishedDate();
        }
        $option = $this->getPaymentOption();
        if ($option == self::END_OF_MONTH) {
            $date = DateTimeTools::lastDayInMonth($date);
        } else if ($option == self::END_OF_NEXT_MONTH) {
            $date = DateTimeTools::lastDayInMonth($date, 1);
        }
        $delay = $this->getPaymentDelay();
        if ($delay > 0) {
            $ts   = DateTimeTools::MysqlDateToTimeStamp($date);
            $date = DateTimeTools::timeStampToMySQLDate(
                $ts + ($delay * DateTimeTools::ONE_DAY)
            ); 
        }
        // calculate amount
        $amount  = $order->getTotalPriceTTC();
        $percent = $this->getPercentOfTotal();
        if ($amount > 0 && $percent > 0 && $percent != 100) {
            $amount = $amount * ($percent / 100);
        }
        // return date and amount in an array
        return array($date, $amount);
    }

    // }}}

}

?>
