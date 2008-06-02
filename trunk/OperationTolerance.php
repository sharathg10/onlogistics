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

require_once ('config.inc.php');
require_once('Objects/Operation.php');

$auth = Auth::Singleton();
$auth->checkProfiles();

define('FRONT_TOLERANCE', 1);
define('END_TOLERANCE', 2);
define('TOTAL_TOLERANCE', 3);
define('TOLERANCE_MODE', 0);
/**
 * enregistrement des informations provenant du formulaire
 */

if (isset($_REQUEST['OperationModified'])
        && is_array($_REQUEST['OperationModified'])) {

    Database::connection()->startTrans();
    $mapper = Mapper::singleton('Operation');
    foreach($_REQUEST['OperationModified'] as $key => $value) {
        $OpToleranceArray = explode ("|", $value);
        $operation = $mapper->load(array('Id' => $key));
        if ($OpToleranceArray[TOLERANCE_MODE] == 'true') {
            // cas tolerance debut et fin
            $operation->setTotalTolerance(null);
            $operation->SetFrontTolerance($OpToleranceArray[FRONT_TOLERANCE]);
            $operation->SetEndTolerance($OpToleranceArray[END_TOLERANCE]);
        } else {
            // cas tolerance totale
            $operation->SetTotalTolerance($OpToleranceArray[TOTAL_TOLERANCE]);
            $operation->SetFrontTolerance(null);
            $operation->SetEndTolerance(null);
        }
        saveInstance($operation, $_SERVER['PHP_SELF']);
        unset($operation, $OpToleranceArray);
    }
    Database::connection()->completeTrans();
    // récupération des valeur et conversion de l'ensemble en minutes...
    Template::infoDialog(_('Tolerances were successfully updated'), $_SERVER['PHP_SELF']);
    Exit;
}

$smarty = new Template();
$pageContent = $smarty->fetch('Operation/OperationTolerance.html');

Template::page(
    '',
    $pageContent,
    array(
	    'js/lib/TCollection.js',
	    'js/lib/TOperationTolerance.js',
	    'js/lib-functions/OperationTaskPopulateTools.js',
	    'JS_OperationTask.php?notask=1',
	    'JS_OPTolerance.php',
	    'js/includes/OperationTolerance.js',
	    'js/lib-functions/ComboBox.js'
	)
);

?>
