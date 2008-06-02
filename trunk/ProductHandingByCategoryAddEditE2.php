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
require_once('Objects/ProductHandingByCategory.php');

define('E_NO_ITEM', _('Discount was not found in the database'));
define('I_PAGE_TITLE', _('Add or update discount by actor category'));

// authentification
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
    					   UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES));

// Prolonger les données en session
SearchTools::ProlongDataInSession(1);

// url de retour
$retURL = isset($_REQUEST['retURL'])?
				$_REQUEST['retURL']:'ProductHandingByCategoryList.php';

// récupération de l'id de la remise
$phcID = isset($_REQUEST['phcID'])?((is_array($_REQUEST['phcID']))?
    $_REQUEST['phcID'][0]:$_REQUEST['phcID']):0;


// Enregistrement des ajouts/modifications en session
if(isset($_REQUEST['formSubmitted'])) {
    if($_POST['type']==ProductHandingByCategory::TYPE_AMOUNT && $_POST['currency']=='##') {
        Template::errorDialog(
            _('Please provide a currency for discounts as amount'), 
            $_SERVER['PHP_SELF']);
        exit;
    }
    SearchTools::InputDataInSession(1, '', false);
    Tools::redirectTo('ProductHandingByCategoryAddEditE3.php?retURL=' . 
        $_SERVER['PHP_SELF']);
}

$mapper = Mapper::singleton('ProductHandingByCategory');
if ($phcID > 0) {
    $phc = $mapper->load(array('Id' => $phcID));
    if (!($phc instanceof ProductHandingByCategory)) {
        Template::errorDialog(E_NO_ITEM, $retURL);
        exit;
    }
} else {
    $phc = new ProductHandingByCategory();
}

// construction du formulaire
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('HTML/QuickForm.php');

$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$form = new HTML_QuickForm('ProductHandingByCategoryAddEdit', 'post', 
    basename($_SERVER['PHP_SELF']));

$form->addElement('text', 'handing', _('Rate/amount'));
$form->addElement('select', 'type', _('type'),
    ProductHandingByCategory::getTypeConstArray());
$CurrencyArray = SearchTools::createArrayIDFromCollection('Currency', array(), _('Select a currency'));
$form->addElement('select', 'currency', _('Currency'), $CurrencyArray);

$smarty->assign('retURL', $retURL);
$smarty->assign('phcID', $phcID);

$catOptions = FormTools::writeOptionsFromObject(
			'Category', $phc->getCategoryCollectionIds(), array(), 
		    array('Name'=>SORT_ASC), 'getName', array('Name'));
		    
$smarty->assign('CategoryOptions', implode("\n", $catOptions));

$defaultValues = array();
$defaultValues['handing'] = $phc->getHanding();
$defaultValues['type'] = $phc->getType();
$defaultValues['currency'] = $phc->getCurrencyId();

$form->setDefaults($defaultValues);

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());

// affichage de la page
$content = $smarty->fetch('Product/ProductHandingByCategoryAddEdit.html');
$js = array('js/lib-functions/checkForm.js', 
    		'js/includes/ProductHandingByCategoryAddEdit.js');
Template::page(I_PAGE_TITLE, $content, $js);

?>
