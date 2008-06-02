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

class CxmlParser {
    // propriétés {{{

    public $xmlCommandPath = 'Request/OrderRequest'; // ou Message.OrderRequest
    public $xmlCommandItemPath = 'Request/OrderRequest/ItemOut';  // idem
    /*
     * L'instance de CommandParser qui a instantie $this
     * Utilise dans buildNeededData()
     **/
    public $commandParser;
    /**
     * La structure du xml pour retrouver les infos de la commande
     *
     * @var    array $commandSchema
     * @access public
     */
    public $commandSchema = array(
            'Destinator'      => array('path'=>'OrderRequestHeader/BillTo/Address/Name'),/* le Name!!! */
//            'WishedStartDate' => array('req'=>true, 'type'=>'datetime'),
//            'WishedEndDate'   => array('req'=>false, 'type'=>'datetime'),
            /* les frais de port HT: il faut que le Tax.TaxDetail|purpose vaille 'Port'... */
/* TODO: gerer l'attribut 'filter' */
            'Port'            => array(
                    'path'=>'OrderRequestHeader/Tax/TaxDetail/TaxableAmount/Money',
                    'filter'=>array(
                        'OrderRequestHeader/Tax/TaxDetail|purpose' => 'Port')),
            'Insurance'       => array(
                    'path'=>'OrderRequestHeader/Tax/TaxDetail/TaxableAmount/Money',
                    'filter'=>array(
                        'OrderRequestHeader/Tax/TaxDetail|purpose' => 'Insurance')),
            'Packing'         => array(
                    'path'=>'OrderRequestHeader/Tax/TaxDetail/TaxableAmount/Money',
                    'filter'=>array(
                        'OrderRequestHeader/Tax/TaxDetail|purpose' => 'Packing')),
            /* peut etre calculé par validateCommand() */
            'TotalPriceHT'    => array('path'=>'OrderRequestHeader/Total/Money'),
            /* peut etre calculé par validateCommand() */
//            'TotalPriceTTC'   => array('req'=>false, 'type'=>'float'),

            'DestinatorSite'  => array('path'=>'OrderRequestHeader/ShipTo/Address',
                                       'attr'=>'addressID'),
/*            'Expeditor'       => array('req'=>true, 'type'=>'int'),
            'ExpeditorSite'   => array('req'=>true, 'type'=>'int'),
            'Customer'        => array('req'=>true, 'type'=>'int'),
            'Incoterm'        => array('req'=>true, 'type'=>'int'),*/

            /* calculé par validateCommand() si ProductCommand
            'Currency'        => array('path'=>'OrderRequestHeader/Total/Money',
                                       'attr'=>'currency'), *//* le ShortName!!! */
            /* calculé par validateCommand() si ProductCommand */
//            'CustomerRemExcep'=> array('req'=>false, 'type'=>'float'),
            /* calculé par validateCommand()
            'ActorBankDetail' => array('req'=>false, 'type'=>'int'),*/
//            'Handing'         => array('req'=>false, 'type'=>'float'),
//            'Commercial'      => array('req'=>false, 'type'=>'int'),
            'CommandDate'     => array('path'=>'OrderRequestHeader',
                                       'attr'=>'orderDate'),
            'Comment' => array('path'=>'OrderRequestHeader/Comments')/*,
            Type bool non gere
            'Cadenced'   => array('req'=>false, 'type'=>'bool')*/
        );
    /**
     * La structure du xml pour retrouver les infos des lignes de commande
     *
     * @var    array $commandItemSchema
     * @access public
     */
    public $commandItemSchema = array(
            'Product'      => array('path'=>'ItemDetail/ManufacturerPartID'),
            'Quantity' => array('path'=>'', 'attr'=>'quantity'),
            'TVA' => array('path'=>'Tax/TaxDetail', 'attr'=>'category'), // Attention; pas un id!!
            'PriceHT'   => array('path'=>'ItemDetail/UnitPrice/Money'),
            'WishedDate' => array('path'=>'', 'attr'=>'requestedDeliveryDate')
        );
    // }}}
    // CommandParser::__construct() {{{

    /**
     * Constructor
     * @param object CommandParser $commandParser
     *
     * @access public
     */
    public function __construct($commandParser) {
        $this->commandParser = $commandParser;
    }

    // }}}
    // CommandParser::buildNeededData() {{{

    /**
     * Recupere le DestinatorId via un Name
     * Ajouter du traitement ici pour d'eventuelles infos a recuperer dans le xml
     * de maniere 'indirecte', comme ici, la WishedStartDate
     *
     * @access public
     */
    public function buildNeededData() {
        $commandParser = $this->commandParser;
        $baseXmlElement = $commandParser->xmlCommand->xpath($this->xmlCommandPath);
        $baseXmlElement = $baseXmlElement[0];
        $destinatorName = $commandParser->getElementValue('Destinator', $baseXmlElement);
        $destinator = Object::load('Actor', array('Name'=>$destinatorName), array('Name'));

        if ($destinator instanceof Exception) {
            throw new Exception(ERROR_DESTINATOR);
        }
        $commandParser->commandData = array_merge(
                $commandParser->commandData,
                array('Destinator' => $destinator->getId(),
                      'WishedStartDate' => '2007-08-10 09:00:00'));

    }

    // }}}
}

?>