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
 * @version   SVN: $Id: SellingPricesGrid.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */


/**
 * PriceByCurrency grid for selling prices
 *
 */
class SellingPricesGrid extends GenericGrid
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
    // SellingPricesGrid::getMapping() {{{

    /**
     * Surchargée ici pour retourner un mapping spécifique.
     *
     * @access protected
     * @return array
     */
    protected function getMapping() {
        return array(
            'Product' => array(
                'label' => _('Product'),
                'shortlabel' => _('Product'),
                'usedby' => array('grid', 'searchform'),
                'required' => false,
                'inplace_edit' => false,
                'add_button' => false,
                'section' => '',
            ),
            'RecommendedPrice' => array(
                'label' => _('Recommended price'),
                'shortlabel' => _('Recommended price'),
                'usedby' => array('grid'),
                'required' => false,
                'inplace_edit' => false,
                'add_button' => false,
                'dec_num' => 2,
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
            'PricingZone' => array(
                'label' => _('Pricing zone'),
                'shortlabel' => _('Pricing zone'),
                'usedby' => array('grid', 'searchform'),
                'required' => false,
                'inplace_edit' => false,
                'add_button' => false,
                'section' => '',
            ),
        );
    }

    // }}}
    // SellingPricesGrid::getGridFilter() {{{

    /**
     * Surchargée ici pour n'afficher que les prix de vente.
     *
     * @access protected
     * @return array
     */
    protected function getGridFilter() {
        return new FilterComponent(
            new FilterRule('ActorProduct', FilterRule::OPERATOR_EQUALS, 0),
            FilterComponent::OPERATOR_AND,
            new FilterRule('Product', FilterRule::OPERATOR_GREATER_THAN, 0)
        );
    }

    // }}}
    // SellingPricesGrid::renderSearchFormProduct() {{{

    /**
     *
     * @access protected
     * @return array
     */
    protected function renderSearchFormProduct() {
        if (!in_array('readytowear', Preferences::get('TradeContext', array()))) {
            return false;
        }
        $this->searchForm->addElement('select', 'Product', 
            _('Product'),
            array(SearchTools::createArrayIDFromCollection(
                'RTWProduct',
                array(),
                MSG_SELECT_AN_ELEMENT,
                'BaseReference'
            )),
            array('Path' => 'Product@RTWProduct.Id')
        );
        $this->searchForm->addElement('select', 'Season', 
            _('Season'),
            array(SearchTools::createArrayIDFromCollection(
                'RTWSeason',
                array(),
                MSG_SELECT_AN_ELEMENT
            )),
            array('Path' => 'Product@RTWProduct.Model.Season.Id')
        );
        $this->searchForm->addElement('select', 'PressName', 
            _('Press name'),
            array(SearchTools::createArrayIDFromCollection(
                'RTWPressName',
                array(),
                MSG_SELECT_AN_ELEMENT
            )),
            array('Path' => 'Product@RTWProduct.Model.PressName.Id')
        );
    }

    // }}}
}

?>
