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

class PrestationCostGrid extends GenericGrid {
    // PrestationCostGrid::__construct() {{{
    
    /**
     * __construct 
     * 
     * @param array $params 
     * @access public
     * @return void
     */
    public function __construct($params) {
        /* une prestation ne peut avoir qu'un seul type de PrestationCost, si il 
         * y en à déjà un autre on affiche un message d'erreur à la place du 
         * grid.
         */
        $types = array(
            'ProductPrestationCost'         => _('products'), 
            'ConcreteProductPrestationCost' => _('SN/Lot'), 
            'FlyTypePrestationCost'         => _('Airplane types')
        );
        foreach($types as $clsname=>$msg) {
            if($params['clsname']==$clsname) {
                continue;
            }
            $pc = Object::loadCollection($clsname, array('Prestation'=>$_GET['prsId']),
                array(), array('Id'));
            if($pc->getCount()>0) {
                Template::errorDialog(sprintf(
                    _('Some prices by %s are already defined for this service.'), 
                    $msg), 'dispatcher.php?entity=Prestation');
                exit();
            }
        }
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES);
        $params['showExportAction'] = false;
        $params['showPrintAction'] = false;
        SearchTools::prolongDataInSession();
        parent::__construct($params);
        $prs = Object::load('Prestation', $_GET['prsId']);
        $this->title = sprintf(
            _('List of prices by %s of service'), $types[$params['clsname']]) .
            ' ' . $prs->getName();
        $this->grid->withNoSortableColumn = true;
    }

    // }}}
    // PrestationCostGrid::getGridFilter() {{{

    /**
     * getGridFilter 
     * 
     * @access public
     * @return void
     */
    protected function getGridFilter() {
        return array('Prestation' => $_GET['prsId']);
    }

    // }}}
    // PrestationCostGrid::additionalGridActions() {{{

    /**
     * additionalGridColumns 
     * 
     * @access public
     * @return void
     */
    public function additionalGridColumns() {
        if($this->clsname == 'ProductPrestationCost') {
            $this->nbSubGridColumns++;
            $this->grid->newColumn('GenericSubGrid', array(_('Reference')), 
                array('Macro'=>'%BaseReference%', 'link'=>'Product'));
        } elseif($this->clsname == 'ConcreteProductPrestationCost') {
            $this->nbSubGridColumns++;
            $this->grid->newColumn('GenericSubGrid', array(_('Reference')), 
                array('Macro'=>'%Product.BaseReference%', 
                'link'=>'ConcreteProduct'));
        }
    }

    // }}}
    // PrestationCostGrid::createGridActions() {{{
    
    /**
     * createGridActions 
     * 
     * @access public
     * @return void
     */
    protected function createGridActions() {
        $this->grid->newAction('AddEdit', array(
            'Action' => 'Add',
            'URL' => 'dispatcher.php?action=add&amp;entity=' . 
                    $this->clsname . '&amp;altname=PrestationCost&amp;prsId=' .
                    $_GET['prsId'],
            'Caption' => 'Ajouter')
        );
        $this->grid->newAction('Redirect', array(
            'URL' => 'dispatcher.php?action=edit&amp;entity=' . 
                $this->clsname . '&amp;altname=PrestationCost' .
                '&amp;objID=%d&amp;prsId=' . $_GET['prsId'],
            'Caption' => 'Editer')
        );
        $this->grid->newAction('Redirect', array(
            'URL' => 'dispatcher.php?action=del&amp;entity=' . 
                $this->clsname . '&amp;altname=PrestationCost&amp;prsId=' .
                $_GET['prsId'],
            'Caption' => 'Supprimer',
            'TransmitedArrayName' => 'objID')
        );
        $this->grid->newAction('Redirect', array(
            'AllowEmptySelection' => true,
            'URL' => 'dispatcher.php?action=grid&amp;entity=Prestation',
            'Caption' => 'Retour')
        );
    }

    // }}}
}

?>
