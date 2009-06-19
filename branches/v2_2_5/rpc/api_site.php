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

define('SKIP_CONNECTION', true);

require_once('../config.inc.php');

// ne planter pour de vilaines notices
error_reporting(E_ALL ^ E_NOTICE);

// error messages {{{

// il faut repositionner la locale avant de définir les constantes des
// messages d'erreur, le config.inc.php ne la positionne pas avec la bonne
// valeur.
if(isset($_SESSION['locale'])) {
    I18N::setLocale($_SESSION['locale']);
}

define('ERROR_NO_101', _('Error 101: User was not found in the database with given email/password.'));
define('ERROR_NO_102', _('Error 102: Incorrect parameter passed to the webservice to register user (%s).'));
define('ERROR_NO_103', _('Error 103: No match found with the triplet %s.'));
define('ERROR_NO_104', _('Error 104: Actor (Id=%d) was not found in the database.'));
define('ERROR_NO_105', _('Error 105: Actor (%s) has not user account.'));
define('ERROR_NO_106', _('Error 106: Error while saving data.'));
define('ERROR_NO_107', _('Error 107: Incorrect parameter passed to the webservice for order informations (%s).'));
define('ERROR_NO_108', _('Error 108: Incorrect parameter passed to the webservice for ordered products informations (%s).'));
define('ERROR_NO_109', _('Error 109: product "%s" was not found in the database, as a consequence, cannot be ordered.'));
define('ERROR_NO_110', _('Error 110: Database error (%s).'));
define('ERROR_NO_111', _('Error 111: Order addressee site was not found in the database.'));
define('ERROR_NO_112', _('Error 112: No site was found in the database for actor with identifier %d.'));
define('ERROR_NO_113', _('Error 113: Order addressee was not found in the database.'));
// }}}

/**
 * Serveur XML-RPC pour le zaurus
 *
 */
class APISiteServer extends XmlRpcServer {
    // APISiteServer::__construct() {{{

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct() {
        parent::__construct();
        $this->registerMethod('auth.loginUser');
        $this->registerMethod('auth.registerUser');
        $this->registerMethod('database.getProductCSVData');
        $this->registerMethod('database.getCategoryCSVData');
        $this->registerMethod('database.getTVACSVData');
        $this->registerMethod('database.getImageCSVData');
        $this->registerMethod('database.getProductTypeCSVData');
        $this->registerMethod('order.order');
        $this->registerMethod('order.checkActors');
        $this->registerMethod('order.getDataForOrder');
        //$this->session = false;
    }

    // }}}
    // APISiteServer::loginUser() {{{

    /**
     * Authentifie un acteur existant provenant du site web. Retourne une
     * chaîne avec le message d'erreur en cas d'echec, un tableau contenant les
     * infos de l'acteur si en cas de succès.
     *
     * Structure du tableau retourné :
     * <code>
     * array(
     *    'Id'      => int ,
     *    'Name'    => string,
     *    'Email'   => string,
     *    'Quality' => string,
     *    'Sites' => array(
     *      0 => array(
     *        'Id'           => int,
     *        'StreetNumber' => string,
     *        'StreetType'   => int,
     *        'StreetName'   => string,
     *        'ZipCode'      => string,
     *        'City'         => string,
     *        'Country'      => int)
     *      ),
     *      1 => array(
     *        'Id'           => int,
     *        'StreetNumber' => string,
     *        [...]
     *      )
     * );
     * </code>
     *
     * @rpc    auth.loginUser()
     * @access public
     * @param  string $user le nom d'utilisateur
     * @param  string $passwd le mot de passe (encodé en sha1)
     * @return mixed array ou string contenant le message d'erreur
     */
    protected function loginUser($method, $params) {
        $this->log('auth.loginUser() called');
        list($login, $passwd) = $params;
        // check de l'acteur en bdd
        $this->auth();
        $userAccountMapper = Mapper::singleton('UserAccount');
        $userAccount = $userAccountMapper->load(
            array('Email'=>$login, 'Password'=>$passwd));
        if(!($userAccount instanceof UserAccount)) {
            return ERROR_NO_101;
        } else {
            $this->log('loginUser : ' . $userAccount->getIdentity);
        }
        $actor = $userAccount->getActor();
        $sitesCollection = $actor->getSiteCollection();
        // Utilise pour rechercher le seuil mini de commande autorise
        // Pour ol site, pas de gestion de la devise, euros par defaut!
        $curMapper = Mapper::singleton('Currency');
        $currency = $curMapper->load(array('ShortName' => 'EUR'));

        $return = array(
            'Id'       => $actor->getId(),
            'Name'     => $actor->getName(),
            'Email'    => $userAccount->getEmail(),
            'Quality'  => $actor->getQuality(),
            'Sites'  => $this->_getSitesArray($sitesCollection),
            'MiniAmountToOrder' => $actor->getMiniAmountToOrder($currency));
        return $return;
    }

