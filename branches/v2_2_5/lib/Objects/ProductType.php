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

class ProductType extends _ProductType {
    // Constructeur {{{

    /**
     * ProductType::__construct()
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
     * Methode raccourci qui renvoie un tableau
     * nom prop=>obj prop
     * 
     * @access public
     * @param boolean $withGeneric si à false ne renvoi pas les propriétés
     *           de l'objet générique lié
     * @return void 
     **/
    function getPropertyArray($withGeneric = true){
        $tempArray = array();
        if ($withGeneric) {
            $tempArray = $this->getGenericPropertyArray();
        }
        $collection = $this->getPropertyCollection();
        if ($collection instanceof Collection) {
            for($i = 0; $i < $collection->getCount(); $i++){
                $prop = $collection->getItem($i);
                if ($prop instanceof Property) {
                    $tempArray[$prop->getName()] = $prop;
                }
                unset($prop);
            } // for
        }
        return $tempArray;
    }
    
    /**
     *
     * @access public
     * @return void 
     **/
    function getGenericPropertyArray(){
        $genProductType = $this->getGenericProductType();
        if ($genProductType instanceof ProductType) {
            return $genProductType->getPropertyArray();
        }
        return array();        
    }
    
    /**
     * Ajoute une propriété dynamique au type de produit
     * 
     * @access public
     * @param Property $property l'objet property à ajouter
     * @return void 
     **/
    function addProperty($property){
        $collection = $this->getPropertyCollection();
        $collection->acceptDuplicate = false;
        $collection->setItem($property);
    }
    
    /**
     * Methode addon pour faciliter la suppression d'une propriété
     * 
     * @access public
     * @param integer $propertyId: l'id de l'objet
     * @return boolean 
     **/
    function removeProperty($propertyId){
        if (false == $propertyId) {
            return false;
        }
        // on charge la collection et le tableau d'ids
        $collection = $this->getPropertyCollection();
        foreach($collection->getItemIds() as $key=>$id){
            if ($propertyId == $id) {
                $collection->removeItem($key);
                break;
            }
        }
        return true;
    }
}

?>