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
require_once('Objects/Licence.php');
require_once('Objects/LicenceType.php');
require_once('Objects/Rating.php');
require_once('Objects/RatingType.php');
require_once('Objects/FlyType.php');


/**
 * Messages d'erreur
 **/
define('E_TITLE', 'Erreur.');
/**
 * Authentification
 **/
$session = Session::singleton();
$auth = Auth::singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER, UserAccount::PROFILE_DIR_COMMERCIAL)
);

SearchTools::ProlongDataInSession();

/**
 * On checke que l'acteur soit bien passé en paramètre
 **/
if (!isset($_REQUEST['actorID'])) {
    Template::errorDialog(E_ERROR_IN_EXEC, 'ActorList.php');
    exit;
}
/**
 * On checke que la licence soit bien en session
 **/
if (!isset($_SESSION['lic'])) {
    Template::errorDialog(E_ERROR_IN_EXEC, 'ActorList.php');
    exit;
}

// l'url de retour
$retURL = sprintf('LicenceAddEdit.php?actorID=%s', $_REQUEST['actorID']);

$ratMapper = Mapper::singleton('Rating');
if (isset($_REQUEST['ratIDs'])) {
    $ratMapper->delete($_REQUEST['ratIDs']);
}
$ratCollection = $_SESSION['lic']->getRatingCollection();
$count = $ratCollection->getCount();
for($i=0; $i<$count; $i++){
    $item = $ratCollection->getItem($i);
    if ($item instanceof Rating && in_array($item->getId(), $_REQUEST['ratIDs'])) {
        $ratCollection->removeItem($i);
    }
}
// Redirection vers la liste des licences
Tools::redirectTo($retURL);
exit;

?>