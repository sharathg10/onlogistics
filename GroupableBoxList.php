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
require_once('Objects/Task.const.php');

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    UserAccount::PROFILE_TRANSPORTEUR, UserAccount::PROFILE_GESTIONNAIRE_STOCK));

SearchTools::ProlongDataInSession(0, 3);

define('PAGE_TITLE', _('Parcels to regroup'));
define('PAGE_SUBTITLE', _('Please select the parcels you want to regroup then click the "Regroup" button').'<br/><br/>');
define('ITEMS_PER_PAGE', 100000000); // tous les items

$retURL = 'GroupableBoxActivatedChainTaskList.php';

if (!isset($_REQUEST['ackIDs'])) {
    Tools::redirectTo($retURL);
    exit;
}
if (!is_array($_REQUEST['ackIDs'])) {
    $_REQUEST['ackIDs'] = array($_REQUEST['ackIDs']);
}

$phpself = basename($_SERVER['PHP_SELF']) . '?ackIDs[]=' .
    implode('&ackIDs[]=', $_REQUEST['ackIDs']);

// construction du grid
$grid = new Grid();
$grid->itemPerPage = ITEMS_PER_PAGE;
$grid->paged = false;
$grid->displayCancelFilter = false;
// colonnes
$grid->NewColumn('FieldMapper', _('Order'),
    array('Macro' => '%ActivatedChain.CommandItem()[0].Command.CommandNo%',
        'Sortable' => false));
$grid->NewColumn('FieldMapper', _('Reference'),
    array('Macro'=>'%Reference%', 'Sortable'=>false));
$grid->NewColumn('FieldMapper', _('Departure city'),
    array('Macro' => '%ActivatedChain.CommandItem()[0].Command.ExpeditorSite' .
        '.CountryCity.CityName.Name%',  'Sortable' => false));
$grid->NewColumn('FieldMapper', _('Arrival city'),
    array('Macro' => '%ActivatedChain.CommandItem()[0].Command.DestinatorSite' .
        '.CountryCity.CityName.Name%', 'Sortable' => false));
$grid->NewColumn('FieldMapper', _('Beginning date'),
    array('Macro'=>'%ExecutionDate%', 'Sortable'=>false));
$grid->NewColumn('FieldMapper', _('Product type(s)'),
    array('Macro'=>'%ProductTypeCollection%', 'Sortable'=>false));
$grid->NewColumn('FieldMapper', _('Regrouping unit'),
    array('Macro'=>'%SellUnitType.ShortName%', 'Sortable'=>false));
$grid->NewColumn('FieldMapper', _('Weight'),
    array('Macro'=>'%Weight%', 'Sortable'=>false));
$grid->NewColumn('FieldMapper', _('Volume'),
    array('Macro'=>'%Volume%', 'Sortable'=>false));

// actions
$grid->NewAction('Submit',
    array(
        'Caption' => _('Regroup'),
        'CheckForm' => 'GroupableBoxList',
        'WithJSConfirm' => true
    )
);
$grid->NewAction('Cancel');

// construction de la collection de box regroupables
$mapper = Mapper::singleton('Box');
$col = new Collection();
$col->acceptDuplicate = false;

$ackMapper = Mapper::singleton('ActivatedChainTask');
$ackCol = $ackMapper->loadCollection(array('Id'=>$_REQUEST['ackIDs']));
$count = $ackCol->getCount();

$found = $exp = $expSite = $dest = $destSite = false;

for($i = 0; $i < $count; $i++){
    $ack = $ackCol->getItem($i);
    if (!$found) {
        $exp  = Tools::getValueFromMacro($ack,
            '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.ExpeditorId%');
        $expSite  = Tools::getValueFromMacro($ack,
            '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.ExpeditorSiteId%');
        $dest = Tools::getValueFromMacro($ack,
            '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.DestinatorId%');
        $destSite = Tools::getValueFromMacro($ack,
            '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.DestinatorSiteId%');
        $found = ($exp != 'N/A' && $expSite != 'N/A' && $dest != 'N/A' && $destSite != 'N/A');
    }
	$tempcol = $ack->getGroupableBoxCollection();
    if (false == $tempcol) {
        continue;
    }
    $jcount  = $tempcol->getCount();
    for($j = 0; $j < $jcount; $j++){
        $box = $tempcol->getItem($j);
    	$col->setItem($box);
    }
}
$col->sort(array('Begin' => SORT_ASC));

// pas de colis à regrouper
if ($col->getCount() == 0) {
    Template::errorDialog(_('Regrouping is impossible for selected tasks.'), $retURL);
    exit;
}
$ctOptions = FormTools::writeOptionsFromObject('CoverType', 0, array(),
    array('Name'=>SORT_ASC));
$filter = array('Active'=>true);
$expOptions  = FormTools::writeOptionsFromObject('Actor', $exp, $filter,
    array('Name'=>SORT_ASC));
$expSiteOptions  = FormTools::writeOptionsFromObject('Site', $expSite,
    array('Owner'=>$exp), array('Name'=>SORT_ASC));
$destOptions = FormTools::writeOptionsFromObject('Actor', $dest, $filter,
    array('Name'=>SORT_ASC));
$destSiteOptions  = FormTools::writeOptionsFromObject('Site', $destSite,
    array('Owner'=>$dest), array('Name'=>SORT_ASC));

$grid->assign('retURL', $phpself);
$grid->assign("CoverTypeOptions", implode("\n", $ctOptions));
$grid->assign("ExpOptions",  implode("\n", $expOptions));
$grid->assign("DestOptions", implode("\n", $destOptions));
$grid->assign("ExpSiteOptions",  implode("\n", $expSiteOptions));
$grid->assign("DestSiteOptions", implode("\n", $destSiteOptions));
$grid->assign("TotalCount", $col->getCount());
$grid->assign("ackIDs", $_REQUEST['ackIDs']);

// affichage du grid
if ($grid->IsPendingAction()) {
    $col = false;
    $grid->setMapper($mapper);
    $dispatchResult = $grid->DispatchAction($col);
    if (Tools::isException($dispatchResult)) {
        Template::errorDialog(
            $dispatchResult->GetMessage(), $_SERVER['PHP_SELF'] . '?ackIDs[]=' .
            implode('&ackIDs[]=', $_REQUEST['ackIDs']));
    }
} else {
    $result = $grid->render($col, false, array(), array(),
        'Box/GroupableBoxListGrid.html');
    $js = array(
    	'js/lib-functions/FormatNumber.js',
    	'js/lib-functions/checkForm.js',
    	'js/includes/GroupableBoxList.js',
    );
    Template::ajaxPage(PAGE_TITLE, PAGE_SUBTITLE . $result . '</form>', $js);
}

?>
