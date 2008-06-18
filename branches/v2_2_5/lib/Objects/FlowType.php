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

class FlowType extends _FlowType {
    // Constructeur {{{

    /**
     * FlowType::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    // getCommandType() {{{

    public function getCommandType() {
        $type = $this->getInvoiceType();
        if($type==self::INVOICE_SUPPLIER_PRODUCT) {
            return Command::TYPE_SUPPLIER;
        } elseif($type==self::INVOICE_CUSTOMER_PRODUCT) {
            return Command::TYPE_CUSTOMER;
        } elseif($type==self::INVOICE_SUPPLIER_PRESTATION || $type==self::INVOICE_CUSTOMER_PRESTATION) {
            return Command::TYPE_PRESTATION;
        } elseif($type==self::INVOICE_SUPPLIER_TRANSPORT || $type==self::INVOICE_CUSTOMER_TRANSPORT) {
            return Command::TYPE_TRANSPORT;
        } elseif($type==self::INVOICE_SUPPLIER_COURSE || $type==self::INVOICE_CUSTOMER_COURSE) {
            return Command::TYPE_COURSE;
        }
        return 0;
    }

    // }}} 
    // FlowType::getCashBalance() {{{

    /**
     * getCashBalance 
     * 
     * Retourne la trésorerie de se flowType en récupèrant ces Flow et Invoice 
     * ainsi que les ForecastFlow associè sur la période.
     *
     * paramètres:
     * - beginDate: mysqldate
     * - endDate: mysqldate
     * - currency: Currency id
     *
     * @param array $params 
     * @access public
     * @return void
     */
    public function getCashBalance($params=array(), $totals=array()) {
        $return = array();
        $return[0] = $this->getName();
        $return[1] = 0;
        $return[2] = 0;
        $return[3] = array();
        $return[4] = 'FlowType_' . $this->getId();
        
        $flowTypeItems = $this->getFlowTypeItemCollection(array(), array('Name'=>SORT_ASC));
        foreach($flowTypeItems as $flowTypeItem) {
            list($result, $totals) = $flowTypeItem->getCashBalance($params, $totals);
            $return[1] += $result[1];
            $return[2] += $result[2];
            $return[3][] = $result;
        }
        return array($return, $totals);
    }

    // }}}

}

?>