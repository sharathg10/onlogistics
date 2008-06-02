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

$abdID = isset($_REQUEST['abdID'])?$_REQUEST['abdID']:0;
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'BankList.php';

if($abdID == 0) {
    // Affiche la liste des banques
    $grid = new Grid();
    $grid->withNoCheckBox = true;

    $grid->newColumn('FieldMapper', _('Name'),
        array('Macro' => '<a href="BankList.php?abdID=%ID%">%BankName%</a>',
              'SortField' => 'Name'));
    $grid->newColumn('FieldMapper', _('Number'), array('Macro' => '%AccountNumber%'));

    Template::pageWithGrid($grid, 'ActorBankDetail', '', array('Actor.DatabaseOwner'=>1),
        array('BankName'=>SORT_ASC));
}
else {
    // Affiche le formulaire
    require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
    require_once('HTML/QuickForm.php');

    $abd = Object::load('ActorBankDetail', $abdID);

    if (isset($_POST['Ok'])) {
        FormTools::autoHandlePostData($_POST, $abd);
        Database::connection()->startTrans();
        saveInstance($abd, $retURL);
        //  Commit de la transaction
        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
            Database::connection()->rollbackTrans();
        }
        Database::connection()->completeTrans();
        Tools::redirectTo($retURL);
        exit;
    }

    $smarty = new Template();
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form = new HTML_QuickForm('BankList', 'post');

    // champs cachés
    $form->addElement('hidden', 'retURL', $retURL);
    $form->addElement('hidden', 'abdID', $abdID);

    $form->addElement('text', 'ActorBankDetail_Amount', _('Amount'), 'style="width:100%;"');
    $form->addElement(
        'select', 'ActorBankDetail_Currency_ID', _('Currency'),
        SearchTools::createArrayIDFromCollection('Currency', array(), '', 'ShortName'),
        'style="width:100%;"');

    $defaultValues['ActorBankDetail_Currency_ID'] = $abd->getCurrencyId();
    $defaultValues['ActorBankDetail_Amount'] = $abd->getAmount();
    $form->setDefaults($defaultValues);

    $form->accept($renderer);
    $smarty->assign('form', $renderer->toArray());
    $smarty->assign('retURL', $retURL);

    $pageTitle = $abd->getBankName() . ' - ' . $abd->getAccountNumber();
    $pageContent = $smarty->fetch('BankList.html');

    Template::page($pageTitle, $pageContent);
}
?>
