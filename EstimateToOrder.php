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
require_once 'ProductCommandTools.php';
require_once 'LangTools.php';
require_once 'CommandManager.php';
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
require_once 'HTML/QuickForm.php';

//Database::connection()->debug = true;

$auth = Auth::singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_ADMIN_VENTES,
    UserAccount::PROFILE_AERO_ADMIN_VENTES,
    UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW
));

SearchTools::prolongDataInSession();

$retURL = isset($_REQUEST['retURL']) ? $_REQUEST['retURL'] : 'EstimateList.php';

if (!isset($_REQUEST['estId'])) {
    Template::errorDialog(I_NEED_SINGLE_ITEM, $retURL);
    exit(1);
}

$est = Object::load('Command', $_REQUEST['estId']);

// check si c'est bien un devis
if (!($est instanceof Command) || !$est->getIsEstimate()) {
    Template::errorDialog(_('Selected item is not an estimate.'), $retURL);
    exit(1);
}
// check si le devis a pas déjà donné lieu a une commande
if (($c = $est->getCommand()) instanceof Command) {
    Template::errorDialog(
        sprintf(
            _('Selected estimate has already given rise to order "%s".'),
            $c->getCommandNo()
        ),
        $retURL
    );
    exit(1);
}
// check sur la durée de validité
if (($days = Preferences::get('EstimateValidityDays', 0)) > 0) {
    $estTs = DateTimeTools::mySQLDateToTimeStamp($est->getCommandDate());
    if (($estTs + (DateTimeTools::ONE_DAY * $days)) < time()) {
        Template::errorDialog(
            sprintf(
                _('Estimate "%s" is not valid anymore (%d days passed since it was issued).'),
                $est->getCommandNo(),
                $days
            ),
            $retURL
        );
        exit(1);
    }
}
// Selon si cmde de Produit ou pas, comportement different
if (get_class($est) == 'ProductCommand') {
    // Mise en session des donnees necessaires a un affichage du form
    $est->putDataInSessionForWebOrderForm();
    $url = ($est->getType() == Command::TYPE_CUSTOMER)?'ProductCommand.php':'ProductCommandSupplier.php';
    Tools::redirectTo($url . '?from=estimate');
    exit;
}



if (isset($_POST['commandButton'])) {
    Database::connection()->startTrans();
    $manager = new CommandManager(array(
        'CommandType'        => get_class($est),
        'ProductCommandType' => $est->getType(),
        'UseTransaction'     => false,
        'IsEstimate'         => false
    ));
    // on duplique la commande
    $command = Tools::duplicateObject($est);
    FormTools::autoHandlePostData($_POST, $command, 'Command');
    $command->setIsEstimate(false);
    $command->save();
    $estCmiCol = $est->getCommandItemCollection();
    $cmiCol = new Collection(get_class($est) . 'Item');
    foreach($estCmiCol as $estCmi) {
        $cmi = Tools::duplicateObject($estCmi);
        $cmi->setCommand($command);
        $cmi->save();
        $cmiCol->setItem($cmi);
    }
    $command->setCommandItemCollection($cmiCol);
    $command->save();
    $est->setCommand($command);
    $est->save();
    $manager->command = $command;
    // Activation du processus
    $result = $manager->activateProcess();
    Tools::handleException($result, $retURL);
    // validation de la commande
    if (get_class($command) == 'ProductCommand') {
        $result = $manager->validateCommand();
    } else {
        $result = $manager->validateChainCommand(false, true);
    }
    Tools::handleException($result, $retURL);
    Database::connection()->completeTrans();
    $msg = sprintf(I_COMMAND_OK . '.', _('order'), $command->getCommandNo());
    $hbr = $command->getHandingByRangePercent();
    if ($hbr > 0) {
        $msg .= "<br/>" . sprintf(I_COMMAND_HANDING, $hbr);
    }
    Template::infoDialog($msg, 'EstimateList.php');
    exit;
}

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm($_SERVER['PHP_SELF'], 'post');
$form->addElement('text', 'Command_CommandNo', _('Order number'),
    'style="width:100%"');
$form->addElement('hidden', 'estId', $_REQUEST['estId']);
$form->addElement('hidden', 'retURL', $retURL);
$form->addElement('hidden', 'CalendarAwareOfPlanning',
    Preferences::get('CalendarAwareOfPlanning', 0),
    'id="CalendarAwareOfPlanning"');
$form->setDefaults(array(
    'Command_CommandNo' => substr($est->getCommandNo(), 2) 
));
$smarty = new Template();
// date de début et de fin
restoreDates($smarty, $est->getWishedEndDate()>0, $est->getWishedStartDate(), $est->getWishedEndDate());
$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);
$content = $smarty->fetch('Command/EstimateToOrder.html');
$JSRequirements = array(
    'js/includes/ProductCommand.js',
    'js/jscalendar/calendar.js',
    getJSCalendarLangFile(),
    'js/jscalendar/calendar-setup.js'
);
Template::page(
    sprintf(_('Order for estimate number "%s"'), $est->getCommandNo()),
    $content,
    $JSRequirements
);

?>
