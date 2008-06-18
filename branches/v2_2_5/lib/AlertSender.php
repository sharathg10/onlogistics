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

require_once('Objects/Alert.const.php');

class AlertSender{
    // properties {{{

    public static $stockAlerts = array(
        ALERT_STOCK_QV_MINI       => 'send_ALERT_STOCK_QV_MINI',
        ALERT_STOCK_QR_MINI       => 'send_ALERT_STOCK_QR_MINI',
        ALERT_STOCK_QV_REACH_ZERO => 'send_ALERT_STOCK_QV_REACH_ZERO',
        ALERT_STOCK_QR_REACH_ZERO => 'send_ALERT_STOCK_QR_REACH_ZERO',
        ALERT_INSUFFICIENT_STOCK  => 'send_ALERT_INSUFFICIENT_STOCK'
    );

    // }}}
    // send_ALERT_STOCK_QV_MINI() {{{

    /**
     *
     * @access public
     * @static
     * @param object Product
     * @return void
     **/
    public static function send_ALERT_STOCK_QV_MINI($pdt, $siteIds=array()){
        $alert = self::_loadAlert(ALERT_STOCK_QV_MINI);
        $params = array(
            'ProductBaseReference' => $pdt->getBaseReference(),
            'ProductMinimumStock' => $pdt->getSellUnitMinimumStoredQuantity(),
            'ProductName' => $pdt->getName(),
            'ProductSupplierName'=>Tools::getValueFromMacro($pdt, '%MainSupplier.Name%')
        );
        $alert->prepare($params);
        $filter = self::getFilterForUserAccount($alert, $siteIds, false, $pdt);
        self::_send($alert, false, false, $filter);
    }

    // }}}
    // send_ALERT_STOCK_QR_MINI() {{{

    /**
     *
     * @access public
     * @static
     * @param object Product
     * @return void
     **/
    public static function send_ALERT_STOCK_QR_MINI($pdt, $siteIds=array()){
        $alert = self::_loadAlert(ALERT_STOCK_QR_MINI);
        $params = array(
            'ProductBaseReference' => $pdt->getBaseReference(),
            'ProductMinimumStock' => $pdt->getSellUnitMinimumStoredQuantity(),
            'ProductName' => $pdt->getName(),
            'ProductSupplierName'=>Tools::getValueFromMacro($pdt, '%MainSupplier.Name%')
        );
        $alert->prepare($params);
        $filter = self::getFilterForUserAccount($alert, $siteIds, false, $pdt);
        self::_send($alert, false, false, $filter);
    }

    // }}}
    // send_ALERT_INSUFFICIENT_STOCK() {{{

    /**
     *
     * @access public
     * @static
     * @param object Product
     * @return void
     **/
    public static function send_ALERT_INSUFFICIENT_STOCK($pdt, $command=false){
        $alert = self::_loadAlert(ALERT_INSUFFICIENT_STOCK);
        $params = array(
            'ProductBaseReference' => $pdt->getBaseReference(),
            'ProductMinimumStock' => $pdt->getSellUnitMinimumStoredQuantity(),
            'ProductName' => $pdt->getName(),
            'ProductSupplierName'=>Tools::getValueFromMacro($pdt, '%MainSupplier.Name%'),
            'NumCde'=> $command instanceof Command?$command->getCommandNo():''
        );
        $alert->prepare($params);
        $filter = self::getFilterForUserAccount($alert, array(), $command);
        self::_send($alert, false, false, $filter);
    }

    // }}}
    // send_ALERT_STOCK_QV_REACH_ZERO() {{{

    /**
     *
     * @access public
     * @static
     * @param object Product
     * @return void
     **/
    public static function send_ALERT_STOCK_QV_REACH_ZERO($pdt, $siteIds=array()){
        $alert = self::_loadAlert(ALERT_STOCK_QV_REACH_ZERO);
        $params = array(
            'ProductBaseReference' => $pdt->getBaseReference(),
            'ProductName' => $pdt->getName(),
            'ProductSupplierName'=>Tools::getValueFromMacro($pdt, '%MainSupplier.Name%')
        );
        $alert->prepare($params);
        $filter = self::getFilterForUserAccount($alert, $siteIds, false, $pdt);
        self::_send($alert, false, false, $filter);
    }

    // }}}
    // send_ALERT_STOCK_QR_REACH_ZERO() {{{

    /**
     *
     * @access public
     * @static
     * @param object Product
     * @return void
     **/
    public static function send_ALERT_STOCK_QR_REACH_ZERO($pdt, $siteIds=array()) {
        $alert = self::_loadAlert(ALERT_STOCK_QR_REACH_ZERO);
        $params = array(
            'ProductBaseReference' => $pdt->getBaseReference(),
            'ProductName' => $pdt->getName(),
            'ProductSupplierName'=>Tools::getValueFromMacro($pdt, '%MainSupplier.Name%')
        );
        $alert->prepare($params);
        $filter = self::getFilterForUserAccount($alert, $siteIds, false, $pdt);
        self::_send($alert, false, false, $filter);
    }

