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

class Nomenclature extends _Nomenclature {
    // Constructeur {{{

    /**
     * Nomenclature::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Nomenclature::isUsed() {{{
    /**
     * Retourne true ou false selon s'il existe des ConcreteProduct relies
     * a cette Nomenclature
     * @access public
     * @return boolean
     */
    function isUsed() {
        $mapper = Mapper::singleton('ConcreteProduct');
        $ConcreteProduct = $mapper->load(
                array('Component.Nomenclature' => $this->getId()));
        return (Tools::isEmptyObject($ConcreteProduct) == false);
    }

    // }}}
    // Nomenclature::levelOneCanBeLot() {{{

    /**
     * Retourne true ssi:
     * - Le Product associe (level 0) a un tracingMode > 0
     * oubien
     * - elle ne possede pas de Component C de level 2
     *
     * @access public
     * @return boolean
     */
    function levelOneCanBeLot() {
        if (Tools::getValueFromMacro($this, '%Product.TracingMode%') > 0) {
            return true;
        }
        $coll = $this->getComponentCollection(array('Level' => 2), array(), array('Id'));
        return (Tools::isEmptyObject($coll));
    }

    // }}}
    // Nomenclature::levelTwoCanExist() {{{

    /**
     * Retourne false ssi:
     * - Le Product associe (level 0) a un tracingMode = 0
     * et
     * - elle possede un Component C de level 1 tel que tracingMode = LOT
     *
     * @access public
     * @return boolean
     */
    function levelTwoCanExist() {
        if (Tools::getValueFromMacro($this, '%Product.TracingMode%') > 0) {
            return true;
        }
        $coll = $this->getComponentCollection(
                array('Level' => 1, 'Product.TracingMode' => Product::TRACINGMODE_LOT),
                array(), array('Id'));
        return (Tools::isEmptyObject($coll));
    }

    // }}}
    // Nomenclature::getTreeItems() {{{

    /**
     * Retourne un tableau representant la structure en arbre
     * $headId=0 => Nomenclature modele
     * $headId>0 => Nomenclature pieces
     *
     * @param integer $headId id du CP racine de la nomenclature pieces
     * @access public
     * @return array of strings
     */
    function getTreeItems($headId=0) {
        $cmpMapper = Mapper::singleton('Component');
        $Component = $cmpMapper->load(
                array('Nomenclature' => $this->getId(), 'Level' => 0));
        $return = ($headId == 0)?$Component->getTreeItems(!$this->isUsed()):
                $Component->getPieceTreeItems($headId);

        return $return;
    }

    // }}}
    // Nomenclature::getGroupTreeItems() {{{

    /**
     * Retourne un tableau representant la structure en arbre
     * $headId=0 => Nomenclature modele
     * $headId>0 => Nomenclature pieces
     *
     * @param integer $headId id du CP racine de la nomenclature pieces
     * @access public
     * @return array of strings
     */
    function getGroupTreeItems($headId=0) {
        $cmpMapper = Mapper::singleton('Component');
        $Component = $cmpMapper->load(
                array('Nomenclature' => $this->getId(), 'Level' => 0));

        $return = ($headId == 0)?$Component->getTreeItems(!$this->isUsed(), false):
                $Component->getPieceTreeItems($headId, false);

        $ComponentGroupColl = $this->getComponentGroupCollection(
                array(), array('Name' => SORT_ASC));
        if (!Tools::isEmptyObject($ComponentGroupColl)) {
            $count = $ComponentGroupColl->getCount();
            for ($i = 0;$i < $count; $i++) {
                $cmpGroup = $ComponentGroupColl->getItem($i);
                $return[] = $cmpGroup->getTreeItems(!$this->isUsed(), $headId);
            }
        }
        return $return;
    }

    // }}}
    // Nomenclature::buildChain() {{{

    /**
     * Construit la base de la chaine associée à la nomenclature en appelant 
     * buildChainOperation depuis le composant head de la nomenclature.
     * A appeler dans une transaction.
     *
     * @access public
     * @return int id de la chaîne crée
     */
    public function buildChain() {
        $component = Object::load('Component', array(
            'Nomenclature' => $this->getId(),
            'Level'        => 0
        ));
        // cherche une ref dispo pour la nouvelle chaîne.
        $product = $this->getProduct();
        $chainRef = $product->getBaseReference();
        $index=1;
        $exist = true;
        while($exist) {
            $chainCol = Object::loadCollection('Chain', array(
                'Reference' => $chainRef
            ));
            if($chainCol->getCount() > 0) {
                $index++;
                $chainRef = $product->getBaseReference() . '-' . $index;
            } else {
                $exist = false;
            }
        }
        $chain = new Chain();
        $chain->generateId();
        $chain->setReference($chainRef);
        $chain->setDescription($chainRef);
        //$chain->setOwner(); XXX
        $chain->setType(Chain::CHAIN_TYPE_PRODUCT);
        $chain->setState(Chain::CHAIN_STATE_CREATED);

        // ajoute les ChainOperation à la chaine
        $component->buildChainOperation($chain);

        return $chain;
    }

    // }}}

}

?>
