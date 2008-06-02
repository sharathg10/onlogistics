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

class GridColumnCommentWOOpeTaskList extends AbstractGridColumn {
    /**
     * Constructor
     * 
     * @access protected 
     */
    public function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
    } 

    public function render($object) {
        require_once('PlanningTools.php');
        $operationName = Tools::getValueFromMacro($object, '%Operation.Name%');
        $Comment = Tools::getValueFromMacro($object, '%ActivatedChain.CommandItem()[#].Command.Comment%');
		
		$StartDate = Tools::getValueFromMacro($object, '%ActivatedChain.CommandItem()[0].Command.WishedStartDate%');
        $EndDate = Tools::getValueFromMacro($object, '%ActivatedChain.CommandItem()[0].Command.WishedEndDate%');
        $WishedStartDate = I18N::formatDate($StartDate, I18N::DATETIME_SHORT);
        $WishedEndDate = I18N::formatDate($EndDate, I18N::DATETIME_SHORT);
        $WishedStartDate = ($WishedEndDate == '0' || $WishedEndDate == '00/00/00&nbsp;00:00')?
				$WishedStartDate:'entre ' . $WishedStartDate . ' et ';
		$WishedEndDate = ($WishedEndDate == '0' || $WishedEndDate == '00/00/00&nbsp;00:00')?'.':$WishedEndDate . '.';
        $WishedDate = _('Order wished date') . ': ' . $WishedStartDate . $WishedEndDate;

        if (false === strpos($operationName, 'TRANSPORT')) { // affichage seulemt du commentaire
            return $Comment;
        } 
		
        $Comment = (empty($Comment))?'':$Comment . '<br>';
		
		/* ATTENTION: ici, on n'utilise pas ActorSiteTransition!!!!!*/
        /* Pour le site de livraison, on prend le MainSite si c'est un site de livraison, 
		 * oubien le 1er site de livraison trouve,
		 * oubien le MainSite si pas de site de livraison  *//*
		$Actor = $object->getEndActor();
        $MainSite = $Actor->getMainSite();
        if (!Tools::isEmptyObject($MainSite) && 
				Site::SITE_TYPE_LIVRAISON == $MainSite->getType() || Site::SITE_TYPE_FACTURATION_LIVRAISON == $MainSite->getType()) {
            $DeliverySite = $Actor->getMainSite();
        } else {
            $SiteCollection = $Actor->getSiteCollection(array(LIVRAISON));
            if (($SiteCollection instanceof Collection) && ($SiteCollection->getCount() > 0)) {
                $DeliverySite = $SiteCollection->getItem(0);
            } else {
                $DeliverySite = $Actor->getMainSite();
            } 
        }*/
		
		$displayedPlanning = '';
        $SiteId = Tools::getValueFromMacro($object, '%ActivatedChainTask()[0].ActorSiteTransition.ArrivalSite.Id%');
		$Site = Object::load('Site', $SiteId);
		// si pas de EndDate renseignee, on fait: $EndDate = $StartDate
        if ($EndDate == 0 || $EndDate == '0000-00-00 00:00:00') {
            $EndDate = $StartDate;
        } 
//		else {
		if (!Tools::isEmptyObject($Site)) {
			$WeeklyPlanning = $Site->getPlanning();
			if (!Tools::isEmptyObject($WeeklyPlanning)) {
				$PlanningRange = "";
	            $PlanningTools = new PlanningTools($WeeklyPlanning);
	            $CurrentTimeStamp = DateTimeTools::MysqlDateToTimeStamp($StartDate);
	            $SameDay = false;
	
	            while (false == $SameDay) {
	                $CurrentDate = DateTimeTools::timeStampToMySQLDate($CurrentTimeStamp);
	                $TimeInfLimit = (substr($StartDate, 0, 10) == substr($CurrentDate, 0, 10))?
							I18N::formatDate($StartDate, I18N::TIME_LONG):'00:00:00';
	                $TimeSupLimit = (substr($CurrentDate, 0, 10) == substr($EndDate, 0, 10))?
							I18N::formatDate($EndDate, I18N::TIME_LONG):'23:59:00';
	                $PlanningRange .= $PlanningTools->getDayRange($CurrentTimeStamp, $TimeInfLimit, $TimeSupLimit) . '<br />';
	                $SameDay = (substr($CurrentDate, 0, 10) == substr($EndDate, 0, 10))?true:false;
					// Ajout de 24 heures
	                $CurrentTimeStamp = (!$SameDay)?$CurrentTimeStamp + 86400:$CurrentTimeStamp;
	            }

				$accessor = "Get" . date('l', $CurrentTimeStamp); 
		        // c'est un DailyPlanning
		        $planning = $WeeklyPlanning->$accessor();
				$displayedPlanning = _('Daily schedule') . ': ';
		        // Nbre de crenaux ds la journee
				// Planning indefini
				if ($PlanningTools->getPlanningRangeLength($planning) == 0) {
					$displayedPlanning .= _('None');
					return $Comment . $WishedDate . '<br>' . $displayedPlanning;
				}
				elseif ($PlanningTools->getPlanningRangeLength($planning) == 1) {
					$displayedPlanning .= $planning->getStart('H:i') . ' - ';
				}
				else {
					$displayedPlanning .= _('Morning') . ': ' . $planning->getStart('H:i') . ' - ';
					$displayedPlanning .= $planning->getPause('H:i') . '<br />';
					$displayedPlanning .= _('Afternoon') . ': ' . $planning->getRestart('H:i') . ' - ';
				}
				
				$displayedPlanning .= $planning->getEnd('H:i') . '<br />';
			}
			else {
				$PlanningRange = _('No schedule found for delivery site.');
			}
		}
		else {
			$PlanningRange = _('No schedule found for delivery site.');
		}
        $PlanningInfos = '<strong>'._('Delivery possible').' : <br />';
        $PlanningInfos .= $PlanningRange . '</strong>';
		$return = $Comment . $WishedDate . '<br>' 
				. $displayedPlanning . $PlanningInfos;
        return $return;
    } 
} 

?>
