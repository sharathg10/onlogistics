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
 * @version   SVN: $Id: ProductStyle.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class ProductStyle extends Object {
    /**
     * Initialize a new instance of Actor
     *
     * @access public
     */
    public function ProductStyle() {
        parent::__construct();
        $this->_ClassName = 'ProductStyle';
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
	 * @var string
     */
    private $_PressName = '';

    public function getPressName() {
        return $this->_PressName;
    }

    public function setPressName($value) {
        $this->_PressName = $value;
    }

	/**
     *
     * @access private
	 * @var string
     */
    private $_Description = '';

    public function getDescription() {
        return $this->_Description;
    }

    public function setDescription($value) {
        $this->_Description = $value;
    }

}
?>
