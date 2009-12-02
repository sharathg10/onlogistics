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

require_once 'ProductCommandTools.php';

class RTWProductForLabelPrintingGrid extends GenericGrid
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
        $params['profiles'] = array(
            UserAccount::PROFILE_ADMIN,
            UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
            UserAccount::PROFILE_RTW_SUPPLIER,
        );
        parent::__construct($params);
        $this->jsRequirements = array('js/lib-functions/ClientCatalog.js');
    }
    
    // }}} 
    // render() {{{
    public function render() {
        parent::render();
        if (!isset($_REQUEST['gridItems'])) {
            return;
        }
        $qties = array();
     	foreach ($_REQUEST['gridItems'] as $id) {
            if (isset($_REQUEST['qty_' . $id]) 
                && is_numeric($_REQUEST['qty_' . $id])
                && $_REQUEST['qty_' . $id] > 1)
            {
     	         $qties[$id] = $_REQUEST['qty_' . $id];
     	    }
     	}
     	$this->session->register('productQuantities', $qties, 2);
     }

    // }}} 
    //  RTWProductForLabelPrintingGrid::getMapping() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function getMapping() {
        return array(
            'BaseReference'=>array(
                'label'        => _('Reference'),
                'shortlabel'   => _('Reference'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'EAN13Code'=>array(
                'label'        => _('EAN13 Code'),
                'shortlabel'   => _('EAN13 Code'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
        );
    }
    
    // }}} 
    // RTWProductForLabelPrintingGrid::getFeatures() {{{

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
    // RTWProductForLabelPrintingGrid::getGridFilter() {{{

    /**
     *
     * @access public
     * @return array
     */
    public function getGridFilter() {
        if ($this->auth->getProfile() == UserAccount::PROFILE_RTW_SUPPLIER) {
            // on n'affiche que les produits qui ont comme fournisseur l'acteur
            // de l'utilisateur connecte
            return SearchTools::newFilterComponent(
                'Owner',
                'ActorProduct().Actor',
                'Equals',
                $this->auth->getActorId(),
                1,
                'RTWProduct'
            );
        }
        // sinon pas de filtre particulier
        return array();
    }

    // }}}
    // RTWProductForLabelPrintingGrid::additionalGridActions() {{{

    /**
     * Ajoute l'action imprimer etiquettes.
     *
     * @access protected
     * @return array
     */
    protected function additionalGridActions() {
        $this->grid->NewAction('Redirect', array(
            'Caption' => _('Print product labels'),
            'TargetPopup' => true,
            'URL' => 'ProductLabelEdit.php',
            'TransmitedArrayName' => 'pdtIds'
        ));
    }

    // }}}
    // RTWProductForLabelPrintingGrid::additionalGridColumns() {{{

    /**
     * Ajoute la colonne pour gerer la quantite
     *
     * @access protected
     * @return array
     */
    protected function additionalGridColumns() {
        $this->grid->NewColumn('FieldMapper', _('Quantity to print'), array(
            'Macro' => '<input name="qty_%Id%" type="text" style="width: 50px;" value="1"/>',
            'Sortable' => false,
        ));
    }

    // }}}
}

?>
