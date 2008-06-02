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

class StockAccountingQuantity extends Object {

    /**
     * StockAccountingQuantity::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }


    /**
     * StoreName string property
     *
     * @access private
     * @var string
     */
    private $_StoreName = '';

    /**
     * StockAccountingQuantity::getPrestationName
     *
     * @access public
     * @return string
     */
    public function getStoreName() {
        return $this->_StoreName;
    }

    /**
     * StockAccountingQuantity::setStoreName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStoreName($value) {
        $this->_StoreName = $value;
    }

    /**
     * PropertyValue string property
     *
     * @access private
     * @var string
     */
    private $_PropertyValue = '';

    /**
     * StockAccountingQuantity::getPrestationName
     *
     * @access public
     * @return string
     */
    public function getPropertyValue() {
        return $this->_PropertyValue;
    }

    /**
     * StockAccountingQuantity::setPropertyValue
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPropertyValue($value) {
        $this->_PropertyValue = $value;
    }

    /**
     *
     * @access private
	 * @var float
     */
    private $_Quantity = 0;

    /**
     * StockAccountingQuantity::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * StockAccountingQuantity::setQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantity($value) {
        $this->_Quantity = $value;
    }

    /**
     *
     * @access private
	 * @var float
     */
    private $_EntryQuantity = 0;

    /**
     * StockAccountingQuantity::getEntryQuantity
     *
     * @access public
     * @return float
     */
    public function getEntryQuantity() {
        return $this->_EntryQuantity;
    }

    /**
     * StockAccountingQuantity::setEntryQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setEntryQuantity($value) {
        $this->_EntryQuantity = $value;
    }

    /**
     *
     * @access private
	 * @var float
     */
    private $_ExitQuantity = 0;

    /**
     * StockAccountingQuantity::getExitQuantity
     *
     * @access public
     * @return float
     */
    public function getExitQuantity() {
        return $this->_ExitQuantity;
    }

    /**
     * StockAccountingQuantity::setExitQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setExitQuantity($value) {
        $this->_ExitQuantity = $value;
    }

    /**
     * Retourne le nom de la table correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return null;
    }

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
            'StoreName' => Object::TYPE_STRING,
            'PropertyValue' => Object::TYPE_STRING,
            'Quantity' => Object::TYPE_DECIMAL,
            'EntryQuantity' => Object::TYPE_DECIMAL,
            'ExitQuantity' => Object::TYPE_DECIMAL);
        return $return;
    }

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

}

?>