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

class ChainCommandItem extends CommandItem {
    
    // Constructeur {{{

    /**
     * ChainCommandItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Command foreignkey property + getter/setter {{{

    /**
     * Command foreignkey
     *
     * @access private
     * @var mixed object ChainCommand or integer
     */
    private $_Command = false;

    /**
     * ChainCommandItem::getCommand
     *
     * @access public
     * @return object ChainCommand
     */
    public function getCommand() {
        if (is_int($this->_Command) && $this->_Command > 0) {
            $mapper = Mapper::singleton('ChainCommand');
            $this->_Command = $mapper->load(
                array('Id'=>$this->_Command));
        }
        return $this->_Command;
    }

    /**
     * ChainCommandItem::getCommandId
     *
     * @access public
     * @return integer
     */
    public function getCommandId() {
        if ($this->_Command instanceof ChainCommand) {
            return $this->_Command->getId();
        }
        return (int)$this->_Command;
    }

    /**
     * ChainCommandItem::setCommand
     *
     * @access public
     * @param object ChainCommand $value
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
    // CoverType foreignkey property + getter/setter {{{

    /**
     * CoverType foreignkey
     *
     * @access private
     * @var mixed object CoverType or integer
     */
    private $_CoverType = false;

    /**
     * ChainCommandItem::getCoverType
     *
     * @access public
     * @return object CoverType
     */
    public function getCoverType() {
        if (is_int($this->_CoverType) && $this->_CoverType > 0) {
            $mapper = Mapper::singleton('CoverType');
            $this->_CoverType = $mapper->load(
                array('Id'=>$this->_CoverType));
        }
        return $this->_CoverType;
    }

    /**
     * ChainCommandItem::getCoverTypeId
     *
     * @access public
     * @return integer
     */
    public function getCoverTypeId() {
        if ($this->_CoverType instanceof CoverType) {
            return $this->_CoverType->getId();
        }
        return (int)$this->_CoverType;
    }

    /**
     * ChainCommandItem::setCoverType
     *
     * @access public
     * @param object CoverType $value
     * @return void
     */
    public function setCoverType($value) {
        if (is_numeric($value)) {
            $this->_CoverType = (int)$value;
        } else {
            $this->_CoverType = $value;
        }
    }

    // }}}
    // ProductType foreignkey property + getter/setter {{{

    /**
     * ProductType foreignkey
     *
     * @access private
     * @var mixed object ProductType or integer
     */
    private $_ProductType = false;

    /**
     * ChainCommandItem::getProductType
     *
     * @access public
     * @return object ProductType
     */
    public function getProductType() {
        if (is_int($this->_ProductType) && $this->_ProductType > 0) {
            $mapper = Mapper::singleton('ProductType');
            $this->_ProductType = $mapper->load(
                array('Id'=>$this->_ProductType));
        }
        return $this->_ProductType;
    }

    /**
     * ChainCommandItem::getProductTypeId
     *
     * @access public
     * @return integer
     */
    public function getProductTypeId() {
        if ($this->_ProductType instanceof ProductType) {
            return $this->_ProductType->getId();
        }
        return (int)$this->_ProductType;
    }

    /**
     * ChainCommandItem::setProductType
     *
     * @access public
     * @param object ProductType $value
     * @return void
     */
    public function setProductType($value) {
        if (is_numeric($value)) {
            $this->_ProductType = (int)$value;
        } else {
            $this->_ProductType = $value;
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
    public static function getProperties($ownOnly = false) {
        $return = array(
            'Command' => 'ChainCommand',
            'CoverType' => 'CoverType',
            'ProductType' => 'ProductType');
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
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
    public static function getLinks($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
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
        return array_merge(parent::getUniqueProperties(), $return);
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
        return array_merge(parent::getEmptyForDeleteProperties(), $return);
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
    public static function getMapping($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
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
    // getParentClassName() {{{

    /**
     * Retourne le nom de la première classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'CommandItem';
    }

    // }}}
}

?>