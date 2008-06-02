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

class Promotion extends _Promotion {
    // Constructeur {{{

    /**
     * Promotion::__construct()
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
     * Le montant ou taux de la promotion a afficher (soit avec % soit €)
     * @access public
     * @return void 
     **/
    function GetDisplayedRate($curStr = false){
        if (!$curStr) {
            $cur = $this->getCurrency();
            $curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';
        }
        if (Promotion::PROMO_TYPE_MONTANT == $this->getType()) {
             return I18N::formatCurrency($curStr, $this->getRate()); 
        }
        return I18N::formatPercent($this->getRate());
    }
    
    /**
     * Retourne la collection des Category d'acteurs affectees a la Promotion
     * Attention: Si la methode GetCategoryCollection() retourne une collection 
     * vide, TOUTES les categories sont concernees!!!
     * D'ou le besoin de cette nouvelle methode
     * @access public
     * @return void 
     **/
    function GetCategoryCollectionForPromotion() {
        $CategoryCollection = $this->GetCategoryCollection();
        
        if (!Tools::isEmptyObject($CategoryCollection)) {  
            return $CategoryCollection;
        }
        $CategoryMapper = Mapper::singleton("Category");
        $CategoryCollection = $CategoryMapper->loadCollection();
        return $CategoryCollection;
    }

    /**
     * Retourne le tableau des id des Category d'acteurs affectees a la Promo
     * Attention: Si la methode GetCategoryCollection() retourne une collection 
     * vide, TOUTES les categories sont concernees!!!
     * D'ou le besoin de cette nouvelle methode
     * @access public
     * @return void 
     **/
    function GetCategoryCollectionIdsForPromotion() {
        $CategoryCollectionIds = $this->GetCategoryCollectionIds();
        
        if (!empty($CategoryCollectionIds)) {  
            return $CategoryCollectionIds;
        }
        $CategoryMapper = Mapper::singleton("Category");
        $CategoryCollection = $CategoryMapper->loadCollection();
        $CategoryCollectionIds = array();
        if ($CategoryCollection instanceof Collection) {
            for ($i=0;$i<$CategoryCollection->GetCount();$i++) {
                $Category = $CategoryCollection->GetItem($i);
                $CategoryCollectionIds[] = $Category->GetId();
                unset($Category);
            }   
        }
        return $CategoryCollectionIds;
    }    
    
    /**
     * Retourne la collection des Product affectes a la Promotion
     * Attention: Si la methode GetProductCollection() retourne une 
     * collection vide, TOUS les Product sont concernes!!!
     * D'ou le besoin de cette nouvelle methode
     * @access public
     * @return void 
     **/
    function GetProductCollectionForPromotion() {
        $ProductCollection = $this->GetProductCollection();
        
        if (!Tools::isEmptyObject($ProductCollection)) {  
            return $ProductCollection;
        }
        $ProductMapper = Mapper::singleton("Product");
        $ProductCollection = $ProductMapper->loadCollection();
        return $ProductCollection;
    }

    /**
     * Retourne le tableau des id des Product affectes a la Promotion
     * Attention: Si la methode GetProductCollection() retourne une 
     * collection vide, TOUS les Product sont concernes!!!
     * D'ou le besoin de cette nouvelle methode
     * @access public
     * @return void 
     **/
    function GetProductCollectionIdsForPromotion() {
        $ProductCollectionIds = $this->GetProductCollectionIds();
        
        if (!empty($ProductCollectionIds)) {  
            return $ProductCollectionIds;
        }
        $ProductMapper = Mapper::singleton("Product");
        $ProductCollection = $ProductMapper->loadCollection();
        $ProductCollectionIds = array();
        if ($ProductCollection instanceof Collection) {
            for ($i=0;$i<$ProductCollection->GetCount();$i++) {
                $Product = $ProductCollection->GetItem($i);
                $ProductCollectionIds[] = $Product->GetId();
                unset($Product);
            }
        }
        return $ProductCollectionIds;
    }
    
    /**
     * Determine si une promotion est affectee a un Product passe en parametre
     * Methode ajoutee pour l'optimisation des Approvisionnements, 
     * dans le but d'user le minimum de ressources
     * @param $ProductId integer: Id de Product
     * @return boolean Content of value
     * @access public
     */
    function isPromotionForProduct($ProductId) {
        $ProductIdArray = $this->GetProductCollectionIds();
        return (empty($ProductIdArray) || in_array($ProductId, $ProductIdArray));
    }

}

?>