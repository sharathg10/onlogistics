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

require_once('XML/Util.php');
require_once('Execution/ExecutionDataProvider.php');
require_once('Objects/ActivatedMovement.php');
require_once('Objects/ActorView.php');
require_once('Objects/Command.php');
require_once('Objects/DocumentModelProperty.inc.php');
require_once('Objects/TVA.inc.php');

define('XMLDOC_VERSION', '1.0');
define('XMLDOC_ENCODING', 'iso-8859-1');
define('DTD_NAME', 'execution.dtd');

if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', 0);
}


/**
 * Class for render movement execution data objects to xml
 */
class MovementExecutionDataXMLRenderer {
    /**
     * Constructor
     *
     * @access protected
     */
    function MovementExecutionDataXMLRenderer($user, $cmdnumber = false)
    {
        $dataprovider = new MovementExecutionDataProvider($user, $cmdnumber);
        $this->_user = $user;
        $this->_commands = $dataprovider->execute();
    }

    /**
     * MovementExecutionDataXMLRenderer::getDTDDeclaration()
     *
     * @access public
     * @return void
     **/
    function getDTDDeclaration(){
        return '<!DOCTYPE command SYSTEM "ExecutionData.dtd">';
    }

    function renderCustomBloc($document, $filter=array(), $order=array())
    {
        $documentModel = $document->findDocumentModel();
        $dompropcol = $documentModel->getDocumentModelPropertycollection(
                        $filter, $order);
        $count = $dompropcol->getCount();
        $xml = XML_Util::createStartElement('custombloc');
        for($i=0 ; $i<$count ; $i++) {
            $property = $dompropcol->getItem($i);
            $xml .= XML_Util::createTag('item',
                    array('label' => getDocumentModelPropertyCellLabel(
                            $property->getPropertyType()),
                    'value' => getDocumentModelPropertyCellValue(
                            $property->getPropertyType(), $document)));
        }
        $xml .= XML_Util::createEndElement('custombloc');

        $xml .= XML_Util::createTag('footer', array(), $documentModel->getFooter());
        $xml .= XML_Util::createTag('logo', array(), $document->getLogo());
        $xml .= XML_Util::createTag('number', array(), $documentModel->getNumber());
        return $xml;
    }

    function renderCustomDesignation($documentModel, $product,
                $filter=array(), $order=array())
    {
        $dompropcol = $documentModel->getDocumentModelPropertyCollection($filter, $order);
        $count = $dompropcol->getCount();

        $xml = XML_Util::createStartElement('custombloc');
        for($j=0 ; $j<$count ; $j++) {
            $domProperty = $dompropcol->getItem($j);
            $property = $domProperty->getProperty();
            $xml .= XML_Util::createTag('item',
                     array('label' => '',
                     'value' => Tools::getValueFromMacro($product, '%' . $property->getName() . '%')));
        }
        $xml .= XML_Util::createEndElement('custombloc');
        return $xml;
    }

    /**
     * Retourne les infos de réf., type et quantité d'UV, pour le produit en 
     * fonction du type de commande.
     * 
     * @access public
     * @param object Product
     * @param object Command
     * @return array  
     */
    function getProductInfos($product, $command) {
        $infos = array();
        if ($command->getType() == Command::TYPE_SUPPLIER) {
            $ref   = $product->getReferenceByActor($expeditor);
            $ref   = empty($ref)?$product->getBaseReference():$ref;
            $ut    = $product->getBuyUnitType();
            $utID  = $ut?$ut->getId():$product->getSellUnitTypeId();
            $utQty = $product->getBuyUnitQuantity();
            $utQty = $utQty?$utQty:$product->getSellUnitQuantity();
        } else {
            $ref   = $product->getBaseReference();
            $utID  = $product->getSellUnitTypeId();
            $utQty = $product->getSellUnitQuantity();
        }
        return array(
            'Reference'    => $ref,
            'UnitType'     => $utID,
            'UnitQuantity' => $utQty
        );
    }

