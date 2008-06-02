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
require_once('Objects/CustomerAttractivity.php');
require_once('HTML/QuickForm.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('CustomerAttractivityAddEditTools.php');
// }}}

// auth et url de retour {{{
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$retURL = isset($_REQUEST['retURL'])?
        $_REQUEST['retURL']:'CustomerAttractivityList.php';
$confirmMsg = _('Categories %s already have an attractivity level, do you want to modify it ?');
$pageTitle = _('Add or update attractivity level.');
// }}}


// déterminer le mode ajout ou édition {{{
if (isset($_REQUEST['cuaID']) && $_REQUEST['cuaID'] > 0) {
    $cuaID = $_REQUEST['cuaID'];
    $mapper = Mapper::singleton('CustomerAttractivity');
    $cua = $mapper->load(array('Id'=>$cuaID));
    // l'entité n'a pu être chargée...
    if (!($cua instanceof CustomerAttractivity)) {
        Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
        exit(1);
    }
    $initCatIds = $cua->getCategoryCollectionIds();
} else {
    $cuaID = 0;
    $cua = new CustomerAttractivity();
}
// }}}

// Traitement après soumission du formulaire {{{
if (isset($_REQUEST['formSubmitted'])) {
    if (!isset($_REQUEST['ok'])) {
        $catIds = isset($_REQUEST['CustomerAttractivity_CategoryCollection']) ?
            $_REQUEST['CustomerAttractivity_CategoryCollection'] : array();
        if (!is_array($catIds)) {
            $catIds = array($catIds);
        } else if (isset($catIds[0]) && $catIds[0] == '##') {
            $catIds = array();
        }
        // Vérifie si les catégories selectionnées n'ont pas déjà une
        // attractivité assignée. Si oui on propose à l'utilisateur de confirmer
        // la mise à jour de l'attractivité.
        $catNames = checkCategories($catIds, $cuaID);
        if (!empty($catNames)) {
            $okURL = $_SERVER['PHP_SELF'].'?ok=1&'.UrlTools::buildURLFromRequest();
            $noURL = $_SERVER['PHP_SELF'] . '?cuaID=' . $cuaID;
            Template::confirmDialog(sprintf($confirmMsg, $catNames), $okURL, $noURL);
            exit;
        }

    }
    Database::connection()->startTrans();
    FormTools::autoHandlePostData($_REQUEST, $cua);
    saveInstance($cua, $retURL);
    // gestion des catégories liées
    foreach ($catIds as $catId) {
        $cat = Object::load('Category', $catId);
        $cat->setAttractivity($cua->getId());
        saveInstance($cat, $retURL);
    }
    // On desaffecte des categories si necessaire, en mode edition seulement
    if ($cuaID > 0) {
        $oldCategoryIds = array_diff($initCatIds, $catIds);
        foreach ($oldCategoryIds as $catId) {
            $cat = Object::load('Category', $catId);
            $cat->setAttractivity(null);
            saveInstance($cat, $retURL);
        }
    }

    if (Database::connection()->hasFailedTrans()) {
        $err = Database::connection()->errorMsg();
        trigger_error($err, E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_SQL . '.<br/>' . $err, $retURL);
        exit;
    }
    Database::connection()->completeTrans();
    Tools::redirectTo($retURL);
    exit;
}
// }}}


// traitement du template avec quickform {{{
$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('CustomerAttractivityAddEdit', 'post');
$form->addElement('hidden', 'retURL', $retURL);
$form->addElement('hidden', 'cuaID', $cuaID);
$form->addElement('hidden', 'formSubmitted', 1);
$form->addElement('text', 'CustomerAttractivity_Name',
        _('Name'), 'style="width:100%;"');
$form->addElement('text', 'CustomerAttractivity_Level',
        _('Attractivity level'), 'style="width:100%;"');
$catArray = SearchTools::createArrayIDFromCollection('Category', array(),
        _('Select one or more categories'), 'Name',
        array('Name'=>SORT_ASC));
$form->addElement('select', 'CustomerAttractivity_CategoryCollection',
        _('Categories matching this attractivity level'),
        $catArray, 'style="width:100%;" multiple size="5"');
// }}}

// valeurs par défaut pour le mode édition {{{
$defaultValues = array();
if ($cua->getId() > 0) {
    $defaultValues['CustomerAttractivity_Name'] = $cua->getName();
    $defaultValues['CustomerAttractivity_Level'] = $cua->getLevel();
    $defaultValues['CustomerAttractivity_CategoryCollection'] =
        $cua->getCategoryCollectionIds();
} else {
    $defaultValues['CustomerAttractivity_Level'] = 1;
}
$form->setDefaults($defaultValues);
// }}}

// validations côté client {{{
$form->addRule('CustomerAttractivity_Name', _('Please provide a name.'),
        'required', '', 'client');
$form->addRule('CustomerAttractivity_Level',
        _('Level must be an integer.'), 'numeric', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));
// }}}

// affichage de la page {{{
$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);
$pageContent = $smarty->fetch('Customer/CustomerAttractivityAddEdit.html');
Template::page($pageTitle, $pageContent);
// }}}

?>
