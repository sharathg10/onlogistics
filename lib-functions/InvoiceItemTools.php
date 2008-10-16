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

function createInvoiceItems($Invoice, $Command, $gridItemsArray,
            $HandingArray, $ProductIdArray, $QuantityArray, $TVAArray,
            $PriceHTArray, $PdtCmdItemIdArray, $Flag=0 ) {
    $InvoiceItemCollection = new Collection();
    $tvaMapper = Mapper::singleton('TVA');
	// Attention,ici, c'est bien val qui contient l'indice de ligne a traiter!!
	foreach($gridItemsArray as $key => $val) {
		//  La transaction est geree au niveau au dessus, dans InvoiceAddEdit.php

		$ProductCommandItem = Object::load('ProductCommandItem',
                $PdtCmdItemIdArray[$val]);
		// si les infos viennent de ProductCommandItem
        if (!Tools::isEmptyObject($ProductCommandItem)) {
			$AMovement = $ProductCommandItem->getActivatedMovement();
		}
		else {  // sinon, elles viennent de LEM (les mvts ont ete executes)
			$AMovement = Object::load('ActivatedMovement', $_POST['ActivatedMvt'][$val]);
			$ProductCommandItem = $AMovement->getProductCommandItem();
		}

		$Product = Object::load('Product', $ProductIdArray[$val]);

        // Creation de l'InvoiceItem
		$InvoiceItem = Object::load('InvoiceItem');
		if ($HandingArray[$val] != 0) {
		    $InvoiceItem->setHanding($HandingArray[$val]);
		}

		$InvoiceItem->setName($Product->getName());
		// Si facture de commande fournisseur de produits:
		// on stocke uniquement la ref fournisseur si elle existe
        if ($Command->getType() == Command::TYPE_SUPPLIER) {
            $ref = $Product->getReferenceByActor($Command->getExpeditor());
            $ref = empty($ref)?$Product->getBaseReference():$ref;
        }
        else {
            $ref = $Product->getBaseReference();
            $assocRef = $Product->getReferenceByActor($Command->getDestinator());
        }
		$InvoiceItem->setReference($ref);
		// Si facture de commande client de produits:
		// on stocke la baseRef + la ref client si elle existe
		if ($Command->getType() == Command::TYPE_CUSTOMER && !empty($assocRef)) {
            $InvoiceItem->setAssociatedReference($assocRef);
        }
		$InvoiceItem->setQuantity($QuantityArray[$val]);
		$tva = $tvaMapper->load(array('Id'=>$TVAArray[$val]));
		if($tva instanceof TVA) {
		    $InvoiceItem->setTva($tva);
		}
		$InvoiceItem->setUnitPriceHT($PriceHTArray[$val]);
		$InvoiceItem->setInvoice($Invoice);
		$InvoiceItem->setActivatedMovement($AMovement);
		$InvoiceItem->save();
		$InvoiceItemCollection->setItem($InvoiceItem);

		// MAJ des infos de l'etat de facturation du ACM
		//  Etat ActivatedMovement::CREE: on prend l'info ds CommandItem
		if (!in_array($AMovement->getState(),
                    array(ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT, ActivatedMovement::ACM_EXECUTE_TOTALEMENT))) {
			// Pas de LEM, et Commande Fournisseur, ds ce cas
	        $AMovement->setHasBeenFactured(ActivatedMovement::ACM_FACTURE);
	   	}
		else {
			$EXMovement = $AMovement->getExecutedMovement();
			// Les LEM concernes par la ligne du Grid traitee:
			// LEMs non annulateurs, non factures, lies au Product correspondant
			// les LEM de Quantity nulle sont ignores
			$filterComponentArray = array();
			$filterComponentArray[] = SearchTools::newFilterComponent(
                    'InvoiceItem', '', 'Equals', 0, 1);
			$filterComponentArray[] = SearchTools::newFilterComponent(
                    'Product', '', 'Equals', $ProductIdArray[$val], 1);
			$filterComponentArray[] = SearchTools::newFilterComponent(
                    'Cancelled', '', 'LowerThan', 1, 1);
			// ici, 'Quantity2' car il existe un input text du meme nom!!
			$filterComponentArray[] = SearchTools::newFilterComponent(
                    'Quantity2', 'Quantity', 'NotEquals', 0, 1);
			$filter = SearchTools::filterAssembler($filterComponentArray);
	        $LEMCollection = $EXMovement->getLocationExecutedMovementCollection($filter);

            // MAJ des infos de l'etat de facturation des LEM: LEM.InvoiceItem
    		if (!Tools::isEmptyObject($LEMCollection)) {
    		    $count = $LEMCollection->getCount();
        		for($i = 0; $i < $count; $i++) {
        			$LEM = $LEMCollection->getItem($i);
        			$LEM->setInvoiceItem($InvoiceItem);
        			$LEM->save();
        		}
    		}

			if ($AMovement->getState() == ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT) {
			    $AMovement->setHasBeenFactured(ActivatedMovement::ACM_FACTURE_PARTIEL);
			}
			else {  // Mouvement execute totalement: plus complique
				if ($AMovement->hasLEMnotFactured() === false) {
					$AMovement->setHasBeenFactured(ActivatedMovement::ACM_FACTURE);
				}
				else {
					$AMovement->setHasBeenFactured(ActivatedMovement::ACM_FACTURE_PARTIEL);
				}
			}
		}
		$AMovement->save();
		unset($Product, $InvoiceItem, $AMovement, $LEMCollection, $filter);
	} // foreach

	return $InvoiceItemCollection;
}

