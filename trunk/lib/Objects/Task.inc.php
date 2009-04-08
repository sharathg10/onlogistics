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

require_once('Objects/Task.const.php');

/**
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 **/
function getTaskId($Task){
    if ($Task instanceof Task) {
        return $Task->getId();
    }
    if ($Task instanceof ActivatedChainTask) {
        return $Task->getTaskID();
    }
}

/**
 * Retourne true si l'activatedChainTask passé en param
 * est une tache d'edition
 *
 * @access public
 * @return void
 * @param object
 * @param object ActivatedTask ou Task $Task
 */
function IsEditionTask($Task)
{
    return in_array(getTaskId($Task),
      array(TASK_BL_EDITING,
            TASK_BE_EDITING,
            TASK_LETTRE_VOITURE_EDITING,
            TASK_COLISAGE_LIST_EDITING,
            TASK_ETIQUETTE_COLIS,
            TASK_ETIQUETTE_PROD,
            TASK_ETIQUETTE_REGROUPEMENT,
            TASK_ETIQUETTE_DIRECTION));
}

/**
 * Retourne true si l'activatedChainTask passé en param
 * est une tache d'edition de documents
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 */
function IsDocumentEditionTask($Task)
{
    return in_array(getTaskId($Task),
      array(TASK_BL_EDITING,
            TASK_BE_EDITING,
            TASK_LETTRE_VOITURE_EDITING,
            TASK_COLISAGE_LIST_EDITING));
}

/**
 * Retourne true si l'activatedChainTask passé en param
 * est une tache d'edition d'étiquettes
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 */
function IsLabelEditionTask($Task)
{
    return in_array(getTaskId($Task),
      array(TASK_ETIQUETTE_COLIS,
            TASK_ETIQUETTE_PROD,
            TASK_ETIQUETTE_REGROUPEMENT,
            TASK_ETIQUETTE_DIRECTION));
}

/**
 * Retourne true si l'activatedChainTask passé en param
 * est une tache de transport
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 */
function IsTransportTask($Task)
{
    return in_array(getTaskId($Task),
      array(TASK_GROUND_TRANSPORT,
            TASK_SEA_TRANSPORT,
            TASK_INLAND_WATERWAY_TRANSPORT,
            TASK_RAILWAY_TRANSPORT,
            TASK_AIR_TRANSPORT));
}

/**
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 */
function IsPackingTask($Task)
{
    return (getTaskId($Task) == TASK_PACK);
}

/**
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 */
function IsUnPackingTask($Task)
{
    return (getTaskId($Task) == TASK_UNPACK);
}

/**
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 */
function IsGroupingTask($Task)
{
    return (getTaskId($Task) == TASK_GROUP);
}

function isGroupingOrStockExitTask($task) {
    return (IsGroupingTask($task) || (getTaskId($task) == TASK_STOCK_EXIT));
}
/**
 *
 * @access public
 * @return void
 * @param object ActivatedTask ou Task $Task
 */
function IsUnGroupingTask($Task)
{
    return (getTaskId($Task) == TASK_UNGROUP);
}

//retourne TRUE si on a une tache de type Conditionning (conditionnement)
function IsConditionningTask($Task)
{
    return (getTaskId($Task) == TASK_CONDITION);
}

//retourne TRUE si on a une tache de type UnConditionning (deconditionnement)
function IsUnConditionningTask($Task)
{
    return (getTaskId($Task) == TASK_UNCONDITION);
}

//retourne TRUE si on a une tache de type TASK_LOAD (chargement)
function IsLoadTask($Task)
{
    return (getTaskId($Task) == TASK_LOAD);
}

//retourne TRUE si on a une tache de type TASK_UNLOAD (dechargement)
function IsUnLoadTask($Task)
{
    return (getTaskId($Task) == TASK_UNLOAD);
}

//retourne TRUE si on a une tache de type TASK_INVOICE_EDITING (Edition facture)
function isInvoicingTask($Task)
{
    return (getTaskId($Task) == TASK_INVOICE_EDITING);
}

//retourne TRUE si on a une tache de type TASK_FLY (Vol)
function isFlightTask($Task)
{
    return (getTaskId($Task) == TASK_FLY);
}

//retourne TRUE si on a une tache de type TASK_FLY_PREPARATION (Preparation Vol)
function isFlightPreparationTask($Task)
{
    return (getTaskId($Task) == TASK_FLY_PREPARATION);
}

