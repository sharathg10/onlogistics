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

class Account extends _Account {
    // Constructeur {{{

    /**
     * Account::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    /**
     * Retourne un tableau contenant les types
     * de factures à ventiller pour ce compte.
     * A comparer avec Invoice.CommandType
     * @access public
     * @return array
     */
    public function getInvoicesTypes() {
        $filter = array();
        $filter[] = SearchTools::NewFilterComponent('InvoiceType', 'InvoiceType', 'NotEquals', 0,1);
        $filter = SearchTools::FilterAssembler($filter);
        $flowTypeCol = $this->getFlowTypeCollection($filter);
        $count = $flowTypeCol->getCount();
        $invoiceTypes = array();
        for($i=0 ; $i<$count ; $i++) {
            $flowType = $flowTypeCol->getItem($i);
            $invoiceTypes[] = $flowType->getInvoiceType();
        }
        return $invoiceTypes;
    }
    
    /**
     * Retourne un tableau avec le detail de ce qu'il 
     * faut ventiller pour un type de facture donné.
     * @param int $invoiceType
     * @access public
     * @return array
     */
    public function getBreakdownParts($invoiceType) {
        $filter=array();
        $filter[] = SearchTools::NewFilterComponent('BreakdownPart', 'BreakdownPart', 'NotEquals', 0,1);
        $filter[] = SearchTools::NewFilterComponent('InvoiceType', 'FlowType.InvoiceType', 'Equals', $invoiceType, 1);
        $filter = SearchTools::FilterAssembler($filter);
        $flowTypeItemCol = $this->getFlowTypeItemCollection($filter);
        $count = $flowTypeItemCol->getCount();
        $breakdownParts = array();
        for($i=0 ; $i<$count ; $i++) {
            $flowTypeItem = $flowTypeItemCol->getItem($i);
            $breakdownParts[] = $flowTypeItem->getBreakdownPart();
        }
        return $breakdownParts;
        
    }
}

?>