/*
* A la facturation, calcule la date de paiement en fonction du SupplierCustomer,
* s'il existe, de $Invoice.EditionDate sinon, et met a jour
* $Invoice.setPaymentDate.
*
* @param $Invoice object Invoice
* @param $Command object: ProductCommand, ou CourseCommand, ...
* @return void
**/
function savePaymentDate($Invoice, $Command) {
	require_once('Objects/SupplierCustomer.php');
	$Expeditor = $Command->getExpeditor();    //on recupere le fournisseur
	$Destinator = $Command->getDestinator();  //on recupere le client

	$SupplierCustomerMapper = Mapper::singleton('SupplierCustomer');
	$SupplierCustomer = $SupplierCustomerMapper->load(
            array('Supplier' => $Expeditor, 'Customer' => $Destinator));

    if (!Tools::isEmptyObject($SupplierCustomer)) {
        // met la date de paiment de la facture au premier reglement
        $top = $SupplierCustomer->getTermsOfPayment();
        $paymentDate = false;
        if ($top instanceof TermsOfPayment) {
            $topItems = $top->getTermsOfPaymentItemCollection();
            foreach ($topItems as $topItem) {
                list($date, $amount, $actor) = $topItem->getDateAndAmountForOrder($Command);
                if (!$paymentDate || $date < $paymentDate) {
                    $paymentDate = $date;
                }
            }
        }
        if (!$paymentDate) {
            // sinon on prend la date d'edition de la facture
            $paymentDate = $Invoice->getEditionDate();
        }
	    $Invoice->setPaymentDate($paymentDate);
	} else {  // Pas de SupplierCustomer defini
	    $Invoice->setPaymentDate($Invoice->getEditionDate());
	}
}

/**
 * Retourne true si le No passe en parametre est deja affecte a un doct, false sinon.
 * @param string $DocumentNo
 * @param string $className : possibilite de restreindre a une classe de documents
 * @return boolean
 **/
function documentNoExist($DocumentNo, $className='AbstractDocument') {
	$mapper = Mapper::singleton($className);
	$Document = $mapper->load(array('DocumentNo' => $DocumentNo));
	if (Tools::isEmptyObject($Document)) {
	    return false;
	}
	return true;
}

