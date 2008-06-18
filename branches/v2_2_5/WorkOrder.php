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
require_once('Objects/Actor.php');
require_once('Objects/WorkOrder.php');
require_once('DateWidget.php');
require_once('WorkOrderAuth.php');

$Auth = WorkOrderAuth::Singleton();
// Id de l'actor lie au User connecte
$UserConnectedActorId = $Auth->getActorId();

// vérification du mode et passage au mode par défaut (add) si pas de mode !
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'add';
//$action = isset($_REQUEST['OTid'])?'edit':'add';

// ouverture Smarty
$smarty = new Template();


if (isset($_REQUEST['Valider'])) {   // test si le formulaire est soumis
	// un UserAccount::PROFILE_ADMIN_VENTES n'a pas les droits ici
	$Auth->checkProfiles(
        array(
            UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
            UserAccount::PROFILE_ACTOR,
            UserAccount::PROFILE_SUPERVISOR,
            UserAccount::PROFILE_GESTIONNAIRE_STOCK,
            UserAccount::PROFILE_TRANSPORTEUR
        )
    );

	// met les saisies en session: sert a conserver les saisies en cas de
    // retour apres erreur
	SearchTools::inputDataInSession();

    $WOMapper = Mapper::singleton('WorkOrder');
	if ($action == 'add') {
	    $wo = new WorkOrder();
		$CompatibilityErrorMsge = "";
		$ConfirmMsge = _('saved');
	}
	else {
		$wo = $WOMapper->load(array('Id' => $_REQUEST['Id']));
		$CompatibilityErrorMsge =
            _("Modified data is no more compatible with work order items.\n");
		$ConfirmMsge = _('modified');
	}

    // met à jour les donnees de l'objet ou les cree
    $wo->setName($_REQUEST['name']);
    if (isset($_REQUEST['hasValidityRange']) && ($_REQUEST['hasValidityRange'] == 1)) {
        // cas ou la case Periode de validite est cochee
        $wo-> setValidityStart ($_REQUEST['Date1']);
        $wo-> setValidityEnd ($_REQUEST['Date2']);
    } else {
        // cas ou la case Periode de validite n'est pas cochee
        $wo-> setValidityStart (null);
        $wo-> setValidityEnd (null);
    }
    $wo->setActor($_REQUEST['actor']);
    $wo->setComment($_REQUEST['comment']);
    $wo->setMaxVolume($_REQUEST['maxvol']);
    $wo->setMaxLM($_REQUEST['maxLM']);
    $wo->setMaxWeigth($_REQUEST['maxweigth']);
    $wo->setMaxDistance($_REQUEST['maxdistance']);
    $wo->setMaxDuration(DateTimeTools::timeanalyzer($_REQUEST['maxduration']));

	if (isset($_REQUEST['SelectedOperations']) || ($action == 'edit')) {
		$urlRedirector = 'WorkOrder.php?OTid=' . $wo->getId() . '&action=edit';
		$acoFilter = array('OwnerWorkerOrder' => $wo->getId());
        // c a d si action == add
		if (isset($_REQUEST['SelectedOperations'])) {
            // reconstruction des arguments get pour utilisation ultérieure
			$GetString = UrlTools::buildURLFromRequest('SelectedOperations');
			$acoFilter = array('Id' => $_REQUEST['SelectedOperations']);
			$urlRedirector = 'WorkOrder.php?action=add&' . $GetString;
		}

		/** Ouverture de la transaction  **/
		Database::connection()->startTrans();

	    // enregistrement des mise à jour de l'OT
	    $ACOMapper = Mapper::singleton('ActivatedChainOperation');
	    $ACOCollection = $ACOMapper->loadCollection($acoFilter);

	    require_once('IsCommandCompatibleWithWorkOrder.php');
	    $IsCompatible = IsCommandCompatibleWithWorkOrder($ACOCollection, $wo);
	    if (true === $IsCompatible) {
            saveInstance($wo, $urlRedirector);
			if (isset($_REQUEST['SelectedOperations'])) {

		        for($i = 0; $i < $ACOCollection->GetCount(); $i++) {
		            unset($anOperation);
		            $anOperation = $ACOCollection->GetItem($i);
		            $anOperation->SetOwnerWorkerOrder($wo->getId());
		            $TaskCollection = $anOperation->GetActivatedChainTaskCollection();
		            for($j = 0; $j < $TaskCollection->GetCount(); $j++) {
		                $aTask = $TaskCollection->GetItem($j);
		                $aTask->SetOwnerWorkerOrder($wo->getId());
                        saveInstance($aTask, $urlRedirector);
		            } // for
                    saveInstance($anOperation, $urlRedirector);
		        } // for
	        }
			/** commit de la transaction **/
            if (Database::connection()->HasFailedTrans()) {
                trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
                Database::connection()->rollbackTrans();
                die('erreur sql');
            }
			Database::connection()->completeTrans();

	    } elseif (Tools::isException($IsCompatible)) {
	        Template::errorDialog($CompatibilityErrorMsge .
                    $IsCompatible->getMessage(), $urlRedirector);
	        exit;
	    }
	}
	else {
        saveInstance($wo, $_SERVER['PHP_SELF']);
	}
	$link = (isset($_REQUEST['SelectedOperations']))?
            'WorkOrderOpeTaskList.php?OtId='.$wo->getId().'&returnURL=WorkOrderList.php':
            'WorkOrderList.php';
	Template::infoDialog(
            _('Work order data were successfully ').$ConfirmMsge,
            $link);
	exit;
}
$req = '';

