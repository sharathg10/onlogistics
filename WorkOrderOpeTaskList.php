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
require_once('Objects/WorkOrder.php');
require_once('WorkOrderAuth.php');
$Auth = WorkOrderAuth::Singleton();
$UserConnectedActorId = $Auth->getActorId();  // Id de l'actor lie au User connecte

if (!isset($_REQUEST['OtId']) || !$Auth->check($_REQUEST['OtId'])) {
	Template::errorDialog(_('You are not allowed to edit this work order.'),
            'WorkOrderList.php');
	exit;
}

$DisplayWorkOrderDetail = false;

$authArray = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR,
    UserAccount::PROFILE_SUPERVISOR, UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR);
// Un UserAccount::PROFILE_ADMIN_VENTES ne voit pas les dates/heures et KM de depart et arrivee
if (in_array($Auth->getProfile(), $authArray)) {
	$DisplayWorkOrderDetail = true;
}

// Si on a demande a changer l'ordre des operations
if (isset($_POST['orderInWO'])) {
	foreach($_POST['OpeId'] as $i => $OpeId){
		$ActivatedChainOperation = Object::load('ActivatedChainOperation', $OpeId);
		if (isset($_POST['orderInWO'][$i]) && intval($_POST['orderInWO'][$i]) > 0) {
			$ActivatedChainOperation->setOrderInWorkOrder($_POST['orderInWO'][$i]);
            saveInstance($ActivatedChainOperation, 'WorkOrderList.php');
		}
		unset($ActivatedChainOperation);
	}
}

if (isset($_REQUEST['OtId'])) {
	$WorkOrder = Object::load('WorkOrder', $_REQUEST['OtId']);
	if (!($WorkOrder instanceof WorkOrder)) {
	     Template::errorDialog(
            _('An error occurred: work order details cannot be displayed.'),
            'WorkOrderList.php');
	}
	$State = $WorkOrder->getState();
	$ConfirmMsge = "";  // confirm d'enregistrement des modif des dates de l'OT

//  si on a clique sur OK ds le form en dessous du Grid on met a jour l'OT
	if (isset($_REQUEST['OK_x'])) {
    	$WorkOrder->setDepartureDate(DateTimeTools::QuickFormDateToMySQL('DepartureDate'));
		$WorkOrder->setArrivalDate(DateTimeTools::QuickFormDateToMySQL('ArrivalDate'));
		$WorkOrder->setDepartureKm($_POST['DepartureKm']);
		$WorkOrder->setArrivalKm($_POST['ArrivalKm']);
        saveInstance($WorkOrder, 'WorkOrderList.php');
		$ConfirmMsge = _('Operation successful.');
	}
}

SearchTools::prolongDataInSession();  // prolonge les datas en session
$retURL = '&returnURL=' . $_REQUEST['returnURL'];

$grid = new Grid();
$grid->withNoSortableColumn = true;

$choice = isset($_REQUEST['choice'])?$_REQUEST['choice']:'';
$otId   = isset($_REQUEST['OtId'])?$_REQUEST['OtId']:'';

$grid->NewAction('Delete', array('Caption' => _('Unassign'),
						         'EntityType' => 'WorkOrderOpeTask',
						         'Query' => 'choice=' . $choice . '&OtId=' . $otId . $retURL,
						         'TransmitedArrayName' => 'Id',
								 'Enabled' => ($State==WorkOrder::WORK_ORDER_STATE_TOFILL),
								 // si cloture, on ne peut plus
								 'Profiles' => $authArray));

// Servira a l'issue de WorkOrderCloture, a rediriger correctement
$from = isset($_REQUEST['from'])?'&from=' . $_REQUEST['from']:'';
$grid->NewAction('Redirect', array('Caption' => _('Close'),
										'Enabled' => ($State==WorkOrder::WORK_ORDER_STATE_TOFILL),
										'TransmitedArrayName' => 'acoId',
										'URL' => 'WorkOrderCloture.php?OtId='.$otId
												. '&WkoCloId[]=' . $otId
												. $retURL . $from,
										'Profiles' => $authArray));

if ($choice != 2) {  // si on affiche les operations, on propose de modifier l'ordre
	$grid->NewAction('Submit', array('Caption' => _('Modify order'),
								 	 'Enabled' => ($State==WorkOrder::WORK_ORDER_STATE_TOFILL),
								 	 // si cloture, on ne peut plus
									 'Profiles' => $authArray));
}

$grid->NewAction('Print', array('Profiles' => $authArray));

$CancelLink = (false === strpos($_REQUEST['returnURL'], 'WorkOrder.php'))?
        '?':'?action=edit&OTid='.$_REQUEST['OtId'];
$grid->NewAction('Cancel', array('ReturnURL' => $_REQUEST['returnURL'].$CancelLink));

// On affiche les operations

$mapper = Mapper::singleton('ActivatedChainOperation');
$Collection = $mapper->loadCollection(
        array('OwnerWorkerOrder'=>$otId),
		array('OrderInWorkOrder' => SORT_ASC, 'LastTask.End' => SORT_ASC));

$grid->NewColumn('FieldMapper', _('Order'),
        array('Macro' => '%ActivatedChain.CommandItem()[0].Command.CommandNo%'));