/**
 * Détermine si la tache passée en paramètre
 * est en mesure de renvoyer un type d'unité manipulée
 * dans la chaine.
 *
 * Ex: Avec une tache de chargement on n'est pas en mesure de
 * déterminer le type d'unité maniplée, alors qu'avec une tache
 * de Regroupement on sait que l'unité manipulée est le packaging
 *
 * @param object $task une ActivatedChainTask
 * @return boolean
 */
function isTypeProviderTask($Task) {
    require_once('GetTaskType.php');
    return in_array(GetTaskSpecificType($Task),
      array(TASK_PACK,
              TASK_UNPACK,
            TASK_GROUP,
            TASK_UNGROUP,
            TASK_ETIQUETTE_COLIS,
            TASK_ETIQUETTE_PROD,
            TASK_ETIQUETTE_REGROUPEMENT));
}

//retourne vrai/faux selon qu'on a une tache de changement d'etat
function isPrincipalTask($Task) {
    require_once('GetTaskType.php');
    return in_array(
        getTaskSpecificType($Task),
        array(
            TASK_PACK,
            TASK_UNPACK,
            TASK_GROUP,
            TASK_UNGROUP,
            TASK_LOAD,
            TASK_UNLOAD,
            TASK_CONDITION,
            TASK_UNCONDITION
        )
    );
}

/**
 * Retourne true si la tache est crétrice de box et false sinon
 *
 * @access public
 * @param object ActivatedTask $ack
 * @return bool
 */
function isBoxCreatorTask($ack) {
    $task = $ack->getTask();
    return $task->getIsBoxCreator();
}

/**
 * Retourne true si la tache est une tache d'assemblage
 *
 * @access public
 * @param object ActivatedTask ou Task $ack
 * @return bool
 */
function isAssemblyTask($ack) {
    $taskID = $ack->getTaskId();
    return $taskID == TASK_ASSEMBLY || $taskID == TASK_SUIVI_MATIERE;
}

/**
 * Retourne true si la tache est une tache d'édition de packinglist
 *
 * @access public
 * @param object ActivatedTask ou Task $ack
 * @return bool
 */
function isPackingListEditionTask($ack) {
    return $ack->getTaskId() == TASK_COLISAGE_LIST_EDITING;
}

/**
 * Retourne true si la tache est une tache d'activation
 *
 * @access public
 * @param object ActivatedTask ou Task $ack
 * @return bool
 */
function isActivationTask($ack)
{
    $taskID = $ack->getTaskId();
    return $taskID == TASK_ACTIVATION || $taskID == TASK_GENERIC_ACTIVATION ;
}

/**
 * Retourne true si la tache est une tache d'activation generique
 *  commande fournisseur + fabrication produit ...
 *
 * @param object ActivatedTask ou Task $ack
 * @return bool
 */
function isGenericActivation($ack) {
    return $ack->getTaskId() == TASK_GENERIC_ACTIVATION;
}

/**
 * Retourne true si la tache est une tache de production
 *
 * @param object ActivatedTask ou Task $ack
 * @return bool
 */
function isProductionTask($ack) {
    return in_array(getTaskId($ack), getProductionTaskIds());
}

/**
 * Retourne les ids des taches de production
 *
 * @return array of integer
 */
function getProductionTaskIds() {
    return array(
        TASK_ASSEMBLY, TASK_SUIVI_MATIERE, TASK_BUILDING, TASK_MANUFACTURING,
        TASK_TOURNAGE, TASK_SOUDURE, TASK_POLISSAGE, TASK_FRAISAGE,
        TASK_CHECKING_PRODUCTION, TASK_SCIAGE, TASK_MICROBILLAGE,
        TASK_CHAUDRONNERIE, TASK_AJUSTAGE, TASK_CTAI, TASK_CTEP, TASK_CTES,
        TASK_TRIT, TASK_EPAR, TASK_BNOR, TASK_BNAR, TASK_DECA, TASK_PEIN
    );
}


/**
 * Retourne true si la tache est une tache de conseil
 *
 * @param object ActivatedTask ou Task $ack
 * @return bool
 */
function isConsultingTask($ack) {
    return in_array(getTaskId($ack), getConsultingTaskIds());
}

/**
 * Retourne les ids des taches de conseil
 *
 * @return array of integer
 */
function getConsultingTaskIds() {
    $ret = array(TASK_WAITING);
    $consts = get_defined_constants();
    foreach ($consts as $k=>$v) {
        if (substr($k, 0, 9) == "TASK_GED_") {
            $ret[] = $v;
        }
    }
    return $ret; 
}

?>
