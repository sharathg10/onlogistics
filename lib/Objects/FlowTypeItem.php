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

class FlowTypeItem extends _FlowTypeItem {
    // Constructeur {{{

    /**
     * FlowTypeItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    // toString() {{{

    /**
     * utilisé pour créer la liste des charges/recettes dans
     * AccountAddEdit.php
     * @return string.
     */
    public function toString() {
        $flowType = $this->getFlowType();
        return $flowType->getName() . ' - ' . $this->getName();
    }

    // }}}
    // getToStringAttribute() {{{

    /**
     * Attributs du toString
     *
     * @static
     * @return array
     */
    public function getToStringAttribute() {
        return array('Name', 'FlowType');
    }

    // }}}
    // getCashBalance() {{{

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
        require_once('FormatNumber.php');
        $beginDate = $params['beginDate'];
        $endDate = $params['endDate'];
        $currency = (isset($params['currency']) && $params['currency']) ? 
            $params['currency'] : false;
        $accountingType = (isset($params['accountingType']) && $params['accountingType']) ? 
            $params['accountingType'] : false;
        $total = 0;
        $forecast = 0;

        $flowType = $this->getFlowType();
        $coeff = 1;
        if($flowType->getType() == FlowType::CHARGE) {
            $coeff = -1;
        }
        if($flowType->getInvoiceType() > 0) {
            // on aura besoin des TVA de fraix annexes on les charges ici pour 
            // ne le faire qu'une fois
            require_once('Objects/TVA.inc.php');
            $TVARates = array();
            $TVACategories = array(
                'INSURANCE' => TVA::TYPE_INSURANCE, 
                'DELIVERY EXPENSES' => TVA::TYPE_DELIVERY_EXPENSES, 
                'PACKING' => TVA::TYPE_PACKING);
            $tvaMapper = Mapper::singleton('TVA');
            foreach ($TVACategories as $category=>$value) {
                $tva = $tvaMapper->load(array('Type' => $value));
                if(!($tva instanceof TVA)) {
                    $TVARates[$category] = 0;
                    continue;
                }
                $TVARates[$category] = $tva->getRate();
            }
        }
        
        // construction du filtre pour les FlowItems
        $filter = array(
            SearchTools::NewFilterComponent('Flow.PaymentDate', '', 
                'GreaterThanOrEquals', $beginDate, 1),
            SearchTools::NewFilterComponent('Flow.PaymentDate', '', 
                'LowerThanOrEquals', $endDate, 1),
            SearchTools::NewFilterComponent('Type', '',
                'Equals', $this->getId(), 1)
        );
        if($currency) {
            $filter[] = SearchTools::NewFilterComponent('Flow.Currency', '',
                'Equals', $currency, 1);
        }
        
        // FlowItems
        $flowItems = Object::loadCollection('FlowItem', SearchTools::FilterAssembler($filter));

        foreach($flowItems as $flowItem) {
            $ttc = troncature($flowItem->getTotalTTC());
            $total += $ttc;
            $totals['total'] += $coeff * $ttc;
        }
              
