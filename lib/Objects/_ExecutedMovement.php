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

class _ExecutedMovement extends Object {
    // class constants {{{

    const EN_COURS = 0;
    const EXECUTE_TOTALEMENT = 1;
    const EXECUTE_PARTIELLEMENT = 2;

    // }}}
    // Constructeur {{{

    /**
     * _ExecutedMovement::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // StartDate datetime property + getter/setter {{{

    /**
     * StartDate int property
     *
     * @access private
     * @var string
     */
    private $_StartDate = 0;

    /**
     * _ExecutedMovement::getStartDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getStartDate($format = false) {
        return $this->dateFormat($this->_StartDate, $format);
    }

    /**
     * _ExecutedMovement::setStartDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStartDate($value) {
        $this->_StartDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * _ExecutedMovement::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * _ExecutedMovement::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // Type foreignkey property + getter/setter {{{

    /**
     * Type foreignkey
     *
     * @access private
     * @var mixed object MovementType or integer
     */
    private $_Type = false;

    /**
     * _ExecutedMovement::getType
     *
     * @access public
     * @return object MovementType
     */
    public function getType() {
        if (is_int($this->_Type) && $this->_Type > 0) {
            $mapper = Mapper::singleton('MovementType');
            $this->_Type = $mapper->load(
                array('Id'=>$this->_Type));
        }
        return $this->_Type;
    }

    /**
     * _ExecutedMovement::getTypeId
     *
     * @access public
     * @return integer
     */
    public function getTypeId() {
        if ($this->_Type instanceof MovementType) {
            return $this->_Type->getId();
        }
        return (int)$this->_Type;
    }

    /**
     * _ExecutedMovement::setType
     *
     * @access public
     * @param object MovementType $value
     * @return void
     */
    public function setType($value) {
        if (is_numeric($value)) {
            $this->_Type = (int)$value;
        } else {
            $this->_Type = $value;
        }
    }

    // }}}
    // State const property + getter/setter/getStateConstArray {{{

    /**
     * State int property
     *
     * @access private
     * @var integer
     */
    private $_State = 0;

    /**
     * _ExecutedMovement::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _ExecutedMovement::setState
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setState($value) {
        if ($value !== null) {
            $this->_State = (int)$value;
        }
    }

    /**
     * _ExecutedMovement::getStateConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getStateConstArray($keys = false) {
        $array = array(
            _ExecutedMovement::EN_COURS => _("In progress"), 
            _ExecutedMovement::EXECUTE_TOTALEMENT => _("Finished"), 
            _ExecutedMovement::EXECUTE_PARTIELLEMENT => _("Partial")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * _ExecutedMovement::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _ExecutedMovement::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // RealProduct foreignkey property + getter/setter {{{

    /**
     * RealProduct foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_RealProduct = false;

    /**
     * _ExecutedMovement::getRealProduct
     *
     * @access public
     * @return object Product
     */
    public function getRealProduct() {
        if (is_int($this->_RealProduct) && $this->_RealProduct > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_RealProduct = $mapper->load(
                array('Id'=>$this->_RealProduct));
        }
        return $this->_RealProduct;
    }

    /**
     * _ExecutedMovement::getRealProductId
     *
     * @access public
     * @return integer
     */
    public function getRealProductId() {
        if ($this->_RealProduct instanceof Product) {
            return $this->_RealProduct->getId();
        }
        return (int)$this->_RealProduct;
    }

    /**
     * _ExecutedMovement::setRealProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setRealProduct($value) {
        if (is_numeric($value)) {
            $this->_RealProduct = (int)$value;
        } else {
            $this->_RealProduct = $value;
        }
    }

    // }}}
    // RealQuantity float property + getter/setter {{{

    /**
     * RealQuantity float property
     *
     * @access private
     * @var float
     */
    private $_RealQuantity = 0;

    /**
     * _ExecutedMovement::getRealQuantity
     *
     * @access public
     * @return float
     */
    public function getRealQuantity() {
        return $this->_RealQuantity;
    }

    /**
     * _ExecutedMovement::setRealQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealQuantity($value) {
        if ($value !== null) {
            $this->_RealQuantity = round(I18N::extractNumber($value), 3);
        }
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
     * _ExecutedMovement::getActivatedMovement
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
     * _ExecutedMovement::getActivatedMovementId
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
     * _ExecutedMovement::setActivatedMovement
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
    // LocationExecutedMovement one to many relation + getter/setter {{{

    /**
     * LocationExecutedMovement 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationExecutedMovementCollection = false;

    /**
     * _ExecutedMovement::getLocationExecutedMovementCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationExecutedMovementCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ExecutedMovement');
            return $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationExecutedMovementCollection) {
            $mapper = Mapper::singleton('ExecutedMovement');
            $this->_LocationExecutedMovementCollection = $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement');
        }
        return $this->_LocationExecutedMovementCollection;
    }

    /**
     * _ExecutedMovement::getLocationExecutedMovementCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationExecutedMovementCollectionIds($filter = array()) {
        $col = $this->getLocationExecutedMovementCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ExecutedMovement::setLocationExecutedMovementCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationExecutedMovementCollection($value) {
        $this->_LocationExecutedMovementCollection = $value;
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
        return 'ExecutedMovement';
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
            'StartDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'Type' => 'MovementType',
            'State' => Object::TYPE_CONST,
            'Comment' => Object::TYPE_STRING,
            'RealProduct' => 'Product',
            'RealQuantity' => Object::TYPE_DECIMAL,
            'ActivatedMovement' => 'ActivatedMovement');
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
            'LocationExecutedMovement'=>array(
                'linkClass'     => 'LocationExecutedMovement',
                'field'         => 'ExecutedMovement',
                'ondelete'      => 'cascade',
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