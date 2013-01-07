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

require_once('Objects/Command.php');
require_once('Objects/Command.const.php');
require_once('Objects/CommandItem.php');
require_once('Objects/ProductCommand.php');
require_once('Objects/ProductCommandItem.php');
require_once('Objects/ChainCommand.php');
require_once('Objects/ChainCommandItem.php');
require_once('Objects/Task.inc.php');
require_once('Objects/TVA.inc.php');
require_once('ProductCommandTools.php');
require_once('InvoiceItemTools.php');
require_once('ActivateChain.php');
require_once('PlanningTools.php');
require_once('Scheduler/Scheduler.php');
require_once('BoxTools.php');
require_once('Quantificator/ChainCommandQuantificator.php');
require_once('Quantificator/ProductCommandQuantificator.php');
require_once('Quantificator/AssemblyQuantificator.php');
require_once('AlertSender.php');
require_once('GenerateDocument.php');
require_once('FormatNumber.php');

// }}}
// constantes {{{

define('E_NO_COMMAND', _('You must create an order first.'));
define('E_ACTIVATE_CHILD_PROCESS', _('Activation of children chains is impossible.'));
define('E_COMMANDNO_EXISTS', _('Provided order number is already allocated, please correct.'));
define('E_NO_DATA_EXISTS_FOR_EXTERNAL_CMD', _('The external database doesn\'t contain needed data.'));
define('E_COMMAND_PTHT_LOWER_THAN_MINI_TO_ORDER', 
    _('Amount excl. VAT is lower than minimum amount to order (%s %s). Please correct or contact customer department.'));
if (!defined('DEBUG')) {
    define('DEBUG', false);
}

// }}}

/**
 * Classe de gestion d'une commande onlogistics.
 *
 * @package    onlogistics
 * @subpackage lib
 */
class CommandManager{
    // propriétés {{{

    /**
     * La commande est ou pas le fruit d'une activation
     *
     * @var    boolean $isRootCommand : false si commande acitvee par une autre
     * @access public
     */
    public $isRootCommand = true;

    /**
     * Booléen qui détermine si la commande est un devis.
     *
     * @var    boolean $isEstimate
     * @access public
     */
    public $isEstimate = false;

    /**
     * Le tableau des alertes a envoyer hors transaction sql
     *
     * @var    mixed $alertsToSend : array
     * @static
     * @access public
     */
    public static $alertsToSend = array();

    /**
     * Le type de commande (ProductCommand, ChainCommand, etc...)
     *
     * @var    string $commandType le type de commande
     * @access public
     */
    public $commandType = false;

    /**
     * Le type de commande de produit (Supplier ou customer)
     *
     * @var    string $commandType le type de commande
     * @access public
     */
    public $productCommandType = false;

    /**
     * L'objet Command
     *
     * @var    object Command $command
     * @access public
     */
    public $command = false;

    /**
     * Doit on utiliser une transaction ?
     *
     * @var    boolean useTransaction
     * @access public
     */
    public $useTransaction = false;

    /**
     * Tableau des objets Chain, utilisé par _getChain()
     *
     * @var    array $_chainArray
     * @access private
     */
    private $_chainArray = false;

    /**
     * Tableau des chaines activées
     *
     * @var    array $_activatedChainArray
     * @access public
     */
    private $_activatedChainArray = array();

    /**
     * Tableau des commanditem déjà traités.
     *
     * @var    array $_processedCommandItems
     * @access public
     */
    private $_processedCommandItems = array();

    // }}}
    // CommandManager::__construct() {{{

    /**
     * Constructeur.
     * 
     * Prend un tableau de paramètres optionnel, qui peut être:
     * <code>
     * array(
     *     'CommandType' => 'ProductCommand', // type de commande
     *     'ProductCommandType' => Command::TYPE_CUSTOMER, // type de cmd pdt
     *     'UseTransaction' => false, // booléen, utiliser une transaction
     *     'IsRootCommand' => true, // false si c'est une commande fille
     *     'IsEstimate' => false // mettre à true si devis
     *     'FromEstimateId' => false // mettre à true si creation de cmde via un devis
     * );
     * </code>
     *
     * @param  array $params
     * @access protected
     */
    public function __construct($params = array())
    {
        $this->commandType = isset($params['CommandType']) ?
            $params['CommandType'] : 'Command';
        $this->productCommandType = isset($params['ProductCommandType']) ?
            $params['ProductCommandType'] : false;
        $this->useTransaction = isset($params['UseTransaction']) ?
            $params['UseTransaction'] : false;
        $this->isRootCommand = isset($params['IsRootCommand']) ?
            $params['IsRootCommand'] : true;
        $this->isEstimate = isset($params['IsEstimate']) ?
            $params['IsEstimate'] : false;
       $this->auth = Auth::singleton();
        if ($this->useTransaction) {
            // démarre une nouvelle transaction
            Database::connection()->startTrans();
            // On MAJ les QV et suppr. les acm si necessaire, DANS la transaction
            if (isset($params['FromEstimateId']) && $params['FromEstimateId'] != 0) {
                $fromEstimate = Object::load('Command',  array('Id' => $params['FromEstimateId']));
                $fromEstimate->updateQvBeforeDeleteACM(true);
                $fromEstimate->delete();
            }
        }
    }

    // }}}
    // CommandManager::createCommand() {{{

    /**
     * Crée une nouvelle commande avec les paramètres $params.
     * le tableau params doit comporter les informations suivantes:
     *
     * 'Incoterm_Code' => le code de l'incoterm
     * 'WishedStartDate' => la date souhaitée de début (YYYY-MM-DD HH:MM:SS)
     * 'WishedEndDate' => la date souhaitée de début (YYYY-MM-DD HH:MM:SS)
     * 'Comment' => le commentaire de la commande
     * 'Expeditor_Code' => le code de l'exediteur
     * 'Destinator_Code' => le code du destinataire
     * 'Handing' => la remise globale (pourcentage, montant ou fraction)
     * 'Port' => les frais de port (montant)
     * 'Packing' => les frais d'emballage (montant)
     * 'Insurance' => les frais d'assurance (montant)
     * 'Currency_Code' => la devise de la commande
     * 'ChainID' => pour une commande de transport, l'ID de la chaine commandée
     *
     * CustomerRemExcep, 'CurrencyId' pour une pdtCommand:
     * geres par validateCommand()
     *
     * @access public
     * @param array $params
     * @return mixed true ou une exception
     **/
    public function createCommand($params)
    {
        // check et formatage des arguments
        $params = $this->_checkAndFormatCommandParams($params);
        if (Tools::isException($params)) {
            return $params;
        }
        // création de la commande (en instanciant la bonne classe)
        $commandType = $this->commandType;
        $this->command = new $commandType();
        $this->command->setCommandDate(date('Y-m-d H:i:s', time()));
        if ($this->isEstimate) {
            $this->command->setIsEstimate(true);
        }
        // assignation des paramètres à la commande
        foreach($params as $name=>$value){
            $setter = 'set' . $name;
            if (method_exists($this->command, $setter)) {
                $this->command->$setter($value);
            } 
        }
        // sauvegarde (dans transaction)
        $this->command->save();
        $this->_debug('* Création de la commande ' . $this->command->getId());
        return true;
    }

    // }}}
    // CommandManager::addCommandItem() {{{

    /**
     * Crée un nouvel item de commande avec les paramètres $params et l'ajoute
     * à la commande en cours.
     * le tableau params doit comporter les informations suivantes:
     * XXX TODO: décrire les arguments à passer dans $params
     *
     * @access public
     * @param array $params
     * @return mixed true ou une exception
     **/
    public function addCommandItem($params)
    {
        if (false == $this->command) {
            return new Exception(E_NO_COMMAND);
        }
        // check et formatage des arguments
        $params = $this->_checkAndFormatCommandItemParams($params);
        if (Tools::isException($params)) {
            return $params;
        }
        // création du command item (en instanciant la bonne classe)
        $commandItemType = $this->commandType . 'Item';
        $commandItem = new $commandItemType();
        $commandItem->setCommand($this->command);
        // référence vers la collection de commanditems de la commande
        $commandItemCollection = $this->command->getCommandItemCollection();
        // assignation des paramètres au commanditem
        foreach($params as $name=>$value){
            $setter = 'set' . $name;
            if (method_exists($commandItem, $setter)) {
                $commandItem->$setter($value);
            }
        }
        // Controle des qtes mini commandables, si cmde CLIENT
        if ($this->productCommandType == Command::TYPE_CUSTOMER) {
            $pdtID  = $commandItem->getProductId();
            $destinator   = $this->command->getDestinator();

            if ($destinator instanceof Actor) {
                $pqcMapper = Mapper::singleton('ProductQuantityByCategory');
                $pqc = $pqcMapper->load(
                    array(
                        'Product' => $pdtID,
                        'Category' => $destinator->getCategoryId()
                    )
                );
                if (!Tools::isEmptyObject($pqc)
                && !$pqc->isValidQuantity($commandItem->getQuantity())) {
                    return new Exception(
                        sprintf(
                            _('Wrong quantity for product %s.'),
                            Tools::getValueFromMacro($commandItem,
                                '%Product.BaseReference%')
                        )
                    );
                }
            }
        }
        // sauvegarde (dans transaction)
        $commandItem->save();
        $this->_debug(' -> Création du commanditem ' . $commandItem->getId());
        // ajout du command item dans la collection
        $commandItemCollection->setItem($commandItem);
        return true;
    }

