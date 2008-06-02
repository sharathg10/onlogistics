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

$auth = Auth::singleton();
$auth->checkProfiles();

$retURL = isset($_REQUEST['retURL']) ? $_REQUEST['retURL']:'NomenclatureList.php';

if(!isset($_REQUEST['nomID'])) {
    Template::errorDialog(I_NEED_SINGLE_ITEM, $retURL);
    exit();
}
$object = Object::load('Nomenclature', $_REQUEST['nomID']);
if (isset($_POST['ok'])) {
    Database::connection()->startTrans();
    $newNom = Tools::duplicateObject($object);
    $newNom->setVersion($_POST['Nomenclature_Version']);
    $newNom->setProduct($_POST['Nomenclature_Product_ID']);
    $newNom->setBeginDate(DateTimeTools::QuickFormDateToMySQL(
        'Nomenclature_BeginDate') . ' 00:00:00');
    $newNom->setEndDate(DateTimeTools::QuickFormDateToMySQL(
        'Nomenclature_EndDate') . ' 23:59:59');

    // check
    if($object->getVersion() == $newNom->getVersion() 
        || ($object->getBeginDate() == $newNom->getBeginDate() 
        && $object->getEndDate() == $newNom->getEndDate())) {
        Template::errorDialog(_('You must provide new version and dates for the new nomenclature'),
            'NomenclatureDuplicate.php?nomID=' . $object->getId());
        exit();
    }

    // copie des Component
    $componentsCol = $object->getComponentCollection(array('Level'=>0));
    foreach($componentsCol as $component) {
        $component->duplicate($newNom->getId(), $retURL);
    }
    
    // copie des ComponentGroup
    $componentGroupCol = $object->getComponentGroupCollection();
    foreach ($componentGroupCol as $componentGroup) {
        $newCompGroup = Tools::duplicateObject($componentGroup);
        $newCompGroup->setNomenclature($newNom->getId());
        saveInstance($newCompGroup, $retURL);
    }

    saveInstance($newNom, $retURL);

    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
    }
    Database::connection()->completeTrans();
    Tools::redirectTo($retURL);
    
}
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('NomenclatureAddEdit', 'post');

$smarty->assign('formAction', $_SERVER['PHP_SELF']);
$smarty->assign('nomID', $object->getId());
$smarty->assign('retURL', $retURL);

$ProductArray = SearchTools::createArrayIDFromCollection('Product', array(), '', 'BaseReference');
$form->addElement('select', 'Nomenclature_Product_ID', _('Related product') . ' *',
        $ProductArray, 'style="width:100%"');
$form->addElement('text', 'Nomenclature_Version', _('Version') . ' *', 'style="width:100%"');

$dateFormat = array();
$dateFormat['format']  = I18N::getHTMLSelectDateFormat();
$dateFormat['minYear'] = date('Y') - 2;
$dateFormat['maxYear'] = date('Y') + 10;

$form->addElement('date', 'Nomenclature_BeginDate', _('Validity begin date'), $dateFormat);
$form->addElement('date', 'Nomenclature_EndDate', _('Validity end date'), $dateFormat);

$defaultValues = FormTools::getDefaultValues($form, $object);
$BeginDate = DateTimeTools::DateExploder($object->getBeginDate());
$EndDate = DateTimeTools::DateExploder($object->getEndDate());
$defaultValues['BeginDate'] = array('d'=>$BeginDate['day'],
    'm'=>$BeginDate['month'], 'Y'=>$BeginDate['year']);
$defaultValues['EndDate'] = array('d'=>$EndDate['day'],
    'm'=>$EndDate['month'], 'Y'=>$EndDate['year']);
$form->setDefaults($defaultValues);

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$content = $smarty->fetch('Nomenclature/NomenclatureDuplicate.html');

$title = _('Nomenclature copy');
Template::page($title, $content);
?>
