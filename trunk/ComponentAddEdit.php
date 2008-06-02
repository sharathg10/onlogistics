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

$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
//Database::connection()->debug = true;

$pageTitle = _('Add or update component');
$errorLevelTwoCanNotExist =
    _('Error: this nomenclature cannot have a component with a level superior to 1.');
$returnURL = (isset($_REQUEST['returnURL']))?
        $_REQUEST['returnURL']:$_SESSION['arboURL'];

SearchTools::prolongDataInSession();

if (isset($_REQUEST['cmpId']) && $_REQUEST['cmpId'] > 0) {
    $Component = Object::load('Component', $_REQUEST['cmpId']);
    if (Tools::isEmptyObject($Component)) {
        Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
        exit;
    }
}else {
    $Component = Object::load('Component');
    $action = 'add';
}
$cmpId = (isset($_REQUEST['cmpId']))?$_REQUEST['cmpId']:0;
$Nomenclature = (isset($_REQUEST['nomId']))?
        Object::load('Nomenclature', $_REQUEST['nomId']):$Component->getNomenclature();

if (Tools::isEmptyObject($Nomenclature)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
    exit;
}
$nomId = $Nomenclature->getId();
$parentId = isset($_REQUEST['parentId'])?
        $_REQUEST['parentId']:$Component->getParentId();

// Test: levelTwoCanExist?
// Si Component Head sans tracingMode et il existe un Component de level 1
// tel que son tracingMode vaut LOT, impossible de creer des components de level > 1
if ($parentId > 0) {
    $ParentComponent = Object::load('Component', $parentId);
    $parentTracingMode = Tools::getValueFromMacro(
            $ParentComponent, '%Product.TracingMode%');
    if ($ParentComponent->getLevel() == 1 && !$Nomenclature->levelTwoCanExist()) {
        Template::errorDialog($errorLevelTwoCanNotExist, $returnURL);
        exit;
    }
}


//  Si on a clique sur OK apres saisie ou confirme la saisie
if (isset($_REQUEST['formSubmitted'])) {
    FormTools::autoHandlePostData($_REQUEST, $Component, 'Component');

    Database::connection()->startTrans();
    // Si le Component ajoute est lie a un Product pas au SN,
    // On met a jour Nomenclature.Buildable: false
    $TracingMode = Tools::getValueFromMacro($Component, '%Product.TracingMode%');
    if (in_array($TracingMode, array(0, Product::TRACINGMODE_LOT))
                && $Nomenclature->getBuildable() == 1) {
        $Nomenclature->setBuildable(0);
        saveInstance($Nomenclature, $returnURL);
    }
    saveInstance($Component, $returnURL);

    //  Commit de la transaction
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die('erreur sql');
    }
    Database::connection()->completeTrans();
    if (isset($action) && $action == 'add') {
        $returnURL = 'ComponentAddEdit.php?cmpId=' . $_REQUEST['Component_Parent_ID'];
    }
    Tools::redirectTo($returnURL);
    exit;
}


/*  Formulaire */
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once ('HTML/QuickForm.php');
$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('ComponentAddEdit', 'post');