    // }}}
    // CommandManager::activateProcess() {{{

    /**
     * Active, quantifie et planifie la chaîne correspondante
     *
     * @access public
     * @param object $activationTask la tache d'activation (uniquement pour les
     * commandes filles)
     * @return mixed true ou une exception
     **/
    public function activateProcess($activationTask=false)
    {
        if ($this->commandType == 'ChainCommand') {
            $result = $this->_activateProcess(
                $this->command->getChain(),
                array(),
                $activationTask
            );
            if (Tools::isException($result)) {
                return $result;
            }
            return true;
        }
        try {
            $this->_getChainArray();
        } catch (Exception $exc) {
            return $exc;
        }
        $count = count($this->_chainArray);
        // la chaine n'a pu être trouvée
        if ($count == 0) {
            return new Exception(
                _('Supplier properties are incomplete, order cannot be completed, please contact your admin.')
            );
        }
        for ($i=0; $i<$count; $i++) {
            list($chain, $pdtIds) = $this->_chainArray[$i];
            // on récupère la chaîne correspondante à la commande
            $result = $this->_activateProcess($chain, $pdtIds, $activationTask);
            if (Tools::isException($result)) {
                return $result;
            }
        }
        return true;
    }

    // }}}
    // CommandManager::_activateProcess() {{{

    /**
     * Active, quantifie et planifie la chaîne correspondante
     *
     * @access public
     * @param object $activationTask la tache d'activation (uniquement pour les
     * commandes filles)
     * @return mixed true ou une exception
     **/
    public function _activateProcess($chain, $pdtIds = array(), $activationTask = false)
    {
        $this->_debug('Commande : '.$this->command->_Id);
        $this->_debug('--- Activation de la chaine : ' . $chain->getReference());
        // Activation de la chaîne
        $activatedChain = activateChain($chain, $this->command);
        $this->_activatedChainArray[] = $activatedChain;
        if (Tools::isException($activatedChain)) {
            return $activatedChain;
        }
        $this->_debug('--- ActivatedChain : ' . $activatedChain->getReference());
        // assignation de la chaîne aux commanditems et création des boxes s'il
        // y a lieu
        $cmiCol = $this->command->getCommandItemCollection();
        $count = $cmiCol->getCount();
        for($i = 0; $i < $count; $i++){
            $cmi = $cmiCol->getItem($i);
            if ($this->commandType == 'ChainCommand') {
                $cmi->setActivatedChain($activatedChain);
                $cmi->save();
                // on ne crée les box que si ce n'est pas un devis
                if (!$this->isEstimate && createInitialBoxes($activatedChain, $cmi)) {
                    $this->_debug('* Création des boxes pour le commanditem ' .
                        $cmi->getId());
                }
            } else {
                if ($this->command->getCadenced() &&
                    in_array($cmi->getId(), $this->_processedCommandItems)) {
                    continue;
                }
                if (count($pdtIds) > 0 && !in_array($cmi->getProductId(), $pdtIds)) {
                    continue;
                }
                $cmi->setActivatedChain($activatedChain);
                $cmi->save();
                // on ne crée les box que si ce n'est pas un devis
                if (!$this->isEstimate && createInitialBoxes($activatedChain, $cmi)) {
                    $this->_debug('* Création des boxes pour le commanditem ' .
                        $cmi->getId());
                }
                if ($this->command->getCadenced()) {
                    $this->_processedCommandItems[] = $cmi->getId();
                    break;
                }
            }
        }
        // Quantification de la chaîne
        $this->_debug('--- Quantification de de la chaine '.$activatedChain->getReference());
        if ($this->commandType == 'ProductCommand') {
            $quantificator = new ProductCommandQuantificator($activatedChain, $this->command);
        } else {
            $quantificator = new ChainCommandQuantificator($activatedChain, $this->command);
        }
        $result = $quantificator->execute();
        if (Tools::isException($result)) {
            return $result;
        }
        if ($this->commandType == 'ProductCommand') {
            // Quantification de l'assemblage...
            $quantificator = new AssemblyQuantificator($activatedChain, $this->command);
            $result = $quantificator->execute();
            if (Tools::isException($result)) {
                return $result;
            }
        }
        $activatedChain->save();
        // Adaptation éventuelle de la date souhaitée si process fils
        $scheduler = new Scheduler();
        if ($this->command->getCadenced()) {
            $object = $cmi;
            $method1 = 'getWishedDate';
            $getter_start = 'getWishedDate';
            $getter_end   = false;
            $setter_start = 'setWishedDate';
            $setter_end   = false;
        } else {
            $object = $this->command;
            $getter_start = 'getWishedStartDate';
            $getter_end   = 'getWishedEndDate';
            $setter_start = 'setWishedStartDate';
            $setter_end   = 'setWishedEndDate';
        }
        if ($this->commandType == 'ProductCommand' && 
            $activationTask instanceof ActivatedChainTask &&
            $activationTask->getDelta()) {
            $dateType = $activationTask->getWishedDateType();
            $pivotTask = $activatedChain->getPivotTask();
            $pivotOpe  = $pivotTask->getActivatedOperation();
            $actor     = $pivotOpe->getActor();
            $site      = $actor->getMainSite();
            $planning  = $site->getPlanning();
            $ptools    = new PlanningTools($planning);
            $wsDate    = $object->$getter_start();
            $weDate    = $getter_end?$object->$getter_end():0;

            $refDate   = $scheduler->getReferenceDate($wsDate, $weDate);
            $available = $scheduler->actorAvailableForPivotTask(
                $ptools, $refDate);
            if ($available !== true) {
                // l'acteur n'est pas dispo il faut décaler la date
                // souhaitée...
                $method = ($dateType==ActivatedChainTask::WISHED_START_DATE_TYPE_COMMAND_PLUS_X
                       ||  $dateType==ActivatedChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X) ?
                    'getNextAvailableRange':'getPreviousAvailableRange';
                $range = $ptools->$method($refDate);

                // si un créneau a été trouvé
                /* Attention, ne fonctionne pas correctement si getPreviousAvailableRange:
                on 'remonte dans le temps', trouve le 1er créneau (range) possible
                mais on prend alors la borne inf du créneau, au lieu de prendre:
                (borne sup du créneau - durée de la tâche)
                Or, la durée de la tache, selon comment elle est calculée
                (proportionnelle à la volumétrie...) n'est pas déterminée à ce stade,
                sauf cas au forfait (DURATIONTYPE_FORFAIT), mais même dans ce cas,
                il faut être certain que cette durée est <= durée du créneau.
                Pour l'instant, on gère donc correctement les cas suivants:
                 - cas où $method == 'getNextAvailableRange'
                 - cas où $method == 'getPreviousAvailableRange' et DURATIONTYPE_FORFAIT
                   et $ack.Duration < $range['Start'] - $range['End']
                */
                if (is_array($range)) {
                    if ($method == 'getPreviousAvailableRange'
                    && $activationTask->getDurationType() == ActivatedChainTask::DURATIONTYPE_FORFAIT
                    && $activationTask->getDuration() <= $range['End'] - $range['Start']) {
                        $start = $range['End'] - $activationTask->getDuration();
                    }else {
                        $start = $range['Start'];
                    }
                    $object->$setter_start(
                        DateTimeTools::timeStampToMySQLDate($start));
                    if ($setter_end) {
                        $object->$setter_end('0000-00-00 00:00:00');
                    }
                }
            }
        }
        // Planification de la chaîne
        $this->_debug('--- Planification de de la chaine ' .
            $activatedChain->getReference());
        $result = $scheduler->scheduleActivatedChain(
            $activatedChain,
            $object->$getter_start(),
            $getter_end?$object->$getter_end():0
        );

        if (Tools::isException($result)) {
            return $result;
        }
        $iterator = new ActivatedChainIterator($activatedChain);
        $iterator->execute();

        // Mise a jour des indisponibilites si pas devis
        if (!$this->isEstimate) {
            $result = $activatedChain->updateUnavailabilities($this->command);
            if (Tools::isException($result)) {
                return $result;
            }
        }
        // tout est ok
        return true;
    }

    // }}}
    // CommandManager::validateCommand() {{{

