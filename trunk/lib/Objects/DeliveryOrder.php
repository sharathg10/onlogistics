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

class DeliveryOrder extends _DeliveryOrder {
    // Constructeur {{{

    /**
     * DeliveryOrder::__construct()
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
     * Retourne un tableau PN, SN/LOT, Qté pour chaque produit (PN) présent
     * dans la facture.
     *
     * @access public
     * @return array
     */
     function getSNLotArray() {
        $tempArray = $return = array();
        // Collection de LEMCP non annulateurs
        $lemccpCol = $this->getLEMConcreteProductCollection();
        $cnt = $lemccpCol->getCount();
        for ($i=0; $i<$cnt; $i++) {
            $lemccp = $lemccpCol->getItem($i);
            if (!($lemccp instanceof LEMConcreteProduct)) {
                continue;
            }
            $ccp = $lemccp->getConcreteProduct();
            if (!($ccp instanceof ConcreteProduct)) {
                continue;
            }
            $pdt = $ccp->getProduct();
            if (!($pdt instanceof Product)) {
                continue;
            }
            $qty = $lemccp->getQuantity() - $lemccp->getCancelledQuantity($this->getEditionDate());
            if ($qty == 0) {
                continue;
            }
            if (!isset($tempArray[$pdt->getBaseReference()])) {
                $tempArray[$pdt->getBaseReference()] = array();
            }
            $tempArray[$pdt->getBaseReference()][] = array(
                    $pdt->getBaseReference(), $ccp->getSerialNumber(), $qty);
        }
        ksort($tempArray);  // tri par BaseReference

        foreach($tempArray as $key => $value) {
            foreach($value as $val) {
                $return[] = $val;
            }
        }
        return $return;
    }

    /**
     * Retourne une collection de LEMConcreteProduct pour l'item de facture,
     * si le produit a un mode de suivi SN ou lot.
     *
     * @access public
     * @return object Collection
     **/
    function getLEMConcreteProductCollection() {
        $lemcpColl = new Collection();
        $mapper = Mapper::singleton('LEMConcreteProduct');
        $cmd = $this->getCommand();
        $cmiCol = $cmd->getCommandItemCollection();
        $count = $cmiCol->getCount();
        for ($i=0; $i<$count; $i++) {
            $cmi = $cmiCol->getItem($i);
            $acm = $cmi->getActivatedMovement();
            if ($acm instanceof ActivatedMovement){
                $exm = $acm->getExecutedMovement();
                if ($exm instanceof ExecutedMovement) {
                    // Les LEMCP non annulateurs (param true)
                    $lemCol = $exm->getLocationExecutedMovementForBL(
                            $this->getEditionDate());
                    // Les lemCP associes ne sont pas annulateurs, du coup
                    $col = $mapper->loadCollection(
                            array('LocationExecutedMovement' => $lemCol->getItemIds()));
                    $lemcpColl = $lemcpColl->merge($col);
                }
            }
        }
        return $lemcpColl;
    }

}

?>