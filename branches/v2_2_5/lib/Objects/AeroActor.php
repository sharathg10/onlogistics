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

class AeroActor extends _AeroActor {
    // Constructeur {{{

    /**
     * AeroActor::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    
    /**
     * Utilisée par la méthode getFlyTypeCollection() pour éviter de recharger 
     * à chaque fois la collection.
     *
     **/
    var $_flyTypeCollection = false;
    
    /**
     * AeroActor::getFlyTypeCollection()
     * Retourne la collection de types d'appareils pour un acteur. 
     *
     * @access public
     * @return object Collection 
     **/
    function getFlyTypeCollection(){
        if (false != $this->_flyTypeCollection) {
            return $this->_flyTypeCollection;
        }
        // on retourne une collection contenant tous les flytypes autorisés
        $this->_flyTypeCollection = $this->getAuthorizedFlyTypeCollection();
        // et les flytypes de chaque qualification des licences de l'acteur
        $this->_flyTypeCollection->acceptDuplicate = false;
        $licences = $this->getLicenceCollection();
        $count = $licences->getCount();
        // on parcours les licences
        for($i = 0; $i < $count; $i++){
            $licence = $licences->getItem($i);
            $ratings = $licence->getRatingCollection();
            $jcount = $ratings->getCount();
            // pour chaque rating on ajoute à la collection son flytype
            for($j = 0; $j < $jcount; $j++){
                $rating = $ratings->getItem($j);
                $ftype  = $rating->getFlyType();
                if ($ftype instanceof FlyType) {
                    $this->_flyTypeCollection->setItem($ftype);
                }   
            }
        }
        return $this->_flyTypeCollection;
    }
    
    /**
     * AeroActor::findLicenceByFlyType()
     * Retourne la licence de l'acteur associée au type type d'appareil passé 
     * en paramètre $flytype
     *
     * @access public
     * @param object FlyType
     * @return boolean
     **/
    function findLicenceByFlyType($flytype){
        if (!($flytype instanceof FlyType)) {
            trigger_error("AeroActor::findLicenceByFlyType(): le paramètre " . 
                "passé n'est pas un objet FlyType", E_USER_ERROR);
        }
        $licences = $this->getLicenceCollection();
        if (Tools::isEmptyObject($licences)) {
            // pas de licence(s) définie(s), on retourne false 
            return false;
        }
        // on parcours les licences
        $count = $licences->getCount();
        for($i = 0; $i < $count; $i++){
            $licence = $licences->getItem($i);
            $ratings = $licence->getRatingCollection();
            $jcount = $ratings->getCount();
            for($j = 0; $j < $jcount; $j++){
                $rating = $ratings->getItem($j);
                if ($flytype->getId() == $rating->getFlyTypeId()) {
                    return $licence;
                }
            }
        }
        return false;
    }
    
    /**
     * AeroActor::canFlyWithType()
     * Retourne true si l'acteur est autorisé à voler avec le type d'appareil 
     * passé en paramètre
     *
     * @access public
     * @return void 
     **/
    function canFlyWithType($flytype){
        if (!($flytype instanceof FlyType)) {
            trigger_error("AeroActor::canFlyWithType(): le paramètre " . 
                "passé n'est pas un objet FlyType", E_USER_ERROR);
        }
        $ftypes = $this->getFlyTypeCollection();
        $ftypesIDs = $ftypes->getItemIds();
        return in_array($flytype->getId(), $ftypesIDs);
    }
    
    /**
     * Update un ou plusieurs attributs numeriques d'une quantite donnee
     * @param array of strings $attributes
     * @param integer or float or decimal $Quantity
     * @access public
     * @return void 
     **/
    function updateAttributesWithQty($attributes, $Quantity) {
        if (!is_array($attributes)) {
            $attributes = array($attributes);
        }
        foreach($attributes as $name) {
            $getter = 'get' . $name;
            $setter = 'set' . $name;
            if (method_exists($this, $getter)) {
                $this->$setter($this->$getter() + $Quantity);
            }
        }
    }
    
    /**
     * Retourne le tableau des propriétés spécifiques à l'aeronautique
     *
     * @access public
     * @return array
     */
    function getAeroProperties() {
        require_once('Objects/AeroCustomer.php');
        require_once('Objects/AeroInstructor.php');
        require_once('Objects/AeroOperator.php');
        return array_merge(
            AeroActor::getProperties(true), 
            AeroCustomer::getProperties(true),
            AeroInstructor::getProperties(true)
        );
    }
    