// noms des champs de saisie à conserver ou supprimer selon les cas
$fieldNameArray = array('name', 'maxvol', 'maxweigth', 'maxLM',
        'maxdistance', 'maxduration', 'comment');

/**  Mode creation d'un OT  */
if ($action == 'add') { // Mode Creation d'un OT
    // suppression des donnée des wo précédent

    for ($i=0; $i<count($fieldNameArray); $i++) {
    	if (isset($_SESSION[$fieldNameArray[$i]])) {
    	    unset($_SESSION[$fieldNameArray[$i]]);
    	}

    }
	// un UserAccount::PROFILE_ADMIN_VENTES n'a pas les droits ici
	$Auth->checkProfiles(
        array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_SUPERVISOR,
              UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR));
    $titre = _('Add a work order'); // titre de la page

    // traitement special pour les Widget des dates
    $BeginingDate = DateWidgetRender(1, false, date('Y'));
    $BeginingTime = HourWidgetRender(1);
    $EndDate = DateWidgetRender(2, false, date('Y'));
    $EndTime = HourWidgetRender(2);
    $maxduration = '<input type="text" name="WidgetHour3" value="0" size="3" />'
            . _('h.');
    $maxduration .= '<input type="text" name="WidgetMin3" value="0" size="3" />'
            . _('m.');
	$displayPeriod = 'none';

    if (isset($_REQUEST['SelectedOperations'])) {
        $smarty->assign('SelectedOperation', $_REQUEST['SelectedOperations']);
    }
}   // add

