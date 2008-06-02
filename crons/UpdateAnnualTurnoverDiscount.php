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

if (php_sapi_name() != 'cli') {
    exit(1);
}

// pour tester, decommenter ça:
//define('SKIP_CONNECTION', true);
//require_once 'config.inc.php';
//Database::connection(getDSNForRealm('lacaze'));

$scColFilter = new FilterComponent(
    new FilterRule('Customer.Category', FilterRule::OPERATOR_GREATER_THAN, 0)
);
$scCol = Object::loadCollection('SupplierCustomer', $scColFilter);

foreach ($scCol as $sc) {
    if (!($percent = $sc->getAnnualTurnoverDiscountPercent())) {
        continue;
    }
    $htCommands = array();
    $htToHaves  = array();
    $commandFilter = new FilterComponent(
        new FilterRule(
            'Type',
            FilterRule::OPERATOR_EQUALS,
            Command::TYPE_CUSTOMER
        ),
        new FilterRule(
            'SupplierCustomer',
            FilterRule::OPERATOR_EQUALS,
            $sc->getId()
        )
    );
    $commandFilter->operator = FilterComponent::OPERATOR_AND;
    $commandCol = Object::loadCollection(
        'ProductCommand',
        $commandFilter,
        array('CommandDate' => SORT_ASC)
    );
    foreach($commandCol as $command) {
        $year = substr($command->getCommandDate(), 0, 4);
        if (!isset($htCommands[$year])) {
            $htCommands[$year] = $command->getTotalPriceHT() * ($percent/100);
        } else {
            $htCommands[$year] += $command->getTotalPriceHT() * ($percent/100);
        }
        $htToHaves[$year] = 0;
    }

    $toHaveFilter = new FilterComponent(
        new FilterRule(
            'Type',
            FilterRule::OPERATOR_EQUALS,
            ToHave::TOHAVE_DISCOUNT_ANNUAL_TURNOVER
        ),
        new FilterRule(
            'SupplierCustomer',
            FilterRule::OPERATOR_EQUALS,
            $sc->getId()
        )
    );
    $toHaveFilter->operator = FilterComponent::OPERATOR_AND;
    $toHaveCol = Object::loadCollection(
        'ToHave',
        $toHaveFilter,
        array('EditionDate' => SORT_ASC)
    );
    foreach($toHaveCol as $toHave) {
        $year = substr($toHave->getEditionDate(), 0, 4);
        $htToHaves[$year] = $toHave->getTotalPriceHT();
    }
    if (count($htCommands) == 0) {
        continue;
    }
    foreach($htCommands as $year=>$commandHT) {
        $annualDiscount = Object::load(
            'AnnualTurnoverDiscount',
            array('Year' => $year, 'SupplierCustomer' => $sc->getId())
        );
        if (!($annualDiscount instanceof AnnualTurnoverDiscount)) {
            $annualDiscount = new AnnualTurnoverDiscount();
            $annualDiscount->setSupplierCustomer($sc->getId());
            $annualDiscount->setYear($year);
        }
        $annualDiscount->setAmount($commandHT - $htToHaves[$year]);
        $annualDiscount->save();
    }
}

?>
