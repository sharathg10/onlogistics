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
require_once('Objects/ProductType.php');
require_once('Objects/Property.php');

// On blinde l'accès à cette page 
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW), 
    array('showErrorDialog' => true));

$session = Session::Singleton();

// On prolonge la session 
if (isset($_SESSION['ProductType']) && $_SESSION['ProductType'] instanceof ProductType) {
    $session->prolong('ProductType'); // 1 par défaut
}

// Quelques variables 
$errorTitle = 'Erreur fatale.';
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'PropertyList.php';

// Si on n'a pas les bons paramètres, on renvoi une erreur
if (!isset($_REQUEST['prtIds'])) {
    Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
    exit;
}

if (!is_array($_REQUEST['prtIds'])) {
    $_REQUEST['prtIds'] = array($_REQUEST['prtIds']);
}

// on vérifie que les propriétés ne soient pas affectées à des catalogues
$catMapper = Mapper::singleton('Catalog');
$catCol = $catMapper->loadCollection();
$count  = $catCol->getCount();
$properties = array();
for ($i=0; $i<$count; $i++) {
    $cat = $catCol->getItem($i);
    $properties = array_merge($properties, array_keys($cat->getCatalogCriteriaList()));
}

// On demarre la transaction 
Database::connection()->startTrans();
//Database::connection()->debug = 1;

// On supprime les alertes sélectionnées 
$notDeleted = array();
$prtMapper = Mapper::singleton('Property');
foreach($_REQUEST['prtIds'] as $prtID){
    $prt = $prtMapper->load(array('Id'=>$prtID));
    if ($prt instanceof Property) {
        if (in_array($prtID, $properties)) {
            $notDeleted[] = $prt->getDisplayName();
        } else {
            deleteInstance($prt, $retURL);
            // Si en session on lui enlève cette propriété 
            if (isset($_SESSION['ProductType'])) {
                $_SESSION['ProductType']->removeProperty($prtID);
            }
        }
    }
}    


// On commite
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
    Exit;
}
Database::connection()->completeTrans();

// Tout est OK, on informe l'utilisateur 
if (count($notDeleted) == 1) { 
    Template::errorDialog(
        sprintf(
            _('"%s" property could not be deleted because it is in use in one or more catalogues'),
            $notDeleted[0]
        ), 
        $retURL
    );
} else if (count($notDeleted) > 1) {
    Template::errorDialog(
        _('The following properties could not be deleted because they are in use in one or more catalogues') 
            . sprintf(':<ul><li>%s</li></ul>', implode('</li><li>', $notDeleted)),
        $retURL
    );
} else {
    Tools::redirectTo($retURL);
}
exit;

?>
