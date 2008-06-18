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

class GridColumnProductBuyUnitTypeQuantities extends AbstractGridColumn {
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnProductBuyUnitTypeQuantities($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['actor'])) {
            $this->actor = $params['actor'];
        }
    } 

    var $actor = false;

    /**
     * Permet d'afficher la quantite et le type d'uv d'achat du produit
     * 
     * @access public
     */
    function render($object) {
        $str = '';
        if (false == $this->actor) {
            $this->actor = $object->getMainSupplier();
        }
        if ($this->actor instanceof Actor) {
            $apMapper = Mapper::singleton('ActorProduct');
            $ap = $apMapper->load(
                array('Product'=>$object->getId(), 'Actor'=>$this->actor->getId())
            );
            if ($ap instanceof ActorProduct) {
                $buyUnitType = $ap->getBuyUnitType();
                if ($buyUnitType instanceof SellUnitType) {
                    $buyUnitName = $buyUnitType->getShortName();
                    $buyUnitQuantity = $ap->getBuyUnitQuantity();
                    $str = "&nbsp;" . $buyUnitQuantity . "&nbsp;" . $buyUnitName;
                }
            }
        }
        return $str;
    } 
} 

?>
