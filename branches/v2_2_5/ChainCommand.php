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
require_once('Objects/CommandItem.inc.php');
require_once('Objects/ChainCommand.php');
require_once('Objects/ChainCommandItem.php');
require_once('Objects/TVA.inc.php');
require_once('Objects/Incoterm.php');
require_once('Objects/Site.php');
require_once('Objects/Command.const.php');
require_once('Objects/Customer.php');
require_once('Objects/Supplier.php');
require_once('Objects/SupplierCustomer.php');
require_once('ProductCommandTools.php');
require_once('BoxTools.php');
require_once('LangTools.php');
require_once('FormatNumber.php');

// }}}
// messages d'erreur et constantes diverses {{{

define('E_CHAINID_MISSING', _('Please select a chain'));
define('E_CHAIN_NOT_FOUND', _('Chain was not found in the database'));
define('E_CHAIN_INVALID', _('Selected chain "%s" is not valid.'));
define('E_CANCEL_CONFIRM', _('Are you sure you want to cancel the order ?'));
define('PAGE_TEMPLATE', 'Command/ChainCommand.html');
define('PAGE_TITLE', _('Carriage order.'));
define('ACTION_ADD', 0);
define('ACTION_DEL', 1);
define('TVA', 19.6); // ### FIXME: TVA
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'TransportChainList.php';
// XXX Pour l'instant la devise de la commande est toujours l'euro
$currencyMapper = Mapper::singleton('Currency');
$currency = $currencyMapper->load(array('Id'=>1));

// }}}
// Authentification: restriction d'accès et session {{{

$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_CLIENT_TRANSPORT,UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_DIR_COMMERCIAL),
    array('showErrorDialog' => true)
);
$session = Session::singleton();

// }}}
// Traitement de la chaine séléctionnée {{{

$chnId = 0;
if(isset($_REQUEST['chnId'])) {
    $chnId = $_REQUEST['chnId'];
} elseif (isset($_SESSION['chnId'])) {
    $chnId = $_SESSION['chnId'];
} 
// on essaie de charger la chîne correspondante
$chainMapper = Mapper::singleton('Chain');
$chain = $chainMapper->load(array('Id'=>$chnId));
if (!($chain instanceof Chain)) {
    Template::errorDialog(E_CHAIN_NOT_FOUND, $retURL);
    exit(1);
}
$session->register('chnId', $chnId, 2);

// }}}
// l'utilisateur a cliqué sur "Valider" {{{

if (isset($_POST['FormSubmitted']) && $_POST['FormSubmitted'] == 1) {
    $redirect = 'TransportChainList.php';
    if (isset($_REQUEST['isEstimate'])) {
        $redirect .= '?editEstimate=1&estId='.$_SESSION['chainCommandId'];
    }
    Template::infoDialog(
        sprintf(
            I_COMMAND_OK,
            isset($_REQUEST['isEstimate']) ? _('estimate') : _('order'),
            $_SESSION['chainCommandNo']
        ), 
        $redirect
    );
    exit();
}

// }}}
// Traitement du template smarty {{{
$smarty = new Template();

// variables smarty simples
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('ReturnURL',  $retURL);
$smarty->assign('Currency', $currency->getSymbol());
$smarty->assign('IsAdmin', in_array($auth->getProfile(), 
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_DIR_COMMERCIAL)));
if (isset($_REQUEST['isEstimate'])) {
    $smarty->assign('isEstimate', 1);
}

// widget incoterm
$incOptions = FormTools::writeOptionsFromObject('Incoterm', 0,
    array(), array('Label'=>SORT_ASC), 'toString', array('Code', 'Label'));
$smarty->assign('IncotermOptions', implode("\n", $incOptions));

// client
$custArray = SearchTools::createArrayIDFromCollection(
    array('Customer', 'AeroCustomer'), array('Active'=>1));
$smarty->assign('CustomerOptions', implode("\n",
    FormTools::writeOptionsFromArray($custArray)));

// widgets expediteur, si l'acteur est générique on propose comme choix
// tous les acteurs de celui-ci, sinon le seul acteur dispo est celui de
// la chaine
$ast  = $chain->getSiteTransition();
if (!($ast instanceof ActorSiteTransition)) {
    Template::errorDialog(sprintf(
        E_CHAIN_INVALID,
        _("Departure and arrival actors are missing.")
    ));
    exit(1);
}
$chainExpeditor = $ast->getDepartureActor();
if (!($chainExpeditor instanceof Actor)) {
    Template::errorDialog(sprintf(
        E_CHAIN_INVALID,
        _("Departure actor is missing.")
    ));
    exit(1);
}
$criteria = $chainExpeditor->isGeneric()?'GenericActor':'Id';
$filter = array($criteria=>$chainExpeditor->getId(), 'Active'=>1);
$expOptions = FormTools::writeOptionsFromObject(
    'Actor', 0, $filter, array('Name'=>SORT_ASC)
);
$smarty->assign("ExpOptions", implode("\n", $expOptions));

// widgets destinataire, si l'acteur est générique on propose comme choix
// tous les acteurs de celui-ci, sinon le seul acteur dispo est celui de
// la chaine
$chainDestinator = $ast->getArrivalActor();
if (!($chainDestinator instanceof Actor)) {
    Template::errorDialog(sprintf(
        E_CHAIN_INVALID,
        _("arrival actor is missing.")
    ));
    exit(1);
}
$criteria = $chainDestinator->isGeneric()?'GenericActor':'Id';
$filter = array($criteria=>$chainDestinator->getId(), 'Active'=>1);
$destOptions = FormTools::writeOptionsFromObject(
    'Actor', $chainDestinator->getId(), $filter, array('Name'=>SORT_ASC)
);
$smarty->assign('DestOptions', implode("\n", $destOptions));

// date de début et de fin
restoreDates($smarty, 0, time(), 0);

// }}}
// affichage du template {{{

$js = array(
    'js/lib-functions/FormatNumber.js',
    'js/includes/ChainCommand.js',
    'js/jscalendar/calendar.js',
    getJSCalendarLangFile(),
    'js/jscalendar/calendar-setup.js'
);

$content = $smarty->fetch(PAGE_TEMPLATE);
Template::ajaxPage(PAGE_TITLE, $content, $js);

// }}}

?>