    /**
     * Valide la commande, 3 étapes:
     *      - commite la transaction en cours
     *      - envoie le mail de confirmation de commande
     *      - envoie les éventuelles alertes
     * @param boolean debug: si true, ne commite pas la transaction
     * @access public
     * @return mixed true ou une exception
     **/
    public function validateCommand($debug=false)
    {   
        if ($this->isRootCommand) { // reinitialisation
            CommandManager::$alertsToSend = array();
        }
        if (!$this->isEstimate && 
            $this->productCommandType == Command::TYPE_CUSTOMER) {
            $ctype = 'client';
            // gestion de la remise par pourcentage du CA annuel, il faut
            // l'augmenter du montant restant de l'avoir
            $sc  = $this->command->getSupplierCustomer();
            if ($sc instanceof SupplierCustomer) {
                $date = $this->command->getCommandDate();
                $percent = $sc->getAnnualTurnoverDiscountPercent($date);
                if (false !== $percent) {
                    // si sujet à cette remise
                    $totalHT = $this->command->getTotalPriceHT()*($percent/100);
                    $sc->updateAnnualTurnoverDiscount($totalHT, $date);
                }
            }
        } else if ($this->productCommandType == Command::TYPE_SUPPLIER) {
            $ctype = 'fournisseur';
        } else {
            $ctype = 'transport';
        }
        $this->_debug('* Type de la commande: ' . $ctype);
        // si pas de numéro de commande passé en paramètre on en génère un
        $command = $this->command;
        $commandNo = $command->getCommandNo();
        if (empty($commandNo)) {
            // on récupère la chaîne correspondante à la commande
            $chainArray = $this->_chainArray;
            $chain = $chainArray[0][0];
            $command->generateCommandNo($chain);
        }


        $this->_debug('* Validation commande ' .  $command->getCommandNo());
        $method = $this->productCommandType==Command::TYPE_CUSTOMER?
                'getDestinator':'getExpeditor';
        $act = $command->$method();
        // devise (differente si commande de produit)
        if ($this->commandType == 'ProductCommand') {
            if ($act instanceof Actor) {
                // on assigne la devise du suppliercustomer
                $command->setCurrency($act->getCurrencyId());
                if ($this->productCommandType == Command::TYPE_CUSTOMER) {
                    $command->setCustomerRemExcep($act->getRemExcep());
                }
            }
        }
          
        // Gestion du ActorBankDetail
        $AccountingType = $act->getAccountingType();
        if (!Tools::isEmptyObject($AccountingType)) {
            $abd = $AccountingType->getActorBankDetail();
            if (!Tools::isEmptyObject($abd)) {
                $command->setActorBankDetail($abd);
            }
        }

        // donnees des mails d'alerte de stock eventuels
        $alerts = array();
        $hbrPercentAmountHT = 0;
        $hbrPercentAmountTTC = 0;
        $hbrPercent = 0;
        // calcul du prix
        if ($this->commandType == 'ChainCommand') {
            $prices = $this->_activatedChainArray[0]->getChainCommandCost($command);
            $ht = $prices['ht'];
            $ttc = $prices['ttc'];
        } else {
            // somme des prix ht des commanditems
            $hbrPercent = $this->command->findHandingByRangePercent();
            $cmiCol = $command->getCommandItemCollection();

            $count = $cmiCol->getCount();
            $ht = $ttc = 0;
            for($i = 0; $i < $count; $i++){
                $cmi = $cmiCol->getItem($i);
                $qty = $cmi->getQuantity();
                $cmiTVA = $cmi->getTVA();
                $cmiTVARate = (!Tools::isEmptyObject($cmiTVA))?$cmiTVA->getRate():0;
                $cmiHT  = $qty * $cmi->getPriceHT();
                if ($hbrPercent > 0) {
                    $cmiHT  -= $cmiHT * ($hbrPercent/100);
                    $hbrPercentAmountHT += $cmiHT * ($hbrPercent/100);
                    $hbrPercentAmountTTC += ($cmiHT * (1 + $cmiTVARate/100)) * ($hbrPercent/100);
                }
                $cmiHT = troncature($cmiHT);
                $ht  += $cmiHT;
                $ttc += troncature($cmiHT * (1 + $cmiTVARate/100));
                // creation des mouvements
                // peut contenir les donnees des mails d'alerte a envoyer
                // Si pas un devis ou si la pref EstimateBehaviour=1: creation des acm, 
                // MAJ des QV
                // XXX commente pour l'instant: on ne peut retrouver les devis actives par un devis...
                if ((!$this->isEstimate) || (Preferences::get('EstimateBehaviour', 0) == 1)) {
                    $currentAlert = $cmi->createActivatedMovement();
                    if (is_array($currentAlert) && !empty($currentAlert)) {
                        $alerts[] = $currentAlert;
                    }
                }
                $cmi->save();
            }
            if ($hbrPercent > 0) {
                $command->setHandingByRangePercent($hbrPercent);
            }
            // Creation des ActivatedMovements pour les entrees et sorties internes
            // uniquement si ce n'est pas un devis ou si la pref EstimateBehaviour=1
            if ((!$this->isEstimate) || (Preferences::get('EstimateBehaviour', 0) == 1)) {
                foreach ($this->_activatedChainArray as $ach) {
                    $ackCollection = $ach->getActivatedChainTaskCollection(
                        array('Task.Id'=>array(
                            TASK_INTERNAL_STOCK_ENTRY,
                            TASK_INTERNAL_STOCK_EXIT)
                        )
                    );
                    for($i=0;$i < $ackCollection->getCount(); $i++) {
                        $ack = $ackCollection->getItem($i);
                        $this->command->generateActivatedMovement($ack, $ach);
                    }
                }
            }
        }
        
        // Remise globale : ToDo
        // Pour l'instant, pas gere:
        // Si TotalPriceHT et TotalPriceTTC pas fournis en param a createCommand()
        if ($command->getTotalPriceHT() == 0) {
            // calcul des frais de port, emballage et assurance ht puis ttc
            $fraisHT  = $command->getPort() + $command->getPacking()
                        + $command->getInsurance();
            // si suppliercustomer->hastva on l'applique
            $sc  = $command->getSupplierCustomer();
            if ($sc instanceof SupplierCustomer && $sc->getHasTVA() == true) {
                $fraisTTC = $fraisHT
                    + troncature($command->getPort() * getTVARateByCategory(TVA::TYPE_DELIVERY_EXPENSES) / 100)
                    + troncature($command->getPacking() * getTVARateByCategory(TVA::TYPE_PACKING) / 100)
                    + troncature($command->getInsurance() * getTVARateByCategory(TVA::TYPE_INSURANCE) / 100);
            } else {
                $fraisTTC = $fraisHT;
            }
            $ht += $fraisHT;
            $ttc += $fraisTTC;

            $this->_debug('* Calcul du prix de la commande ' .
                $command->getCommandNo());
            $this->_debug(' -> totalHT : ' . $ht);
            $this->_debug(' -> totalTTC: ' . $ttc);
            $command->setTotalPriceHT($ht);
            $command->setTotalPriceTTC($ttc);
        } else {
            if ($hbrPercent) {
                $command->setTotalPriceHT($command->getTotalPriceHT() - $hbrPercentAmountHT);
                $command->setTotalPriceTTC($command->getTotalPriceTTC() - $hbrPercentAmountTTC);
            }
        }
        if ($hbrPercent) {
            $command->setHandingByRangePercent($hbrPercent);
            $command->setHanding($command->getHanding() + $hbrPercent);
        }
        
        // Controle du MiniamountToOrder si cmde client produit et 'client' 
        // connecte: le total HT doit etre > seuil, si seuil parametre
        $auth = Auth::singleton();
        $customerProfiles = array(UserAccount::PROFILE_CUSTOMER,
                UserAccount::PROFILE_OWNER_CUSTOMER, 
                UserAccount::PROFILE_EXTERNAL_CUSTOMER);
        if ($this->productCommandType==Command::TYPE_CUSTOMER 
        && in_array($auth->getProfile(), $customerProfiles)) {
            $currency = $command->getCurrency();
            // Euro par defaut
            if (!($currency instanceof Currency)) {
                $curMapper = Mapper::singleton('Currency');
                $currency = $curMapper->load(array('ShortName' => 'EUR'));
            }
            $customer = $command->getCustomer();
            $mato = $customer->getMiniAmountToOrder($currency);
            if ($ht < $mato) {
                return new Exception(sprintf(
                    E_COMMAND_PTHT_LOWER_THAN_MINI_TO_ORDER, 
                    I18N::formatNumber($mato), 
                    $currency->getShortName()));
            }
        }

        if (!$this->isEstimate && $this->productCommandType == Command::TYPE_CUSTOMER) {
            $tp = $sc->getTermsOfPayment();
            if ($tp instanceof TermsOfPayment && $tp->hasPrePayment()) {
                $command->blockDeblock(1, false);
            }
        }

        $spc  = $command->getSupplierCustomer();
        $command->setTermsOfPayment($spc->getTermsOfPayment());

        if(( $_REQUEST['Instalment'] > 0) && $this->isRootCommand ) { 
            // On doit creer un nouvel accompte
	        $newInstalment = Object::load('Instalment');
            $InstalmentMapper = Mapper::singleton('Instalment');
            $InstalmentId = $InstalmentMapper->generateId();
            $newInstalment->setId($InstalmentId);
            $DocumentNo = GenerateDocumentNo('IN', 'Instalment', $InstalmentId);
            $newInstalment->setDocumentNo($DocumentNo);
            $newInstalment->setCommand($command);
            $newInstalment->setDate(date('Y-m-d H:i:s'));
            $newInstalment->setModality($_REQUEST['InstalmentModality']);
            $newInstalment->setTotalPriceTTC($_REQUEST['Instalment']);
            $newInstalment->save();
        }


         
        $command->save();

        // active les processus fils uniquement si ce n'est pas un devis
        // XXX commente pour l'instant: on ne peut retrouver les devis actives par un devis...
        if ((!$this->isEstimate) || Preferences::get('EstimateBehaviour', 0) == 1) {
            $result = $this->activateChildrenProcesses();
            if (Tools::isException($result)) {
                return $result;
            }
            // Si commande Fr dont le Fr a son OnlogisticsAccount non vide:
            // On va creer automatiqt une cmde client dans sa base
            // Pas si c'est un devis, qque soit la pref
            if ($ctype == 'fournisseur' && !$this->isEstimate) {
                $olaccount = $command->getExpeditor()->getOnlogisticsAccount();
                if (!is_null($olaccount) && $olaccount != '##') {
                    $externalCommand = $this->handleMirroringCommand($command);
                    if (Tools::isException($externalCommand)) {
                        return $externalCommand;
                    }
                }
            }
        }
        if ($debug) {
            return true; // On ne commite pas la Transaction
        }

        if ($this->useTransaction) {
            // commite la transaction
            if (Database::connection()->hasFailedTrans()) {
                trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
                Database::connection()->rollbackTrans();
                return new Exception(_('Database transaction failed.'));
            }
            Database::connection()->completeTrans();

        }
        
        // envoi du mail de récépissé, envoye en fait apres la transaction
        if ($this->commandType == 'ProductCommand') {
            $customer = $this->auth->getActor();
            $this->_debug('* Envoi du récépissé de la commande de produit ' .
                $command->getCommandNo());
            if ($this->isEstimate) {
                $docCls = 'Estimate';
            } else {
                $docCls = $this->productCommandType == Command::TYPE_CUSTOMER ? 
                    'CommandReceipt' : 'CommandReceiptSupplier';
            }
            $doc = new $docCls();
            $doc->setCommand($command);
            $doc->setCommandType($this->productCommandType);
	        $doc->setDocumentNo($command->getCommandNo());
            $doc->setSupplierCustomer($command->getSupplierCustomer());
            $doc->setCurrency($command->getCurrency());
	        $doc->setEditionDate(date('Y-m-d H:i:s'));
            if (($dmodel = $doc->findDocumentModel())) {
                $doc->setDocumentModel($dmodel);
            }
            $doc->save();
            $uacCol = new Collection();
            //on envoie l'alerte de reception de commande au commercial lié a la commande
            $commercial = $command->getCommercial();
            if ($commercial instanceof UserAccount && ($commercial->getEmail() != "")) {
                $uacCol->setItem($commercial);
            }
            CommandManager::$alertsToSend[] = array(
                'ALERT_PRODUCT_COMMAND_RECEIPT',
                array(
                    $doc, 
                    $uacCol, 
                    array(
                        $command->getDestinatorSiteId(),
                        $command->getExpeditorSiteId()
                    )
                )
            );
        } else if ($this->commandType == 'ChainCommand') {
            $additionnalUsers = new Collection();
            $additionnalUsers->setItem($this->auth->getUser());
            $this->_debug('* Envoi du récépissé de la commande de transport ' .
                $command->getCommandNo());

            CommandManager::$alertsToSend[] = array(
                'ALERT_CHAIN_COMMAND_RECEIPT',
                array(
                    $command, $command->getTotalPriceTTC(), $additionnalUsers
                )
            );
        }
        if (!$this->isEstimate) {
            // envoi des mails d'alerte de stock
            for ($i=0; $i < count($alerts); $i++) {
                $alertId = $alerts[$i][0];
                $pdt = $alerts[$i][1];
                $this->_debug('* Envoi alerte stock de la commande '.$command->getCommandNo());
                if (AlertSender::isStockAlert($alertId)) {
                    CommandManager::$alertsToSend[] = array(
                        'sendStockAlert',
                        array(
                            $alertId, $pdt, array(), $command
                        )
                    );
                }
            }
            // envoi des mails d'alerte encours
            if ($this->commandType == 'ProductCommand') {
                $this->_debug('* Gestion de l\'encours de la commande ' .
                    $command->getCommandNo());
                handleIncurUpdate($command, '€'); // XXX FIXME devise
            }
        }
        // Si la commande 'initiatrice de tout', on est hors transaction, on
        // envoit les mails
        if ($this->isRootCommand) {
            $alertSender = new AlertSender();
            foreach(CommandManager::$alertsToSend as $alert) {
                $sender = ($alert[0] == 'sendStockAlert')?
                    'sendStockAlert':'send_' . $alert[0];
                call_user_func_array(
                    array($alertSender, $sender),
                    $alert[1]
                );
            }
        }
        return true;
    }