/**
 * Genere un No de Document
 * @access public
 * @param $CdeType string: FC pour facture client,
 						   FF pour facture fournisseur,
						   Av pour Avoir
 * @param string $className : possibilite de restreindre a une classe de documents
 * @param integer $id : Id du document cree
 * @return string
 **/
function generateDocumentNo($CdeType='', $className='AbstractDocument', $id) {
	$DocumentNo = $CdeType . $id;
	while(documentNoExist($DocumentNo, $className)) {
		$id++;
		$DocumentNo = $CdeType . $id;
	}
	// On est sur que ce No n'est pas deja attribue
	return $DocumentNo;
}

/**
 * Gestion de la facturation de n factures.
 * @param array $cmdIds
 * @return void
 **/
function chargeSeveralCommands($cmdIds) {
    $errorUrl = 'CommandList.php';
    $notBillableCommands = array();
    $lockedCommands = array();
    $withNoMailUserCommands = array(); // Concerne les invoice a envoyer par mail
    $invoicesToPrint = array();
    foreach($cmdIds as $cmdId) {
        $cmd = Object::load('Command', $cmdId);
        $cmdState = $cmd->getState();
        if ($cmdState == Command::FACT_COMPLETE || $cmdState == Command::REGLEMT_TOTAL) {
            continue;  // Deja facture
        }
        $sp = $cmd->getSupplierCustomer();
        $hasTVA = (($sp instanceof SupplierCustomer && $sp->getHasTVA()) || $cmd->isWithTVA());
        if ($cmdState == Command::BLOCAGE_CDE) {
            $lockedCommands[] = $cmd->getCommandNo();
            continue;
        }
        if (!$cmd->isBillable()) {
            $notBillableCommands[] = $cmd->getCommandNo();
            continue;
        }
        /*  Ouverture de la transaction: une transaction par commande  */
        Database::connection()->startTrans();

        $invoice = createInvoice($cmdId);
        saveInstance($invoice, $errorUrl); // necessaire ici

        // Les InvoiceItems
        $CommandItemCollection = $cmd->getCommandItemCollection();
        $cmdType = $cmd->getType();
        $count = $CommandItemCollection->getCount();
        /* Regle de gestion: si pour un pdt donne on a une cmdItem non mvtee et
         * un LEM d'une autre cmdItem (suite a une substitution), on cree 2
         * InvoceItems (car par exple, on peut avoir 2 handing differents)
         * Si par contre pour un pdt donne on a 2 LEM mais lies a la meme CmdItem,
         * (via acm), on ne cree qu'une seule InvoiceItem (on add les qty)
        */
        for($i = 0; $i < $count; $i++) {
            $cmdItem = $CommandItemCollection->getItem($i);
            if (!method_exists($cmdItem, 'getActivatedmovement')) {
                continue;
            }

            $acm = $cmdItem->getActivatedmovement();
            // si un ActivatedMovement a deja ete facture
            if(!($acm instanceof ActivatedMovement) || $acm->getHasBeenFactured() == 1) {
                // tout ce qui suit est basé sur l'ACM.
                continue;
            }

        	//  Etat ActivatedMovement::CREE: on prend l'info ds CommandItem
        	if (!in_array($acm->getState(),
                    array(ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT,
                        ActivatedMovement::ACM_EXECUTE_TOTALEMENT))) {
                // LES ACM pas encore execute sont non facturables pour cmd client
                if ($cmdType == Command::TYPE_CUSTOMER) {
                    continue;
                }
                $Product = $cmdItem->getProduct();
                $productId = $Product->getId();
                $quantity = $cmdItem->getQuantity();
        		$invoiceItem = createInvoiceItem(
                        $cmdItem, $invoice, $quantity, $hasTVA, $Product);
            }

            // Si un activatedMovement a ete passe en execution:
            // l'info est ds le LEM non factures
            else {
                // Le array suivant, pour n'avoir qu'une seule InvoiceItem
                // par Product, pour un ACM donne
                $invoiceItemByProduct = array();
                $exm = $acm->getExecutedMovement();
                $filterComponentArray = array();
        		$filterComponentArray[] = SearchTools::newFilterComponent(
                        'InvoiceItem', '', 'Equals', 0, 1);
        		$filterComponentArray[] = SearchTools::newFilterComponent(
                        'Cancelled', '', 'LowerThan', 1, 1);
        		$filterComponentArray[] = SearchTools::newFilterComponent(
                        'Quantity', 'Quantity', 'NotEquals', 0, 1);
        		$filter = SearchTools::filterAssembler($filterComponentArray);
                $LEMCollection = $exm->getLocationExecutedMovementCollection($filter);
                if (!Tools::isEmptyObject($LEMCollection)) {
        			$lemCount = $LEMCollection->getCount();
        			for($j = 0;$j < $lemCount; $j++) {
                        $LEM = $LEMCollection->getItem($j);
        				$Product = $LEM->getProduct();
        				$productId = $Product->getId();
        				// si ce Product deja traite (1 invoiceItem par Product)
        				if (!in_array($productId, array_keys($invoiceItemByProduct))) {
        				    $invoiceItem = createInvoiceItem(
                                $cmdItem, $invoice, $LEM->getQuantity(), $hasTVA, $Product);
                            saveInstance($invoiceItem, $errorUrl);
                            $invoiceItemByProduct[$productId] = $invoiceItem->getId();
        				}
                        else {
                            $invoiceItem = Object::load(
                                'InvoiceItem', $invoiceItemByProduct[$productId]);
                            // Qte mvtee, non reintegree, et non facturee
            				$quantity = $exm->getProductMovedQuantity($Product, 0);
            				if ($quantity == 0) {  // si tout a ete reintegre
            				    continue;
            				}
            				$invoiceItem->setQuantity($invoiceItem->getQuantity() + $quantity);
            				saveInstance($invoiceItem, $errorUrl);
                        }
        			} // for
        		}
        	}
        } // for sur les cmdItem

        $invoiceItemColl = $invoice->getInvoiceItemCollection();
        // Permet de supprimer une Invoice, si pas de InvoiceItem finalement
        $count = $invoiceItemColl->getCount();
        if ($count == 0) { // Rollback car pas seulement l'invoice a supprimer
            Database::connection()->rollbackTrans();
            continue;  // Pas de message d'erreur dans ce cas
        }

        // Prix total, toPay, en fonction d'un eventuel acompte...
        // Si acompte (Installment), seule la 1ere facture pour cette cmde en
        // tient compte
        $installment = ($cmd->getInstallment() != 0 && $invoice->isFirstInvoiceForCommand())?
            $cmd->getInstallment():0;

        // Calcul du prix HT total pour l'invoice
        $totalpriceHT = $totalpriceTTC = 0;

        for($i = 0; $i < $count; $i++){
            $invoiceItem = $invoiceItemColl->getItem($i);
            $qty = $invoiceItem->getQuantity();
            $handing = $invoiceItem->getHanding();
            $handingType = $invoiceItem->HandingType();
            $basePrice = $invoiceItem->getUnitPriceHT();
            require_once('CalculatePriceHanding.php');
            require_once('FormatNumber.php');
            $htTotal = calculatePriceHanding($handingType, $basePrice, $qty, $handing);
            $htTotal = troncature($htTotal, 2);
            $tvaRate = ($invoiceItem->getTVAId() > 0)?$invoiceItem->getTVA()->getRate():0;
            $totalpriceHT += $htTotal;
            $totalpriceTTC += troncature($htTotal * (1 + ($tvaRate / 100)));
         } // for

        $invoice->setTotalPriceHT($totalpriceHT);
    	
    	// Gestion de la tva surtaxee
    	$spc = $invoice->getSupplierCustomer();
    	$hasTvaSurtax = $spc->getHasTvaSurtax();
        $tvaSurtaxRate = Preferences::get('TvaSurtax', 0);
        if ($hasTvaSurtax && $tvaSurtaxRate > 0) {
            $totalpriceTTC += $totalpriceHT * $tvaSurtaxRate / 100;
        }
    	// Gestion de la taxe Fodec
    	$hasFodecTax = $spc->getHasFodecTax();
        $fodecTaxRate = Preferences::get('FodecTax', 0);
        if ($hasFodecTax && $fodecTaxRate > 0) {
            $totalpriceTTC += $totalpriceHT * $fodecTaxRate / 100;
        }
        // Gestion du timbre fiscal
        $hasTaxStamp = $spc->getHasTaxStamp();
        $taxStamp = Preferences::get('TaxStamp', 0);
        if ($hasTaxStamp && $taxStamp > 0) {
            $totalpriceTTC += $taxStamp;
        }
        
    	$invoice->setTotalPriceTTC(troncature($totalpriceTTC, 2));

        //TotalPriceTTC de la facture est <= a l'accompte
        if ($invoice->getTotalPriceTTC() <= $installment) {
            $invoice->setToPay(0);
        } else {
            $invoice->setToPay($invoice->getTotalPriceTTC() - $installment);
        }

        // calcule la commission du commercial
        $invoice->updateCommercialCommission();

        // MAJ les FK DeliveryOrder.Invoice, cmd.State, invoice.PaymentDate
        updateMixedObjects($cmd, $invoice, $errorUrl);
        saveInstance($invoice, $errorUrl);

        // mise à jour de l'encours courant
        require_once('ProductCommandTools.php');
        $alert = commandBlockage($cmd, $invoice);

        /*  Commit de la transaction  */
        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
            Database::connection()->rollbackTrans();
            Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $errorUrl);
            exit;
        }
        Database::connection()->completeTrans();

        // Seulement apres la transaction, on envoie l'alerte si necessaire
    	if (!Tools::isEmptyObject($alert)) {
    	    $alert->send();
    	}
    	// Commande fourniseur: pas d'impression ni d'envoi par mail
    	if ($cmdType != Command::TYPE_CUSTOMER) {
    	    continue;
    	}
    	$auth = Auth::Singleton();  // pour le parametrage des mails
    	if ($sp->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_NONE) {
            // Pas d'envoi de mail: simplement edition de la facture
	        $invoicesToPrint[] = $invoice->getId();
        } else if ($sp->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_ALERT) {
    	    // Envoi de mail: simplement une alerte: la facture est disponible
            // Si pas d'envoi de mail car pas de userAccount parametre pour
            // les recevoir: cas gere via l'exception retournee par MailTools::send()
            $result = AlertSender::send_ALERT_INVOICE_TO_DOWNLOAD($invoice, $auth->getUser());
    	} else if ($sp->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_YES) {
    	    // Envoi de mail: la facture en piece jointe
            // Si pas d'envoi de mail car pas de userAccount parametre pour
            // les recevoir: cas gere via l'exception retournee par MailTools::send()
            $result = AlertSender::send_ALERT_INVOICE_BY_MAIL($invoice, $auth->getUser());
    	}
        if (isset($result) && Tools::isException($result)) {
            $withNoMailUserCommands[] = $cmd->getCommandNo();
        }
    }  // foreach sur les cmdIds

    $notBillableCmdMsg = _('The following orders are not billable: <ul><li>%s</li></ul>');
    $lockedCmdMsg = _('The following orders are locked because outstanding debts amount exceeds their maximum values: <ul><li>%s</li></ul>');
    $noMailUserCmdMsg = _('For the following orders invoice could not be sent by email because no user is set to receive it: <ul><li>%s</li></ul>');
    $msg = '';
    if (!empty($notBillableCommands)) {
        $msg = sprintf($notBillableCmdMsg, implode('</li><li>', $notBillableCommands)).'<br>';
    }
    if (!empty($lockedCommands)) {
         $msg .= sprintf($lockedCmdMsg, implode('</li><li>', $lockedCommands)).'<br>';
    }
    if (!empty($withNoMailUserCommands)) {
        $msg .= sprintf($noMailUserCmdMsg, implode('</li><li>', $withNoMailUserCommands)).'<br>';
    }
    if (empty($invoicesToPrint) && empty($msg)) {
        Tools::redirectTo('CommandList.php');
    }
    if (!empty($invoicesToPrint)) {
        $msg .= _('Click on the button below to print invoices.') . '<br>';
        $tpl = new Template();
        $_REQUEST['InvoiceId'] = $invoicesToPrint;
        $hiddenFields = UrlTools::buildHiddenFieldsFromURL('InvoiceId');
        $tpl->assign('hiddenFields', $hiddenFields);
        $tpl->assign('message', $msg);
        Template::page(
            _('Charge several orders'), $tpl->fetch('Invoice/InvoiceAddEditSeveral.html'));
        exit;
    }
    if (!empty($msg)) {
        Template::infoDialog($msg, $errorUrl);
    	exit;
    }
    Tools::redirectTo('CommandList.php');
}


