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

class GridColumnDeliveredQuantity extends AbstractGridColumn {
    /**
     * Permet d'afficher la quantite de produit livree
     * 
     * @access private 
     */
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnDeliveredQuantity($title = '', $params = array()) {
        parent::__construct($title, $params);
    } 

    function Render($object) {
        $Quantity = _('N/A');
        if ($object->getState() != ActivatedMovement::CREE) {
            $EXMovement = $object->GetExecutedmovement();
            if (!Tools::isEmptyObject($EXMovement)) {
                $LocationExecutedMovtCollection = $EXMovement->GetDeliveredLocationExecutedMovtCollection();
                if (!Tools::isEmptyObject($LocationExecutedMovtCollection)) {
                    $ProductIdArray = array(); // references a afficher comme livrees
                    for($i = 0; $i < $LocationExecutedMovtCollection->getCount(); $i++) {
                        $LEM = $LocationExecutedMovtCollection->getItem($i);
                        $ProductId = $LEM->GetProductId();
                        if (!array_key_exists ($ProductId, $ProductIdArray)) {
                            $ProductIdArray[$ProductId] = $LEM->GetQuantity();
                        } else {
                            $ProductIdArray[$ProductId] += $LEM->GetQuantity();
                        } 
                        unset($LEM);
                    } 
                    $Quantity = implode("<br />", $ProductIdArray);
                } 
            } 
        } else {
            $Quantity = 0;
        } 
        return $Quantity;
    } 
} 

?>