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

require_once('Objects/ActivatedMovement.php');
require_once('Objects/MovementType.const.php');
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');
require_once('Objects/Alert.const.php');
require_once('InvoiceItemTools.php');
require_once('ExecutedMovementTools.php');
require_once('AlertSender.php');
require_once('RPCTools.php');  // pour clean()

// $file = "/tmp/xmlData.xml";
/**
 *
 * @param  $file string : flux XML correspondant a une command
 * @param  $rang integer : rang de la commande traitee (pour le rapport d'erreur)
 * @return mixed : boolean TRUE si tout est OK, string (msge d'erreur) sinon
 */
function parseCommand($file, $rang)
{
    /* Teste si le document xml est 'well formed' */
    //if (!$dom = domxml_open_mem($file)) {
    //    return "Document mal formé pour la commande de rang $rang. <br />";
    //}

    Database::connection()->beginTrans();
    /* Teste si le document est conforme à sa DTD  */
    //if (!@domxml_doc_validate($dom, $error)) {
        // return "La validation DTD n'est pas OK pour la commande de rang $rang.\n";
    //}
    /*	else { echo "Validation DTD OK...\n"; }exit;*/

    $xmlCmd = simplexml_load_string($file);
    if (!$xmlCmd) {
        return "Erreur de parsing pour la commande de rang $rang. <br />";
    }

	$auth = Auth::singleton();
    $Command = Object::load('Command', (int)$xmlCmd->id);

    $errMsge = "Erreur dans les informations remontées au serveur concernant "
            . "la commande traitée qui n'a pu être trouvée en base de données. <br />";
    if (Tools::isEmptyObject($Command)) return $errMsge;
    $CommandNo = $Command->getCommandNo();
    $logger = new Logger($CommandNo, $file);
    $errors = array(); // Gestion des erreurs
    /*  Chargement des mappers  */
    $exmMapper = Mapper::singleton('ExecutedMovement');
    $LPQMapper = Mapper::singleton('LocationProductQuantities');

    $AlertArray = array(); // tableau contenant les eventuels msges d'alerte de stock
    // les mvts avortes: necessaire pour remettre leur etat a: A FAIRE
    $AbortedActivatedMvtCollection = new Collection();
    $ErrorMsgeHeader = "Des mouvements n'ont pas pu être enregistrés en base de données:<ul>";
    /* Edition d'une facture */
    $invoicePrinted = (int)$xmlCmd->invoiceprinted;
    if ($invoicePrinted == 1) {
        Database::connection()->beginTrans();

    	$invoiceMapper = Mapper::singleton('Invoice');
    	$invoice = Object::load('Invoice');
    	$xmlInv = $xmlCmd->invoice;

    	$invoice->setEditionDate(clean((string)$xmlCmd->editiondate));
    	$invoice->setDocumentNo(clean((string)$xmlCmd->blnumber));
    	$invoice->setTotalPriceHT((float)$xmlInv->totalpriceht);
    	$invoice->setTotalPriceTTC((float)$xmlInv->totalpricettc);
    	$invoice->setToPay((float)$xmlInv->topay);

        $invoice->setCommand($Command);
        $commandType = $Command->getType();
        $invoice->setCommandType($Command->getInvoiceCommandType());

        // l'acteur possedant l'accounting type
        $typesCustomer = array(Command::TYPE_CUSTOMER, Command::TYPE_TRANSPORT, Command::TYPE_COURSE);
        if (in_array($commandType, $typesCustomer)) {
            $invoice->setAccountingTypeActor($Command->getDestinatorId());
        } else if ($commandType == Command::TYPE_SUPPLIER) {
            $invoice->setAccountingTypeActor($Command->getExpeditorId());
        }
        $invoice->setSupplierCustomer($Command->getSupplierCustomer());
        $invoice->setCurrency($Command->getCurrency());
        $dom = $invoice->findDocumentModel();
        $invoice->setDocumentModel($dom);
        $invoice->setPort((float)$xmlCmd->port);
        $invoice->setPacking((float)$xmlCmd->packing);
        $invoice->setInsurance((float)$xmlCmd->insurance);
        $invoice->setGlobalHanding((float)$xmlCmd->handing);

        $xmlSpc = $xmlCmd->suppliercustomer;

        $invoice->save();
        $Command->save();
    	savePaymentDate($invoice, $Command);
    	//Sauvegarde de la facture
        $invoice->save();
        $logger->logData('Sauvegarde de l\'invoice avec l\'ID ' . $invoice->getId());

        //maj de l'encour du suppliercustomer de la commande
        $sp = $Command->getSupplierCustomer();
        $sp->setUpdateIncur($sp->getUpdateIncur() + $invoice->getToPay());
        $sp->save();
        $logger->logData('Mise à jour de l\'encour du SupplierCustomer ' . $sp->getId() . ' à ' . $sp->getUpdateIncur());
        //alert pour encour dépassé
        if ($sp->getUpdateIncur() > $sp->getMaxIncur()) {
        	$AlertArray[] = array(ALERT_CLIENT_INVOICE_INCUR_OVER, array());
        }

        // mise a jour de l'etat de la cde en cours : on a une facture complete
        if ($Command->isFactured() == 1) {
            $Command->setState(Command::FACT_COMPLETE);
        } else { // il exite une ou plusieurs lignes de commande non facturées
            $Command->setState(Command::FACT_PARTIELLE);
        }
        $logger->logData('Mise à jour de l\'etat de la commande à ' . $Command->getState());
        Database::connection()->commitTrans();
    }

    $i = 0;
    foreach ($xmlCmd->commanditem as $xmlCmditem) {
        Database::connection()->beginTrans();
        $logger->logData("Debut de la transaction pour l'item " . $i + 1);

        /*  MAJ ActivatedMovement  */
        $xmlACM = $xmlCmditem->activatedmovement;
        $ActivatedMovement = Object::load('ActivatedMovement', (int)$xmlACM->id);
        if (Tools::isEmptyObject($ActivatedMovement)) {
            $j = $i + 1;
            $ieme = ($j == 1)?'er':'ième';
            $errors[] = '<li>Le ' . $j . $ieme . ' mouvement ne correspond pas'
                    . ' à un mouvement attendu.</li>';
            //return $ActivatedMovement -> getMessage();
            Database::connection()->rollbackTrans();
            Database::connection()->commitTrans();
            continue; // passage au mvt suivant
        }
        // On met a jour le RealActor de l'ACO associee
        $acoId = Tools::getValueFromMacro($ActivatedMovement,
                '%ActivatedChainTask.ActivatedOperation.Id%');
        $aco = Object::load('ActivatedChainOperation', $acoId);
        if (!Tools::isEmptyObject($aco)) {
            $aco->setRealActor($auth->getActor());
            $aco->save();
        }

        /* /// Gestion des erreurs: recup des infos a afficher  /// */
        $PartialExecutedMovement = $exmMapper->load(
                array('ActivatedMovement' => $ActivatedMovement->getId()));
        if (!Tools::isEmptyObject($PartialExecutedMovement)) {
            $PartialQuantity = $PartialExecutedMovement->getRealQuantity();
            $NeededProductRef = Tools::getValueFromMacro(
                    $PartialExecutedMovement, '%RealProduct.BaseReference%');
        } else {
            $NeededProductRef = Tools::getValueFromMacro(
                    $ActivatedMovement, '%ProductCommandItem.Product.BaseReference%');
        }
        $NeededQuantity = (isset($PartialQuantity))?
                Tools::getValueFromMacro($ActivatedMovement, '%ProductCommandItem.quantity%') - $PartialQuantity:
                Tools::getValueFromMacro($ActivatedMovement, '%ProductCommandItem.quantity%');
        $ErrorMsge = '<li>Commande ' . $Command->getCommandNo() . ', Référence attendue '
                . $NeededProductRef . ', Quantité attendue ' . $NeededQuantity
                . ' :<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        // ////////////
        $ExecutedMovement = $ActivatedMovement->getExecutedMovement();
        if (!Tools::isEmptyObject($PartialExecutedMovement)) {
            // c'est donc une reprise de partiel
            $InitialState = ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT;
        } else {
            $InitialState = ActivatedMovement::CREE;
        }

        $ActivatedMovement->setState((int)$xmlACM->state);
        $ActivatedMovement->save();
        $logger->logData("Mise à jour de l'état de l'acm " . $ActivatedMovement->getId()
                . ' à ' . (int)$xmlACM->state);

        // teste si un ExecutedMovemt est present ds le XML pour l'ActivatedMovt
        // Ne rentre pas dans cette boucle si pas d'exm
        foreach ($xmlCmditem->executedmovement as $xmlEXM) {
            $MovementType = Object::load('MovementType', (int)$xmlEXM->type);
            if (Tools::isEmptyObject($MovementType)
            || $MovementType->getId() != $ActivatedMovement->getTypeId()) {
                $errors[] = $ErrorMsge . 'Le type du mouvement exécuté ne '
                        . 'correspond pas à celui attendu.</li>';
                $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                Database::connection()->rollbackTrans();
                Database::connection()->commitTrans();
                continue; // passage au mvt suivant
            }
            $State = $xmlEXM->state;
            if (!($State instanceof SimpleXMLElement)) {
                $errors[] = $ErrorMsge . "L'état du mouvement exécuté est incorrect.</li>";
                $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                Database::connection()->rollbackTrans();
                Database::connection()->commitTrans();
                continue; // passage au mvt suivant
            }

            $MvtTypeEntrieExit = $MovementType->getEntrieExit();
            // string vide si balise absente
            $Comment = clean((string)$xmlEXM->comment);
            $ScannedRef = clean((string)$xmlEXM->reference);
            if ($ScannedRef == '') {
                // string vide si balise absente
                $ScannedRef = clean((string)$xmlCmditem->productbasereference);
            }
            $RealProduct = Object::load('Product', array('BaseReference'=>$ScannedRef));

            if (Tools::isEmptyObject($RealProduct)) {
                $errors[] = $ErrorMsge . 'La référence scannée ' . $ScannedRef
                        . ' est intouvable en base de donnée.</li>';
                $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                Database::connection()->rollbackTrans();
                Database::connection()->commitTrans();
                continue; // passage au mvt suivant
            }

            $ProductId = $RealProduct->getId();
            // Si l activatedMovt possede deja un ExecutedMovt associe
            if ($InitialState == ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT) {
                $ExecutedMovement = $exmMapper->load(
                        array('ActivatedMovement' => (int)$xmlACM->id));
                if (Tools::isEmptyObject($ExecutedMovement)) {
                    $errors[] = $ErrorMsge . "Il n'a pas été trouvé de mouvement "
                            . "exécuté en base de donnée, alors que le mouvement "
                            . "a déjà été partiellement exécuté.</li>";
                    $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                    Database::connection()->rollbackTrans();
                    Database::connection()->commitTrans();
                    continue; // passage au mvt suivant
                }
                // qte totale mouvementee avant cette execution
                $InitialRealQuantity = $ExecutedMovement->getRealQuantity();
                $EnvisagedQuantity = Tools::getValueFromMacro(
                        $ActivatedMovement, '%ProductCommandItem.Quantity%') - $InitialRealQuantity;
                $Comment = $ExecutedMovement->getComment() . ' ' . $Comment;
                $ExecutedMovement->setComment($Comment);
                $ExecutedMovement->setState((int)$State);
                $logger->logData("Mise à jour de l'état de l'exm "
                        . $ExecutedMovement->getId() . ' à ' . $State);
            } else { // on cree un nouveau ExectdMovt
                $ExecutedMovement = $ActivatedMovement->createExecutedMovement(
                        0, $ProductId, $Comment, $State);
                $ExecutedMovement->setStartDate(clean((string)$xmlEXM->startdate));
                $InitialRealQuantity = 0;
                $EnvisagedQuantity = Tools::getValueFromMacro(
                        $ActivatedMovement, '%ProductCommandItem.Quantity%');
                $logger->logData("Création de l'exm " . $ExecutedMovement->getId()
                        . " avec l'état " . $State);
            }


            $ExecutedMovement->setEndDate(clean((string)$xmlEXM->enddate));
            $ExecutedMovement->save();
            $CumulatedQuantity = (float)$xmlEXM->quantity;
            if (!($xmlEXM->quantity instanceof SimpleXMLElement)) {
                $errors[] = $ErrorMsge . "La quantité mouvementée n'a pas été récupérée correctement.</li>";
                $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                Database::connection()->rollbackTrans();
                Database::connection()->commitTrans();
                continue; // passage au mvt suivant
            }

            //  Traitement des quantites par emplacement
            $numOfLEMCreated = 0;
            foreach ($xmlEXM->location as $xmlLoc) {
                if (!($xmlLoc->quantity instanceof SimpleXMLElement)) {
                    $errors[] = $ErrorMsge . "La quantité mouvementée n'a pas été récupérée correctement.</li>";
                    $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                    Database::connection()->rollbackTrans();
                    Database::connection()->commitTrans();
                    continue 2; // passage au mvt suivant
                }
                $RealQuantity = (float)$xmlLoc->quantity;
				$LocationRef = clean((string)$xmlLoc->reference);
				$LocationId  = clean((int)$xmlLoc->id);
				$LocationStoreId  = clean((int)$xmlLoc->storeid);
                $LocationFilter = array();
                if ($LocationId > 0) {
                    $LocationFilter['Id'] = $LocationId;
                } else {
                    $LocationFilter['Name'] = $LocationRef;
                    if ($LocationStoreId > 0) {
                        $LocationFilter['Store.Id'] = $LocationStoreId;
                    }
                }
                $Location = Object::load('Location', $LocationFilter);
                if (Tools::isEmptyObject($Location)) {
                    $errors[] = $ErrorMsge . "Il n'a pas été trouvé en base de donnée d'emplacement " . $LocationRef . '.</li>';
                    $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                    Database::connection()->rollbackTrans();
                    Database::connection()->commitTrans();
                    continue 2; // passage au mvt suivant
                }

                $LocationId = $Location->getId();

                // Mise à jour des LocationConcreteProduct et ConcreteProduct
                $LCPCollection = new Collection();
                $tracingMode = $RealProduct->getTracingMode();
                $CPParams['Product'] = $RealProduct;
                $LCPParams['Location'] = $Location;

                foreach ($xmlLoc->concreteproduct as $xmlCP) {
                    $SerialNumber = clean((string)$xmlCP->serialnumber);
                    $EndOfLifeDate = clean((string)$xmlCP->dlcdate);
                    $quantity = (float)$xmlCP->quantity;
                    $CPID = (int)$xmlCP->id;
                    if(!checkCPQuantity($CPID, $LocationId, $quantity)) {
                        $errors[] = $ErrorMsge . 'Quantité insuffisante en stock pour le produit ' . $SerialNumber . ".</li>";
                        continue; // passage au CP suivant
                    }
                    $CPParams['SerialNumber'] = $SerialNumber;
                    $CPParams['EndOfLifeDate'] = $EndOfLifeDate;
			        $LCPParams['LCPQuantity'] = $quantity;

			        $result = createOrUpdateLCP(
			             $tracingMode, $MovementType, $CPParams, $LCPParams);

                    $LCP = $result['LCP'];
                    $errorCode = $result['error'];
                    if($errorCode==0 && $LCP instanceof LocationConcreteProduct) {
                        $LCP->save();
                        $LCPCollection->setItem($LCP);
                        $logger->logData('Création du lcp ' . $LCP->getId());
                    } else {
                        $errors[] = $ErrorMsge . getErrorMessage($errorCode). ".</li>";
                        continue; // passage au CP suivant
                    }
                }  // foreach cp
                // fin maj des LCP

                //  Creation des LocationExecutedMovement
                $LEMParams = array('Location'=>$Location,
                    'ExecutedMovement'=>$ExecutedMovement->getId(),
                    'Quantity'=>$RealQuantity,
                    'Date'=>$ExecutedMovement->getEndDate(),
                    'Product'=>$RealProduct->getId());
                try {
                    $LocationExecutedMovement = createLocationExecutedMovement($LEMParams, null, false);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
                $numOfLEMCreated++;
                $logger->logData('Création du lem ' . $LocationExecutedMovement->getId());

                try {
                    // maj LEMCP
                    $LEMCPCollection = createLEMConcreteProduct(
                        $LocationExecutedMovement, $LCPCollection, $tracingMode, 0, null, false);
                    //  MAJ des LocationProductQuantities  */
                    $LocationProductQuantities = getLocationProductQuantities(
                        $RealProduct, $Location, $MvtTypeEntrieExit, null, false);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                // fx de checking du lpq
                if ($MvtTypeEntrieExit == MovementType::TYPE_EXIT) {
                    if (Tools::isEmptyObject($LocationProductQuantities)) {
                        $errors[] = $ErrorMsge . "Il n'a pas été trouvé en base de donnée de référence "
                            . $RealProduct->getBaseReference() . " à l'emplacement "
                            . $Location->getName() . ".</li>";
                        $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                        Database::connection()->rollbackTrans();
                        Database::connection()->commitTrans();
                        continue 2; // passage au mvt suivant
                    }
                    if($LocationProductQuantities->getRealQuantity() < $RealQuantity) {
                        $errors[] = $ErrorMsge . "Il n'a pas été trouvé en base de donnée de référence "
                                . $RealProduct->getBaseReference() . " à l'emplacement "
                                . $Location->getName() . " en quantité suffisante.</li>";
                        $AbortedActivatedMvtCollection->setItem($ActivatedMovement);
                        Database::connection()->rollbackTrans();
                        Database::connection()->commitTrans();
                        continue 2; // passage au mvt suivant
                    }
                }
                // fin fx checking lpq
                // maj qty du lpq
                $LPQid = $LocationProductQuantities->getId();
                try {
                    $LocationProductQuantities = updateLPQQuantity(
                        $LocationProductQuantities, $RealQuantity, $MvtTypeEntrieExit, null, false);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage(); 
                }
                if(!$LocationProductQuantities) {
                    $logger->logData("Suppression du lpq avec l'ID " . $LPQid);
                    continue;
                }
                $logger->logData("Sauvegarde du lpq avec l'ID " . $LocationProductQuantities->getId());
                unset($LocationProductQuantities, $LocationNode, $LocationExecutedMovement);
            }  // foreach locations

            $ExecutedMovement->setRealQuantity($InitialRealQuantity + $CumulatedQuantity);
            $ExecutedMovement->save();

            /* MAJ des VirtualQty et recup des infos de mail d'alerte eventuel */
            $AlertArray = $ExecutedMovement->setProductVirtualQuantity();

            // Verification que la qte totale de product en stock est superieure
            // a la Qte minimum autorisee
            // qte totale en stock de ce produit
            $TotalRealStockQuantity = $RealProduct->getRealQuantity();
            if ($TotalRealStockQuantity <= $RealProduct->getSellUnitMinimumStoredQuantity()) {
                $AlertArray[] = array(ALERT_STOCK_QR_MINI, $RealProduct);  // alerte Qte reelle
            }

			$partialMvtBodyAddon = $comment = '';

            // Si mouvement partiel, envoi d'une alerte
            if ($ExecutedMovement->getState() == ExecutedMovement::EXECUTE_PARTIELLEMENT) {
                /*// Le Product qui etait attendu
				$initProductId = Tools::getValueFromMacro($ActivatedMovement, '%ProductCommandItem.Product.Id%');
				$initProduct = Object::load('Product', $initProductId);*/
                if ($RealProduct->getId() != Tools::getValueFromMacro(
                $ActivatedMovement, '%ProductCommandItem.Product.Id%')) {
                    $partialMvtBodyAddon .= 'La référence attendue ('
					       . Tools::getValueFromMacro(
                                $ActivatedMovement, '%ProductCommandItem.Product.BaseReference%')
					       . ') a été remplacée par ' . $RealProduct->getBaseReference()
                           . '.';
                }
                if (is_string($ExecutedMovement->getComment())
                && $ExecutedMovement->getComment() != '') {
                    $comment = 'Commentaire: ' . $ExecutedMovement->getComment();
                }
				// ToDo: un array associatif serait plus clair...
                $AlertArray[] = array(ALERT_PARTIAL_MOVEMENT, $RealProduct,
                        $CumulatedQuantity, $EnvisagedQuantity, $partialMvtBodyAddon,
                        $comment);
            }
        }  // foreach de exm

        // Création d'un invoiceItem pour le commandItem
        if ($invoicePrinted == 1) {
            $invoiceItem = Object::load('InvoiceItem');
            $invoiceItem->setQuantity($CumulatedQuantity);
            $invoiceItem->setName(clean((string)$xmlCmditem->productname));
            $invoiceItem->setReference(clean((string)$xmlCmditem->productbasereference));
            $invoiceItem->setHanding(clean((string)$xmlCmditem->handing));
            $invoiceItem->setTva((float)$xmlCmditem->tvarate);
            $invoiceItem->setUnitPriceHT((float)$xmlCmditem->priceht);
            $invoiceItem->setInvoice($invoice);
            $invoiceItem->setActivatedMovement($ActivatedMovement);
            $invoiceItem->save();
            $logger->logData('Sauvegarde de l\'InvoiceItem d\'Id ' . $invoiceItem->getId());
        }
        /* on commite la transaction */
        $logger->logData("Fin de la transaction pour l'item");
        Database::connection()->commitTrans();
        unset($CommandItemNode, $ExecutedMovement, $PartialExecutedMovement, $ErrorMsge);
        $i++;
    } // foreach des CmdItems

    //  Edition de BL
    // il faut sauver un BL en bado uniquement s'il a été édité et si on a créé des LEM
    $blprinted = (int)$xmlCmd->blprinted;
    if ($blprinted == 1 && isset($numOfLEMCreated) && $numOfLEMCreated > 0) {
		$DeliveryOrderMapper = Mapper::singleton('DeliveryOrder');
		$DeliveryOrder = Object::load('DeliveryOrder');
		$DeliveryOrder->setDocumentNo(clean((string)$xmlCmd->blnumber));
		$DeliveryOrder->setEditionDate(clean((string)$xmlCmd->editiondate));
		$DeliveryOrder->setCommand($Command);
		$dm = $DeliveryOrder->findDocumentModel();
		$DeliveryOrder->setDocumentModel($dm);
		$DeliveryOrder->save();
    }

    // MAJ de l'etat de la cmde en fonction aussi des autres mvts lies a la Cmde
    // SI PAS MOUVEMENT INTERNE
    if (!in_array($ActivatedMovement->getTypeId(), array(ENTREE_INTERNE, SORTIE_INTERNE))) {
        $UpdateCommand = $Command->updateState();
    }

    $Command->save();
    $logger->logData("Mise à jour de l'état de la commande à "
            . getCommandStateToString($Command));
    if (Database::connection()->HasFailedTrans()) {
        Database::connection()->rollbackTrans();
        $errors[] = "<li>Erreur : La transaction MySQL a avorté. L'état des mouvements qui ont échoué n'a pu être mis à jour.</li>";
        $logger->logData("Erreur : La transaction MySQL pour la commande a avorté.");
    }
    Database::connection()->commitTrans();
    $logger->logData("Fin de la transaction");

    if (count($errors) > 0) {
        // Remise des ActivdMovt a l'etat A FAIRE pour ceux ayant avorte,
        // en dehors de la transaction
        if (isset($AbortedActivatedMvtCollection)
                && $AbortedActivatedMvtCollection instanceof Collection
                && ($AbortedActivatedMvtCollection->getCount() > 0)) {
            Database::connection()->beginTrans();
            for($i = 0; $i < $AbortedActivatedMvtCollection->getCount(); $i++) {
                $item = $AbortedActivatedMvtCollection->getItem($i);
                $item->setState(ActivatedMovement::CREE);
                $item->save();
            }
            if (Database::connection()->HasFailedTrans()) {
                Database::connection()->rollbackTrans();
                $errors[] = "<li>Erreur : La transaction MySQL a avorté. L'état des mouvements qui ont échoué n'a pu être mis à jour.</li>";
                $logger->logData("Erreur : La transaction MySQL pour la commande a avorté.");
            }
            Database::connection()->commitTrans();
        }
        $errorsListing = implode("", $errors);
        $logger->logData('Des erreurs sont présentes: ' . $ErrorMsgeHeader . $errorsListing . '</ul>');
        $logger->sendLog();
        return $ErrorMsgeHeader . $errorsListing . '</ul>';
    }
	else {  // s'il ne s'est pas produit d'erreur, on peut envoyer les mails d'alerte eventuels
        //  Envoi de mails d'alerte
        for ($i = 0;$i < count($AlertArray);$i++) {
			$Product = $AlertArray[$i][1];
            if (AlertSender::isStockAlert($AlertArray[$i][0])) {
                AlertSender::sendStockAlert($AlertArray[$i][0], $Product);
            } else {
			    $alert = Object::load('Alert', $AlertArray[$i][0]);
			    $cur = $Command->getCurrency();
			    $Destinator = $Command->getDestinator();
			    $curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';
                $params = array(
                    'ProductBaseReference' => (!empty($Product))?$Product->getBaseReference():'',
                    'ProductMinimumStock' => (!empty($Product))?
                        $Product->getSellUnitMinimumStoredQuantity():'',
			        'ProductName' => (!empty($Product))?$Product->getName():'',
                    'ProductSupplierName' => (!empty($Product))?
                        Tools::getValueFromMacro($Product, '%MainSupplier.Name%'):'',
				    'TotalRealQuantity' => (isset($AlertArray[$i][2]))?$AlertArray[$i][2]:0,
				    'EnvisagedQuantity' => (isset($AlertArray[$i][3]))?$AlertArray[$i][3]:0,
				    'NumCde' => $Command->getCommandNo(),
				    'PartialMvtBodyAddon' => (isset($AlertArray[$i][4]))?$AlertArray[$i][4]:'',
				    'Comment' => (isset($AlertArray[$i][5]))?$AlertArray[$i][5]:'',
                    'CustomerName'=>$Destinator->getName(),
                    'MaximumIncurse' =>(isset($sp))?$sp->getMaxIncur():'',
                    'NumInvoice'=>(isset($invoice))?$invoice->getDocumentNo():'',
                    'UpdateIncurseWithCommand' =>(isset($sp))?$sp->getUpdateIncur():'',
                    'Currency' => TextTools::entityDecode($curStr)
			    );
			    $alert->prepare($params);
			    $alert->send();  // on envoie l'alerte
			    unset($alert);
            }
			$logger->logData("Mail d'alerte envoyé.");
        }
        $logger->sendLog();
        return true; // "Récupération OK par le serveur des données pour la commande No "
        // . $Command -> getCommandNo() . "\n";
    }
}


/**
 * Classe de debogage, loggue en détail ce que fait le script ci-dessus
 * et envoie un rapport à MAIL_DEV
 *
 * @access public
 */
class Logger {
    /**
     * Constructor
     *
     * @access protected
     */
    function Logger($cmdref, $xmldata)
    {
        $this->_subject = sprintf("Rapport concernant l'intégration de la " .
			"commande %s renvoyée par le zaurus", $cmdref);
        $this->_body = sprintf("XML envoyé: \n============\n\n%s", $xmldata);
        $this->_destinators = MAIL_DEV;
    }

    /**
     *
     * @access public
     * @return void
     */
    function logData($data)
    {
        $this->_body .= "\n >>> " . $data;
    }

    /**
     *
     * @access public
     * @return void
     */
    function sendLog()
    {
        return MailTools::send($this->_destinators, $this->_subject, $this->_body);
    }
}

?>
