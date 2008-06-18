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

$pageTitle = _('Add or update set');

SearchTools::ProlongDataInSession();


if (isset($_REQUEST['cgrId']) && $_REQUEST['cgrId'] > 0) {
	$ComponentGroup = Object::load('ComponentGroup', $_REQUEST['cgrId']);
}else if (isset($_SESSION['ComponentGroup'])) {
    $ComponentGroup = $_SESSION['ComponentGroup'];
} else {
    $ComponentGroup = new ComponentGroup();
}

// Si le ComponentGroup est dans la collection, il faut prendre celui là
$cgrId = $ComponentGroup instanceof ComponentGroup?$ComponentGroup->getId():$_REQUEST['cgrId'];
$Nomenclature = $_SESSION['Nomenclature'];
$ComponentGroupCollection = $Nomenclature->getComponentGroupCollection();
$count = $ComponentGroupCollection->getCount();
for($i=0; $i<$count; $i++){
    $item = $ComponentGroupCollection->getItem($i);
    if ($item->getId() == $cgrId) {
        $ComponentGroup = $item;
        break;
    }
}

if (!($ComponentGroup instanceof ComponentGroup)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'NomenclatureAddEdit.php');
  	exit;
}

$session->register('ComponentGroup', $ComponentGroup, 2);

/**
 * Servira à déterminer s'il s'agit d'un ajout, auquel cas, 
 * il faudra l'ajouter aux ComponentGroup de la Nomenclature 
 **/
$isNewComponentGroup = $ComponentGroup->getId() == 0;

//  Si on a clique sur OK apres saisie  ou confirme la saisie
if (isset($_REQUEST['formSubmitted'])) {
	// Modification ou Creation: on ne sauve rien en base ici!
	if (!isset($ComponentGroup)) {
	    $ComponentGroup = Object::load('ComponentGroup');
	}
	FormTools::autoHandlePostData($_REQUEST, $ComponentGroup, 'ComponentGroup');
	
	// Verification des saisies
	$ComponentGroupMapper = Mapper::singleton('ComponentGroup');
	$testComponentGroup = $ComponentGroupMapper->load(
			array('Nomenclature' => $Nomenclature->getId(),
				  'Name' => $_REQUEST['ComponentGroup_Name']));
	
	// Permet en cas de retour sur le form, de conserver les saisies		  
	$query = UrlTools::buildURLFromRequest(array('cgrId', 'ComponentGroup_Name'));
						   	  
	if ($testComponentGroup instanceof ComponentGroup 
			&& $testComponentGroup->getId() != $ComponentGroup->getId() && !isset($_REQUEST['ok'])) {
	    Template::errorDialog(
		    _('A set with this designation already exists for this nomenclature, please correct.'), 
			basename($_SERVER['PHP_SELF']) . '?' . $query);
    	exit;
	}

	if ($isNewComponentGroup) {
        $ComponentGroup->setId($ComponentGroupMapper->generateId());
    }
	else {
		$count = $ComponentGroupCollection->getCount();
		for($i=0; $i<$count; $i++) {
		    $item = $ComponentGroupCollection->getItem($i);
		    if ($item instanceof ComponentGroup && ($item->getId() == $ComponentGroup->getId())) {
		        $ComponentGroupCollection->removeItem($i);
				break;
		    }
		}
	}
	$ComponentGroupCollection->setItem($ComponentGroup);
    unset($_SESSION['ComponentGroup']);

	Tools::redirectTo('NomenclatureAddEdit.php?' . SID);
	exit;
}

/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('ComponentGroupAddEdit', 'post');

$form->addElement('text', 'ComponentGroup_Name', 'Désignation *', 'style="width:100%"');
$form->addElement('hidden', 'cgrId', isset($_REQUEST['cgrId'])?$_REQUEST['cgrId']:0);

/*  Si Edition d'un ComponentGroup existant  */
if (isset($ComponentGroup)) {
	$defaultValues = FormTools::getDefaultValues($form, $ComponentGroup);
}
else {
	$defaultValues = SearchTools::createDefaultValueArray();
}
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut

/*  Validation du formulaire */
$form->addRule('ComponentGroup_Name', 
			   _('Please provide a designation.'), 'required', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());

$pageContent = $smarty->fetch('Nomenclature/ComponentGroupAddEdit.html');

Template::page($pageTitle, $pageContent);

?>
