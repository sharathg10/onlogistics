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
require_once('Objects/Promotion.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$query = (isset($_REQUEST['prmId']))?'?prmId='.$_REQUEST['prmId']:'';
$dateError = _('Wrong date range provided.');

// Si on a clique sur 'Restreindre à des produits': on met les saisies
// en session, et on redirige
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 0)) {
	SearchTools::inputDataInSession(0, '', false);  // met les saisies en session
	$StartDate = DateTimeTools::quickFormDateToMySQL('StartDate') . ' 00:00:00';
	$EndDate = DateTimeTools::quickFormDateToMySQL('EndDate') . ' 00:00:00';
	if ($StartDate > $EndDate) {
	    Template::errorDialog($dateError, $_SERVER['PHP_SELF'] . $query);
   		exit;
	}
	Tools::redirectTo('PromotionProductAdd.php' . $query);
}

/*  Si on a clique sur OK apres le choix des Product,
*   oubien sur OK apres saisie des donnees de la promo (pas de ref supplemtaire)  */
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1)
    || (isset($_REQUEST['addRef']) && $_REQUEST['addRef']==1)) {
	SearchTools::inputDataInSession(1, '', false);  // met les saisies en session
	if (isset($_REQUEST['prmId'])) {
	    $Promotion = Object::load('Promotion', $_REQUEST['prmId']);
		if (Tools::isEmptyObject($Promotion)) {
		    Template::errorDialog(E_MSG_TRY_AGAIN, 'PromotionList.php');
    		exit;
		}
	}
	else {
		$Promotion = Object::load('Promotion');  // si ajout de Promotion
	}

	$Var = (isset($_POST['StartDate']))?'_REQUEST':'_SESSION';  // var dynamique
	$StartDate = DateTimeTools::quickFormDateToMySQL('StartDate') . ' 00:00:00';
	$EndDate = DateTimeTools::quickFormDateToMySQL('EndDate') . ' 00:00:00';
	$Promotion->setStartDate($StartDate);
	$Promotion->setEndDate($EndDate);
	$Promotion->setName(${$Var}['PromoName']);
	$Promotion->setRate(${$Var}['PromoRate']);
	$Promotion->setCurrency(${$Var}['Currency']);
	$Promotion->setType(${$Var}['PromoType']);
	$Promotion->setApproImpactRate(${$Var}['PromoApproImpactRate']);

	if (SearchTools::requestOrSessionExist('Category')
    && !in_array('##', ${$Var}['Category'])) {
	    $Promotion->setCategoryCollectionIds(${$Var}['Category']);
	}


	/*  Si on vient de selectionner des Product:
	* Recup de la var de session liee au grid de PromotionProductAdd.php
 	* On n'utilise pas les id passes ds l'url, car ça peut depasser 255 caracteres!!  */
	if (isset($_REQUEST['addRef'])) {
        $itemsVarName = SearchTools::getGridItemsSessionName('PromotionProductAdd');
		if (!empty($_SESSION[$itemsVarName])) {
		    $Promotion->setProductCollectionIds($_SESSION[$itemsVarName]);
		}
    }

    saveInstance($Promotion, $_SERVER['PHP_SELF'] . $query);
	Tools::redirectTo('PromotionList.php');
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('PromotionAddEdit', 'post', $_SERVER['PHP_SELF'].$query);
// PromoName plutot que Name car interferences avec le form de PromotionProductAdd.php
$form->addElement('text', 'PromoName', _('Name'));
$form->addElement('text', 'PromoRate', _('Rate/amount'));
$CurrencyArray = SearchTools::createArrayIDFromCollection('Currency', array());
$form->addElement('select', 'Currency', _('Currency'), $CurrencyArray);
$form->addElement('select', 'PromoType', '', Promotion::getTypeConstArray());

$form->addElement('date', 'StartDate', '',
        array('format'    => I18N::getHTMLSelectDateFormat(),
              'minYear'   => date('Y'),
              'maxYear'   => date('Y')+1));
$form->addElement('date', 'EndDate', '',
        array('format'    => I18N::getHTMLSelectDateFormat(),
              'minYear'   => date('Y'),
              'maxYear'   => date('Y')+1));
$CategoryArray = SearchTools::createArrayIDFromCollection(
        'Category', array(), _('All customer categories'));
$form->addElement('select', 'Category',  _('Targeted customers'), $CategoryArray,
        'multiple size="5"');
$form->addElement('text', 'PromoApproImpactRate', _('Supplying impacts (%)'));
$form->addElement('submit', 'AddRef', _('Restrict to products'),
		'onclick="return validate_PromotionAddEdit(PromotionAddEdit);"');

/*  Si Edition d'une Promotion existante et on ne vient pas de restreindre a des Product  */
if (isset($_REQUEST['prmId']) && !isset($_REQUEST['addRef'])) {
	$Promotion = Object::load('Promotion', $_REQUEST['prmId']);
	if (Tools::isEmptyObject($Promotion)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'PromotionList.php');
   		exit;
	}
    foreach($Promotion->getProperties() as $name => $class) {
	    $getter = 'get' . $name;
	    $val = $Promotion->$getter();
		$defaultValues['Promo' . $name] = $Promotion->$getter();
	}

	$defaultValues['Category'] = $Promotion->getCategoryCollectionIds();
	$defaultValues['Currency'] = $Promotion->getCurrencyId();

	$StartDate = DateTimeTools::DateExploder($Promotion->getStartDate());
	$EndDate = DateTimeTools::DateExploder($Promotion->getEndDate());
	$defaultValues['StartDate'] = array(
        'd'=>$StartDate['day'], 'm'=>$StartDate['month'], 'Y'=>$StartDate['year']);
	$defaultValues['EndDate'] = array(
        'd'=>$EndDate['day'], 'm'=>$EndDate['month'], 'Y'=>$EndDate['year']);
}
else {
	$defaultValues = array(
            'EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
			'StartDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')));
}

$defaultValues2 = SearchTools::dataInSessionToDisplay();
$defaultValues = array_merge($defaultValues, $defaultValues2);
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut


/*  Validation du formulaire: chps ProductType, Rate obligatoires  */
$form->addRule('PromoName', _('Please provide a name.'), 'required', '', 'client');
$form->addRule('PromoRate', _('Please provide a rate/amount.'), 'required', '', 'client');
$form->addRule('PromoType', _('Please provide the offer on sale type.'), 'numeric', '', 'client');
$form->addRule('PromoApproImpactRate',
        _('Please provide a name for supplying impact.'),
        'numeric', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM . ': ',_('Please correct.'));

$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('PromotionAddEdit.html');

$action = (isset($_REQUEST['prmId']))?_('Update'):_('Create');
Template::page($action . ' ' . _('of an offer on sale'), $pageContent);

?>
