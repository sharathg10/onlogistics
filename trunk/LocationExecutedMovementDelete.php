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
require_once('Objects/MovementType.const.php');
require_once('Objects/Alert.const.php');
 // utile ici pour la mise en session
require_once('Objects/LocationProductQuantities.php');
require_once('Objects/LocationExecutedMovement.php');
require_once('Objects/SellUnitType.const.php');
require_once('ActivatedMovementTools.php');
require_once('AlertSender.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_GESTIONNAIRE_STOCK));
$uac = $Auth->getUser();
$siteIds = $uac->getSiteCollectionIds();
$ProfileId = $Auth->getProfile();
$UserConnectedActorId = $Auth->getActorId();

//Database::connection()->debug = true;

$session = Session::Singleton();
SearchTools::prolongDataInSession();  // prolonge les datas en session

$cancelLink = (isset($_SESSION['firstarrival']))?
		'LocationExecutedMovementList.php?firstarrival=1':
		'LocationExecutedMovementList.php';

$LEM = Object::load('LocationExecutedMovement', $_REQUEST['LEM']);
if (Tools::isEmptyObject($LEM)) {
    Template::errorDialog(E_ERROR_IN_EXEC, $cancelLink);
	exit;
}
$Cancellable = $LEM->isCancellable();
if (false == $Cancellable[0]) { // teste si le LEM est annulable
    Template::errorDialog($Cancellable[1], $cancelLink);
	exit;
}
if (!isset($_REQUEST['ok'])) {
    Template::confirmDialog(_('Do you want to cancel the selected item ?'),
        $_SERVER['PHP_SELF'].'?ok=1&LEM='.$_REQUEST['LEM'],
        $cancelLink);
   	exit;
}

$ExecutedMovement = $LEM->getExecutedMovement();
$entrieExit = Tools::getValueFromMacro($ExecutedMovement, '%Type.EntrieExit%');
$foreseeable = Tools::getValueFromMacro($ExecutedMovement, '%Type.Foreseeable%');
Database::connection()->startTrans();


/*
 * Si mvt non prevu ou si sortie normale sans qu'il y ait eu de BL edite
 * ou si mvt interne (interne avec ou sans prevision: tjs annulé en totalité),
 * c'est une LocationExecutedMovement::ANNULATION
 **/
if ($foreseeable != 1 || $LEM->isBLEditionPossible()) {
	$AlertArray = array();  // Contiendra les alertes eventuelles a envoyer
	$LocationId = $LEM->getLocationId();
	$ProductId = $LEM->getProductId();
	$LPQMapper = Mapper::singleton('LocationProductQuantities');
	$LocationPdtQuantities = $LPQMapper->load(
            array('Location' => $LocationId, 'Product' => $ProductId));

	// Si on annule une sortie, on doit faire une entree: le LPQ doit encore
    // exister, sinon, on le recree
	if ($entrieExit == SORTIE && Tools::isEmptyObject($LocationPdtQuantities)) {
		$LocationPdtQuantities = Object::load('LocationProductQuantities');
		$LocationPdtQuantities->setProduct($LEM->getProduct());
		$LocationPdtQuantities->setLocation($LEM->getLocation());
	}
	$coef = ($entrieExit == SORTIE)?1:-1;
	$initialQuantity = $LocationPdtQuantities->getRealQuantity();
	$LocationPdtQuantities->setRealQuantity($initialQuantity + ($coef * $LEM->getQuantity()));

	// Suppression de l'emplacement s'il a une quantite nulle
	if ($LocationPdtQuantities->getRealQuantity() == 0) {
        deleteInstance($LocationPdtQuantities, $cancelLink);
	}
	else {
        saveInstance($LocationPdtQuantities, $cancelLink);
	}

	// Gestion des LocationConcreteProduct
	$LEMConcreteProductCollection = $LEM->getLEMConcreteProductCollection();
	$countLEMCP = $LEMConcreteProductCollection->getCount();
	for($i = 0; $i < $countLEMCP; $i++){
		$LEMConcreteProduct = $LEMConcreteProductCollection->getItem($i);
		$LCPMapper = Mapper::singleton('LocationConcreteProduct');
		$LCP = $LCPMapper->load(
		      array('ConcreteProduct' => $LEMConcreteProduct->getConcreteProductId(),
					'Location' => $LocationId));
		// Si on annule une sortie, on doit faire une entree: le LCP doit
        // encore exister, sinon, on le recree
		if ($entrieExit == SORTIE && Tools::isEmptyObject($LCP)) {
			$LCP = Object::load('LocationConcreteProduct');
			$LCP->setConcreteProduct($LEMConcreteProduct->getConcreteProduct());
			$LCP->setLocation($LEM->getLocation());
		}
		$initialQuantity = $LCP->getQuantity();
		$LCP->setQuantity($initialQuantity + ($coef * $LEMConcreteProduct->getQuantity()));
		// Suppression du LCP s'il a une quantite nulle
		if ($LCP->getQuantity() == 0) {
            deleteInstance($LCP, $cancelLink);
		}
		else {
            saveInstance($LCP, $cancelLink);
		}
	}

	$Product = $LEM->getProduct();
	// Verification que la qte totale de product en stock est superieure a
    // la Qte minimum autorisee
    // qte totale en stock de ce produit
	$TotalRealQuantity = $Product->getRealQuantity();
	if ($TotalRealQuantity <= $Product->getSellUnitMinimumStoredQuantity()) {
		$AlertArray[] = Object::load('Alert', ALERT_STOCK_QR_MINI);
	}

	//  MAJ de ACM.State, EXM.State si besoin, c à d si sortie normale ou
	// si mvt interne *avec* prevision
	$initVirtualQuantity = $Product->getSellUnitVirtualQuantity();
	$ActivatedMovement = $ExecutedMovement->getActivatedMovement();
	if (!Tools::isEmptyObject($ActivatedMovement)) {
	    $ActivatedMovement->setState(ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT);
        saveInstance($ActivatedMovement, $cancelLink);
        $initialEXMState = $ExecutedMovement->getState();
        $ExecutedMovement->setState(ExecutedMovement::EXECUTE_PARTIELLEMENT);
        $ExecutedMovement->setEndDate(date('Y-m-d H:i:s'));
        $InitialRealQuantity = $ExecutedMovement->getRealQuantity();
        $ExecutedMovement->setRealQuantity($InitialRealQuantity - $LEM->getQuantity());
        saveInstance($ExecutedMovement, $cancelLink);
	}
	// MAJ des quantités virtuelles
	// Pas une sortie normale, ici
	if ($foreseeable != 1) {
	    $Product->setSellUnitVirtualQuantity(
                $initVirtualQuantity + ($coef * $LEM->getQuantity()));
    	if ($initVirtualQuantity + ($coef * $LEM->getQuantity()) <= 0) {
    		$AlertArray[] = Object::load('Alert', ALERT_STOCK_QV_REACH_ZERO);
    	}
    	elseif ($initVirtualQuantity + ($coef * $LEM->getQuantity())
    			<= $Product->getSellUnitMinimumStoredQuantity()) {
    		$AlertArray[] = Object::load('Alert', ALERT_STOCK_QV_MINI);
    	}
	}
	// Pour une sortie normale, plus complique
	// jamais d'alerte ici, car entree en stock
	else {
        // Le pdt commande
        $OrderedProduct = $ActivatedMovement->getProduct();
        $OrderedQuantity = $ActivatedMovement->getQuantity();

        // MAJ des VirtualQty : seulement pour le Pdt reintegre et si != celui cmde
        // Oubien si le mvt etait total, avec Qte < Qte commandee
         if ($OrderedProduct->getId() != $Product->getId()) {
            $Product->setSellUnitVirtualQuantity(
                    $Product->getSellUnitVirtualQuantity() + $LEM->getQuantity());
         }
         elseif ($initialEXMState == ExecutedMovement::EXECUTE_TOTALEMENT
                && $InitialRealQuantity < $OrderedQuantity) {
            $Product->setSellUnitVirtualQuantity($Product->getSellUnitVirtualQuantity()
                    - ($OrderedQuantity - $InitialRealQuantity));
         }
    }
    saveInstance($Product, $cancelLink);

	//  MAJ du LocationExecutedMovt et des LEMConcreteProduct associes
	$LEM->cancel();
    saveInstance($LEM, $cancelLink);

	// Suppression des Box associees au LEM annule
	$boxCollection = $LEM->getBoxCollection();
	$boxCount = $boxCollection->getCount();
	for($i = 0; $i < $boxCount; $i++){
	    $box = $boxCollection->getItem($i);
        deleteInstance($box, $cancelLink);
	}

	//  Creation du LocationExecutedMovt annulateur
	$CancellerLEM = $LEM->createCancellerMovement();

	// On committe la transaction
	if (Database::connection()->hasFailedTrans()) {  // gestion des erreurs
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
		Database::connection()->rollbackTrans();
		Template::errorDialog('Erreur SQL...', $cancelLink);
        exit;
	}

	Database::connection()->completeTrans();

	//  Mail pour avertir d'une annulation
	$AlertArray[] = Object::load('Alert', ALERT_MOVEMENT_CANCELLED);

	//  Envoi des eventuelles alertes
	$params = array(
            'ProductBaseReference' => $Product->getBaseReference(),
			'ProductMinimumStock' => $Product->getSellUnitMinimumStoredQuantity(),
			'ProductName' => $Product->getName(),
			'ProductSupplierName' => Tools::getValueFromMacro($Product, '%MainSupplier.Name%'),
			'Quantity' => $LEM->getQuantity(),
			'LocationExecutedMovtId' => $LEM->getId(),
			'MvtTypeName' => Tools::getValueFromMacro($LEM, '%ExecutedMovement.Type.Name%')
				);
	for ($i=0;$i<count($AlertArray);$i++) {
		$alert = $AlertArray[$i];
        if (AlertSender::isStockAlert($alert->getId())) {
            AlertSender::sendStockAlert($alert->getId(), $Product);
        } else {
		    $alert->prepare($params);
            $alert->send();  // on envoie l'alerte
        }
		unset($alert);
	}

	Tools::redirectTo('LocationExecutedMovementList.php?return=1');
}

/*
 * Sinon, mvt prevu, c'est une REINTEGRATION EN STOCK
 **/
else {
    // Reintegration dans ce cas, pas annulation complete:
    // Il faudra selectionner les Locations et qtes
    $cancellationTypeArray = array(0 => _('Select a movement type'))
            + array_slice(LocationExecutedMovement::getCancelledConstArray(), 1, 2, true);

	// si on n'a pas encore selectionne un type d'annulation
	if (!isset($_REQUEST['CancellationType'])) {
		require_once ('HTML/QuickForm.php');
        require_once('HTML/QuickForm/Renderer/Default.php');
		$form = new HTML_QuickForm('monForm', 'post', $_SERVER['PHP_SELF']);
        $renderer = new HTML_QuickForm_Renderer_Default();
		$form->addElement('select', 'CancellationType', '',
                          $cancellationTypeArray,
						  'onchange="this.form.submit()"');
		$form->addElement('hidden', 'ok', 1);  // pour sauter la demande de confirm
		$form->addElement('hidden', 'LEM', $_REQUEST['LEM']);

		$form->accept($renderer);  // affecte au form le renderer personnalise
		$monformHTML = $renderer->toHtml();  // recup le HTML du form
		/*  Suppression des LPQ en session s'il y en a  */
		if (isset($_SESSION['LPQCollection'])) {
		    unset($_SESSION['LPQCollection']);
		}
        Template::page(_('Execute a stock reinstatement'), $monformHTML);
	}

	else {  // Si on a selectionne un type d'annulation
		$LPQMapper = Mapper::singleton('LocationProductQuantities');
		$filter = array('Product' => $LEM->getProductId(),
						'Location.Activated' => 1, 'Activated' => 1);

		if ($ProfileId == UserAccount::PROFILE_GESTIONNAIRE_STOCK) {
			$filter = array_merge(
                    $filter,
                    array('Location.Store.StorageSite.Owner' => $UserConnectedActorId));
		}

		$LPQCollection = $LPQMapper->loadCollection(
                $filter, array('Location.Name' => SORT_ASC));
		if (isset($_SESSION['LPQCollection'])) {
		    $LPQCollectionForSession = unserialize($_SESSION['LPQCollection']);
			for ($i=0;$i<$LPQCollectionForSession->getCount();$i++) {
				$LPQCollection->setItem($LPQCollectionForSession->getItem($i));
			}
		}

		/*  On construit le select sur les Location  */
		$HTMLLocationSelect = getHTMLLocationSelect($LPQCollection,
            $ProfileId, $UserConnectedActorId, $siteIds);

		/*  Creation du grid  */
		$Product = $LEM->getProduct();
		// Param ENTREE car on ne reintegre que des sorties: on fait donc des entrees
		$grid = executionGrid($Product->getTracingMode(), ENTREE, $LPQCollection);
		$ActivatedMvtGrid = $grid->render($LPQCollection);

		/*  Affichage du formulaire avec smarty  */
		$Smarty = new Template();
		$cmdTitle = _('Execute a stock reinstatement');
		$JSRequirements = "";
		$Smarty->assign('FormAction', 'ActivatedMovementAddWithPrevisionToBeExecuted.php'
									. '?returnURL=LocationExecutedMovementDelete.php');
		$Smarty->assign('ActivatedMvtGrid', $ActivatedMvtGrid);
		$Smarty->assign('CancellationType', $_REQUEST['CancellationType']);
	    $Smarty->assign('MvtTypeEntrieExit', ENTREE);
		$Smarty->assign('MvtTypeName', _('Stock reinstatement') . ': '
                . $cancellationTypeArray[$_REQUEST['CancellationType']]);
        $qty = $LEM->getMaxQuantityForCancellation();
		$Smarty->assign('Quantity', $qty);
        $Smarty->assign('displayedQuantity', I18N::formatNumber($qty, 3, true));
		$Smarty->assign('CommandNo', Tools::getValueFromMacro($LEM,
		      '%ExecutedMovement.ActivatedMovement.ProductCommandItem.Command.CommandNo%'));
		$Smarty->assign('ActivatedMvt', Tools::getValueFromMacro($LEM,
                '%ExecutedMovement.ActivatedMovement.Id%'));
		$Smarty->assign('SelectProduct', $LEM->getProductId());
		$Smarty->assign('ProductBaseReference', $Product->getBaseReference());
		$Smarty->assign('ProductName', $Product->getName());
		$Smarty->assign('TracingMode', $Product->getTracingMode());
		if ($Product->getTracingMode() > 0) {
            $tmArray = Product::getTracingModeConstArray();
		    $Smarty->assign('TracingModeName', $tmArray[$Product->getTracingMode()]);
		}
        // gestion uv
        $sut = $Product->getSellUnitType();
        if ($sut instanceof SellUnitType && $sut->getId() > SELLUNITTYPE_UR) {
            $Smarty->assign('SellUnitType', $sut->getShortName());
        }
		$Smarty->assign('HTMLLocationSelect', $HTMLLocationSelect);

		$Comment = Tools::getValueFromMacro($LEM, '%ExecutedMovement.Comment%');
		if (isset($_REQUEST['Comment'])){
			$exmComment = stripcslashes(stripcslashes(html_entity_decode(
                    urldecode($_REQUEST['Comment']))));
		} else {
		    $exmComment = (isset($Comment))?$Comment:"";
		}
		$Smarty->assign('Comment', $exmComment);
		$Smarty->assign('ExecutedMovtId', $LEM->getExecutedMovementId());
		$Smarty->assign('returnURL', $cancelLink);
		$Smarty->assign('LEM', $_REQUEST['LEM']);
        Template::page(
            $cmdTitle,
            $Smarty->fetch('ActivatedMovement/ActivatedMovementAddWithPrevision.html'),
            array('js/includes/ActivatedMovementAdd.js')
        );
	}
}

?>