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
// pour les sessions
require_once('Objects/Nomenclature.php');
require_once('Objects/Product.php');
require_once('Objects/AeroProduct.php');
require_once('Objects/Component.php');
require_once('Objects/ComponentGroup.php');

$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

//Database::connection()->debug = true;
$pageTitle = _('Add or update nomenclature');

SearchTools::prolongDataInSession();

if (isset($_REQUEST['nomId']) && $_REQUEST['nomId'] > 0) {
	$Nomenclature = Object::load('Nomenclature', $_REQUEST['nomId']);
	if (Tools::isEmptyObject($Nomenclature)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'NomenclatureList.php');
        exit;
	}
}else if (isset($_SESSION['Nomenclature'])) {
    $Nomenclature = $_SESSION['Nomenclature'];
} else {
    $Nomenclature = new Nomenclature();
}
$session->register('Nomenclature', $Nomenclature, 2);

// Cleanage de session
$sname = SearchTools::getGridItemsSessionName();
unset($_SESSION[$sname], $_SESSION['ComponentGroup']);


// Oblige de faire ceci en 2 lignes, sinon, on recharge les donnees en base
$ComponentGroupCollection = $Nomenclature->getComponentGroupCollection();
$ComponentGroupCollection->sort('Name', SORT_ASC);

// Le grid des ComponentGroup
// Place ici pour le isPendingAction()
$grid = new Grid();

$grid->NewColumn('FieldMapper', _('Designation'),
        array('Macro' =>'<a href="ComponentGroupAddEdit.php?cgrId=%Id%&retURL='
        . $_SERVER['PHP_SELF'].'">%Name%</a>'));

$grid->NewAction('AddEdit', array('Action'=>'Add', 'EntityType' => 'ComponentGroup'));

$grid->NewAction('Delete', array('TransmitedArrayName'=>'cgrId',
        'EntityType'=>'ComponentGroup', 'ReturnURL' => basename($_SERVER['PHP_SELF'])));
$grid->displayCancelFilter = false;
$grid->withNoSortableColumn = true;

//  Si on a clique sur OK apres saisie  ou confirme la saisie
if (isset($_REQUEST['formSubmitted'])) {
	$errorUrl = basename($_SERVER['PHP_SELF']);
	// Modification ou Creation
	if (!isset($Nomenclature)) {
	    $Nomenclature = Object::load('Nomenclature');
	}

	$_REQUEST['Nomenclature_BeginDate'] = DateTimeTools::QuickFormDateToMySQL(
            'Nomenclature_BeginDate') . ' 00:00:00';
	$_REQUEST['Nomenclature_EndDate'] = DateTimeTools::QuickFormDateToMySQL(
            'Nomenclature_EndDate')	. ' 23:59:59';
	FormTools::autoHandlePostData($_REQUEST, $Nomenclature, 'Nomenclature');

	// Pour differencier le cas ou on a clique sur une action du grid
	if ($grid->isPendingAction()) {
        $res = $grid->dispatchAction($ComponentGroupCollection);
        if (Tools::isException($res)) {
            Template::errorDialog(E_ERROR_IN_EXEC . ': ' . $res->getMessage(),
                    'NomenclatureAddEdit.php');
            exit;
        }
    }

	Database::connection()->startTrans();

	// Verification des saisies
	$NomenclatureMapper = Mapper::singleton('Nomenclature');
	$testNomenclature = $NomenclatureMapper->load(
			array('Product' => $_REQUEST['Nomenclature_Product_ID'],
				  'Version' => $_REQUEST['Nomenclature_Version']));

	// Permet en cas de retour sur le form, de conserver les saisies
	$query = UrlTools::buildURLFromRequest(array('nomId', 'Nomenclature_Product_ID',
	       'Nomenclature_Version', 'Nomenclature_BeginDate', 'Nomenclature_EndDate'));
    $errorUrl .= '?' . $query;

	if ($testNomenclature instanceof Nomenclature
			&& $testNomenclature->getId() != $Nomenclature->getId()
            && !isset($_REQUEST['ok'])) {
	    Template::errorDialog(
		    _('A nomenclature with the same version number already exists for this product.'),
			basename($_SERVER['PHP_SELF']) . '?' . $query);
    	exit;
	}

	// Si ajout, on cree et sauve un Component de niveau 0 pour cette Nomenclature
	if ($Nomenclature->getId() == 0) {
        saveInstance($Nomenclature, $errorUrl);
	    $Component = new Component();
		$Component->setNomenclature($Nomenclature);
		$Component->setProduct($Nomenclature->getProduct());
		$Component->setQuantity(1);
		$Component->setLevel(0);
        saveInstance($Component, $errorUrl);
	}
    else {
        // Au cas ou on ait modifie l'attribut Product,
        // on modifie la FK Product ds le Component de Level 0
        $ComponentMapper = Mapper::singleton('Component');
        $Component = $ComponentMapper->load(
    			array('Nomenclature' => $Nomenclature->getId(), 'Level' => 0));

        if (!Tools::isEmptyObject($Component)) {
            $Component->setProduct($Nomenclature->getProductId());
            saveInstance($Component, $errorUrl);
        }

    }
    // Buildable indiquera si on peut ajouter des ConcreteComponent
    // via la Nomenclature pieces
    $tracingMode = Tools::getValueFromMacro($Nomenclature, '%Product.TracingMode%');
    if (in_array($tracingMode, array(0, Product::TRACINGMODE_LOT))) {
        $Nomenclature->setBuildable(0);
    }
    saveInstance($Nomenclature, $errorUrl);
	// On sauvegarde seulement maintenant les ComponentGroups
	$count = $ComponentGroupCollection->getCount();
    for($i=0; $i<$count; $i++){
        $ComponentGroup = $ComponentGroupCollection->getItem($i);
        $ComponentGroup->setNomenclature($Nomenclature);
        saveInstance($ComponentGroup, $errorUrl);
    }

	//  Commit de la transaction
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die('erreur sql');
    }
    Database::connection()->completeTrans();

	// Cleanage de session
    unset($_SESSION['Nomenclature']);
	Tools::redirectTo('NomenclatureList.php');
	exit;
}

