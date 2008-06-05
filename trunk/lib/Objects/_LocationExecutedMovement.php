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

class _LocationExecutedMovement extends Object {
    // class constants {{{

    const ANNULATION = 1;
    const REFUS_CLIENT = 2;
    const LIVRAISON_IMPOSSIBLE = 3;

    // }}}
    // Constructeur {{{

    /**
     * _LocationExecutedMovement::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = 0;

    /**
     * _LocationExecutedMovement::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * _LocationExecutedMovement::setQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantity($value) {
        if ($value !== null) {
            $this->_Quantity = round(I18N::extractNumber($value), 3);
        }
    }

    // }}}
    // PrestationFactured string property + getter/setter {{{

    /**
     * PrestationFactured int property
     *
     * @access private
     * @var integer
     */
    private $_PrestationFactured = 0;

    /**
     * _LocationExecutedMovement::getPrestationFactured
     *
     * @access public
     * @return integer
     */
    public function getPrestationFactured() {
        return $this->_PrestationFactured;
    }

    /**
     * _LocationExecutedMovement::setPrestationFactured
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPrestationFactured($value) {
        if ($value !== null) {
            $this->_PrestationFactured = (int)$value;
        }
    }

    // }}}
    // TransportPrestationFactured string property + getter/setter {{{

    /**
     * TransportPrestationFactured int property
     *
     * @access private
     * @var integer
     */
    private $_TransportPrestationFactured = 0;

    /**
     * _LocationExecutedMovement::getTransportPrestationFactured
     *
     * @access public
     * @return integer
     */
    public function getTransportPrestationFactured() {
        return $this->_TransportPrestationFactured;
    }

    /**
     * _LocationExecutedMovement::setTransportPrestationFactured
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTransportPrestationFactured($value) {
        if ($value !== null) {
            $this->_TransportPrestationFactured = (int)$value;
        }
    }

    // }}}
    // InvoicePrestation foreignkey property + getter/setter {{{

    /**
     * InvoicePrestation foreignkey
     *
     * @access private
     * @var mixed object Invoice or integer
     */
    private $_InvoicePrestation = 0;

    /**
     * _LocationExecutedMovement::getInvoicePrestation
     *
     * @access public
     * @return object Invoice
     */
    public function getInvoicePrestation() {
        if (is_int($this->_InvoicePrestation) && $this->_InvoicePrestation > 0) {
            $mapper = Mapper::singleton('Invoice');
            $this->_InvoicePrestation = $mapper->load(
                array('Id'=>$this->_InvoicePrestation));
        }
        return $this->_InvoicePrestation;
    }

    /**
     * _LocationExecutedMovement::getInvoicePrestationId
     *
     * @access public
     * @return integer
     */
    public function getInvoicePrestationId() {
        if ($this->_InvoicePrestation instanceof Invoice) {
            return $this->_InvoicePrestation->getId();
        }
        return (int)$this->_InvoicePrestation;
    }

    /**
     * _LocationExecutedMovement::setInvoicePrestation
     *
     * @access public
     * @param object Invoice $value
     * @return void
     */
    public function setInvoicePrestation($value) {
        if (is_numeric($value)) {
            $this->_InvoicePrestation = (int)$value;
        } else {
            $this->_InvoicePrestation = $value;
        }
    }

    // }}}
    // Date datetime property + getter/setter {{{

    /**
     * Date int property
     *
     * @access private
     * @var string
     */
    private $_Date = 0;

    /**
     * _LocationExecutedMovement::getDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getDate($format = false) {
        return $this->dateFormat($this->_Date, $format);
    }

    /**
     * _LocationExecutedMovement::setDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDate($value) {
        $this->_Date = $value;
    }

    // }}}
    // ExecutedMovement foreignkey property + getter/setter {{{

    /**
     * ExecutedMovement foreignkey
     *
     * @access private
     * @var mixed object ExecutedMovement or integer
     */
    private $_ExecutedMovement = false;

    /**
     * _LocationExecutedMovement::getExecutedMovement
     *
     * @access public
     * @return object ExecutedMovement
     */
    public function getExecutedMovement() {
        if (is_int($this->_ExecutedMovement) && $this->_ExecutedMovement > 0) {
            $mapper = Mapper::singleton('ExecutedMovement');
            $this->_ExecutedMovement = $mapper->load(
                array('Id'=>$this->_ExecutedMovement));
        }
        return $this->_ExecutedMovement;
    }

