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

require_once('config.inc.php');
require_once('Objects/ActivatedMovement.php');
$auth = Auth::Singleton();

$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
            			   UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL,
                           UserAccount::PROFILE_DIR_COMMERCIAL));

if (!isset($_GET['CommandId'])) {
    Template::errorDialog(_('No order selected'), 'javascript:window.close();', BASE_POPUP_TEMPLATE);
   exit;
}
// Pour recuperer et afficher le CommandNo
$ProductCommand = Object::load('ProductCommand', $_GET['CommandId']);

$grid = new Grid();

$grid->paged = false;
$grid->withNoCheckBox = true;
$grid->displayCancelFilter = false;
$grid->withNoSortableColumn = true;

$grid->NewAction('Close');
$grid->NewColumn('ProductReference', _('Ordered ref.'),
        array('cmdType' => $ProductCommand->getType()));
$grid->NewColumn('FieldMapper', _('Designation'),
        array('Macro' => '%ProductCommandItem.Product.Name%'));
$grid->NewColumn('DeliveredReference', _('Delivered ref.'));
$grid->NewColumn('FieldMapper', _('Ordered qty'),
        array('Macro' => '%ProductCommandItem.quantity%'));
$grid->NewColumn('DeliveredQuantity', _('Delivered qty'));
$grid->NewColumn('CancelledQuantity', _('Returned qty'));
$grid->NewColumn('DeliveredQuantityRemaining', _('To deliver'));

$ActivatedMovementMapper = Mapper::singleton('ActivatedMovement');
$grid->setMapper($ActivatedMovementMapper);

$result = $grid->execute(array('ProductCommandItem.Command.Id' => $_GET['CommandId']),
						 array('ProductCommandItem.Product.BaseReference' => SORT_ASC));

Template::page(
    _('State of deliveries for order ') . $ProductCommand->getCommandNo() . ' ',
    $result,
    array(),
    array(),
    BASE_POPUP_TEMPLATE
);
        
?>
