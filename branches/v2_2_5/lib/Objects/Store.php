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

class Store extends _Store {
    // Constructeur {{{

    /**
     * Store::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Constants {{{

    const CAN_NOT_BE_DISABLED = 0;
    const DELETABLE = 1;
    const CAN_BE_DISABLED = 2;

    // }}}
    // Store::getLocationNameArray() {{{

    /**
     * Retourne un tableau contenant le nom des Location qu'il contient
     * @access public
     * @return array of strings
     **/
    public function getLocationNameArray() {
        $NameArray = array();
        $LocationCollection = $this->getLocationCollection(array(), array(), array('Name'));
        for ($i=0; $i<$LocationCollection->getCount(); $i++) {
            $Location = $LocationCollection->getItem($i);
            $NameArray[] = $Location->getName();
            unset($Location);
        }
        return $NameArray;
    }

    // }}}
    // Store::getOccupiedLocationOwners {{{

    /**
     * Retourne un tableau contenant le nom des Location qu'il contient
     *
     * @param boolean $factured false par defaut
     * @access public
     * @return object collection d'Actors
     **/
    function getOccupiedLocationOwners($factured=false) {
        $ownerColl = new Collection();
        $oclMapper = Mapper::singleton('OccupiedLocation');

        $FilterComponentArray = array();
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'Store', 'Location.Store', 'Equals', $this->getId(), 1);
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'Owner', 'Product.Owner', 'NotEquals', 0, 1, 'OccupiedLocation');
        if ($factured == false) {
            $FilterComponentArray[] = SearchTools::newFilterComponent(
                'InvoiceItem', '', 'Equals', 0, 1);
        }
        $filter = SearchTools::filterAssembler($FilterComponentArray);
        $oclCollection = $oclMapper->loadCollection($filter);

        if (Tools::isEmptyObject($oclCollection)) {
            return $ownerColl;
        }
        $count = $oclCollection->getCount();
        $ownerColl->acceptDuplicate = false;
        for ($i = 0; $i < $count; $i++) {
            $ownerColl->setItem($oclCollection->getItem($i)->getOwner());
        }
        return $ownerColl;
    }

    // }}}
    // Store::isDeletable() {{{

    /**
     * Retourne DELETABLE si le Store est supprimable, c a d si vide ou si tous ses
     * Location le sont
     * retourne CAN_BE_DISABLED si ne contient aucun Location CAN_NOT_BE_DISABLED
     * et au moins un Location CAN_BE_DISABLED
     * retourne CAN_NOT_BE_DISABLED si au moins un de ses Location est CAN_NOT_BE_DISABLED
     *
     * @access public
     * @return integer: une des 3 constantes ci-dessus
     **/
    function isDeletable() {
         $locColl = $this->getLocationCollection();
         $canBeDisabled = false;
        // si la collection est vide, on peut supprimer le store sans probleme
        if (Tools::isEmptyObject($locColl)) {
            return Store::DELETABLE;
        }
        $count = $locColl->getCount();
        for($i = 0; $i < $count; $i++){
            $loc = $locColl->getItem($i);
            if ($loc->isDeletable() == Store::CAN_NOT_BE_DISABLED) {
                return Store::CAN_NOT_BE_DISABLED;
            }
            elseif ($loc->isDeletable() == Store::CAN_BE_DISABLED) {
                $canBeDisabled = true;
            }
        }
        return $canBeDisabled?Store::CAN_BE_DISABLED:Store::DELETABLE;
    }

    // }}}
    // Store::getNameWithDetails() {{{
    /**
     *
     * @return string
     */
    public function toString() {
        $stoSite = $this->getStorageSite();
        $details = '';
        if($stoSite instanceof Site) {
            $details .= '(' . $stoSite->getName() . ' ';
            $owner = $stoSite->getOwner();
            if ($owner instanceof Actor) {
                $details .= $owner->getName() . ')';
            } else {
                $details .= ')';
            }
        }
        return $this->getName() . $details;
    }
    // }}}
    // Store::getToStringAttribute() {{{
    /**
     * @static
     * @return array
     */
    public function getToStringAttribute() {
        return array('Name', 'StorageSite');
    }
    // }}}
    // Store::setActivated() {{{

    /**
     * Surcharge du setter setActivated().
     * Desactive/Active le Store et ses locations en cascade.
     * Ajout du param $saveLoc, car lors d'un simple Object::load('Store', $id);
     * cela generait autant de req UPDATE Location... que de Locations dans le Store
     *
     * @access public
     * @param boolean $state
     * @param boolean $saveLoc true s'il faut modifier et sauver les locations liees
     * @return void
     */
    public function setActivated($state, $saveLoc=false) {
        if ($saveLoc) {
            // change l'état des emplacements
            $locCollection = $this->getLocationCollection();
            $count = $locCollection->getCount();
    	    for($i = 0; $i < $count; $i++) {
    	        $loc = $locCollection->getItem($i);
    	        $loc->setActivated($state);
    	        $loc->save();
    	    }
        }
        
        // change l'état du magasin
        parent::setActivated($state);
    }

    // }}}

}

?>