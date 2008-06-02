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

class _FlowItem extends Object {
    
    // Constructeur {{{

    /**
     * _FlowItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // TotalHT float property + getter/setter {{{

    /**
     * TotalHT float property
     *
     * @access private
     * @var float
     */
    private $_TotalHT = 0;

    /**
     * _FlowItem::getTotalHT
     *
     * @access public
     * @return float
     */
    public function getTotalHT() {
        return $this->_TotalHT;
    }

    /**
     * _FlowItem::setTotalHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalHT($value) {
        if ($value !== null) {
            $this->_TotalHT = round(I18N::extractNumber($value), 2);
        }
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
     * _FlowItem::getTVA
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
     * _FlowItem::getTVAId
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
     * _FlowItem::setTVA
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
    // Handing string property + getter/setter {{{

    /**
     * Handing string property
     *
     * @access private
     * @var string
     */
    private $_Handing = '0';

    /**
     * _FlowItem::getHanding
     *
     * @access public
     * @return string
     */
    public function getHanding() {
        return $this->_Handing;
    }

    /**
     * _FlowItem::setHanding
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setHanding($value) {
        $this->_Handing = $value;
    }

    // }}}
    // Flow foreignkey property + getter/setter {{{

    /**
     * Flow foreignkey
     *
     * @access private
     * @var mixed object Flow or integer
     */
    private $_Flow = false;

    /**
     * _FlowItem::getFlow
     *
     * @access public
     * @return object Flow
     */
    public function getFlow() {
        if (is_int($this->_Flow) && $this->_Flow > 0) {
            $mapper = Mapper::singleton('Flow');
            $this->_Flow = $mapper->load(
                array('Id'=>$this->_Flow));
        }
        return $this->_Flow;
    }

    /**
     * _FlowItem::getFlowId
     *
     * @access public
     * @return integer
     */
    public function getFlowId() {
        if ($this->_Flow instanceof Flow) {
            return $this->_Flow->getId();
        }
        return (int)$this->_Flow;
    }

    /**
     * _FlowItem::setFlow
     *
     * @access public
     * @param object Flow $value
     * @return void
     */
    public function setFlow($value) {
        if (is_numeric($value)) {
            $this->_Flow = (int)$value;
        } else {
            $this->_Flow = $value;
        }
    }

    // }}}
    // Type foreignkey property + getter/setter {{{

    /**
     * Type foreignkey
     *
     * @access private
     * @var mixed object FlowTypeItem or integer
     */
    private $_Type = false;

    /**
     * _FlowItem::getType
     *
     * @access public
     * @return object FlowTypeItem
     */
    public function getType() {
        if (is_int($this->_Type) && $this->_Type > 0) {
            $mapper = Mapper::singleton('FlowTypeItem');
            $this->_Type = $mapper->load(
                array('Id'=>$this->_Type));
        }
        return $this->_Type;
    }

    /**
     * _FlowItem::getTypeId
     *
     * @access public
     * @return integer
     */
    public function getTypeId() {
        if ($this->_Type instanceof FlowTypeItem) {
            return $this->_Type->getId();
        }
        return (int)$this->_Type;
    }

    /**
     * _FlowItem::setType
     *
     * @access public
     * @param object FlowTypeItem $value
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'FlowItem';
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
            'TotalHT' => Object::TYPE_DECIMAL,
            'TVA' => 'TVA',
            'Handing' => Object::TYPE_STRING,
            'Flow' => 'Flow',
            'Type' => 'FlowTypeItem');
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