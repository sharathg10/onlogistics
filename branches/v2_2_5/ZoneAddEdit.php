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
require_once('Objects/Zone.php');

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
//Database::connection()->debug = true;
$pageTitle = _('Add or update zone');
$retURL = 'ZoneList.php';
$znId = isset($_REQUEST['znId'])?$_REQUEST['znId']:0;
$thisURL = $_SERVER['PHP_SELF'] . '?znId=' . $znId;
$Zone = ($znId)?Object::load('Zone', $znId):new Zone();

if (Tools::isException($Zone)) {
    Template::errorDialog($Zone->getMessage(), $retURL);
    exit;
}

/**
 * Messages
 */
$znAlreadyExist = _('Zone could not be saved: a zone with the name provided already exists.');



/**
 * Traitement du formulaire poste
*/
if (isset($_POST['formSubmitted'])) {

    // CountryCitys et sites lies a $Zone
    if ($znId == 0) {
        $ccyCollection = $siteCollection = new Collection();
    }
    else {
        $ccyCollection = Object::loadCollection(
            'CountryCity', array('Zone' => $znId), array(), array('Id'));
        $siteCollection = Object::loadCollection(
            'Site', array('Zone' => $znId), array(), array('Id'));
    }


    // 2 zones ne peuvent avoir le même nom
    $zoneMapper = Mapper::singleton('Zone');
    $name = $_POST['Zone_Name'];
    if (($name != $Zone->getName())
            && ($zoneMapper->alreadyExists(array('Name' => $name)))) {
        Template::infoDialog($znAlreadyExist, $thisURL);
        exit;
    }
	// On remplit l'objet
    FormTools::autoHandlePostData($_POST, $Zone);

    Database::connection()->startTrans();
    saveInstance($Zone, $thisURL);
    $znId = $Zone->getId();
    $thisURL = $_SERVER['PHP_SELF'] . '?znId=' . $znId;

    // Gestion des villes liees a la Zone
    if (isset($_POST['countrycity_ok'])) {
        foreach($_POST['CountryCity'] as $ccyId) {
            // Si deja dans la collection, rien à faire
            if ($ccyId == 0 || $ccyCollection->getItemById($ccyId) !== false) {
                continue;
            }
            $ccy = Object::load('CountryCity', $ccyId);
            // La CountryCity ne doit pas deja etre affectee a une Zone
            if ($ccy->getZoneId() == 0) {
                $ccy->setZone($Zone);
                saveInstance($ccy, $thisURL);
                // $ccyCollection->setItem($ccy); pas ici, car coll pas triable...
            }
            unset($ccy);
        }
    }
    // Gestion des sites liees a la Zone
    if (isset($_POST['site_ok'])) {
        foreach($_POST['Site'] as $sitId) {
            // Si deja dans la collection, rien à faire
            if ($sitId == 0 || $sitId == '##' || $siteCollection->getItemById($sitId) !== false) {
                continue;
            }
            $site = Object::load('Site', $sitId);
            // Le Site ne doit pas deja etre affecte a une Zone
            if ($site->getZoneId() == 0) {
                $site->setZone($Zone);
                saveInstance($site, $thisURL);
                // $siteCollection->setItem($site);  pas ici, car coll pas triable...
            }
            unset($site);
        }
    }

    //On commite la transaction et gestion des erreurs
    if (Database::connection()->hasFailedTrans()) {
        if (DEV_VERSION) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        }
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $thisURL);
        exit;
    }
    Database::connection()->completeTrans();
}


/**
 * Le formulaire pour selectionner les composants de la Zone
 **/
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('ZoneAddEdit', 'post', basename($_SERVER['PHP_SELF']));
$CountryArray = SearchTools::createArrayIDFromCollection('Country');
$form->addElement('text', 'Zone_Name', _('Name'), 'class="searchField"');
$form->addElement('select', 'Country', 'Pays', $CountryArray,
        'onchange="fw.ajax.updateSelect(\'country\', \'state\', \'State\', \'Country\');'
        . 'fw.ajax.updateSelect(\'country\', \'department\', \'Department\', \'Country\');'
    //    . 'fw.ajax.updateSelectCustom(\'country\', \'countrycity\', \'CountryCity\', \'Country\', \'zoneaddedit_getCollection\');'
        . 'fw.ajax.updateSelectCustom(\'country\', \'site\', \'Site\', '
        . '\'CountryCity.Country\', \'zoneaddedit_getCollection\');'
        . 'return false;" class="searchField" id="country"');