/* Dans le Select pour le Product, on ne propose pas les Product correspondant
   a un Component de meme Parent; si le Parent n'a pas de TracingMode,
   on n'a le choix que parmi les Product sans TracingMode;
   si le Parent a son TracingMode=LOT, les Products possibles ne sont pas suivis au SN.
   Exception ajoutee si Component.level==1: le Parent n'a pas de TracingMode, mais
   qu'il n'y a pas encore de Component de level 2, on autorise le choix parmi
   les Product sans TracingMode ET ceux suivis au LOT !!!
*/
if ($parentId == 0) {  // Level = 0 et mode edition
    $ProductArray = array($Component->getProductId() =>
            Tools::getValueFromMacro($Component, '%Product.BaseReference%'));
    $ProductNameArray = array($Component->getProductId() =>
            Tools::getValueFromMacro($Component, '%Product.Name%'));
    ///$productTextDisabled = 'readonly';
    $ParentArray = array(0 => '');
}
else {  // level > 0
    switch ($parentTracingMode) {
    case 0:
        if ($ParentComponent->getLevel() == 0 && $Nomenclature->levelOneCanBeLot()) {
            $filterCmptArray = array(SearchTools::NewFilterComponent(
                'TracingMode', '', 'In', array(0, Product::TRACINGMODE_LOT), 1));
        }else {
            $filterCmptArray = array(SearchTools::NewFilterComponent(
                    'TracingMode', '', 'Equals', 0, 1));
        }
        break;
    case Product::TRACINGMODE_LOT:
        $filterCmptArray = array(SearchTools::NewFilterComponent(
                'TracingMode', '', 'In', array(0, Product::TRACINGMODE_LOT), 1));
        break;
    default:
        $filterCmptArray = array();
    }

    $prohibitedProductIdArray = array();
    $ComponentColl = $ParentComponent->getComponentCollection(
            array(), array(), array('Product'));
    if (!Tools::isEmptyObject($ComponentColl)) {
        $count = $ComponentColl->getCount();  // $cmpId
        for($i = 0; $i < $count; $i++){
            $prohibitedProductIdArray[] = $ComponentColl->getItem($i)->getProductId();
        }
        $prohibitedProductIdArray = array_diff(
                $prohibitedProductIdArray, array($Component->getProductId()));
        if (!empty($prohibitedProductIdArray)) {
            $filterCmptArray[] = SearchTools::NewFilterComponent(
                    'Id', '', 'NotIn', $prohibitedProductIdArray, 1);
        }
    }

    $pdtFilter = SearchTools::filterAssembler($filterCmptArray);
    $ProductArray = SearchTools::createArrayIDFromCollection(
            'Product', $pdtFilter, '', 'BaseReference');
    $ProductNameArray = SearchTools::createArrayIDFromCollection(
            'Product', $pdtFilter);

    $ParentArray = array($ParentComponent->getId() =>
            Tools::getValueFromMacro($ParentComponent, '%Product.BaseReference%'));
    $Component->setLevel($ParentComponent->getLevel() + 1);
    $productTextDisabled = '';
}

$form->addElement('select', 'ComponentProductName',
        _('Related product') . ' (' . _('Design.') . ') *', $ProductNameArray,
		'onchange="fw.dom.selectOptionByValue(\'Component_Product_ID\', this.value);" style="width:100%"');
$form->addElement('select', 'Component_Product_ID',
        _('Related product') . ' (' . _('Ref.') . ') *',
        $ProductArray, 'id="Component_Product_ID" style="width:100%"');

$ComponentGroupArray = SearchTools::createArrayIDFromCollection(
        'ComponentGroup', array('Nomenclature' => $nomId));
$ComponentGroupArray = array(_('None')) + $ComponentGroupArray;
$form->addElement('select', 'Component_ComponentGroup_ID', _('Set'),
        $ComponentGroupArray, 'style="width:100%"');

// Construction du tableau des Parents possibles
$Filter = SearchTools::NewFilterComponent('Id', '', 'NotEquals', $cmpId, 1);

// Les champs Parent et Level ne sont jamais modifiables
$form->addElement('select', 'Component_Parent_ID', _('Parent'), $ParentArray,
        'style="width:100%"');
$form->addElement('text', 'Component_Level', _('Level'),
        'style="width:100%;background-color:#E1E8EF;border:1px #000000 dotted;" readonly');
$form->addElement('text', 'Component_Quantity', _('Quantity') . ' *', 'style="width:100%"');
$form->addElement('hidden', 'cmpId', $cmpId);
$form->addElement('hidden', 'nomId', $nomId);
$form->addElement('hidden', 'Component_Nomenclature_ID', $nomId);

/*  Si Edition d'un Component existant  */
if ($Component->getId() > 0) {
    $defaultValues = FormTools::getDefaultValues($form, $Component);
    $defaultValues['ComponentProductName'] = $Component->getProductId();
}
else {
    $defaultValues = array('Component_Level' => $Component->getLevel());
}
$form->setDefaults($defaultValues);  // affiche le form avec les val par defaut

/*  Validation du formulaire */
$form->addRule('Component_Level',
        _('Please provide a level.'), 'required', '', 'client');
$form->addRule('Component_Quantity',
        _('Please provide a quantity.'), 'required', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('returnURL', $returnURL);
$smarty->assign('form', $renderer->toArray());
if ($cmpId > 0) {  // Mode Edit
    $smarty->assign('addUrl', 'ComponentAddEdit.php?nomId=' . $nomId
            . '&parentId='.$cmpId . '&returnURL=ComponentAddEdit.php?cmpId='.$cmpId);
    $smarty->assign('additionalActions', 'yes');
}

$pageContent = $smarty->fetch('Nomenclature/ComponentAddEdit.html');

Template::page($pageTitle, $pageContent);
?>
