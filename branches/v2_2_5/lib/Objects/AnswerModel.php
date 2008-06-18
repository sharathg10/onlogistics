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

class AnswerModel extends Object {
    
    // Constructeur {{{

    /**
     * AnswerModel::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * AnswerModel::getValue
     *
     * @access public
     * @return string
     */
    public function getValue() {
        return $this->_Value;
    }

    /**
     * AnswerModel::setValue
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setValue($value) {
        $this->_Value = $value;
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
     * AnswerModel::getAlert
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
     * AnswerModel::getAlertId
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
     * AnswerModel::setAlert
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'AnswerModel';
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
            'Value' => Object::TYPE_STRING,
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
            'LinkQuestionAnswerModel'=>array(
                'linkClass'     => 'LinkQuestionAnswerModel',
                'field'         => 'AnswerModel',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RealAnswer'=>array(
                'linkClass'     => 'RealAnswer',
                'field'         => 'AnswerModel',
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
        $return = array('Value');
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
            'Value'=>array(
                'label'        => _('Answer text'),
                'shortlabel'   => _('Answer text'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>