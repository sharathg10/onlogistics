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

class ActorProduct extends _ActorProduct {
    // Constructeur {{{

    /**
     * ActorProduct::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ActorProduct::getPriceByActor() {{{

    /**
     * Retourne le prix de l'UV dans la devise de l'acteur passé en paramètre
     *
     * @access public
     * @param $actor le client
     * @return float le prix dans la devise définie pour le client
     **/
    public function getPriceByActor($actor=false){
        $actor = $this->getActor();
        // on essaie de récupérer le prix associé à la devise
        $mapper = Mapper::singleton('PriceByCurrency');
        $currencyID = $actor->getCurrencyId();
        $pbc = $mapper->load(array('ActorProduct'=>$this->getId(),
            'Currency'=>$currencyID));
        if (!($pbc instanceof PriceByCurrency)) {
            return 0;
        }
        return $pbc->getPrice();
    }

    // }}}
    // ActorProduct::getUBPrice() {{{

    /**
     * Product::GetUBPrice()
     * Retourne le prix de l'unité de base du couple acteur/produit
     *
     * @param Object $actor: si renseigné le prix est dans la devise de celui-ci
     * @return float
     **/
    public function getUBPrice() {
        // on ne connait pas l'acteur donc on prend l'acteur du couple
        $product = $this->getProduct();
        $numberUBInUV = $product->GetNumberUBInUV();
        if ($numberUBInUV != 0) {
            $price = $this->GetPriceByActor();
            return round($price/$numberUBInUV, 2);
        }
        return 0;
    }

    // }}}
    // ActorProduct::getPriceByCurrencyForInventory() {{{

    /**
     * retourne le pricebycurrency correspondant à la devise du propriétaire
     * du stock, ou si non trouvé le 1er pricebycurrency défini dans une devise
     * ou bien false en dernier recours.
     *
     * @access public
     * @param object $stockOwner le propriétaire du stock de l'inventaire
     * @return mixed un objet PriceByCurrency ou false sinon
     **/
    public function getPriceByCurrencyForInventory($stockOwner){
        if (!($stockOwner instanceof Actor)) {
            return false;
        }
        $currency = $stockOwner->getCurrency();
        $curID = $currency instanceof Currency?$currency->getId():0;
        // on essaie de trouver le pricebycurrency correspondant à la devise du
        // propriétaire du stock
        $pbcMapper = Mapper::singleton('PriceByCurrency');
        $filter = array('ActorProduct'=>$this->getId(), 'Currency'=>$curID);
        $pbc = $pbcMapper->load($filter);
        // s'il n'existe pas on prend le premier défini dans une autre devise...
        if (!($pbc instanceof PriceByCurrency)) {
            // on charge la collection de PriceByCurrency
            $filter = new FilterComponent(
                new FilterRule(
                    'ActorProduct',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getId()
                ),
                new FilterRule(
                    'Price',
                    FilterRule::OPERATOR_GREATER_THAN,
                    0
                )
            );
            $filter->operator = FilterComponent::OPERATOR_AND;
            $pbcCol = $pbcMapper->loadCollection($filter);
            if (!Tools::isEmptyObject($pbcCol)) {
                $pbc = $pbcCol->getItem(0);
            } else {
                $pbc = false;
            }
        }
        return $pbc;
    }

    // }}}
    // ActorProduct::getCSVDataSQL() {{{

    /**
     * Retourne une requête sql pour la methode OnlogisticsXmlRpcServer::getCSVData()
     *
     * @access public
     * @return string
     */
    public function getCSVDataSQL() {
        $ret  = 'SELECT apd._Id, CONCAT(pdt._BaseReference, "-", act._Name) ';
        $ret .= 'FROM ActorProduct apd, Actor act, Product pdt ';
        $ret .= 'WHERE apd._Actor=act._Id AND apd._Product=pdt._Id';
        return $ret;
    }

    // }}}
    // ActorProduct::canBeDeleted() {{{

    /**
     * ActorProduct::canBeDeleted()
     * Retourne true si l'objet peut être détruit en base de donnees.
     * Concerne les references client:
     * Il ne faut pas qu'une commande client ait deja
     * ete passee pour ActorProduct.Product
     *
     * @access public
     * @return boolean
     */
    public function canBeDeleted() {
        $test = parent::canBeDeleted();
        $actor = $this->getActor();
        if (parent::canBeDeleted()
        && !(($actor instanceof Customer) || ($actor instanceof AeroCustomer))) {
            return true;
        }
        // C'est bien une occurrence pour stocker une ref client
        // Au vu du path, pas possible d'utiliser $mapper->alreadyExists()
        $mapper = Mapper::singleton('ProductCommandItem');
        $testColl = $mapper->loadCollection(
                array(
                    'Command.Destinator' => $this->getActorId(),
                    'Product '=> $this->getProductId()));

        if ($testColl->getCount() > 0) {
            throw new Exception('A customer command already exists with this customer and this product.');
        }
        return true;
    }

    // }}}
    // ActorProduct::getToStringAttribute() {{{

    /**
     * Retourne le nom des attributs utilisÃ©s par la mÃ©thode toString()

     * @access public
     * @return array
     */
    function getToStringAttribute() {
        return array('Actor', 'AssociatedProductReference');
    }

    // }}}
    // ActorProduct::toString() {{{

    /**
     * Retourne la representation textuelle de l'ActorProduct

     * @access public
     * @return string
     */
    function toString() {
        $ret  = $this->getAssociatedProductReference();
        if (($actor = $this->getActor()) instanceof Actor) {
            $ret .= ' / ' . $actor->getName();
        }
        return $ret;
    }

    // }}}
}

?>
