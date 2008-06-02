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

class _Question extends Object {
    // class constants {{{

    const ANSWER_TYPE_TEXT = 0;
    const ANSWER_TYPE_SINGLE_SELECT = 1;
    const ANSWER_TYPE_MULTI_SELECT = 2;
    const ANSWER_TYPE_SINGLE_CHECKBOX = 3;
    const ANSWER_TYPE_CHECKBOX = 4;
    const ANSWER_TYPE_RADIO = 5;

    // }}}
    // Constructeur {{{

    /**
     * _Question::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Theme foreignkey property + getter/setter {{{

    /**
     * Theme foreignkey
     *
     * @access private
     * @var mixed object Theme or integer
     */
    private $_Theme = 0;

    /**
     * _Question::getTheme
     *
     * @access public
     * @return object Theme
     */
    public function getTheme() {
        if (is_int($this->_Theme) && $this->_Theme > 0) {
            $mapper = Mapper::singleton('Theme');
            $this->_Theme = $mapper->load(
                array('Id'=>$this->_Theme));
        }
        return $this->_Theme;
    }

    /**
     * _Question::getThemeId
     *
     * @access public
     * @return integer
     */
    public function getThemeId() {
        if ($this->_Theme instanceof Theme) {
            return $this->_Theme->getId();
        }
        return (int)$this->_Theme;
    }

    /**
     * _Question::setTheme
     *
     * @access public
     * @param object Theme $value
     * @return void
     */
    public function setTheme($value) {
        if (is_numeric($value)) {
            $this->_Theme = (int)$value;
        } else {
            $this->_Theme = $value;
        }
    }

    // }}}
    // Text string property + getter/setter {{{

    /**
     * Text string property
     *
     * @access private
     * @var string
     */
    private $_Text = '';

    /**
     * _Question::getText
     *
     * @access public
     * @return string
     */
    public function getText() {
        return $this->_Text;
    }

    /**
     * _Question::setText
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setText($value) {
        $this->_Text = $value;
    }

    // }}}
    // AnswerType const property + getter/setter/getAnswerTypeConstArray {{{

    /**
     * AnswerType int property
     *
     * @access private
     * @var integer
     */
    private $_AnswerType = 0;

    /**
     * _Question::getAnswerType
     *
     * @access public
     * @return integer
     */
    public function getAnswerType() {
        return $this->_AnswerType;
    }

    /**
     * _Question::setAnswerType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAnswerType($value) {
        if ($value !== null) {
            $this->_AnswerType = (int)$value;
        }
    }

    /**
     * _Question::getAnswerTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getAnswerTypeConstArray($keys = false) {
        $array = array(
            _Question::ANSWER_TYPE_TEXT => _("text"), 
            _Question::ANSWER_TYPE_SINGLE_SELECT => _("select list, single selection"), 
            _Question::ANSWER_TYPE_MULTI_SELECT => _("select list, multiple selection"), 
            _Question::ANSWER_TYPE_SINGLE_CHECKBOX => _("List with checkbox (only one possible answer)"), 
            _Question::ANSWER_TYPE_CHECKBOX => _("List with checkbox"), 
            _Question::ANSWER_TYPE_RADIO => _("List with radio button")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Alert foreignkey property + getter/setter {{{

    /**
     * Alert foreignkey
     *
     * @access private
     * @var mixed object Alert or integer
     */
    private $_Alert = false;

    /**
     * _Question::getAlert
     *
     * @access public
     * @return object Alert
     */
    public function getAlert() {
        if (is_int($this->_Alert) && $this->_Alert > 0) {
            $mapper = Mapper::singleton('Alert');
            $this->_Alert = $mapper->load(
                array('Id'=>$this->_Alert));
        }
        return $this->_Alert;
    }

    /**
     * _Question::getAlertId
     *
     * @access public
     * @return integer
     */
    public function getAlertId() {
        if ($this->_Alert instanceof Alert) {
            return $this->_Alert->getId();
        }
        return (int)$this->_Alert;
    }

