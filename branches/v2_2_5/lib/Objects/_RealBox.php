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

class _RealBox extends Object {
    
    // Constructeur {{{

    /**
     * _RealBox::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Grouping foreignkey property + getter/setter {{{

    /**
     * Grouping foreignkey
     *
     * @access private
     * @var mixed object Grouping or integer
     */
    private $_Grouping = false;

    /**
     * _RealBox::getGrouping
     *
     * @access public
     * @return object Grouping
     */
    public function getGrouping() {
        if (is_int($this->_Grouping) && $this->_Grouping > 0) {
            $mapper = Mapper::singleton('Grouping');
            $this->_Grouping = $mapper->load(
                array('Id'=>$this->_Grouping));
        }
        return $this->_Grouping;
    }

    /**
     * _RealBox::getGroupingId
     *
     * @access public
     * @return integer
     */
    public function getGroupingId() {
        if ($this->_Grouping instanceof Grouping) {
            return $this->_Grouping->getId();
        }
        return (int)$this->_Grouping;
    }

    /**
     * _RealBox::setGrouping
     *
     * @access public
     * @param object Grouping $value
     * @return void
     */
    public function setGrouping($value) {
        if (is_numeric($value)) {
            $this->_Grouping = (int)$value;
        } else {
            $this->_Grouping = $value;
        }
    }

    // }}}
    // ActivatedChainTask foreignkey property + getter/setter {{{

    /**
     * ActivatedChainTask foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_ActivatedChainTask = false;

    /**
     * _RealBox::getActivatedChainTask
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getActivatedChainTask() {
        if (is_int($this->_ActivatedChainTask) && $this->_ActivatedChainTask > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_ActivatedChainTask = $mapper->load(
                array('Id'=>$this->_ActivatedChainTask));
        }
        return $this->_ActivatedChainTask;
    }

    /**
     * _RealBox::getActivatedChainTaskId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainTaskId() {
        if ($this->_ActivatedChainTask instanceof ActivatedChainTask) {
            return $this->_ActivatedChainTask->getId();
        }
        return (int)$this->_ActivatedChainTask;
    }

    /**
     * _RealBox::setActivatedChainTask
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setActivatedChainTask($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChainTask = (int)$value;
        } else {
            $this->_ActivatedChainTask = $value;
        }
    }

    // }}}
    // PN string property + getter/setter {{{

    /**
     * PN string property
     *
     * @access private
     * @var string
     */
    private $_PN = '';

    /**
     * _RealBox::getPN
     *
     * @access public
     * @return string
     */
    public function getPN() {
        return $this->_PN;
    }

    /**
     * _RealBox::setPN
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPN($value) {
        $this->_PN = $value;
    }

    // }}}
    // SN string property + getter/setter {{{

    /**
     * SN string property
     *
     * @access private
     * @var string
     */
    private $_SN = '';

    /**
     * _RealBox::getSN
     *
     * @access public
     * @return string
     */
    public function getSN() {
        return $this->_SN;
    }

    /**
     * _RealBox::setSN
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSN($value) {
        $this->_SN = $value;
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
        return 'RealBox';
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
            'Grouping' => 'Grouping',
            'ActivatedChainTask' => 'ActivatedChainTask',
            'PN' => Object::TYPE_STRING,
            'SN' => Object::TYPE_STRING);
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