$grid->NewColumn('FieldMapper', _('Operation'),
        array('Macro' => '%Operation.Name%'));
$grid->NewColumn('OrderWOOpeTaskList', _('Index'));
$grid->NewColumn('BeginEndWOOpeTaskList', _('Beginning'), array('BeginEnd' => 'Begin'));
$grid->NewColumn('BeginEndWOOpeTaskList', _('End'), array('BeginEnd' => 'End'));
$grid->NewColumn('PackingNumberWOOpeTaskList', _('Parcel number'));
$grid->NewColumn('PackingWeightWOOpeTaskList', _('Weight'));
$grid->NewColumn('CommentWOOpeTaskList', _('Comment'));
$grid->NewColumn('FieldMapper', '<div class="onlyPrintable">' .
        _('Sign and/or seal') . '</div>');
$select1 = 'selected="selected"';
$select2 = '';

/*  Contruction du formulaire de saisie des infos sur l'OT   */
$smarty = new Template();
require_once ('HTML/QuickForm.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$form = new HTML_QuickForm('WorkOrderOpeTaskList', 'post', $_SERVER['PHP_SELF']);

$form->addElement('date', 'DepartureDate', '',
        array('format'    => I18N::getHTMLSelectDateFormat() . ' H:i',
			  'minYear'   => date('Y')-1,
		      'maxYear'   => date('Y')+1));
$form->addElement('date', 'ArrivalDate', '',
        array('format'    => I18N::getHTMLSelectDateFormat() . ' H:i',
		      'minYear'   => date('Y')-1,
		      'maxYear'   => date('Y')+1));
$form->addElement('text', 'DepartureKm', '');
$form->addElement('text', 'ArrivalKm', '');


/*  Gestion des valeurs affichees par defaut  */
$defaultValues = array(
        'ArrivalDate' => array('d'=>date('d'), 'm'=>date('m'),
		                       'Y'=>date('Y'), 'H'=>date('H'), 'i'=>date('i')),
        'DepartureDate' => array('d'=>date('d'), 'm'=>date('m'),
		   						 'Y'=>date('Y'), 'H'=>date('H'), 'i'=>date('i')));

$DepartureDate = $WorkOrder->getDepartureDate();
$ArrivalDate = $WorkOrder->getArrivalDate();

if ($DepartureDate != '0000-00-00 00:00:00') {
    $DateArray = DateTimeTools::DateExploder($DepartureDate);
    $defaultValues['DepartureDate'] = array(
            'd'=>$DateArray['day'], 'm'=>$DateArray['month'],
			'Y'=>$DateArray['year'], 'H'=>$DateArray['hour'], 'i'=>$DateArray['mn']);
}
if ($ArrivalDate != '0000-00-00 00:00:00') {
    $DateArray = DateTimeTools::DateExploder($ArrivalDate);
    $defaultValues['ArrivalDate'] = array(
            'd'=>$DateArray['day'], 'm'=>$DateArray['month'],
			'Y'=>$DateArray['year'], 'H'=>$DateArray['hour'], 'i'=>$DateArray['mn']);
}
$defaultValues = array_merge($defaultValues,
        array('DepartureKm' => $WorkOrder->getDepartureKm(),
			  'ArrivalKm' => $WorkOrder->getArrivalKm()));

if ($State == WorkOrder::WORK_ORDER_STATE_FULL) {  // si OT cloture, on ne peut plus modifier ca
    $form->freeze();
    $smarty->assign('readonly', $State);  // pour ne pas afficher le bouton OK
}
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut
$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$smarty->assign('ConfirmMsge', $ConfirmMsge);

$TotalPackingInfos = $WorkOrder->getTotalPackingNumberAndWeight();
$smarty->assign('TotalPackingNumber', $TotalPackingInfos['PackingNumber']);
$smarty->assign('TotalPackingWeight',
        I18N::formatNumber($TotalPackingInfos['PackingWeight'])._('Kg'));
$smarty->assign('isForecast', ($TotalPackingInfos['isForecast'])?'<sup>P</sup>':'');

// si user n'a pas UserAccount::PROFILE_ADMIN_VENTES, on affiche le form sous le Grid
$FormContent = ($DisplayWorkOrderDetail)?
        $smarty->fetch('WorkOrder/WorkOrderOpeTaskList.html'):'';


if ($grid->isPendingAction()) {
    $dispatchResult = $grid->dispatchAction($Collection);
    if (Tools::isException($dispatchResult)) {
        Template::errorDialog($dispatchResult->getMessage(),
            'WorkOrderOpeTaskList.php?OtId=' . $otId . '&choice=' . $choice . $retURL);
    } else {
        Template::page(_('Work order contents'), $dispatchResult);
    }
} else {
    $pagecontent = '
	<form name="formSession" method="post">
	<table width="100%" border="0" cellpadding="3" class="grid">
	  	<tr>
	  		<td class="gris1">
            <b>'._('Reference').': '. $otId . ', '._('Name').': ' .
            $WorkOrder->getName() . '</b></td>
	  	</tr>
	</table>';
	$result = $grid->render($Collection, true);
    Template::page(
		_('Work order contents'),
        $pagecontent . $result . $FormContent . '</form>',
        array('js/includes/JSWorkOrderListOpeTask.js')
    );
}

?>