    /**
     * _Question::setAlert
     *
     * @access public
     * @param object Alert $value
     * @return void
     */
    public function setAlert($value) {
        if (is_numeric($value)) {
            $this->_Alert = (int)$value;
        } else {
            $this->_Alert = $value;
        }
    }

    // }}}
    // Category one to many relation + getter/setter {{{

    /**
     * Category *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CategoryCollection = false;

    /**
     * _Question::getCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Question');
            return $mapper->getManyToMany($this->getId(),
                'Category', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CategoryCollection) {
            $mapper = Mapper::singleton('Question');
            $this->_CategoryCollection = $mapper->getManyToMany($this->getId(),
                'Category');
        }
        return $this->_CategoryCollection;
    }

    /**
     * _Question::getCategoryCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCategoryCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getCategoryCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_CategoryCollection) {
            $mapper = Mapper::singleton('Question');
            return $mapper->getManyToManyIds($this->getId(), 'Category');
        }
        return $this->_CategoryCollection->getItemIds();
    }

    /**
     * _Question::setCategoryCollectionIds
     *
     * @access public
     * @return array
     */
    public function setCategoryCollectionIds($itemIds) {
        $this->_CategoryCollection = new Collection('Category');
        foreach ($itemIds as $id) {
            $this->_CategoryCollection->setItem($id);
        }
    }

    /**
     * _Question::setCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCategoryCollection($value) {
        $this->_CategoryCollection = $value;
    }

    /**
     * _Question::CategoryCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function CategoryCollectionIsLoaded() {
        return ($this->_CategoryCollection !== false);
    }

    // }}}
    // LinkQuestionAnswerModel one to many relation + getter/setter {{{

    /**
     * LinkQuestionAnswerModel 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LinkQuestionAnswerModelCollection = false;

    /**
     * _Question::getLinkQuestionAnswerModelCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLinkQuestionAnswerModelCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Question');
            return $mapper->getOneToMany($this->getId(),
                'LinkQuestionAnswerModel', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LinkQuestionAnswerModelCollection) {
            $mapper = Mapper::singleton('Question');
            $this->_LinkQuestionAnswerModelCollection = $mapper->getOneToMany($this->getId(),
                'LinkQuestionAnswerModel');
        }
        return $this->_LinkQuestionAnswerModelCollection;
    }

    /**
     * _Question::getLinkQuestionAnswerModelCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLinkQuestionAnswerModelCollectionIds($filter = array()) {
        $col = $this->getLinkQuestionAnswerModelCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Question::setLinkQuestionAnswerModelCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLinkQuestionAnswerModelCollection($value) {
        $this->_LinkQuestionAnswerModelCollection = $value;
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
        return 'Question';
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
            'Theme' => 'Theme',
            'Text' => Object::TYPE_STRING,
            'AnswerType' => Object::TYPE_CONST,
            'Alert' => 'Alert');
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
            'Category'=>array(
                'linkClass'     => 'Category',
                'field'         => 'FromQuestion',
                'linkTable'     => 'questionToCategory',
                'linkField'     => 'ToCategory',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'LinkQuestionAnswerModel'=>array(
                'linkClass'     => 'LinkQuestionAnswerModel',
                'field'         => 'Question',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LinkParagraphModelQuestion'=>array(
                'linkClass'     => 'LinkParagraphModelQuestion',
                'field'         => 'Question',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RealAnswer'=>array(
                'linkClass'     => 'RealAnswer',
                'field'         => 'Question',
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
        $return = array('Text');
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
        return array('grid', 'add', 'edit', 'del');
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
        $return = array(
            'Theme'=>array(
                'label'        => _('Question theme'),
                'shortlabel'   => _('Theme'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Text'=>array(
                'label'        => _('Question message'),
                'shortlabel'   => _('Text'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'AnswerType'=>array(
                'label'        => _('Answer type'),
                'shortlabel'   => _('Answer type'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Category'=>array(
                'label'        => _('Targeted customer categories'),
                'shortlabel'   => _('Targeted customer categories'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>