    // }}}
    // send_ALERT_PARTIAL_ENTRY() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_PARTIAL_ENTRY(){
        $alert = self::_loadAlert(ALERT_PARTIAL_ENTRY);

    }

    // }}}
    // send_ALERT_PARTIAL_EXIT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_PARTIAL_EXIT(){
        $alert = self::_loadAlert(ALERT_PARTIAL_EXIT);

    }

    // }}}
    // send_ALERT_CLIENT_COMMAND_INCUR_OVER() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_CLIENT_COMMAND_INCUR_OVER($cmd, $curStr, $siteIds=array()){
        // le client
        $actor = $cmd->getDestinator();
        // le couple client/fournisseur
        $spc = $cmd->getSupplierCustomer();
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_CLIENT_COMMAND_INCUR_OVER);
        $alert->prepare(
            array(
                'Numcde'=>$cmd->getCommandNo(),
                'ActorName'=>$actor->getname(),
                'Currency'=>TextTools::entityDecode($curStr),
                'MaximumIncurse'=>$spc->getMaxIncur(),
                'UpdateIncurseWithCommand'=>$spc->GetUpdateIncur()
                    + $cmd->GetTotalPriceTTC()
            )
        );
        $filter = self::getFilterForUserAccount($alert, $siteIds);
        $alert->send(false, false, $filter);
    }

    // }}}
    // send_ALERT_SUPPLIER_COMMAND_INCUR_OVER() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_SUPPLIER_COMMAND_INCUR_OVER($cmd, $curStr, $siteIds){
        // le fournisseur
        $actor = $cmd->getExpeditor();
        // le couple client/fournisseur
        $spc = $cmd->getSupplierCustomer();
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_SUPPLIER_COMMAND_INCUR_OVER);
        $alert->prepare(
            array(
                'Numcde'=>$cmd->getCommandNo(),
                'ActorName'=>$actor->getname(),
                'Currency'=>TextTools::entityDecode($curStr),
                'MaximumIncurse'=>$spc->getMaxIncur(),
                'UpdateIncurseWithCommand'=>$spc->GetUpdateIncur()
                    + $cmd->GetTotalPriceTTC()
            )
        );
        $filter = self::getFilterForUserAccount($alert, $siteIds);
        $alert->send(false, false, $filter);
    }

    // }}}
    // send_ALERT_CLIENT_INVOICE_INCUR_OVER() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_CLIENT_INVOICE_INCUR_OVER($command, $sc, $invoice){
        $alert = self::_loadAlert(ALERT_CLIENT_INVOICE_INCUR_OVER);
        $destinator = $command->getDestinator();
        $params = array(
            'CommandNumber' => $command->getCommandNo(),
            'InvoiceNumber' => $invoice->getDocumentNo(),
            'CustomerName' => $destinator->getname(),
            'MaximumIncurse' => $sc->getMaxIncur(),
            'UpdateIncurseWithCommand' => $sc->getUpdateIncur(),
        );
        $alert->prepare($params);
        self::_send($alert);
    }

    // }}}
    // send_ALERT_SUPPLIER_INVOICE_INCUR_OVER() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_SUPPLIER_INVOICE_INCUR_OVER(){
        $alert = self::_loadAlert(ALERT_SUPPLIER_INVOICE_INCUR_OVER);

    }

    // }}}
    // send_ALERT_CLIENT_COMMAND_RECEIPT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_CLIENT_COMMAND_RECEIPT($params, $uacCol, $siteIds=array()){
        $alert = self::_loadAlert(ALERT_CLIENT_COMMAND_RECEIPT);
        $alert->prepare($params);
        $filter = self::getFilterForUserAccount($alert, $siteIds);
        self::_send($alert, $uacCol, true, $filter);
    }

    // }}}
    // send_ALERT_SUPPLIER_COMMAND_RECEIPT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_SUPPLIER_COMMAND_RECEIPT($params, $uacCol, $siteIds=array()){
        $alert = self::_loadAlert(ALERT_SUPPLIER_COMMAND_RECEIPT);
        $alert->prepare($params);
        $filter = self::getFilterForUserAccount($alert, $siteIds);
        self::_send($alert, $uacCol, true, $filter);
    }

    // }}}
    // send_ALERT_CLIENT_LATE_PAYMENT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_CLIENT_LATE_PAYMENT($actorName, $body){
        $alert = self::_loadAlert(ALERT_CLIENT_LATE_PAYMENT);
        $params = array('CustomerName' => $actorName, 'body' => $body);
        $alert->prepare($params);
        self::_send($alert);
    }

    // }}}
    // send_ALERT_SUPPLIER_LATE_PAYMENT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_SUPPLIER_LATE_PAYMENT(){
        $alert = self::_loadAlert(ALERT_SUPPLIER_LATE_PAYMENT);

    }

    // }}}
    // send_ALERT_STOCK() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_STOCK(){
        $alert = self::_loadAlert(ALERT_STOCK);

    }

    // }}}
    // send_ALERT_PARTIAL_MOVEMENT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_PARTIAL_MOVEMENT(){
        $alert = self::_loadAlert(ALERT_PARTIAL_MOVEMENT);

    }

    // }}}
    // send_ALERT_CHAIN_COMMAND_RECEIPT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_CHAIN_COMMAND_RECEIPT($cmd, $toPay, $additionnalUsers){
        require_once('Objects/Command.const.php');
        require_once('Objects/CommandItem.inc.php');
        require_once('FormatNumber.php');

        $alert = self::_loadAlert(ALERT_CHAIN_COMMAND_RECEIPT);
        $exp      = $cmd->getExpeditor();
        $expSite  = $cmd->getExpeditorSite();
        $expInfo  = $expSite->getAddressInfos();

        $dest     = $cmd->getDestinator();
        $destSite = $cmd->getDestinatorSite();
        $destInfo = $destSite->getAddressInfos();

        $params = array();

        // customer de la commande
        $cust = $cmd->getCustomer();
        $params['customerName'] = $cust instanceof Actor?$cust->getName():_('N/A');

        // données expéditeur
        $params['expeditorName'] = $exp->getName();
        $params['expeditorStreetNo'] = $expInfo['StreetNumber'];
        $params['expeditorStreetType'] = $expInfo['StreetType'];
        $params['expeditorStreetName'] = $expInfo['StreetName'];
        $params['expeditorStreetAddons'] = $expInfo['StreetAddons'];
        $params['expeditorZip'] = $expInfo['Zip'];
        $params['expeditorCity'] = $expInfo['CityName']. ' ' .$expInfo['Cedex'];
        $params['expeditorCountry'] = $expInfo['Country'];
        $expPhone = $expSite->getPhone();
        $expFax   = $expSite->getFax();
        $params['expeditorPhone'] = !empty($expPhone)?_('Tel.').':' . $expPhone:'';

        // données destinataire
        $params['destinatorName'] = $dest->getName();
        $params['destinatorStreetNo'] = $destInfo['StreetNumber'];
        $params['destinatorStreetType'] = $destInfo['StreetType'];
        $params['destinatorStreetName'] = $destInfo['StreetName'];
        $params['destinatorStreetAddons'] = $destInfo['StreetAddons'];
        $params['destinatorZip'] = $destInfo['Zip'];
        $params['destinatorCity'] = $destInfo['CityName']. ' ' .$destInfo['Cedex'];
        $params['destinatorCountry'] = $destInfo['Country'];
        $destPhone = $destSite->getPhone();
        $destFax   = $destSite->getFax();
        $params['destinatorPhone'] = !empty($destPhone)?_('Tel.').':' . $destPhone:'';
        // items
        $params['commandContent'] = "";
        $cmiCollection = $cmd->getCommandItemCollection();
        $count = $cmiCollection->getCount();
        for($i = 0; $i < $count; $i++){
            $cmi = $cmiCollection->getItem($i);
            $type = $cmi->getCoverType();
            $params['commandContent'] .= sprintf(
                "<tr><td>%s</td><td>%s</td><td>%s</td><td>%sx%sx%s</td>" .
                "<td>%s</td><td>%s</td></tr>\n",
                $type instanceof CoverType?$type->toString():'',
                $cmi->getQuantity(),
                $cmi->getWeight(),
                $cmi->getWidth(),
                $cmi->getLength(),
                $cmi->getHeight(),
                $cmi->getGerbability(),
                getMasterDimensionLabel($cmi->getMasterDimension()));
        }
        // Incoterm
        $incoterm = $cmd->getIncoterm();
        $params['commandIncoterm'] = $incoterm instanceof Incoterm?
            $incoterm->toString():'N/A';
        // date et date souhaitée
        $params['commandDate'] = I18N::formatDate($cmd->getCommandDate());
        $startDate = I18N::formatDate($cmd->getWishedStartDate());
        $endDate = $cmd->getWishedEndDate();
        $endDate = $endDate==0?false:I18N::formatDate($endDate);
        $type = $cmd->getDateType()==DATE_TYPE_DELIVERY?
            _('for delivery'):_('of collection');
        $params['commandDateType'] = $type;
        $params['commandWishedDate'] = $endDate?
            sprintf(_("between %s and %s"), $startDate, $endDate):$startDate;
        // N° de commande
        $params['commandCommandNo'] = $cmd->getCommandNo();
        // N° d'imputation
        $inputationNo = $cmd->getInputationNo();
        $params['commandInputationNo'] = $inputationNo?$inputationNo:_('N/A');
        // montant à récupérer à la livraison
        $deliveryPayment = $cmd->getDeliveryPayment();
        $params['commandDeliveryPayment'] = $deliveryPayment>0?
            I18N::formatNumber($deliveryPayment):'0';
        // frais d'assurance
        $insur = $cmd->getInsurance();
        $params['commandInsurance'] = $insur>0?I18N::formatNumber($insur):'0';
        // frais d'emballage
        $packing = $cmd->getPacking();
        $params['commandPacking'] = $packing>0?I18N::formatNumber($packing):'0';
        // Accompte
        $installment = $cmd->getInstallment();
        $params['commandInstallment'] = $installment?
            I18N::formatNumber($installment):'0';
        // Remise globale
        $handing = $cmd->getHanding();
        $params['commandHanding'] = $handing?
            I18N::formatNumber($handing):'0';
        // TVA
        $params['commandTVA'] = I18N::formatNumber($cmd->getTotalPriceTTC() -
            $cmd->getTotalPriceHT());
        // prix HT
        $params['commandTotalPriceHT'] = I18N::formatNumber($cmd->getTotalPriceHT());
        // prix TTC
        $params['commandTotalPriceTTC'] = I18N::formatNumber($cmd->getTotalPriceTTC());
        // Commentaire
        $params['commandComment'] = $cmd->getComment();
        // net à payer
        $params['commandToPay'] = $toPay; // déjà formatté
        // construction et envoi de l'alerte en html
        $alert->prepare($params);
        self::_send($alert, $additionnalUsers, true);
    }

    // }}}
    // send_ALERT_PLANIFICATION_CHAIN() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_PLANIFICATION_CHAIN($chainRef, $error){
        $alert = self::_loadAlert(ALERT_PLANIFICATION_CHAIN);
        $params = array(
            'ChainReference' => $chainRef,
            'Error' => $error
        );
        $alert->prepare($params);
        self::_send($alert, false, true);
    }

    // }}}
    // send_ALERT_COMMAND_DELETE() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_COMMAND_DELETE($commandNumber, $username){
        $alert = self::_loadAlert(ALERT_COMMAND_DELETE);
        $params = array(
            'CommandNumber' => $commandNumber,
            'UserName' => $username
        );
        $alert->prepare($params);
        self::_send($alert);
    }

    // }}}
    // send_ALERT_COURSE_COMMAND_RECEIPT() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_COURSE_COMMAND_RECEIPT($command,
        $additionnalRecipients = false){
        $alert = self::_loadAlert(ALERT_COURSE_COMMAND_RECEIPT);
        $instructor = $command->getInstructor();
        $flytype = $command->getFlyType();
        list($wisheddate, $hourbegin) = explode(' ',
            $command->getWishedStartDate());
        list($notused, $hourend) = explode(' ', $command->getWishedEndDate());
        $instructorName = $instructor instanceof AeroInstructor?
            $instructor->getName():_('Solo flight');
        $customer = $command->getCustomer();
        $params = array(
            'CommandCustomer' => $customer->getName(),
            'CommandDate' => I18N::formatDate($command->getCommandDate()),
            'CommandCommandNo' => $command->getCommandNo(),
            'CommandWishedStartDate' => I18N::formatDate(
                $command->getWishedStartDate(), I18N::DATE_LONG),
            'CommandBeginHour' => $hourbegin,
            'CommandEndHour' => $hourend,
            'CommandFlyType' => $flytype->getName(),
            'CommandInstructor' => $instructorName,
            'CommandComment' => $command->getComment(),
        );

        $alert->prepare($params);
        self::_send($alert, $additionnalRecipients);
    }

    // }}}
    // send_ALERT_REINTEGRATION_STOCK() {{{

    /**
     *
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_REINTEGRATION_STOCK(){
        $alert = self::_loadAlert(ALERT_REINTEGRATION_STOCK);

    }

    // }}}
    // send_ALERT_TEN_DAYS_SINCE_LAST_FLY() {{{

    /**
     *
     * @static
     * @access public
     * @return void
     **/
    public static function send_ALERT_TEN_DAYS_SINCE_LAST_FLY($name, $lastFly,
        $recipients = false){
        $params = array(
            'AeroCustomerName'=>$name,
            'LastFlyDate'=>I18N::formatDate($lastFly)
        );
        $alert = self::_loadAlert(ALERT_TEN_DAYS_SINCE_LAST_FLY);
        $alert->prepare($params);
        self::_send($alert, $recipients);
    }

    // }}}
    // send_ALERT_POTENTIAL_OVER() {{{

    /**
     *
     * @static
     * @access public
     * @return void
     **/
    public static function send_ALERT_POTENTIAL_OVER($ccp, $pdt, $prestations){
        $prestationsString = '';
        $count = $prestations->getCount();
        for($i=0; $i<$count; $i++){
            $prest = $prestations->getItem($i);
            $pot = $prest->getPotential()?$prest->getPotential():'0';
            $prestationsString .= sprintf("\t- %s ("._('Potential').": %s)\n",
                $prest->getName(), DateTimeTools::hundredthsOfHourToTime($pot));
        }
        $params = array(
            'Immatriculation' => $ccp->getImmatriculation(),
            'BaseReference' => $pdt!=false?$pdt->getBaseReference():'',
            'SerialNumber' => $ccp->getSerialNumber(),
            'RealHourSinceOverall' => DateTimeTools::hundredthsOfHourToTime(
                $ccp->getRealHourSinceOverall()),
            'VirtualHourSinceOverall' => DateTimeTools::hundredthsOfHourToTime(
                $ccp->getVirtualHourSinceOverall()),
            'Prestations' => $prestationsString
        );
        $alert = self::_loadAlert(ALERT_POTENTIAL_OVER);
        $alert->prepare($params);
        self::_send($alert);

    }

    // }}}
    // send_ALERT_LICENCE_OUT_OF_DATE_ADMIN() {{{

    /**
     *
     * @static
     * @param string $body
     * @access public
     * @return void
     **/
    public static function send_ALERT_LICENCE_OUT_OF_DATE_ADMIN($body) {
        $alert = self::_loadAlert(ALERT_LICENCE_OUT_OF_DATE_ADMIN);
        $alert->prepare(array('Body' => $body));
        self::_send($alert);
    }

    // }}}
    // send_ALERT_LICENCE_OUT_OF_DATE() {{{

    /**
     *
     * @static
     * @param string $body
     * @param object $Actor
     * @access public
     * @return void
     **/
    public static function send_ALERT_LICENCE_OUT_OF_DATE($body, $Actor) {
        $alert = self::_loadAlert(ALERT_LICENCE_OUT_OF_DATE);
        $alert->prepare(array('Body' => $body, 'ActorName' => $Actor->getName()));
        self::_send($alert, false, false, array('Actor' => $Actor->getId()));
    }

    // }}}
    // send_ALERT_CUSTOMER_SITUATION() {{{

    /**
     * Alerte envoyée lors du passage d'un client en alerte
     * @param object action l'action créée.
     *
     * @access public
     * @return void
     */
    public static function send_ALERT_CUSTOMER_SITUATION($action) {
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_CUSTOMER_SITUATION);
        $actor = $action->getActor();
        $commercial = $action->getCommercial();
        $date = DateTimeTools::DateExploder($action->getActionDate());
        $hour = sprintf('%s:%s:%s', $date['hour'], $date['mn'], $date['sec']);
        $date = sprintf('%s/%s/%s', $date['day'], $date['month'], $date['year']);

        $alert->prepare(
            array(
                'actorName'=>$actor->getName(),
                'userAccountName'=>$commercial->getIdentity(),
                'date'=>$date,
                'heure'=>$hour,
                'actionComment'=>$action->getComment()
            )
        );

        $destinatorsCol = new Collection();
        $destinatorsCol->setItem($commercial);

        $additionalUserProfiles = array(UserAccount::PROFILE_DIR_COMMERCIAL,
            UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        foreach ($additionalUserProfiles as $profile) {
            $userAccount = $userAccountMapper->load(
                array('Profile'=>$profile));
            if($userAccount instanceof UserAccount) {
                $destinatorsCol->setItem($userAccount);
            }
        }
        $alert->send($destinatorsCol);
    }

    // }}}
    // send_ALERT_MAX_MEETING_DATE_EXCEEDED() {{{

    /**
     * Envoie l'alerte de date limite de visite dépassée pour l'acteur.
     *
     * @param string $actor le nom de l'acteur
     * @param string $date la date
     * @param collection $destinator les destinataires du mail
     * @access public
     * @return void
     */
    public static function send_ALERT_MAX_MEETING_DATE_EXCEEDED($actor, $date, $destinator) {
        $alert = self::_loadAlert(ALERT_MAX_MEETING_DATE_EXCEEDED);
        $alert->prepare(array('actorName'=>$actor,
                        'date'=>$date));
        $alert->send($destinator);
    }

    // }}}
    // send_ALERT_PRODUCTION_TASK_VALIDATION() {{{

    /**
     * Envoie l'alerte de validation de tâche de production
     *
     * @param string $actor le nom de l'acteur
     * @param string $date la date
     * @param string , $uacName
     * @access public
     * @return void
     */
    public static function send_ALERT_PRODUCTION_TASK_VALIDATION($msg, $date, $uacName) {
        $alert = self::_loadAlert(ALERT_PRODUCTION_TASK_VALIDATION);
        $alert->prepare(
            array(
                'date'=>$date,
                'userAccountName'=>$uacName,
                'msg'=>$msg
            )
        );
        $alert->send();
    }

    // }}}
    // send_ALERT_INVOICE_TO_DOWNLOAD() {{{

    /**
     *
     * @param object $inv Invoice
     * @param object $userAccount UserAccount connecte
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_INVOICE_TO_DOWNLOAD($inv, $userAccount) {
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_INVOICE_TO_DOWNLOAD);
        $alert->prepare(
            array(
                'numInvoice' => $inv->getDocumentNo(),
                'userAccountName' => $userAccount->getIdentity(),
            )
        );
        return $alert->send(
            false,
            false,
            array('Actor' => $inv->getSupplierCustomer()->getCustomerId()),
            array(),
            true,
            $userAccount->getEmail());  // peut etre vide
    }

    // }}}
    // send_ALERT_INVOICE_BY_MAIL() {{{

    /**
     *
     * @param object $inv Invoice
     * @param object $userAccount UserAccount connecte
     * @access public
     * @static
     * @return void
     **/
    public static function send_ALERT_INVOICE_BY_MAIL($inv, $userAccount) {
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_INVOICE_BY_MAIL);
        $alert->prepare(
            array(
                'numInvoice' => $inv->getDocumentNo(),
                'userAccountName' => $userAccount->getIdentity(),
            )
        );
        $from = $userAccount->getEmail();  // peut etre vide

        require_once('GenerateDocument.php');
        $pdfContent = generateDocument($inv, 0, 'S');
        $attachment = array(
            'content' => $pdfContent,
            'contentType' => 'application/pdf',
            'fileName' => 'facture.pdf',
            'isFile' => false
        );

        return $alert->send(
            false,
            false,
            array('Actor' => $inv->getSupplierCustomer()->getCustomerId()),
            $attachment,
            true,
            $userAccount->getEmail());  // peut etre vide
    }

    // }}}
    // send_ALERT_CUSTOMER_WITHOUT_ORDER_SINCE_THIRTY_DAYS() {{{

    /**
     *
     * @param array $customers
     * @return void
     */
    public static function send_ALERT_CUSTOMER_WITHOUT_ORDER_SINCE_THIRTY_DAYS($customers) {
        if (empty($customers)) {
            return false;
        }
        $customerStr = '<ul><li>' . implode('</li><li>', $customers) . '</li></ul>';
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_CUSTOMER_WITHOUT_ORDER_SINCE_THIRTY_DAYS);
        $alert->prepare(array('customerList' => $customerStr));
        return $alert->send(false, true);
    }

    // }}}
    // send_ALERT_FORECAST_EXPENSE_OVER_THE_BORD() {{{

    public static function send_ALERT_FORECAST_EXPENSE_OVER_THE_BORD($params) {
        $alert = self::_loadAlert(ALERT_FORECAST_EXPENSE_OVER_THE_BORD);
        $alert->prepare($params);
        return $alert->send();
    }

    // }}}
    // send_ALERT_FORECAST_RECEIPT_OVER_THE_BORD() {{{

    public static function send_ALERT_FORECAST_RECEIPT_OVER_THE_BORD($params) {
        $alert = self::_loadAlert(ALERT_FORECAST_RECEIPT_OVER_THE_BORD);
        $alert->prepare($params);
        return $alert->send();
    }

    // }}}
    // send_ALERT_GED_DOCUMENT_UPLOADED() {{{

    /**
     * Alerte déclenchée quand un document est uploadé.
     *
     * @param object $doc UploadedDocument
     *
     * @access public
     * @static
     * @return void
     */
    public static function send_ALERT_GED_DOCUMENT_UPLOADED($doc) {
        // nom de l'utilisateur
        $uac = $doc->getUserAccount();
        $uacIdentity = ($uac instanceof UserAccount) ? 
            $uac->getIdentity() : _('unknown');
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_GED_DOCUMENT_UPLOADED);
        $alert->prepare(
            array(
                'name' => $doc->getName(),
                'date' => $doc->getCreationDate('localedate'),
                'user' => $uacIdentity
            )
        );
        return $alert->send();
    }

    // }}}
    // send_ALERT_GED_DOCUMENT_UPDATED() {{{

    /**
     * Alerte déclenchée quand un document est modifié.
     *
     * @param object $doc UploadedDocument
     *
     * @access public
     * @static
     * @return void
     */
    public static function send_ALERT_GED_DOCUMENT_UPDATED($doc) {
        // nom de l'utilisateur
        $uac = $doc->getUserAccount();
        $uacIdentity = ($uac instanceof UserAccount) ? 
            $uac->getIdentity() : _('unknown');
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_GED_DOCUMENT_UPDATED);
        $alert->prepare(
            array(
                'name' => $doc->getName(),
                'date' => $doc->getLastModificationDate('localedate'),
                'user' => $uacIdentity
            )
        );
        return $alert->send();
    }

    // }}}
    // send_ALERT_GED_DOCUMENT_DELETED() {{{

    /**
     * Alerte déclenchée quand un document est supprimé.
     *
     * @param object $doc UploadedDocument
     *
     * @access public
     * @static
     * @return void
     */
    public static function send_ALERT_GED_DOCUMENT_DELETED($doc) {
        // nom de l'utilisateur
        $uac = $doc->getUserAccount();
        $uacIdentity = ($uac instanceof UserAccount) ? 
            $uac->getIdentity() : _('unknown');
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_GED_DOCUMENT_DELETED);
        $alert->prepare(
            array(
                'name' => $doc->getName(),
                'date' => $doc->getLastModificationDate('localedate'),
                'user' => $uacIdentity
            )
        );
        return $alert->send();
    }

    // }}}
    // send_ALERT_GED_DOCUMENT_ASSIGNED() {{{

    /**
     * Alerte déclenchée quand un document est assigné à une tâche.
     *
     * @param object $doc UploadedDocument
     *
     * @access public
     * @static
     * @return void
     */
    public static function send_ALERT_GED_DOCUMENT_ASSIGNED($doc) {
        // nom de l'utilisateur
        $uac = $doc->getUserAccount();
        $uacIdentity = ($uac instanceof UserAccount) ? 
            $uac->getIdentity() : _('unknown');
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_GED_DOCUMENT_ASSIGNED);
        $alert->prepare(
            array(
                'name'  => $doc->getName(),
                'date'  => $doc->getCreationDate('localedate'),
                'user'  => $uacIdentity,
                'task'  => Tools::getValueFromMacro($doc, 
                    '%ActivatedChainTask.Task.Name%'),
                'order' => Tools::getValueFromMacro($doc, 
                    '%ActivatedChainTask.ActivatedOperation.' .
                    'ActivatedChain.CommandItem()[0].Command.CommandNo%')
            )
        );
        return $alert->send();
    }

    // }}}
    // send_ALERT_GED_DOCUMENT_UNASSIGNED() {{{

    /**
     * Alerte déclenchée quand un document est assigné à une tâche.
     *
     * @param object $doc UploadedDocument
     *
     * @access public
     * @static
     * @return void
     */
    public static function send_ALERT_GED_DOCUMENT_UNASSIGNED($doc) {
        // nom de l'utilisateur
        $uac = $doc->getUserAccount();
        $uacIdentity = ($uac instanceof UserAccount) ? 
            $uac->getIdentity() : _('unknown');
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_GED_DOCUMENT_UNASSIGNED);
        $alert->prepare(
            array(
                'name'  => $doc->getName(),
                'date'  => $doc->getCreationDate('localedate'),
                'user'  => $uacIdentity
            )
        );
        return $alert->send();
    }

    // }}}
    // send_ALERT_GED_ACK_OUT_OF_DATE() {{{

    /**
     * Alerte déclenchée quand une ack est en retard.
     *
     * @param integer $cmdId
     * @param mixed $data
     *
     * @access public
     * @static
     * @return void
     */
    public static function send_ALERT_GED_ACK_OUT_OF_DATE($cmdId, $data) {
        $command = Object::load('Command', $cmdId);
        if (!($command instanceof Command)) {
            return false;
        }
        $customer = $command->getDestinator();
        if (!($customer instanceof Actor)) {
            return false;
        }
        // chargement de l'alerte
        $alert = self::_loadAlert(ALERT_GED_ACK_OUT_OF_DATE);
        $alert->prepare(
            array(
                'customerName' => $customer->getName(),
                'commandNo' => $command->getCommandNo(),
                'commandContent' => $data
            )
        );
        // envoie l'alerte en html
        return $alert->send(false, true);
    }

    // }}}
    // _loadAlert() {{{

    /**
     * Méthode privée qui charge une entité alerte à partir de la constante
     * passée en paramètre. Si l'alerte ne peut pas être chargée une erreur
     * php est déclenchée.
     *
     * @access private
     * @static
     * @param integer $id la constante de l'alerte à charger
     * @return object Alert
     **/
    public static function _loadAlert($id){
        $mapper = Mapper::singleton('Alert');
        $alert = $mapper->load(array('Id' => $id), array(), true);
        if ($alert instanceof Alert && $alert->getId() > 0) {
            return $alert;
        }
        trigger_error(
            sprintf(_("Alert \"%s\" was not found in the database."), $id),
            E_USER_ERROR
        );
    }

    // }}}
    // _send() {{{

    /**
     * Méthode privée qui se charge d'envoyer l'alerte passée en paramètre.
     * Si l'alerte ne peut pas être envoyée un warning php est déclenché.
     *
     * @access private
     * @static
     * @param object Alert
     * @param $additionnalRecipients
     * @param $isHTML
     * @param array $filter: filtre supplementaire pour
     * $alert->getUserAccountCollection(): attention, un array et pas un Filter!
     * @return void
     **/
    public static function _send($alert, $additionnalRecipients = false, $isHTML = false,
        $filter = array()){
        $result = $alert->send($additionnalRecipients, $isHTML, $filter);
        if (true != $result) {
            $msg = Tools::isException($result)?$result->getMessage():'';
            trigger_error(
                sprintf(_("Alert \"%s\" cannot be sent."), $msg),
                E_USER_WARNING
            );
        }
    }

    // }}}
    // getFilterForUserAccount() {{{

    /**
     * Retourne les ids des UserAccount autorisé à recevoir l'alerte en fonction
     * des sites affectés aux UserAccount et de $siteIds, si le UserAccount n'a
     * pas de site ou si il a un des sites passé dans $siteIds il peut recevoir
     * l'alaerte.
     *
     * @param mixed $alert
     * @param mixed $siteIds
     * @access public
     * @return void
     */
    public static function getFilterForUserAccount($alert, $siteIds=array(),
        $command=false, $pdt=false)
    {
        $filter = array();
        $uacCol = $alert->getUserAccountCollection();
        $count = $uacCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $uac = $uacCol->getItem($i);
            $pf = $uac->getProfile();
            if ($pdt && $pf == UserAccount::PROFILE_OWNER_CUSTOMER
                && $uac->getActorId() != $pdt->getOwnerId())
            {
                // Alertes pour stock mini atteint ou stock atteint 0 (que ce
                // soit pour la QV ou la QR) : Si un utilisateur client
                // proprietaire a ce type d'alerte qui lui est affecte, on ne
                // lui envoie l'alerte que pour les produits dont son acteur
                // est Product_Owner
                continue;
            }
            if (($command && $alert->getId() == ALERT_INSUFFICIENT_STOCK) &&
                ($pf == UserAccount::PROFILE_CUSTOMER ||
                 $pf == UserAccount::PROFILE_OWNER_CUSTOMER) &&
                ($command->getCustomerId() != $uac->getActorId()))
            {
                // Alertes pour stock insuffisant: si un utilisateur de profil
                // client proprietaire ou client a ce type d'alerte affecte on
                // ne lui envoie l'alerte que pour les commandes dont il est
                // client (ProductCommand_Destinator)
                continue;
            }
            if (!empty($siteIds)) {
                $mainSiteId = Tools::getValueFromMacro($uac, '%Actor.MainSite.Id%');
                $uacSiteIds = $uac->getSiteCollectionIds();
                $intersect = array_intersect($uacSiteIds, $siteIds);
                if (!in_array($mainSiteId, $siteIds) 
                    && (empty($uacSiteIds) || empty($intersect)))
                {
                    continue;
                }
            }
            $filter['Id'][] = $uac->getId();
        }
        if (empty($filter)) {
            // XXX sinon il va considerer qu'il n'y a pas de filtre !
            $filter = array('Id' => 0);
        }
        return $filter;
    }

    // }}}
    // isStockalert() {{{

    /**
     * Retourne true si l'id de l'alerte passé correspond à une alerte de
     * stock.
     *
     * @param int $alertId
     * @access public
     * @return boolean
     */
    public static function isStockalert($alertId) {
        return in_array($alertId, array_keys(self::$stockAlerts));
    }

    // }}}
    // sendStockAlert() {{{

    /**
     * Envoie une alerte de stock.
     *
     * @param int $alertId
     * @param object Product $pdt
     * @param array $siteIds
     * @param object Command $command
     * @access public
     * @return boolean
     */
    public static function sendStockAlert($alertId, $pdt=false, $siteIds=array(),
        $command=false)
    {
        foreach (self::$stockAlerts as $id=>$method) {
            if ($alertId == $id) {
                if ($alertId == ALERT_INSUFFICIENT_STOCK) {
                    return self::$method($pdt, $command);
                }
                return self::$method($pdt, $siteIds);
            }
        }
        return false;
    }

    // }}}
}

?>
