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

require_once('Objects/Task.inc.php');
 
define("LOAD_OR_UNLOAD", 1);
define("PACK_OR_UNPACK", 2);
define("GROUP_OR_UNGROUP", 3);

/**
 * 
 * @access public 
 * @return void 
 */

function GetTaskType($Task)
{
    if($Task instanceof Task) {
        $Id = $Task->getId();
	
	} else {
        if ($Task instanceof ActivatedChainTask) {
            $Id = Tools::getValueFromMacro($Task, '%Task.Id%');
        } else {
            return false;
        } 
    } 

    $PackOrUnPackArray = array(TASK_PACK, TASK_UNPACK);
    $GroupOrUngroupArray = array(TASK_GROUP, TASK_UNGROUP);
    $LoadOrUnloadArray = array(TASK_LOAD, TASK_UNLOAD);
    $print_task = array (TASK_BL_EDITING,
        TASK_BL_EDITING,
        TASK_LETTRE_VOITURE_EDITING,
        TASK_COLISAGE_LIST_EDITING,
        TASK_ETIQUETTE_COLIS,
        TASK_ETIQUETTE_PROD,
        TASK_ETIQUETTE_REGROUPEMENT,
        TASK_ETIQUETTE_DIRECTION,
        TASK_ACCUSE_RECEPTION,
        TASK_MARITIM_TRANS_LETTER,
        TASK_AERIEN_TRANS_LETTER);

    if (in_array($Id, $LoadOrUnloadArray)) {
        return LOAD_OR_UNLOAD;
    } else if (in_array($Id, $PackOrUnPackArray)) {
        return PACK_OR_UNPACK;
    } else if (in_array($Id, $GroupOrUngroupArray)) {
        return GROUP_OR_UNGROUP;
    } else if (in_array($Id, $print_task)) {
        switch ($Id) {
            case TASK_BL_EDITING:
            case TASK_BL_EDITING:
            case TASK_ETIQUETTE_COLIS:
            case TASK_ETIQUETTE_DIRECTION:
            case TASK_ETIQUETTE_PROD:
            case TASK_COLISAGE_LIST_EDITING:
                return $Id;
            default :
                return false;
        } // switch
    } 
    return false;
} 

/**
 * GetTaskSpecificType :
 * 
 * @param  $ : $Task [object] tache en execution ou activée
 * retourne l'id de la tache à partir d'une tache
 * 	---> en execution 
 * 	---> Activée
 *     sinon renvoie une excetion
 */
function GetTaskSpecificType($Task)
{
	if ($Task instanceof Task) {
	    return $Task->getId();
	}
    if ($Task instanceof ActivatedChainTask) {
        return Tools::getValueFromMacro($Task, '%Task.Id%');
    }
    return false; 
} 

?>
