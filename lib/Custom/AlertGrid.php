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

class AlertGrid extends GenericGrid {
    // AlertGrid::__construct() {{{

    /**
     * __construct
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params) {
        // il faut nettoyer les eventuelles cases qui restent cochées
        unset($_SESSION['Alert_griditems']);

        $params['itemsperpage'] = 50;
        parent::__construct($params);
        if(isset($_REQUEST['uacId'])) {
            // il faut nettoyer les eventuelles cases qui restent cochées
            unset($_SESSION['Alert_griditems']);
            $uac = Object::load('UserAccount', $_REQUEST['uacId']);
            if(Tools::isException($uac, 'UserAccount')) {
                Template::errorDialog($uac->getMessage(), 'dispatcher.php?entity=UserAccount');
                exit();
            }
	        $this->grid->NewAction(
		        'Redirect',
		        array(
			        'Caption'=>_('Assign'),
			        'Title'=>_('Update alert settings for user'),
                    'AllowEmptySelection'=>true,
			        'TransmitedArrayName'=>'alertIds',
	                'URL'=>'AffectAlertToUserAccount.php?uacId=' . $uac->getId()
		        )
            );
            $this->grid->NewAction(
		        'Redirect',
		        array(
			        'Caption'=>A_CANCEL,
		            'URL'=>'dispatcher.php?entity=UserAccount'
		        )
	        );
            if (!$this->grid->isPendingAction()) {
                $this->grid->setPreselectedItems($uac->getAlertCollectionIds());
            }
        }
    }

    // }}}
}

?>
