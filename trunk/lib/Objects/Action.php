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

class Action extends Object {
    // class constants {{{

    const ACTION_STATE_TODO = 0;
    const ACTION_STATE_CURRENT = 1;
    const ACTION_STATE_DO = 2;
    const ACTION_STATE_ALERT = 3;

    // }}}
    // Constructeur {{{

    /**
     * Action::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Commercial foreignkey property + getter/setter {{{

    /**
     * Commercial foreignkey
     *
     * @access private
     * @var mixed object UserAccount or integer
     */
    private $_Commercial = false;

    /**
     * Action::getCommercial
     *
     * @access public
     * @return object UserAccount
     */
    public function getCommercial() {
        if (is_int($this->_Commercial) && $this->_Commercial > 0) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_Commercial = $mapper->load(
                array('Id'=>$this->_Commercial));
        }
        return $this->_Commercial;
    }

    /**
     * Action::getCommercialId
     *
     * @access public
     * @return integer
     */
    public function getCommercialId() {
        if ($this->_Commercial instanceof UserAccount) {
            return $this->_Commercial->getId();
        }
        return (int)$this->_Commercial;
    }

    /**
     * Action::setCommercial
     *
     * @access public
     * @param object UserAccount $value
     * @return void
     */
    public function setCommercial($value) {
        if (is_numeric($value)) {
            $this->_Commercial = (int)$value;
        } else {
            $this->_Commercial = $value;
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
     * Action::getActor
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
     * Action::getActorId
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
     * Action::setActor
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
    // FormModel foreignkey property + getter/setter {{{

    /**
     * FormModel foreignkey
     *
     * @access private
     * @var mixed object FormModel or integer
     */
    private $_FormModel = 0;

    /**
     * Action::getFormModel
     *
     * @access public
     * @return object FormModel
     */
    public function getFormModel() {
        if (is_int($this->_FormModel) && $this->_FormModel > 0) {
            $mapper = Mapper::singleton('FormModel');
            $this->_FormModel = $mapper->load(
                array('Id'=>$this->_FormModel));
        }
        return $this->_FormModel;
    }

    /**
     * Action::getFormModelId
     *
     * @access public
     * @return integer
     */
    public function getFormModelId() {
        if ($this->_FormModel instanceof FormModel) {
            return $this->_FormModel->getId();
        }
        return (int)$this->_FormModel;
    }

    /**
     * Action::setFormModel
     *
     * @access public
     * @param object FormModel $value
     * @return void
     */
    public function setFormModel($value) {
        if (is_numeric($value)) {
            $this->_FormModel = (int)$value;
        } else {
            $this->_FormModel = $value;
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
    private $_WishedDate = '0';

    /**
     * Action::getWishedDate
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
     * Action::setWishedDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setWishedDate($value) {
        $this->_WishedDate = $value;
    }

    // }}}
    // ActionDate datetime property + getter/setter {{{

    /**
     * ActionDate int property
     *
     * @access private
     * @var string
     */
    private $_ActionDate = 0;

    /**
     * Action::getActionDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getActionDate($format = false) {
        return $this->dateFormat($this->_ActionDate, $format);
    }

    /**
     * Action::setActionDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setActionDate($value) {
        $this->_ActionDate = $value;
    }

    // }}}
    // Type string property + getter/setter {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * Action::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * Action::setType
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
     * Action::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * Action::setState
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
     * Action::getStateConstArray
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
            Action::ACTION_STATE_TODO => _("To do"), 
            Action::ACTION_STATE_CURRENT => _("In progress"), 
            Action::ACTION_STATE_DO => _("Finished"), 
            Action::ACTION_STATE_ALERT => _("In alert")
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
     * Action::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * Action::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // RealAnswer one to many relation + getter/setter {{{

    /**
     * RealAnswer 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_RealAnswerCollection = false;

    /**
     * Action::getRealAnswerCollection
     *
     * @access public
     * @return object Collection
     */
    public function getRealAnswerCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Action');
            return $mapper->getOneToMany($this->getId(),
                'RealAnswer', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_RealAnswerCollection) {
            $mapper = Mapper::singleton('Action');
            $this->_RealAnswerCollection = $mapper->getOneToMany($this->getId(),
                'RealAnswer');
        }
        return $this->_RealAnswerCollection;
    }

    /**
     * Action::getRealAnswerCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getRealAnswerCollectionIds($filter = array()) {
        $col = $this->getRealAnswerCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * Action::setRealAnswerCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setRealAnswerCollection($value) {
        $this->_RealAnswerCollection = $value;
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
        return 'Action';
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
            'Commercial' => 'UserAccount',
            'Actor' => 'Actor',
            'FormModel' => 'FormModel',
            'WishedDate' => Object::TYPE_DATETIME,
            'ActionDate' => Object::TYPE_DATETIME,
            'Type' => Object::TYPE_INT,
            'State' => Object::TYPE_CONST,
            'Comment' => Object::TYPE_TEXT);
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
            'RealAnswer'=>array(
                'linkClass'     => 'RealAnswer',
                'field'         => 'Action',
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