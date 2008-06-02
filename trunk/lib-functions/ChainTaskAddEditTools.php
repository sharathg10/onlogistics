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

define('INSTANTTYPE_FIXED', 1);
define('INSTANTTYPE_WEEKLY', 2);

require_once('Objects/Instant.php');
require_once('Objects/WeeklyInstant.php');
require_once('Objects/Chain.inc.php');

/**
 * Retourne la constante correspondante au type d'instant.
 *
 * @access public
 * @param  object $instant une instance d'AbstractInstant
 * @return integer
 */
function getInstantType($instant) {
    if (!($instant instanceof AbstractInstant)) {
        return -1;
    }
    switch (get_class($instant)) {
        case 'DailyInstant':
            return DAILY_INSTANT;
        case 'WeeklyInstant':
            return WEEKLY_INSTANT;
        default:
            return INSTANT;
    }
}

/**
 * Fonction utilitaire pour sauvegarder les dates ou jour/heure des 
 * DepartureInstant et ArrivalInstant du formulaire
 *
 * @access public
 * @param object $ctk une instance de ChainTask
 * @param object $instant 
 * @param string $type soit 'Departure' soit 'Arrival'
 * @return void
 **/ 
function saveInstants($ctk, $instant, $type = 'Departure') {
    $url = 'ChainTaskList.php';
    if (isset($_REQUEST[$type . 'Instant_Date_Active'])) {
        if (!$instant) {
            $instant = new Instant();
        } else if (!($instant instanceof Instant)) {
            deleteInstance($instant, $url);
            $instant = new Instant();
        }
        $date = $_REQUEST[$type . 'Instant_Date_Year'] . '-' 
              . $_REQUEST[$type . 'Instant_Date_Month'] . '-' 
              . $_REQUEST[$type . 'Instant_Date_Day'] . ' ' 
              . $_REQUEST[$type . 'Instant_Date_Hour'] . ':' 
              . $_REQUEST[$type . 'Instant_Date_Minute'] . ':' 
              . '00'; 
        $instant->setDate($date);
        saveInstance($instant, $url);
    } else if (isset($_REQUEST[$type . 'Instant_DayTime_Active'])) {
        if (!$instant) {
            $instant = new WeeklyInstant();
        } else if (!($instant instanceof WeeklyInstant)) {
            deleteInstance($instant, $url);
            $instant = new WeeklyInstant();
        }
        $instant->setDay($_REQUEST[$type . 'Instant_Day']);
        $time = $_REQUEST[$type . 'Instant_Time_Hour'] . ':' 
              . $_REQUEST[$type . 'Instant_Time_Minute'] . ':' 
              . '00'; 
        $instant->setTime($time);
        saveInstance($instant, $url);
    } else if (isset($_REQUEST[$type . 'Instant_DailyTime_Active'])) {
        if (!$instant) {
            $instant = new DailyInstant();
        } else if (get_class($instant) != 'DailyInstant') {
            deleteInstance($instant, $url);
            $instant = new DailyInstant();
        }
        $time = $_REQUEST[$type . 'Instant_DailyTime_Hour'] . ':' 
              . $_REQUEST[$type . 'Instant_DailyTime_Minute'] . ':' 
              . '00'; 
        $instant->setTime($time);
        saveInstance($instant, $url);
    } else {
        if ($instant) {
            deleteInstance($instant, $url);
            $instant = false;
        }
    }
    $setter = 'set' . $type . 'Instant';
    $ctk->$setter($instant);
}

/**
 * Fonction utilitaire pour assigner les dates ou jour/heure des 
 * DepartureInstant et ArrivalInstant du formulaire
 *
 * @access public
 * @param object $smarty l'instance de smarty en cours
 * @param object $instant 
 * @param string $type soit 'Departure' soit 'Arrival'
 * @return void
 **/ 
function assignInstants($smarty, $instant, $type = 'Departure') {
    $smarty->assign($type . 'Instant_DayOptions', getDaysOfWeek(true));
    if ($instant instanceof Instant) {
        $smarty->assign($type . 'Instant_ActualDate', 
            $instant->getDate('localedate'));
        $smarty->assign($type . 'Instant_Date', 
            $instant->getDate('timestamp'));
        $smarty->assign($type . 'Instant_Date_Active', 1);
    } else if ($instant instanceof WeeklyInstant) {
        $days = getDaysOfWeek();
        $smarty->assign(
            $type . 'Instant_ActualDayTime', 
            sprintf(_('The %s at %s'),
                $days[$instant->getDay()], 
                $instant->getTime()
            )
        );
        $smarty->assign($type . 'Instant_DayOptions', 
            getDaysOfWeek(true, $instant->getDay()));
        $smarty->assign($type . 'Instant_Time', $instant->getTime());
        $smarty->assign($type . 'Instant_DayTime_Active', 1);
    } else if ($instant instanceof DailyInstant) {
        $smarty->assign($type . 'Instant_ActualDailyTime', $instant->getTime());
        $smarty->assign($type . 'Instant_DailyTime', $instant->getTime());
        $smarty->assign($type . 'Instant_DailyTime_Active', 1);
    }
}

/**
 * Retourne un tableau de jours ou une chaine contenant les options de select 
 * des jours si le paramètre asOptions vaut true.
 * Pour les options il est possible de passer un entier qui correspond à 
 * l'option qui doit apparaitre sélectionnée.
 *
 * @access public
 * @param boolean $asOptions
 * @param boolean $selected
 * @return mixed array or string
 **/ 
function getDaysOfWeek($asOptions=false, $selected=1) {
    $days = array(
        1=>_('Monday'), 
        2=>_('Tuesday'),
        3=>_('Wednesday'),
        4=>_('Thursday'),
        5=>_('Friday'),
        6=>_('Saturday'),
        7=>_('Sunday')
    );
    if (!$asOptions) {
        return $days;
    }
    $ret = $padding = '';
    foreach($days as $val=>$day) {
        $sel = $val==$selected?' selected':'';
        $ret .= sprintf(
            '%s<option value="%s"%s>%s</option>', 
            $padding, $val, $sel, $day
        );
        $padding = '\n';
    }
    return $ret;
}

?>
