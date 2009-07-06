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

require_once('RPCTools.php');
require_once('SQLRequest.php');

/**
 * Usage:
 *
 * $s = new OnlogisticsXmlRpcServer();
 * $s->handleRequest();
 *
 * Pour ajouter des fonctions, deux options:
 *    - rajouter une méthode à cette classe et l'enregistrer dans le
 *      constructeur (option recommandée)
 *    - créer une fonction et faire:
 *      $s->registerFunction('ns.mafonction');
 *
 * @package    onlogistics
 * @subpackage rpc
 */
class OnlogisticsXmlRpcServer extends XmlRpcServer{
    // OnlogisticsXmlRpcServer::__construct() {{{

    /**
     * Constructor
     * @access protected
     */
    function __construct(){
        parent::__construct();
        $this->registerMethod('zaurus.getStorageSiteList');
        $this->registerMethod('zaurus.getProductList');
        $this->registerMethod('zaurus.getActorList');
        $this->registerMethod('zaurus.getInventoryStorageSiteList');
        $this->registerMethod('zaurus.getInventory');
        $this->registerMethod('zaurus.blockLocations');
        $this->registerMethod('zaurus.unblockLocations');
        $this->registerMethod('zaurus.putCommands');
        $this->registerMethod('zaurus.getCommands');
        $this->registerMethod('zaurus.getCustomerCommands');
        $this->registerMethod('zaurus.getSupplierCommands');
        $this->registerMethod('zaurus.putUnexpectedMovement');
        $this->registerMethod('zaurus.blockMovements');
        $this->registerMethod('zaurus.unblockMovements');
        $this->registerMethod('zaurus.handleInventory');
        $this->registerMethod('onlogistics_desktop.getAuthorizedApps');
        $this->registerMethod('glao_edition.getStorageSiteList');
        $this->registerMethod('glao_edition.getProductList');
        $this->registerMethod('glao_edition.getActorList');
        $this->registerMethod('glao_edition.getUserAccountList');
        $this->registerMethod('glao_import.getCSVData');
        $this->registerMethod('glao_import.spreadSheetListChanged');
        $this->registerMethod('glao_import.getSpreadSheetList');
        $this->registerMethod('glao_import.importSpreadSheetCSVData');
        $this->registerMethod('optimAppro.getSupplierList');
        $this->registerMethod('optimAppro.getDataForStats');
        $this->registerMethod('optimAppro.getSubstitutionInfoList');
        $this->registerMethod('optimAppro.command');
        $this->registerMethod('glao_command.getProductIdsByReferences');
        $this->registerMethod('glao_command.getCustomerCatalogStructure');
        $this->registerMethod('glao_command.getCustomerCatalogData');
        $this->registerMethod('glao_command.getCustomerList');
        $this->registerMethod('glao_command.getOwnerList');
        $this->registerMethod('glao_command.getCommandScreenData');
        $this->registerMethod('glao_command.saveAll');
        $this->registerMethod('onlogistics_biztrack.getServiceConfig');
        $this->registerMethod('onlogistics_biztrack.getBiztrackDatabase');
        $this->registerMethod('onlogistics_biztrack.handleTest');
        $this->registerMethod('btscan.handleValidationScan');
        $this->session = false;
    }
    
    // }}}
    // OnlogisticsXmlRpcServer::getStorageSiteList() {{{

