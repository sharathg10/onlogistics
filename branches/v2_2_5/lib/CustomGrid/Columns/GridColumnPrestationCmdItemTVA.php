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

class GridColumnPrestationCmdItemTVA extends AbstractGridColumn {
    /**
     * Constructor
     *
     * @access protected
     */
    public function __construct($title='', $params=array()) {
        parent::__construct($title, $params);
        if (isset($params['spc'])) {
            $this->_spc = $params['spc'];
        }
    }
    /**
     * Permet de recuperer le taux de tva tenant compte de la surtaxe si necessaire
     *
     * @var string $_method
     **/
    var $_spc = 0;

    public function render($object) {
        $return = 0;
        if ($this->_spc instanceof SupplierCustomer) {
            // Gestion de la tva surtaxee
            $hasTvaSurtax = $this->_spc->getHasTvaSurtax();
            $tvaSurtaxRate = ($hasTvaSurtax)?Preferences::get('TvaSurtax', 0):0;
            $tva = $object->getTva();
            if ($tva instanceof TVA) {
                $return = $tva->getRealTvaRate($tvaSurtaxRate);
            }
        }
        return sprintf('<input name="HiddenTVA[]" value="%s" type="hidden">%s', 
                $return, 
                I18N::formatNumber($return));
    }
}
?>