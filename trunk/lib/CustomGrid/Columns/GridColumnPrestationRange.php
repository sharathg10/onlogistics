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

class GridColumnPrestationRange extends AbstractGridColumn {
    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['beginEnd'])) {
            $this->_beginEnd = $params['beginEnd'];
        }
    }

    private $_beginEnd = ''; // vaut 'begin' ou 'end'

    function render(&$object) {
        require_once('Objects/Prestation.const.php');

        $costType = $object->getCostType();
        $arrayVolume = array (CostRange::TYPE_HOUR_VOLUME_RANGE, CostRange::TYPE_AMOUNT_VOLUME_RANGE,
            CostRange::TYPE_FIXED_VOLUME_RANGE);
        $arrayWeight = array (CostRange::TYPE_HOUR_WEIGHT_RANGE, CostRange::TYPE_AMOUNT_WEIGHT_RANGE,
            CostRange::TYPE_FIXED_WEIGHT_RANGE);
        $method = ($this->_beginEnd == 'begin')?'getBeginRange':'getEndRange';
        if(in_array($costType, $arrayVolume)) {
            return $object->$method() . ' ' . _('m3');
        } elseif(in_array($costType, $arrayWeight)) {
            return $object->$method() . ' ' . _('Kg');
        } else {
            return '';
        }
    }

}

?>