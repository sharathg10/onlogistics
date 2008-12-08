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
 * @version   SVN: $Id: RTWModelGrid.php 71 2008-07-07 09:03:06Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once 'Custom/RTWModelGrid.php';

class RTWModelForSupplierGrid extends RTWModelGrid
{
    //  RTWModelForSupplierGrid::getMapping() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function getMapping() {
        return array(
            'Order' => array(
                'label'        => _('Order'),
                'shortlabel'   => _('Order'),
                'usedby'       => array('searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'StyleNumber' => array(
                'label'        => _('Style number'),
                'shortlabel'   => _('Style number'),
                'usedby'       => array('grid'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PressName'=>array(
                'label'        => _('Press name'),
                'shortlabel'   => _('Press name'),
                'usedby'       => array('grid'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Material1' => array(
                'label'        => _('Material 1'),
                'shortlabel'   => _('Material 1'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
            ),
            'Material2' => array(
                'label'        => _('Material 2'),
                'shortlabel'   => _('Material 2'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
            ),
            'Material3' => array(
                'label'        => _('Material 3'),
                'shortlabel'   => _('Material 3'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
            ),
            'Accessory1' => array(
                'label'        => _('Accessory 1'),
                'shortlabel'   => _('Accessory 1'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
            ),
            'Accessory2' => array(
                'label'        => _('Accessory 2'),
                'shortlabel'   => _('Accessory 2'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
            ),
            'Accessory3' => array(
                'label'        => _('Accessory 3'),
                'shortlabel'   => _('Accessory 3'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
            ),
        );
    }
    
    // }}} 
    // RTWModelForSupplierGrid::getFeatures() {{{

    /**
     * Retourne le tableau des "fonctionalités" pour l'objet en cours.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public function getFeatures() {
        return array(self::FEATURE_GRID, self::FEATURE_SEARCHFORM);
    }

    // }}}
    // RTWModelForSupplierGrid::renderSearchFormCustomer() {{{

    /**
     * Render custom du customer
     *
     * @access protected
     * @return void
     */
    protected function renderSearchFormOrder()
    {
        $filter = array('Type' => Command::TYPE_SUPPLIER);

        if ($this->auth->getProfile() == UserAccount::PROFILE_RTW_SUPPLIER) {
            $filter['Expeditor'] = $this->auth->getActorId();
        }

        $orders = SearchTools::createArrayIDFromCollection(
            'ProductCommand',
            $filter,
            MSG_SELECT_AN_ELEMENT,
            'CommandNo',
            array('CommandNo' => SORT_ASC)
        );

        $this->searchForm->addElement('select', 'CustomerSelected', _('Order'),
            array($orders),
            array('Path' => 'RTWProduct().ProductCommandItem().Command.Id')
        );
    }

    // }}}
    // RTWModelForSupplierGrid::additionalGridActions() {{{

    /**
     * Ajoute l'action imprimer fiche technique.
     *
     * @access protected
     * @return array
     */
    protected function additionalGridActions() {
        $this->grid->NewAction('Redirect', array(
            'Caption' => _('Print worksheet'),
            'TargetPopup' => true,
            'URL' => 'WorksheetEdit.php?retURL='.$_SERVER['PHP_SELF'],
            'TransmitedArrayName' => 'modelIDs'
        ));
    }

    // }}}
}

?>