if ($action == 'edit') { // mode edition d'un OT
    $titre = _('Update work order');

    $WOMapper = Mapper::singleton('WorkOrder');
    // récupération de l'objet WorkOrder
    $OT = $WOMapper->load(array('Id' => $_REQUEST['OTid']));

	/*  Pour rendre le formulaire readOnly ou pas  */
	$readOnly = ((WorkOrder::WORK_ORDER_STATE_FULL == $OT -> GetState()) ||
	        ($Auth->getProfile() == UserAccount::PROFILE_ADMIN_VENTES ||
	         $Auth->getProfile() == UserAccount::PROFILE_AERO_ADMIN_VENTES))?'readonly="readonly"':'';
	$disabled = ((WorkOrder::WORK_ORDER_STATE_FULL == $OT -> GetState()) ||
	        ($Auth->getProfile() == UserAccount::PROFILE_ADMIN_VENTES ||
	         $Auth->getProfile() == UserAccount::PROFILE_AERO_ADMIN_VENTES))?'disabled="disabled"':'';

    /**
     * Traitement des dates pour affichage par Widget : test de l'existence
     * de la période de validité
     */
    if (($OT->getValidityStart() == null) || ($OT->getValidityEnd() == null)
			|| ($OT->getValidityStart() == '0000-00-00 00:00:00')
        	|| ($OT->getValidityEnd() == '0000-00-00 00:00:00')) {
        $validityPeriod = "";
        $displayPeriod = 'none';
		$FirstDate = date('Y');
    } else {
        $validityPeriod = 'checked="checked"';
        $displayPeriod = 'block';
		$BeginDate = DateTimeTools::dateExploder($OT->getValidityStart());
		$FirstDate = $BeginDate['year'];
    }
    $BeginDate = DateTimeTools::dateExploder($OT->getValidityStart());
    $BeginingDate = datetoDateWidgetRender(1, $BeginDate['day'],$BeginDate['month'],
            $BeginDate['year'], $disabled, $FirstDate);
    $BeginingTime = hourtoHourWidgetRender(1, $BeginDate['hour'],
            $BeginDate['mn'], $disabled);

    $EDate = DateTimeTools::dateExploder($OT->getValidityEnd());
    $EndDate = datetoDateWidgetRender(2, $EDate['day'], $EDate['month'],
            $EDate['year'], $disabled, $FirstDate);
    $EndTime = hourtoHourWidgetRender(2, $EDate['hour'], $EDate['mn'], $disabled);

    $duration = explode (':', $OT->getMaxDuration());
    $maxduration = "<input type='text' name='WidgetHour3' value='" .
        			$duration[0] . "' size='3' />" . _('h.');
    $maxduration .= "<input type='text' name='WidgetMin3' value='" .
        			$duration[1] . "' size='3' />" . _('m.');

    // assignation des variable Smarty
	$smarty->assign('readonly', $readOnly);
	$smarty->assign('disabled', $disabled);


    $Actor = $OT->getActor(); //Objet acteur
    if ($Actor instanceof Actor) {
        $selectedJobId = 0;
        $req = '?selectedActor=' . $Actor->getId();
        $JobCollection = $Actor->getJobCollection();
	    if ($JobCollection instanceof Collection && $JobCollection->getCount() > 0) {
	        $selectedJobId = $JobCollection->getItem(0)->getId();
			$req .= '&selectedJob=' . $selectedJobId;
	    }
    	$smarty->assign('actor', $Actor->getId());
    }

     // Nom de l'acteur selectionné
    // Id de l'acteur pour préselection dans la liste deroulante

    $smarty->assign('name', $OT->getName());
	$smarty->assign('detailButtonEnabled', 1);
    $smarty->assign('DetailLink',
        	'WorkOrderOpeTaskList.php?returnURL=WorkOrder.php&OtId=' . $OT->getId());
    $smarty->assign('comment', $OT->getComment()); //
    $smarty->assign('validityPeriod', $validityPeriod);

    $OT->getMaxVolume()==0?$smarty->assign('maxvol', ""):
            $smarty->assign('maxvol', $OT->getMaxVolume());
    $OT->getMaxLM()==0?$smarty->Assign('maxLM', ""):
            $smarty->assign('maxLM', $OT->getMaxLM());
    $OT->getMaxWeigth()==0?$smarty->assign('maxweigth', ""):
            $smarty->assign('maxweigth', $OT->getMaxWeigth());
    $OT->getMaxDistance()==0?$smarty->Assign('maxdistance', ""):
            $smarty->assign('maxdistance', $OT->getMaxDistance());
    $smarty->assign('Id', $_REQUEST['OTid']);
}  // edit

// sert a conserver les saisies en cas de retour apres erreur
for ($i=0; $i<count($fieldNameArray); $i++) {
	if (isset($_SESSION[$fieldNameArray[$i]])) {
	    $smarty->assign($fieldNameArray[$i], $_SESSION[$fieldNameArray[$i]]);
	}

}

// Construction du select sur les Jobs
$jobColl = Job::getJobWhitchHasActorsCollection();
$jobOptions = FormTools::writeOptionsFromCollection($jobColl);
$smarty->assign('jobOptions', implode("\n\t", $jobOptions));

$smarty->assign('titre', $titre);
$smarty->assign('formaction', $_SERVER['PHP_SELF']);
$smarty->assign('action', $action);
$smarty->assign('CancelLink', 'WorkOrderList.php');
$smarty->assign('displayPeriod', $displayPeriod);
$smarty->assign('BeginingDate', $BeginingDate);
$smarty->assign('BeginingTime', $BeginingTime);
$smarty->assign('EndDate', $EndDate);
$smarty->assign('EndTime', $EndTime);
$smarty->assign('maxduration', $maxduration);

$jsArray = array('JS_AjaxTools.php',
		         'js/includes/WidgetDateTools.js',
                 'JS_WorkOrder.php' . $req
		         );

Template::page($titre, $smarty->fetch('WorkOrder/WorkOrder.html'), $jsArray);
?>
