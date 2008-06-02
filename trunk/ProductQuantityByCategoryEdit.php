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
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
//Database::connection()->debug = true;
$pageTitle = _('Minimum quantity for order of product "%s"');
$pageTitle .= _(' and category %s');
// Pour prolonger la session du formulaire de recherche de ProductList
SearchTools::ProlongDataInSession();

// Checks habituels
if (!isset($_REQUEST['pqcId'])) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'ProductList.php');
    exit;
}
$pqc = Object::load('ProductQuantityByCategory', $_REQUEST['pqcId']);
if (Tools::isEmptyObject($pqc)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'ProductList.php');
    exit;
}
$returnURL = 'ProductQuantityByCategoryList.php?pdtId=' . $pqc->getProductId();


/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_POST['formSubmitted'])) {
    Database::connection()->startTrans();
    $pqc = Object::load('ProductQuantityByCategory', $_REQUEST['pqcId']);
    $pqc->setMinimumQuantity($_REQUEST['MinimumQuantity']);
    $pqc->setMinimumQuantityType($_REQUEST['MinimumQuantityType']);
    saveInstance($pqc, 'ProductList');

    if (Database::connection()->hasFailedTrans()) {
        if (DEV_VERSION) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        }
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $_SERVER['PHP_SELF']);
        exit;
    }
    Database::connection()->completeTrans();
    Tools::redirectTo($returnURL);
}


// Formulaire
$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('pqcEdit', 'post', $_SERVER['PHP_SELF']);

$form->addElement('hidden', 'pqcId', $_REQUEST['pqcId']);
$form->addElement('text', 'MinimumQuantity',
        _('Minimum ordered quantity'), 'style="width:100%;"');
$form->addElement('select', 'MinimumQuantityType', _('Quantity type'),
        ProductQuantityByCategory::getMinimumQuantityTypeConstArray(),
        'style="width:100%;"');

$defaultValues = array('MinimumQuantity' => $pqc->getMinimumQuantity(),
                       'MinimumQuantityType' => $pqc->getMinimumQuantityType());
$form->setDefaults($defaultValues);
$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$smarty->assign('returnURL', $returnURL);


$pageContent = $smarty->fetch('ProductQuantityByCategory/ProductQuantityByCategoryEdit.html');

$js = array('js/lib-functions/checkForm.js',
            'js/includes/ProductQuantityByCategoryList.js');
$pageTitle = sprintf($pageTitle,
        Tools::getValueFromMacro($pqc, '%Product.BaseReference%'),
        Tools::getValueFromMacro($pqc, '%Category.Name%'));

Template::page($pageTitle, $pageContent, $js);
?>
