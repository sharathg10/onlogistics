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

class WorkOrderAuth extends Auth{
    /**
     * Constructor
     *
     * @access public
     */
    public function WorkOrderAuth(){
        Auth::singleton();
    }
    /**
     * WorkOrderAuth::singleton()
     *
     * @access public
     * @return object
     * @static
     * single uniformed way to access shared authentification ressources
     */
    public static function singleton()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new WorkOrderAuth();
        }

        return $instance;
    }
    /**
     *
     * @access public
     * @return boolean
     */
    public function check($otid=false){
        /*if (false == $otid){
            return true;
        }*/
        $this->checkProfiles(
            array(
                UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_ACTOR,
                UserAccount::PROFILE_ADMIN_VENTES,
                UserAccount::PROFILE_AERO_ADMIN_VENTES,
                UserAccount::PROFILE_SUPERVISOR,
                UserAccount::PROFILE_GESTIONNAIRE_STOCK,
                UserAccount::PROFILE_TRANSPORTEUR
            ),
            array(
                'showErrorDialog' => true,
                'debug' => false
            )
         );
        // Id de l'actor lie au User connecte
        $UserConnectedActorId = $this->GetActorId();
        // Actor lie au User connecte
        $UserConnectedActor = $this->GetActor();
        $WorkOrder = Object::load('WorkOrder', $otid);
        $validProfiles = array(
            UserAccount::PROFILE_ACTOR,
            UserAccount::PROFILE_SUPERVISOR,
            UserAccount::PROFILE_GESTIONNAIRE_STOCK,
            UserAccount::PROFILE_TRANSPORTEUR
         );
        if (in_array($this->getProfile(), $validProfiles)){
            // Un User ne voit que les OT de son Actor
            if ($UserConnectedActorId != $WorkOrder->GetActorId()){
                return false;
            }
        } else if (UserAccount::PROFILE_ADMIN_VENTES == $this->getProfile() ||
            UserAccount::PROFILE_AERO_ADMIN_VENTES == $this->getProfile()){
            // recup des OT qui ont 1 operation liee a une commande passee
            // par l'actor lie au UserAccount connecte
            $WorkOrderArrayId = $UserConnectedActor->GetWorkOrderCollection();
            if (!in_array($otid, $WorkOrderArrayId)){
                return false;
            }
        }
        return true;
    }
}

?>