$form->setDefaults(array('Zone_Name' => $Zone->getName()));

// Controle de la saisie cote client
$form->addRule('Zone_Name', _('Please provide a zone name.'),
        'required', '', 'client');
$form->setJsWarnings('Erreur de saisie: ', _('Please correct.'));

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$smarty->assign('znId', $znId);

/**
 * Le grid des villes (CountryCity) incluses
 **/
$ccyGrid = new Grid();
$ccyGrid->javascriptFormOwnerName = 'ccyGrid';
$ccyGrid->displayCancelFilter = false;
$ccyGrid->withNoSortableColumn = true;

$ccyGrid->NewColumn('FieldMapper', _('Name'), array('Macro' => '%CityName.Name%'));
$ccyGrid->NewColumn('FieldMapper', _('Zip code'), array('Macro' => '%Zip.Code%'));

$ccyGrid->NewAction('Delete',
        array('TransmitedArrayName' => 'ccyId',
	          'EntityType' => 'ZoneComponent',
              'Query' => 'znId=' . $znId,
              'ReturnURL' => $thisURL
));

/**
 * Le grid des sites (Site) inclus
 **/
$siteGrid = new Grid();
$siteGrid->javascriptFormOwnerName = 'siteGrid';
$siteGrid->displayCancelFilter = false;
$siteGrid->withNoSortableColumn = true;

$siteGrid->NewColumn('FieldMapper', _('Name'), array('Macro' => '%Name%'));

$siteGrid->NewAction('Delete',
        array('TransmitedArrayName' => 'sitId',
	          'EntityType' => 'ZoneComponent',
              'Query' => 'znId=' . $znId,
              'ReturnURL' => $thisURL
));

// CountryCitys et sites lies a $Zone
if ($znId == 0) {
        $ccyCollection = $siteCollection = new Collection();
    }
    else {
        $ccyCollection = Object::loadCollection(
                'CountryCity', array('Zone' => $znId),
                array('CityName.Name' => SORT_ASC), array('CityName', 'Zip'));
        $siteCollection = Object::loadCollection(
                'Site', array('Zone' => $znId),
                array('Name' => SORT_ASC), array('Name'));
    }


$ccyList = $ccyGrid->render(
        $ccyCollection, false, array(), array('CityName.Name' => SORT_ASC));
$siteList = $siteGrid->render(
        $siteCollection, false, array(), array('Name' => SORT_ASC));

/**
 * Traitement du clic sur l'action des grids
*/
if ($ccyGrid->isPendingAction() && isset($_REQUEST['ccySubmitted'])) {
    $ccyGrid->setMapper('CountryCity');
    $res = $ccyGrid->dispatchAction(false);
    if (Tools::isException($res)) {
        Template::errorDialog(
                sprintf(E_ERROR_IN_EXEC, $res->getMessage()), $thisURL);
        exit;
    }
}
elseif ($siteGrid->isPendingAction() && isset($_REQUEST['siteSubmitted'])) {
    $siteGrid->setMapper('Site');
    $res = $siteGrid->dispatchAction(false);
    if (Tools::isException($res)) {
        Template::errorDialog(
                sprintf(E_ERROR_IN_EXEC, $res->getMessage()), $thisURL);
        exit;
    }
}

/**
 * Affichage
 **/
$smarty->assign('ccyGrid', $ccyList);
$smarty->assign('siteGrid', $siteList);
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);
$pageContent = $smarty->fetch('Zone/ZoneAddEdit.html');

$js = array('JS_AjaxTools.php', 'js/includes/ZoneAddEdit.js');
Template::page($pageTitle, $pageContent, $js);
?>