    /**
     *
     * @access public
     * @return string the xml string
     */
    function render()
    {
        // mappers utilisés plus bas
        $lpqMapper = Mapper::singleton('LocationProductQuantities');
        $lcpMapper = Mapper::singleton('LocationConcreteProduct');
        $commands = array();
        foreach($this->_commands as $key=>$command) {
            $command = $this->_commands[$key];
            // sert à déterminer si un des produits de la commande est avec mode de suivi 
            $hasSNLot = 0; 

            $incoterm = $command->getIncoterm();

            $xmldoc  = XML_Util::getXMLDeclaration(XMLDOC_VERSION, XMLDOC_ENCODING);
            $xmldoc .= $this->getDTDDeclaration();
            $xmldoc .= XML_Util::createStartElement('command');

            $xmldoc .= XML_Util::createTag('id', array(), $command->getId());
            $xmldoc .= XML_Util::createTag('state', array(), 0);
            $xmldoc .= XML_Util::createTag('date', array(), $command->getCommandDate());
            $xmldoc .= XML_Util::createTag('number', array(), $command->getCommandNo());
            $xmldoc .= XML_Util::createTag('comment', array(), $command->getComment());
            $xmldoc .= XML_Util::createTag('incoterm', array(), $incoterm->toString());
            $xmldoc .= XML_Util::createTag('wishedstartdate', array(), $command->getWishedStartDate());
            $xmldoc .= XML_Util::createTag('merchandisevalue', array(), $command->getMerchandiseValue());
            $xmldoc .= XML_Util::createTag('additionnalgaranties', array(), $command->getAdditionnalGaranties());
            $xmldoc .= XML_Util::createTag('handing', array(), $command->getHanding());
            $xmldoc .= XML_Util::createTag('port', array(), $command->getPort());
            $xmldoc .= XML_Util::createTag('packing', array(), $command->getPacking());
            $xmldoc .= XML_Util::createTag('insurance', array(), $command->getInsurance());
            $xmldoc .= XML_Util::createTag('porttva', array(), getTVARateByCategory(TVA::TYPE_DELIVERY_EXPENSES));
            $xmldoc .= XML_Util::createTag('packingtva', array(), getTVARateByCategory(TVA::TYPE_PACKING));
            $xmldoc .= XML_Util::createTag('insurancetva', array(), getTVARateByCategory(TVA::TYPE_INSURANCE));
            $xmldoc .= XML_Util::createTag('totalht', array(), $command->getTotalPriceHT());
            $xmldoc .= XML_Util::createTag('totalttc', array(), $command->getTotalPriceTTC());

            $ach = $command->getActivatedChain();
            if (!($ach instanceof ActivatedChain)) {
                continue;
            }
            if ($ach->hasBLEditionTask()) {
                $xmldoc .= XML_Util::createTag('printbl', array(),1);
            }
            if ($ach->hasInvoiceEditionTask()) {
                $xmldoc .= XML_Util::createTag('printinvoice', array(), 1);
            }
            if ($ach->hasDirectionnalLabelEditionTask()) {
                $xmldoc .= XML_Util::createTag('printdirectionnallabel', array(), 1);
            }
            if ($ach->hasProductLabelEditionTask()) {
                $xmldoc .= XML_Util::createTag('printproductlabel', array(), 1);
            }

            //pour la facture
            $cur = $command->getCurrency();
            $curSymbol = $cur instanceof Currency?TextTools::entityDecode($cur->getSymbol()):'?';
            $xmldoc .= XML_Util::createTag('currency', array(), $curSymbol);

            $xmldoc .= XML_Util::createStartElement('invoice');
            $xmldoc .= XML_Util::createTag('totalpriceht', array(), 0);
            $xmldoc .= XML_Util::createTag('totalpricettc', array(), 0);
            $xmldoc .= XML_Util::createTag('topay', array(), 0);
            $xmldoc .= XML_Util::createEndElement('invoice');

            $destinator = new ActorView($command->GetDestinatorID());
            $xmldoc .= XML_Util::createStartElement('destinator');

            $xmldoc .= XML_Util::createTag('id', array(), $destinator->getId());
            $xmldoc .= XML_Util::createTag('name', array(), $destinator->getName());
            $xmldoc .= XML_Util::createTag('quality', array(),
                $destinator->getQualityForAddress());
            $xmldoc .= XML_Util::createTag('paymentcondition', array(), $destinator->getPaymentCondition());

            $xmldoc .= XML_Util::createStartElement('deliveryaddress');
            $xmldoc .= XML_Util::createTag('streettype', array(), $destinator->getStreetType('delivery'));
            $xmldoc .= XML_Util::createTag('streetnumber', array(), $destinator->getStreetNumber('delivery'));
            $xmldoc .= XML_Util::createTag('streetname', array(), $destinator->getStreetName('delivery'));
            $xmldoc .= XML_Util::createTag('complement', array(), $destinator->getStreetAddons('delivery'));
            $xmldoc .= XML_Util::createTag('cedex', array(), $destinator->getCedex('delivery'));
            $xmldoc .= XML_Util::createTag('zipcode', array(), $destinator->getZipCode('delivery'));
            $xmldoc .= XML_Util::createTag('cityname', array(), $destinator->getCityName('delivery'));
            $xmldoc .= XML_Util::createTag('countryname', array(), $destinator->getCountryName('delivery'));
            $xmldoc .= XML_Util::createEndElement('deliveryaddress');

            $xmldoc .= XML_Util::createStartElement('invoiceaddress');
            $xmldoc .= XML_Util::createTag('streettype', array(), $destinator->getStreetType('invoice'));
            $xmldoc .= XML_Util::createTag('streetnumber', array(), $destinator->getStreetNumber('invoice'));
            $xmldoc .= XML_Util::createTag('streetname', array(), $destinator->getStreetName('invoice'));
            $xmldoc .= XML_Util::createTag('complement', array(), $destinator->getStreetAddons('invoice'));
            $xmldoc .= XML_Util::createTag('cedex', array(), $destinator->getCedex('invoice'));
            $xmldoc .= XML_Util::createTag('zipcode', array(), $destinator->getZipCode('invoice'));
            $xmldoc .= XML_Util::createTag('cityname', array(), $destinator->getCityName('invoice'));
            $xmldoc .= XML_Util::createTag('countryname', array(), $destinator->getCountryName('invoice'));
            $xmldoc .= XML_Util::createEndElement('invoiceaddress');

            $destSite = $command->getDestinatorSite();
            if ($destSite instanceof Site) {
                $xmldoc .= XML_Util::createStartElement('site');
                $xmldoc .= XML_Util::createTag('id', array(), $destSite->getId());
                $xmldoc .= XML_Util::createTag('name', array(), $destSite->getName());
                $xmldoc .= XML_Util::createTag('phone', array(), $destSite->getPhone());
                $contactName = 'N/A';
                $contactCol = $destSite->getContactCollection(array(), array('Id'=>SORT_ASC));
                $nbContact = $contactCol->getCount();
                if($nbContact > 0) {
                    $contact = $contactCol->getItem(0);
                    $contactName = $contact->getName();
                }
                $xmldoc .= XML_Util::createTag('contact', array(), $contactName);
                $addrInfos = $destSite->getAddressInfos();    
                $xmldoc .= XML_Util::createStartElement('address');
                $xmldoc .= XML_Util::createTag('streettype', array(), $addrInfos['StreetType']);
                $xmldoc .= XML_Util::createTag('streetnumber', array(), $addrInfos['StreetNumber']);
                $xmldoc .= XML_Util::createTag('streetname', array(), $addrInfos['StreetName']);
                $xmldoc .= XML_Util::createTag('complement', array(), $addrInfos['StreetAddons']);
                $xmldoc .= XML_Util::createTag('cedex', array(), $addrInfos['StreetAddons']);
                $xmldoc .= XML_Util::createTag('zipcode', array(), $addrInfos['Zip']);
                $xmldoc .= XML_Util::createTag('cityname', array(), $addrInfos['CityName']);
                $xmldoc .= XML_Util::createTag('cedex', array(), $addrInfos['Cedex']);
                $xmldoc .= XML_Util::createTag('countryname', array(), $addrInfos['Country']);
                $xmldoc .= XML_Util::createEndElement('address');
                $xmldoc .= XML_Util::createEndElement('site');
            }

            $xmldoc .= XML_Util::createEndElement('destinator');

            $expeditor = new ActorView($command->getExpeditorID());

            $xmldoc .= XML_Util::createStartElement('expeditor');

            $xmldoc .= XML_Util::createTag('id', array(), $expeditor->getId());
            $xmldoc .= XML_Util::createTag('name', array(), $expeditor->getName());
            $xmldoc .= XML_Util::createTag('quality', array(),
                $expeditor->getQualityForAddress());

            $xmldoc .= XML_Util::createTag('paymentcondition', array(), $expeditor->getPaymentCondition());

            $xmldoc .= XML_Util::createStartElement('deliveryaddress');
            $xmldoc .= XML_Util::createTag('streettype', array(), $expeditor->getStreetType('delivery'));
            $xmldoc .= XML_Util::createTag('streetnumber', array(), $expeditor->getStreetNumber('delivery'));
            $xmldoc .= XML_Util::createTag('streetname', array(), $expeditor->getStreetName('delivery'));
            $xmldoc .= XML_Util::createTag('complement', array(), $expeditor->getStreetAddons('delivery'));
            $xmldoc .= XML_Util::createTag('cedex', array(), $expeditor->getCedex('delivery'));
            $xmldoc .= XML_Util::createTag('zipcode', array(), $expeditor->getZipCode('delivery'));
            $xmldoc .= XML_Util::createTag('cityname', array(), $expeditor->getCityName('delivery'));
            $xmldoc .= XML_Util::createTag('cedex', array(), $expeditor->getCedex('delivery'));
            $xmldoc .= XML_Util::createTag('countryname', array(), $expeditor->getCountryName('delivery'));
            $xmldoc .= XML_Util::createEndElement('deliveryaddress');

            $xmldoc .= XML_Util::createStartElement('invoiceaddress');
            $xmldoc .= XML_Util::createTag('streettype', array(), $expeditor->getStreetType('invoice'));
            $xmldoc .= XML_Util::createTag('streetnumber', array(), $expeditor->getStreetNumber('invoice'));
            $xmldoc .= XML_Util::createTag('streetname', array(), $expeditor->getStreetName('invoice'));
            $xmldoc .= XML_Util::createTag('complement', array(), $expeditor->getStreetAddons('invoice'));
            $xmldoc .= XML_Util::createTag('cedex', array(), $expeditor->getCedex('invoice'));
            $xmldoc .= XML_Util::createTag('zipcode', array(), $expeditor->getZipCode('invoice'));
            $xmldoc .= XML_Util::createTag('cityname', array(), $expeditor->getCityName('invoice'));
            $xmldoc .= XML_Util::createTag('cedex', array(), $expeditor->getCedex('invoice'));
            $xmldoc .= XML_Util::createTag('countryname', array(), $expeditor->getCountryName('invoice'));
            $xmldoc .= XML_Util::createEndElement('invoiceaddress');

            $expSite = $command->getExpeditorSite();
            if ($expSite instanceof Site) {
                $xmldoc .= XML_Util::createStartElement('site');
                $xmldoc .= XML_Util::createTag('id', array(), $expSite->getId());
                $xmldoc .= XML_Util::createTag('name', array(), $expSite->getName());
                $xmldoc .= XML_Util::createTag('phone', array(), $expSite->getPhone());
                $contactName = 'N/A';
                $contactCol = $expSite->getContactCollection(array(), array('Id'=>SORT_ASC));
                $nbContact = $contactCol->getCount();
                if($nbContact > 0) {
                    $contact = $contactCol->getItem(0);
                    $contactName = $contact->getName();
                }
                $xmldoc .= XML_Util::createTag('contact', array(), $contactName);
                $addrInfos = $expSite->getAddressInfos();    
                $xmldoc .= XML_Util::createStartElement('address');
                $xmldoc .= XML_Util::createTag('streettype', array(), $addrInfos['StreetType']);
                $xmldoc .= XML_Util::createTag('streetnumber', array(), $addrInfos['StreetNumber']);
                $xmldoc .= XML_Util::createTag('streetname', array(), $addrInfos['StreetName']);
                $xmldoc .= XML_Util::createTag('complement', array(), $addrInfos['StreetAddons']);
                $xmldoc .= XML_Util::createTag('cedex', array(), $addrInfos['StreetAddons']);
                $xmldoc .= XML_Util::createTag('zipcode', array(), $addrInfos['Zip']);
                $xmldoc .= XML_Util::createTag('cityname', array(), $addrInfos['CityName']);
                $xmldoc .= XML_Util::createTag('cedex', array(), $addrInfos['Cedex']);
                $xmldoc .= XML_Util::createTag('countryname', array(), $addrInfos['Country']);
                $xmldoc .= XML_Util::createEndElement('address');
                $xmldoc .= XML_Util::createEndElement('site');
            }
            $xmldoc .= XML_Util::createEndElement('expeditor');

            // pour la facture :
            $supCust = $command->getSupplierCustomer();
            $supplier = $supCust->getSupplier();
            $xmldoc .= XML_Util::createStartElement('suppliercustomer');
            /*$aBankDetail = $command->getActorBankDetail();
            if (!Tools::isEmptyObject($aBankDetail)) {
                $xmldoc .= XML_Util::createStartElement('actorbankdetail');
                $xmldoc .= XML_Util::createtag('iban', array(), $aBankDetail->getIban());
                $xmldoc .= XML_Util::createtag('bankname', array(),
                                               $aBankDetail->getBankName());
                $xmldoc .= XML_Util::createTag('swift', array(), $aBankDetail->getSwift());
                $xmldoc .= XML_Util::createTag('bankaddressno', array(),
                                               $aBankDetail->getBankAddressNo());
                $xmldoc .= XML_Util::createTag('bankaddressstreettype', array(),
                                               $aBankDetail->getBankAddressStreetType());
                $xmldoc .= XML_Util::createTag('bankaddressstreet', array(),
                                               $aBankDetail->getBankAddressStreet());
                $xmldoc .= XML_Util::createTag('bankaddressadd', array(),
                                               $aBankDetail->getBankAddressAdd());
                $xmldoc .= XML_Util::createTag('bankaddresscity', array(),
                                               $aBankDetail->getBankAddressCity());
                $xmldoc .= XML_Util::createTag('bankaddresszipcode', array(),
                                               $aBankDetail->getBankAddressZipCode());
                $xmldoc .= XML_Util::createTag('bankaddresscountry', array(),
                                               $aBankDetail->getBankAddressCountry());
                $xmldoc .= XML_Util::createTag('accountnumber', array(),
                                               $aBankDetail->getAccountNumber());
                $xmldoc .= XML_Util::createEndElement('actorbankdetail');
            }*/

            $xmldoc .= XML_Util::createTag('modality', array(),
                                           $supCust->getModality());
            $xmldoc .= XML_Util::createtag('totaldays', array(),
                                           $supCust->getTotalDays());
            $xmldoc .= XML_Util::createTag('option', array(), $supCust->getOption());
            $xmldoc .= XML_Util::createTag('hastva', array(), $supCust->getHasTVA());

            $customer = $supCust->getCustomer();
            $xmldoc .= XML_Util::createTag('customerremexcep', array(),
                                           $customer->getRemExcep());
            $xmldoc .= XML_Util::createEndElement('suppliercustomer');
            /**
             * Personnalistion des documents
             **/
            // BL
            $do = Object::load('DeliveryOrder');
            $do->setCommand($command);
            $dodm = $do->findDocumentModel();
            $do->setDocumentModel($dodm);

            // gestion de la personnalisation du tableau des infos sur la commande pour le bl
            $xmldoc .= XML_Util::createStartElement('deliveryorderdocumentmodel');
            $xmldoc .= $this->renderCustomBloc($do, array('Property' => 0), array('Order' => SORT_ASC));
            $xmldoc .= XML_Util::createEndElement('deliveryorderdocumentmodel');


            // Facture
            $iv = Object::load('Invoice');
            $iv->setCommand($command);
            $ivdm = $iv->findDocumentModel();
            $iv->setDocumentModel($ivdm);

            // gestion de la personnalisation du tableau des infos sur la commande pour la facture
            $xmldoc .= XML_Util::createStartElement('invoicedocumentmodel');
            $xmldoc .= $this->renderCustomBloc($iv, array('Property' => 0), array('Order' => SORT_ASC));
            $xmldoc .= XML_Util::createEndElement('invoicedocumentmodel');

            $commandItems = $command->getCommandItemCollection();
            // ajout du n° d'expedition à la commande
            $ackEdition = $ach->hasTaskOfType(TASK_ETIQUETTE_DIRECTION);
            if($ackEdition instanceof ActivatedChaintask) {
                $expeditionNo = $ackEdition->getId() . date('dmY');
                $xmldoc .= XML_Util::createTag('expeditionno', array(), $expeditionNo);
            }
            
            // gestion des commandItems
            for($i = 0; $i < $commandItems->getCount(); $i++) {
                $cmi = $commandItems->getItem($i);

                $product = $cmi->getProduct();
                if (!($product instanceof Product)) {
                    // le commanditem n'a pas de product ?? on passe au suivant
                    continue;
                }
                $tva = $product->getTVA();
                if(!($tva instanceof TVA)) {
                    $tva = Object::load('TVA');
                }
                $acm = $cmi->getActivatedMovement();
                if (!($acm instanceof ActivatedMovement)) {
                    // le commanditem n'a pas d'acm ?? on passe au suivant
                    continue;
                }
                if ($acm->GetState() != ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT &&
                    $acm->GetState() != ActivatedMovement::CREE) {
                    continue;
                }
                $movementType = $acm->getType();
                $xmldoc .= XML_Util::createStartElement('commanditem');
                $xmldoc .= XML_Util::createTag('id', array(), $cmi->getId());

                /*  Si reprise de partiel  */
                if ($acm instanceof ActivatedMovement &&
                    $acm -> GetState() == ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT) {
                    $exm = $acm->getExecutedmovement();
                    if ($exm instanceof ExecutedMovement) {
                        $RealProduct = $exm->GetRealProduct();
                        if ($RealProduct instanceof Product &&
                            !($product->GetId() == $RealProduct->GetId())) {
                            //  Si reprise de partiel avec une nouvelle reference
                            $xmldoc .= XML_Util::createTag('initialproductname', 
                                array(), $product->getName());
                            $infos = $this->getProductInfos($product, $command);
                            $xmldoc .= XML_Util::createTag('initialproductbasereference', 
                                array(), $infos['Reference']);
                            $xmldoc .= XML_Util::createTag('initialsellunitquantity', 
                                array(), $infos['UnitQuantity']);

                            unset($product, $infos);
                            $product = $RealProduct;
                        }
                    }
                }

                $xmldoc .= XML_Util::createTag('productname', array(), $product->getName());
                $xmldoc .= XML_Util::createTag('producttracingmode', array(), $product->getTracingMode());

                $infos = $this->getProductInfos($product, $command);
                $xmldoc .= XML_Util::createTag('productbasereference', array(),
                    $infos['Reference']);
                $xmldoc .= XML_Util::createTag('sellunittype', array(),
                    $infos['UnitType']);
                $xmldoc .= XML_Util::createTag('sellunitquantity', array(),
                    $infos['UnitQuantity']);
                $xmldoc .= XML_Util::createTag('productweight', array(), $product->getSellUnitWeight());

                // XXX FIXME récupérer le prix du produit pour facture Zaurus
                // il faut regarder dans PriceByCurrency
                $xmldoc .= XML_Util::createTag('priceht', array(), $cmi->getPriceHT());
                $xmldoc .= XML_Util::createTag('pricettc', array(), '0');

                $xmldoc .= XML_Util::createTag('tvarate', array(), $tva->getRate());
                $xmldoc .= XML_Util::createTag('handing', array(), $cmi->getHanding());
                /*  Infos pour calculer le nbre de colis (Product->PackagingUnitNumber($quantity))
                    packagingnumbercoefficient: a multiplier par le Nbre de SellUnitType et
                    arrondir a l'entier superieur
                */
                $unitCount = $product->PackagingUnitNumber(1, 1);
                if (Tools::isException($unitCount)) {
                    $unitCount = 1;
                }
                $xmldoc .= XML_Util::createTag('packagingnumbercoefficient', array(),
                                               $unitCount);
                $xmldoc .= XML_Util::createTag('quantity', array(),
                                               $cmi->getQuantity());
                $xmldoc .= XML_Util::createTag('totalht', array(),
                                  $cmi->getQuantity() * $product->getPrice());
                $xmldoc .= XML_Util::createTag('totalttc', array(),
                                  $cmi->getQuantity() * $product->getPriceTTC());

                if ($acm instanceof ActivatedMovement) {
                    $xmldoc .= XML_Util::createStartElement('activatedmovement');

                    $xmldoc .= XML_Util::createTag('id', array(), $acm->getId());
                    $xmldoc .= XML_Util::createTag('type', array(), $movementType->getId());
                    $xmldoc .= XML_Util::createTag('state', array(), $acm->getState());
                    $xmldoc .= XML_Util::createTag('startdate', array(), $acm->getStartDate());
                    $xmldoc .= XML_Util::createTag('enddate', array(), $acm->getEndDate());
                    /*  Qte restant a mouvementer ds tous les cas     */
                    if (isset($exm) && $exm instanceof ExecutedMovement) {
                        $newproduct = $exm->getRealProduct();
                        if ($newproduct instanceof Product) {
                            $infos = $this->getProductInfos($newproduct, $command);
                            $xmldoc .= XML_Util::createTag('reference', array(), $infos['Reference']);
                            $xmldoc .= XML_Util::createTag('quantity', array(), $cmi->getQuantity() - $exm->GetRealQuantity());
                        }
                    }
                    else {
                        $xmldoc .= XML_Util::createTag('reference', array(), $infos['Reference']);
                        $xmldoc .= XML_Util::createTag('quantity', array(), $cmi->getQuantity());
                    }

                    $lpqCollection = $lpqMapper->loadCollection(
                        array(
                            'Product' => $cmi->getProductID(),
                            'Activated' => 1,
                            'Location.Store.StorageSite.Owner.Id' => $this->_user->getActorID()
                        )
                    );
                    if ($lpqCollection instanceof Collection) {
                        for($j = 0; $j < $lpqCollection->getCount(); $j++){
                            $lpq = $lpqCollection->getItem($j);
                            $location = $lpq->getLocation();
                            $xmldoc .= XML_Util::createStartElement('location');
                            $xmldoc .= XML_Util::createTag('id', array(), $location->getId());
                            $xmldoc .= XML_Util::createTag('reference', array(), $location->getName());
                            $xmldoc .= XML_Util::createTag('storeid', array(), $location->getStoreId());
                            $xmldoc .= XML_Util::createTag('quantity', array(), $lpq->getRealQuantity());
                            $lcpStr = '';
                            if ($product->getTracingMode() > 0) {
                                $filter = array(
                                    'ConcreteProduct'=>$product->getConcreteProductCollectionIds(),
                                    'Location'=>$location->getId()
                                );
                                $lcpCol = $lcpMapper->loadCollection($filter); 
                                $lcpColCount = $lcpCol->getCount(); 
                                $padding = '';
                                for ($k=0; $k<$lcpColCount; $k++) {
                                    $lcp = $lcpCol->getItem($k);
                                    $cp  = $lcp->getConcreteProduct();
                                    $lcpStr .= $padding . sprintf(
                                        '%s:%s:%s', 
                                        $cp->getId(), 
                                        $cp->getSerialNumber(), 
                                        $lcp->getQuantity()
                                    );
                                    $padding = '|';
                                }
                                $hasSNLot = 1;
                            }
                            $xmldoc .= XML_Util::createTag('cpmapstring', array(), $lcpStr);
                            $xmldoc .= XML_Util::createEndElement('location');
                            unset($lpq, $location);
                        }
                    }
                    unset($lpqCollection);
                    $xmldoc .= XML_Util::createEndElement('activatedmovement');
                }
                /* créer le noeud custombloc pour personnaliser le champ designation
                        des infos sur la commande
                    */
                // pour le BL
                $xmldoc .= XML_Util::createStartElement('deliveryorderdocumentmodel');
                $xmldoc .= $this->renderCustomDesignation($dodm, $product, array('PropertyType'=>0), array('Order'=>SORT_ASC));
                $xmldoc .= XML_Util::createEndElement('deliveryorderdocumentmodel');
                // pour l'invoice
                $xmldoc .= XML_Util::createStartElement('invoicedocumentmodel');
                $xmldoc .= $this->renderCustomDesignation($ivdm, $product, array('PropertyType'=>0), array('Order'=>SORT_ASC));
                $xmldoc .= XML_Util::createEndElement('invoicedocumentmodel');

                $xmldoc .= XML_Util::createEndElement('commanditem');

                unset($tva, $cmi, $product, $acm, $exm);
            }

            $xmldoc .= XML_Util::createTag('hassnlot', array(), $hasSNLot);
            $xmldoc .= XML_Util::createEndElement('command');

            /**
             * Flush the xml string to file
             **/
            $commands[] = $xmldoc;
            unset($command, $commandItems);
        }
        return $commands;
    }
}

function debug($string){
    if(DEBUG_MODE == true){
        printf(">> %s: %s\n", date('F j, Y, g:i a'), $string);
    }
}

?>
