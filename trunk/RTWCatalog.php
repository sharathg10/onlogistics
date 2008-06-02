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

require_once 'config.inc.php';
require_once 'RTWProductManager.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

$auth = Auth::singleton();
$auth->checkProfiles();
$profileId = $auth->getProfile();
$actorId = $auth->getActorId();

// initialize les vars de session
$_SESSION['customer'] = 0;
$_SESSION['pdt'] = array();
$_SESSION['qty'] = array();

// Gestion de l'edition du devis si necessaire
// ouverture d'un popup en arriere-plan, impression du contenu (pdf), et fermeture de ce popup
if (isset($_REQUEST['editEstimate']) && isset($_REQUEST['estId'])) {
	$editEstimate = "
	<SCRIPT language=\"javascript\">
	function kill() {
		window.open(\"KillPopup.html\",'popback','width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no');
	}
	function TimeToKill(sec) {
		setTimeout(\"kill()\",sec*1000);
	}
	var w=window.open(\"EstimateEdit.php?estId=" . $_REQUEST['estId']
        . "\",\"popback\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
	w.blur();
	TimeToKill(12);
	</SCRIPT>";
} else {
    $editEstimate = '';
}

// Si reinitialisation de toute la commande
if (isset($_REQUEST['new'])) {
    SearchTools::cleanDataSession('noPrefix');
}

//  Pour la liste de selection des clients
$disabled = '';
if ($profileId == UserAccount::PROFILE_CUSTOMER || $profileId == UserAccount::PROFILE_OWNER_CUSTOMER) {
    $customerFilter = array('Id' => $actorId, 'Active' => 1);
    $disabled = 'disabled';
} else if ($profileId == UserAccount::PROFILE_COMMERCIAL) {
    $customerFilter = array('Commercial' => $auth->getUserId(), 'Active' => 1);
} else {
    $customerFilter = array('Active' => 1);
}

/*  Contruction du formulaire de recherche */
$tpl = new Template();
$form = new HTML_QuickForm('RTWModel');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($tpl);

$customerArray = SearchTools::createArrayIDFromCollection(array('Customer', 'AeroCustomer'), $customerFilter);
$form->addElement('select', 'Customer', _('Customer'), $customerArray, 'style="width:100%;"');

$modelArray = SearchTools::createArrayIDFromCollection('RTWModel', array(), _('Select a model'));
$form->addElement('select', 'Model', _('Model'), $modelArray, 'style="width:100%" onchange="this.form.submit();"');

if (isset($_POST['Order']) || isset($_POST['Estimate'])) {
    $_SESSION['customer'] = $_POST['Customer'];
    foreach ($_POST['pdt'] as $id) {
        $qty = $_POST['qty_'.$id];
        if (is_numeric($qty) && $qty > 0 && !in_array($id, $_SESSION['pdt'])) {
            $_SESSION['pdt'][] = $id;
            $_SESSION['qty'][] = $qty;
        }
    }
    session_write_close();
    $url = 'ProductCommand.php?from=rtwcatalog';
    if (isset($_POST['Estimate'])) {
        $url .= '&isEstimate=1';
    }
    Tools::redirectTo($url);
    exit(0);
}
if (isset($_POST['Model']) && $_POST['Model'] !== '##') {
    $names   = array(
        'Material1'  => _('Material 1'),
        'Material2'  => _('Material 2'),
        'Accessory1' => _('Accessory 1'),
        'Accessory2' => _('Accessory 2'),
    );
    $model   = Object::load('RTWModel', $_POST['Model']);
    foreach ($names as $k=>$v) {
        $getter = "get{$k}Collection";
        $col = $model->$getter();
        if (count($col) > 0) {
            $form->addElement('select', $k, $v, $col->toArray(), 'style="width:100%";"');
        }
    }
    if (isset($_POST['Search'])) {
        $postData = array();
        foreach ($names as $k=>$v) {
            if (isset($_POST[$k])) {
                $postData[$k] = $_POST[$k];
            }
        }
        $col = RTWProductManager::getOrCreateProducts($model, $postData);
        $tpl->assign('Products', $col->toArray('getBaseReference'));
    }
}

$form->accept($renderer);
$tpl->assign('form', $renderer->toArray());

// on n'affiche que le formulaire de recherche, pas le Grid
Template::page('', $tpl->fetch('Catalog/RTWCatalog.html'));

?>
