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

class Chain extends _Chain {
    // Constructeur {{{

    /**
     * Chain::__construct()
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
     * Methode addon qui va chercher toutes les chaines 
     * auxquelles le produit est affecté
     * 
     * @access public 
     * @return collection 
     */
    function getProductCollection()
    {
        $ProductChainLinkCollection = $this->getProductChainLinkCollection();
        require_once("Product/ProductCollection.php");
        $ProductCollection = new ProductCollection();
        if (!Tools::isEmptyObject($ProductChainLinkCollection)) {
            for($i = 0; $i < $ProductChainLinkCollection->GetCount(); $i++) {
                unset($ProductChainLink);
                $ProductChainLink = $ProductChainLinkCollection->GetItem($i);
                $ProductCollection->SetItem($ProductChainLink->GetProduct());
            } 
        } 
        return $ProductCollection;
    } 

    var $ListTaskName = Array();
    /**
     * Methode addon qui va retouner un tableau ListTaskName contenant le nom et le changement d'etat de toutes les TaskTypes liees aux taches de la chaine 
     * trié par ordre d'opérations ds la chaine et de taches dans l'opération
     * 
     * @access public 
     * @return Array ()
     */
    function LoadTaskName()
    {
        require_once('Objects/Task.inc.php');
        $ListTaskName = array();
        $ChainOperationCollection = $this->GetChainOperationCollection();
        $ChainOperationCollection->Sort('Order'); //trie la collection des operations de la chaine ds l'ordre (order) croissant
        for ($k = 0; $k < $ChainOperationCollection->getCount(); $k++) {
            $ChainOperation = $ChainOperationCollection->getItem($k);
            $OrderOperation = $ChainOperation->GetOrder();
            $ChainTaskCollection = $ChainOperation->GetChainTaskCollection();
            $ChainTaskCollection->Sort('Order'); //trie la collection des taches de l'operation ds l'ordre (order) croissant
            for ($j = 0; $j < $ChainTaskCollection->getCount(); $j++) {
                $ChainTask = $ChainTaskCollection->getItem($j);
                $Task = $ChainTask->GetTask();
                $OrderTask = $ChainTask->GetOrder(); 
                // $TaskType = $Task->GetTaskType();
                // $ListTaskNameField['Name'] = $TaskType->GetName();
                // $ListTaskNameField['ChangeState'] = $TaskType->GetChangeState();
                // $ListTaskNameField['TaskId'] = $Task->GetId();
                $ListTaskNameField['Task'] = $Task;

                if(isPrincipalTask($Task)) {
                    $ListTaskNameField['ChangeState'] = 1;
                } else {
                    $ListTaskNameField['ChangeState'] = 0;
                } 
                $ListTaskNameField['OrderOperation'] = $OrderOperation;
                $ListTaskNameField['OrderTask'] = $ChainTask->GetOrder();
                $ListTaskName[] = $ListTaskNameField;
            } 
        }
        $this->ListTaskName = $ListTaskName;
        return $ListTaskName;
    } 

    /**
     * Methode addon qui va tester si la chaine a une tache de type conditionnement ou de deconditionnement
     * 
     * @access public 
     * @return boolean 
     */
    function hasConditionnedTypeTaskOrUnConditionnedTypeTask($Tab)
    {
        reset($Tab);
        $tab = $Tab;

        while (list($key, $val) = each ($tab)) {
            $Task = $val["Task"];
            if ((IsConditionningTask($Task)) || (IsUnConditionningTask($Task))) {
                return 1;
                exit;
            } 
        } 
        return 0;
    } 

    /**
     * Methode addon qui va retouner vrai si la chaine contient une tache de type NameTask1 suivie d'une tache de type NameTask2 sans entre les deux de tache avec un changement d'etat = 1
     * 
     * @access public 
     * @return boolean 
     */
    function HasTypeTask1BeforeTypeTask2($TypeTask1, $TypeTask2, $Tab)
    {
        reset($Tab);
        $tab = $Tab;

        switch ($TypeTask1) {
            case TASK_PACK:
                $Fct1 = "IsPackingTask";
                break;
            case TASK_CONDITION:
                $Fct1 = "IsConditionningTask";
                break;
            case TASK_GROUP:
                $Fct1 = "IsGroupingTask";
                break;
            case TASK_UNLOAD:
                $Fct1 = "IsUnLoadTask";
                break;
        } 
        // echo "fct1 = ".$Fct1."<br>";
        switch ($TypeTask2) {
            case TASK_PACK:
                $Fct2 = "IsPackingTask";
                break;
            case TASK_UNPACK:
                $Fct2 = "IsUnPackingTask";
                break;
            case TASK_GROUP:
                $Fct2 = "IsGroupingTask";
                break;
            case TASK_UNGROUP:
                $Fct2 = "IsUnGroupingTask";
                break;
        } 
        // echo "fct2 = ".$Fct2."<br>";
        while (list($key, $val) = each ($tab)) {
            // $Name = $val["Name"];
            $Task = $val["Task"];
            $ChangeState = $val["ChangeState"]; 
            // on teste si la tache est de type TypeTask1
            if ($Fct1($Task)) { // echo "oui ".$Task->GetName();
                $Name1 = $TypeTask1;
            } 
            // on teste si la tache courante a un changement d'etat a 1 et n'est pas de type TypeTask1 ni de type TypeTask2
            // et si on a trouve avant une tache de type TypeTask1
            if ((!$Fct1($Task)) && (!$Fct2($Task)) && ($Name1 == $TypeTask1) && ($ChangeState == 1)) {
                return 0;
                exit;
            } 
            // on teste si la tache est de type TypeTask2
            if (($Name1 == $TypeTask1) && ($Fct2($Task))) {
                return 1;
            } 
        } 
        return 0;
    } 

    /**
     * Methode addon qui va retouner vrai si le tableau tab contient une tache de type egal a TypeTask
     * 
     * @access public 
     * @return boolean 
     */

    function HasTypeTask($TypeTask, $Tab)
    {
        reset($Tab);
        $tab = $Tab;

        switch ($TypeTask) {
            case TASK_PACK:
                $Fct = "IsPackingTask";
                break;
            case TASK_UNPACK:
                $Fct = "IsUnPackingTask";
                break;
            case TASK_GROUP:
                $Fct = "IsGroupingTask";
                break;
            case TASK_UNGROUP:
                $Fct = "IsUnGroupingTask";
                break;
            case TASK_ACTIVATION:
               $Fct = 'IsActivationTask';
               break;
        } while (list($key, $val) = each ($tab)) {
            $Task = $val["Task"];
            if ($Fct($Task)) {
                return true;
                exit;
            } 
        } 
        return 0;
    }

}

?>