        // Invoices
        if($flowType->getInvoiceType() > 0) {
            $filter = array(
                SearchTools::NewFilterComponent('PaymentDate', '', 
                    'GreaterThanOrEquals', $beginDate, 1),
                SearchTools::NewFilterComponent('PaymentDate', '', 
                    'LowerThanOrEquals', $endDate, 1),
                SearchTools::NewFilterComponent('CommandType', '',
                    'Equals', $flowType->getInvoiceType(), 1)
            );
            if($currency) {
                $filter[] = SearchTools::NewFilterComponent('Currency', '',
                    'Equals', $currency, 1);
            }
            $invoices = Object::loadCollection('Invoice', SearchTools::FilterAssembler($filter));
            $breakdownPart = $this->getBreakdownPart();
            foreach($invoices as $inv) {
                $ttc = 0;
                if($breakdownPart == self::BREAKDOWN_INSURANCE) {
                    $ttc = troncature($inv->getInsurance() + troncature($inv->getInsurance() * 
                        $TVARates['INSURANCE'] / 100));
                } elseif($breakdownPart == self::BREAKDOWN_PACKING) {
                    $ttc = troncature($inv->getPacking() + troncature($inv->getPacking() * 
                        $TVARates['PACKING'] / 100));
                } elseif($breakdownPart == self::BREAKDOWN_PORT) {
                    $ttc = troncature($inv->getPort() + troncature($inv->getPort() * 
                        $TVARates['DELIVERY EXPENSES'] / 100));
                } elseif($breakdownPart == self::BREAKDOWN_INVOICE_ITEM) {
                    $invoiceItems = $inv->getInvoiceItemCollection();
                    foreach($invoiceItems as $invoiceItem) {
                        $ttc += troncature($invoiceItem->getTotalPriceHT() + troncature($invoiceItem->getTotalTVA()));
                    }
                }
                $total += $ttc;
                $totals['total'] += $coeff * $ttc;
            }
            // commandes non facturés: on ne détaille pas et on ajoute à la 
            // ligne des lignes de facture
            if($breakdownPart == self::BREAKDOWN_INVOICE_ITEM) {
                require_once('SQLRequest.php');
                
                $result = request_commandForCashBalance(
                    $flowType->getCommandType(),
                    $currency);
                //$beginDate;
                //$endDate; 
                while($result && !$result->EOF) {
                    $cmdTTC = $result->fields['cmdTotalTTC'];
                    $topay  = troncature($cmdTTC - $result->fields['cmdPayed']);
                    $topId  = $result->fields['scTermsOfPayment'];
                    if ($topId > 0 && ($top = Object::load('TermsOfPayment', $topId)) instanceof TermsOfPayment) {
                        $order = Object::load('Command', $result->fields['cmdId']);
                        $topItems = $top->getTermsOfPaymentItemCollection();
                        foreach($topItems as $topItem) {
                            list($date, $amount, $supplier) = $topItem->getDateAndAmountForOrder($order, $topay);
                            if ($date >= $beginDate && $date <= $endDate) {
                                $total += $amount;
                                $totals['total'] += $coeff * $amount;
                            }
                        }
                    }
                    $result->moveNext();
                }
            }
        }

        // prévisionnel correspondant
        $d1 = explode(' ', $beginDate);
        $d2 = explode(' ', $endDate);
        $filter = array(
            SearchTools::NewFilterComponent('BeginDate', '', 
                'GreaterThanOrEquals', $d1[0], 1),
            SearchTools::NewFilterComponent('EndDate', '', 
                'LowerThanOrEquals', $d2[0], 1),
            SearchTools::NewFilterComponent('ForecastFlow', 'FlowTypeItem().Id', 
                'Equals', $this->getId(), 1, 'ForecastFlow'),
            SearchTools::NewFilterComponent('Active', '', 'Equals', 1, 1));
        if($currency) {
            $filter[] = SearchTools::NewFilterComponent('Currency', '',
                'Equals', $currency, 1);
        }
        if($accountingType) {
            $filter[] = SearchTools::NewFilterComponent('AccountingType', 
                'FlowTypeItem().FlowType.AccountingType', 'Equals', $accountingType, 1, 
                'ForecastFlow');
        }
        $forecastFlow = Object::load('ForecastFlow', SearchTools::FilterAssembler($filter));
        if($forecastFlow instanceof ForecastFlow) {
            $forecast = $forecastFlow->getAmount();
            if($flowType->getType() == FlowType::CHARGE) {
                $totals['forecast'] -= $forecast;
            } else {
                $totals['forecast'] += $forecast;
            }
        }

        return array(
            array(
                0 => $flowType->getName() . ' - ' . $this->getName(), 
                1 => $total, 
                2 => $forecast, 
                4 => 'FlowTypeItem_' . $this->getId()
            ), 
            $totals);
    }

    // }}}
}

?>
