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

if (php_sapi_name() != 'cli') {
    exit(1);
}

require_once('Objects/Action.php');
require_once('Objects/FormModel.php');
require_once('Objects/CustomerSituation.php');
require_once('AlertSender.php');

$actorMapper = Mapper::singleton('Actor');
$actionMapper = Mapper::singleton('Action');
$actorAlertIds = array();

$actorCol = $actorMapper->loadCollection(
    array('Active'=>1, 'ClassName'=>array('Customer', 'AeroCustomer')));

$currentDate = date('Y-m-d h:i:s', time());
$secPerWeek = 7*24*60*60;

$count = $actorCol->getCount();
for ($i=0 ; $i<$count ; $i++) {
    $actor = $actorCol->getItem($i);
    $frequency = Tools::getValueFromMacro($actor,
        '%CustomerProperties.PersonalFrequency.Frequency%');
    $delta = ceil($frequency * 0.26);
    /* on récupère les actions succeptibles de passer
    en alerte pour cette acteur */
    $actionCol = $actionMapper->loadCollection(
        array('Actor' => $actor->getId(),
              'Type' => FormModel::ACTION_TYPE_MEETING,
              'State' => Action::ACTION_STATE_TODO));
    
    $countAction = $actionCol->getCount();
    for ($j=0 ; $j<$countAction ; $j++) {
        $action = $actionCol->getitem($j);
        
        if($action->getWishedDate() != '0000-00-00 00:00:00') {
            $maxMeetingDate = DateTimeTools::DateModeler($action->getWishedDate(),
                $delta*$secPerWeek);
            if ($maxMeetingDate < $currentDate) {
                $actorAlertIds[] = array('actorId'=>$i, 'date'=>$maxMeetingDate);
                break;
            }
        }
    }
}

$userAccountMapper = Mapper::singleton('UserAccount');
foreach ($actorAlertIds as $actorId) {
    $actor = $actorCol->getItem($actorId['actorId']);
    $date = $actorId['date'];
    $customerProperties = $actor->getCustomerProperties();
    if($customerProperties instanceof CustomerProperties) {
        $customerSituation = $customerProperties->getSituation();
        $customerSituation->setType(CustomerSituation::TYPE_SITUATION_ALERT);
        $customerSituation->save();
    }
    
    $destinatorCol = new Collection();
    
    $commercial = $actor->getCommercial();
    if($commercial instanceof UserAccount) {
        $destinatorCol->setItem($commercial);
    }
    
    $additionalUserProfiles = array(UserAccount::PROFILE_DIR_COMMERCIAL,
        UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
    foreach ($additionalUserProfiles as $profile) {
        $userAccount = $userAccountMapper->load(
            array('Profile'=>$profile));
        if($userAccount instanceof UserAccount) {
            $destinatorCol->setItem($userAccount);
        }   
    }
    
    AlertSender::send_ALERT_MAX_MEETING_DATE_EXCEEDED($actor->getName(), 
        $date, $destinatorCol);
}
?>
