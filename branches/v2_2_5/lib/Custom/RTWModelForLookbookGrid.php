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
 * @version   SVN: $Id: RTWModelGrid.php 52 2008-06-24 13:03:02Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class RTWModelForLookbookGrid extends GenericGrid
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
        $params['showPrintAction'] = false;
        parent::__construct($params);
    }
    
    // }}} 
    //  RTWModelForLookbookGrid::getMapping() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function getMapping() {
        return array(
            'Season'=>array(
                'label'        => _('Season'),
                'shortlabel'   => _('Season'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'StyleNumber'=>array(
                'label'        => _('Style number'),
                'shortlabel'   => _('Style number'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PressName'=>array(
                'label'        => _('Press name'),
                'shortlabel'   => _('Press name'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
        );
    }
    
    // }}} 
    // RTWModelForLookbookGrid::getFeatures() {{{

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
    // RTWModelForLookbookGrid::onAfterBuildSearchForm() {{{

    /**
     *
     * @access public
     */
    public function onAfterBuildSearchForm() {
        $zones = SearchTools::createArrayIDFromCollection('PricingZone');
        $this->searchForm->addElement('select', 'Zone', _('Pricing zone'),
            array($zones),
            array('Path' => 'RTWProduct().PriceByCurrency().PricingZone.Id')
        );
    }

    // }}}
    // RTWModelForLookbookGrid::additionalGridActions() {{{

    /**
     *
     * @access public
     */
    public function additionalGridActions() {
        $zone = isset($_REQUEST['Zone']) ? $_REQUEST['Zone'] : 0;
        $this->grid->NewAction('Redirect', array(
            'Caption' => _('Print lookbook'),
            'TargetPopup' => true,
            'URL' => 'LookbookEdit.php?zoneId='.$zone.'&retURL='.$_SERVER['PHP_SELF'],
            'TransmitedArrayName' => 'modelIDs'
        ));
    }

    // }}}
}

?>