/**
 * Creation de l'Invoice:
 *  - Soit appel en boucle pour la facturation multiple
 *  - Soit appel une fois pour facturation d'une seule cmde: on utilise dans ce
 *    cas les donnees de $_POST
 * @param integer $cmdId
 * @return object Invoice
 **/
function createInvoice($cmdId) {
    $cmd = Object::load('Command', $cmdId);
    $invoiceMapper = Mapper::singleton('Invoice');
    $invoice = Object::load('Invoice');
    $cmdType = $cmd->getType();
    $invoice->setCommand($cmd);
    $invoice->setCommandType($cmd->getInvoiceCommandType());
    // l'acteur possédant l'accounting type
    $typesCustomer = array(Command::TYPE_CUSTOMER, Command::TYPE_TRANSPORT, Command::TYPE_COURSE);
    if (in_array($cmdType, $typesCustomer)) {
        $invoice->setAccountingTypeActor($cmd->getDestinatorId());
    } else if ($cmdType == Command::TYPE_SUPPLIER) {
        $invoice->setAccountingTypeActor($cmd->getExpeditorId());
    }
    $invoice->setSupplierCustomer($cmd->getSupplierCustomer());
    $invoice->setCurrency($cmd->getCurrency());
    // on veut facturer les frais de port de transport et d'assurance
    // Par defaut (facturation multiple), on n'en tient pas compte
    if (isset($_POST['CoutPortPackingInsurance'])) {
        $invoice->setInsurance($_POST['Assurance']);
        $invoice->setPacking($_POST['Emballage']);
        $invoice->setPort($_POST['Port']);
    }
    if (isset($_POST['GlobalHanding'])) {
        $invoice->setGlobalHanding($_POST['GlobalHanding']);
    } else {
        $invoice->setGlobalHanding($cmd->getHanding());
    }

	if(isset($_POST['IAEComment'])) {
		$invoice->setComment(trim($_POST['IAEComment']));
	}
    if (isset($_POST['InvoiceNumero']) && ($_POST['InvoiceNumero'] != "")) {
		if (documentNoExist($_POST['InvoiceNumero'])) {
		    Template::errorDialog(
                    _('A document with the same number already exists, please correct.'),
					'javascript:history.go(-1)');
       		exit;
		}
        $DocumentNo = $_POST['InvoiceNumero'];
    }
	else {
        $cdetype = (in_array($cmdType, $typesCustomer))?'FC':'FF';
		$invoiceId = $invoiceMapper->generateId();
        $DocumentNo = generateDocumentNo($cdetype, 'AbstractDocument', $invoiceId);
    }
	if (!isset($invoiceId)) {
	    $invoiceId = $invoiceMapper->generateId();
	}
	$invoice->setId($invoiceId);
	$invoice->setDocumentNo($DocumentNo);
	if(isset($_POST['Invoice_EditionDate'])) {
        $invoice->setEditionDate($_POST['Invoice_EditionDate']);
	} else {
	    $invoice->setEditionDate(date('Y-m-d H:i:s'));
	}
    $DocumentModel = $invoice->findDocumentModel();
	if (!(false == $DocumentModel)) {
	    $invoice->setDocumentModel($DocumentModel);
	}
    // calcul de la commission commerciale
    return $invoice;
}

