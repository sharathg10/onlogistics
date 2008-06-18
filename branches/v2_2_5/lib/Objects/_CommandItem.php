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

class _CommandItem extends Object {
    // class constants {{{

    const MASTER_DIMENSION_LENGTH = 1;
    const MASTER_DIMENSION_WIDTH = 2;
    const MASTER_DIMENSION_HEIGHT = 3;

    // }}}
    // Constructeur {{{

    /**
     * _CommandItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ActivatedChain foreignkey property + getter/setter {{{

    /**
     * ActivatedChain foreignkey
     *
     * @access private
     * @var mixed object ActivatedChain or integer
     */
    private $_ActivatedChain = false;

    /**
     * _CommandItem::getActivatedChain
     *
     * @access public
     * @return object ActivatedChain
     */
    public function getActivatedChain() {
        if (is_int($this->_ActivatedChain) && $this->_ActivatedChain > 0) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_ActivatedChain = $mapper->load(
                array('Id'=>$this->_ActivatedChain));
        }
        return $this->_ActivatedChain;
    }

    /**
     * _CommandItem::getActivatedChainId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainId() {
        if ($this->_ActivatedChain instanceof ActivatedChain) {
            return $this->_ActivatedChain->getId();
        }
        return (int)$this->_ActivatedChain;
    }

    /**
     * _CommandItem::setActivatedChain
     *
     * @access public
     * @param object ActivatedChain $value
     * @return void
     */
    public function setActivatedChain($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChain = (int)$value;
        } else {
            $this->_ActivatedChain = $value;
        }
    }

    // }}}
    // Command foreignkey property + getter/setter {{{

    /**
     * Command foreignkey
     *
     * @access private
     * @var mixed object Command or integer
     */
    private $_Command = false;

    /**
     * _CommandItem::getCommand
     *
     * @access public
     * @return object Command
     */
    public function getCommand() {
        if (is_int($this->_Command) && $this->_Command > 0) {
            $mapper = Mapper::singleton('Command');
            $this->_Command = $mapper->load(
                array('Id'=>$this->_Command));
        }
        return $this->_Command;
    }

    /**
     * _CommandItem::getCommandId
     *
     * @access public
     * @return integer
     */
    public function getCommandId() {
        if ($this->_Command instanceof Command) {
            return $this->_Command->getId();
        }
        return (int)$this->_Command;
    }

    /**
     * _CommandItem::setCommand
     *
     * @access public
     * @param object Command $value
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
    // Width float property + getter/setter {{{

    /**
     * Width float property
     *
     * @access private
     * @var float
     */
    private $_Width = 0;

    /**
     * _CommandItem::getWidth
     *
     * @access public
     * @return float
     */
    public function getWidth() {
        return $this->_Width;
    }

    /**
     * _CommandItem::setWidth
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setWidth($value) {
        if ($value !== null) {
            $this->_Width = I18N::extractNumber($value);
        }
    }

    // }}}
    // Height float property + getter/setter {{{

    /**
     * Height float property
     *
     * @access private
     * @var float
     */
    private $_Height = 0;

    /**
     * _CommandItem::getHeight
     *
     * @access public
     * @return float
     */
    public function getHeight() {
        return $this->_Height;
    }

    /**
     * _CommandItem::setHeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setHeight($value) {
        if ($value !== null) {
            $this->_Height = I18N::extractNumber($value);
        }
    }

    // }}}
    // Length float property + getter/setter {{{

    /**
     * Length float property
     *
     * @access private
     * @var float
     */
    private $_Length = 0;

    /**
     * _CommandItem::getLength
     *
     * @access public
     * @return float
     */
    public function getLength() {
        return $this->_Length;
    }

    /**
     * _CommandItem::setLength
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setLength($value) {
        if ($value !== null) {
            $this->_Length = I18N::extractNumber($value);
        }
    }

    // }}}
    // Weight float property + getter/setter {{{

    /**
     * Weight float property
     *
     * @access private
     * @var float
     */
    private $_Weight = 0;

    /**
     * _CommandItem::getWeight
     *
     * @access public
     * @return float
     */
    public function getWeight() {
        return $this->_Weight;
    }

    /**
     * _CommandItem::setWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setWeight($value) {
        if ($value !== null) {
            $this->_Weight = I18N::extractNumber($value);
        }
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
     * _CommandItem::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * _CommandItem::setQuantity
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
    // Gerbability string property + getter/setter {{{

    /**
     * Gerbability int property
     *
     * @access private
     * @var integer
     */
    private $_Gerbability = 0;

    /**
     * _CommandItem::getGerbability
     *
     * @access public
     * @return integer
     */
    public function getGerbability() {
        return $this->_Gerbability;
    }

    /**
     * _CommandItem::setGerbability
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setGerbability($value) {
        if ($value !== null) {
            $this->_Gerbability = (int)$value;
        }
    }

    // }}}
    // MasterDimension const property + getter/setter/getMasterDimensionConstArray {{{

    /**
     * MasterDimension int property
     *
     * @access private
     * @var integer
     */
    private $_MasterDimension = 0;

    /**
     * _CommandItem::getMasterDimension
     *
     * @access public
     * @return integer
     */
    public function getMasterDimension() {
        return $this->_MasterDimension;
    }

    /**
     * _CommandItem::setMasterDimension
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMasterDimension($value) {
        if ($value !== null) {
            $this->_MasterDimension = (int)$value;
        }
    }

    /**
     * _CommandItem::getMasterDimensionConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getMasterDimensionConstArray($keys = false) {
        $array = array(
            _CommandItem::MASTER_DIMENSION_LENGTH => _("Length"), 
            _CommandItem::MASTER_DIMENSION_WIDTH => _("Width"), 
            _CommandItem::MASTER_DIMENSION_HEIGHT => _("Height")
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
     * _CommandItem::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _CommandItem::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // Handing string property + getter/setter {{{

    /**
     * Handing string property
     *
     * @access private
     * @var string
     */
    private $_Handing = '0';

    /**
     * _CommandItem::getHanding
     *
     * @access public
     * @return string
     */
    public function getHanding() {
        return $this->_Handing;
    }

    /**
     * _CommandItem::setHanding
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setHanding($value) {
        $this->_Handing = $value;
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
     * _CommandItem::getTVA
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
     * _CommandItem::getTVAId
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
     * _CommandItem::setTVA
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
    // PriceHT float property + getter/setter {{{

    /**
     * PriceHT float property
     *
     * @access private
     * @var float
     */
    private $_PriceHT = 0;

    /**
     * _CommandItem::getPriceHT
     *
     * @access public
     * @return float
     */
    public function getPriceHT() {
        return $this->_PriceHT;
    }

    /**
     * _CommandItem::setPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPriceHT($value) {
        if ($value !== null) {
            $this->_PriceHT = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // WishedDate datetime property + getter/setter {{{

    /**
     * WishedDate int property
     *
     * @access private
     * @var string
     */
    private $_WishedDate = 0;

    /**
     * _CommandItem::getWishedDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getWishedDate($format = false) {
        return $this->dateFormat($this->_WishedDate, $format);
    }

    /**
     * _CommandItem::setWishedDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setWishedDate($value) {
        $this->_WishedDate = $value;
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
     * _CommandItem::getBoxCollection
     *
     * @access public
     * @return object Collection
     */
    public function getBoxCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('CommandItem');
            return $mapper->getOneToMany($this->getId(),
                'Box', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_BoxCollection) {
            $mapper = Mapper::singleton('CommandItem');
            $this->_BoxCollection = $mapper->getOneToMany($this->getId(),
                'Box');
        }
        return $this->_BoxCollection;
    }

    /**
     * _CommandItem::getBoxCollectionIds
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
     * _CommandItem::setBoxCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setBoxCollection($value) {
        $this->_BoxCollection = $value;
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
    public static function getProperties() {
        $return = array(
            'ActivatedChain' => 'ActivatedChain',
            'Command' => 'Command',
            'Width' => Object::TYPE_FLOAT,
            'Height' => Object::TYPE_FLOAT,
            'Length' => Object::TYPE_FLOAT,
            'Weight' => Object::TYPE_FLOAT,
            'Quantity' => Object::TYPE_DECIMAL,
            'Gerbability' => Object::TYPE_INT,
            'MasterDimension' => Object::TYPE_CONST,
            'Comment' => Object::TYPE_STRING,
            'Handing' => Object::TYPE_STRING,
            'TVA' => 'TVA',
            'PriceHT' => Object::TYPE_DECIMAL,
            'WishedDate' => Object::TYPE_DATETIME);
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
                'field'         => 'CommandItem',
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
    // _CommandItem::mutate() {{{

    /**
     * "Mutation" d'un objet parent en classe fille et vice-versa.
     * Cela permet par exemple dans un formulaire de modifier la classe d'un
     * objet via un select.
     *
     * @access public
     * @param string type le type de l'objet vers lequel 'muter'
     * @return object
     **/
    public function mutate($type){
        // on instancie le bon objet
        require_once('Objects/' . $type . '.php');
        $mutant = new $type();
        if(!($mutant instanceof _CommandItem)) {
            trigger_error('Invalid classname provided.', E_USER_ERROR);
        }
        // propriétés fixes
        $mutant->hasBeenInitialized = $this->hasBeenInitialized;
        $mutant->readonly = $this->readonly;
        $mutant->setId($this->getId());
        // propriétés simples
        $properties = $this->getProperties();
        foreach($properties as $property=>$type){
            $getter = 'get' . $property;
            $setter = 'set' . $property;
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        // relations
        $links = $this->getLinks();
        foreach($links as $property=>$data){
            $getter = 'get' . $property . 'Collection';
            $setter = 'set' . $property . 'Collection';
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        return $mutant;
    }

    // }}}
}

?>