    /**
     * Affiche le detail des donnees aeronautiques de l'acteur
     * @access public
     * @return string: table HTML 
     **/
    function displayDetail(){
        require_once('ActorAddEditTools.php');
        $aeroProperties = $this->getAeroProperties();
        $smarty = new Template();
        // Toutes les heures sont affichees en hh::mm
        $smarty->register_function('date_hundredthsOfHourToTime', array('DataConverter', "hundredthsOfHourToTime"));
        assignObjectAttributes($smarty, $this, $aeroProperties);
        
        if (get_class($this) == 'AeroCustomer') {
            $role = 'le' . ' ' . _('customer');
            $divId = 'customerDetail'; // nom du layer
        }
        elseif (get_class($this) == 'AeroInstructor') {
            $role = 'l\'' . _('instructor');
            $divId = 'instructorDetail';
        }
        else {
            $role = 'l\'' . _('actor');
        }
        if (method_exists($this, 'getTrainee')) {
            $smarty->assign('IsTrainee', ($this->getTrainee() == 1)?'Oui':'Non');
        }
        if (method_exists($this, 'getSoloFly')) {
            $smarty->assign('IsSoloFly', ($this->getSoloFly() == 1)?'Oui':'Non');
        }
        $InstructorId = (method_exists($this, 'getInstructorId'))?$this->getInstructorId():0;
        $Instructor = Object::load('AeroInstructor', $InstructorId);
        $smarty->assign('InstructorName', $Instructor->getName());
        $smarty->assign('role', $role);
        $smarty->assign('divId', $divId);
        $detail = $smarty->fetch('Actor/AeroActorDetail.html');
        return $detail;
    }
    
    /**
     * Affiche les stats aeronautiques de l'acteur
     * 
     * @access public
     * @param $filter object Filter: filtre sur les ActivatedChainTask:
     * la base pour les stats
     * @return string: table HTML  
     **/
    function displayStat($filter){
        if (get_class($this) == 'AeroCustomer') {
            $role = 'le' . ' ' . _('customer');
            $divId = 'customerStat'; // nom du layer
        }
        elseif (get_class($this) == 'AeroInstructor') {
            $role = 'l\'' . _('instructor');
            $divId = 'instructorStat';
        }
        else {
            $role = 'l\'' . _('actor');
        }
        $actMapper = Mapper::singleton('ActivatedChainTask');
        $actCollection = $actMapper->loadCollection($filter);
        if (Tools::isEmptyObject($actCollection)) {
            return '<div id="'. $divId .'"></div>';
        }
        $TechnicalHour = $NightHours = $PublicHours = $VLAEHours 
        = $MonoEngineHours = $BiEngineHours = $CommercialHours = 0;
        for($i = 0; $i < $actCollection->getCount(); $i++){
            $act = $actCollection->getItem($i);
            $actDetail = $act->getActivatedChainTaskDetail();
            $TechnicalHour += $actDetail->getTechnicalHour();
            $NightHours += $actDetail->getPilotHoursNight() + $actDetail->getCoPilotHoursNight() 
                + $actDetail->getPilotHoursBiEngineNight() + $actDetail->getCoPilotHoursBiEngineNight();
            $PublicHours += $actDetail->getPublicHours();
            $VLAEHours += $actDetail->getVLAEHours();
            // Heures sur bimoteur
            /*if ($actDetail->getCycleEngine2() > 0 || $actDetail->getCycleEngine2N1() > 0 
                                                  || $actDetail->getCycleEngine2N2() > 0 ) {
                $BiEngineHours += $actDetail->getTechnicalHour();
            }
            else { // Heures sur monomoteur
                $MonoEngineHours += $actDetail->getTechnicalHour();
            }*/
            $BiEngineHours += $actDetail->getPilotHoursBiEngine() + $actDetail->getCoPilotHoursBiEngine() 
                + $actDetail->getPilotHoursBiEngineNight() + $actDetail->getCoPilotHoursBiEngineNight();
            $MonoEngineHours += $actDetail->getPilotHours() + $actDetail->getCoPilotHours() 
                + $actDetail->getPilotHoursNight() + $actDetail->getCoPilotHoursNight();
            $CommercialHours += $actDetail->getRealCommercialDuration();
        }
        
        $smarty = new Template();
        $smarty->assign('technicalHour', DateTimeTools::hundredthsOfHourToTime($TechnicalHour));
        $smarty->assign('NightHoursOfFlight', DateTimeTools::hundredthsOfHourToTime($NightHours));
        $smarty->assign('PublicHoursOfFlight', DateTimeTools::hundredthsOfHourToTime($PublicHours));
        $smarty->assign('VLAEHoursOfFlight', DateTimeTools::hundredthsOfHourToTime($VLAEHours));
        $smarty->assign('MonoEngineHoursOfFlight', DateTimeTools::hundredthsOfHourToTime($MonoEngineHours));
        $smarty->assign('BiEngineHoursOfFlight', DateTimeTools::hundredthsOfHourToTime($BiEngineHours));
        $smarty->assign('CommercialHours', DateTimeTools::hundredthsOfHourToTime($CommercialHours));
        
        $smarty->assign('role', $role);
        $smarty->assign('divId', $divId);
        $detail = $smarty->fetch('Actor/AeroActorStat.html');
        return $detail;
    }

}

?>