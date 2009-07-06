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

require_once('Objects/MovementType.const.php');
require_once('Objects/ActivatedMovement.php');
require_once('Objects/Command.const.php');

// volontairement mis ici pour ne pas planter le script du serveur XMLRPC
ini_set('max_execution_time', 120);

/**
 * Classe qui recupere les commandes pour l'execution des mouvements sur les 
 * zaurus.
 *
 */
class MovementExecutionDataProvider
{
    /**
     * Constructor
     *
     * @param UserAccount $user   The connected user
     * @param string      $cmdnum An optional order number pattern
     * 
     * @access public 
     * @return void
     */
    public function __construct($user, $cmdnum=false, $cmdtype = false)
    {
        $this->user = $user;
        $this->cmdnumber = $cmdnum==false?'%':str_replace('*', '%', $cmdnum);

        if( $cmdtype == false ) {
            $this->filtercmdtype = new FilterRule(
                'Command.Type',
                FilterRule::OPERATOR_LIKE,
                '%');
        } else {
            $this->filtercmdtype = new FilterRule(
                'Command.Type',
                FilterRule::OPERATOR_EQUALS ,
                $cmdtype);
        }
    }

    /**
     * Retourne le tableau des commandes recuperees en base de donnees.
     * 
     * @access public 
     * @return array The orders array
     */
    public function execute()
    {
        // on récupère la collection de commanditems filtrés
        $filter = new FilterComponent(
            new FilterComponent(
                new FilterComponent(
                    FilterComponent::OPERATOR_OR,
                    new FilterRule(
                        'Command.State',
                        FilterRule::OPERATOR_EQUALS,
                        Command::ACTIVEE
                    ),
                    new FilterRule(
                        'Command.State',
                        FilterRule::OPERATOR_EQUALS,
                        Command::LIV_PARTIELLE
                    ),
                    new FilterRule(
                        'Command.State',
                        FilterRule::OPERATOR_EQUALS,
                        Command::FACT_PARTIELLE
                    ),
                    new FilterRule(
                        'Command.State',
                        FilterRule::OPERATOR_EQUALS,
                        Command::REGLEMT_PARTIEL
                    ),
                    new FilterRule(
                        'Command.State',
                        FilterRule::OPERATOR_EQUALS,
                        Command::PREP_PARTIELLE
                    )
                ),
                FilterComponent::OPERATOR_AND,
                new FilterComponent(
                    FilterComponent::OPERATOR_AND,
                    new FilterRule(
                        'Command.CommandNo',
                        FilterRule::OPERATOR_LIKE,
                        $this->cmdnumber
                    ),
                    new FilterRule(
                        'Command.IsEstimate',
                        FilterRule::OPERATOR_EQUALS,
                        0
                    ),
                    $this->filtercmdtype
                )
            ),    
            FilterComponent::OPERATOR_AND,
            new FilterComponent(
                FilterComponent::OPERATOR_OR,
                new FilterRule(
                    'ActivatedMovement.State',
                    FilterRule::OPERATOR_EQUALS,
                    ActivatedMovement::CREE
                ),
                new FilterRule(
                    'ActivatedMovement.State',
                    FilterRule::OPERATOR_EQUALS,
                    ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT
                )
            )
        ); 
        $order = array(
            'ActivatedMovement.StartDate' => SORT_ASC,
            'ActivatedMovement.Type' => SORT_ASC
        );
        // on limite à 50
        $col = Object::loadCollection('ProductCommandItem', $filter, $order, array(), 0, 1, 50); 
        // on instancie le bon executiondata
        $commands = array();
        $lastCommandID = 0;
        foreach ($col as $pci) {
            $command = $pci->getCommand();
            if ($lastCommandID == $command->getId()) {
                continue;
            }
            if (commandIsCompatibleWithUser($pci, $this->user)) {
                $commands[] = $command;
                $lastCommandID = $command->getId();
            }
            unset($pci, $command);
            if (count($commands) == 3) {
                break;
            }
        } 
        return $commands;
    } 
} 

/**
 * Retourne true si l'utilisateur peut traiter cette commande, false sinon
 * 
 * @access public
 * @return boolean 
 */
function commandIsCompatibleWithUser($commandItem, $user){
    // XXX Voir avec dalhia pourquoi c'est désactivé
    return true;

    require_once('SQLRequest.php');
    require_once('Objects/Operation.const.php');
    // l'ID de la chaine rattachée à l'item de la commande
    $activatedChainID = $commandItem->getActivatedChainID();
    // l'ID de l'acteur rattaché à l'utilisateur
    $actorID = $user->getActorID();
    // on charche une (ou des) operation de stockage dont la chaine est celle 
    // en cours, et dont l'acteur correspond à notre utilisateur 
    $result = Request_CommandIsCompatibleWithUserForExecution(
        $activatedChainID, $actorID, OPERATION_STOC);
    if (isset($result->fields) && is_array($result->fields)) {
        return intval($result->fields['count(*)']) > 0;
    }
    return false;
}

?>
