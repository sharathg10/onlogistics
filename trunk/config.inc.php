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

require_once 'bootstrap.inc.php';

/**
 * fonction qui retourne le dsn en fonction du realm
 *
 * @param string $realm le realm de connection, si false -> Auth::getRealm()
 * @return string $dsn
 * @throws Exception
 */
function getDSNForRealm($realm=false) {
    if (false === $realm) {
        $realm = Auth::getRealm();
    }
    $db_dsn = 'DSN_' . strtoupper($realm);
    if (!in_array($db_dsn, $GLOBALS['DSNS'])) {
        throw new Exception('No DSN found for realm ' . $realm);
    }
    $dsn = constant($db_dsn);
    if (substr_count($dsn, '/') == 4) {
        $dbID = substr($dsn, strrpos($dsn, '/') + 1);
        $dsn  = substr($dsn, 0, strrpos($dsn, '/'));
        if (!defined('DATABASE_ID')) {
            define('DATABASE_ID', (int)$dbID);
        }
    }
    return $dsn;
}

/**
 * Récupère la liste des comptes onlogistics
 *
 * @param  bool  $includeDSN si false le tableau retourne est realm=>realm 
 *                           sinon c'est realm => dsn (defaut: true)
 * @return array le tableau $realm=>$dsn ou $realm=>$realm
 */
function getOnlogisticsAccountArray($includeDSN=true)
{
    $accounts = array();
    if (defined('ACCOUNTS_BACKEND') && ACCOUNTS_BACKEND == 'db') {
        // ajout du filtre par environnement
        $env = 'OnlogisticsAccount::ENV_' . strtoupper(ENVIRONMENT);
        if (!defined($env)) {
            throw new Exception('Environement inconnu: ' . $env);
        }
        // connection à la meta base
        Database::connection(DSN);
        $accountCol = Object::loadCollection('OnlogisticsAccount', 
            array('Active' => true, 'Environment' => constant($env)), 
            array('Name'=>SORT_ASC)
        );
        foreach ($accountCol as $account) {
            $accounts[$account->getName()] = $includeDSN ? 
                $account->getDSN() : $account->getName();
        }
    } else {
        $constants = get_defined_constants();
        foreach ($constants as $name => $value) {
            if (substr($name, 0, 4) == 'DSN_') {
                $realm = strtolower(substr($name, 4));
                $accounts[$realm] = $includeDSN ? $value : $realm;
            }
        }
    }
    // déconnection
    return $accounts;
}

try {
    // parse main and project config files
    if (isset($_SERVER['ONLOGISTICS_CONFIGFILE_PATH'])) {
        $conf = $_SERVER['ONLOGISTICS_CONFIGFILE_PATH'];
    } else {
        $conf = dirname(__FILE__) . '/config/project.conf';
    }
    parseConfigFile($conf);
    // initialisation de l'I18N
    if (isset($_COOKIE['_Auth_Lang'])) {
        define('LOCALE', $_COOKIE['_Auth_Lang']);
    }
    // bootstrap du framework
    bootstrap();
    // ajout de lib-functions au path
    ini_set('include_path', 
        ini_get('include_path') . PATH_SEPARATOR . PROJECT_ROOT 
        . DIRECTORY_SEPARATOR . 'lib-functions');
    // ajout de lib-external au path
    ini_set('include_path', 
        ini_get('include_path') . PATH_SEPARATOR . PROJECT_ROOT
        . DIRECTORY_SEPARATOR . 'lib-external');
    // if prod or demo environment do not show errors
    if (!defined('ENVIRONMENT')) {
        define('ENVIRONMENT', 'current');
    }
    if (ENVIRONMENT == 'prod' || ENVIRONMENT == 'demo') {
        ini_set('display_errors', '0');
    }
    if (defined('ACCOUNTS_BACKEND') && ACCOUNTS_BACKEND == 'db' && !defined('DSN')) {
        throw new Exception('Constante DSN non définie dans le project.conf.');
    }
    require_once 'ObjectTools.php';
    $accounts = getOnlogisticsAccountArray();
    $GLOBALS['DSNS'] = array();
    foreach ($accounts as $realm => $dsn) {
        $constname = 'DSN_' . strtoupper($realm);
        if (!defined($constname)) {
            define("$constname", $dsn);
        }
        $GLOBALS['DSNS'][] = $constname;
    }
    if (empty($GLOBALS['DSNS'])) {
        throw new Exception('Aucun compte onlogistics n\'est paramétré.');
    }
} catch (Exception $exc) {
    echo $exc->getMessage();
    exit(1);
}

// initialisation et config du système de templates
require_once 'TemplateTools.php';
Template::$prebuildFunctions[BASE_TEMPLATE] = 'prebuildPage';
Template::$prebuildFunctions[BASE_POPUP_TEMPLATE] = 'prebuildPopupPage';

$auth  = Auth::singleton(); 
// connection à la bdd
if (isset($_POST['login']) || $auth->isUserConnected()) {
    if (isset($_POST['login'])) {
        $auth_data = explode('@', $_POST['login']);
        $realm  = isset($auth_data[1])?$auth_data[1]:'';
    } else {
        $realm = $auth->getRealm();
    }
    try {
        $dsn = getDSNForRealm($realm);
    } catch (Exception $exc) {
        Template::errorDialog('Client inconnu ' . $realm, 'Login.php');
        exit(0);
    }
    $conn = Database::connection($dsn);
    if (!$conn && !defined('SKIP_CONNECTION')) {
        // redirige vers la page d'auth
        Tools::redirectTo('Login.php');
        exit(0);
    }
} else if (!defined('SKIP_CONNECTION')) {
    // redirige vers la page d'auth
    Tools::redirectTo('Login.php');
    exit(0);
}

?>
