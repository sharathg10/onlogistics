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

class ForwardingFormPacking extends Object {
    
    // Constructeur {{{

    /**
     * ForwardingFormPacking::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ForwardingForm foreignkey property + getter/setter {{{

    /**
     * ForwardingForm foreignkey
     *
     * @access private
     * @var mixed object ForwardingForm or integer
     */
    private $_ForwardingForm = false;

    /**
     * ForwardingFormPacking::getForwardingForm
     *
     * @access public
     * @return object ForwardingForm
     */
    public function getForwardingForm() {
        if (is_int($this->_ForwardingForm) && $this->_ForwardingForm > 0) {
            $mapper = Mapper::singleton('ForwardingForm');
            $this->_ForwardingForm = $mapper->load(
                array('Id'=>$this->_ForwardingForm));
        }
        return $this->_ForwardingForm;
    }

    /**
     * ForwardingFormPacking::getForwardingFormId
     *
     * @access public
     * @return integer
     */
    public function getForwardingFormId() {
        if ($this->_ForwardingForm instanceof ForwardingForm) {
            return $this->_ForwardingForm->getId();
        }
        return (int)$this->_ForwardingForm;
    }

    /**
     * ForwardingFormPacking::setForwardingForm
     *
     * @access public
     * @param object ForwardingForm $value
     * @return void
     */
    public function setForwardingForm($value) {
        if (is_numeric($value)) {
            $this->_ForwardingForm = (int)$value;
        } else {
            $this->_ForwardingForm = $value;
        }
    }

    // }}}
    // CoverType foreignkey property + getter/setter {{{

    /**
     * CoverType foreignkey
     *
     * @access private
     * @var mixed object CoverType or integer
     */
    private $_CoverType = 0;

    /**
     * ForwardingFormPacking::getCoverType
     *
     * @access public
     * @return object CoverType
     */
    public function getCoverType() {
        if (is_int($this->_CoverType) && $this->_CoverType > 0) {
            $mapper = Mapper::singleton('CoverType');
            $this->_CoverType = $mapper->load(
                array('Id'=>$this->_CoverType));
        }
        return $this->_CoverType;
    }

    /**
     * ForwardingFormPacking::getCoverTypeId
     *
     * @access public
     * @return integer
     */
    public function getCoverTypeId() {
        if ($this->_CoverType instanceof CoverType) {
            return $this->_CoverType->getId();
        }
        return (int)$this->_CoverType;
    }

    /**
     * ForwardingFormPacking::setCoverType
     *
     * @access public
     * @param object CoverType $value
     * @return void
     */
    public function setCoverType($value) {
        if (is_numeric($value)) {
            $this->_CoverType = (int)$value;
        } else {
            $this->_CoverType = $value;
        }
    }

    // }}}
    // Product foreignkey property + getter/setter {{{

    /**
     * Product foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_Product = 0;

    /**
     * ForwardingFormPacking::getProduct
     *
     * @access public
     * @return object Product
     */
    public function getProduct() {
        if (is_int($this->_Product) && $this->_Product > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_Product = $mapper->load(
                array('Id'=>$this->_Product));
        }
        return $this->_Product;
    }

    /**
     * ForwardingFormPacking::getProductId
     *
     * @access public
     * @return integer
     */
    public function getProductId() {
        if ($this->_Product instanceof Product) {
            return $this->_Product->getId();
        }
        return (int)$this->_Product;
    }

    /**
     * ForwardingFormPacking::setProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setProduct($value) {
        if (is_numeric($value)) {
            $this->_Product = (int)$value;
        } else {
            $this->_Product = $value;
        }
    }

    // }}}
    // Quantity string property + getter/setter {{{

    /**
     * Quantity int property
     *
     * @access private
     * @var integer
     */
    private $_Quantity = 0;

    /**
     * ForwardingFormPacking::getQuantity
     *
     * @access public
     * @return integer
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * ForwardingFormPacking::setQuantity
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setQuantity($value) {
        if ($value !== null) {
            $this->_Quantity = (int)$value;
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
        return 'ForwardingFormPacking';
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
            'ForwardingForm' => 'ForwardingForm',
            'CoverType' => 'CoverType',
            'Product' => 'Product',
            'Quantity' => Object::TYPE_INT);
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