    // }}}
    // CommandManager::validateChainCommand() {{{

    /**
     * Valide la commande de transport.
     *
     * @param boolean debug: si true, ne commite pas la transaction
     * @access public
     * @return mixed true ou une exception
     */
    public function validateChainCommand($ht=false, $commitTrans=true)
    { 
        $activatedChain = $this->_activatedChainArray[0];
        // calcul du prix
        try {
            $prices = $activatedChain->getChainCommandCost($this->command, $ht);
        } catch (Exception $exc) {
            return $exc;
        }
        $toPay = troncature($prices['ttc'] - $this->command->getTotalInstalments());
        $resultData = array();
        $resultData['ChainCommand_RawHT']         = $prices['raw_ht'];
        $resultData['ChainCommand_TotalPriceHT']  = $prices['ht'];
        $resultData['ChainCommand_TotalPriceTTC'] = $prices['ttc'];
        $resultData['ChainCommand_ToPay'] = $toPay;
        $resultData['ChainCommand_TVA'] = $prices['tva'];
        $resultData['Initialized'] = 1;
        $this->command->setTotalPriceHT($prices['ht']);
        $this->command->setTotalPriceTTC($prices['ttc']);
        $spc = findSupplierCustomer(
            $this->command->getExpeditor(),
            $this->command->getDestinator(),
            ($prices['ttc'] - $ht > 0)
        );
	$serial = $this->command->getCommandNo();
	if (trim($serial) == ''){
	    $this->command->generateCommandNo($this->command->getChain());
	}
	else {
	    $commandMapper = Mapper::singleton('Command');
	    if ($commandMapper->alreadyExists(array('CommandNo'=>$serial))) {
              return new Exception(E_COMMANDNO_EXISTS);
            }
	}
        $this->command->setSupplierCustomer($spc);
        $this->command->setTermsOfPayment($spc->getTermsOfPayment());
        $currency = Object::load('Currency', array('Id'=>1));
        $this->command->setCurrency($currency);

        // activation des éventuels processus/commandes fils
        // XXX commente pour l'instant: on ne peut retrouver les devis actives par un devis...
        if ((!$this->isEstimate) || Preferences::get('EstimateBehaviour', 0) == 1) {
            $result = $this->activateChildrenProcesses(
                $this->command,
                $activatedChain
            );
            if (Tools::isException($result)) {
                return $result;
            }
        }
        $this->command->save();
        if ($this->useTransaction && $commitTrans) {
            // commite la transaction
            if (Database::connection()->hasFailedTrans()) {
                trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
                Database::connection()->rollbackTrans();
                return new Exception(_('Database transaction failed.'));
            }
            Database::connection()->completeTrans();

        }
        // si c'est un devis, on a plus rien à faire ici
        if ($this->isEstimate || !$commitTrans) {
            return $resultData;
        }
        // envoi du mail de récépissé, envoye en fait apres la transaction
        $additionnalUsers = new Collection();
        $additionnalUsers->setItem($this->auth->getUser());
        $this->_debug('* Envoi du récépissé de la commande de transport ' .
            $this->command->getCommandNo());
        CommandManager::$alertsToSend[] = array(
            'ALERT_CHAIN_COMMAND_RECEIPT',
            array($this->command, $toPay, $additionnalUsers)
        );
        // Si la commande 'initiatrice de tout', on est hors transaction, on
        // envoit les mails
        if ($this->isRootCommand) {
            $alertSender = new AlertSender();
            foreach(CommandManager::$alertsToSend as $alert) {
                $sender = ($alert[0] == 'sendStockAlert')?
                    'sendStockAlert':'send_' . $alert[0];
                call_user_func_array(
                    array($alertSender, $sender),
                    $alert[1]
                );
            }
        }
        return $resultData;
    }