    /**
     * _LocationExecutedMovement::getExecutedMovementId
     *
     * @access public
     * @return integer
     */
    public function getExecutedMovementId() {
        if ($this->_ExecutedMovement instanceof ExecutedMovement) {
            return $this->_ExecutedMovement->getId();
        }
        return (int)$this->_ExecutedMovement;
    }

    /**
     * _LocationExecutedMovement::setExecutedMovement
     *
     * @access public
     * @param object ExecutedMovement $value
     * @return void
     */
    public function setExecutedMovement($value) {
        if (is_numeric($value)) {
            $this->_ExecutedMovement = (int)$value;
        } else {
            $this->_ExecutedMovement = $value;
        }
    }

    // }}}
    // Location foreignkey property + getter/setter {{{

    /**
     * Location foreignkey
     *
     * @access private
     * @var mixed object Location or integer
     */
    private $_Location = false;

    /**
     * _LocationExecutedMovement::getLocation
     *
     * @access public
     * @return object Location
     */
    public function getLocation() {
        if (is_int($this->_Location) && $this->_Location > 0) {
            $mapper = Mapper::singleton('Location');
            $this->_Location = $mapper->load(
                array('Id'=>$this->_Location));
        }
        return $this->_Location;
    }

    /**
     * _LocationExecutedMovement::getLocationId
     *
     * @access public
     * @return integer
     */
    public function getLocationId() {
        if ($this->_Location instanceof Location) {
            return $this->_Location->getId();
        }
        return (int)$this->_Location;
    }

