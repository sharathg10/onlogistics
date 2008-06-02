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
require_once('Objects/Product.php');
require_once('Objects/Task.const.php');
$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

SearchTools::ProlongDataInSession();
//Database::connection()->debug = true;

if (!isset($_REQUEST['cpId']) || !isset($_REQUEST['cmpId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, 'NomenclatureConcreteProductList.php');
   	exit;
}

$returnURL = (isset($_SESSION['arboURL']))?
        $_SESSION['arboURL']:'NomenclatureConcreteProductList.php';
$query = 'cpId=' . $_REQUEST['cpId'] . '&cmpId=' . $_REQUEST['cmpId']
	    . '&parId=' . $_REQUEST['parId'];

// Le ConcreteProduct head
$headCP = Object::load('ConcreteProduct', $_REQUEST['cpId']);
$Component = Object::load('Component', $_REQUEST['cmpId']);
// Le Component de Level 0 a deja du etre affecte par ailleurs a un ConcreteProduct
if (Tools::isEmptyObject($Component) || $Component->getLevel() == 0) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
    exit;
}
$parentComponent = $Component->getParent();
$tracingMode = Tools::getValueFromMacro($Component, '%Product.TracingMode%');
$parentCP = Object::load('ConcreteProduct', $_REQUEST['parId']);
$mapper = Mapper::singleton('ConcreteComponent');

// Si pas de mode de suivi, collection vide
$ConcreteComponentCollection = new Collection();

if ($tracingMode == Product::TRACINGMODE_SN) {
    $CPCollection = $headCP->getConcreteProductCollection(
            array('Component' => $_REQUEST['cmpId']),
            array('SerialNumber' => SORT_ASC));
    $ConcreteComponentCollection = $mapper->loadCollection(
            array('ConcreteProduct' => $CPCollection->getItemIds(),
                  'Parent' => $_REQUEST['parId']),
            array('ConcreteProduct.SerialNumber' => SORT_ASC));
}
// Plus complique ds ce cas
else {  // Au LOT
    $CPCollection = $headCP->getConcreteProductCollection(
            array('Product' => $Component->getProductId()),
            array('SerialNumber' => SORT_ASC));
    $ackMapper = Mapper::singleton('ActivatedChainTask');
    $ackCollection = $ackMapper->loadCollection(
            array('Component' => $parentComponent->getId(),
                  'State' => ActivatedChainTask::STATE_FINISHED/*,
                  'AssembledRealQuantity' > 0*/));
    if (!Tools::isEmptyObject($ackCollection) && !Tools::isEmptyObject($CPCollection)) {
        $count = $ackCollection->getCount();
        for($i = 0; $i < $count; $i++) {
        	$ack = $ackCollection->getItem($i);
            $ccpCollection = $ack->getConcreteComponentCollection();
            // Parcourir cette collection, recupérer les CP de chaque item, ne
            // garder que ceux qui ont le bon Head, et garder le CCP, pour la QTY
            $ccpCount = $ccpCollection->getCount();
            for($j = 0; $j < $ccpCount; $j++) {
            	$ccp = $ccpCollection->getItem($j);
                $cpId = $ccp->getConcreteProductId();
                if (!in_array($cpId, $CPCollection->getItemIds())) {
                    continue;
                }
                $ConcreteComponentCollection->setItem($ccp);
            }
        }
    }
}


$pageTitle = _('Parts nomenclature reference ') . $headCP->getSerialNumber()
			. ': ' . _('Add or update component');

$isBuildable = (Tools::getValueFromMacro($Component, '%Nomenclature.Buildable%') == 1);

// Le grid des ConcreteComponent: place ici pour le isPendingAction()
$grid = new Grid();

$editMacro = $isBuildable?'<a href="ConcreteComponentAddEdit.php?ccmpId=%Id%&'
        . $query .'">%ConcreteProduct.SerialNumber%</a>':'%ConcreteProduct.SerialNumber%';

$grid->NewColumn('FieldMapper', _('SN or Lot'), array('Macro' => $editMacro));
$grid->NewColumn('FieldMapper', _('Quantity'), array('Macro' =>'%Quantity%'));

$grid->NewAction('AddEdit', array('Action' => 'Add',
						          'EntityType' => 'ConcreteComponent',
								  'Query' => $query,
                                  'Enabled' => $isBuildable));
$grid->NewAction('Delete', array('TransmitedArrayName' => 'ccmpId',
								 'EntityType' => 'ConcreteComponent',
                                 'ReturnURL' => basename($_SERVER['PHP_SELF']).'?'.$query,
								 'Query' => $query,
                                 'Enabled' => $isBuildable));
$grid->displayCancelFilter = false;
$grid->withNoSortableColumn = true;


// Si on a clique sur une action du grid
if ($grid->isPendingAction()) {
    $res = $grid->dispatchAction($ConcreteComponentCollection);
    if (Tools::isException($res)) {
        Template::errorDialog(E_ERROR_IN_EXEC . ': ' . $res->getMessage(),
            		   basename($_SERVER['PHP_SELF']) . '?' . $query);
        exit;
    }
}

$Product = $Component->getProduct();
$ComponentGroupName = Tools::getValueFromMacro($Component, '%ComponentGroup.Name%');
$ComponentGroupName = ($ComponentGroupName == '0' || $ComponentGroupName == 'N/A')?
		_('none'):$ComponentGroupName;

$smarty = new Template();
$smarty->assign('BaseReference', $Product->getBaseReference());
$smarty->assign('ComponentGroupName', $ComponentGroupName);
$smarty->assign('Level', $Component->getLevel());
$smarty->assign('ParentBaseReference',
				Tools::getValueFromMacro($parentComponent, '%Product.BaseReference%'));
$smarty->assign('ParentSerialNumber', $parentCP->getSerialNumber());
$smarty->assign('Quantity', $Component->getQuantity());
$smarty->assign('returnURL', $returnURL);
$smarty->assign('encodedReturnURL', urlencode($returnURL));

$result = $grid->render($ConcreteComponentCollection, false);
$smarty->assign('ConcreteComponentGrid', $result);
$pageContent = $smarty->fetch('Nomenclature/ComponentConcreteProduct.html');

Template::page($pageTitle, $pageContent);

?>
