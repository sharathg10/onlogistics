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

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
//Database::connection()->debug = true;

SearchTools::ProlongDataInSession();

$returnURL = 'ConcreteComponentsTree.php?cpId=' . $_REQUEST['cpId'];

// Le ConcreteProduct head
$headCP = Object::load('ConcreteProduct', $_REQUEST['cpId']);
if (Tools::isEmptyObject($headCP)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
    exit;
}
$Component = Object::load('Component', $_REQUEST['cmpId']);

$ParentComponent = $Component->getParent();
$CPCollection = $ParentComponent->getConcreteProducts($_REQUEST['cpId']);
// Le Component parent n'a pas de piece associee
if (Tools::isEmptyObject($CPCollection)) {
    Template::errorDialog(_('Error: parent component has no associated part.') ,
                   $returnURL);
    exit;
}
elseif ($CPCollection->getCount() == 1) {
    $ids = $CPCollection->getItemIds();
    $parentId = $ids[0];
    $query = 'cpId=' . $_REQUEST['cpId']. '&cmpId=' . $_REQUEST['cmpId']
            . '&parId=' . $parentId;
    Tools::redirectTo('ComponentConcreteProduct.php?' . $query);
    exit;
}
else {
    $filter = array('Id' => $CPCollection->getItemIds());
    $cpArray = SearchTools::CreateArrayIDFromCollection(
            'ConcreteProduct', $filter, '', 'SerialNumber');
}


/* Plusieurs ConcreteProduct peuvent etre parent du ConcreteProduct
 * lie au Component selectionne
 * Il faut que l'utilisateur choisisse un de ces ConcreteProduct.
**/
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('selectCP', 'post',
                           'ComponentConcreteProduct.php');

$form->addElement('select', 'parId', '', $cpArray, 'onChange="selectCP.submit()"');
$form->addElement('hidden', 'cpId', $_REQUEST['cpId']);
$form->addElement('hidden', 'cmpId', $_REQUEST['cmpId']);

Template::page(_('Please select a SN for the parent.'), $form->toHtml());

?>
