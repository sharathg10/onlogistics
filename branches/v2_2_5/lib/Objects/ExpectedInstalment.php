<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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
 * @version   SVN: $Id: SiteAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * Instalment class
 *
 */
class ExpectedInstalment extends Instalment {
    // Constructeur {{{

    /**
     * Instalment::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // CommandTotal float property + getter/setter {{{

    /**
     * CommandTotal float property
     *
     * @access private
     * @var float
     */
    private $_CommandTotal = null;

    /**
     * CommandTotal::getCommandTotal
     *
     * @access public
     * @return float
     */
    public function getCommandTotal() {
        return $this->_CommandTotal;
    }

    /**
     * CommandTotal::setCommandTotal
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCommandTotal($value) {
        $this->_CommandTotal = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // PercentTotal float property + getter/setter {{{

    /**
     * PercentTotal float property
     *
     * @access private
     * @var float
     */
    private $_PercentTotal = null;

    /**
     * PercentTotal::getPercentTotal
     *
     * @access public
     * @return float
     */
    public function getPercentTotal() {
        return $this->_PercentTotal;
    }

    /**
     * PercentTotal::setPercentTotal
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPercentTotal($value) {
        $this->_PercentTotal = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
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
        return _('Expected Instalments');
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
            'DocumentNo' => Object::TYPE_STRING,
            'Instalment' => Object::TYPE_DECIMAL,
            'Currency' => 'Currency',
            'Command' => 'Command',
            'CommandTotal' => OBJECT::TYPE_DECIMAL,
            'PercentTotal' => OBJECT::TYPE_DECIMAL,
            'Command' => 'Command',
            'SupplierCustomer' => 'SupplierCustomer',
            'InstalmentDate' => Object::TYPE_DATETIME,
            'Cancelled' => Object::TYPE_BOOL,
            'CancellationDate' => Object::TYPE_DATETIME,
            'Modality' => Object::TYPE_CONST);
        return $return;
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
            'DocumentNo'=>array(
                'label'        => _('DocumentNo'),
                'shortlabel'   => _('DocumentNo'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Instalment'=>array(
                'label'        => _('Amount'),
                'shortlabel'   => _('Amount'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'Currency'=>array(
                'label'        => _('Currency'),
                'shortlabel'   => _('Currency'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Command'=>array(
                'label'        => _('Command'),
                'shortlabel'   => _('Command'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'CommandTotal'=>array(
                'label'        => _('Command Total TTC'),
                'shortlabel'   => _('Total TTC'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PercentTotal'=>array(
                'label'        => _('Percentage'),
                'shortlabel'   => _('Percentage'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'SupplierCustomer'=>array(
                'label'        => _('SupplierCustomer'),
                'shortlabel'   => _('SupplierCustomer'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'InstalmentDate'=>array(
                'label'        => _('Date'),
                'shortlabel'   => _('Date'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Modality'=>array(
                'label'        => _('Modality'),
                'shortlabel'   => _('Modality'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
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
