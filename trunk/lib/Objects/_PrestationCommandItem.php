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

class _PrestationCommandItem extends CommandItem {
    
    // Constructeur {{{

    /**
     * _PrestationCommandItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Prestation foreignkey property + getter/setter {{{

    /**
     * Prestation foreignkey
     *
     * @access private
     * @var mixed object Prestation or integer
     */
    private $_Prestation = false;

    /**
     * _PrestationCommandItem::getPrestation
     *
     * @access public
     * @return object Prestation
     */
    public function getPrestation() {
        if (is_int($this->_Prestation) && $this->_Prestation > 0) {
            $mapper = Mapper::singleton('Prestation');
            $this->_Prestation = $mapper->load(
                array('Id'=>$this->_Prestation));
        }
        return $this->_Prestation;
    }

    /**
     * _PrestationCommandItem::getPrestationId
     *
     * @access public
     * @return integer
     */
    public function getPrestationId() {
        if ($this->_Prestation instanceof Prestation) {
            return $this->_Prestation->getId();
        }
        return (int)$this->_Prestation;
    }

    /**
     * _PrestationCommandItem::setPrestation
     *
     * @access public
     * @param object Prestation $value
     * @return void
     */
    public function setPrestation($value) {
        if (is_numeric($value)) {
            $this->_Prestation = (int)$value;
        } else {
            $this->_Prestation = $value;
        }
    }

    // }}}
    // UnitPriceHT float property + getter/setter {{{

    /**
     * UnitPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_UnitPriceHT = 0;

    /**
     * _PrestationCommandItem::getUnitPriceHT
     *
     * @access public
     * @return float
     */
    public function getUnitPriceHT() {
        return $this->_UnitPriceHT;
    }

    /**
     * _PrestationCommandItem::setUnitPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setUnitPriceHT($value) {
        if ($value !== null) {
            $this->_UnitPriceHT = round(I18N::extractNumber($value), 3);
        }
    }

    // }}}
    // QuantityForPrestationCost float property + getter/setter {{{

    /**
     * QuantityForPrestationCost float property
     *
     * @access private
     * @var float
     */
    private $_QuantityForPrestationCost = 0;

    /**
     * _PrestationCommandItem::getQuantityForPrestationCost
     *
     * @access public
     * @return float
     */
    public function getQuantityForPrestationCost() {
        return $this->_QuantityForPrestationCost;
    }

    /**
     * _PrestationCommandItem::setQuantityForPrestationCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantityForPrestationCost($value) {
        if ($value !== null) {
            $this->_QuantityForPrestationCost = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CostType int property + getter/setter {{{

    /**
     * CostType int property
     *
     * @access private
     * @var integer
     */
    private $_CostType = null;

    /**
     * _PrestationCommandItem::getCostType
     *
     * @access public
     * @return integer
     */
    public function getCostType() {
        return $this->_CostType;
    }

    /**
     * _PrestationCommandItem::setCostType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCostType($value) {
        $this->_CostType = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'CommandItem';
    }

    // }}}
    // getObjectLabel() {{{

    /**
     * Retourne le "label" de la classe.
     *
     * @static
     * @access public
     * @return string
     */
    public static function getObjectLabel() {
        return _('None');
    }

    // }}}
    // getProperties() {{{

    /**
     * Retourne le tableau des propriétés.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getProperties($ownOnly = false) {
        $return = array(
            'Prestation' => 'Prestation',
            'UnitPriceHT' => Object::TYPE_DECIMAL,
            'QuantityForPrestationCost' => Object::TYPE_DECIMAL,
            'CostType' => Object::TYPE_INT);
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
    }

    // }}}
    // getLinks() {{{

    /**
     * Retourne le tableau des entités liées.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getLinks($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
    }

    // }}}
    // getUniqueProperties() {{{

    /**
     * Retourne le tableau des propriétés qui ne peuvent prendre la même valeur
     * pour 2 occurrences.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getUniqueProperties() {
        $return = array();
        return array_merge(parent::getUniqueProperties(), $return);
    }

    // }}}
    // getEmptyForDeleteProperties() {{{

    /**
     * Retourne le tableau des propriétés doivent être "vides" (0 ou '') pour
     * qu'une occurrence puisse être supprimée en base de données.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getEmptyForDeleteProperties() {
        $return = array();
        return array_merge(parent::getEmptyForDeleteProperties(), $return);
    }

    // }}}
    // getFeatures() {{{

    /**
     * Retourne le tableau des "fonctionalités" pour l'objet en cours.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getFeatures() {
        return array();
    }

    // }}}
    // getMapping() {{{

    /**
     * Retourne le mapping nécessaires aux composants génériques.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getMapping($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
    }

    // }}}
    // useInheritance() {{{

    /**
     * Détermine si l'entité est une entité qui utilise l'héritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entités
     * dans le mapper car classes filles et parentes sont mappées dans la même
     * table.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function useInheritance() {
        return true;
    }

    // }}}
    // getParentClassName() {{{

    /**
     * Retourne le nom de la première classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'CommandItem';
    }

    // }}}
}

?>