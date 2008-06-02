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

class ToHave extends AbstractDocument {
    // class constants {{{

    const TOHAVE_RETOUR_MARCHANDISE = 1;
    const TOHAVE_DISCOUNT = 2;
    const TOHAVE_WITHOUT_COMMAND = 3;
    const TOHAVE_DISCOUNT_ANNUAL_TURNOVER = 4;

    // }}}
    // Constructeur {{{

    /**
     * ToHave::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Type const property + getter/setter/getTypeConstArray {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * ToHave::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * ToHave::setType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setType($value) {
        if ($value !== null) {
            $this->_Type = (int)$value;
        }
    }

    /**
     * ToHave::getTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTypeConstArray($keys = false) {
        $array = array(
            ToHave::TOHAVE_RETOUR_MARCHANDISE => _("Return of goods"), 
            ToHave::TOHAVE_DISCOUNT => _("Discount"), 
            ToHave::TOHAVE_WITHOUT_COMMAND => _("Not linked to an order"), 
            ToHave::TOHAVE_DISCOUNT_ANNUAL_TURNOVER => _("Annual turnover discount")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // TotalPriceHT float property + getter/setter {{{

    /**
     * TotalPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_TotalPriceHT = null;

    /**
     * ToHave::getTotalPriceHT
     *
     * @access public
     * @return float
     */
    public function getTotalPriceHT() {
        return $this->_TotalPriceHT;
    }

    /**
     * ToHave::setTotalPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalPriceHT($value) {
        $this->_TotalPriceHT = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // TotalPriceTTC float property + getter/setter {{{

    /**
     * TotalPriceTTC float property
     *
     * @access private
     * @var float
     */
    private $_TotalPriceTTC = null;

    /**
     * ToHave::getTotalPriceTTC
     *
     * @access public
     * @return float
     */
    public function getTotalPriceTTC() {
        return $this->_TotalPriceTTC;
    }

    /**
     * ToHave::setTotalPriceTTC
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalPriceTTC($value) {
        $this->_TotalPriceTTC = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // RemainingTTC float property + getter/setter {{{

    /**
     * RemainingTTC float property
     *
     * @access private
     * @var float
     */
    private $_RemainingTTC = null;

    /**
     * ToHave::getRemainingTTC
     *
     * @access public
     * @return float
     */
    public function getRemainingTTC() {
        return $this->_RemainingTTC;
    }

    /**
     * ToHave::setRemainingTTC
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRemainingTTC($value) {
        $this->_RemainingTTC = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // Comment string property + getter/setter {{{

    /**
     * Comment string property
     *
     * @access private
     * @var string
     */
    private $_Comment = '';

    /**
     * ToHave::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * ToHave::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // TVA foreignkey property + getter/setter {{{

    /**
     * TVA foreignkey
     *
     * @access private
     * @var mixed object TVA or integer
     */
    private $_TVA = false;

    /**
     * ToHave::getTVA
     *
     * @access public
     * @return object TVA
     */
    public function getTVA() {
        if (is_int($this->_TVA) && $this->_TVA > 0) {
            $mapper = Mapper::singleton('TVA');
            $this->_TVA = $mapper->load(
                array('Id'=>$this->_TVA));
        }
        return $this->_TVA;
    }

    /**
     * ToHave::getTVAId
     *
     * @access public
     * @return integer
     */
    public function getTVAId() {
        if ($this->_TVA instanceof TVA) {
            return $this->_TVA->getId();
        }
        return (int)$this->_TVA;
    }

    /**
     * ToHave::setTVA
     *
     * @access public
     * @param object TVA $value
     * @return void
     */
    public function setTVA($value) {
        if (is_numeric($value)) {
            $this->_TVA = (int)$value;
        } else {
            $this->_TVA = $value;
        }
    }

    // }}}
    // InvoicePayment one to many relation + getter/setter {{{

    /**
     * InvoicePayment 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_InvoicePaymentCollection = false;

    /**
     * ToHave::getInvoicePaymentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getInvoicePaymentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ToHave');
            return $mapper->getOneToMany($this->getId(),
                'InvoicePayment', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_InvoicePaymentCollection) {
            $mapper = Mapper::singleton('ToHave');
            $this->_InvoicePaymentCollection = $mapper->getOneToMany($this->getId(),
                'InvoicePayment');
        }
        return $this->_InvoicePaymentCollection;
    }

    /**
     * ToHave::getInvoicePaymentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getInvoicePaymentCollectionIds($filter = array()) {
        $col = $this->getInvoicePaymentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * ToHave::setInvoicePaymentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setInvoicePaymentCollection($value) {
        $this->_InvoicePaymentCollection = $value;
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
        return 'AbstractDocument';
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
            'Type' => Object::TYPE_CONST,
            'TotalPriceHT' => Object::TYPE_DECIMAL,
            'TotalPriceTTC' => Object::TYPE_DECIMAL,
            'RemainingTTC' => Object::TYPE_DECIMAL,
            'Comment' => Object::TYPE_TEXT,
            'TVA' => 'TVA');
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
        $return = array(
            'InvoicePayment'=>array(
                'linkClass'     => 'InvoicePayment',
                'field'         => 'ToHave',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ));
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
        return 'AbstractDocument';
    }

    // }}}
}

?>