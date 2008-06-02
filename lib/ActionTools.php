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

class ActionStats {
    var $actionStats;
    
    /**
     * Constructeur
     * @param integer $commId id du commercial 
     * @access public
     * @return void
     */
    function ActionStats($commId=0) {
        $this->process($commId);                        
    }
    
    /**
     * Récupère les stats des commerciaux ou de celui
     * dont l'id est en paramètres.
     *
     * @param integer $commId id du commercial
     * @access pubilc
     * @return void
     */
    function process($commId=0) {
        $this->actionStats = array();
        if ($commId == 0) {
            $userAccountMapper = Mapper::singleton('UserAccount');
            $commercialCol = $userAccountMapper->loadCollection(
                array('Profile'=>UserAccount::PROFILE_COMMERCIAL)); 
            $count = $commercialCol->getCount();
            for ($i=0 ; $i<$count ; $i++) {
                $commercial = $commercialCol->getItem($i);
                $key = $commercial->getId();
                $this->actionStats[$key]['Name'] = $commercial->getIdentity();
                $this->actionStats[$key]['Stats'] = $this->_processForCommercial($key);
            }
        } else {
            $commercial = Object::load('UserAccount', $commId);
            $this->actionStats[$commId]['Name'] = $commercial->getIdentity();
            $this->actionStats[$commId]['Stats'] = $this->_processForCommercial($commId);
        }
    }
    
    /**
     * Récupère les stats du commercial dont l'id
     * vaut $commId
     *
     * @param integer $commId id du commercial
     * @access private
     * @return array
     */
    function _processForCommercial($commId) {
        $actionMapper = Mapper::singleton('Action');
        $stats = array();
        $actionArray = FormModel::getActionTypeConstArray();
        
        $actionCol = $actionMapper->loadCollection(
            array('Commercial'=>$commId));
        $count = $actionCol->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $action = $actionCol->getItem($i);
            $key = $action->getType();
            if(!isset($stats[$key])) {
                $stats[$key] = array(
                    'ActionName' => $actionArray[$key],
                    'Count' => 0);
            }
            $stats[$key]['Count'] += 1;
        }
        return $stats;
    }
    
    /**
     * Retourne une chaîne au format csv (comma separated values) représentant 
     * les stats par commercial
     *
     * @param string $delim: optionnel un caractère délimiteur (défaut: ";")
     * @param string $nl: optionnel le caractère retour de ligne (défaut: "\n")
     * @return string
     */
    function toCSV($delim=';', $nl="\n") {
        // en-tête
        $csvData = _('Salesman') . $delim 
                 . _('Action type') . $delim  
                 . _('Number of actions');
        // données
        foreach ($this->actionStats as $key=>$vals) {//$vals=array(name, stats
            foreach ($vals['Stats'] as $val) {
                $csvData .= $nl . $vals['Name'] . $delim . 
                    $val['ActionName'] . $delim . 
                    $val['Count'];
            }
        }
        return $csvData;
    }
}
?>
