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
 * @version   SVN: $Id: SupplierPricesGrid.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */


/**
 * PriceByCurrency grid for selling prices
 *
 */
class SupplierPricesGrid extends GenericGrid
{
    // Constructeur {{{

    /**
     * Constructeur
     *
     * @param string $entity nom de l'objet
     * @param array $params tableau de paramètres
     * @return void
     */
    public function __construct($params=array()) {
        $params['title'] = _('Selling prices');
        parent::__construct($params);
    }
    
    // }}} 
    // SupplierPricesGrid::getMapping() {{{

    /**
     * Surchargée ici pour retourner un mapping spécifique.
     *
     * @access protected
     * @return array
     */
    protected function getMapping() {
        return array(
            'ActorProduct' => array(
                'label' => _('Product'),
                'shortlabel' => _('Product'),
                'usedby' => array('grid', 'searchform'),
                'required' => false,
                'inplace_edit' => false,
                'add_button' => false,
                'section' => '',
            ),
            'Price' => array(
                'label' => _('Price'),
                'shortlabel' => _('Price'),
                'usedby' => array('grid'),
                'required' => false,
                'inplace_edit' => false,
                'add_button' => false,
                'dec_num' => 2,
            ),
            'Currency' => array(
                'label' => _('Currency'),
                'shortlabel' => _('Currency'),
                'usedby' => array('grid', 'searchform'),
                'required' => false,
                'inplace_edit' => false,
                'add_button' => false,
                'section' => '',
            ),
        );
    }

    // }}}
    // SupplierPricesGrid::getGridFilter() {{{

    /**
     * Surchargée ici pour n'afficher que les prix de vente.
     *
     * @access protected
     * @return array
     */
    protected function getGridFilter() {
        return new FilterComponent(
            new FilterRule('ActorProduct', FilterRule::OPERATOR_GREATER_THAN, 0)
        );
    }

    // }}}
    // SupplierPricesGrid::renderSearchFormActorProduct() {{{

    /**
     *
     * @access protected
     * @return array
     */
    protected function renderSearchFormActorProduct() {
        $this->searchForm->addElement('select', 'Supplier', 
            _('Supplier'),
            array(SearchTools::createArrayIDFromCollection(
                array('Supplier', 'AeroSupplier'),
                array(),
                MSG_SELECT_AN_ELEMENT
            )),
            array('Path' => 'ActorProduct.Actor.Id')
        );
        $this->searchForm->addElement('select', 'Product2', 
            _('Product'),
            array(SearchTools::createArrayIDFromCollection(
                array('Product', 'AeroProduct', 'RTWMaterial'),
                array(),
                MSG_SELECT_AN_ELEMENT,
                'BaseReference'
            )),
            array('Path' => 'ActorProduct().Product.Id')
        );
    }

    // }}}
    // SupplierPricesGrid::renderColumnActorProduct() {{{

    /**
     *
     * @access protected
     * @return array
     */
    protected function renderColumnActorProduct() {
        $this->grid->newColumn('FieldMapper', _('Product'),
            array('Macro' => '%ActorProduct.Product.BaseReference%')
        );
        if (in_array('readytowear', Preferences::get('TradeContext', array()))) {
            $this->grid->newColumn('FieldMapper', _('Commercial designation'),
                array('Macro' => '%ActorProduct.Product.CommercialNameAndColor%'));
        }
        $this->grid->newColumn('FieldMapper', _('Supplier'),
            array('Macro' => '%ActorProduct.Actor.Name%')
        );
    }

    // }}}
}

?>