    // }}}
    // CommandManager::handleHandingByRange() {{{

    public function handleHandingByRange() {
    }

    // }}}
    // CommandManager::handleMirroringCommand() {{{
    /**
     * Gère l'enregistrement d'une commande via le webservice onlogistics
     * qui est le reflet de $this->command.
     * Cette commande possede le meme CommandNo.
     *
     * @param  integer l'id du destinataire de la commande
     * @return mixed boolean ou lève une exception
     * @throws Exception
     */
    public function handleMirroringCommand($command) {
        $cmdParams = array();
        $cmiParams = array();
        // Liste des Properties qui seront differentes:
        // Celles de type FK et qques autres
        $cmdProperties = ProductCommand::getProperties();
        $props = array();

        // Donnees pour lesquelles on ne peut faire transiter des ids, car ils
        // ne correspondraient pas aux meme donnees dans l'autre base
        $neededData = array(
            'Actor' => array_unique(array(
                    $command->getExpeditor()->getName(),
                    $command->getDestinator()->getName(),
                    $command->getCustomer()->getName(),
                    )),
            'Site' => array_unique(array(
                    $command->getExpeditorSite()->getName(),
                    $command->getDestinatorSite()->getName()
                    ))
        );
        // On va recuperer les donnees via rpc
        try {
            $realm = $command->getExpeditor()->getOnlogisticsAccount();
            $user = 'ecustomer@' . $realm;
            // Attention, password en dur ici (TODO??)
            $cli = new XmlRpcClient(
                ONLOGISTICS_API,
                array('user'=>$user, 'passwd'=>'koa6shaU')
            );  // , 'verbose'=>true

            $result = $cli->order__getDataForOrder($neededData);
            if (!$result) {
                return new Exception(E_NO_DATA_EXISTS_FOR_EXTERNAL_CMD);
            }
        } catch (Exception $exc) {
            return $exc;
        }

        foreach($cmdProperties as $prop=>$type) {
            if (is_string($type) || in_array($prop, array(/*'CommandNo',*/ 'Type'))) {
                continue;
            }
            $getter = 'get' . $prop;
            $cmdParams[$prop] = $command->$getter();
        }
        $cmdParams['Type'] = Command::TYPE_CUSTOMER;
        $cmdParams['Expeditor'] = $result['Actor'][$command->getExpeditor()->getName()];
        $cmdParams['Destinator'] = $result['Actor'][$command->getDestinator()->getName()];
        $cmdParams['ExpeditorSite'] = $result['Site'][$command->getExpeditorSite()->getName()];
        $cmdParams['DestinatorSite'] = $result['Site'][$command->getDestinatorSite()->getName()];
        $cmdParams['Customer'] = $result['Actor'][$command->getCustomer()->getName()];
        if ($cmdParams['WishedEndDate'] == 'NULL') {
            unset($cmdParams['WishedEndDate']);
        }

        // Liste des Properties qui seront differentes:
        $props = array('Command', 'ActivatedChain', 'Product', 'ActivatedMovement', 'TVA');
        // Pour les attributs qui seront identiques:
        $cmiProperties = array_diff(
                array_keys(ProductCommandItem::getProperties()), $props);

        $cmiCol = $command->getCommandItemCollection();
        $cmiCount = $cmiCol->getCount();
        for($i = 0; $i < $cmiCount; $i++) {
            $cmi = $cmiCol->getItem($i);
            $params = array();
            foreach($cmiProperties as $property) {
                $getter = 'get' . $property;
                $params[$property] = $cmi->$getter();
            }
            // Ici, le web service de cmde accepte les baseReference
            // On passe la reference fournisseur, si elle existe,
            // et sinon, la BaseReference
            $pdt = $cmi->getProduct();
            $supplierRef = $pdt->getReferenceByActor($command->getExpeditor());
            $params['Reference'] = ($supplierRef == '')?
                    $pdt->getBaseReference():$supplierRef;

            // $params['TVA'] facultatif, et on passe le rate si necessaire
            $tva = $cmi->getTVA();
            if ($tva instanceof TVA) {
                $params['TVA'] = $tva->getRate();
            }
            $cmiParams[] = $params;
        }

        try {
            $result = $cli->order__order($cmdParams, $cmiParams);
        } catch (Exception $exc) {
            return $exc;
        }
        if (is_string($result)) {
            return new Exception($result);
        }
        return $result;
    }

    // }}}
    // CommandManager::activateChildrenProcesses() {{{

    /**
     * Active les éventuels processus/commandes fils.
     *
     * @access public
     * @return void
     **/
    public function activateChildrenProcesses($cmd=false, $ach=false)
    {
        // A VIRER: avec les 2 paramètres qd refactoring chaincommand sera ok
        if ($cmd) {
            $this->command = $cmd;
        }
        // fin A VIRER

        $this->_debug('* Activation des process fils de la commande ' .  
            $this->command->getCommandNo());

        if ($ach) {
            $achArray = array($ach);
        } else {
            $achArray = $this->_activatedChainArray;
        }
        foreach($achArray as $ach) {
            if (!(
                ($ack = $ach->hasTaskOfType(TASK_ACTIVATION) ) || ($ack = $ach->hasTaskOfType(TASK_GENERIC_ACTIVATION) )
                )
            ) {
                continue;
            }

            $filterComponentArray[] = new FilterComponent(
                new FilterRule( 'Task.Id', FilterRule::OPERATOR_EQUALS, TASK_ACTIVATION),
                new FilterRule( 'Task.Id', FilterRule::OPERATOR_EQUALS, TASK_GENERIC_ACTIVATION),
                FilterComponent::OPERATOR_OR);
            $filter = SearchTools::filterAssembler($filterComponentArray);

            $ackCol = $ach->getActivatedChainTaskCollection($filter);
                
            $count = $ackCol->getCount();
            for($i = 0; $i < $count; $i++){
                $ack = $ackCol->getItem($i);
                $result = $this->_activateChildProcess($ack);
                if (Tools::isException($result)) {
                    $this->_debug($result->getMessage());
                    return $result;
                }
            }
        }
        return true;
    }

    // }}}
    // CommandManager::_activateChildProcess() {{{

    /**
     * Active la chaîne et crée la commande fille nécessaire si une tache
     * d'activation le demande
     * XXX FIXME: pour l'instant on passe explicitement la commande et la
     * chaîne, car les scripts de commande (ProductCommand.php et
     * ProductCommandSupplier.php n'utilisent pas encore ce système)
     *
     * @access private
     * @param object Command la commande en cours
     * @param object ActivatedChain la chaine activée de la commande
     * @return mixed true ou une exception
     **/
    private function _activateChildProcess($ack)
    {
        // chaine à activer
        $chainToActivate = $ack->getChainToActivate();
        // XXX FOR TEST ONLY
        if( (false == $chainToActivate) && ( $ack->getTaskId() != TASK_GENERIC_ACTIVATION ) ) {
            $task = $ack->getTask();
            return new Exception('Aucune chaîne à activer définie dans la ' .
                'tâche d\'activation');
        }
        // vérification de la validité de l'activation
        // cas cmde de produit active cmde de transport   -> OK
        // cas cmde de produit active cmde de produit     -> OK
        // cas cmde de transport active cmde de transport -> OK
        // cas cmde de transport active cmde de produit   -> KO
        if ( $ack->getCommandType() == false ||    
            ($ack->getCommandType() == 'ProductCommand' && get_class($this->command) == 'ChainCommand') ){
            return new Exception(E_ACTIVATE_CHILD_PROCESS);
        } 

        if ($ack->getActivationPerSupplier()) {
            // si on doit activer une commande par fournisseur, on appelle la
            // méthode adhoc
            return $this->_activateChildProcessByProduct($ack, $chainToActivate);
        }
        
        $manager = new CommandManager(array(
            'CommandType'        => $ack->getCommandType(),
            'ProductCommandType' => $ack->getProductCommandType(),
            'UseTransaction'     => false,
            'IsRootCommand'      => false,
            'IsEstimate'         => $this->isEstimate,
            'ParentCommand'      => $this->command
        ));
        $manager->_chainArray = array(array($chainToActivate, array()));
        // XXX FIXME cela changera cf note dans la doc de la méthode
        $commandParams = $manager->_getCommandParams($this->command, $ack);
        // creation de la commande fille


        $result = $manager->createCommand($commandParams);
        if (Tools::isException($result)) {
            return $result;
        }
        // ajout des items de commande
        // si la tache d'activation s'applique à tous ou partie des composants
        // de la nomenclature, la collection de commanditem n'est pas celle de
        // la commande
        $cpnCol = $ack->getComponentCollection();
        if ($cpnCol->getCount() > 0) {
            // si la tache d'activation s'applique à tous ou partie des
            // composants de la nomenclature, la collection de commanditem est
            // est construite à partie des composants et du ratio
            $ratio  = $ack->getComponentQuantityRatio();
            $cmiCol = $this->command->createCommandItemsFromComponents($cpnCol, $ratio);
        } else {
            // sinon c'est la collection de commanditems de la commande
            $cmiCol = $this->command->getCommandItemCollection();
        }

        $count = $cmiCol->getCount();
        for($i = 0; $i < $count; $i++){
            $cmi = $cmiCol->getItem($i);
            // XXX FIXME cela changera cf note dans la doc de la méthode
            $cmiParams = $manager->_getCommandItemParams($cmi, $ack);
            if (!$cmiParams) {
                continue;
            }
            $result = $manager->addCommandItem($cmiParams);
            if (Tools::isException($result)) {
                return $result;
            }
        }
        // activation et validation du processus
        $result = $manager->activateProcess($ack);
        if (Tools::isException($result)) {
            return $result;
        }
        $result = $manager->validateCommand();
        if (Tools::isException($result)) {
            return $result;
        }
        $this->_debug($manager->command->htmlDump());
        return true;
    }

