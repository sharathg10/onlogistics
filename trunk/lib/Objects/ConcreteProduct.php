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

class ConcreteProduct extends _ConcreteProduct {
    // Constructeur {{{

    /**
     * ConcreteProduct::__construct()
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
     * Update un ou plusieurs attributs numeriques d'une quantite donnee
     * @param array of strings $attributes
     * @param integer or float or decimal $Quantity
     * @access public
     * @return void
     **/
    function updateAttributesWithQty($attributes, $Quantity) {
        if (!is_array($attributes)) {
            $attributes = array($attributes);
        }
        foreach($attributes as $name) {
            $getter = 'get' . $name;
            $setter = 'set' . $name;
            if (method_exists($this, $getter)) {
                $this->$setter($this->$getter() + $Quantity);
            }
        }
    }

    /**
     * Indique si le ConcreteProduct est le Head d'une Nomenclature
     * On pourrait passer aussi par: Component.Nomenclature
     * @access public
     * @return boolean
     */
    function isHead() {
        $Collection = $this->getConcreteProductCollection();
        return (!Tools::isEmptyObject($Collection));
    }

    /**
     * Afecte un Head a un Concreteproduct,
     * c a d  remplit la table cptHead si besoin,
     * ou y supprime un element, selon la valeur de add
     * En suppression, n'utiliser que si mode de suivi = SN !!!!
     *
     * @param mixed $idsToAddRemove (integer or array of integer) ConcreteProduct id
     * @param boolean $add true par defaut
     *
     * @access public
     * @return void
     **/
    function addRemoveChild($idsToAddRemove, $add=true) {
        $idsToAddRemove = (is_array($idsToAddRemove))?
                $idsToAddRemove:array($idsToAddRemove);
        $childrenIds = $this->getConcreteProductCollectionIds();
        if ($add) {
            foreach($idsToAddRemove as $id) {
                if (!in_array($id, $childrenIds)) {
                    $childrenIds[] = $id;
                }
            }
        }
        else {  // On doit supprimer une ligne de cptHead
            foreach($idsToAddRemove as $id) {
                $key = array_search($id, $childrenIds);
                if ($key !== false) {
                    unset($childrenIds[$key]);
                }
            }
        }
        $this->setConcreteProductCollectionIds($childrenIds);
    }

    /**
     * Indique si le ConcreteProduct est en stock ou non
     *
     * @access public
     * @return entier 1 s'il y a du stock
     *                0 sinon
     *
    function isEnStock() {
        $bResult       = 0;
        $mapper    = Mapper::singleton('LocationConcreteProduct');
        $lcpCollection = $mapper->loadCollection(array("ConcreteProduct" => this->getId()));

        if(!Tools::isEmptyObject($lcpCollection)) {
            $dQuantity = 0;
            for($i=0; $i < $lcpCollection->getCount(); $i++) {
                $dQuantity += ($lcpCollection->getItem($i))->getQuantity();
            }
            if($dQuantity > 0) {
                // si la somme des LocationConcreteProduct_Quantity est superieure a 0 alors
                // le ConcreteProduct lie est en stock.
                $bResult = 1;
            }
        }

        return $bResult;
    }

    /**
     * Indique si le ConcreteProduct est impacte par un mouvement
     *
     * @access public
     * @return entier 1 s'il est impacte
     *                0 sinon
     *
    function isInLEM() {
        $bResult       = 0;
        $mapper    = Mapper::singleton('LEMConcreteProduct');
        $lcpCollection = $mapper->loadCollection(array("ConcreteProduct" => this->getId()));

        if(!Tools::isEmptyObject($lcpCollection)) {
            if($lcpCollection->getCount() > 0) {
                // si le nombre de LEMConcreteProduct lie au ConcreteProduct est > 0
                // alors il est implique dans un mouvement.
                $bResult = 1;
            }
        }

        return $bResult;
    }*/
    // toString() {{{

    /**
     * Retourne la représentation texte de l'objet
     *
     * @access public
     * @return string
     */
    public function toString() {
        return $this->getSerialNumber();
    }

    // }}}

    // getToStringAttribute() {{{

    /**
     * Retourne le nom de l'attribut pointé par toString()
     *
     * static
     * @access public
     * @return string
     */
    public function getToStringAttribute() {
        return 'SerialNumber';
    }

    // }}}

}

?>