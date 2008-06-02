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

function NewSite($sitId, $actId, $sitName){ // {{{
    return 'ns(' . (int)$sitId . ',' . (int)$actId . ',' .
        JsTools::JSQuoteString($sitName) . ')' . ";\n";
} // }}}

/**
 * Créé une nouvelle variable JavaScript Job
 *
 * @param integer $jobId Identifiant du job
 * @param string $jobName Nom du job
 * @access public
 * @return string

function NewJob($jobId, $jobName) { // {{{
    return 'nj(' . (int)$jobId . ',' . JsTools::JSQuoteString($jobName) . ')' . ";\n";
}*/ // }}}

/**
 * Créé une nouvelle variable JavaScript Actor
 *
 * @param integer $actId Identifiant de l'acteur
 * @param string $actName Nom de l'acteur
 * @param TJob $TJob Nom du job
 * @access public
 * @return string
 */
function NewActor($actId, $actName/*, $TJobs*/){ // {{{
    return 'na(' . (int)$actId . ',' . JsTools::JSQuoteString($actName) /*. ',' .
        $TJobs*/ . ')' . ";\n";
} // }}}

/**
 * Génère le cache js pour Actor/Site/Job
 *
 * @param boolean $withJob si true les Jobs sont traités
 * @param boolean $dumpSites si true les sites sont traités
 * @access public
 * @return string
 */
function generateActorSiteJobCache($dumpSites = false, $withJob = false){ // {{{
    $auth = Auth::singleton();
    $return = NewActor(0, _('No actor')/*, '[]'*/);
    $sitMapper = Mapper::singleton('Site');
    $actMapper = Mapper::singleton('Actor');
    $order = array('Name' => SORT_ASC);
    $authID = $auth->getProfile();
    $filter = new FilterComponent(
        new FilterComponent(
            new FilterRule(
                'Active',
                FilterRule::OPERATOR_EQUALS,
                1
            )
        ),
        FilterComponent::OPERATOR_OR,
        new FilterComponent(
            new FilterRule(
                'Generic',
                FilterRule::OPERATOR_EQUALS,
                1
            )
        )
    );
    // Récupération des Acteurs
    $actCol = $actMapper->loadCollection($filter, $order, array('Name'));
    $count = $actCol->getCount();
    for($i = 0; $i < $count; $i++) {
        $act = $actCol->getItem($i);
        if (true == $dumpSites) {
            $return .= NewSite(0, 0, _('No site'));
            $sitCol = $sitMapper->loadCollection(
                array('Owner' => $act->getId()),
                array('Name'=>SORT_ASC),
                array('Name')
            );
            $jcount = $sitCol->getCount();
            for($j = 0; $j < $jcount; $j++) {
                $sit = $sitCol->getItem($j);
                $return .= NewSite($sit->getId(), $act->getId(), $sit->getName());
            }
            if ($j == 0) {
                $return .= NewSite(0, $act->getId(), _('Generic actor site'));
            }
        }
        $return .= NewActor($act->getId(), $act->getName());
    }
    return $return;
} // }}}

?>