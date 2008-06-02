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

function getElementFromSAQCollection($saqCollection, $storeName, $propertyValue, $firstIndex=0) {
    $count = $saqCollection->getCount();
    for($i = $firstIndex; $i < $count; $i++) {
        $saq = $saqCollection->getItem($i);
        if ($saq->getStoreName() == $storeName && $saq->getPropertyValue() == $propertyValue) {
            return array($saq, $i);
        }
        // ToDo: Controler que strcasecmp() est OK pour tous les types de PropertyValue!!
        if (($saq->getStoreName() == $storeName
            && strcasecmp($saq->getPropertyValue(), $propertyValue) > 0)
        || strcasecmp($saq->getStoreName(), $storeName) > 0) {
            return array(false, $i);
        }
    }
    return array(false, -1);
}

/**
 * Les types d'unites sur lesquelles faire les calculs
 * @return mixed array
 **/
function getUnitTypeArray() {
    return array(1 => _('Selling unit'),
                 2 => _('Kilogramme'),
                 3 => _('Cube meter'),
                 4 => _('Liter'));
}
/**
 * Les types d'unites sur lesquelles faire les calculs, en contracte
 * @return mixed array
 **/
function getShortUnitTypeArray() {
    return array(1 => _('Selling unit'),
                 2 => _('Kg'),
                 3 => _('M3'),
                 4 => _('L'));
}

?>
