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
 * @version   SVN: $Id: SupplierDelayStock.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');

if (isset($_REQUEST['UserAccount_CommissionPercent'])) {
    foreach ($_REQUEST['UserAccount_CommissionPercent'] as $id => $percent) {
        $uac = Object::load('UserAccount', $id);
        $percent = I18N::extractNumber($percent);
        $percent = str_replace('%', '', $percent);
        if ($uac instanceof UserAccount && $uac->getCommissionPercent() != $percent) {
            if (is_numeric($percent)) {
                $uac->setCommissionPercent($percent);
            } else if (empty($percent)) {
                $uac->setCommissionPercent(0);
            } else {
                continue;
            }
            $uac->save();
        }
    }
}

$grid = new Grid();
$grid->withNoCheckBox = true;
$grid->displayCancelFilter = false;
$grid->paged = false;
$grid->withNoSortableColumn = true;
$grid->itemPerPage = 200;

$grid->newAction('Submit');
$grid->newAction('Cancel');

$grid->newColumn('FieldMapper', _('Salesman'), array(
    'Macro' => '%Identity%'
));
$grid->newColumn('FieldMapper', _('Commission percent by invoice'), array(
    'Macro' => '<input type="text" name="UserAccount_CommissionPercent[%Id%]" '
             . 'size="10" value="%CommissionPercent%" >'
));

Template::pageWithGrid($grid, 'UserAccount', '',
	array('Profile' => UserAccount::PROFILE_COMMERCIAL),
	array('Identity' => SORT_ASC)
);

?>