/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('NomenclatureAddEdit', 'post');

// Si Edition d'une Nomenclature existant, qui n'a pas de ConcreteProduct associe
// On peut encore changer le Product associe
if (isset($Nomenclature) && !$Nomenclature->isUsed()) {
	$ProductArray = SearchTools::createArrayIDFromCollection(
            'Product', array(), '', 'BaseReference');
}
else {
	$Product = $Nomenclature->getProduct();
	$ProductArray = array($Product->getId() => $Product->getBaseReference());
}
$form->addElement('select', 'Nomenclature_Product_ID', _('Related product') . ' *',
        $ProductArray, 'style="width:100%"');
$form->addElement('text', 'Nomenclature_Version', _('Version') . ' *', 'style="width:100%"');

$dateFormat = array();
$dateFormat['format']  = I18N::getHTMLSelectDateFormat();
$dateFormat['minYear'] = date('Y');
$dateFormat['maxYear'] = date('Y') + 10;

$form->addElement('date', 'Nomenclature_BeginDate', _('Validity begin date'), $dateFormat);
$form->addElement('date', 'Nomenclature_EndDate', _('Validity end date'), $dateFormat);
$form->addElement('hidden', 'nomId', isset($_REQUEST['nomId'])?$_REQUEST['nomId']:0);

$ProductArray = array();
/*  Si Edition d'une Nomenclature existant  */
if (isset($Nomenclature)) {
	$defaultValues = FormTools::getDefaultValues($form, $Nomenclature);
	$BeginDate = DateTimeTools::DateExploder($Nomenclature->getBeginDate());
	$EndDate = DateTimeTools::DateExploder($Nomenclature->getEndDate());
	$defaultValues['BeginDate'] = array('d'=>$BeginDate['day'],
            'm'=>$BeginDate['month'], 'Y'=>$BeginDate['year']);
	$defaultValues['EndDate'] = array('d'=>$EndDate['day'],
            'm'=>$EndDate['month'], 'Y'=>$EndDate['year']);
}
else {
	$defaultValues = SearchTools::createDefaultValueArray();
	$defaultValues['BeginDate'] = array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y'));
	$defaultValues['EndDate'] = array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y'));
}
$form->setDefaults($defaultValues);  // Le form avec les valeurs par defaut

/*  Validation du formulaire */
$form->addRule('Nomenclature_Product_ID',
        _('Please select a product.'), 'required', '', 'client');
$form->addRule('Nomenclature_Version',
        _('Please provide a version.'), 'required', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());

$result = $grid->render($ComponentGroupCollection, false);
$smarty->assign('ComponentGroupGrid', $result);
$pageContent = $smarty->fetch('Nomenclature/NomenclatureAddEdit.html');

Template::page($pageTitle, $pageContent);

?>
