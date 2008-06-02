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

class RealAnswer extends Object {
    
    // Constructeur {{{

    /**
     * RealAnswer::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Action foreignkey property + getter/setter {{{

    /**
     * Action foreignkey
     *
     * @access private
     * @var mixed object Action or integer
     */
    private $_Action = false;

    /**
     * RealAnswer::getAction
     *
     * @access public
     * @return object Action
     */
    public function getAction() {
        if (is_int($this->_Action) && $this->_Action > 0) {
            $mapper = Mapper::singleton('Action');
            $this->_Action = $mapper->load(
                array('Id'=>$this->_Action));
        }
        return $this->_Action;
    }

    /**
     * RealAnswer::getActionId
     *
     * @access public
     * @return integer
     */
    public function getActionId() {
        if ($this->_Action instanceof Action) {
            return $this->_Action->getId();
        }
        return (int)$this->_Action;
    }

    /**
     * RealAnswer::setAction
     *
     * @access public
     * @param object Action $value
     * @return void
     */
    public function setAction($value) {
        if (is_numeric($value)) {
            $this->_Action = (int)$value;
        } else {
            $this->_Action = $value;
        }
    }

    // }}}
    // AnswerModel foreignkey property + getter/setter {{{

    /**
     * AnswerModel foreignkey
     *
     * @access private
     * @var mixed object AnswerModel or integer
     */
    private $_AnswerModel = false;

    /**
     * RealAnswer::getAnswerModel
     *
     * @access public
     * @return object AnswerModel
     */
    public function getAnswerModel() {
        if (is_int($this->_AnswerModel) && $this->_AnswerModel > 0) {
            $mapper = Mapper::singleton('AnswerModel');
            $this->_AnswerModel = $mapper->load(
                array('Id'=>$this->_AnswerModel));
        }
        return $this->_AnswerModel;
    }

    /**
     * RealAnswer::getAnswerModelId
     *
     * @access public
     * @return integer
     */
    public function getAnswerModelId() {
        if ($this->_AnswerModel instanceof AnswerModel) {
            return $this->_AnswerModel->getId();
        }
        return (int)$this->_AnswerModel;
    }

    /**
     * RealAnswer::setAnswerModel
     *
     * @access public
     * @param object AnswerModel $value
     * @return void
     */
    public function setAnswerModel($value) {
        if (is_numeric($value)) {
            $this->_AnswerModel = (int)$value;
        } else {
            $this->_AnswerModel = $value;
        }
    }

    // }}}
    // Question foreignkey property + getter/setter {{{

    /**
     * Question foreignkey
     *
     * @access private
     * @var mixed object Question or integer
     */
    private $_Question = false;

    /**
     * RealAnswer::getQuestion
     *
     * @access public
     * @return object Question
     */
    public function getQuestion() {
        if (is_int($this->_Question) && $this->_Question > 0) {
            $mapper = Mapper::singleton('Question');
            $this->_Question = $mapper->load(
                array('Id'=>$this->_Question));
        }
        return $this->_Question;
    }

    /**
     * RealAnswer::getQuestionId
     *
     * @access public
     * @return integer
     */
    public function getQuestionId() {
        if ($this->_Question instanceof Question) {
            return $this->_Question->getId();
        }
        return (int)$this->_Question;
    }

    /**
     * RealAnswer::setQuestion
     *
     * @access public
     * @param object Question $value
     * @return void
     */
    public function setQuestion($value) {
        if (is_numeric($value)) {
            $this->_Question = (int)$value;
        } else {
            $this->_Question = $value;
        }
    }

    // }}}
    // Value string property + getter/setter {{{

    /**
     * Value string property
     *
     * @access private
     * @var string
     */
    private $_Value = '';

    /**
     * RealAnswer::getValue
     *
     * @access public
     * @return string
     */
    public function getValue() {
        return $this->_Value;
    }

    /**
     * RealAnswer::setValue
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setValue($value) {
        $this->_Value = $value;
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
        return 'RealAnswer';
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
            'Action' => 'Action',
            'AnswerModel' => 'AnswerModel',
            'Question' => 'Question',
            'Value' => Object::TYPE_STRING);
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