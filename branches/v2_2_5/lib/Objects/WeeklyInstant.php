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

class WeeklyInstant extends _WeeklyInstant {
    // Constructeur {{{

    /**
     * WeeklyInstant::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // WeeklyInstant::getNearestOccurence() {{{

    /**
     * Renvoie le timestamp correspondant à la première occurence 
     * valide à partir de $fromThisDate
     * 
     * @param integer $fromThisDate TimeStamp correspondant à la date 
     * minimale de validité
     * @param boolean $type true si postPivot
     * @return integer Le timestamp correspondant ou FALSE si aucun ne 
     * correspond
     */
    function getNearestOccurence($fromThisDate, $type=true)
    {
        
        $nbSndPerDay = 24*60*60;
        $nbSndPerWeek = 7*$nbSndPerDay;
        
        $timeTS = DateTimeTools::TimeToTimeStamp($this->getTime());
        $day = DateTimeTools::GetDayValue($fromThisDate);
        if($type) {
            //calcul la date pour le prochain depart à partir de la date $fromThisDate
            // en avancant
            $nextValidDate = $fromThisDate;
            while(DateTimeTools::GetDayValue($nextValidDate) != $this->getDay()) {
                $nextValidDate += $nbSndPerDay;
            }
            $expDate = DateTimeTools::DateExploder(date('Y-m-d h:i:s', $nextValidDate));
            if(DateTimeTools::GetTimeFromDate($nextValidDate) >= $timeTS
              || $nextValidDate != $fromThisDate) {
                // si l'heure est sup ou que la date est différente ca passe
                return mktime(0,0,0, $expDate['month'],
                $expDate['day'], $expDate['year']) + $timeTS; 
            } else {
                // sinon ce sera pour la semaine prochaine
                return mktime(0,0,0,
                    $expDate['month'], $expDate['day'],$expDate['year']) 
                     + $timeTS + $nbSndPerWeek;
            }
        } else {
            //calcul la date pour le prochain depart à partir de la date $fromThisDate
            // en reculant
            $previousValidDate = $fromThisDate;
            while(DateTimeTools::GetDayValue($previousValidDate) != $this->getDay()) {
                $previousValidDate -= $nbSndPerDay;
            }
            $expDate = DateTimeTools::DateExploder(date('Y-m-d h:i:s', $previousValidDate));
            if($previousValidDate == $fromThisDate) {
                if(DateTimeTools::GetTimeFromDate($previousValidDate) < $timeTS) {
                    return mktime(0,0,0, $expDate['month'],
                        $expDate['day'], $expDate['year']) + $timeTS; 
                } else {
                    return mktime(0,0,0,
                        $expDate['month'], $expDate['day'],$expDate['year']) 
                        + $timeTS - $nbSndPerWeek; 
                }   
            } else {
                return mktime(0,0,0, $expDate['month'],
                    $expDate['day'], $expDate['year']) + $timeTS; 
            }
        } 
        return false;
    } 

    // }}}

}

?>