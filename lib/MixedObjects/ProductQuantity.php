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

class ProductQuantity extends Object {
    /**
     * Initialize a new instance of Actor
     *
     * @access public
     */
    public function ProductQuantity() {
        parent::__construct();
        $this->_ClassName = 'ProductQuantity';
    }

    /**
     *
     * @access private
     * @var string
     */
    private $_BaseReference = "";

    /**
     *
     * @param string $value The new value
     * @access public
     */
    public function setBaseReference($value) {
        $this->_BaseReference = $value;
    }

    /**
     *
     * @return string Content of value
     * @access public
     */
    public function getBaseReference() {
        return $this->_BaseReference;
    }

	/**
     *
     * @access private
	 * @var integer
     */
    private $_Quantity = 0;

    public function getQuantity() {
        return $this->_Quantity;
    }

    public function setQuantity($value) {
        $this->_Quantity = $value;
    }

	/**
     *
     * @access private
	 * @var string
     */
    private $_Name = '';

    public function getName() {
        return $this->_Name;
    }

    public function setName($value) {
        $this->_Name = $value;
    }

	/**
     *
     * @access private
	 * @var integer
     */
    private $_VirtualQuantity = 0;

    public function getVirtualQuantity() {
        return $this->_VirtualQuantity;
    }

    public function setVirtualQuantity($value) {
        $this->_VirtualQuantity = $value;
    }

	/**
     *
     * @access private
	 * @var integer
     */
    private $_MiniQuantity = 0;

    public function getMiniQuantity() {
        return $this->_MiniQuantity;
    }

    public function setMiniQuantity($value) {
        $this->_MiniQuantity = $value;
    }

	/**
     *
     * @access private
	 * @var integer
     */
    private $_ACMEntryQuantity = 0;

    public function getACMEntryQuantity() {
        return $this->_ACMEntryQuantity;
    }

    public function setACMEntryQuantity($value) {
        if ($value != null) {
            $this->_ACMEntryQuantity = $value;
        }
    }

	/**
     *
     * @access private
	 * @var integer
     */
    private $_ACMExitQuantity = 0;

    public function getACMExitQuantity() {
        return $this->_ACMExitQuantity;
    }

    public function setACMExitQuantity($value) {
        if ($value != null) {
            $this->_ACMExitQuantity = $value;
        }
    }

	/**
     *
     * @access private
	 * @var integer
     */
    private $_Category = 0;

    public function getCategory() {
        return $this->_Category;
    }

    public function setCategory($value) {
        $this->_Category = $value;
    }

	/**
     *
     * @access private
	 * @var string
     */
    private $_ProductType = '';

    public function getProductType() {
        return $this->_ProductType;
    }

    public function setProductType($value) {
        $this->_ProductType = $value;
    }

    /**
     *
     * @access private
     * @var string
     */
    private $_SellUnitTypeShortName = "";

    /**
     *
     * @param string $value The new value
     * @access public
     */
    public function setSellUnitTypeShortName($value) {
        $this->_SellUnitTypeShortName = $value;
    }

    /**
     *
     * @return string Content of value
     * @access public
     */
    public function getSellUnitTypeShortName() {
        return $this->_SellUnitTypeShortName;
    }
}
?>
