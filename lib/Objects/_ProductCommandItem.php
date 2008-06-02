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

class _ProductCommandItem extends CommandItem {
    
    // Constructeur {{{

    /**
     * _ProductCommandItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _ProductCommandItem::getProduct
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
     * _ProductCommandItem::getProductId
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
     * _ProductCommandItem::setProduct
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
    // PackagingUnitQuantity int property + getter/setter {{{

    /**
     * PackagingUnitQuantity int property
     *
     * @access private
     * @var integer
     */
    private $_PackagingUnitQuantity = null;

    /**
     * _ProductCommandItem::getPackagingUnitQuantity
     *
     * @access public
     * @return integer
     */
    public function getPackagingUnitQuantity() {
        return $this->_PackagingUnitQuantity;
    }

    /**
     * _ProductCommandItem::setPackagingUnitQuantity
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPackagingUnitQuantity($value) {
        $this->_PackagingUnitQuantity = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // ActivatedMovement foreignkey property + getter/setter {{{

    /**
     * ActivatedMovement foreignkey
     *
     * @access private
     * @var mixed object ActivatedMovement or integer
     */
    private $_ActivatedMovement = false;

    /**
     * _ProductCommandItem::getActivatedMovement
     *
     * @access public
     * @return object ActivatedMovement
     */
    public function getActivatedMovement() {
        if (is_int($this->_ActivatedMovement) && $this->_ActivatedMovement > 0) {
            $mapper = Mapper::singleton('ActivatedMovement');
            $this->_ActivatedMovement = $mapper->load(
                array('Id'=>$this->_ActivatedMovement));
        }
        return $this->_ActivatedMovement;
    }

    /**
     * _ProductCommandItem::getActivatedMovementId
     *
     * @access public
     * @return integer
     */
    public function getActivatedMovementId() {
        if ($this->_ActivatedMovement instanceof ActivatedMovement) {
            return $this->_ActivatedMovement->getId();
        }
        return (int)$this->_ActivatedMovement;
    }

    /**
     * _ProductCommandItem::setActivatedMovement
     *
     * @access public
     * @param object ActivatedMovement $value
     * @return void
     */
    public function setActivatedMovement($value) {
        if (is_numeric($value)) {
            $this->_ActivatedMovement = (int)$value;
        } else {
            $this->_ActivatedMovement = $value;
        }
    }

    // }}}
    // Command foreignkey property + getter/setter {{{

    /**
     * Command foreignkey
     *
     * @access private
     * @var mixed object ProductCommand or integer
     */
    private $_Command = false;

    /**
     * _ProductCommandItem::getCommand
     *
     * @access public
     * @return object ProductCommand
     */
    public function getCommand() {
        if (is_int($this->_Command) && $this->_Command > 0) {
            $mapper = Mapper::singleton('ProductCommand');
            $this->_Command = $mapper->load(
                array('Id'=>$this->_Command));
        }
        return $this->_Command;
    }

    /**
     * _ProductCommandItem::getCommandId
     *
     * @access public
     * @return integer
     */
    public function getCommandId() {
        if ($this->_Command instanceof ProductCommand) {
            return $this->_Command->getId();
        }
        return (int)$this->_Command;
    }

    /**
     * _ProductCommandItem::setCommand
     *
     * @access public
     * @param object ProductCommand $value
     * @return void
     */
    public function setCommand($value) {
        if (is_numeric($value)) {
            $this->_Command = (int)$value;
        } else {
            $this->_Command = $value;
        }
    }

    // }}}
    // Promotion foreignkey property + getter/setter {{{

    /**
     * Promotion foreignkey
     *
     * @access private
     * @var mixed object Promotion or integer
     */
    private $_Promotion = false;

    /**
     * _ProductCommandItem::getPromotion
     *
     * @access public
     * @return object Promotion
     */
    public function getPromotion() {
        if (is_int($this->_Promotion) && $this->_Promotion > 0) {
            $mapper = Mapper::singleton('Promotion');
            $this->_Promotion = $mapper->load(
                array('Id'=>$this->_Promotion));
        }
        return $this->_Promotion;
    }

    /**
     * _ProductCommandItem::getPromotionId
     *
     * @access public
     * @return integer
     */
    public function getPromotionId() {
        if ($this->_Promotion instanceof Promotion) {
            return $this->_Promotion->getId();
        }
        return (int)$this->_Promotion;
    }

    /**
     * _ProductCommandItem::setPromotion
     *
     * @access public
     * @param object Promotion $value
     * @return void
     */
    public function setPromotion($value) {
        if (is_numeric($value)) {
            $this->_Promotion = (int)$value;
        } else {
            $this->_Promotion = $value;
        }
    }

    // }}}
    // ActivatedMovement one to many relation + getter/setter {{{

    /**
     * ActivatedMovement 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedMovementCollection = false;

    /**
     * _ProductCommandItem::getActivatedMovementCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedMovementCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ProductCommandItem');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedMovement', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedMovementCollection) {
            $mapper = Mapper::singleton('ProductCommandItem');
            $this->_ActivatedMovementCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedMovement');
        }
        return $this->_ActivatedMovementCollection;
    }

    /**
     * _ProductCommandItem::getActivatedMovementCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedMovementCollectionIds($filter = array()) {
        $col = $this->getActivatedMovementCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ProductCommandItem::setActivatedMovementCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedMovementCollection($value) {
        $this->_ActivatedMovementCollection = $value;
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
            'Product' => 'Product',
            'PackagingUnitQuantity' => Object::TYPE_INT,
            'ActivatedMovement' => 'ActivatedMovement',
            'Command' => 'ProductCommand',
            'Promotion' => 'Promotion');
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
            'ActivatedMovement'=>array(
                'linkClass'     => 'ActivatedMovement',
                'field'         => 'ProductCommandItem',
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
        return 'CommandItem';
    }

    // }}}
}

?>