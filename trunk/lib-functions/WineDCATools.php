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
 * @version   SVN: $Id: GridActionDownloadUploadedDocument.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */


function generateDCA($itemsIds) {

    if( ! is_array($itemsIds)) return FALSE ;
    // an array where we will stock LEMids selected for the csvs
    $DCA_LEMids = array();

    // Array templates for csv {{{
    $header_template = array(
        'CODESOC' =>  '', 'SITE'  => '', 'NUMFACTURE' => '',
        'DATE FACTURE' => '', 'CODE MOUVEMENT' => 'S', 
        'NATURE DOCUMENT' => 'DCA', 'TRANSFERTVENTE' => '2', 
        'ABREG EXPEDITEUR' => '', 'DEST LIBELLE' => '',
        'DEST ADDR1' => '', 'DEST ADDR2' => '', 'DEST ADDR3' => '',
        'DEST CODE POSTAL' => '', 'DEST VILLE' => '', 'DEST PAYS' => '',
        'LIV LIBELLE' => '', 'LIV ADDR1' => '', 'LIV ADDR2' => '',
        'LIV ADDR3' => '', 'LIV CODE POSTAL' => '', 'LIV VILLE' => '',
        'LIV PAYS' => '', 'NUMTVADEST' => '', 'NUMACCISEDEST' => '',
        'TPS LIBELLE' => '', 'TPS ADDR1' => '', 'TPS ADDR2' => '',
        'TPS ADDR3' => '', 'TPS CODE POSTAL' => '', 'TPS VILLE' => '',
        'TPS PAYS' => '', 'INFO COMPLEMENTAIRE' => '', 'DATE DOC' => '', 
        'CRD' => '0');

    $details_template = array( 
        'NUMFACTURE' => '', 'CODEART' => '', 'NBR CONDITIONNEMENT' => '', 
        'CODE EMBALLAGE' => '', 'CONTENANCE' => '', 'QUANTITE' => '', 
        'VOL EN LITRE' => '', 'TOTAL NET' => '', 'TOTAL BRUT' => '');
    // }}}
    // Generate arrays for csv files , looping on each selected LEM {{{
    while( list($itemKey, $itemId) = each($itemsIds)) {

        $LEMMapper = Mapper::singleton('WineLocationExecutedMovement');
        $LEM = $LEMMapper->load(array('Id' => $itemId )) ;

        // On ne peut pas exporter un LEM deja exporte dans un DCA
        if ($LEM instanceof WineLocationExecutedMovement && $LEM->DCA == FALSE)  {

            $DeliveryOrder = $LEM->getDeliveryOrder() ;
            $ForwardingForm = $LEM->getForwardingForm();

            // Cet export ne concerne que les mouvements liés à un document 
            // BL  ou BE
            if ( $DeliveryOrder instanceof DeliveryOrder || $ForwardingForm instanceof ForwardingForm ) {

                $ptype = $LEM->Product->getProductType();
                if ($ptype instanceof ProductType) {
                    $genericPType = $ptype->getGenericProductType();
                    $pdtProperties = Product::getPropertiesByContext();
                    $dynproperties = $ptype->getPropertyArray();
                    $properties = array();
                    foreach($dynproperties as $name=>$property){
                        $dname = $property->getDisplayName();
                        $getter = 'get' . $name;
                        $value = $LEM->Product->$getter();
                        $properties[$name] = $value ;
                    }
                }
                // Cet export ne concerne que les produits non CRD
                if( (!isset($properties['Winetaxes'])) || 
                    ( isset($properties['Winetaxes']) && $properties['Winetaxes'] != "O") ) {         
                    $DCA_LEMids[] = $LEM->Id ;
                
                    // {{{ Generate Header line for DCA Export
                    // {{{ Settings from Forwarding form ...
                    if ($ForwardingForm instanceof ForwardingForm) {
                        $docNo = $ForwardingForm->DocumentNo ;
                        $docDate = $ForwardingForm->EditionDate;
                        $Supplier = $ForwardingForm->getSupplierCustomer()->getSupplier();
                        $Customer = $ForwardingForm->getSupplierCustomer()->getCustomer();

                        $Expeditor = $Supplier ;
                        $ExpeditorSites = $Expeditor->getSiteCollection();
                        $ExpeditorSite= $ExpeditorSites->getItem(0);

                        $Destinator = $Customer ;
                        $DestinatorSite = $ForwardingForm->getDestinatorSite();

                        $Transporter = $ForwardingForm->getTransporter();
                        if($Transporter instanceof Actor ) {
                            $TransporterSites = $Transporter->getSiteCollection();
                            $TransporterSite = $TransporterSites->getItem(0);
                            $TransporterSiteAddress = $TransporterSite->getAddressInfos();
                        }
                    }
                    // }}}
                    // {{{ Settings from DeliveryOrder ...
                    if ($DeliveryOrder instanceof DeliveryOrder) {
                        $docNo = $DeliveryOrder->DocumentNo ;
                        $docDate = $DeliveryOrder->EditionDate;
                        $Order = $LEM->getExecutedMovement()->getActivatedMovement()->getProductCommand();
                        $Supplier = $Order->getSupplierCustomer()->getSupplier();
                        $Customer = $Order->getCustomer();
                        $Expeditor = $Order->getExpeditor();
                        $ExpeditorSite = $Order->getExpeditorSite();
                        $Destinator = $Order->getDestinator();
                        $DestinatorSite = $Order->getDestinatorSite();
                    }
                    // }}}
                    // {{{ Define header array for a csv line
                    $curId = $docNo ;
                    $ExpeditorSite = $LEM->Location->Store->getStorageSite();
                    
                    $CustomerSiteCol = $Customer->getSiteCollection();
                    $CustomerMainSite = $CustomerSiteCol->getItem(0);

                    $CustomerMainSiteAddress = $CustomerMainSite->getAddressInfos();
                    $DestinatorSiteAddress = $DestinatorSite->getAddressInfos();
                    $ExpeditorSiteAddress = $ExpeditorSite->getAddressInfos();

                    $hdr_line[$curId] = $header_template ;
                    $hdr_line[$curId]['CODESOC'] = $Supplier->Code ;
                    $hdr_line[$curId]['SITE'] = $ExpeditorSite->Name ;
                    $hdr_line[$curId]['NUMFACTURE'] = $docNo ;
                    $hdr_line[$curId]['DATE FACTURE'] = $docDate ; 
                    $hdr_line[$curId]['ABREG EXPEDITEUR'] = $Supplier->Code ;
                    $hdr_line[$curId]['DEST LIBELLE'] = $Destinator->Name ;
                    $hdr_line[$curId]['DEST ADDR1'] = $DestinatorSiteAddress['StreetNumber']
                        . " " . $DestinatorSiteAddress['StreetType']
                        . " " . $DestinatorSiteAddress['StreetName'];
                    $hdr_line[$curId]['DEST ADDR2'] = $DestinatorSiteAddress['StreetAddons'];
                    $hdr_line[$curId]['DEST CODE POSTAL'] = $DestinatorSiteAddress['Zip'];
                    $hdr_line[$curId]['DEST VILLE'] = $DestinatorSiteAddress['CityName'];
                    $hdr_line[$curId]['DEST PAYS'] = $DestinatorSite->getCountry()->getInterCountryCode();
                    $hdr_line[$curId]['NUMTVADEST'] = $Customer->getTVA();
                    $hdr_line[$curId]['DATE DOC'] = date("Ymd");
                    // }}}
                    // {{{ Delivery Address != Billing Address
                    // Si le site du client n'est pas celui de destination ...
                    if($CustomerMainSite->getId() != $DestinatorSite->Id ) { 
                        $hdr_line[$curId]['LIV LIBELLE'] = $DestinatorSite->Name ;
                        $hdr_line[$curId]['LIV ADDR1'] = $DestinatorSiteAddress['StreetNumber']
                                . " " . $DestinatorSiteAddress['StreetType']
                                . " " . $DestinatorSiteAddress['StreetName'];
                        $hdr_line[$curId]['LIV ADDR2'] = $DestinatorSiteAddress['StreetAddons'];
                        $hdr_line[$curId]['LIV CODE POSTAL'] = $DestinatorSiteAddress['Zip'];
                        $hdr_line[$curId]['LIV VILLE'] = $DestinatorSiteAddress['CityName'] ;
                        $hdr_line[$curId]['LIV PAYS'] = $DestinatorSite->getCountry()->getInterCountryCode();
                    } 
                    // }}}
                // {{{ Transporter Informations exists
                // Si des infos transporteurs sont presentes .
                if(isset($TransporterSite)) {
                    $hdr_line[$curId]['TPS LIBELLE'] = $TransporterSite->Name ;
                    $hdr_line[$curId]['TPS ADDR1'] = $TransporterorSiteAddress['StreetNumber']
                        . " " . $TransporterSiteAddress['StreetType']
                        . " " . $TransporterSiteAddress['StreetName'];
                    $hdr_line[$curId]['TPS ADDR2'] = $TransporterorSiteAddress['StreetAddons'] ;
                    $hdr_line[$curId]['TPS CODE POSTAL'] = $TransporterorSiteAddress['Zip'] ;
                    $hdr_line[$curId]['TPS VILLE'] = $TransporterorSiteAddress['CityName'] ;
                    $hdr_line[$curId]['TPS PAYS'] = $Transporteror->getCountry()->getInterCountryCode();

                    unset($Transporter, $TransporterSite , $TransporterSiteAddress);
                }
                // }}} 
                    // }}}    
                    // {{{ Generate Details line for DCA Export
                    $ProductProperties = $LEM->Product->ProductType->getPropertyArray();
                    $dtl_line[$curId][$LEM->Product->Id] = $details_template ;
                    $dtl_line[$curId][$LEM->Product->Id]['NUMFACTURE'] = $docNo ;
                    $dtl_line[$curId][$LEM->Product->Id]['CODEART'] = $LEM->Product->BaseReference ;
                    $dtl_line[$curId][$LEM->Product->Id]['NBR CONDITIONNEMENT'] = $LEM->Quantity ;
                    $dtl_line[$curId][$LEM->Product->Id]['CODE EMBALLAGE'] = '' ;
                    $dtl_line[$curId][$LEM->Product->Id]['CONTENANCE'] = $LEM->Product->Volume ;
                    $dtl_line[$curId][$LEM->Product->Id]['QUANTITE'] =  $LEM->Quantity ;
                    $dtl_line[$curId][$LEM->Product->Id]['VOL EN LITRE'] = 
                        $ProductProperties['Volume']->getValue($LEM->Product->Id) * $LEM->Quantity ;
                    $dtl_line[$curId][$LEM->Product->Id]['TOTAL NET'] = '' ;
                    $dtl_line[$curId][$LEM->Product->Id]['TOTAL BRUT'] = '' ;
                    // }}}    
                    // Purge loaded objects ... {{{
                    unset($DeliveryOrder,
                        $Customer, $Supplier, $Order, 
                        $CustomerSiteCol, $CustomerMainSite, $CustomerMainSiteAddess, 
                        $Expeditor, $ExpeditorSite, $ExpeditorSiteAddress,
                        $Destinator, $DestinatorSite, $DestinatorSiteAddress, $properties);
                    // }}}
                } 
            }
        }
    }
    // }}}
    // Render csv files {{{
    $DCA_header = FALSE ;
    $DCA_details = FALSE ;
    if (empty($DCA_LEMids)) return FALSE ;

    while (list($k,$v)=each($hdr_line)) {
        $DCA_header .= implode(";", $v)."\n";
    }

    while (list($k,$v)=each($dtl_line)) {
        while (list($ki,$vi)=each($v)) {
            $DCA_details .= implode(";", $vi)."\n";
        }
    }
    // }}}
    return array($DCA_header, $DCA_details, $DCA_LEMids) ;
} 

