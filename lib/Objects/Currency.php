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

class Currency extends _Currency {
    // Constructeur {{{

    /**
     * Currency::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    // getConverterRate() {{{

    /**
     * Retourne le taux de change a une date donnee, pour une devise donnee.
     *
     * @access public
     * @param object $currency Currency instance
     * @param string $date date donnee au format YYYY-mm-dd
     * @return mixed
     */
    public static function getConverterRate($currency, $date) {
        $return = false;
        $mapper = Mapper::singleton('CurrencyConverter');
        $filter = array(
            SearchTools::NewFilterComponent(
                'BeginDate', '', 'LowerThanOrEquals', $date, 1),
            SearchTools::NewFilterComponent(
                'EndDate', '', 'GreaterThanOrEquals', $date, 1)
        );
        
        $cur1Filter = array(
            SearchTools::NewFilterComponent(
                'FromCurrency', '', 'Equals', $this->getId(), 1),
            SearchTools::NewFilterComponent(
                'ToCurrency', '', 'Equals', $currency->getId(), 1)
        );
        $cur1Filter = SearchTools::filterAssembler($cur1Filter);
        
        $cur2Filter = array(
            SearchTools::NewFilterComponent(
                'ToCurrency', '', 'Equals', $this->getId(), 1),
            SearchTools::NewFilterComponent(
                'FromCurrency', '', 'Equals', $currency->getId(), 1)
        );
        $cur2Filter = SearchTools::filterAssembler($cur2Filter);
        
        $curFilter = SearchTools::filterAssembler(
                array($cur1Filter, $cur2Filter), FilterComponent::OPERATOR_OR);
        
        $filter[] = $curFilter;
        $filter = SearchTools::filterAssembler($filter);
        $converter = $mapper->load($filter);
        if (!($converter instanceof CurrencyConverter)) {
            return false;
        }
        if ($converter->getFromCurrencyId() == $this->getId()) {
            $return = $converter->getRate();
        } else {
            $return = round(1 / $converter->getRate(), 6);
        }
        
        return $return;
    }

    // }}}
}

?>