    // }}}
    // CommandManager::_activateChildProcessByProduct() {{{

    /**
     * Active le processus correspondant pour chaque fournisseur de produit de
     * la commande.
     * Il faut donc construire un tableau:
     *      $supplier=>array($product1, ...))
     *
     * @access public
     * @return void
     **/
    public function _activateChildProcessByProduct($ack, $chainToActivate)
    {
        $cmiMapper = Mapper::singleton('CommandItem');
        $actMapper = Mapper::singleton('Actor');
        // la collection de composants (peut-être vide...)
        $cpnCol = $ack->getComponentCollection();
        $ratio  = $ack->getComponentQuantityRatio();
        $supplierCommandItemMap = $this->command->getSupplierCommandItemArray($cpnCol, $ratio);

        foreach($supplierCommandItemMap as $supplierID=>$cmiCol){

            $manager = new CommandManager(array(
                'CommandType'        => $ack->getCommandType(),
                'ProductCommandType' => $ack->getProductCommandType(),
                'UseTransaction'     => false,
                'IsRootCommand'      => false,
            ));
            $manager->productCommandType = $ack->getProductCommandType();

            $commandParams = $manager->_getCommandParams($this->command, $ack);
            // il faut changer les paramètres de l'expediteur/destinataire
            $supplier = $actMapper->load(array('Id'=>$supplierID));
            $supplierSite = $supplier->getMainSite();
            $commandParams['Expeditor'] = $supplier->getId();
            $commandParams['ExpeditorSite'] = $supplierSite->getId();
            $commandParams['ParentCommand'] = $this->command;

            // creation de la commande fille
            $result = $manager->createCommand($commandParams);
            if (Tools::isException($result)) {
                return $result;
            }
            // ajout des items de commande, ici on ajoute que ceux liés au
            // supplier du produit associé
            $cmiCol = $supplierCommandItemMap[$supplierID];
            $count = $cmiCol->getCount();
            $cmiChains = array();
            for($i = 0; $i < $count; $i++){
                $cmi = $cmiCol->getItem($i);
                $cmiProd = $cmi->getProduct();
                $ref = $cmiProd->getBaseReference() ;

                if($ack->getTaskId() == TASK_GENERIC_ACTIVATION) {
                    $cmiChain = Object::load('Chain', array('Reference'=>$ref)) ;
                    $manager->_chainArray = array(array($cmiChain, array()));
                }

                // XXX FIXME cela changera cf note dans la doc de la méthode
                $cmiParams = $manager->_getCommandItemParams($cmi, $ack);
                if (!$cmiParams) {
                    continue;
                }
                $result = $manager->addCommandItem($cmiParams);
                if (Tools::isException($result)) {
                    return $result;
                }

                if($ack->getTaskId()== TASK_GENERIC_ACTIVATION) {
                    if(!(Tools::IsException($cmiChain))) {
                        $result = $manager->activateProcess($cmiChain);
                        if (Tools::isException($result)) {
                            return $result;
                        }
                    }
                }
            }

            $manager->_chainArray = array(array($chainToActivate, array()));

            // activation et validation du processus
            if($ack->getTaskId()!= TASK_GENERIC_ACTIVATION) {
                $result = $manager->activateProcess($ack);
                if (Tools::isException($result)) {
                    return $result;
                }
            }
            $result = $manager->validateCommand();
            if (Tools::isException($result)) {
                return $result;
            }
            $this->_debug($manager->command->htmlDump());
        }
        return true;
    }

    // }}}
    // CommandManager::_getChainArray() {{{

    /**
     * Retourne la chaîne (le processus)
     *
     * @access public
     * @return object Chain
     **/
    public function _getChainArray()
    {
        if (false == $this->_chainArray) {
            // il faut chercher la chaine correspondante
            if ($this->commandType == 'ProductCommand') {
                // on récupère la collection de produits de la commande
                // afin de récupèrer la collection de chaines compatibles
                // avec les produits de la commande
                $cmiCol = $this->command->getCommandItemCollection();
                $pdtCol = new Collection();
                $count = $cmiCol->getCount();
                for($i = 0; $i < $count; $i++){
                    $cmi = $cmiCol->getItem($i);
                    $pdtCol->setItem($cmi->getProduct());
                }
                if (!Preferences::get('CommandActivateMultipleChains')) {
                    try {
                        $chainCol = getCommandChainCollection(
                            $pdtCol,
                            $this->command->getExpeditor(),
                            $this->command->getdestinator()
                        );
                    } catch (Exception $exc) {
                        throw $exc;
                    }
                    $chain = $chainCol->getItem(0);
                    if ($this->command->getCadenced()) {
                        // cas d'une commande cadencée on active une chaine par
                        // commanditem
                        $this->_chainArray = array();
                        $count = $pdtCol->getCount();
                        for ($i=0; $i<$count; $i++) {
                            $pdt = $pdtCol->getItem($i);
                            $this->_chainArray[] = array($chain, array($pdt->getId()));
                        }
                    } else {
                        $this->_chainArray = array(array($chain, array()));
                    }
                } else {
                    try {
                        $chainProductMap = getChainProductMap(
                            $pdtCol,
                            $this->command->getExpeditor(),
                            $this->command->getdestinator()
                        );
                    } catch (Exception $exc) {
                        throw $exc;
                    }
                    $this->_chainArray = array();
                    if ($this->command->getCadenced()) {
                        // cas d'une commande cadencée on active une chaine par
                        // commanditem
                        foreach ($chainProductMap as $chainID=>$pdtArray) {
                            $chain = Object::load('Chain', $chainID);
                            foreach ($pdtArray as $pdtID) {
                                $this->_chainArray[] = array($chain, array($pdtID));
                            }
                        }
                    } else {
                        foreach ($chainProductMap as $chainID=>$pdtArray) {
                            $chain = Object::load('Chain', $chainID);
                            $this->_chainArray[] = array($chain, $pdtArray);
                        }
                    }
                }
            }
        }
        return $this->_chainArray;
    }

    // }}}
    // CommandManager::_checkAndFormatCommandParams() {{{

    /**
     * Vérifie les paramètres requis, et formate les arguments $params, en fait
     * cela consiste à rendre le tableau $params compatible avec la commande.
     *
     * @access public
     * @param array $params le tableau des paramètres
     * @return void
     **/
    public function _checkAndFormatCommandParams($params)
    {
        if ($this->productCommandType != false) {
            $params['Type'] = $this->productCommandType;
        }elseif ($this->commandType == 'ChainCommand') {
            $params['Type'] = Command::TYPE_TRANSPORT;
        }elseif ($this->commandType == 'CourseCommand') {
            $params['Type'] = Command::TYPE_COURSE;
        }elseif ($this->commandType == 'PrestationCommand') {
            $params['Type'] = Command::TYPE_PRESTATION;
        }
        if ($this->commandType == 'ProductCommand') {
            $supplierCustomer = findSupplierCustomer(
                    $params['Expeditor'], $params['Destinator']);
            $params['SupplierCustomer'] = $supplierCustomer->getId();
        }
        // Si empty($params['CommandNo']), la creation du CommandNo
        // se fait dans validateCommand() (pour avoir acces a la Chain)
        if (!empty($params['CommandNo'])) {
            $cmdMapper = Mapper::singleton('Command');
            if ($cmdMapper->alreadyExists(array('CommandNo' => $params['CommandNo']))) {
                return new Exception(E_COMMANDNO_EXISTS);
            }
        }
        // XXX TODO
        return $params;
    }

