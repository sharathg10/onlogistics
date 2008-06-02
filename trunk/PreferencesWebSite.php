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
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('HTML/QuickForm.php');

$auth = Auth::singleton();
$auth->checkProfiles();

if (isset($_POST['formSubmitted'])) {
    Preferences::set('WSActorGenericActor',
        (int)$_POST['Preference_WSActorGenericActor_ID']);
    Preferences::set('WSActorCommercial',
        (int)$_POST['Preference_WSActorCommercial_ID']);
    Preferences::set('WSActorCategory',
        (int)$_POST['Preference_WSActorCategory_ID']);
    Preferences::set('WSActorAccountingType',
        (int)$_POST['Preference_WSActorAccountingType_ID']);
    Preferences::save();
    Template::infoDialog(_('Preferences were successfully saved.'),
        $_SERVER['PHP_SELF']);
    exit(0);
}

$smarty = new Template();

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('Preference', 'post');

// actor.GenericActor
$genericActorArray = SearchTools::createArrayIDFromCollection('Actor',
    array('Generic'=>1), MSG_SELECT_AN_ELEMENT);
$form->addElement('select', 'Preference_WSActorGenericActor_ID',
    _('Generic actor'), $genericActorArray, 'style="width:100%;"');
// actor.commercial
$commercialArray = SearchTools::createArrayIDFromCollection('UserAccount',
    array('Profile'=>UserAccount::PROFILE_COMMERCIAL), MSG_SELECT_AN_ELEMENT);
$form->addElement('select', 'Preference_WSActorCommercial_ID',
    _('Salesman'), $commercialArray, 'style="width:100%;"');
// actor.category
$categoryArray = SearchTools::createArrayIDFromCollection('Category',
    array(), MSG_SELECT_AN_ELEMENT, 'Name');
$form->addElement('select', 'Preference_WSActorCategory_ID', _('Category'),
    $categoryArray, 'style="width:100%;"');
// actor.accountingType
$accTypeArray = SearchTools::createArrayIDFromCollection('AccountingType',
    array(), MSG_SELECT_AN_ELEMENT, 'Type');
$form->addElement('select', 'Preference_WSActorAccountingType_ID',
    _('Accounting model'), $accTypeArray, 'style="width:100%;"');

$defaultValues = array(
    'Preference_WSActorGenericActor_ID'=>Preferences::get('WSActorGenericActor', 0),
    'Preference_WSActorCommercial_ID'=>Preferences::get('WSActorCommercial', 0),
    'Preference_WSActorCategory_ID'=>Preferences::get('WSActorCategory', 0),
    'Preference_WSActorAccountingType_ID'=>Preferences::get('WSActorAccountingType', 0)
);
$form->setDefaults($defaultValues);

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());

Template::page('', $smarty->fetch('Preferences/PreferencesWebSite.html'));

?>
