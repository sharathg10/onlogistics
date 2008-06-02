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

class HandingByRange extends Object {
    
    // Constructeur {{{

    /**
     * HandingByRange::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Percent float property + getter/setter {{{

    /**
     * Percent float property
     *
     * @access private
     * @var float
     */
    private $_Percent = 0;

    /**
     * HandingByRange::getPercent
     *
     * @access public
     * @return float
     */
    public function getPercent() {
        return $this->_Percent;
    }

    /**
     * HandingByRange::setPercent
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPercent($value) {
        if ($value !== null) {
            $this->_Percent = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Minimum float property + getter/setter {{{

    /**
     * Minimum float property
     *
     * @access private
     * @var float
     */
    private $_Minimum = 0;

    /**
     * HandingByRange::getMinimum
     *
     * @access public
     * @return float
     */
    public function getMinimum() {
        return $this->_Minimum;
    }

    /**
     * HandingByRange::setMinimum
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMinimum($value) {
        if ($value !== null) {
            $this->_Minimum = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Maximum float property + getter/setter {{{

    /**
     * Maximum float property
     *
     * @access private
     * @var float
     */
    private $_Maximum = 0;

    /**
     * HandingByRange::getMaximum
     *
     * @access public
     * @return float
     */
    public function getMaximum() {
        return $this->_Maximum;
    }

    /**
     * HandingByRange::setMaximum
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaximum($value) {
        if ($value !== null) {
            $this->_Maximum = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Currency foreignkey property + getter/setter {{{

    /**
     * Currency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_Currency = false;

    /**
     * HandingByRange::getCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getCurrency() {
        if (is_int($this->_Currency) && $this->_Currency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_Currency = $mapper->load(
                array('Id'=>$this->_Currency));
        }
        return $this->_Currency;
    }

    /**
     * HandingByRange::getCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getCurrencyId() {
        if ($this->_Currency instanceof Currency) {
            return $this->_Currency->getId();
        }
        return (int)$this->_Currency;
    }

    /**
     * HandingByRange::setCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setCurrency($value) {
        if (is_numeric($value)) {
            $this->_Currency = (int)$value;
        } else {
            $this->_Currency = $value;
        }
    }

    // }}}
    // Category foreignkey property + getter/setter {{{

    /**
     * Category foreignkey
     *
     * @access private
     * @var mixed object Category or integer
     */
    private $_Category = false;

    /**
     * HandingByRange::getCategory
     *
     * @access public
     * @return object Category
     */
    public function getCategory() {
        if (is_int($this->_Category) && $this->_Category > 0) {
            $mapper = Mapper::singleton('Category');
            $this->_Category = $mapper->load(
                array('Id'=>$this->_Category));
        }
        return $this->_Category;
    }

    /**
     * HandingByRange::getCategoryId
     *
     * @access public
     * @return integer
     */
    public function getCategoryId() {
        if ($this->_Category instanceof Category) {
            return $this->_Category->getId();
        }
        return (int)$this->_Category;
    }

    /**
     * HandingByRange::setCategory
     *
     * @access public
     * @param object Category $value
     * @return void
     */
    public function setCategory($value) {
        if (is_numeric($value)) {
            $this->_Category = (int)$value;
        } else {
            $this->_Category = $value;
        }
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
        return 'HandingByRange';
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
    public static function getProperties() {
        $return = array(
            'Percent' => Object::TYPE_DECIMAL,
            'Minimum' => Object::TYPE_DECIMAL,
            'Maximum' => Object::TYPE_DECIMAL,
            'Currency' => 'Currency',
            'Category' => 'Category');
        return $return;
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
    public static function getLinks() {
        $return = array();
        return $return;
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
        return $return;
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
        return $return;
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
    public static function getMapping() {
        $return = array();
        return $return;
    }

    // }}}
}

?>