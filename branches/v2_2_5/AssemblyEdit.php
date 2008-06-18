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

require_once('config.inc.php');
require_once('AssemblyTools.php');
require_once('Objects/Task.inc.php');
require_once('Objects/Product.php');
require_once('Objects/ActivatedChainTask.php');
require_once('ProductionTaskValidationTools.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_SUPERVISOR,
						   UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_AERO_INSTRUCTOR,
						   UserAccount::PROFILE_AERO_CUSTOMER));

$snError = _('The SN "%s" is already assigned to a part.');
$snLotError = _('Incorrect SN provided ("%s"), please correct.');
$qtyError = _('Incorrect quantity provided: assembled part %s/%s.');
$invalidState = _('You must start or restart the selected task first.');
///$nAAError = _('Le nombre d\'assemblages est inférieur à celui prévu. Confirmez-vous?');
$returnURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ActivatedChainTaskList.php';
$url = $_SERVER['PHP_SELF'].'?ackId=' . $_REQUEST['ackId'];
//Database::connection()->debug = true;

SearchTools::prolongDataInSession();  // Conserve les criteres de rech dans ACKList

// Test sur l'Id de l'ActivatedChainTask
if (!isset($_REQUEST['ackId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
	exit;
}
$ack = Object::Load('ActivatedChainTask', $_REQUEST['ackId']);
if (Tools::isEmptyObject($ack) ||
    !in_array($ack->getTaskId(), array(TASK_ASSEMBLY, TASK_SUIVI_MATIERE))) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
  	exit;
}

// on est magnanimes pour les tâches suivi matière
if ($ack->getTaskId() == TASK_SUIVI_MATIERE) {
    $ack->setState(ActivatedChainTask::STATE_IN_PROGRESS);
}

// checks
// si assemblage et état non conforme -> erreur
if ($ack->getState() != ActivatedChainTask::STATE_IN_PROGRESS && !isset($_REQUEST['history'])) {
    Template::errorDialog($invalidState, $returnURL);
    exit(1);
}

$commandId = Tools::getValueFromMacro($ack,
        '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%');
$command = Object::load('Command', $commandId);
// Le composant en sortie d'assemblage
$component = $ack->getComponent();
$product = $component->getProduct();
$tracingMode = $product->getTracingMode();
$tmArray = Product::getTracingModeConstArray();
$tracingModeName = $tmArray[$tracingMode];
$hasTracingMode = ($tracingMode != Product::TRACINGMODE_None);
$mapper = Mapper::singleton('Component');
$childComponentCollection = $mapper->loadCollection(
		array('Parent' => $component->getId()));

$count = $childComponentCollection->getCount();
$cptCollection = new Collection();

// Le grid qui sera en boucle si plusieurs assemblages
$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;
$grid->withNoSortableColumn = true;

$grid->NewColumn('FieldMapper', _('Reference'),
        array('Macro' => '%Product.BaseReference%<input type="hidden" name="itemIds[]" value="%Id%" />'));
$grid->NewColumn('FieldMapperWithTranslation', _('Tracing mode'),
		array('Macro' => '%Product.TracingMode%', 'TranslationMap' => $tmArray));
$grid->NewColumn(
        'FieldMapperWithTranslation', _('SN/Lot'),
        array('Macro' =>'%Product.TracingMode%',
			  'TranslationMap' => array(0 =>
                    'aucun<input type="hidden" name="SerialNumber[]" value="##" />'),
			  'DefaultValue' => '<input type="text" name="SerialNumber[]" />'));
$grid->NewColumn(
        'FieldMapperWithTranslationExpression', _('Quantity'),
        array('Macro' =>'%Product.TracingMode%',
			  'TranslationMap' => array(
                    Product::TRACINGMODE_SN =>
                        '<input type="text" name="Quantity[]" value="1" '
                                . 'readonly="readonly" />%Product.MeasuringUnit%',
                    Product::TRACINGMODE_LOT => '<input type="text" name="Quantity[]" />'
                            . '%Product.MeasuringUnit%'),
			  'DefaultValue' => '<input type="text" name="Quantity[]" '
                    . 'value="%Quantity%" />%Product.MeasuringUnit%'));


// Le nombre d'assemblages a effectuer
$realQty = SearchTools::requestOrSessionExist('realQuantity');
$realQty = ($realQty === false)?$ack->getAssembledRealQuantity():$realQty;
$assemblyNb = ($realQty >0)?$realQty:$ack->getAssembledQuantity();

$qtyArray = SearchTools::requestOrSessionExist('Quantity');
$snArray = SearchTools::requestOrSessionExist('SerialNumber');
$gridResults = array();
$linesByAssembly = 0;  // Nombre de lignes de grid proposees par assemblage
$qtyArrayForDetail = array();
$linesByChildComponent = array();  // Nb de lignes de grid proposees par composant

/* Determination du nombre de lignes de Grid affichees par assemblage
   Construction du popup de detail et des hiddens des ComponentIds pour controle l 219
   ALGO:
   * si pas de tracingMode: 1 ligne
   * si au SN et getMeasuringUnit() == ''  (sellunitType n'est pas une unité de mesure):
        n lignes = Component.Quantity (cf nomenclature modèle)
   * sinon, LOT ou (SN et SellUnitType est une unite de mesure):
        n lignes = min (Qté attendue, nb de cp en stock)
*/
for($i = 0; $i < $count; $i++) {
	$ChildComponent = $childComponentCollection->getItem($i);
	$childProduct = $ChildComponent->getProduct();
	$ChildTracingMode = $childProduct->getTracingMode();
	if ($ChildTracingMode == 0) {  // Pas de TracingMode
	    $childComponentLineNb = 1;
	}
	else {
        if ($ChildTracingMode == Product::TRACINGMODE_SN
                && $childProduct->getMeasuringUnit() == '') {
            // Si SellUnitType n'est pas une unite de mesure
            $childComponentLineNb = $ChildComponent->getQuantity();
        }
        // Sinon, LOT ou (SN et SellUnitType est une unite de mesure)
        else {
            require_once('Objects/ConcreteProduct.php');
            $cpColl = $childProduct->getConcreteProductInStockCollection(
                1, array(ConcreteProduct::EN_MARCHE, ConcreteProduct::EN_REPARATION, ConcreteProduct::EN_STOCK, ConcreteProduct::EN_LOCATION),
                array('Id'));
            // Si la collection est vide, probleme!
            $childComponentLineNb = (Tools::isEmptyObject($cpColl))?
                1:min($ChildComponent->getQuantity(), $cpColl->getCount());
        }
    }
    $linesByAssembly += $childComponentLineNb;
    $linesByChildComponent[$i] = $childComponentLineNb;
    $qtyArrayForDetail[$childProduct->getBaseReference()] =
            $ChildComponent->getQuantity();
}

for($k = 0; $k < $assemblyNb; $k++) {
	$currentColl = new Collection();  // Collection courante
	for($i = 0; $i < $count; $i++) {
		$childComponent = $childComponentCollection->getItem($i);
		if ($linesByChildComponent[$i] > 0) {
		    for($j = 0; $j < $linesByChildComponent[$i]; $j++) {
                $currentColl->setItem($childComponent);
			}
			continue;
		}
		$currentColl->setItem($childComponent);
	}
	$gridResults[] = $grid->render($currentColl, false, array(),
			array('Product.BaseReference'=>SORT_ASC), 'GridLite.html');
}


//  Si on a clique sur OK apres saisie des donnees de l'ActivatedChainTask
if (isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1) {
	// Met les saisies en session en cas de retour apres erreur
	putInSession($assemblyNb);
	$assembledCPArray = array();
	$cpMapper = Mapper::singleton('ConcreteProduct');
    $ccpMapper = Mapper::singleton('ConcreteComponent');
    $ccpCollection = new Collection();

	Database::connection()->startTrans();
    // Si le produit en sorti n'a pas de TracingMode, on ne met
    // a jour que l'ActivatedChainTask, mais on controle qd meme les Qty

    // 1. Controle des Produits en sortie
	if ($tracingMode > 0) {
    	for($k = 0; $k < $assemblyNb; $k++) {
			// Si Product final est suivi au SN, on controle le SN saisi:
			// il ne doit pas exister de CP avec ce SN, lie au meme Product
            $cpExists = $cpMapper->alreadyExists(
                    array('SerialNumber' => $_REQUEST['SerialNumber_' . $k],
						  'Product' => $product->getId()));
			if ($cpExists) {
				if ($tracingMode == Product::TRACINGMODE_SN) {
					Template::errorDialog(sprintf($snError, $_REQUEST['SerialNumber_'.$k]), $url);
			        exit;
				}
                $concreteProduct = $cpMapper->load(
                        array('SerialNumber' => $_REQUEST['SerialNumber_' . $k],
                              'Product' => $product->getId()));
            }
            else {  // Si le ConcretePdt n'existe pas, on le cree
                $concreteProduct = Object::load('ConcreteProduct');
				$concreteProduct->setSerialNumber($_REQUEST['SerialNumber_' . $k]);
				$concreteProduct->setProduct($product);
                // Cette FK n'est utilisee que si au SN
                if ($tracingMode == Product::TRACINGMODE_SN) {
                    $concreteProduct->setComponent($component);
                }
                saveInstance($concreteProduct, $returnURL);
            }
            // Si Level=0, on remplit cptHead si besoin
            if ($component->getLevel() == 0) {
                $concreteProduct = $cpMapper->load(
                        array('SerialNumber' => $_REQUEST['SerialNumber_' . $k],
                              'Product' => $product->getId()));
                $concreteProduct->addRemoveChild($concreteProduct->getId());
                saveInstance($concreteProduct, $returnURL);
            }
			$assembledCPArray[$k] = $concreteProduct;
		}
	}

	// 2. Controle des Produits en entree
    $ccpIds = $ack->getConcreteComponentCollectionIds();

    // Boucle sur les id de Component
	foreach($_REQUEST['itemIds'] as $i => $cptId) {
        if ($i == $linesByAssembly * $assemblyNb) {
            break;  // On se limite au nbre d'assemblage reellement validés
        }
		$cpt = Object::load('Component', $cptId);
        $childProduct = $cpt->getProduct();
        $childTracingMode = $childProduct->getTracingMode();

        // Pas de mode de suivi => pas de SN saisi, que la Quantity a verifier
		if ($childTracingMode == 0) {
			if ($cpt->getQuantity() != I18N::extractNumber($_REQUEST['Quantity'][$i])) {
				// Indice de la piece qui pose probleme
				$pieceNo = ceil(($i + 1) / $linesByAssembly);
			    Template::errorDialog(sprintf($qtyError, $pieceNo,
                        $ack->getAssembledQuantity()), $url);
                exit;
			}
            continue;
		}

        // else: mode de suivi
        if ($i % $linesByAssembly == 0) {
		    $qtyByComponent = array();  // Pour les Lots, et le controle des qty saisies
		}

        $sn = $_REQUEST['SerialNumber'][$i];

        if ($childTracingMode == Product::TRACINGMODE_SN) {
            // Le SN saisi est a verifier, contrairement a la Quantity bloquee a 1
            // Il doit exister, correspondre a un CP lie au bon Product, pas
            // etre deja implique dans un ConcreteComponent en tant que child
            // et avoir son State dans (ConcreteProduct::EN_STOCK, ConcreteProduct::EN_MARCHE)
            if ($sn == '') { // ->GetMessage()
                Template::errorDialog(_('Missing SN, please complete.'), $url);
                exit;
            }
            $test = concreteProductExists($sn, $childProduct);
            if (Tools::isException($test)) {
                Template::errorDialog(sprintf($test->getMessage(), $sn), $url);
                exit;
            }
        }  // /sn
        else { // $tracingMode == Product::TRACINGMODE_LOT
            $qty = I18N::extractNumber($_REQUEST['Quantity'][$i]);
            if ($qty != 0) {
            	// Le LOT saisi est a verifier, et la Quantity ou plutot somme des Qty
            	// Inutile de verifier ici si le CP est deja implique dans un ConcreteComponent
                // Pas de notion de ConcreteProduct.State non plus
                $cpExists = $cpMapper->alreadyExists(
                    array('SerialNumber' => $sn,
						  'Product' => $childProduct->getId()));
            	if (!$cpExists) {
            	    Template::errorDialog(sprintf($snLotError, $sn), $url);
            	    exit;
            	}
            }
            // Verification des Qty saisies
            $qtyByComponent[$cpt->getId()] = (isset($qtyByComponent[$cpt->getId()]))?
            		$qtyByComponent[$cpt->getId()] + $qty:$qty;

            // Si derniere ligne pour un des assemblages, on effectue le controle
            if ($i % $linesByAssembly == $linesByAssembly - 1) {
                foreach($qtyByComponent as $key => $value) {
                	$Compont = Object::load('Component', $key);
            		if ($value != $Compont->getQuantity()) {
            			// Indice de la piece qui pose probleme
            			$pieceNo = ceil(($i + 1) / $linesByAssembly);
            		    Template::errorDialog(
            					sprintf($qtyError, $pieceNo,
                                        $ack->getAssembledQuantity()),
            					$url);
                		exit;
            		}
                }
            }
            // Si qte=0, plus rien a faire pour cette ligne de saisie
            if ($qty == 0) {
                continue;
            }
        }  // /lot

        // Pour recuperer l'Id en base de donnees
        $persistantCP = $cpMapper->load(
        	array('SerialNumber' => $sn, 'Product' => $childProduct->getId()));

        // Creation du ConcreteComponent si n'existe pas deja
        // Patch ajouté pour nouvelle regle:
        // Un Product sans tracingMode, si au level 0 d'une nomenclature,
        // peut avoir un childComponent suivi au lot!!
        $parentCP = (!empty($assembledCPArray))?
                $assembledCPArray[floor($i / $linesByAssembly)]:0;
        $parentCPId = is_object($parentCP)?$parentCP->getId():0;

        if (!$ccpMapper->alreadyExists(
                array('ConcreteProduct' => $persistantCP->getId(),
                      'Parent' => $parentCPId,
                      'Quantity' => $qty))) {
            $ccp = Object::load('ConcreteComponent');
            $ccp->setParent($parentCP);
            $ccp->setConcreteProduct($persistantCP);
            $ccp->setQuantity($qty);
            saveInstance($ccp, $returnURL);
        }
        else {
            $ccp = $ccpMapper->load(
                    array('ConcreteProduct' => $persistantCP->getId(),
                          'Parent' => $parentCP->getId(),
                          'Quantity' => $qty));
        }

        // Si le composant est au SN, on affecte CP.Component
        if ($childTracingMode == Product::TRACINGMODE_SN) {
            $persistantCP->setComponent($cpt);
            saveInstance($persistantCP, $returnURL);
        }

        // Si Level=0, on remplit cptHead si besoin
        if ($component->getLevel() == 0) {
            $parentCP->addRemoveChild($persistantCP->getId());
            saveInstance($parentCP, $returnURL);
            // Remplit cptHead en cascade si le CP choisi resulte d'un assemblage
            // Recursion
        	$ccp->updateChildsHead($parentCP);
        }
        // Sert a remplir la table de liens entre CCP et ACK
        $ccpIds[] = $ccp->getId();
	}

    $date = date('Y-m-d H:i:s');
    $ack->setConcreteComponentCollectionIds($ccpIds);
	$ack->setEnd($date);
	$ack->setAssembledRealQuantity($assemblyNb);
    $ack->setRealQuantity($assemblyNb);
    $ack->setValidationUser($auth->getUser());
    // passe la tache à l'état terminé si c'est une tache suivi matière
    if ($ack->getTaskId() == TASK_SUIVI_MATIERE) {
        $ack->setRealEnd($date);
        $ack->setState(ActivatedChainTask::STATE_FINISHED);
    }
    saveInstance($ack, $returnURL);
	// Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
        exit;
    }
	Database::connection()->completeTrans();

	// Suppression des donnees en session et redirection
	unset($_SESSION['realQuantity'], $_SESSION['Quantity'], $_SESSION['SerialNumber']);
	for($i = 0; $i < $assemblyNb; $i++) {
		unset($_SESSION['SerialNumber_'. $i]);
	}
	Tools::redirectTo($returnURL);
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$smarty->assign('CommandNo', $command->getCommandNo());
$smarty->assign('BaseReference', $product->getBaseReference());
$smarty->assign('MeasuringUnit', $product->getMeasuringUnit());
$smarty->assign('TracingModeName', $tracingModeName);
$smarty->assign('HasTracingMode', $hasTracingMode);
$smarty->assign('TracingMode', $tracingMode);
$smarty->assign('Quantity', $ack->getAssembledQuantity());
$smarty->assign('realQuantity', $assemblyNb);
$smarty->assign('ccpGridArray', $gridResults);


require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('AssemblyEdit', 'post', $_SERVER['PHP_SELF']);
$form->updateAttributes(array('onsubmit' => "return checkBeforeSubmit();"));
$form->addElement('hidden', 'ackId', $_REQUEST['ackId']);

$pieces = array();
$sn = array();
for($i = 0; $i < $assemblyNb; $i++){
	$pieces[$i] = 1;
	// Si la piece fabriquee a un mode de suivi, il faut saisir un SN/Lot
	if ($product->getTracingMode() > 0) {
		$form->addElement('text', 'SerialNumber_'. $i);
	    $sn[] = "{\$form.SerialNumber_" . $i . ".html}";
	}
}
$defaultValues = $_SESSION;
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut

if ($ack->getTaskId() == TASK_ASSEMBLY) {
    $taskname = _('of assembly');
} else if ($ack->getTaskId() == TASK_SUIVI_MATIERE) {
    $taskname = _('of material tracking');
} else {
    $taskname = '';
}

$smarty->assign('TaskName', $taskname);
$smarty->assign('Pieces', $pieces);
$smarty->assign('SN', $sn);
$smarty->assign('returnURL', $returnURL);
$smarty->assign('cancelLabel', A_CANCEL);
$smarty->assign('qtyArrayForDetail', $qtyArrayForDetail);


$form->addElement('submit', 'submitForm', A_VALIDATE);
$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('ActivatedChainTask/AssemblyEdit.html');
Template::page(_('Validation ') . $taskname, $pageContent,
        array('js/includes/AssemblyEdit.js'));

?>
