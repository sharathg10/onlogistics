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

// Pour prolonger la session du formulaire de recherche de ProductList
SearchTools::ProlongDataInSession();

// Checks habituels
if (!isset($_REQUEST['pdtId'])) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'ProductList.php');
    exit;
}
$Product = Object::load('Product', $_REQUEST['pdtId']);
if (Tools::isEmptyObject($Product)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'ProductList.php');
    exit;
}
$pqcCollection = $Product->getProductQuantityByCategoryCollection();

// Le grid recapitulatif
// avant le formSubmitted pour le dispatchAction
$grid = new Grid();
$grid->displayCancelFilter = false;
$grid->withNoSortableColumn = true;

$grid->NewColumn('FieldMapper', _('Category'), array('Macro' => '%Category.Name%'));
$grid->NewColumn('FieldMapper', _('Minimum quantity'), array('Macro' =>'%MinimumQuantity%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Quantity type'),
        array('Macro' => '%MinimumQuantityType%',
              'TranslationMap' => ProductQuantityByCategory::getMinimumQuantityTypeConstArray()));

$grid->NewAction('AddEdit', array('Action'=> 'Edit',
                                  'EntityType' => 'ProductQuantityByCategory',
                                  'TransmitedArrayName' => 'pqcId',
                                  'URL'=>'ProductQuantityByCategoryEdit.php')
);
$grid->NewAction('Delete', array('TransmitedArrayName' => 'pqcIds',
                        		 'EntityType' => 'ProductQuantityByCategory',
                                 'Query' => 'pdtId=' . $_REQUEST['pdtId'])
);


/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_POST['formSubmitted'])) {

    // Sans ca, lea actions du Grid sont inoperantes
    if ($grid->isPendingAction()) {
        $res = $grid->dispatchAction($pqcCollection);
        if (Tools::isException($res)) {
            Template::errorDialog(E_ERROR_IN_EXEC . ': ' . $res->getMessage(),
                	$_SERVER['PHP_SELF'] . '?pdtId=' . $_REQUEST['pdtId']);
            exit;
        }
    }
    Database::connection()->startTrans();

    foreach($_REQUEST['CategoryIds'] as $ctgId) {
        if ($ctgId == '##') {
            continue;
        }
        // En cas de refresh de la page,
        // unicite du couple (_Product, _Category) gere par un index unique
        $pqc = Object::load('ProductQuantityByCategory');
        $pqc->setProduct($_REQUEST['pdtId']);
        $pqc->setCategory($ctgId);
        $pqc->setMinimumQuantity($_REQUEST['MinimumQuantity']);
        $pqc->setMinimumQuantityType($_REQUEST['MinimumQuantityType']);
        saveInstance($pqc, $_SERVER['PHP_SELF']);
        $pqcCollection->setItem($pqc);
    }

    if (Database::connection()->hasFailedTrans()) {
        if (DEV_VERSION) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        }
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $_SERVER['PHP_SELF']);
        exit;
    }

    Database::connection()->completeTrans();
}


// Formulaire
$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('pqcList', 'post', $_SERVER['PHP_SELF']);

$form->addElement('hidden', 'pdtId', $_REQUEST['pdtId']);

// On n'affiche pas dans le select les Categories deja affectees
$count = $pqcCollection->getCount();
$badCategoryIds = array();
for($i = 0; $i < $count; $i++){
    $item = $pqcCollection->getItem($i);
    $badCategoryIds[] = $item->getCategoryId();
}
$filter = (empty($badCategoryIds))?array():
        SearchTools::NewFilterComponent('Id', '', 'NotIn', $badCategoryIds, 1);
$categoryArray = SearchTools::CreateArrayIDFromCollection('Category', $filter,
        _('Select one or more categories'));
$form->addElement('select', 'CategoryIds',
        _('Concerned categories'),
        $categoryArray, 'multiple size="4" style="width:100%;"');
$form->addElement('text', 'MinimumQuantity',
        _('Minimum ordered quantity'), 'style="width:100%;"');
$form->addElement('select', 'MinimumQuantityType', _('Quantity type'),
        ProductQuantityByCategory::getMinimumQuantityTypeConstArray(),
        'style="width:100%;"');

$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());

$result = $grid->render($pqcCollection, false, array(), array('Category.Name' => SORT_ASC));
$smarty->assign('pqcGrid', $result);


$pageContent = $smarty->fetch('ProductQuantityByCategory/ProductQuantityByCategoryList.html');

$js = array('js/lib-functions/checkForm.js',
            'js/includes/ProductQuantityByCategoryList.js');
Template::page(
    sprintf($pageTitle, $Product->getBaseReference()),
    $pageContent, $js
);
?>