    // }}}
    // APISiteServer::registerUser() {{{

    /**
     * Enregistre un acteur provenant du site web et retourne un tableau avec
     * les infos de l'acteur ou false en fonction de la réussite de
     * l'enregistrement.
     *
     * Format du tableau des données de l'acteur (valable pour les paramètres
     * et pour la valeur de retour):
     * <code>
     * array(
     *   'Id'       => 2,                            // int, pour màj
     *   'Name'     => 'robert dupond',              // string, obligatoire
     *   'Email'    => 'robert.dupond@quasimodo.fr', // string, obligatoire
     *   'Quality'  => 1,                            // int
     *   'Password' => 'catapulte',                  // string, obligatoire
     *   'Sites'  => array(
     *      0 => array(
     *          'StreetNumber' => 12,                   // string
     *          'StreetType'   => 3,                    // int
     *          'StreetName'   => 'de la république',   // string, obligatoire
     *          'StreetAddons' => 'apartement 5b',      // string
     *          'ZipCode'      => '59000',              // string, obligatoire
     *          'City'         => 'Lille',              // string, obligatoire
     *          'Country'      => 'France'),             // string, obligatoire
     *      1 => array()
     * );
     * </code>
     *
     * Le tableau des sites à le format suivant:
     * <code>
     * array(
     *     'Id'       => int,     // id du site pour mode édition
     *     'Name'     => string,  // nom du site, obligatoire en création
     *     'Email'    => string,
     *     'Fax'      => string,
     *     'Phone'    => string,
     *     'Mobile'   => string,
     *     'PreferedCommunicationMode' => int,
     *     'StreetNumber' => string,
     *     'StreetType'   => int,
     *     'StreetName'   => string, // obligatoire en création
     *     'StreetAddons' => string,
     *     'ZipCode'      => string, // string, obligatoire
     *     'City'         => string, // string, obligatoire
     *     'Country'      => string, // string, obligatoire
     *     'Cedex'        => string,
     *     'GPS'          => string,
     *     'Type'         => int
     * );
     * </code>
     *
     * @rpc    auth.registerUser()
     * @access public
     * @param  array $actorData un tableau de données sur l'acteur
     * @return boolean
     */
    protected function registerUser($method, $params) {
        $this->auth();
        $this->log('auth.registerUser() called');
        $actorData = $params[0];
        $return = false;
        // récupération de la devise, toujours euro
        $currency = Object::load('Currency', 1);
        if(!($currency instanceof Currency)) {
            $this->log(I18N::getLocaleCode());
            return sprintf(ERROR_NO_110, _('Currency "euro" was not found in database'));
        }
        // enregistrement
        if(isset($actorData['Id']) && $actorData['Id']>0) {
            // édition d'un acteur, check des données.
            try {
                $actorData = $this->_checkParams(
                    $actorData, array(
                        'Id'       => array('req'=>true, 'type'=>'int'),
                        'Email'    => array('req'=>false, 'type'=>'string'),
                        'Password' => array('req'=>false, 'type'=>'string'),
                        'Quality'  => array('req'=>false, 'type'=>'int'),
                        'Sites'    => array('req'=>false, 'type'=>'array')));
            } catch(Exception $exc) {
                return sprintf(ERROR_NO_102, $exc->getMessage());
            }
            $actor = Object::load('Customer', $actorData['Id']);
            if(!($actor instanceof Customer)) {
                return sprintf(ERROR_NO_104, $actorData['Id']);
            }

            $userAccountMapper = Mapper::singleton('UserAccount');
            $userAccount = $userAccountMapper->load(
                array(
                    'Actor'=>$actor->getId(),
                    'Identity'=>$actor->getName()));
            if(!($userAccount instanceof UserAccount)) {
                return sprintf(ERROR_NO_105, $actor->getName());
            }
        } else {
            // création d'un acteur, check des données
            try {
                $actorData = $this->_checkParams(
                    $actorData,
                    array(
                        'Name'     => array('req'=>true, 'type'=>'string'),
                        'Email'    => array('req'=>true, 'type'=>'string'),
                        'Password' => array('req'=>true, 'type'=>'string'),
                        'Quality'  => array('req'=>false, 'type'=>'int'),
                        'Sites'    => array('req'=>true, 'type'=>'array')));
            } catch(Exception $exc) {
                return sprintf(ERROR_NO_102, $exc->getMessage());
            }
            $actor = Object::load('Customer');
            $userAccount = Object::load('UserAccount');
        }

        Database::connection()->startTrans();

        // création de l'acteur
        if(isset($actorData['Name'])) {
            $actor->setName($actorData['Name']);
        }
        if(isset($actorData['Quality'])) {
            $actor->setQuality($actorData['Quality']);
        }
        // ajout des données paramérable de l'acteur
        $actor->setGenericActor(Preferences::get('WSActorGenericActor'));
        $actor->setCommercial(Preferences::get('WSActorCommercial'));
        $actor->setAccountingType(Preferences::get('WSActorAccountingType'));
        $actor->setCategory(Preferences::get('WSActorCategory'));
        // ajout des données non paramérable de l'acteur
        $actor->setCurrency($currency);
        $actor->save();

        // création du userAccount
        if(isset($actorData['Email'])) {
            $userAccount->setLogin($actorData['Email']);
            $userAccount->setEmail($actorData['Email']);
        }
        if(isset($actorData['Password'])) {
            $userAccount->setPassword($actorData['Password']);
        }
        if(isset($actorData['Name'])) {
            $userAccount->setIdentity($actorData['Name']);
        }
        // ajout des données non paramérable du userAccount
        $userAccount->setActor($actor);
        $userAccount->setProfile(UserAccount::PROFILE_EXTERNAL_CUSTOMER);
        $userAccount->save();

        // création des sites
        foreach ($actorData['Sites'] as $data) {
            try {
                $site = $this->_addSite($actor, $data);
            } catch(Exception $exc) {
                return $exc->getMessage();
            }
            unset($site);
        }

        // Sauvegardes des sites
        $sitesCollection = $actor->getSiteCollection();
        if($sitesCollection->getCount()>0) {
            $actor->setSiteCollection($sitesCollection);
            $actor->setMainSite($sitesCollection->getItem(0));
            $actor->save();
        }

        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
            Database::connection()->rollbackTrans();
            return ERROR_NO_106;
        }
        Database::connection()->completeTrans();

