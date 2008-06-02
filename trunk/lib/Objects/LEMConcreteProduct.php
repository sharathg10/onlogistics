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

class LEMConcreteProduct extends _LEMConcreteProduct {
    // Constructeur {{{

    /**
     * LEMConcreteProduct::__construct()
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
     * Si LEMCP annule, ou reintegre, donne la qte d'UV reintegree en stock
     *
     * @param $dateSup si renseigne, limite sup dans le temps: sert a la REedition
     * de BL, pour editer le BL initial, sans les reintegrations ulterieures
     * @access public
     * @return integer
     **/
    function getCancelledQuantity($dateSup=0) {
        require_once('Objects/MovementType.const.php');

        $Quantity = 0;
        // si le mvt n'a pas subi de reintegration / annulation,
        // et un mvt annulateur ne peut etre annule
        if (0 <= $this->getCancelled()) {
            return 0;
        }

        $MovementTypeId = Tools::getValueFromMacro($this,
                '%LocationExecutedMovement.ExecutedMovement.Type.Id%');
        $MovementType = Object::load('MovementType', $MovementTypeId);
        $LEMCPMapper = Mapper::singleton('LEMConcreteProduct');

        if ($dateSup != 0) {
            $filter = new FilterComponent();
            $filter->setItem(new FilterRule(
                    'CancelledLEMConcreteProduct',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getId()));
            $filter->setItem(new FilterRule(
                    'LocationExecutedMovement.Date',
                    FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                    $dateSup));
            $filter->operator = FilterComponent::OPERATOR_AND;
        }
        else {
            $filter = array('CancelledLEMConcreteProduct' => $this->getId());
        }
        // Si mvt prevu (sortie normale), on ne peut annuler partiellement, avant BL
        // et 1 lemcp est annule par 1 et 1 seul lemcp
        // Reste a verifier si annule dans le creneau de dates couvert par le BL,
        // si $dateSup != 0
        if ($MovementType->getForeseeable() == 1) {
            $cancellerLEMCP = $LEMCPMapper->load($filter);
            $cancelledQty = (!Tools::isEmptyObject($cancellerLEMCP))?$this->getQuantity():0;
            return $cancelledQty;
        }

        // Remarque: si tracingMode=1, collection de 1 element au max
        $LEMCPCollection = $LEMCPMapper->loadCollection($filter);
        for ($i=0; $i<$LEMCPCollection->getCount(); $i++) {
            $LEMCP = $LEMCPCollection->getItem($i);
            $Quantity += $LEMCP->getQuantity();
        }

        return $Quantity;
    }

}

?>