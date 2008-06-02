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

class _DocumentModel extends Object {
    // class constants {{{

    const NO_LOGO = 0;
    const EXPEDITOR = 1;
    const DESTINATOR = 2;
    const ONE_ACTOR = 3;

    // }}}
    // Constructeur {{{

    /**
     * _DocumentModel::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name string property + getter/setter {{{

    /**
     * Name string property
     *
     * @access private
     * @var string
     */
    private $_Name = '';

    /**
     * _DocumentModel::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _DocumentModel::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Footer string property + getter/setter {{{

    /**
     * Footer string property
     *
     * @access private
     * @var string
     */
    private $_Footer = '';

    /**
     * _DocumentModel::getFooter
     *
     * @access public
     * @return string
     */
    public function getFooter() {
        return $this->_Footer;
    }

    /**
     * _DocumentModel::setFooter
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setFooter($value) {
        $this->_Footer = $value;
    }

    // }}}
    // LogoType const property + getter/setter/getLogoTypeConstArray {{{

    /**
     * LogoType int property
     *
     * @access private
     * @var integer
     */
    private $_LogoType = 0;

    /**
     * _DocumentModel::getLogoType
     *
     * @access public
     * @return integer
     */
    public function getLogoType() {
        return $this->_LogoType;
    }

    /**
     * _DocumentModel::setLogoType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLogoType($value) {
        if ($value !== null) {
            $this->_LogoType = (int)$value;
        }
    }

    /**
     * _DocumentModel::getLogoTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getLogoTypeConstArray($keys = false) {
        $array = array(
            _DocumentModel::NO_LOGO => _("No logo"), 
            _DocumentModel::EXPEDITOR => _("Order shipper"), 
            _DocumentModel::DESTINATOR => _("Order addressee"), 
            _DocumentModel::ONE_ACTOR => _("Fixed actor")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // DocType string property + getter/setter {{{

    /**
     * DocType string property
     *
     * @access private
     * @var string
     */
    private $_DocType = '';

    /**
     * _DocumentModel::getDocType
     *
     * @access public
     * @return string
     */
    public function getDocType() {
        return $this->_DocType;
    }

    /**
     * _DocumentModel::setDocType
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDocType($value) {
        $this->_DocType = $value;
    }

    // }}}
    // Default string property + getter/setter {{{

    /**
     * Default int property
     *
     * @access private
     * @var integer
     */
    private $_Default = 0;

    /**
     * _DocumentModel::getDefault
     *
     * @access public
     * @return integer
     */
    public function getDefault() {
        return $this->_Default;
    }

    /**
     * _DocumentModel::setDefault
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDefault($value) {
        if ($value !== null) {
            $this->_Default = (int)$value;
        }
    }

    // }}}
    // DisplayDuplicata string property + getter/setter {{{

    /**
     * DisplayDuplicata int property
     *
     * @access private
     * @var integer
     */
    private $_DisplayDuplicata = 1;

    /**
     * _DocumentModel::getDisplayDuplicata
     *
     * @access public
     * @return integer
     */
    public function getDisplayDuplicata() {
        return $this->_DisplayDuplicata;
    }

    /**
     * _DocumentModel::setDisplayDuplicata
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDisplayDuplicata($value) {
        if ($value !== null) {
            $this->_DisplayDuplicata = (int)$value;
        }
    }

    // }}}
    // Actor foreignkey property + getter/setter {{{

    /**
     * Actor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Actor = false;

    /**
     * _DocumentModel::getActor
     *
     * @access public
     * @return object Actor
     */
    public function getActor() {
        if (is_int($this->_Actor) && $this->_Actor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Actor = $mapper->load(
                array('Id'=>$this->_Actor));
        }
        return $this->_Actor;
    }

    /**
     * _DocumentModel::getActorId
     *
     * @access public
     * @return integer
     */
    public function getActorId() {
        if ($this->_Actor instanceof Actor) {
            return $this->_Actor->getId();
        }
        return (int)$this->_Actor;
    }

    /**
     * _DocumentModel::setActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setActor($value) {
        if (is_numeric($value)) {
            $this->_Actor = (int)$value;
        } else {
            $this->_Actor = $value;
        }
    }

    // }}}
    // Number string property + getter/setter {{{

    /**
     * Number int property
     *
     * @access private
     * @var integer
     */
    private $_Number = 1;

