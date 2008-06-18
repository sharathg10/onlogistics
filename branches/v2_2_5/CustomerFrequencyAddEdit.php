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

define('E_FREQUENCY_EXISTS',
    _('A frequency is already defined for this attractivity and this potential.'));

// dépendences {{{
require_once('config.inc.php');
require_once('Objects/CustomerFrequency.php');
require_once('HTML/QuickForm.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
// }}}

// auth, url de retour et activation sajax {{{
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$retURL = isset($_REQUEST['retURL'])?
        $_REQUEST['retURL']:'CustomerFrequencyList.php';
// }}}

// déterminer le mode ajout ou édition {{{
$mapper = Mapper::singleton('CustomerFrequency');
if (isset($_REQUEST['cufID']) && $_REQUEST['cufID'] > 0) {
    $cufID = $_REQUEST['cufID'];
    $cuf = $mapper->load(array('Id'=>$cufID));
    // l'entité n'a pu être chargée...
    if (!($cuf instanceof CustomerFrequency)) {
        Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
        exit(1);
    }
} else {
    $cufID = 0;
    $cuf = new CustomerFrequency();
}
// }}}

// traitement après soumission du formulaire {{{
if (isset($_POST['Ok'])) {
    Database::connection()->startTrans();
    // il faut formatter les dates
    // Les 'if sont au cas ou les widgets soient grises
    if (isset($_POST['CustomerFrequency_BeginDate'])) {
        $_POST['CustomerFrequency_BeginDate'] = DateTimeTools::QuickFormDateToMySQL(
            'CustomerFrequency_BeginDate');
    }
    if (isset($_POST['CustomerFrequency_EndDate'])) {
        $_POST['CustomerFrequency_EndDate'] = DateTimeTools::QuickFormDateToMySQL(
            'CustomerFrequency_EndDate');
    }
    FormTools::autoHandlePostData($_POST, $cuf);
    saveInstance($cuf, $retURL);
    // on vérifie si une fréquence n'esiste pas déjà avec le couple
    // attractivité/potentiel sélectionné
    $exist = $mapper->load(
        array(
            'Attractivity'=>$_POST['CustomerFrequency_Attractivity_ID'],
            'Potential'=>$_POST['CustomerFrequency_Potential_ID']
        )
    );
    if ($exist instanceof Frequency && $exist->getId() != $cuf->getId()) {
        Template::errorDialog(E_FREQUENCY_EXISTS, $retURL);
        exit(1);
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
    exit(0);
}
// }}}

// traitement du template avec quickform {{{
$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('CustomerFrequencyAddEdit', 'post');
$form->addElement('hidden', 'retURL', $retURL);
$form->addElement('hidden', 'cufID', $cufID);
$form->addElement('text', 'CustomerFrequency_Name',
    _('Name'), 'style="width:100%;"');
$cuaArray = SearchTools::createArrayIDFromCollection('CustomerAttractivity', array(),
    '', 'Name', array('Name'=>SORT_ASC));
$form->addElement('select', 'CustomerFrequency_Attractivity_ID',
    _('Attractivity level'), $cuaArray, 'style="width:100%;"');
$cupArray = SearchTools::createArrayIDFromCollection('CustomerPotential', array(),
    '', 'Name', array('Name'=>SORT_ASC));
$form->addElement('select', 'CustomerFrequency_Potential_ID',
    _('Potential'), $cupArray,
    'style="width:100%;" onchange="updatePotentialWidgets();" id="potentialID"');
$form->addElement('text', 'CustomerFrequency_Frequency',
    _('Frequency (weeks)'));
$form->addElement('select', 'CustomerFrequency_Type',
    _('Frequency type'), CustomerFrequency::getTypeConstArray(),
    'style="width:100%;" onchange="updateDateWidgetsState();"');
$dateFormat = array();
$dateFormat['format']  = I18N::getHTMLSelectDateFormat();
$dateFormat['minYear'] = date('Y');
$dateFormat['maxYear'] = date('Y') + 10;
$form->addElement('date', 'CustomerFrequency_BeginDate', _('Beginning'), $dateFormat);
$form->addElement('date', 'CustomerFrequency_EndDate', _('End'), $dateFormat);
// }}}

// valeurs par défaut pour le mode édition {{{
$defaultValues = array();
if ($cuf->getId() > 0) {
    $defaultValues = array();
    $defaultValues['CustomerFrequency_Name'] = $cuf->getName();
    $defaultValues['CustomerFrequency_Frequency'] = $cuf->getFrequency();
    $defaultValues['CustomerFrequency_Potential_ID'] = $cuf->getPotentialId();
    $defaultValues['CustomerFrequency_Attractivity_ID'] =
        $cuf->getAttractivityId();
    $defaultValues['CustomerFrequency_Type'] = $cuf->getType();
    // XXX cela risque de pas marcher avec d'autres locales (cf, "dMY"
    // paramétrable), mais bon vu qu'il y en a un peu partout dans le code...
	$beginDate = DateTimeTools::DateExploder($cuf->getBeginDate());
	$endDate = DateTimeTools::DateExploder($cuf->getEndDate());
    $defaultValues['CustomerFrequency_BeginDate'] = array(
        'd'=>$beginDate['day'],
        'm'=>$beginDate['month'],
        'Y'=>$beginDate['year']
    );
    $defaultValues['CustomerFrequency_EndDate'] = array(
        'd'=>$endDate['day'],
        'm'=>$endDate['month'],
        'Y'=>$endDate['year']
    );
    $form->setDefaults($defaultValues);
}
// }}}

// validations côté client {{{
$form->addRule('CustomerFrequency_Name', _('Please provide a name.'),
    'required', '', 'client');
$form->addRule('CustomerFrequency_Frequency',
     _('Please provide a frequency.'), 'required', '', 'client');
$form->addRule('CustomerFrequency_Frequency',
    _('Frequency must be an integer (number of weeks).'),
    'numeric', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));
// }}}

// affichage de la page {{{
$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);
$pageTitle = _('Add or update visit frequency.');
$pageContent = $smarty->fetch('Customer/CustomerFrequencyAddEdit.html');
$js = array('JS_AjaxTools.php', 'js/includes/CustomerFrequencyAddEdit.js');
Template::page($pageTitle, $pageContent, $js);
// }}}

?>