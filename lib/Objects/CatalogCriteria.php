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

class CatalogCriteria extends Object {
    
    // Constructeur {{{

    /**
     * CatalogCriteria::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Property foreignkey property + getter/setter {{{

    /**
     * Property foreignkey
     *
     * @access private
     * @var mixed object Property or integer
     */
    private $_Property = false;

    /**
     * CatalogCriteria::getProperty
     *
     * @access public
     * @return object Property
     */
    public function getProperty() {
        if (is_int($this->_Property) && $this->_Property > 0) {
            $mapper = Mapper::singleton('Property');
            $this->_Property = $mapper->load(
                array('Id'=>$this->_Property));
        }
        return $this->_Property;
    }

    /**
     * CatalogCriteria::getPropertyId
     *
     * @access public
     * @return integer
     */
    public function getPropertyId() {
        if ($this->_Property instanceof Property) {
            return $this->_Property->getId();
        }
        return (int)$this->_Property;
    }

    /**
     * CatalogCriteria::setProperty
     *
     * @access public
     * @param object Property $value
     * @return void
     */
    public function setProperty($value) {
        if (is_numeric($value)) {
            $this->_Property = (int)$value;
        } else {
            $this->_Property = $value;
        }
    }

    // }}}
    // DisplayName string property + getter/setter {{{

    /**
     * DisplayName string property
     *
     * @access private
     * @var string
     */
    private $_DisplayName = '';

    /**
     * CatalogCriteria::getDisplayName
     *
     * @access public
     * @return string
     */
    public function getDisplayName() {
        return $this->_DisplayName;
    }

    /**
     * CatalogCriteria::setDisplayName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDisplayName($value) {
        $this->_DisplayName = $value;
    }

    // }}}
    // Index string property + getter/setter {{{

    /**
     * Index int property
     *
     * @access private
     * @var integer
     */
    private $_Index = 0;

    /**
     * CatalogCriteria::getIndex
     *
     * @access public
     * @return integer
     */
    public function getIndex() {
        return $this->_Index;
    }

    /**
     * CatalogCriteria::setIndex
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIndex($value) {
        if ($value !== null) {
            $this->_Index = (int)$value;
        }
    }

    // }}}
    // Displayable string property + getter/setter {{{

    /**
     * Displayable int property
     *
     * @access private
     * @var integer
     */
    private $_Displayable = 0;

    /**
     * CatalogCriteria::getDisplayable
     *
     * @access public
     * @return integer
     */
    public function getDisplayable() {
        return $this->_Displayable;
    }

    /**
     * CatalogCriteria::setDisplayable
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDisplayable($value) {
        if ($value !== null) {
            $this->_Displayable = (int)$value;
        }
    }

    // }}}
    // Searchable string property + getter/setter {{{

    /**
     * Searchable int property
     *
     * @access private
     * @var integer
     */
    private $_Searchable = 0;

    /**
     * CatalogCriteria::getSearchable
     *
     * @access public
     * @return integer
     */
    public function getSearchable() {
        return $this->_Searchable;
    }

    /**
     * CatalogCriteria::setSearchable
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setSearchable($value) {
        if ($value !== null) {
            $this->_Searchable = (int)$value;
        }
    }

    // }}}
    // SearchIndex string property + getter/setter {{{

    /**
     * SearchIndex int property
     *
     * @access private
     * @var integer
     */
    private $_SearchIndex = 0;

    /**
     * CatalogCriteria::getSearchIndex
     *
     * @access public
     * @return integer
     */
    public function getSearchIndex() {
        return $this->_SearchIndex;
    }

    /**
     * CatalogCriteria::setSearchIndex
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setSearchIndex($value) {
        if ($value !== null) {
            $this->_SearchIndex = (int)$value;
        }
    }

    // }}}
    // Catalog foreignkey property + getter/setter {{{

    /**
     * Catalog foreignkey
     *
     * @access private
     * @var mixed object Catalog or integer
     */
    private $_Catalog = false;

    /**
     * CatalogCriteria::getCatalog
     *
     * @access public
     * @return object Catalog
     */
    public function getCatalog() {
        if (is_int($this->_Catalog) && $this->_Catalog > 0) {
            $mapper = Mapper::singleton('Catalog');
            $this->_Catalog = $mapper->load(
                array('Id'=>$this->_Catalog));
        }
        return $this->_Catalog;
    }

    /**
     * CatalogCriteria::getCatalogId
     *
     * @access public
     * @return integer
     */
    public function getCatalogId() {
        if ($this->_Catalog instanceof Catalog) {
            return $this->_Catalog->getId();
        }
        return (int)$this->_Catalog;
    }

    /**
     * CatalogCriteria::setCatalog
     *
     * @access public
     * @param object Catalog $value
     * @return void
     */
    public function setCatalog($value) {
        if (is_numeric($value)) {
            $this->_Catalog = (int)$value;
        } else {
            $this->_Catalog = $value;
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
        return 'CatalogCriteria';
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
            'Property' => 'Property',
            'DisplayName' => Object::TYPE_STRING,
            'Index' => Object::TYPE_INT,
            'Displayable' => Object::TYPE_INT,
            'Searchable' => Object::TYPE_INT,
            'SearchIndex' => Object::TYPE_INT,
            'Catalog' => 'Catalog');
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