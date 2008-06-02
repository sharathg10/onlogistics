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

class ChainGrid extends GenericGrid {

    var $forAffectations = false;
    // ChainGrid::__construct() {{{

    /**
     * __construct 
     * 
     * @param array $params 
     * @access public
     * @return void
     */
    public function __construct($params=array()) {
        $session = Session::singleton();
        if (!isset($_SESSION['ProductId']) && isset($_REQUEST['p'])) {
            $session->register('ProductId', $_REQUEST['p'], 4);
            $this->forAffectations = true; 
            $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        }
        if (isset($_SESSION['ProductId']) && is_array($_SESSION['ProductId'])) {
            $session->prolong('ProductId', 1);
            $this->forAffectations = true; 
            $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        }
        parent::__construct($params);
    }

    // }}}
    // ChainGrid::additionalGridActions() {{{

    /**
     * additionalGridActions 
     * 
     * @access public
     * @return void
     */
    public function additionalGridActions() {
        if ($this->forAffectations) {
            // ajout de l'action affecter
            $this->grid->newAction('Redirect', array(
                    'Caption' => _('Assign'),
                    'Title' => _('Assign to chain'),
                    'Enabled' => true,
                    'TransmitedArrayName' => 'ChnId',
                    'URL' => 'AffectProductsToSelectedChain.php'
                )
            );
        } else {
            // action modéliser
            $this->grid->newAction('Redirect', array(
                    'Caption' => _('Build'),
                    'Title' => _('Chain operations and tasks'),
                    'URL' => 'ChainEdit.php?chnId=%d'
                )
            );
            // action copier
            $this->grid->newAction('Redirect', array(
                    'Caption' => _('Copy'),
                    'Title' => _('Copy chain'),
                    'URL' => 'ChainDuplicate.php?chnID=%d'
                )
            );
        }
    }

    // }}}
}
?>
