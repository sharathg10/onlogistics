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

define('SKIP_CONNECTION', true);

require_once('config.inc.php');
require_once('Objects/UserAccount.php');

$auth = Auth::Singleton();

// Check du browser pour le code en prod
if (!isset($_POST['ForceLogin']) && !DEV_VERSION 
    && !in_array(php_sapi_name(), array('cli', 'cgi')))
{
    require_once 'Net/UserAgent/Detect.php';
    if (!Net_UserAgent_Detect::isBrowser('firefox') && 
        !Net_UserAgent_Detect::isBrowser('mozilla'))
    {
        $body = sprintf(
            _('Your browser (%s) is not supported by onlogistics.'),
            Net_UserAgent_Detect::getBrowserString()
        );
        Template::page(_('Unsupported browser'), $body, array(), array(),
            'UnsupportedBrowser.html');
        exit(1);
    } 
}

$redirect = (isset($_REQUEST['redirect']))?$_REQUEST['redirect']:'home.php';
$error_msg = '';

if (isset($_POST['login']) && isset($_POST['password'])) {
    $auth_data = explode('@', $_POST['login']);
    $user = false;
    if (count($auth_data) != 2) {
        $error_msg = _('Login must be: yourlogin@yourcustomername');
    } else {
        $user = $auth->login($auth_data[0], $auth_data[1], $_POST['password']);
        if (Tools::isException($user)) {
            // dormons un peu pour éviter les attaques brute force...
            sleep(5);
            // le message d'erreur à afficher
            $error_msg = $user->getMessage();
        } else {
            if (isset($_REQUEST['language'])) {
                Auth::setCookie('_Auth_Lang', $_REQUEST['language']);
            }
            // Créé la session
            $extraParams = "";
            if (isset($_REQUEST['ExtraParams'])) {
                $padding = "";
                foreach ($_REQUEST['ExtraParams'] as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $Tkey => $Tvalue) {
                            $extraParams .= sprintf("%s%s[%s]=%s", 
                                $padding, $key, $Tkey, $Tvalue);
                            $padding = "&";
                        } 
                    } else {
                        $extraParams .= $padding . $key . "=" . $value;
                        $padding = "&";
                    } 
                } 
            } 
    
            if (!empty($extraParams)) {
                $extraParams .= '&';
            } 
            // on loggue l'utilisateur qui vient de se connecter
            $logger = Tools::loggerFactory();
            $logger->log(
                sprintf(
                    '%s@%s connected (sid: %s).', 
                    $auth_data[0], $auth_data[1], session_id()
                ), 
                PEAR_LOG_NOTICE
            );
            // met à jour le last access date si on utilise le backend db pour 
            // la gestion des comptes
            if (defined('ACCOUNTS_BACKEND') && ACCOUNTS_BACKEND == 'db') {
                if (!defined('ENVIRONMENT')) {
                    define('ENVIRONMENT', 'current');
                }
                $env = 'OnlogisticsAccount::ENV_' . strtoupper(ENVIRONMENT);
                if (!defined($env)) {
                    throw new Exception('Environement inconnu: ' . $env);
                }
                // connection à la meta base
                Database::connection(DSN);
                $account = Object::load('OnlogisticsAccount', array(
                    'Environment' => constant($env),
                    'Name' => Auth::getRealm()
                ));
                if ($account instanceof OnlogisticsAccount) {
                    $account->setLastAccessDate(DateTimeTools::timeStampToMySQLDate(time()));
                    $account->save();
                }
            }
            Tools::redirectTo($redirect . '?' . $extraParams);
            exit(0);
        } 
    }
} 
// On affiche le template
$hidden = '';
foreach($_REQUEST as $key => $val) {
    if (is_array($val)) {
        foreach($val as $akey => $avalue) {
            if (!is_array($avalue)) { 
                $tag = '<input type="hidden" name="ExtraParams[%s][%s]" value="%s" />';
                $hidden .= sprintf($tag, $key, $akey, $avalue);
            }
        } 
    } else {
        $tag = '<input type="hidden" name="ExtraParams[%s]" value="%s" />';
        $hidden .= sprintf($tag, $key, $val);
    } 
} 

$smarty = new Template();
$smarty->assign('formaction', basename($_SERVER['PHP_SELF']));
$smarty->assign('error_msg', $error_msg);
$smarty->assign('url', $redirect);
$smarty->assign('extraparams', $hidden);
$smarty->assign('ForceLogin', isset($_POST['ForceLogin'])?1:0);
$smarty->assign('languages', I18N::getSupportedLocales());
Template::page(_('Authentication'), $smarty->fetch('Login.html'), array());

?>
