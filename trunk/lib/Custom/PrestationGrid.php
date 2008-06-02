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

class PrestationGrid extends GenericGrid {
    // PrestationGrid::__construct() {{{

    /**
     * __construct
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params) {
        // on accède à d'autres grid à partir de celui ci, il faut forcer le
        // vidage des griditems cochés pour éviter les conflits.
        if(isset($_SESSION['ProductPrestationCost_griditems'])) {
            unset($_SESSION['ProductPrestationCost_griditems']);
        }
        if(isset($_SESSION['ConcreteProductPrestationCost_griditems'])) {
            unset($_SESSION['ConcreteProductPrestationCost_griditems']);
        }
        if(isset($_SESSION['FlyTypePrestationCost_griditems'])) {
            unset($_SESSION['FlyTypePrestationCost_griditems']);
        }
        if(isset($_SESSION['prestationID'])) {
            unset($_SESSION['prestationID']);
        }
        $params['showPrintAction'] = false;
        $params['showExportAction'] = false;
        parent::__construct($params);
        if(!$this->searchForm->displayGrid()) {
            $this->searchForm->setDefaultValues(array('Active'=>1));
        }

    }

    // }}}
    // PrestationGrid::render() {{{

    /**
     * render
     *
     * @access public
     * @return void
     */
    /*public function render() {
        if(!isset($_POST['Active']) && isset($_SESSION['Active'])) {
            unset($_SESSION['Active']);
        }
        parent::render();
    }*/

    // }}}
    // PrestationGrid::additionalGridActions() {{{

    /**
     * additionalGridActions 
     * 
     * @access public
     * @return void
     */
    public function additionalGridActions() {
        $this->grid->newAction('Redirect', array(
            'URL' => 'dispatcher.php?entity=ProductPrestationCost&amp;altname=PrestationCost&amp;prsId=%d',
            'Caption' => _('Price by product')
        ));
        $this->grid->newAction('Redirect', array(
            'URL' => 'dispatcher.php?entity=ConcreteProductPrestationCost&amp;altname=PrestationCost&amp;prsId=%d',
            'Caption' => _('Price by SN/Lot')
        ));
        $this->grid->newAction('Redirect', array(
            'URL' => 'dispatcher.php?entity=FlyTypePrestationCost&amp;altname=PrestationCost&amp;prsId=%d',
            'Caption' => _('Price by airplane')
        ));
    }

    // }}}
    // PrestationGrid::getGridSortOrder() {{{

    /**
     * getGridSortOrder
     *
     * @access protected
     * @return void
     */
    protected function getGridSortOrder() {
        return array('Name'=>SORT_ASC);
    }

    // }}}
    // PrestationGrid::getGridFilter() {{{

    /**
     * getGridFilter 
     * 
     * @access protected
     * @return void
     */
    protected function getGridFilter() {
        $return = array();
        if(isset($_POST['BaseReference'])) {
            $filter = SearchTools::newFilterComponent('BaseReference',
                'Product().BaseReference', 'Like', $_POST['BaseReference'],
                1, 'ProductPrestationCost');
            $prdPrsCostCol = Object::loadCollection('ProductPrestationCost',
                $filter);
            $ids = $prdPrsCostCol->getItemIds();
            if(count($ids)>0) {
                $return[] = SearchTools::newFilterComponent('BaseReference',
                    'PrestationCost().Id', 'In', $ids, 1, 'Prestation');
            }
        }
        return $return;
    }

    // }}}
    // PrestationGrid::renderSearchFormActive() {{{

    /**
     * renderSearchFormActive 
     * 
     * @access public
     * @return void
     */
    public function renderSearchFormActive() {
        $this->searchForm->addElement('select', 'PrestationCustomer', 
            _('Customer associated to the service'), array(
                SearchTools::createArrayIDFromCollection(
                'Actor',
                array('Generic'=>0, 'Active'=>1),
                MSG_SELECT_AN_ELEMENT
            )), array('Path'=>'PrestationCustomer().Actor.Id')
        );
        $this->searchForm->addElement('text', 'BaseReference', 
            _('Product reference associated to the service'), array(), 
            array('Disable'=>true));
        $this->searchForm->addElement('select', 'Active', _('Active'), array( 
            array(
                1=>_('yes'), 
                0=>_('no')
            ))
        );
    }

    // }}}
}

?>