function generateDCAPeriodical() {

    // Array templates for csv {{{
    $periodic_template = array(
        'CODESOC' => '1', 'CODEREG' => '1', 'SITE' => '',
        'DATE' => '', 'TYPE' => '' , 'CODEMVT' => '',
        'CRD' => '', 'TRANSFERTVENTE' => '', 'NUMDOC' => '',
        'EXPED' => '', 'CPOSTEXP' => '', 'COMMUNEEXP' => '', 'PAYSEXP' => '',
        'DEST' => '', 'CPOSTDEST' => '', 'COMMUNEDEST' => '', 'PAYSDEST' => '',
        'CODEART' => '' , 'NBCOLS' => '', 'CONTENANCE' => '',
        'VOLUMEFLACON' => '', 'VOLUMECERCLE' => '', 'NBKG' => '');
    // }}}
    // Find LocationExecutedMovements according to dates  {{{
    // and not previously exported ...  
    $startDate = DateTimeTools::quickFormDateToMySQL('startDate');
    $endDate = DateTimeTools::quickFormDateToMySQL('endDate');
    // Construction du filtre pour la selection des LEM
    $filterCmpnt = array();
    $filterCmpnt[] = SearchTools::newFilterComponent('Date', '', 
        'GreaterThanOrEquals', $startDate, 1);
    $filterCmpnt[] = SearchTools::newFilterComponent('Date', '',
        'LowerThanOrEquals', $endDate, 1);
    $filterCmpnt[] = SearchTools::newFilterComponent('DCA', '',
        'Equals', 0, 1);
    $filterCmpnt[] = SearchTools::newFilterComponent('ExecutedMovement.Type', 
        '', 'In', array(
            SORTIE_NORMALE, SORTIE_INTERNE, ENTREE_NORMALE, ENTREE_INTERNE), 
        1);
    $FilterComponentArray[] = SearchTools::filterAssembler($filterCmpnt, FilterComponent::OPERATOR_AND);
    $Filter = SearchTools::filterAssembler($FilterComponentArray);

    // On charge la Collec
    $LEMMapper = Mapper::singleton('LocationExecutedMovement');
    $LEMCollection = $LEMMapper->LoadCollection($Filter);

    // Si vide on degage ...
    if (Tools::isEmptyObject($LEMCollection))  {
        return FALSE ;
    }
    // }}}
    // Generate array for csv file , looping on each selected LEM {{{
    for($i = 0; $i < $LEMCollection->getCount(); $i++){
        $LEM = $LEMCollection->getItem($i);
        $MovementType = $LEM->ExecutedMovement->Type->getEntrieExit() ;
        $ProductId = $LEM->Product->getId();

        $ptype = $LEM->Product->getProductType();
        if ($ptype instanceof ProductType) {
            $genericPType = $ptype->getGenericProductType();
            $pdtProperties = Product::getPropertiesByContext();
            $dynproperties = $ptype->getPropertyArray();
            $properties = array();
            foreach($dynproperties as $name=>$property){
                $dname = $property->getDisplayName();
                $getter = 'get' . $name;
                $value = $LEM->Product->$getter();
                $properties[$name] = $value ;
            }
        }

        if(!isset($DCAPeriodical[$MovementType][$ProductId])) {
            $DCAPeriodical[$MovementType][$ProductId] = $periodic_template ;
            $DCAPeriodical[$MovementType][$ProductId]['CODESOC'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['CODEREG'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['SITE'] = $LEM->Location->Store->StorageSite->Name ;
            $DCAPeriodical[$MovementType][$ProductId]['DATE'] = $LEM->Date ;
            $DCAPeriodical[$MovementType][$ProductId]['CODEMVT'] = 'VTE' ;
            $DCAPeriodical[$MovementType][$ProductId]['CRD'] = $properties['Winetaxes'] ;
            $DCAPeriodical[$MovementType][$ProductId]['TRANSFERTVENTE'] = 'VTE' ;
            $DCAPeriodical[$MovementType][$ProductId]['CODEMVT'] = 'VTE' ;

            if($MovementType == 1 ) {
                // Sortie
                $DCAPeriodical[$MovementType][$ProductId]['TYPE'] = 'S' ;
                $DCAPeriodical[$MovementType][$ProductId]['EXPED'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['CPOSTEXP'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['COMMUNEEXP'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['PAYSEXP'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['DEST'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['CPOSTDEST'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['COMMUNEDEST'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['PAYSDEST'] = '' ;
            } else { 
                // Entree
                $DCAPeriodical[$MovementType][$ProductId]['TYPE'] = 'E' ;
                $DCAPeriodical[$MovementType][$ProductId]['EXPED'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['CPOSTEXP'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['COMMUNEEXP'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['PAYSEXP'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['DEST'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['CPOSTDEST'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['COMMUNEDEST'] = '' ;
                $DCAPeriodical[$MovementType][$ProductId]['PAYSDEST'] = '' ;
            }
            $DCAPeriodical[$MovementType][$ProductId]['CODEART'] = $LEM->Product->BaseReference ;
            $DCAPeriodical[$MovementType][$ProductId]['NBCOLS'] = $LEM->Quantity ;
            $DCAPeriodical[$MovementType][$ProductId]['CONTENANCE'] = $LEM->Product->Volume ;
            $DCAPeriodical[$MovementType][$ProductId]['VOLUMEFLACON'] = $LEM->Quantity * $LEM->Product->Volume ;
            $DCAPeriodical[$MovementType][$ProductId]['CUMULE'] = "N";
        } else {
            $DCAPeriodical[$MovementType][$ProductId]['EXPED'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['CPOSTEXP'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['COMMUNEEXP'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['PAYSEXP'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['DEST'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['CPOSTDEST'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['COMMUNEDEST'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['PAYSDEST'] = '' ;
            $DCAPeriodical[$MovementType][$ProductId]['NBCOLS'] += $LEM->Quantity ;
            $DCAPeriodical[$MovementType][$ProductId]['VOLUMEFLACON'] += $LEM->Quantity * $LEM->Product->Volume ;
            $DCAPeriodical[$MovementType][$ProductId]['CUMULE'] = "O";
        }
        $DCA_LEMids[] = $LEM->Id ;
    }
    unset($LEMCollection);

    // }}}
    // Render csv files {{{
    $DCAPeriodicalCSV = "" ;
    while (list($k,$v)=each($DCAPeriodical)) {
        while (list($l,$w)=each($v)) {
            $DCAPeriodicalCSV .= implode(";", $w)."\n";
        }
    }
    // }}}
    return array($DCAPeriodicalCSV, $DCA_LEMids) ;

}

function updateLEMDCA($LEMids, $DCA , $DCAType) {

    while( list($itemKey, $itemId) = each($LEMids)) {
        $LEMMapper = Mapper::singleton('WineLocationExecutedMovement');
        $LEMup = $LEMMapper->load(array('Id' => $itemId )) ;
        if ($LEMup instanceof WineLocationExecutedMovement && $LEMup->DCA == FALSE)  {
            $LEMup->setDCA($DCA) ;
            $LEMup->setDCAType($DCAType) ;
            $LEMup->save();
        }
        unset($LEMup);
    }
}

?>