    // }}}
    // CommandManager::_checkAndFormatCommandItemParams() {{{

    /**
     * Identique à la méthode _formatCommandParams, mais adaptée au commanditem
     *
     * @access public
     * @param array $params le tableau des paramètres
     * @return void
     **/
    public function _checkAndFormatCommandItemParams($params)
    {
        if (isset($params['Product'])) {
            $pdt = Object::load('Product', $params['Product']);
            $destinator = $this->command->getDestinator();
            $expeditor  = $this->command->getExpeditor();
            if (!isset($params['TVA'])) {
                // si suppliercustomer->hastva on l'applique
                $sc  = $this->command->getSupplierCustomer();
                if ($sc instanceof SupplierCustomer && $sc->getHasTVA() == true) {
                    $tva = $pdt->getTVAId();
                } else {
                    $tva = 0;
                }
                $params['TVA'] = $tva;
            }
            if ($this->productCommandType == Command::TYPE_CUSTOMER
                    && !isset($params['Promotion'])) {
                $params['Promotion'] = $pdt->getPromotion($destinator);
            }
            if (!isset($params['PriceHT'])) {
                if ($this->productCommandType == Command::TYPE_CUSTOMER) {
                    $pht = $pdt->getUnitHTForCustomerPriceWithDiscount($destinator);
                } elseif ($this->productCommandType == Command::TYPE_SUPPLIER) {
                    $apMapper = Mapper::singleton('ActorProduct');
                    $ap = $apMapper->load(
                            array('Actor'   => $expeditor->getId(),
                                  'Product' => $pdt->getId()));
                    $pht = 0;
                    if ($ap instanceof ActorProduct) {
                        $pht = $ap->getPriceByActor();
                    }
                }
                $params['PriceHT'] = $pht;
            }
        }
        // XXX TODO
        return $params;
    }

    // }}}
    // CommandManager::checkParams() {{{
    /**
     * Effectue les checks sur les aparms de creation de commande et reformate
     * au besoin: par exemple pour les float: I18N::extractNumber()
     *
     * @access public
     * @param array $commandData le tableau des paramètres pour la commande
     * @param array $array contient les controles
     * @param boolean $ignoreBaseArray permet d'ignorer $baseArray, et de tenir
     * seulement compte du second param: $array
     * @return array
     **/
    public function checkParams($commandData, $array=array(), $ignoreBaseArray=false) {
        $baseArray = array(
            'Destinator'      => array('req'=>true, 'type'=>'int'),
            'WishedStartDate' => array('req'=>true, 'type'=>'datetime'),
            'WishedEndDate'   => array('req'=>false, 'type'=>'datetime'),
            /* dans le CommandManager::createCommand() (date du jour)*/
            'CommandDate'     => array('req'=>false, 'type'=>'datetime'),
            'Port'            => array('req'=>false, 'type'=>'float'),
            'Insurance'       => array('req'=>false, 'type'=>'float'),
            'Packing'         => array('req'=>false, 'type'=>'float'),
            /* calculé par validateCommand() */
            'TotalPriceHT'    => array('req'=>false, 'type'=>'float'),
            /* calculé par validateCommand() */
            'TotalPriceTTC'   => array('req'=>false, 'type'=>'float'),

            'DestinatorSite' => array('req'=>true, 'type'=>'int'),
            'Expeditor' => array('req'=>true, 'type'=>'int'),
            'ExpeditorSite' => array('req'=>true, 'type'=>'int'),
            'Customer' => array('req'=>true, 'type'=>'int'),
            /* false au niveau api_site, celui t.q. 'Code'='FCA' par defaut */
            'Incoterm' => array('req'=>true, 'type'=>'int'),

            /* forcé dans validateCommand() via actor, si ProductCommand client */
            'CustomerRemExcep' => array('req'=>false, 'type'=>'float'),
            /* forcé dans validateCommand() via actor, si ProductCommand */
            'Currency' => array('req'=>false, 'type'=>'int'),
            'Commercial' => array('req'=>false, 'type'=>'int'),
            'Handing' => array('req'=>false, 'type'=>'float')
        );
        //  'Type': rempli via CommandManager::__construct()
        // 'ActorBankDetail' toujours forcé dans validateCommand() via actor.AccountingType
        // 'CommandNo' force si besoin dans validateCommand() pour acces au nom de la chaine
        // Cadenced: type boolean, non gere
        // Comment: type string: pas de controle a y faire

        // Les valeurs de $array ecrasent celles de $baseArray
        $array = $ignoreBaseArray?$array:array_merge($baseArray, $array);

        foreach($array as $field=>$rules) {
            if ($rules['req'] && !isset($commandData[$field])) {
                throw new Exception(
                    sprintf(_('array must contain the element "%s"'), $field)
                );
            }
            if (isset($commandData[$field]) && !empty($commandData[$field])) {
                if ($rules['type'] == 'int'
                && (!is_numeric($commandData[$field])
                    || (int)$commandData[$field] != ceil($commandData[$field]))) {
                    throw new Exception(
                        sprintf(_('"%s" must be an integer'), $field)
                    );
                }
                if ($rules['type'] == 'float') {
                    if (is_string($commandData[$field])) {
                        $commandData[$field] = I18N::extractNumber($commandData[$field]);
                    }
                    if (!is_numeric($commandData[$field])) {
                        throw new Exception(
                            sprintf(_('"%s" must be a float number'), $field)
                        );
                    }
                }
                if ($rules['type'] == 'datetime' && $commandData[$field] != 'NULL'
                && !preg_match('/^\d{4}-\d{2}-\d{2}.*$/', $commandData[$field])) {
                        throw new Exception(sprintf(
                            _('"%s" must be a date at the format AAAA-MM-JJ HH:MM:SS'),
                            $field)
                        );
                }
            }
        }
        return $commandData;
    }
    // }}}
    // CommandManager::_getCommandParams() {{{

    /**
     * Retourne les paramètres nécessaires pour une nouvelle commande créee par
     * une tâche d'activation
     *
     * @access public
     * @param object Command la commande initiale
     * @param object ActivatedChainTask la tache d'activation
     * @return array
     **/
    public function _getCommandParams($command, $ack)
    {
        $delta = $ack->getDelta() * 3600; // le delta est en heures
        // calcul de la date souhaitée
        switch($ack->getWishedDateType()){ // XXX WishedDateType
            case ActivatedChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X:
                // identique à la date de début de la tâche d'activation + x H
                $wsDate = DateTimeTools::MySQLDateToTimeStamp(
                    $ack->getBegin()) + $delta;
                $wsDate = DateTimeTools::timeStampToMySQLDate($wsDate);
                $weDate = 0;
                break;
            case ActivatedChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_MINUS_X:
                // identique à la date de début de la tâche d'activation + x H
                $wsDate = DateTimeTools::MySQLDateToTimeStamp(
                    $ack->getBegin()) - $delta;
                $wsDate = DateTimeTools::timeStampToMySQLDate($wsDate);
                $weDate = 0;
                break;
            case ActivatedChainTask::WISHED_START_DATE_TYPE_COMMAND_PLUS_X:
                // date souhaitée de la commande initiale + X heures
                $wsDate = DateTimeTools::MySQLDateToTimeStamp(
                    $command->getWishedStartDate()) + $delta;
                $wsDate = DateTimeTools::timeStampToMySQLDate($wsDate);

                if ($command->getWishedEndDate() > 0) {
                    $weDate = DateTimeTools::MySQLDateToTimeStamp(
                        $command->getWishedEndDate()) + $delta;
                    $weDate = DateTimeTools::timeStampToMySQLDate($weDate);
                } else {
                    $weDate = 0;
                }
                break;
            case ActivatedChainTask::WISHED_START_DATE_TYPE_COMMAND_MINUS_X:
                $wsDate = DateTimeTools::MySQLDateToTimeStamp(
                    $command->getWishedStartDate()) - $delta;
                $wsDate = DateTimeTools::timeStampToMySQLDate($wsDate);

                if ($command->getWishedEndDate() > 0) {
                    $weDate = DateTimeTools::MySQLDateToTimeStamp(
                        $command->getWishedEndDate()) - $delta;
                    $weDate = DateTimeTools::timeStampToMySQLDate($weDate);
                } else {
                    $weDate = 0;
                }
                break;
            default:
                // identique à la commande initiale
                $wsDate = $command->getWishedStartDate();
                $weDate = $command->getWishedEndDate();
        }
        // construction du tableau à passer à CommandManager
        $params = array(
            'Incoterm'=>$command->getIncotermId(),
            'WishedStartDate'=>$wsDate,
            'WishedEndDate'=>$weDate,
            'Expeditor'=>$ack->getDepartureActorId()?
                $ack->getDepartureActorId():$command->getExpeditorId(),
            'ExpeditorSite'=>$ack->getDepartureSiteId()?
                $ack->getDepartureSiteId():$command->getExpeditorSiteId(),
            'Destinator'=>$ack->getArrivalActorId()?
                $ack->getArrivalActorId():$command->getDestinatorId(),
            'DestinatorSite'=>$ack->getArrivalSiteId()?
                $ack->getArrivalSiteId():$command->getDestinatorSiteId(),
            'Customer'=>$command->getCustomerId(),
            'Commercial'=>$command->getCommercialId(),
            'Currency'=>$command->getCurrencyId() // pourra changer en fonction
        );
        return $params;
    }