/**
 * Creation de l'InvoiceItem:
 * @param integer $cmdId
 * @return object InvoiceItem
 **/
function createInvoiceItem($cmdItem, $invoice, $quantity, $hasTVA, $Product) {
    $invoiceItem = Object::load('InvoiceItem');
    $invoiceItem->setQuantity($quantity);
    $invoiceItem->setHanding($cmdItem->getHanding());
	$invoiceItem->setInvoice($invoice);
	$invoiceItem->setActivatedMovement($cmdItem->getActivatedMovement());
	if ($hasTVA) {
        $invoiceItem->setTva($cmdItem->getTVA());
    }
	$invoiceItem->setName($Product->getName());
    $cmd = $cmdItem->getCommand();
    $cmdType = $cmd->getType();

	// UnitPriceHT: le prix unitaire HT *avant* handing
	// si c'est le produit commande
	if ($Product->getId() == $cmdItem->getProductId()) {
	    $invoiceItem->setUnitPriceHT($cmdItem->getPriceHT());
	}else {
        // on va chercher le prix ds la table Product (ou ActorProduct si cmde fournisseur)
		$unitPriceHT = ($cmdType == Command::TYPE_CUSTOMER)?
            $Product->getPriceByActor($cmd->getDestinator()):
            $Product->getUVPrice($cmd->getExpeditor());
        $invoiceItem->setUnitPriceHT($unitPriceHT);
    }
    // Si facture de commande fournisseur de produits:
	// on stocke uniquement la ref fournisseur si elle existe
    if ($cmdType == Command::TYPE_SUPPLIER) {
        $ref = $Product->getReferenceByActor($cmd->getExpeditor());
        $ref = empty($ref)?$Product->getBaseReference():$ref;
    }
    else {
        $ref = $Product->getBaseReference();
        $assocRef = $Product->getReferenceByActor($cmd->getDestinator());
    }
	$invoiceItem->setReference($ref);
	// Si facture de commande client de produits:
	// on stocke la baseRef + la ref client si elle existe
	if ($cmdType == Command::TYPE_CUSTOMER && !empty($assocRef)) {
        $invoiceItem->setAssociatedReference($assocRef);
    }
    return $invoiceItem;
}