        $return = array(
            'Id'       => $actor->getId(),
            'Name'     => $actor->getName(),
            'Email'    => $userAccount->getEmail(),
            'Quality'  => $actor->getQuality(),
            'Sites'  => $this->_getSitesArray($sitesCollection),
            'MiniAmountToOrder' => $actor->getMiniAmountToOrder($currency));
        return $return;
    }

    // }}}
    // APISiteServer::getProductCSVData() {{{

    /**
     * Retourne le fichier csv de la base produit onlogistics, le format
     * retourné est une chaîne de caractères csv avec les colonnes suivantes:
     *     - Id
     *     - BaseReference
     *     - Name
     *     - Description
     *     - SellUnitQuantity
     *     - Category
     *     - TVA
     *     - Image
     *     - UnitHT
     *     - DiscountHT
     *     - DiscountComment
     *     - IsInPromotion
     *
     * @rpc    database.getProductCSVData()
     * @param  string $lastUpdate datetime (au format mysql) du dernier
     *         update de la bdd
     * @access public
     * @return string
     */
    protected function getProductCSVData($method, $params) {
        $this->auth();
        $lastUpdate = $params[0];
        $filter = new FilterComponent(
            new FilterRule(
                'LastModified',
                FilterRule::OPERATOR_IS_NULL
            ),
            FilterComponent::OPERATOR_OR,
            new FilterRule(
                'LastModified',
                FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                $lastUpdate
            )
        );
        $mapper = Mapper::singleton('Product');
        $col = $mapper->loadCollection($filter);
        $cnt = $col->getCount();
        $csv = "Id;BaseReference;Name;SellUnitQuantity;Category;TVA;Image;"
             . "UnitPriceHT;Discount;DiscountComment;IsInPromotion\n";
        for ($i=0; $i<$cnt; $i++) {
            $pdt = $col->getItem($i);
            $pbc = Object::load('PriceByCurrency',
                array('Product'=>$pdt->getId(), 'Currency'=>1));
            $priceHT = $pbc instanceof PriceBycurrency?$pbc->getPrice():0;
            $discount = $pdt->getDiscount()=='N/A'?0:$pdt->getDiscount();
            $dComment = $pdt->getDiscountComment()=='N/A'?
                '':$pdt->getDiscountComment();
            $isInPromo = $pdt->getIsInPromotion()=='N/A'?
                0:$pdt->getIsInPromotion();
            $csv .= sprintf(
                "%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n",
                $pdt->getId(), $pdt->getBaseReference(), $pdt->getName(),
                $pdt->getSellUnitQuantity(), $pdt->getCategoryId(),
                $pdt->getTVAId(), $pdt->getImageId(), $priceHT,
                $discount, $dComment, $isInPromo
            );
        }
        return $csv;
    }

    // }}}
    // APISiteServer::getCategoryCSVData() {{{

    /**
     * Retourne le fichier csv de la base categories onlogistics, le format
     * retourné est une chaîne de caractères csv avec les colonnes suivantes:
     *     - Id
     *     - Name
     *     - Description
     *
     * @rpc    database.getCategoryCSVData()
     * @param  string $lastUpdate datetime (au format mysql) du dernier
     *         update de la bdd
     * @access public
     * @return string
     */
    protected function getCategoryCSVData($method, $params) {
        $this->auth();
        $lastUpdate = $params[0];
        $fields = array('_Id', '_Name', '_Description');
        return $this->_getCSVData('Category', $fields, $lastUpdate);
    }

    // }}}
    // APISiteServer::getTVACSVData() {{{

    /**
     * Retourne le fichier csv de la base tva onlogistics, le format
     * retourné est une chaîne de caractères csv avec les colonnes suivantes:
     *     - Id
     *     - Rate
     *
     * @rpc    database.getTVACSVData()
     * @param  string $lastUpdate datetime (au format mysql) du dernier
     *         update de la bdd
     * @access public
     * @return string
     */
    protected function getTVACSVData($method, $params) {
        $this->auth();
        $lastUpdate = $params[0];
        $fields = array('_Id', '_Rate');
        return $this->_getCSVData('TVA', $fields, $lastUpdate);
    }

    // }}}
    // APISiteServer::getImageCSVData() {{{

    /**
     * Retourne le fichier csv de la base images onlogistics, le format
     * retourné est une chaîne de caractères csv avec les colonnes suivantes:
     *     - Id
     *     - UUID
     *     - Extension
     *     - Data
     *
     * @rpc    database.getImageCSVData()
     * @param  string $lastUpdate datetime (au format mysql) du dernier
     *         update de la bdd
     * @access public
     * @return string
     */
    protected function getImageCSVData($method, $params) {
        $this->auth();
        $lastUpdate = $params[0];
        $fields = array('_Id', '_UUID', '_Extension', '_Data');
        return $this->_getCSVData('Image', $fields, $lastUpdate);
    }

    // }}}
    // APISiteServer::getProductTypeCSVData() {{{

    /**
     * Retourne le fichier csv de la table ProductType onlogistics, le format
     * retourné est une chaîne de caractères csv avec les colonnes suivantes:
     *     - Id
     *     - DBId
     *     - Name
     *
     * @rpc    database.getProductTypeCSVData()
     * @param  string $lastUpdate datetime (au format mysql) du dernier
     *         update de la bdd
     * @access public
     * @return string
     */
    protected function getProductTypeCSVData($method, $params) {
        $this->auth();
        $lastUpdate = $params[0];
        $fields = array('_Id', '_DBId', '_Name');
        return $this->_getCSVData('ProductType', $fields, $lastUpdate);
    }

    // }}}
    // APISiteServer::order() {{{

    /**
     * Valide une commande client de produit provenant des sites web et
     * retourne true ou un message d'erreur.
     *
     * Tableau des infos de la commande:
     * ================================
     * array(
     *     'Destinator'      => 3,                     // ID du client
     *     'WishedStartDate' => '2006-09-25 10:00:00', // date souhaitée (début)
     *     'WishedEndDate'   => '2006-09-25 10:00:00', // date souhaitée (fin)
     *     'Comment'         => 'Un commentaire...',   // commentaire saisi
     *     'Port'            => 3.20,                  // frais de port
     *     'Insurance'       => 3.20,                  // frais d'assurances
     *     'Packing'         => 3.20,                  // frais d'emballage
     *     'TotalPriceHT'    => 100.50,                // total HT
     *     'TotalPriceTTC'   => 120.50                 // total TTC
     * )
     *
     * Tableau des infos des items de la commande:
     * ==========================================
     * Pour l'identifiant du Product, on utilise :
     *  soit 'Reference' => [une string: Product.BaseReference]
     *  soit 'Product' => [un integer: Product.Id]
     * array(
     *     array(
     *         'Reference' => 'A123', // référence du produit commandé
     *         'Quantity'  => 3,      // la quantité commandée
     *         'TVA'       => 19.6,   // le taux de tva
     *         'PriceHT'   => 32.2,   // le prix ht
     *         'Discount'  => 3,      // la remise ht
     *     ),
     *     array(
     *         'Reference' => 'A124', // référence du produit commandé
     *         'Quantity'  => 1,      // la quantité commandée
     *         'TVA'       => 5.5,    // le taux de tva
     *         'PriceHT'   => 23.12,  // le prix ht
     *         'Discount'  => 0,      // la remise ht
     *     ),
     *     etc... autant de tableaux que d'items de commande
     * );
     *
     * @rpc    order.order()
     * @access public
     * @param  array $commandData tableau des infos de la commande
     * @param  array $commandItemData tableau des items de la commande
     * @param  array $paymentDone booléen qui renseigne sur l'état du règlement
     * @return mixed true or string
     */
    protected function order($method, $params, $paymentDone=false) {
        $this->auth();
        $this->log('order.order() called');
        $commandData = $params[0];

        // check que le destinator existe
        $actor = Object::load('Customer', $commandData['Destinator']);
        if(!($actor instanceof Customer)) {
            return ERROR_NO_113;
        }
        // check pour le site destinataire
        if(!isset($commandData['DestinatorSite'])) {
            $destSite = $actor->getMainSite();
            if(!($destSite instanceof Site)) {
                return ERROR_NO_111;
            }
            $commandData['DestinatorSite'] = $destSite->getId();
        } else {
            $destSite = Object::load('Site', $commandData['DestinatorSite']);
            if(!($destSite instanceof Site) || $destSite->getOwnerId() != $commandData['Destinator']) {
                return ERROR_NO_111;
            }
            $commandData['DestinatorSite'] = $destSite->getId();
        }
        // check pour l'expediteur et le site expediteur
        $exp = $expSite = false;
        if(isset($commandData['Expeditor'])) {
            $exp = Object::load('Actor', $commandData['Expeditor']);
        }
        if(!($exp instanceof Actor)) {
            $exp = Object::load('Actor', array('DatabaseOwner'=>1));
        }
        if(isset($commandData['ExpeditorSite'])) {
            $expSite = Object::load('Site', $commandData['ExpeditorSite']);
        } else {
            $expSite = $exp->getMainSite();
        }
        if(!($expSite instanceof Site) || $expSite->getOwnerId() != $exp->getId()) {
            return ERROR_NO_111;
        }
        $commandData['Expeditor'] = $exp->getId();
        $commandData['ExpeditorSite'] = $expSite->getId();

        // valeurs par défaut
        $commandData['Customer'] = $commandData['Destinator'];
        $incoterm = Object::load('Incoterm', array('Code'=>'FCA'));
        if(!($incoterm instanceof Incoterm)) {
            return sprintf(ERROR_NO_110,
                _('Incoterm "FCA" was not found in the database'));
        }
        $commandData['Incoterm'] = $incoterm->getId();
        require_once('CommandManager.php');
        // Cheks sur $commandData
        try {
            $commandData = CommandManager::checkParams(
                $commandData,
                array(
                    'TotalPriceHT'  => array('req'=>true, 'type'=>'float'),
                    'TotalPriceTTC' => array('req'=>true, 'type'=>'float')
                ));
        } catch (Exception $exc) {
            return sprintf(ERROR_NO_107, $exc->getMessage());
        }

        require_once('Objects/Command.php');
        // La transaction innodb est initiee dans le constructeur
        $manager = new CommandManager(array(
            'CommandType'        => 'ProductCommand',
            'ProductCommandType' => Command::TYPE_CUSTOMER,
            'UseTransaction'     => true
        ));
        $result = $manager->createCommand($commandData);
        if (Tools::isException($result)) {
            return $result->getMessage();
        }
        $commandItemData = $params[1];
        foreach($commandItemData as $data) {
            try {
                $data = CommandManager::checkParams(
                    $data,
                    array(
                        'Product'  => array('req'=>false, 'type'=>'int'),
                        'Quantity' => array('req'=>true, 'type'=>'float'),
                        'Discount' => array('req'=>false, 'type'=>'float'),
                        'TVA'      => array('req'=>false, 'type'=>'float'),
                        'PriceHT'  => array('req'=>true, 'type'=>'float')),
                    true
                    );
            } catch (Exception $exc) {
                return sprintf(ERROR_NO_108, $exc->getMessage());
            }
            if (isset($data['TVA']) && $data['TVA'] > 0) {
                // formatage de la tva
                $tva = Object::load('TVA', array('Rate'=>$data['TVA']));
                if (!($tva instanceof TVA)) {
                    // XXX à faire en mieux code d'erreur, i18n et tutti quanti
                    return sprintf('Erreur: taux de TVA "%s" non géré', $data['TVA']);
                }
                $data['TVA'] = $tva->getId();
            }
            // le product
            $pdt = false;
            if(isset($data['Product'])) {
                $pdt = Object::load('Product', array('Id'=>$data['Product']));
            } elseif(isset($data['Reference'])) {
                $pdt = Object::load('Product', array('BaseReference'=>$data['Reference']));
            } else {
                $msg = _('array must contain "Product" or "Reference".');
                return sprintf(ERROR_NO_108, $msg);
            }
            if (!($pdt instanceof Product)) {
                // les bases de données ne sont pas synchro: message d'erreur
                return sprintf(
                    ERROR_NO_109,
                    isset($data['Reference']) ? $data['Reference'] : $data['Product']
                );
            }
            $data['Product'] = $pdt->getId();
            $result = $manager->addCommandItem($data);
            if (Tools::isException($result)) {
                return $result->getMessage();
            }
        }
        // activation du processus direct de la commande
        $result = $manager->activateProcess();
        if (Tools::isException($result)) {
            return $result->getMessage();
        }
        // valide la commande
        $result = $manager->validateCommand();
        if (Tools::isException($result)) {
            return $result->getMessage();
        }
        if ($paymentDone) {
            // XXX TODO gérer les choses à faire si le paiement a été effectué
        }
        return true;
    }

    // }}}
    // APISiteServer::_getCSVData() {{{

    /**
     * Retourne une chaîne csv à partir d'un nom d'entité et d'une liste de
     * colonnes à récupérer en base de données. La méthode ne retourne que les
     * enregistrements dont la dernière modification est supérieure ou égale à
     * la date passée en paramètre.
     *
     * @access private
     * @param  string $entity
     * @param  array  $fields
     * @param  string $date
     * @return string
     */
    private function _getCSVData($entity, $fields, $date, $sql=false) {
        if (!$sql) {
            require_once('Objects/' . $entity . '.php');
            $tbname = call_user_func(array($entity, 'getTableName'));
            $sql = sprintf(
                'SELECT %s FROM %s WHERE _LastModified IS NULL OR '
                . '_LastModified >= \'%s\'',
                implode(', ', $fields), $tbname, $date
            );
        }
        $rs = Database::connection()->execute($sql);
        $csvData = implode(';',
            array_map(
                create_function('$a', 'return substr($a, 1);'),
                $fields
            )
        ) . "\n";
        while($rs && !$rs->EOF) {
            $fsep = '';
            for($i=0; $i<count($fields); $i++) {
                $csvData .= $fsep . $rs->fields[$fields[$i]];
                $fsep = ';';
            }
            $rs->moveNext();
            if ($rs->EOF) {
                break;
            }
            $csvData .= "\n";
        }
        return $csvData;
    }

    // }}}
    // APISiteServer::_checkParams() {{{

    /**
     * Vérifie la validité du tableau et reformate éventuellement les données
     * selon le type, et reformate au besoin (pour le type float)
     *
     * @access private
     * @param  array $array
     * @param  array $fields
     * @return array
     */
    private function _checkParams($array, $fields) {
        foreach($fields as $field=>$rules) {
            if ($rules['req'] && !isset($array[$field])) {
                throw new Exception(
                    sprintf(_('array must contain the element "%s"'), $field)
                );
            }
            if (isset($array[$field]) && !empty($array[$field])) {
                if ($rules['type'] == 'int' && (!is_numeric($array[$field]) || (int)$array[$field] != ceil($array[$field]))) {
                    throw new Exception(
                        sprintf(_('"%s" must be an integer'), $field)
                    );
                }
                if ($rules['type'] == 'float') {
                    if (is_string($array[$field])) {
                        $array[$field] = I18N::extractNumber($array[$field]);
                    }
                    if (!is_numeric($array[$field])) {
                        throw new Exception(
                            sprintf(_('"%s" must be a float number'), $field)
                        );
                    }
                }
                if ($rules['type'] == 'datetime'  &&
                    !preg_match('/^\d{4}-\d{2}-\d{2}.*$/', $array[$field])) {
                        throw new Exception(sprintf(
                            _('"%s" must be a date at the format AAAA-MM-JJ HH:MM:SS'),
                            $field)
                        );
                }

            }
        }
        return $array;
    }

    // }}}
    // APISiteServer::_addSite() {{{

    /**
     * Ajoute ou modifie un site à/de l'acteur.
     *
     * @param Actor $actor Acteur
     * @param array $data cf APISiteServer::registerUser() format du tableau des
     * sites
     * @access private
     * @return Site
     */
    private function _addSite($actor, $data) {
        require_once('RPCTools.php');
        $sitesCollection = $actor->getSiteCollection();
        if(isset($data['Id']) && $data['Id']>0) {
            // édition d'un site, check des données
            try {
                $data = $this->_checkParams(
                    $data, array(
                        'Id'      => array('req'=>true, 'type'=>'int'),
                        'ZipCode' => array('req'=>true, 'type'=>'string'),
                        'City'    => array('req'=>true, 'type'=>'string'),
                        'Country' => array('req'=>true, 'type'=>'string')));
            } catch (Exception $exc) {
                throw new Exception(
                    sprintf(ERROR_NO_107, $exc->getMessage())
                );
            }
            $site = $sitesCollection->getItemById($data['Id']);
            if(!($site instanceof Site)) {
                throw new Exception(
                    sprintf(ERROR_NO_112, $data['Id'])
                );
            }
        } else {
            // création d'un site, check des données
            try {
                $data = $this->_checkParams(
                    $data, array(
                        'Name'         => array('req'=>true, 'type'=>'string'),
                        'StreetName'   => array('req'=>true, 'type'=>'string'),
                        'ZipCode'      => array('req'=>true, 'type'=>'string'),
                        'City'         => array('req'=>true, 'type'=>'string'),
                        'Country'      => array('req'=>true, 'type'=>'string')));
            } catch (Exception $exc) {
                throw new Exception(
                    sprintf(ERROR_NO_107, $exc->getMessage())
                );
            }
            $site = Object::load('Site');
            $site->setOwner($actor);
        }
        $countryCityStr = $data['ZipCode'] . '|' .
            $data['City'] . '|' . $data['Country'];
        $countryCity = findCountryCityFromString($countryCityStr);
        if(!($countryCity instanceof CountryCity)) {
            throw new Exception(
                sprintf(ERROR_NO_103, $countryCityStr)
            );
        }

        // données obligatoires
        $site->setCountryCity($countryCity);

        // données facultatives
        $requiredFields = array('ZipCode', 'City',
            'Country', 'Id');
        foreach ($data as $key=>$value) {
            if(!in_array($key, $requiredFields)) {
                $setter = 'set' . $key;
                if(method_exists($site, $setter)) {
                    $site->$setter($value);
                }
            }
        }
        $site->save();
        if(!in_array($site->getId(), $sitesCollection->getItemIds())) {
            $sitesCollection->setItem($site);
        }
        return $site;
    }
    // }}}
    // APISiteServer::_getSitesArray() {{{

    /**
     * Construit le tableau Sites retourné par auth.loginUser() et
     * auth.registerUser().
     *
     * @param Collection $sitesCollection Collection de Site.
     * @access private
     * @return array
     */
    private function _getSitesArray($sitesCollection) {
        $sitesArray = array(); // contiendra les infos retournées pour le site
        $siteFields = array('Id', 'Name', 'Email', 'Fax', 'Phone', 'Mobile',
            'PreferedCommunicationMode', 'StreetNumber', 'StreetType',
            'StreetName', 'StreetAddons', 'Cedex',
            'GPS', 'Type');

        $count = $sitesCollection->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $site = $sitesCollection->getItem($i);
            $loopSiteArray = array();
            foreach($siteFields as $field) {
                $getter = 'get' . $field;
                $loopSiteArray[$field] = $site->$getter();
                $loopSiteArray['ZipCode'] = Tools::getValueFromMacro($site,
                    '%CountryCity.Zip.Code%');
                $loopSiteArray['City'] = Tools::getValueFromMacro($site,
                    '%CountryCity.CityName.Name%');
                $loopSiteArray['Country'] = Tools::getValueFromMacro($site,
                    '%CountryCity.Country.Id%');
            }
            $sitesArray[] = $loopSiteArray;
        }
        return $sitesArray;
    }

    // }}}
    // APISiteServer::checkActors() {{{

    /**
     * Verifie que 2 actors donnes sont bien en base.
     * Utilise en vue de la commande dans une autre base.
     *
     * @param array of strings $actorNames
     * @access private
     * @return boolean
     */
    protected function checkActors($method, $actorNames) {
        //$this->auth();
        $actorName = $actorNames[0];
        $supplierName = $actorNames[1];
        $mapper = Mapper::singleton('Actor');
        $coll = $mapper->loadCollection(array('Name' => array($actorName, $supplierName)),
                array(), array('Name'));
        $this->log('$coll->getCount() =' . $coll->getCount());
        return ($coll->getCount() == 2);
    }

    // }}}
    // APISiteServer::getDataForOrder() {{{

    /**
     * Recupere, pour des Actors, Sites, leur dans une autre base,
     * en passant leur Name.
     * Actuellement, cela ne fonctionne que pour un attribut 'Name'
     *
     * @param array $data exple:
     * <code>
     * array(
     *   'Actor'  => array(Name1, Name2, Name3, Name4)
     *   'Site'  => array(Name1, Name2, Name3)
     * );
     * </code>
     * ATTENTION:
     * array(
     *       'Actor' => array('toto', 'titi'),
     *       'Site' => array('roro', 'riri')
     *  );
     *  passé en param donne effectivement:
     *   array(
     *       array(
     *          'Actor' => array('toto', 'titi'),
     *          'Site' => array('roro', 'riri')
     *      )
     *   );
     * @access private
     * @return boolean
     */
    protected function getDataForOrder($method, $data) {
        $this->log('getDataForOrder() called.........');
        $return = array();
        $data = $data[0];
        foreach($data as $className=>$names) {
            $return[$className] = array();
            $mapper = Mapper::singleton($className);
            $coll = $mapper->loadCollection(
                    array('Name' => $names), array(), array('Name'));
            $count = $coll->getCount();
            if ($count != count($names)) {
                $this->log('$coll->getCount() incorrect: pas les bonnes donnees pour ' . $className);
                return false;
            }
            for($i = 0; $i < $count; $i++){
                $item = $coll->getItem($i);
                $return[$className][$item->getName()] = $item->getId();
            } // for
        }
        return $return;
    }

    // }}}
    // APISiteServer::auth() {{{

    /**
     * Authentifie un utilisateur via l'auth onlogistics
     * Méthode surchargée pour gestion des dsn avec dbid
     *
     * @access public
     * @param string $user le nom d'utilisateur
     * @param string $realm le nom du realm client (ex: wp)
     * @param string $passwd le mot de passe
     * @return boolean
     */
    public function auth($login=false, $realm=false, $passwd=false, $pf=false) {
        $this->auth = Auth::Singleton();
        if ($this->auth->isUserConnected()) {
            // déjà authentifié
            $user = $this->auth->getUser();
            $this->log('User "'.$user->getLogin().'" already authenticated.');
        } else {
            // l'user n'est pas encore authentifié
            $this->log('Authenticating user "' . $login . '".');
            if (!$login || !$realm || !$passwd) {
                return $this->showAuthError();
            }
            try {
                $dsn = getDSNForRealm($realm);
                define('DB_DSN', $dsn);
            } catch (Exception $exc) {
                return $this->showAuthError();
            }
            Database::connection($dsn);
            $user = $this->auth->login($login, $realm, $passwd);
        }
        if (!($user instanceof UserAccount)) {
            return $this->showAuthError();
        }
        if ($pf && !$this->auth->checkProfiles($pf, array('showErrorDialog' => false))){
            return $this->showAuthError();
        }
        return true;
    }

    // }}}
}

// démarrage du serveur
$server = new APISiteServer();
$server->handleRequest();

?>