    /**
     * _LocationExecutedMovement::setLocation
     *
     * @access public
     * @param object Location $value
     * @return void
     */
    public function setLocation($value) {
        if (is_numeric($value)) {
            $this->_Location = (int)$value;
        } else {
            $this->_Location = $value;
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
    private $_Product = false;

    /**
     * _LocationExecutedMovement::getProduct
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
     * _LocationExecutedMovement::getProductId
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
     * _LocationExecutedMovement::setProduct
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
    // Cancelled const property + getter/setter/getCancelledConstArray {{{

    /**
     * Cancelled int property
     *
     * @access private
     * @var integer
     */
    private $_Cancelled = 0;

    /**
     * _LocationExecutedMovement::getCancelled
     *
     * @access public
     * @return integer
     */
    public function getCancelled() {
        return $this->_Cancelled;
    }

    /**
     * _LocationExecutedMovement::setCancelled
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCancelled($value) {
        if ($value !== null) {
            $this->_Cancelled = (int)$value;
        }
    }

    /**
     * _LocationExecutedMovement::getCancelledConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCancelledConstArray($keys = false) {
        $array = array(
            _LocationExecutedMovement::ANNULATION => _("Cancellation"), 
            _LocationExecutedMovement::REFUS_CLIENT => _("Customer refusal"), 
            _LocationExecutedMovement::LIVRAISON_IMPOSSIBLE => _("Impossible delivery")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CancelledMovement foreignkey property + getter/setter {{{

    /**
     * CancelledMovement foreignkey
     *
     * @access private
     * @var mixed object LocationExecutedMovement or integer
     */
    private $_CancelledMovement = false;

    /**
     * _LocationExecutedMovement::getCancelledMovement
     *
     * @access public
     * @return object LocationExecutedMovement
     */
    public function getCancelledMovement() {
        if (is_int($this->_CancelledMovement) && $this->_CancelledMovement > 0) {
            $mapper = Mapper::singleton('LocationExecutedMovement');
            $this->_CancelledMovement = $mapper->load(
                array('Id'=>$this->_CancelledMovement));
        }
        return $this->_CancelledMovement;
    }

    /**
     * _LocationExecutedMovement::getCancelledMovementId
     *
     * @access public
     * @return integer
     */
    public function getCancelledMovementId() {
        if ($this->_CancelledMovement instanceof LocationExecutedMovement) {
            return $this->_CancelledMovement->getId();
        }
        return (int)$this->_CancelledMovement;
    }

    /**
     * _LocationExecutedMovement::setCancelledMovement
     *
     * @access public
     * @param object LocationExecutedMovement $value
     * @return void
     */
    public function setCancelledMovement($value) {
        if (is_numeric($value)) {
            $this->_CancelledMovement = (int)$value;
        } else {
            $this->_CancelledMovement = $value;
        }
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
     * _LocationExecutedMovement::getForwardingForm
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
     * _LocationExecutedMovement::getForwardingFormId
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
     * _LocationExecutedMovement::setForwardingForm
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
    // InvoiceItem foreignkey property + getter/setter {{{

    /**
     * InvoiceItem foreignkey
     *
     * @access private
     * @var mixed object InvoiceItem or integer
     */
    private $_InvoiceItem = false;

    /**
     * _LocationExecutedMovement::getInvoiceItem
     *
     * @access public
     * @return object InvoiceItem
     */
    public function getInvoiceItem() {
        if (is_int($this->_InvoiceItem) && $this->_InvoiceItem > 0) {
            $mapper = Mapper::singleton('InvoiceItem');
            $this->_InvoiceItem = $mapper->load(
                array('Id'=>$this->_InvoiceItem));
        }
        return $this->_InvoiceItem;
    }

    /**
     * _LocationExecutedMovement::getInvoiceItemId
     *
     * @access public
     * @return integer
     */
    public function getInvoiceItemId() {
        if ($this->_InvoiceItem instanceof InvoiceItem) {
            return $this->_InvoiceItem->getId();
        }
        return (int)$this->_InvoiceItem;
    }

    /**
     * _LocationExecutedMovement::setInvoiceItem
     *
     * @access public
     * @param object InvoiceItem $value
     * @return void
     */
    public function setInvoiceItem($value) {
        if (is_numeric($value)) {
            $this->_InvoiceItem = (int)$value;
        } else {
            $this->_InvoiceItem = $value;
        }
    }

    // }}}
    // Box one to many relation + getter/setter {{{

    /**
     * Box 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_BoxCollection = false;

    /**
     * _LocationExecutedMovement::getBoxCollection
     *
     * @access public
     * @return object Collection
     */
    public function getBoxCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('LocationExecutedMovement');
            return $mapper->getOneToMany($this->getId(),
                'Box', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_BoxCollection) {
            $mapper = Mapper::singleton('LocationExecutedMovement');
            $this->_BoxCollection = $mapper->getOneToMany($this->getId(),
                'Box');
        }
        return $this->_BoxCollection;
    }

    /**
     * _LocationExecutedMovement::getBoxCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getBoxCollectionIds($filter = array()) {
        $col = $this->getBoxCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _LocationExecutedMovement::setBoxCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setBoxCollection($value) {
        $this->_BoxCollection = $value;
    }

    // }}}
    // LEMConcreteProduct one to many relation + getter/setter {{{

    /**
     * LEMConcreteProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LEMConcreteProductCollection = false;

    /**
     * _LocationExecutedMovement::getLEMConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLEMConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('LocationExecutedMovement');
            return $mapper->getOneToMany($this->getId(),
                'LEMConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LEMConcreteProductCollection) {
            $mapper = Mapper::singleton('LocationExecutedMovement');
            $this->_LEMConcreteProductCollection = $mapper->getOneToMany($this->getId(),
                'LEMConcreteProduct');
        }
        return $this->_LEMConcreteProductCollection;
    }

    /**
     * _LocationExecutedMovement::getLEMConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLEMConcreteProductCollectionIds($filter = array()) {
        $col = $this->getLEMConcreteProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _LocationExecutedMovement::setLEMConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLEMConcreteProductCollection($value) {
        $this->_LEMConcreteProductCollection = $value;
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
        return 'LocationExecutedMovement';
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
            'Quantity' => Object::TYPE_DECIMAL,
            'PrestationFactured' => Object::TYPE_BOOL,
            'TransportPrestationFactured' => Object::TYPE_BOOL,
            'InvoicePrestation' => 'Invoice',
            'Date' => Object::TYPE_DATETIME,
            'ExecutedMovement' => 'ExecutedMovement',
            'Location' => 'Location',
            'Product' => 'Product',
            'Cancelled' => Object::TYPE_CONST,
            'CancelledMovement' => 'LocationExecutedMovement',
            'ForwardingForm' => 'ForwardingForm',
            'InvoiceItem' => 'InvoiceItem');
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
        $return = array(
            'Box'=>array(
                'linkClass'     => 'Box',
                'field'         => 'LocationExecutedMovement',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'LEMConcreteProduct'=>array(
                'linkClass'     => 'LEMConcreteProduct',
                'field'         => 'LocationExecutedMovement',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'LocationExecutedMovement'=>array(
                'linkClass'     => 'LocationExecutedMovement',
                'field'         => 'CancelledMovement',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ));
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