/**
 * MAJ les DeliveryOrder de la commande s'ils existent: renseigne la FK Invoice
 * MAJ Command.State
 * MAJ Invoice.PaymentDate
 * @param object $cmd (Product)Command
 * @param object $invoice Invoice
 * @param string $errorUrl seulement pour les saveInstance()
 * @return void
 **/
function updateMixedObjects($cmd, $invoice, $errorUrl) {
    $deliveryOrderMapper = Mapper::singleton('DeliveryOrder');
    $deliveryOrderCollection = $deliveryOrderMapper->loadCollection(
            array('Command' => $cmd->getId(), 'Invoice' => 0));
    if (!Tools::isEmptyObject($deliveryOrderCollection)) {
        for($i = 0; $i < $deliveryOrderCollection->getCount(); $i++) {
            $item = $deliveryOrderCollection->getItem($i);
            //on met a jour les bl avec la facture correspondante
            $item->setInvoice($invoice);
            saveInstance($item, $errorUrl);
            unset($item);
        }
    }
    // Mise a jour de l'etat de la cde en cours : on a une facture complete
    if ($cmd->isFactured() == 1) {
        $cmd->setState(Command::FACT_COMPLETE);
    } else { // il exite une ou plusieurs lignes de commande non facturées
        $cmd->setState(Command::FACT_PARTIELLE);
    }
    saveInstance($cmd, $errorUrl);
    //on renseigne la date de reglement
    savePaymentDate($invoice, $cmd);
}
?>
