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

require_once ('Objects/DocumentModelProperty.php');

/**
 * Retourne le label coresspondant à la property.
 * @param int $cell la property
 * @return string
 * @access public
 */
function getDocumentModelPropertyCellLabel($cell)
{
    $labels = DocumentModelProperty::getPropertyTypeConstArray();
    if(isset($labels[$cell])) {
        return $labels[$cell];
    }
    return '';
}

/**
 * retourne la valeur d'une DocumentModelProperty. Cette méthode
 * est utilisé pour obtenir les propertys lors de la génération d'un
 * document via l'appli web ou via le Zaurus.
 * @param int $cell la property
 * @param object $documentGenerator :
 *          - méthode appelé via l'appliweb, $documentGenerator est un objet
 *          DocumentGenerator.
 *          - méthode appelé depuis ExecutionDataXMLRenderer pour construire
 *          le xml à envoyer au zaurus, $documentGenerator est un objet
 *          DeliveryOrder ou Invoice (à venir).
 * @return string
 * @access public
 */
function getDocumentModelPropertyCellValue($cell, $documentGenerator, $cmd=false)
{
    if($documentGenerator instanceof DocumentGenerator) {
        $document = $documentGenerator->document;
    } else {
        //require_once('DocumentGenerator.php');
        if($documentGenerator instanceof DeliveryOrder) {
            $document = $documentGenerator;
        } elseif($documentGenerator instanceof Invoice) {
            $document = $documentGenerator;
        }
    }

    if (!($cmd instanceof Command)) {
        $cmd = $document->getCommand();
    }
    if($cmd instanceof Command) {
        $destinator = $cmd->getDestinator();
    }
    if ($documentGenerator instanceof InvoicesListGenerator) {
    	$destinator = $documentGenerator->destinator;
    }
    $value = '';
    switch ($cell) {
    	case DocumentModelProperty::CELL_NO_DOC:
        	$date = $document->getEditionDate('localedate_short');
        	$value .= sprintf(_('Number %s of %s'), $document->getDocumentNo(), $date);
        	if($documentGenerator instanceof DeliveryOrder || $documentGenerator instanceof Invoice){
        	    /* le n° de doc etant générer sur le zaurus il faut passer
        	    une valeur que documentwriter.py pourra rempacer*/
        	    $value = 'docnumber';
        	    //A TESTER : un truc du genre
        	    //$value = 'n° self.formatValue(self.cmd.getBLNumber()) du self.formatValue(self.cmd.getEditionDate())';
    	    }
        	break;
        case DocumentModelProperty::CELL_NO_COMMAND:
    	    if($cmd instanceof Command) {
        	    $value .= $cmd->getCommandNo();
            }
        	break;
    	case DocumentModelProperty::CELL_NO_BL:
    	    if($document instanceof Invoice || $document instanceof ForwardingForm) {
    	        // on affiche les BL liés au LEM facturés.
    	        // cad : ceux dont la date d'edition est sup a la date du lem
                // et inf à la date d'edition de la facture

    	        if($document instanceof Invoice) {
    	            $invoiceItemCol = $document->getInvoiceItemCollection();
    	            $lemMapper = Mapper::singleton('LocationExecutedMovement');
    	            $lemCol = $lemMapper->loadCollection(
    	                array('InvoiceItem'=>$invoiceItemCol->getItemIds()), 
                        array(), array('Date'));
                } else {
    	            $lemCol = $document->getLocationExecutedMovementCollection();
                }
    	        $count = $lemCol->getCount();
    	        $doMapper = Mapper::singleton('DeliveryOrder');
    	        $doSet = array();
    	        $dateArray = array();
    	        for($i=0 ; $i<$count ; $i++) {
    	            $lem = $lemCol->getItem($i);
    	            if(!in_array($lem->getDate(), $dateArray)) {
    	                $dateArray[] = $lem->getDate();
    	                $filter = new FilterComponent();
    	                if($document instanceof Invoice) {
                            $filter->setItem(new FilterRule(
                                'Command',
                                FilterRule::OPERATOR_EQUALS,
                                $cmd->getId()));
                        }
                        $filter->setItem(new FilterRule(
                            'EditionDate',
                            FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                            $lem->getDate()));
                        $filter->setItem(new FilterRule(
                            'EditionDate',
                            FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                            $document->getEditionDate()));
                        $filter->operator = FilterComponent::OPERATOR_AND;
    	                $doCollection = $doMapper->loadCollection($filter);
    	                $countDO = $doCollection->getCount();
                        for($j = 0; $j < $countDO; $j++) {
                            $item = $doCollection->getItem($j);
                            if(!in_array($item->getId(), $doSet)) {
                                $doSet[] = $item->getId();
                                $value .= sprintf(_("No %s from %s\n"), 
                                    $item->getDocumentNo(),
                                    $item->getEditionDate('localedate_short'));
                            }
                        }
    	            }
    	        }
            } elseif (method_exists($document, 'getDocumentNo') && method_exists($document, 'geteditionDate')) {
                $value .= sprintf(_("No %s from %s\n"), $document->getDocumentNo(),
                        $document->getEditionDate('localedate_short'));
            }
            if($documentGenerator instanceof DeliveryOrder || $documentGenerator instanceof Invoice) {
        	    /* le n° de doc etant générer sur le zaurus il faut passer
        	    une valeur que documentwriter.py pourra rempacer*/
        	    $value = 'blnumber';
        	    //A TESTER : un truc du genre
        	    //$value = 'n° self.formatValue(self.cmd.getBLNumber()) du self.formatValue(self.cmd.getEditionDate())';
        	}
        	break;
    	case DocumentModelProperty::CELL_PACKING_LIST:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	$value .= $ced instanceof CommandExpeditionDetail?$ced->getPackingList():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_AIRWAY_BILL:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	$value .= $ced instanceof CommandExpeditionDetail?$ced->getAirwayBill():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_CUSTOMER_CODE:
    	    if(isset($destinator) && $destinator instanceof Actor) {
    	        $value .= $destinator->getCode();
    	    }
        	break;
    	case DocumentModelProperty::CELL_COMMERCIAL:
        	if($cmd instanceof Command) {
            	$comm = $cmd->getCommercial();
            	$value .= $comm instanceof UserAccount?$comm->getIdentity():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_CUSTOMER_TVA:
    	    if(isset($destinator) && $destinator instanceof Actor) {
    	        $value .= $destinator->getTVA();
    	    } else {
    	        $sp = $document->getSupplierCustomer();
    	        if($sp instanceof SupplierCustomer) {
    	            $customer = $sp->getCustomer();
    	            $value .= $customer->getTVA();
    	        }
    	    }
        	break;
    	case DocumentModelProperty::CELL_REGLEMENT:
            if (method_exists($document, 'getPaymentDate') && method_exists($document, 'getPaymentcondition')) {
                $paymentDate = $document->getPaymentDate('localedate_short');
                $paymentCondition = $document->getPaymentCondition();
                if($paymentDate) {
                    $value .= _('Date') . ' : ' . $paymentDate;
                }
                if($paymentCondition) {
                    $value .= "(" . $paymentCondition . ")\n";
                }
            }
            if($cmd instanceof Command) {
                $sp = $cmd->getSupplierCustomer();
                if ($document instanceof DeliveryOrder) {
                    if ($sp instanceof SupplierCustomer) {
                        $value .= $sp->getPaymentCondition() . "\n";
                    }
                }
                $abd = $cmd->getActorBankDetail();
                if (!($abd instanceof ActorBankDetail)) {
                    break;
                }
                if ($sp instanceof SupplierCustomer &&
                    !in_array($sp->getModality(),
                              array(SupplierCustomer::VIREMENT, SupplierCustomer::TRAITE, SupplierCustomer::BILLET_ORDRE))) {
                    break;
                }
                $streetConstArray = $abd->getBankAddressStreetTypeConstArray();
                $streetType = isset($streetConstArray[$abd->getBankAddressStreetType()])?
                        $streetConstArray[$abd->getBankAddressStreetType()]:'';
                $value .= sprintf(_("Bank: %s\n"), $abd->getBankName());
                $value .= sprintf("%s %s %s\n", $abd->getBankAddressNo(),
                    $streetType, $abd->getBankAddressStreet());
                if ($abd->getBankAddressAdd() != '') {
                    $value .= sprintf("%s\n", $abd->getBankAddressAdd());
                }
                $iban = ($abd->getIban()!="0")?$abd->getIban():'';
                $value .= sprintf("%s %s %s\n%s\n%s", $abd->getBankAddressCity(),
                    $abd->getBankAddressZipCode(), $abd->getBankAddressCountry(),
                    $abd->getAccountNumber(), $iban);
            }
            break;
    	case DocumentModelProperty::CELL_PORT_CONDITION:
        	if($cmd instanceof Command) {
            	$inco = $cmd->getIncoterm();
            	$value .= $inco instanceof Incoterm?$inco->toString():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_STORE:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	if($ced instanceof CommandExpeditionDetail)  {
            	    $value .= $ced->getDestinatorStore() . ' - ' . $ced->getDestinatorRange();
            	} else {
            	    $value .= '';
            	}
        	}
        	break;
    	case DocumentModelProperty::CELL_FAMILY:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	$value .= $ced instanceof CommandExpeditionDetail?$ced->getSeason():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_CUSTOMER_COMMAND_NO:
        	if($cmd instanceof Command) {
        	    $ced = $cmd->getCommandExpeditionDetail();
        	    $value .= $ced instanceof CommandExpeditionDetail?$ced->getCustomerCommandNo():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_DEAL:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	$value .= $ced instanceof CommandExpeditionDetail?$ced->getDeal():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_SUPPLIER_CODE:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	$value .= $ced instanceof CommandExpeditionDetail?$ced->getSupplierCode():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_RESERVATION_NO:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	$value .= $ced instanceof CommandExpeditionDetail?$ced->getReservationNo():'';
        	}
        	break;
    	case DocumentModelProperty::CELL_LOADING_PORT:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	$value .= $ced instanceof CommandExpeditionDetail?$ced->getLoadingPort():'';
        	}
    	   break;
    	case DocumentModelProperty::CELL_SHIPMENT:
    	    $value .= '';
        	if($cmd instanceof Command) {
        	    $ced = $cmd->getCommandExpeditionDetail();
        	    if ($ced instanceof CommandExpeditionDetail) {
            	    $shipmentConstArray = $ced->getShipmentConstArray();
                	$value .= $shipmentConstArray[$ced->getShipment()];
        	    }
        	}
        	break;
    	case DocumentModelProperty::CELL_VOLUME:
        	if($cmd instanceof Command) {
            	$ced = $cmd->getCommandExpeditionDetail();
            	if($ced instanceof CommandExpeditionDetail) {
                	if($ced->getWeight()>0) {
                	   $value .= _('Parcels total weight (Kg)') . ' : ' . $ced->getWeight() . "\n";
                	}
                	if($ced->getNumberOfContainer()>0) {
                	   $value .= _('Number of parcels') . ' : ' . $ced->getNumberOfContainer();
                	}
            	}
        	}
        	break;
        case DocumentModelProperty::CELL_TOTAL_PIECE:
            $totalPiece = 0;
            if($document instanceof Invoice) {
                if($cmd instanceof ProductCommand) {
                    $productMapper = Mapper::singleton('Product');
                    $invoiceItemCol = $document->getInvoiceItemCollection();
                    $iviCount = $invoiceItemCol->getCount();
                    for ($i=0 ; $i<$iviCount ; $i++) {
                        $invoiceItem = $invoiceItemCol->getItem($i);
                        $product = $productMapper->load(
                            array('BaseReference'=>$invoiceItem->getReference()));
                        $totalPiece += $invoiceItem->getQuantity() * $product->getNumberUBInUV();
                    }
                } elseif ($cmd instanceof ChainCommand) {
                    $chCmdItemCol = $cmd->getCommandItemCollection();
                    $chCmdItemCount = $chCmdItemCol->getCount();
                    for ($i=0 ; $i<$chCmdItemCount ; $i++) {
                        $chCmdItem = $chCmdItemCol->getItem($i);
                        $totalPiece += $chCmdItem->getQuantity();
                    }
                }
            } elseif ($document instanceof DeliveryOrder) {
                $cmiCol = $cmd->getCommandItemCollection();
                $count = $cmiCol->getCount();
                for ($i=0; $i<$count; $i++) {
                    $cmi = $cmiCol->getItem($i);
                    $acm = $cmi->getActivatedMovement();
                    if ($acm instanceof ActivatedMovement){
                        $exm = $acm->getExecutedMovement();
                        if ($exm instanceof ExecutedMovement){
                            $lemCol = $exm->getLocationExecutedMovementForBL(
                                $document->getEditionDate(), true);
                            $lemCount = $lemCol->getCount();
                            for ($j=0;$j<$lemCount;$j++) {
                                $lem = $lemCol->getItem($j);
                                $product = $lem->getProduct();
                                $totalPiece += $lem->getQuantity() * $product->getNumberUBInUV();  
                            }    
                        }
                    }
                }
            } elseif ($document instanceof PackingList) {
                $box = $document->getBox();
                $parentBox = $box->getParentBox();
                if($parentBox instanceof Box) {
                    $boxCol = $parentBox->getChildrenBoxes();
                } else {
                    $boxCol = $box->getChildrenBoxes();
                }
                $totalPiece = $boxCol->getCount();
            } elseif ($document instanceof ForwardingForm) {
                // pour ne pas tenir compte des lem lié à un des products
                // liés à un ForwardingFormPacking
                $FFP_products = array();
                $ffpCol = $document->getForwardingFormPackingCollection(
                    array('CoverType'=>0));
                $count = $ffpCol->getCount();
                for($i=0 ; $i<$count ; $i++) {
                    $ffp = $ffpCol->getItem($i);
                    $FFP_products[] = $ffp->getProductId();
                }
                // calcul du total piéces
                $lemMapper = Mapper::singleton('LocationExecutedMovement');
                $lemCol = $lemMapper->loadCollection(
                    array('ForwardingForm'=>$document->getId()));
                $count = $lemCol->getCount();
                for ($i=0 ; $i<$count ; $i++) {
                    $lem = $lemCol->getItem($i);
                    if(!in_array($lem->getProductId(), $FFP_products)) {
                        $product = $lem->getProduct();
                        $totalPiece += $lem->getQuantity() * 
                            $product->getNumberUBInUV();
                    }
                }
                
            }
            
            if($totalPiece > 0) {
                $value .= $totalPiece;
            }
            break;
    	default:
    	   $value .= 'N/A';
    	   break;
    }
    return $value;
}
?>
