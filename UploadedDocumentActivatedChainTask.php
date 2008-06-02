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
require_once 'AlertSender.php';

$session = Session::singleton();
$auth    = Auth::singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_GED_PROJECT_MANAGER
));

SearchTools::prolongDataInSession();

if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'assign' && isset($_REQUEST['ackId']) && isset($_SESSION['udId'])) {
        // assign
        $udId   = $_SESSION['udId'];
        $ack    = Object::load('ActivatedChainTask', $_REQUEST['ackId']);
        if (!($ack instanceof ActivatedChainTask)) {
            Template::errorDialog(
                E_ERROR_IMPOSSIBLE_ACTION,
                'dispatcher.php?entity=UploadedDocument'
            );
            exit(1);
        }
        $method  = 'send_ALERT_GED_DOCUMENT_ASSIGNED';
        $msg     = _('Document "%s" has been assigned to task "%s"'); 
        $ackName = Tools::getValueFromMacro($ack, '%Task.Name%') . ' ';
        if (isset($_SESSION['udIds'])) {
            unset($_SESSION['udIds']);
        }
    } else if ($_REQUEST['action'] == 'unassign' && isset($_REQUEST['udId'])) {
        // unassign
        $udId    = $_REQUEST['udId'];
        $ack     = 0;
        $ackName = '';
        $method  = 'send_ALERT_GED_DOCUMENT_UNASSIGNED';
        $msg     = _('Document "%s" has been unassigned from its task.');
    }
    if (isset($udId)) {
        $doc = Object::load('UploadedDocument', $udId);
        if (!($doc instanceof UploadedDocument)) {
            Template::errorDialog(
                E_ERROR_IMPOSSIBLE_ACTION,
                'dispatcher.php?entity=UploadedDocument'
            );
            exit(1);
        }
        $doc->setActivatedChainTask($ack);
        $doc->setLastModificationDate(date('Y-m-d H:i:s', time()));
        $doc->setUserAccount($auth->getUserId());
        $doc->save();
        AlertSender::$method($doc);
        $msg = sprintf($msg, $doc->getName(), $ackName);
        Template::infoDialog($msg, 'dispatcher.php?entity=UploadedDocument');
        exit(0);
    }
}

Tools::redirectTo('dispatcher.php?entity=UploadedDocument');

?>
