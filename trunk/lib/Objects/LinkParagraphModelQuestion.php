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

class LinkParagraphModelQuestion extends Object {
    
    // Constructeur {{{

    /**
     * LinkParagraphModelQuestion::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // QuestionOrder int property + getter/setter {{{

    /**
     * QuestionOrder int property
     *
     * @access private
     * @var integer
     */
    private $_QuestionOrder = null;

    /**
     * LinkParagraphModelQuestion::getQuestionOrder
     *
     * @access public
     * @return integer
     */
    public function getQuestionOrder() {
        return $this->_QuestionOrder;
    }

    /**
     * LinkParagraphModelQuestion::setQuestionOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setQuestionOrder($value) {
        $this->_QuestionOrder = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // ParagraphModel foreignkey property + getter/setter {{{

    /**
     * ParagraphModel foreignkey
     *
     * @access private
     * @var mixed object ParagraphModel or integer
     */
    private $_ParagraphModel = false;

    /**
     * LinkParagraphModelQuestion::getParagraphModel
     *
     * @access public
     * @return object ParagraphModel
     */
    public function getParagraphModel() {
        if (is_int($this->_ParagraphModel) && $this->_ParagraphModel > 0) {
            $mapper = Mapper::singleton('ParagraphModel');
            $this->_ParagraphModel = $mapper->load(
                array('Id'=>$this->_ParagraphModel));
        }
        return $this->_ParagraphModel;
    }

    /**
     * LinkParagraphModelQuestion::getParagraphModelId
     *
     * @access public
     * @return integer
     */
    public function getParagraphModelId() {
        if ($this->_ParagraphModel instanceof ParagraphModel) {
            return $this->_ParagraphModel->getId();
        }
        return (int)$this->_ParagraphModel;
    }

    /**
     * LinkParagraphModelQuestion::setParagraphModel
     *
     * @access public
     * @param object ParagraphModel $value
     * @return void
     */
    public function setParagraphModel($value) {
        if (is_numeric($value)) {
            $this->_ParagraphModel = (int)$value;
        } else {
            $this->_ParagraphModel = $value;
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
     * LinkParagraphModelQuestion::getQuestion
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
     * LinkParagraphModelQuestion::getQuestionId
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
     * LinkParagraphModelQuestion::setQuestion
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'LinkParagraphModelQuestion';
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
            'QuestionOrder' => Object::TYPE_INT,
            'ParagraphModel' => 'ParagraphModel',
            'Question' => 'Question');
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