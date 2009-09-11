<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * AbstractDocument class
 *
 * Class containing addon methods.
 */
class AbstractDocument extends _AbstractDocument {
    // Constructeur {{{

    /**
     * AbstractDocument::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // getLogo() {{{

    /**
     * Retourne le logo (sous forme base64), ou une string vide
     * @access public
     * @return string
     **/
    function getLogo() {
        require_once('Objects/DocumentModel.php');
        $DocumentModel = $this->GetDocumentModel();
        if (Tools::isEmptyObject($DocumentModel)) {
            return '';
        }
        $Command = $this->GetCommand();

        switch($DocumentModel->GetLogoType()) {
            case DocumentModel::EXPEDITOR:
                if (Tools::isEmptyObject($Command)) {
                    return '';
                }
                $Actor = $Command->GetExpeditor();
                break;
            case DocumentModel::DESTINATOR:
                if (Tools::isEmptyObject($Command)) {
                    return '';
                }
                $Actor = $Command->GetDestinator();
                break;
            case DocumentModel::ONE_ACTOR:
                $Actor = $DocumentModel->GetActor();
                break;
            default:
                return '';
        } // switch

        if (!Tools::isEmptyObject($Actor)) {
            $result = (is_null($Actor->GetLogo()))?'':$Actor->GetLogo();
            return $result;
        }
        return '';
    }

    // }}}
    // getFooter() {{{

    /**
     * Retourne le logo (sous forme base64), ou une string vide
     *
     * @access public
     * @return string
     */
    function getFooter() {
        $DocumentModel = $this->GetDocumentModel();
        if (Tools::isEmptyObject($DocumentModel)) {
            return '';
        }
        return $DocumentModel->GetFooter();
    }

    // }}}
    // findDocumentModel() {{{

    function findDocumentModel() {
        $docModel = $this->getDocumentModel();
        if (!Tools::isEmptyObject($docModel)) {
            return clone $docModel;  // une copie...
        }
        // On cherche d'abord si un DocumentModel est defini ds le SupplierCustomer associe
        // Sinon, on regarde si un modele par defaut est defini pour le type de document
        $DModelMapper = Mapper::singleton('DocumentModel');
        $SupplierCustomer = false;

        $Command = $this->getCommand();
        if (!Tools::isEmptyObject($Command)) {
            $SupplierCustomer = $Command->getSupplierCustomer();
        }
        elseif (method_exists($this, 'getSupplierCustomer')) {
            $SupplierCustomer = $this->getSupplierCustomer();
        }

        if (!Tools::isEmptyObject($SupplierCustomer)) {
            $DocumentModelCollection = $SupplierCustomer->getDocumentModelCollection(
                                            array('DocType' => get_class($this)));
            if (!Tools::isEmptyObject($DocumentModelCollection)) {
                $DocumentModel = $DocumentModelCollection->getItem(0);
                return $DocumentModel;
            }
        }

        /*   On regarde si un modele par defaut est defini pour le type de document  */
        $DocumentModel = $DModelMapper->load(array('DocType' => get_class($this),
                                                               'Default' => 1));
        if (!Tools::isEmptyObject($DocumentModel)) {
            return $DocumentModel;
        }
        return false;
    }

    // }}}
    // getDocumentList() {{{

    /**
     * Retourne un tableau de correspondance entre le
     * ClassName et le nom du document.
     *
     * @return array
     * @access public
     */
    function getDocumentList()
    {
        $documentlist = array(
            'CommandReceipt'         => _('Command receipt'),
            'CommandReceiptSupplier' => _('Supplier order'),
            'Invoice'                => _('Invoice'),
            'Estimate'               => _('Estimate'),
            'PackingList'            => _('Packing list'),
            'ToHave'                 => _('Credit note'),
            'DeliveryOrder'          => _('Delivery order'),
            'ForwardingForm'         => _('Forwarding form')
        );

        $context = Preferences::get('TradeContext', array());
        if(in_array('wine', $context))
            $documentlist =array_merge($documentlist, array(
                'WineDCAHeader'          => _('DCA Header'),
                'WineDCADetails'         => _('DCA Footer'),
                'WineDCAPeriodical'      => _('Periodical DCA')
            )) ;
 
        return $documentlist ;
    }

    // }}}
    // getDocumentReeditionURL() {{{

    /**
     * Retourne l'url de reedition pour le document
     * de ClassName $docType ou à défaut un tableau
     * avec les correspondance ClassName => url
     *
     * @param string $docType le ClassName
     * @return string|array
     * @access public
     */
    function getDocumentReeditionURL($docType=false)
    {
        $urls = array(
            'CommandReceipt'         => 'CommandReceiptEdit.php?print=1&id=%d',
            'CommandReceiptSupplier' => 'CommandReceiptEdit.php?print=1&id=%d',
            'Invoice'                => 'EditInvoice.php?print=1&InvoiceId=%d',
            'Estimate'               => 'EstimateEdit.php?print=1&id=%d',
            'PackingList'            => 'PackingListEdit.php?reedit=1&pId=%d',
            'ToHave'                 => 'ToHaveEdit.php?reedit=1&thId=%d',
            'DeliveryOrder'          => 'DeliveryOrderEdit.php?reedit=1&idBL=%d',
            'ForwardingForm'         => 'ForwardingFormEdit.php?print=1&reedit=1&doc=%d',
        );

        $context = Preferences::get('TradeContext', array());
        if(in_array('wine', $context))
            $urls =array_merge($urls , array(
                'WineDCAHeader'          => 'WineDCAEdit.php?print=1&reedit=1&doc=%d',
                'WineDCADetails'         => 'WineDCAEdit.php?print=1&reedit=1&doc=%d',
                'WineDCAPeriodical'      => 'WineDCAEdit.php?print=1&reedit=1&doc=%d'
            )) ;
 
        return isset($urls[$docType]) ? $urls[$docType] : false;
    }
    
    // }}}

}

?>