    /**
     * _DocumentModel::getNumber
     *
     * @access public
     * @return integer
     */
    public function getNumber() {
        return $this->_Number;
    }

    /**
     * _DocumentModel::setNumber
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setNumber($value) {
        if ($value !== null) {
            $this->_Number = (int)$value;
        }
    }

    // }}}
    // DisplayTotalWeight string property + getter/setter {{{

    /**
     * DisplayTotalWeight int property
     *
     * @access private
     * @var integer
     */
    private $_DisplayTotalWeight = 1;

    /**
     * _DocumentModel::getDisplayTotalWeight
     *
     * @access public
     * @return integer
     */
    public function getDisplayTotalWeight() {
        return $this->_DisplayTotalWeight;
    }

    /**
     * _DocumentModel::setDisplayTotalWeight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDisplayTotalWeight($value) {
        if ($value !== null) {
            $this->_DisplayTotalWeight = (int)$value;
        }
    }

    // }}}
    // DisplayProductDetail string property + getter/setter {{{

    /**
     * DisplayProductDetail int property
     *
     * @access private
     * @var integer
     */
    private $_DisplayProductDetail = 0;

    /**
     * _DocumentModel::getDisplayProductDetail
     *
     * @access public
     * @return integer
     */
    public function getDisplayProductDetail() {
        return $this->_DisplayProductDetail;
    }

    /**
     * _DocumentModel::setDisplayProductDetail
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDisplayProductDetail($value) {
        if ($value !== null) {
            $this->_DisplayProductDetail = (int)$value;
        }
    }

    // }}}
    // AbstractDocument one to many relation + getter/setter {{{

    /**
     * AbstractDocument 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AbstractDocumentCollection = false;

    /**
     * _DocumentModel::getAbstractDocumentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAbstractDocumentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('DocumentModel');
            return $mapper->getOneToMany($this->getId(),
                'AbstractDocument', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AbstractDocumentCollection) {
            $mapper = Mapper::singleton('DocumentModel');
            $this->_AbstractDocumentCollection = $mapper->getOneToMany($this->getId(),
                'AbstractDocument');
        }
        return $this->_AbstractDocumentCollection;
    }

    /**
     * _DocumentModel::getAbstractDocumentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAbstractDocumentCollectionIds($filter = array()) {
        $col = $this->getAbstractDocumentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _DocumentModel::setAbstractDocumentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAbstractDocumentCollection($value) {
        $this->_AbstractDocumentCollection = $value;
    }

    // }}}
    // DocumentModelProperty one to many relation + getter/setter {{{

    /**
     * DocumentModelProperty 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_DocumentModelPropertyCollection = false;

    /**
     * _DocumentModel::getDocumentModelPropertyCollection
     *
     * @access public
     * @return object Collection
     */
    public function getDocumentModelPropertyCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('DocumentModel');
            return $mapper->getOneToMany($this->getId(),
                'DocumentModelProperty', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_DocumentModelPropertyCollection) {
            $mapper = Mapper::singleton('DocumentModel');
            $this->_DocumentModelPropertyCollection = $mapper->getOneToMany($this->getId(),
                'DocumentModelProperty');
        }
        return $this->_DocumentModelPropertyCollection;
    }

    /**
     * _DocumentModel::getDocumentModelPropertyCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getDocumentModelPropertyCollectionIds($filter = array()) {
        $col = $this->getDocumentModelPropertyCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _DocumentModel::setDocumentModelPropertyCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setDocumentModelPropertyCollection($value) {
        $this->_DocumentModelPropertyCollection = $value;
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
        return 'DocumentModel';
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
            'Name' => Object::TYPE_STRING,
            'Footer' => Object::TYPE_TEXT,
            'LogoType' => Object::TYPE_CONST,
            'DocType' => Object::TYPE_STRING,
            'Default' => Object::TYPE_BOOL,
            'DisplayDuplicata' => Object::TYPE_BOOL,
            'Actor' => 'Actor',
            'Number' => Object::TYPE_INT,
            'DisplayTotalWeight' => Object::TYPE_BOOL,
            'DisplayProductDetail' => Object::TYPE_BOOL);
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
            'AbstractDocument'=>array(
                'linkClass'     => 'AbstractDocument',
                'field'         => 'DocumentModel',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'DocumentModelProperty'=>array(
                'linkClass'     => 'DocumentModelProperty',
                'field'         => 'DocumentModel',
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