    /**
     * Retourne la liste des sites/magasins/locations pour l'inventaire.
     *
     * @access protected
     * @return array
     **/
    protected function getStorageSiteList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getStorageSiteList called');
        $return = _getStorageSiteList2($this->auth->getActorId());
        return $return;
    }
    
    // }}}
    // OnlogisticsXmlRpcServer::getProductList() {{{

    /**
     * Retourne la liste des produits
     *
     * @access protected
     * @return array
     **/
    protected function getProductList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getProductList called');
        return _getProductList();
    }

    // }}}
    // OnlogisticsXmlRpcServer::getActorList() {{{

    /**
     * Retourne la liste des acteurs.
     *
     * @access protected
     * @return array
     **/
    protected function getActorList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getActorList called');
        return _getActorList();
    }

    // }}}
    // OnlogisticsXmlRpcServer::getUserAccountList() {{{

    /**
     * Retoune la liste des UserAccount sous la forme d'un tableau de tuples:
     * array(array(ID, Nom1), array(ID, Nom2), etc...)
     *
     * @access protected
     * @return array
     **/
    protected function getUserAccountList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getUserAccountList called');
        $ret = array();
        $mapper = Mapper::singleton('UserAccount');
        $col = $mapper->loadCollection(array(),
            array('Identity'=>SORT_ASC), array('Id', 'Identity'));
        $count = $col->getCount();
        for ($i=0; $i<$count; $i++) {
            $user = $col->getItem($i);
            $ret[] = array($user->getId(), $user->getIdentity());
        }
        return $ret;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getInventoryStorageSiteList() {{{

    /**
     * Retourne la liste des sites/magasins/locations pour l'inventaire
     *
     * @access protected
     * @return array
     **/
    protected function getInventoryStorageSiteList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getInventoryStorageSiteList called');
        return _getStorageSiteList($this->auth->getActorId());
    }

    // }}}
    // OnlogisticsXmlRpcServer::getInventory() {{{

    /**
     * Retourne le fichier xml pour l'inventaire à partir des emplacements
     * sélectionnés par l'utilisateur.
     *
     * @access protected
     * @return void
     **/
    protected function getInventory($method, $params) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getInventory called');
        return _getInventory($params[0]);
    }

    // }}}
    // OnlogisticsXmlRpcServer::getAuthorizedApps() {{{

    /**
     * Retourne la liste des applications python autorisées en fonction de
     * l'utilisateur connecté.
     * XXX: il faudra à terme je pense créer une table d'association
     * PythonApp/Profile, et eventuellement un écran de paramétrage pour
     * définir les droits.
     *
     * @access protected
     * @return object XML_RPC_Response
     **/
    protected function getAuthorizedApps($method) {
        $this->auth(false, false, false,
            array(
                UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_GESTIONNAIRE_STOCK,
                UserAccount::PROFILE_CUSTOMER, UserAccount::PROFILE_COMMERCIAL,
                UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES
            )
        );
        $this->log('OnlogisticsXmlRpcServer::getAuthorizedApps() called');
        $pfID = $this->auth->getProfile();
        if ($this->auth->isRootUserAccount()) {
            // le root à accès à toutes les applis
            $ret = array('glao_edition', 'glao_import', 'glao_optimappro',
                'glao_command');
        } elseif (in_array($pfID,
        array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))) {
            // l'admin à accès à optimappro et glao-import
            $ret = array('glao_optimappro', 'glao_import', 'glao_command');
        } elseif ($pfID == UserAccount::PROFILE_GESTIONNAIRE_STOCK) {
            // le gestionnaire de stock à glao-edition
            $ret = array('glao_edition');
        } elseif (in_array($pfID,
                array(UserAccount::PROFILE_CUSTOMER, UserAccount::PROFILE_COMMERCIAL,
                      UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES))) {
            // Accès uniquement a glao_command
            $ret = array('glao_command');
        } else {
            // on ne devrait pas être ici
            $ret = array();
        }
        return $ret;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getCSVData() {{{

    /**
     * Retourne une chaine csv compressée gzip, des champs de la table donnée.
     * le tablea $params doit contenir les variables definies ci dessous.
     *
     * @param string $table le nom de la table sql
     * @param array $fields le tableau des champs à traiter
     * @access protected
     * @return object XML_RPC_Response
     **/
    protected function getCSVData($method, $params){
        $this->auth(false, false, false,
            array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
        $this->log('OnlogisticsXmlRpcServer::getCSVData called');
        $cls     = $params[0];
        $propcls = $params[1];
        $fields  = $params[2];
        $sql = false;
        if (strtolower($fields[1]) == 'tostring') {
            if (is_callable(array($propcls, 'getCSVDataSQL'))) {
                $sql = call_user_func(array($propcls, 'getCSVDataSQL'));
            } else {
                $tmp = new $propcls();
                $attrs = $tmp->getToStringAttribute();
                if (!is_array($attrs)) {
                    $fields[1] = $attrs;
                } else {
                    $fields[1] = array_shift($attrs);
                    $fields = array_merge($fields, $attrs);
                }
                $sql = false;
            }
        }
        $return = '';
        $newline = '';
        if (false !== $sql) {
            // on passe en mode ADODB_FETCH_NUM pour + de perfs
            Database::connection()->setFetchMode(ADODB_FETCH_NUM);
            // execution de la requête
            $rs = Database::connection()->execute($sql);
            $return = $newline = '';
            if ($rs) {
                while (!$rs->EOF) {
                    $id = array_shift($rs->fields);
                    $return .= $newline . $id . ';' . implode(' ', $rs->fields);
                    $newline = "\n";
                    $rs->moveNext();
                }
                $rs->close();
            }
            // on remet le mode à sa valeur par défaut
            Database::connection()->setFetchMode(ADODB_FETCH_DEFAULT);
        } else {
            $col = Object::loadCollection($propcls, array(), 
                array($fields[1]=>SORT_ASC), $fields);
            $count = $col->getCount();
            for ($i=0; $i<$count; $i++) {
                $item = $col->getItem($i);
                $return .= $newline;
                $padding = '';
                for ($j=0; $j<count($fields); $j++) {
                    $getter = 'get'.$fields[$j];
                    $return .= $padding . $item->$getter();
                    $padding =  ($j == 0) ? ';' : ' ';
                }
                $newline = "\n";
            }
        }
        // on utilise la compression gzip pour optimiser le temps de transfert
        $return = base64_encode(gzencode($return));
        return $return;
    }

    // }}}
    // OnlogisticsXmlRpcServer::spreadSheetListChanged() {{{

    /**
     * Retourne true si la date passée en paramètre est plus ancienne que
     * la date de dernière modification d'une SpreadSheet.
     *
     * @access protected
     * @return array
     */
    protected function spreadSheetListChanged($method, $params) {
        $this->auth(false, false, false,
            array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
        $this->log('OnlogisticsXmlRpcServer::spreadSheetListHasChanged called');
        $ret = lastModifiedSpreadSheetDate();
        if (!$ret) {
            return false;
        }
        $date = $params[0];
        return $date < $ret->fields[0];
    }

    // }}}
    // OnlogisticsXmlRpcServer::getSpreadSheetList() {{{

    /**
     * Retourne un tableau associatif des modèles de tableurs et des colonnes.
     *
     * @access protected
     * @return array
     */
    protected function getSpreadSheetList($method) {
        $this->auth(false, false, false,
            array(UserAccount::PROFILE_ROOT, UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
        $this->log('OnlogisticsXmlRpcServer::getSpreadSheetList called');
        $mapper = Mapper::singleton('SpreadSheet');
        $col = $mapper->loadCollection(array('Active'=>true));
        $count = $col->getCount();
        $array = array();
        for ($i=0; $i<$count; $i++) {
            $sps = $col->getItem($i);
            $entity = $sps->getEntity();
            if (!($entity instanceof Entity)) {
                return $array;
            }
            $entity = $entity->getName();
            // tri des colonnes par ordre
            $sscCol = $sps->getSpreadSheetColumnCollection(
                array(),
                array('Order'=>SORT_ASC)
            );
            $jcount = $sscCol->getCount();
            $columnsArray = array();
            for ($j=0; $j<$jcount; $j++) {
                $ssc = $sscCol->getItem($j);
                $ptype = $ssc->getPropertyType();
                $pname = $ssc->getPropertyName();
                $pclass = $ssc->getPropertyClass();
                $fkeypname = $ssc->getFkeyPropertyName();
                $cacheable = 0;
                // pour les types "constante" on envoie aussi un mapping des
                // constantes
                $map = false;
                if ($ptype == Object::TYPE_CONST) {
                    require_once('Objects/' . $entity . '.php');
                    $map = call_user_func(array($entity, 'get' . $pname . 'ConstArray'));
                } else if (in_array($ptype, array(Object::TYPE_FKEY, Object::TYPE_ONETOMANY, Object::TYPE_MANYTOMANY))) {
                    // foreignkey ou relation
                    $pname = $pclass;
                    $cacheable = 1;
                    if (!$fkeypname) {
                        $fkeypname = 'toString';
                    }
                }
                if ($ptype == Object::TYPE_I18N_STRING || 
                    $type == Object::TYPE_I18N_TEXT || 
                    $type == Object::TYPE_I18N_HTML) {
                    // le type i18n est géré comme les types string côté client
                    $ptype = Object::TYPE_STRING;
                }

                $columnsArray[] = array(
                    'id'=>$ssc->getId(),
                    'name'=>$ssc->getName(),
                    'comment'=>$ssc->getComment(),
                    'default'=>$ssc->getDefault(),
                    'property_name'=>$pname,
                    'property_type'=>$ptype,
                    'fkey_property_name'=>$fkeypname,
                    'cacheable'=>$cacheable,
                    'required'=>$ssc->getRequired(),
                    'width'=>$ssc->getWidth(),
                    'ids'=>$map?array_keys($map):false,
                    'labels'=>$map?array_values($map):false
                );
            }
            $values = array(); //getDataForSpreadSheet($sps);
            $array[] = array(
                'id'=>$sps->getId(),
                'name'=>$sps->getName(),
                'entity'=>$entity,
                'columns'=>$columnsArray,
                'values'=>$values
            );
        }
        return $array;
    }

    // }}}
    // OnlogisticsXmlRpcServer::importSpreadSheetCSVData() {{{

    /**
     * Importe les données csv $data (avec les séparateurs passés en paramètre)
     * du tableur avec l'id $ssId dans la base de données.
     *
     * @access private
     * @param  integer $ssId l'id du tableur
     * @param  string $data les données au format csv
     * @param  string $line_sep le séparateur de lignes
     * @param  string $field_sep le séparateur de colonnes
     * @return mixed boolean or string error
     **/
    protected function importSpreadSheetCSVData($method, $params) {
        $this->auth(false, false, false,
            array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
        $this->log('OnlogisticsXmlRpcServer::importSpreadSheetCSVData called');
        // récupération des paramètres
        list($ssId, $data, $line_sep, $field_sep) = $params;
        // appel de la fonction définie dans lib-functions/RPCTools.php
        return _importSpreadSheetCSVData($ssId, $data, $line_sep, $field_sep);
    }

    // }}}
    // OnlogisticsXmlRpcServer::blockLocations() {{{

    /**
     * blockLocations: bloque les locations passés en paramètres.
     *
     * @access protected
     * @param array $locationIds
     * @return boolean
     **/
    protected function blockLocations($method, $params){
        $this->auth();
        $this->Log('OnlogisticsXmlRpcServer::blockLocations called');
        $locationIds = $params[0];
        _blockLocations($locationIds);
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::unblockLocations() {{{

    /**
     * unblockLocations: débloque les locations passés en paramètres.
     *
     * @access protected
     * @param array $locationIds
     * @return boolean
     **/
    protected function unblockLocations($method, $params){
        $this->auth();
        $this->Log('OnlogisticsXmlRpcServer::unblockLocations called');
        $locationIds = $params[0];
        _blockLocations($locationIds, true);
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::putCommands() {{{

    /**
     * putData => récupère les résultats envoyés après traitement par le pda.
     *
     * @access protected
     * @param object $ data : struct ('name1' => string "xmlcontent", ...)
     * @return  bool
     */
    protected function putCommands($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::putCommands called');
        require_once('ExecutionXMLParsing.php');
        $data = $params[0];
        $errors = "";
        $padding = "";
        foreach($data as $cmdnumber=>$xmldata) {
            $result = ParseCommand($xmldata, $cmdnumber);
            if (is_string($result)) {
                $errors .= $padding . $result;
                $padding = "<br />";
            }
        }
        if (strlen($errors) == 0) {
            return true;
        }
        return $errors;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getCommands() {{{

    /**
     * getCommands => récupère les donnees du serveur pour traitement par le pda.
     * ( pour les pdas non upgrades avec glao-embedded < 0.12 )
     *
     * @access protected
     * @return array(0 => string xmlcontent, 1 => ...)
     */
    protected function getCommands($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getCommands called');
        require_once('Execution/ExecutionDataXMLRenderer.php');
        $data = $params[0];
        $renderer = new MovementExecutionDataXMLRenderer($this->auth->getUser(), $data);
        $result = $renderer->render();
        return $result;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getCustomerCommands() {{{

    /**
     * getCustomerCommands => récupère les commandes clients
     *  ( pda upgradés avec glao-embedded >= 0.12 )
     *
     * @access protected
     * @return array(0 => string xmlcontent, 1 => ...)
     */
    protected function getCustomerCommands($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getCommands called');
        require_once('Execution/ExecutionDataXMLRenderer.php');
        $data = $params[0];
        $renderer = new MovementExecutionDataXMLRenderer($this->auth->getUser(), $data, 1);
        $result = $renderer->render();
        return $result;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getSupplierCommands() {{{

    /**
     * getSupplierCommands => récupère les commandes fournisseurs
     *  ( pda upgradés avec glao-embedded >= 0.12 )
     *
     * @access protected
     * @return array(0 => string xmlcontent, 1 => ...)
     */
    protected function getSupplierCommands($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getCommands called');
        require_once('Execution/ExecutionDataXMLRenderer.php');
        $data = $params[0];
        $renderer = new MovementExecutionDataXMLRenderer($this->auth->getUser(), $data , 2);
        $result = $renderer->render();
        return $result;
    }

    // }}}
    // OnlogisticsXmlRpcServer::putUnexpectedMovement() {{{

    /**
     * putUnexpectedMovement.
     *
     * @access protected
     * @param object $ data : struct ('name1' => string "xmlcontent", ...)
     * @return  bool
     */
    protected function putUnexpectedMovement($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::putmovementdata called');
        require_once('ExecutedMovementWithoutForecastXMLParsing.php');
        $data = $params[0];
        $result = process($this->auth, $data);
        return $result;
    }

    // }}}
    // OnlogisticsXmlRpcServer::blockMovements() {{{

    /**
     * blockMovements: bloque les mouvements passés en paramètres.
     * En fait le script met leur état à ActivatedMovement::ACM_EN_COURS
     *
     * @param array $movementIds
     * @return boolean
     */
    protected function blockMovements($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::blockMovements called');
        $movementIds = $params[0];
        _blockMovements($movementIds, true);
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::unblockMovements() {{{

    /**
     * unblockMovements: débloque les mouvements passés en paramètres.
     * En fait le script met leur état à ActivatedMovement::CREE (0)
     *
     * @param array $movementIds
     * @return boolean
     */
    protected function unblockMovements($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::unblockMovements called');
        $movementIds = $params[0];
        _blockMovements($movementIds, false);
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::handleInventory() {{{

    /**
     * Traite le fichier xml remonté par le zaurus après inventaire
     *
     * @access protected
     * @param data la chaine xmlrpc contenant une chaine xml
     * @return mixed true si tout est ok, une chaine d'erreur sinon
     **/
    protected function handleInventory($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::handleInventory called');
        $data = $params[0];
        $result = _handleInventory($this->auth, $data);
        return $result;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getSupplierList() {{{

    /**
     * Liste de tous les fournisseurs en bado
     * (Pas necessaire de mutualiser pour la version web)
     * @access protected
     * @return array of arrays
     **/
    protected function getSupplierList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getSupplierList called');
        $SupplierArray = array();  // Contiendra les resultats
        $ActorMapper = Mapper::singleton('Actor');
        $SupplierCollection = $ActorMapper->loadCollection(
            array('ClassName' => array('Supplier', 'AeroSupplier'), 'Active'=>1), 
            array('Name' => SORT_ASC), 
            array('Name'));

        for($i = 0; $i < $SupplierCollection->getCount(); $i++) {
            $Supplier = $SupplierCollection->getItem($i);
            $SupplierArray[] = array($Supplier->getId(), $Supplier->getName());
        }
        return $SupplierArray;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getDataForStats() {{{

    /**
     * Retourne les dates de livraison souhaitees au format MySQL,
     * pour les semaines a venir, en cas de commande
     * en fonction du delai de livraison, pour un fournisseur passe en parametre,
     * pour livrer l'Actor lie au UserAccount connecte
     * Met aussi en session des donnees
     * @param $params array : dictionnaire
     * @access protected
     * @return array or boolean
     **/
    protected function getDataForStats($method, $params) {
    	$this->auth();
        $this->log('OnlogisticsXmlRpcServer::getDataForStats called');
        require_once('SupplyingOptimizator.php');
        $params = $params[0];

        $this->log('OnlogisticsXmlRpcServer::getDataForStats: recup des donnees...');
        $optimizator = new SupplyingOptimizator($params);
        $data = $optimizator->getData();
        if ($data === false) {
            return false;
        }

        $data['wishedStartDate'] = $this->_formatArray($optimizator->wishedStartDates);
        foreach($data as $name=>$content) {
            // Pas de _formatArray() pour obtenir une liste et non un dico!
            if ($name != 'productIdSorted') {
                $data[$name] = $this->_formatArray($content);
            }
        }
        return $data;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getSubstitutionInfoList() {{{

    /**
     * Retourne les infos a afficher en cas de substitution
     * @param $ProductIdArray array of integer (Ids de Product)
     * @access protected
     * @return array of strings
     **/
    protected function getSubstitutionInfoList($method, $ProductIdArray) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::GetSubstitutionInfo called');
        require_once('SupplyingOptimizator.php');
        $ProductIdArray = $ProductIdArray[0];
        $SubstitutionInfo = SupplyingOptimizator::getSubstitutionInfoList(
                $ProductIdArray, $_SESSION['supplierId']);

        return $this->_formatArray($SubstitutionInfo);
    }

    // }}}
    // OnlogisticsXmlRpcServer::command() {{{

    /**
     * Enregistre une Commande de Products
     *
     * @param array $params
     * valeurs d'attributs de la Command, et array(pdtId => qty, ...)
     * @access protected
     * @return object XML_RPC_Response (boolean true si OK ou string: msg d'erreur)
     **/
    protected function command($method, $params) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::command called');
        $paramsArray = $params[0];
        $pdtArray = $params[1];
        $Supplier = Object::load('Actor', $paramsArray['SupplierId']);
        $paramsArray['Incoterm'] = $Supplier->getIncotermId();
        $paramsArray['Destinator'] = $this->auth->getActorId();
        $paramsArray['Expeditor'] = $paramsArray['SupplierId'];
        $paramsArray['ExpeditorSite'] = $Supplier->getMainSiteId();
        $paramsArray['DestinatorSite'] = $this->auth->getActor()->getMainSiteId();
        $paramsArray['Customer'] = $this->auth->getActorId();
        $paramsArray['Commercial'] = $Supplier->getCommercialId();

        require_once('CommandManager.php');
        require_once('Objects/Command.php');
        // La transaction innodb est initiee dans le constructeur
        $manager = new CommandManager(array(
            'CommandType'        => 'ProductCommand',
            'ProductCommandType' => Command::TYPE_SUPPLIER,
            'UseTransaction'     => true
        ));
        $result = $manager->createCommand($paramsArray);
        if (Tools::isException($result)) {
            return $result->getMessage();
        }
        //$this->log('CM::createCommand was called');
        foreach($pdtArray as $pdtId => $qty) {
            // le product
            $pdtMapper = Mapper::singleton('Product');
            $pdt = $pdtMapper->load(array('Id'=>$pdtId));
            if (!($pdt instanceof Product)) {
                // huh... la bdd est désyncronisée avec le client
                continue;
            }
            // Tableau des paramètres; note: Pour PriceHT, promo et tva:
            // geres par CommandManager::_checkAndFormatCommandItemParams()
            $cmiParams = array('Product' => $pdtId, 'Quantity' => $qty);
            $result = $manager->addCommandItem($cmiParams);
            if (Tools::isException($result)) {
                return $result->getMessage();
            }
        }
        //$this->log('CM::addCommandItem were called');
        $ach = $manager->activateProcess();
        if (Tools::isException($ach)) {
            return $ach->getMessage();
        }
        //$this->log('CM::activateProcess was called');
        $result = $manager->validateCommand();
        if (Tools::isException($result)) {
            return $result->getMessage();
        }
        //$this->log('CM::validateCommand was called');
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getProductIdsByReferences() {{{

    /**
     * Retourne une liste d'ids à partir d'une liste références.
     * Si toutes les refs ne sont pas présentes en bdd, les refs erronées dont
     * retournées sous forme d'une chaine séparée par des virgules.
     *
     * @param array $params
     * @access protected
     * @return object XML_RPC_Response (une liste ou une chaine d'erreur)
     */
    protected function getProductIdsByReferences($method, $params) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getProductIdsByReferences called');
        $refArray = $params[0];
        // Les ref client (param optionnel)
        $assocRefArray = $params[1];
        // Le customer selectionne
        $customerId = $params[2];
        $mapper = Mapper::singleton('Product');
        $realRefArray = array();
        $col = new Collection;
        $col2 = new Collection;

        // Si le fichier importe contient des refs client
        if (is_array($assocRefArray)) {
            // Si des lignes contiennent baseRef et ref client,
            // c'est cette derniere qui prime
            // On y supprime les ref client vides
            $assocRefArray = array_filter($assocRefArray, 'strlen');
            // Les ref client a tester
            $notEmptyAssocRefArray = array_values($assocRefArray);
            // Les baseRef a tester
            $notEmptyRefArray = array_values(
                    array_diff_key($refArray, $assocRefArray));

            // Test sur les Ref client
            if (count($notEmptyAssocRefArray) > 0) {
                $filterArray = array();
                $filterArray[] = SearchTools::NewFilterComponent(
                    'Customer', 'ActorProduct().Actor', 'Equals', $customerId, 1, 'Product');
                $filterArray[] = SearchTools::NewFilterComponent(
                    'AssocRef', 'ActorProduct().AssociatedProductReference',
                    'In', $notEmptyAssocRefArray, 1, 'Product');
                $filter = SearchTools::filterAssembler($filterArray);
                $col = $mapper->loadCollection(
                    $filter, array(), array('BaseReference'));
                $count = $col->getCount();
                for ($i=0; $i<$count; $i++) {
                    $pdt = $col->getItem($i);
                    $ref = $pdt->getReferenceByActor($customerId);
                    if (in_array($ref, $notEmptyAssocRefArray)) {
                        $realRefArray[] = $ref;
                    }
                }
            }
        }
        // Test sur les BaseRefences
        $baseRefArray = isset($notEmptyRefArray)?$notEmptyRefArray:$refArray;
        if (!empty($baseRefArray)) {
            $col2 = $mapper->loadCollection(
                array('BaseReference'=>$refArray),
                array(),
                array('BaseReference')
            );
            $count = $col2->getCount();
            for ($i=0; $i<$count; $i++) {
                $pdt = $col2->getItem($i);
                $ref = $pdt->getBaseReference();
                if (in_array($ref, $baseRefArray)) {
                    $realRefArray[] = $ref;
                }
            }
        }

        $refArray = isset($notEmptyAssocRefArray)?
            array_merge($notEmptyRefArray, $notEmptyAssocRefArray):$refArray;
        $errors = array_diff($refArray, $realRefArray);
        if (count($errors) > 0) {
            return implode(', ', $errors);
        }
        return $col = $col2->merge($col)->getItemIds();
    }

    // }}}
    // OnlogisticsXmlRpcServer::getCustomerCatalogStructure() {{{

    /**
     * Retourne une liste de dictionnaires représentant les colonnes à afficher
     * dans le listview du catalogue client en fonction du user connecté.
     * Les colonnes sont triées par leur Index d'affichage.
     *
     * Exemple de liste retournée:
     * [
     *     {'DisplayName':'Référence', 'Type':1, 'Accessor':'getBaseReference:0'},
     *     {'DisplayName':'Famille', 'Type':1, 'Accessor': 'getFamily:0'},
     *     etc...
     * ]
     *
     * Type correspond aux constantes définies dans Object.php et DisplayName
     * représente le nom de colonne à afficher, Accessor est utilisé de manière
     * interne et ne devrait pas être utilisé dans le client.
     *
     * @access protected
     * @return object XML_RPC_Response
     */
    protected function getCustomerCatalogStructure($method, $params) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getCustomerCatalogStructure called.');
        $user = $this->auth->getUser();
        $customerID = $params[0];
        $catalog = $user->getCatalog();
        if (!($catalog instanceof Catalog)) {
            return false;
        }
        $struct = $catalog->getStructureArray($customerID);
        // on met en session la structure pour usage ulterieur
        $_SESSION['CustomerCatalogStructure'] = $struct;
        return $struct;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getCustomerCatalogData() {{{

    /**
     * Retourne une chaine gzippée représentant pour chaque ligne les données
     * (séparées par des "|" ) d'un produit de la collection de produit
     * correspondante au catalogue de l'user connecté.
     * ATTENTION: la première entrée de chaque tuple est l'id du produit, le
     * reste correspond à l'ordre des colonnes tel que défini dans le catalogue.
     *
     * Exemple de chaine retournée:
     * 1|1234|Produit test|Un super produit|30|Test
     * 3|3234|Produit test 2|Un autre super produit|5|Machin
     * etc...
     *
     * Cette liste est retournée pour un catalogue qui aurait comme structure:
     * BaseReference | Name | Description | Price | Category
     *
     * @access protected
     * @return object XML_RPC_Response
     */
    protected function getCustomerCatalogData($method, $params) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getCustomerCatalogData called');
        // le customer passé en paramètre
        $customerID = $params[0];
        $searchData = $params[1];
        $custMapper  = Mapper::singleton('Actor');
        $customer = $custMapper->load(array('Id'=>$customerID));

        // le catalogue de l'utilisateur connecté
        $user = $this->auth->getUser();
        $catalog = $user->getCatalog();
        // la structure du catalogue
        if (isset($_SESSION['CustomerCatalogStructure'])) {
            $struct = $_SESSION['CustomerCatalogStructure'];
        } else {
            $struct = $catalog->getStructureArray($customerID);
        }

	    // filtres pour la collection de produits
        $filterArray = array();
	    $ptypes = array_keys($catalog->getProductTypeList());
	    $filterArray[] = SearchTools::NewFilterComponent('ProductType', '', 'In', $ptypes, 1);
        $filterArray[] = SearchTools::NewFilterComponent('Activated', '', 'Equals', 1, 1);
        $filterArray[] = SearchTools::NewFilterComponent('Affected', '', 'Equals', 1, 1);
        if (!empty($searchData['BaseReference'])) {
            $filterArray[] = SearchTools::NewFilterComponent('BaseReference', '', 'Like',
                str_replace('*', '%', $searchData['BaseReference']), 1);
        }
        if (!empty($searchData['AssociatedReference'])) {
            $filterArray[] = SearchTools::NewFilterComponent(
                'AssociatedRef', 'ActorProduct().AssociatedProductReference', 'Like',
                str_replace('*', '%', $searchData['AssociatedReference']), 1, 'Product');
            $filterArray[] = SearchTools::NewFilterComponent(
                'Customer', 'ActorProduct().Actor', 'Equals', $customerID, 1, 'Product');
        }
        $this->log($searchData['Name']);
        if (!empty($searchData['Name'])) {
            $filterArray[] = SearchTools::NewFilterComponent('Name', '', 'Like',
                str_replace('*', '%', $searchData['Name']), 1);
        }
        if ($searchData['Supplier'] > 0) {
            $filterArray[] = SearchTools::NewFilterComponent('Supplier',
                'ActorProduct().Actor.Id', 'Equals', $searchData['Supplier'],
                1, 'Product');
        }
        if ($searchData['Owner'] > 0) {
            $filterArray[] = SearchTools::NewFilterComponent('Owner',
                'Owner.Id', 'Equals', $searchData['Owner'], 1, 'Product');
        }

	    $filter = SearchTools::filterAssembler($filterArray);
	    $order = array('BaseReference' => SORT_ASC);

        // chargement de la collection
        $mapper = Mapper::singleton('Product');
        $col = $mapper->loadCollection($filter, $order);//, array(), 0, 1, 2);
        $count  = $col->getCount();
        $jcount = count($struct);
        $linePadding = $data = '';
        $padding = '|';
        for ($i=0; $i<$count; $i++) {
            $pdt = $col->getItem($i);
            // si filtre sur les produits en promotion:
            $promo = $pdt->getPromotion($customer);
            if (!$promo && $searchData['IsInPromotion']) {
                continue;
            }
            $data .= $linePadding . $pdt->getId();
            for ($j=0; $j<$jcount; $j++) {
                $structItem = $struct[$j];
                list($accessor, $param) = explode(':', $structItem['Accessor']);
                if (preg_match('/%.+%/', $accessor)) {
                    // on a une macro
                    $result = Tools::getValueFromMacro($pdt, $accessor);
                    $data  .= $padding . $result;
                } else {
                    if ($accessor == 'getUnitHTForCustomerPriceWithDiscount'
                    && (int)$param == 1) {
                        $result = $pdt->$accessor($customer);
                    } elseif ((int)$param == 0) {
                        $result = $pdt->$accessor();
                    }else {
                        $result = $pdt->$accessor((int)$param);
                    }
                    if ($structItem['Type'] == Property::OBJECT_TYPE) {
                        // gestion des fkeys
                        if ($result instanceof Object) {
                            $result = $result->toString();
                        } else {
                            $result = 'N/A';
                        }
                    }
                    // XXX
                    // le trim est nécessaire car il y a encore des produits en
                    // bdd avec des "\n"... il faudra enlever le trim qd tout
                    // sera ok.
                    $data .= $padding . trim(str_replace("\n", '', $result));
                }
            }
            $linePadding = "\n";
        }
        // on utilise la compression gzip pour optimiser le temps de transfert
        $data = base64_encode(gzencode($data));
        return $data;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getCustomerList() {{{

    /**
     * Retourne une liste de clients en fonction du profile du user connecté.
     *
     * @access protected
     * @return object XML_RPC_Response
     */
    protected function getCustomerList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getCustomerList called');
        $profileId = $this->auth->getProfile();
        $entity = array('Customer', 'AeroCustomer');

        if ($profileId == UserAccount::PROFILE_COMMERCIAL) {
            $filter = array('Commercial' => $this->auth->getUserId(), 'Active' => 1);
        } elseif ($profileId == UserAccount::PROFILE_CUSTOMER) {
            $filter = array('Id' => $this->auth->getActorId(), 'Active' => 1);
        } elseif (in_array($profileId,
                array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_COMMERCIAL,
                      UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES))) {
            $filter = array('Active' => 1);
        } else {  // On ne devrait pas etre ici
            $filter = array('Id' => -1);
        }
        $filter['ClassName'] = array('Customer', 'AeroCustomer');
        $actorMapper = Mapper::singleton('Actor');
        $customerCollection = $actorMapper->loadCollection(
                $filter, array('Name' => SORT_ASC), array('Name'));

        $count = $customerCollection->getCount();
        $customerArray = array();
        for($i = 0; $i < $count; $i++) {
            $Customer = $customerCollection->getItem($i);
            $customerArray[] = array($Customer->getId(), $Customer->getName());
        }
        return $customerArray;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getOwnerList() {{{

    /**
     * Retourne une liste d'acteurs owner
     *
     * @access protected
     * @return object XML_RPC_Response
     */
    protected function getOwnerList($method) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getOwnerList called');
        $profileId = $this->auth->getProfile();
        $ownerCol = Object::loadCollection(
            'Actor',
            array('Active'=>1),
            array('Name' => SORT_ASC),
            array('Name')
        );
        $count = $ownerCol->getCount();
        $ownerArray = array();
        for($i = 0; $i < $count; $i++) {
            $owner = $ownerCol->getItem($i);
            $ownerArray[] = array($owner->getId(), $owner->getName());
        }
        return $ownerArray;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getCommandScreenData() {{{

    /**
     * Retourne un dictionnaire représentant les données à afficher dans
     * l'écran de commande du client python en fonction des produits et du
     * client passé en paramètres.
     *
     * Exemple de tableau retourné:
     * ----------------------------
     * Array(
     *  [IncotermList] => Array(
     *      [0] => Array(4, "CIP: Carriage and Insurance Paid To"),
     *      ...
     *  )
     *  [CommercialList] => Array(
     *      [0] => Array(45000, "Philippe Thull", true),
     *      ...
     *  )
     *  [ExpeditorList] => Array(
     *      [0] => Array
     *          (
     *              1,
     *              WinePassion,
     *              Array(
     *                  [0] => Array(1, "WinePassion"),
     *                  ...
     *              )
     *          )
     *  )
     *  [DestinatorList] => Array
     *      [0] => Array
     *          (
     *              1,
     *              WinePassion,
     *              Array(
     *                  [0] => Array(1, "Toto"),
     *                  ...
     *              )
     *          )
     *  )
     *  [UpdateIncur] => 0
     *  [MaxIncur] => 0
     *  [PortTVA] => 0
     *  [InsuranceTVA] => 0
     *  [PackingTVA] => 10.3
     *  [Currency] => 10.3
     *  [MiniAmountToOrder] => 100
     *  [ProductData] => Array(
     *      [0] => Array
     *          (
     *              [Reference] => "001452075B",
     *              [Name] => "Coteaux d'Ancenis",
     *              [Type] => "VIN",
     *              [BaseHT] => 2.95,
     *              [PriceHT] => 2.95,
     *              [TVA] => 19.6,
     *              [Promotion] =>,
     *              [UBNumber] => 1
     *          )
     *      ),
     *      ...
     *  )
     * )
     *
     * @access protected
     * @param array $params: tableau d'arguments: id du customer et array d'id
     *                       de produits
     * @return object XML_RPC_Response
     */
    protected function getCommandScreenData($method, $params) {
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::getCommandScreenData called');
        require_once('ProductCommandTools.php');
        require_once('Objects/TVA.inc.php');
        $custId = $params[0];
        $pdtIds = $params[1];
        $result = array();
        // le customer
        $actMapper = Mapper::singleton('Actor');
        $cust = $actMapper->load(array('Id'=>$custId));
        // la collection de produits
        $pdtMapper = Mapper::singleton('Product');
        $pdtCol = $pdtMapper->loadCollection(
                array('Id' => $pdtIds), array('BaseReference' => SORT_ASC));
        // récupération des incoterms
        $result['IncotermList'] = array();
        $incMapper = Mapper::singleton('Incoterm');
        $incCol = $incMapper->loadCollection(array(), array('Label'=>SORT_ASC));
        $count = $incCol->getCount();
        for ($i=0; $i<$count; $i++) {
            $inc = $incCol->getItem($i);
            $result['IncotermList'][] = array($inc->getId(), $inc->toString());
        }
        // récupération des commerciaux
        $custComID = $cust->getCommercialId();
        $result['CommercialList'] = array(array(0, 'Aucun', false));
        $comMapper = Mapper::singleton('UserAccount');
        $comCol = $comMapper->loadCollection(
                array('Profile'=>UserAccount::PROFILE_COMMERCIAL), array('Identity'=>SORT_ASC));
        $count  = $comCol->getCount();
        for ($i=0; $i<$count; $i++) {
            $com = $comCol->getItem($i);
            $result['CommercialList'][] = array($com->getId(),
                $com->getIdentity(), ($custComID==$com->getId()));
        }
        // Récupération des expéditeurs et de leur(s) site(s)
        $result['ExpeditorList'] = array();
        try {
            // peut lever une exception
            $expIDs = getExpeditorList($pdtCol, $cust);
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        $expCol = $actMapper->loadCollection(
            array('Id'=>$expIDs), array('Name'=>SORT_ASC));
        $count  = $expCol->getCount();
        for ($i=0; $i<$count; $i++) {
            $exp = $expCol->getItem($i);
            $siteCol = $exp->getSiteCollection();
            $jcount = $siteCol->getCount();
            $siteArray = array();
            for ($j=0; $j<$jcount; $j++) {
                $site = $siteCol->getItem($j);
                $siteArray[] = array($site->getId(), $site->getName());
            }
            $result['ExpeditorList'][] = array($exp->getId(), $exp->getName(),
                $siteArray);
        }
        // Récupération du destinataire et de son (ses) site(s)
        $siteCol = $cust->getSiteCollection();
        $count = $siteCol->getCount();
        $siteArray = array();
        for ($i=0; $i<$count; $i++) {
            $site = $siteCol->getItem($i);
            $siteArray[] = array($site->getId(), $site->getName());
        }
        // il n'y en a qu'un mais ça permet de garder la même interface
        $result['DestinatorList'] = array();
        $result['DestinatorList'][0] = array($cust->getId(), $cust->getName(),
            $siteArray);
        // récupération de l'encours courant, maximum et des terms of payment
        $spc = findSupplierCustomer($this->auth->getActor(), $cust);
        $result['CurrentIncur'] = $spc->getUpdateIncur();
        $result['MaxIncur'] = $spc->getMaxIncur();
        $top = $spc->getTermsOfPayment();
        $result['PaymentCondition'] = $top instanceof TermsOfPayment ? 
            $top->getName() : '';
        // devise
        $cur = $cust->getCurrency();
        $result['Currency'] = $cur instanceof Currency?$cur->getShortName():'EUR';
        // récupération des TVA pour les frais, si le spc est soumis à la tva
        $result['PortTVA']      = $spc->getHasTVA()?getTVARateByCategory(TVA::TYPE_DELIVERY_EXPENSES):0;
        $result['InsuranceTVA'] = $spc->getHasTVA()?getTVARateByCategory(TVA::TYPE_INSURANCE):0;
        $result['PackingTVA']   = $spc->getHasTVA()?getTVARateByCategory(TVA::TYPE_PACKING):0;
        
        // Gestion du MiniAmountToOrder
        // Affichage du seuil mini HT de commande autorise, si profile 'client'
        $customerProfiles = array(UserAccount::PROFILE_CUSTOMER,UserAccount::PROFILE_OWNER_CUSTOMER);
        if (in_array($this->auth->getProfile(), $customerProfiles)) {
            // Euro par defaut
            if (!($cur instanceof Currency)) {
                $curMapper = Mapper::singleton('Currency');
                $cur = $curMapper->load(array('ShortName' => 'EUR'));
            }
            $result['MiniAmountToOrder'] = $cust->getMiniAmountToOrder($cur);
            // I18N::formatNumber($customer->getMiniAmountToOrder($currency));
        }
        else { 
            // Donnera l'indication qu'il ne faut pas afficher l'info, 
            // ni effectuer de controle
            $result['MiniAmountToOrder'] = -1;
        }
        
        // données sur les produits
        $result['ProductData'] = array();
        $count = $pdtCol->getCount();
        for ($i=0; $i<$count; $i++) {
            $pdt = $pdtCol->getItem($i);
            $pdtType = $pdt->getProductType();
            $tva = $spc->getHasTVA()?$pdt->getTVA():false;
            $promo = $pdt->getPromotion($cust);
            $result['ProductData'][] = array(
                'Id' => $pdt->getId(),
                'Reference' => ($pdt->getReferenceByActor($cust) != '')?
                        $pdt->getReferenceByActor($cust):$pdt->getBaseReference(),
                'Name' => $pdt->getName(),
                'Type' => $pdtType instanceof ProductType?$pdtType->getName():'N/A',
                'BaseHT' => $pdt->getUnitHTForCustomerPrice($cust),
                'PriceHT' => $pdt->getUnitHTForCustomerPriceWithDiscount($cust),
                'TVA' => $tva instanceof TVA?$tva->getRate():0,
                // pour que python ne caste pas la valeur en bool
                'Promotion' => false==$promo?0:$promo->getRate(),
                'UBNumber' => $pdt->getNumberUBInUV()
            );
        }
        return $result;
    }

    // }}}
    // OnlogisticsXmlRpcServer::saveAll() {{{

    /**
     * Enregistre la commande en base etc...
     * @param $params de la forme:
     *      array('IncotermId'=>..., 'WishedStartDate'=>...)
     *      array('164'=>array('qty'=>6, 'hdg'=>'10%', 'pht'=>'3.50'),
     *            '5000'=>array('qty'=>12, 'hdg'=>'1/6', 'pht'=>'5.60'))
     * Params a fournir pour la commande:
     * 'IncotermId', 'WishedStartDate', 'WishedEndDate', 'Comment',
     * 'ExpeditorId', 'DestinatorId', 'CommercialId',
     * 'ExpeditorSiteId', 'DestinatorSiteId', 'Handing', 'Port', 'Packing'
     * 'Insurance', 'Installment', 'GlobalHanding'
     * Params a fournir pour chaque CommandItem:
     * [pdtId]=>array('qty'=>[Qte cmdee], 'hdg'=>[remise], 'pht'=>[prix unit HT]
     * pht peut prend en compte: le prix de base, une promo, une remise,
     * et une remise globale a la commande entiere
     *
     * Possibilite de dumper les req. SQL executees en mettant $debug a true
     * @access protected
     * @return object XML_RPC_Response
     **/
    protected function saveAll($method, $params){
        $this->auth();
        $this->log('OnlogisticsXmlRpcServer::saveAll called');
        $paramsArray = $params[0];
        $cmdItemArray = $params[1];
        $paramsArray['Customer'] = $this->auth->getActorId();
        require_once('CommandManager.php');
        require_once('Objects/Command.php');
        // La transaction innodb est initiee dans le constructeur
        $manager = new CommandManager(array(
            'CommandType'        => 'ProductCommand',
            'ProductCommandType' => Command::TYPE_CUSTOMER,
            'UseTransaction'     => true
        ));
        $result = $manager->createCommand($paramsArray);
        if (Tools::isException($result)) {
            return $result->getMessage();
        }
        foreach($cmdItemArray as $pdtId => $pdtDatas) {
            // le product
            $pdtMapper = Mapper::singleton('Product');
            $pdt = $pdtMapper->load(array('Id' => $pdtId));
            if (!($pdt instanceof Product)) {
                continue;  // huh... la bdd est désyncronisée avec le client
            }
            // Tableau des paramètres; note: Pour promo et tva:
            // geres par CommandManager::_checkAndFormatCommandItemParams()
            $cmiParams = array(
                'Product' => $pdtId,
                'Quantity' => $pdtDatas['qty'],
                'Handing' => $pdtDatas['hdg'],
                'PriceHT' => $pdtDatas['pht']
            );
            $result = $manager->addCommandItem($cmiParams);
            if (Tools::isException($result)) {
                return $result->getMessage();
            }
        }
        $ach = $manager->activateProcess();
        if (Tools::isException($ach)) {
            return $ach->getMessage();
        }
        $result = $manager->validateCommand($debug);
        if (Tools::isException($result)) {
            return $result->getMessage();
        }
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::getServiceConfig() {{{

    /**
     * Retourne le tableau associatif des variables de configuration du service
     * onlogistics-biztrack.
     * Le tableau est une liste de tuples de la forme:
     * {
     *    {'section1.option1', 'valeur1'},
     *    {'section1.option2', 'valeur2'},
     *    {'section2.option1', 1},
     * }
     *
     * @access protected
     * @return array
     */
    protected function getServiceConfig($method) {
        $this->auth();
        $this->log('onlogistics_biztrack.getServiceConfig() called');
        return array(
            // chemin vers le répertoire d'échange
            array('general/exchange_dir', 'c:\\temp'),
            // nom du fichier trame de la base biztrack
            array('general/biztrackdb_filename', 'biztrack.dat'),
            // update de la conf du service toutes les 4 heures
            array('general/autoconf_interval', 60), //3600*4),
            // update de la base de données toutes les 4 heures
            array('general/dbupdate_interval', 60),
            // les fichiers trames et leurs callback correpondants
            array('monitored_files/test.dat', 'handleTest')
        );
    }

    // }}}
    // OnlogisticsXmlRpcServer::getBiztrackDatabase() {{{

    /**
     * Retourne les données de la base produit onlogistics au format "trame"
     * imposé par biztrack.
     * Format de la "trame":
     * TODO
     *
     * @access protected
     * @return string
     */
    protected function getBiztrackDatabase($method) {
        $this->auth();
        $this->log('onlogistics_biztrack.getBiztrackDatabase() called');
        // TODO
        return 'BIZTRACK DATABASE CONTENTS...';
    }

    // }}}
    // OnlogisticsXmlRpcServer::handleTest() {{{

    /**
     * Exemple de methode callback pour les fichiers monitorés par
     * onlogistics_biztrack.
     *
     * @access protected
     * @return boolean
     */
    protected function handleTest($method, $params) {
        $this->auth();
        $this->log('onlogistics_biztrack.handleTest() called');
        $file_contents = $params[0];
        MailTools::send(
            'david@ateor.com',
            'test onlogistics_biztrack',
            $file_contents
        );
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::handleValidationScan() {{{

    /**
     * Gére les scans renvoyés par les opérateurs équipés de scanners bluetooth
     * baracoda.
     *
     * @access protected
     * @return boolean
     */
    protected function handleValidationScan($method, $params) {
        require_once('AlertSender.php');
        require_once('Objects/ActivatedChainTask.php');
        require_once('ProductionTaskValidationTools.php');
        $this->auth();
        $this->log('btscan.handleValidationScan() called');
        list($date, $userID, $action, $ackID) = $params[0];

        // on ne doit pas tenir compte du dernier chiffre de $ackID
        // correspondant au checkDigit du code barre généré par fpdf
        $ackID = substr($ackID, 0, strlen($ackID)-1);
        // idem pour les scan d'user
        $userID = substr($userID, 0, strlen($userID)-1);

        $errors = array();
        // chargement de l'utilisateur
        $uacMapper = Mapper::singleton('UserAccount');
        $user   = $uacSer->load(array('Id'=>(int)$userID));
        if (!($user instanceof UserAccount)) {
            $noUser = true;
            $user   = $uacSer->load(array('Id'=>ROOT_USERID));
        }
        // gestion des actions
        if ($action == ACTION_SUSPEND_ALL) {
            // on appelle la fonction handleTaskAction avec ACTION_STOP pour
            // mettre en pause toutes les taches en cours de l'utilisateur
            $filter = getValidationTaskFilter(true);
            $ackCol = $user->getValidatedActivatedChainTaskCollection($filter);
            $count  = $ackCol->getCount();
            for ($i=0; $i<$count; $i++) {
                try {
                    $ret = handleTaskAction(ACTION_STOP, $ack, $user, $date);
                } catch (Exception $e) {
                    $ret = $e;
                }
                if (Tools::isException($ret)) {
                    $errors[] = $ret->getMessage();
                }
                if (isset($noUser)) {
                    $exc = handleTaskActionException(E_NOUSER, $ack);
                    $errors[] = $exc->getMessage();
                }
            }
        } else {
            // chargement de la tache
            $ackMapper = Mapper::singleton('ActivatedChainTask');
            $ack = $ackMapper->load(array('Id'=>(int)$ackID));
            if (!($ack instanceof ActivatedChainTask)) {
                $msg = _('Cannot find task number ') . $ackID;
                AlertSender::send_ALERT_PRODUCTION_TASK_VALIDATION(
                    $msg, $date, $user->getIdentity());
                return false;
            }
            // autres actions ici
            try {
                $ret = handleTaskAction($action, $ack, $user, $date);
            } catch (Exception $e) {
                $ret = $e;
            }
            if (Tools::isException($ret)) {
                $errors[] = $ret->getMessage();
            }
            if (isset($noUser)) {
                $exc = handleTaskActionException(E_NOUSER, $ack);
                $errors[] = $exc->getMessage();
            }
        }
        // gestion des alertes sur les erreurs
        if (count($errors) > 0) {
            foreach($errors as $msg) {
                $alert = AlertSender::send_ALERT_PRODUCTION_TASK_VALIDATION(
                    $msg, $date, $user->getIdentity()
                );
            }
            return false;
        }
        return true;
    }

    // }}}
    // OnlogisticsXmlRpcServer::auth() {{{

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
    public function auth($login=false, $realm=false, $passwd=false, $pf=false)
    {
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

?>
