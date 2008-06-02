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

class ActorProductGrid extends GenericGrid {

    // ActorProductGrid::renderSearchFormActor() {{{
    public function renderSearchFormActor() {
        $this->searchForm->addElement('select', 'Actor',
            _('Customer'), array(
                SearchTools::createArrayIDFromCollection(
                    'Actor',
                    array('ClassName' => array('Customer', 'AeroCustomer'),
                          'Active' => 1),
                    MSG_SELECT_AN_ELEMENT)
            )
        );
    }
    // }}}

    // ActorProductGrid::renderSearchFormProduct() {{{
    public function renderSearchFormProduct() {
        $this->searchForm->addElement(
                'text', 'BaseReference', _('Reference'), array(),
                array('Path' => 'Product.BaseReference'));
        $this->searchForm->addElement(
                'text', 'ProductName', _('Designation'), array(),
                array('Path' => 'Product.Name'));
    }
    // }}}
    // ActorProductGrid::renderColumnProduct() {{{
    public function renderColumnProduct() {
        $this->grid->NewColumn('FieldMapper', _('Reference'), array('Macro'=>'%Product.BaseReference%'));
        $this->grid->NewColumn('FieldMapper', _('Designation'), array('Macro'=>'%Product.Name%'));
    }
    // }}}

    // ActorProductGrid::getGridFilter() {{{

    /**
     * On filtre les ActorProduct pour ne pas afficher les ref fournisseur
     * Utile dans le cas ou pas de Customer selectionne
     *
     * @access protected
     * @return mixed array of filters
     */
    protected function getGridFilter() {
        $return = array();
        $return[] = SearchTools::newFilterComponent('ActorClassName',
                    'Actor.ClassName', 'In', array('Customer', 'AeroCustomer'), 1);
        $return[] = SearchTools::newFilterComponent(
                    'ActorActive', 'Actor.Active', 'Equals', 1, 1);
        return $return;
    }
    // }}}
}

?>