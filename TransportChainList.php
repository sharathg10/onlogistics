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
require_once('Objects/Chain.php');

$auth = Auth::Singleton();
$auth->checkProfiles();

//Database::connection()->debug = true;
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

// Démarrage de session
$session = Session::singleton();
// cleanage des infos de session si besoin
unset($_SESSION['chain'], $_SESSION['chainCommand']);

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'home.php?';

$form = new SearchForm('Chain');
$form->addElement('text', 'Reference', _('Reference'));
$form->addElement('text', 'Description', _('Designation'));

if (true === $form->displayGrid()) {
    $grid = new Grid();
    $grid->itemPerPage = 30;
    $macro = in_array($auth->getProfile(), array(
        UserAccount::PROFILE_ADMIN,
        UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_ADMIN_VENTES,
        UserAccount::PROFILE_AERO_ADMIN_VENTES,
        UserAccount::PROFILE_CLIENT_TRANSPORT)
    ) ? '<a href="ChainCommand.php?chnId=%Id%" title="' 
        . _('Order') . '">%Reference%</a>' : '%Reference%';
    $grid->NewColumn('FieldMapper', _('Reference'),
		array('Macro' => $macro));
    $grid->NewColumn('FieldMapper', _('Description'),
        array('Macro' => '%Description%'));
    $grid->NewAction('Redirect',
        array(
            'Caption' =>_('Ask for estimate'),
            'Profiles' => array(
                UserAccount::PROFILE_ADMIN,
                UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_ADMIN_VENTES,
                UserAccount::PROFILE_AERO_ADMIN_VENTES
            ),
            'URL'=>'ChainCommand.php?chnId=%d&isEstimate=1'
        )
    );
    $grid->NewAction('Redirect',
        array(
            'Caption' =>_('Order'),
            'Profiles' => array(
                UserAccount::PROFILE_ADMIN,
                UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_ADMIN_VENTES,
                UserAccount::PROFILE_AERO_ADMIN_VENTES,
                UserAccount::PROFILE_CLIENT_TRANSPORT
            ),
            'URL'=>'ChainCommand.php?chnId=%d'
        )
    );

    $filterArray = array_merge(
        array(
            SearchTools::NewFilterComponent('Type', '', 'In', array(
                Chain::CHAIN_TYPE_TRANSPORT,
                Chain::CHAIN_STATE_BUILT), true)
        ),
        $form->buildFilterComponentArray()
    );
    $filter = SearchTools::filterAssembler($filterArray);
    $order = array('Reference' => SORT_ASC);
    $form->displayResult($grid, true, $filter, $order, '', array(),
        array('beforeForm' => $editEstimate)
    );
} else {
    Template::page('', $editEstimate . $form->render() . '</form>');
}


?>
