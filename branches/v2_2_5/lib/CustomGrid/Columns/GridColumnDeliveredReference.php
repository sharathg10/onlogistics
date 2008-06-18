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

class GridColumnDeliveredReference extends AbstractGridColumn {
    /**
     * Permet d'afficher la reference de produit livrée ,
     * 
     * @access private 
     */
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnDeliveredReference($title = '', $params = array()) {
        parent::__construct($title, $params);
    } 

    function Render($object) {
        $ProductRefList = "";
        if ($object->getState() != ActivatedMovement::CREE) {
            $EXMovement = $object->GetExecutedmovement();
            if (!Tools::isEmptyObject($EXMovement)) {
                $ProductRefArray = array(); // references a afficher comme livrees
                $LocationExecutedMovtCollection = $EXMovement->GetDeliveredLocationExecutedMovtCollection();
                if (!Tools::isEmptyObject($LocationExecutedMovtCollection)) {
                    for($i = 0; $i < $LocationExecutedMovtCollection->getCount(); $i++) {
                        $LEM = $LocationExecutedMovtCollection->getItem($i);
                        $ProductBaseReference = Tools::getValueFromMacro($LEM, "%Product.BaseReference%");

                        if ($ProductBaseReference != Tools::getValueFromMacro($object, "%ProductCommandItem.Product.BaseReference%")) {
                            $ProductBaseReference = "<a href=\"javascript:void(0);\" title=\"" . Tools::getValueFromMacro($LEM, "%Product.Name%") . "\">" . $ProductBaseReference . "</a>";
                        } 

                        if (!in_array($ProductBaseReference, $ProductRefArray)) {
                            $ProductRefArray[] = $ProductBaseReference;
                        } 
                        unset($LEM);
                    } 
                    $ProductRefList = implode("<br />", $ProductRefArray);
                } 
            } 
        } 
        return $ProductRefList;
    } 
} 

?>