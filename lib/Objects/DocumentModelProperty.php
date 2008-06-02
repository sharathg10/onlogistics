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

class DocumentModelProperty extends Object {
    // class constants {{{

    const CELL_NO_DOC = 1;
    const CELL_NO_COMMAND = 2;
    const CELL_NO_BL = 3;
    const CELL_PACKING_LIST = 4;
    const CELL_AIRWAY_BILL = 5;
    const CELL_CUSTOMER_CODE = 6;
    const CELL_COMMERCIAL = 7;
    const CELL_CUSTOMER_TVA = 8;
    const CELL_REGLEMENT = 9;
    const CELL_PORT_CONDITION = 10;
    const CELL_STORE = 11;
    const CELL_FAMILY = 12;
    const CELL_CUSTOMER_COMMAND_NO = 13;
    const CELL_DEAL = 14;
    const CELL_SUPPLIER_CODE = 15;
    const CELL_RESERVATION_NO = 16;
    const CELL_LOADING_PORT = 17;
    const CELL_SHIPMENT = 18;
    const CELL_VOLUME = 19;
    const CELL_TOTAL_PIECE = 20;

    // }}}
    // Constructeur {{{

    /**
     * DocumentModelProperty::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // PropertyType const property + getter/setter/getPropertyTypeConstArray {{{

    /**
     * PropertyType int property
     *
     * @access private
     * @var integer
     */
    private $_PropertyType = 0;

    /**
     * DocumentModelProperty::getPropertyType
     *
     * @access public
     * @return integer
     */
    public function getPropertyType() {
        return $this->_PropertyType;
    }

    /**
     * DocumentModelProperty::setPropertyType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPropertyType($value) {
        if ($value !== null) {
            $this->_PropertyType = (int)$value;
        }
    }

    /**
     * DocumentModelProperty::getPropertyTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPropertyTypeConstArray($keys = false) {
        $array = array(
            DocumentModelProperty::CELL_NO_DOC => _("Document number"), 
            DocumentModelProperty::CELL_NO_COMMAND => _("Order number"), 
            DocumentModelProperty::CELL_NO_BL => _("Delivery order number"), 
            DocumentModelProperty::CELL_PACKING_LIST => _("Packing list"), 
            DocumentModelProperty::CELL_AIRWAY_BILL => _("Air waybill"), 
            DocumentModelProperty::CELL_CUSTOMER_CODE => _("Customer code"), 
            DocumentModelProperty::CELL_COMMERCIAL => _("Salesman"), 
            DocumentModelProperty::CELL_CUSTOMER_TVA => _("VAT number"), 
            DocumentModelProperty::CELL_REGLEMENT => _("Payment"), 
            DocumentModelProperty::CELL_PORT_CONDITION => _("Incoterm"), 
            DocumentModelProperty::CELL_STORE => _("store"), 
            DocumentModelProperty::CELL_FAMILY => _("Season category"), 
            DocumentModelProperty::CELL_CUSTOMER_COMMAND_NO => _("Customer order number"), 
            DocumentModelProperty::CELL_DEAL => _("Deal number"), 
            DocumentModelProperty::CELL_SUPPLIER_CODE => _("Supplier code"), 
            DocumentModelProperty::CELL_RESERVATION_NO => _("Booking number"), 
            DocumentModelProperty::CELL_LOADING_PORT => _("Port of embarkation"), 
            DocumentModelProperty::CELL_SHIPMENT => _("Freight mode"), 
            DocumentModelProperty::CELL_VOLUME => _("Parcel volumetry"), 
            DocumentModelProperty::CELL_TOTAL_PIECE => _("Total number of parts")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * DocumentModelProperty::getProperty
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
     * DocumentModelProperty::getPropertyId
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
     * DocumentModelProperty::setProperty
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
    // Order string property + getter/setter {{{

    /**
     * Order int property
     *
     * @access private
     * @var integer
     */
    private $_Order = 0;

    /**
     * DocumentModelProperty::getOrder
     *
     * @access public
     * @return integer
     */
    public function getOrder() {
        return $this->_Order;
    }

    /**
     * DocumentModelProperty::setOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setOrder($value) {
        if ($value !== null) {
            $this->_Order = (int)$value;
        }
    }

    // }}}
    // DocumentModel foreignkey property + getter/setter {{{

    /**
     * DocumentModel foreignkey
     *
     * @access private
     * @var mixed object DocumentModel or integer
     */
    private $_DocumentModel = false;

    /**
     * DocumentModelProperty::getDocumentModel
     *
     * @access public
     * @return object DocumentModel
     */
    public function getDocumentModel() {
        if (is_int($this->_DocumentModel) && $this->_DocumentModel > 0) {
            $mapper = Mapper::singleton('DocumentModel');
            $this->_DocumentModel = $mapper->load(
                array('Id'=>$this->_DocumentModel));
        }
        return $this->_DocumentModel;
    }

    /**
     * DocumentModelProperty::getDocumentModelId
     *
     * @access public
     * @return integer
     */
    public function getDocumentModelId() {
        if ($this->_DocumentModel instanceof DocumentModel) {
            return $this->_DocumentModel->getId();
        }
        return (int)$this->_DocumentModel;
    }

    /**
     * DocumentModelProperty::setDocumentModel
     *
     * @access public
     * @param object DocumentModel $value
     * @return void
     */
    public function setDocumentModel($value) {
        if (is_numeric($value)) {
            $this->_DocumentModel = (int)$value;
        } else {
            $this->_DocumentModel = $value;
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
        return 'DocumentModelProperty';
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
            'PropertyType' => Object::TYPE_CONST,
            'Property' => 'Property',
            'Order' => Object::TYPE_INT,
            'DocumentModel' => 'DocumentModel');
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