    // }}}
    // CommandManager::_getCommandItemParams() {{{

    /**
     * Retourne les paramètres nécessaires pour un nouveau commanditem crée par
     * une tâche d'activation
     *
     * @access public
     * @param object CommandItem le commanditem initial
     * @param object ActivatedChainTask la tache d'activation
     * @return array
     **/
    public function _getCommandItemParams($cmi, $ack)
    {
        // construction du tableau à passer à CommandManager
        $params = array('Quantity' => $cmi->getQuantity());
        // XXX Fixme $ack->commandType rajouter CommandType dans CTK et ACK
        $initialCommand = $cmi->getCommand();

        // Commande Produit
        if ($ack->getCommandType() == 'ProductCommand') {
            $pdtCmdType = $this->productCommandType;
            // cas: tache d'activation d'une commande produit
            $params['Product'] = $cmi->getProductId();
            // calcul du prix
            $pdt = $cmi->getProduct();
            if ($ack->getActivationPerSupplier()) {
                // on prend le supplier
                if (!($act = $pdt->getMainSupplier())) {
                    return false;
                }
            } else {
                $method = $pdtCmdType == Command::TYPE_CUSTOMER?
                        'getDestinator':'getExpeditor';
                $act = $this->command->$method();
            }
            // Si commande fournisseur, on tient compte des qtes mini a commander
            // $cmi->getQuantity() doit etre un multiple de ActorProduct.BuyUnitQty
            if ($pdtCmdType == Command::TYPE_SUPPLIER) {
                // $act = expeditor dans ce cas
                $buyUnitQty = $pdt->getBuyUnitQuantity($act);
                $diff = $cmi->getQuantity() % $buyUnitQty;
                if ($diff  > 0) {
                    $qty = $cmi->getQuantity() - $diff + $buyUnitQty;
                    //$cmi->setQuantity($qty);
                    $params['Quantity'] = $qty;
                }
            }
        
            $qty = $cmi->getQuantity();

            // Si non fourni (peut etre fction d'une Handing (par ligne)
            // et d'une globalHanding
            if (!isset($params['PriceHT'])) {
                $pht = 0;
                if ($pdtCmdType == Command::TYPE_CUSTOMER) {
                    $pht = $pdt->getUnitHTForCustomerPriceWithDiscount($act);
                } elseif ($pdtCmdType == Command::TYPE_SUPPLIER) {
                    $apMapper = Mapper::singleton('ActorProduct');
                    $ap = $apMapper->load(
                            array('Priority' => 1,
                                  'Product' => $pdt->getId()));
                    if ($ap instanceof ActorProduct) {
                        $pht = $ap->getPriceByActor();
                    }
                }
                $params['PriceHT'] = troncature($pht);
            }

            // si suppliercustomer->hastva on l'applique
            $sc = $this->command->getSupplierCustomer();
            if ($sc instanceof SupplierCustomer && $sc->getHasTVA() == true) {
                $tva = $pdt->getTVAId();
            } else {
                $tva = 0;
            }
            $params['TVA'] = $tva;
            $params['Promotion'] = $pdt->getPromotion($act);
        }
        else {
            if (get_class($initialCommand) == 'ProductCommand') {
                // cas: tache d'activation d'une commande transport à partir
                // d'une commande de produit
                $product = $cmi->getProduct();
                $params['Width'] = $product->getSellUnitWidth();
                $params['Height'] = $product->getSellUnitHeight();
                $params['Length'] = $product->getSellUnitLength();
                $params['Weight'] = $product->getSellUnitWeight();
                $params['Gerbability'] = $product->getSellUnitGerbability();
                $params['MasterDimension'] = $product->getSellUnitMasterDimension();
                $params['ProductType'] = $product->getProductTypeId();
                $params['CoverType'] = 1;
            } else {
                // cas: tache d'activation d'une commande transport à partir
                // d'une commande de transport
                $params['Width'] = $cmi->getWidth();
                $params['Height'] = $cmi->getHeight();
                $params['Length'] = $cmi->getLength();
                $params['Weight'] = $cmi->getWeight();
                $params['Gerbability'] = $cmi->getGerbability();
                $params['MasterDimension'] = $cmi->getMasterDimension();
                $params['ProductType'] = $cmi->getProductTypeId();
                $params['CoverType'] = $cmi->getCoverTypeId();
            }
        }
        return $params;
    }

    // }}}
    // CommandManager::_debug() {{{

    /**
     *
     * @access public
     * @return void
     **/
    public function _debug($str)
    {
        if (DEBUG) {
            echo $str . '<br>';
        }
    }

    // }}}
}

// test {{{
/**
 * Ebauche de classe de test
 *
 **/
class CommandManagerTest {
    /**
     * Constructor
     * @access protected
     */
    function CommandManagerTest($cmdParamsArray=false, $cmdItemArray=false) {
        $auth = Auth::singleton();
        if (!$cmdParamsArray) {
            $cmdParamsArray = array(
                    'CommandNo' => 'test_Nooo',
                    'Incoterm' => 5,
                    'WishedStartDate' => '2006-05-10 10:00:00', /*
                    'WishedEndDate' => '', */
                    'Comment' => 'Ceci est un commentaire...',
                    'Expeditor' => 1,
                    'Destinator' => 121,
                    'Commercial' => 15000,
                    'Handing' => 10,
                    'Port' => 20,
                    'Packing' => 100,
                    'Insurance' => 30
            );
        }
        if (!isset($cmdParamsArray['Customer'])) {
            $cmdParamsArray['Customer'] = $auth->getActorId();
        }
        $this->cmdParamsArray = $cmdParamsArray;

        if (!$cmdItemArray) {
            $cmdItemArray = array(
                          383 => array('qty' => 1, 'hdg' => '2%', 'pht' => 10.23),
                          384 => array('qty' => 2, 'hdg' => 0, 'pht' => 11),
                          386 => array('qty' => 3, 'hdg' => 0, 'pht' => 6.02)
                          );
        }
        $this->cmdItemArray = $cmdItemArray;
    }

    /**
     *
     * @access public
     * @return void
     **/
    function test($cmdType='ProductCommand', $pdtCmdType=Command::TYPE_CUSTOMER) {
        require_once('Objects/Command.php');
        // La transaction innodb est initiee dans le constructeur
        $manager = new CommandManager(array(
            'CommandType'        => $cmdType,
            'ProductCommandType' => $pdtCmdType,
            'UseTransaction'     => true
        ));
        $result = $manager->createCommand($this->cmdParamsArray);
        echo 'createCommand were called...';
        if (Tools::isException($result)) {
            echo 'createCommand -> exception';
        }

        foreach($this->cmdItemArray as $pdtId => $pdtDatas) {
            // le product
            $pdtMapper = Mapper::singleton('Product');
            $pdt = $pdtMapper->load(array('Id' => $pdtId));
            if (!($pdt instanceof Product)) {
                continue;  // huh... la bdd est désyncronisée avec le client
            }
            // Tableau des paramètres; note: Pour PriceHT, promo et tva:
            // geres par CommandManager::_getCommandItemParams()
            $cmiParams = array('Product' => $pdtId,
                               'Quantity' => $pdtDatas['qty'],
                               'Handing' => $pdtDatas['hdg'],
                               'PriceHT' => $pdtDatas['pht']);
            $manager->addCommandItem($cmiParams);
        }
        echo 'addCommandItem were called...';
        $ach = $manager->activateProcess();
        if (Tools::isException($ach)) {
            return new XML_RPC_Response($this->encode($ach->getMessage()));
        }
        echo 'activateProcess was called...';

        // On ne commite pas la transaction
        $result = $manager->validateCommand(true);
        echo 'saveAll: validateCommand was called...';
        if (Tools::isException($result)) {
            //return new XML_RPC_Response($this->encode($result->getMessage()));
            echo 'validateCommand-> exception ' . $result->getMessage();
        }
    }

}
// }}}

?>
