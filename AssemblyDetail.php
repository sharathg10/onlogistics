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

$returnURL = 'ActivatedChainTaskHistory.php';

SearchTools::prolongDataInSession();

// Test sur l'Id de l'ActivatedChainTask
if (!isset($_REQUEST['ackId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
	exit;
}
$ack = Object::Load('ActivatedChainTask', $_REQUEST['ackId']);
if (Tools::isEmptyObject($ack) || $ack->getState() != ActivatedChainTask::STATE_FINISHED
        || !in_array($ack->getTaskId(), array(TASK_ASSEMBLY, TASK_SUIVI_MATIERE))) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
  	exit;
}
// Le nombre d'assemblages effectues
$assemblyNb = $ack->getAssembledRealQuantity();
if ($assemblyNb == 0) {
    Template::errorDialog(_('Assembly has no registered item.'),
            $returnURL);
    exit;
}

// Le composant en sortie d'assemblage
$component = $ack->getComponent();
$nomenclature = $component->getNomenclature();
// Si: le Product head (level 0) a un tracingMode = 0 et la nomenclature
// associee possede un Component C de level 1 tel que tracingMode = LOT,
// redirection, car affichage en mode dégradé, car on n'a pas la traçabilité totale
if (!$nomenclature->levelTwoCanExist()) {
    Tools::redirectTo('AssemblyDegradedModeDetail.php?ackId=' . $_REQUEST['ackId']);
    exit;
}

$ccpColl = $ack->getConcreteComponentCollection(
        array(), array(
                'Parent.SerialNumber' => SORT_ASC,
                'ConcreteProduct.Product.BaseReference' => SORT_ASC)
);
// Les CP issus des assemblages, si mode de suivi
$cpColl = $ack->getAssembledConcreteProductCollection();

$commandNo = Tools::getValueFromMacro($ack,
        '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%');


$product = $component->getProduct();
$tracingMode = $product->getTracingMode();
$tmArray = Product::getTracingModeConstArray();
$tracingModeName = $tmArray[$tracingMode];
$mapper = Mapper::singleton('Component');
$childComponentCollection = $mapper->loadCollection(
		array('Parent' => $component->getId()));
$count = $childComponentCollection->getCount();

// Le grid qui sera en boucle si plusieurs assemblages
$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;
$grid->withNoSortableColumn = true;

$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%Product.BaseReference%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Tracing mode'),
		array('Macro' => '%Product.TracingMode%', 'TranslationMap' => $tmArray));
$grid->NewColumn('FieldMapper', _('SN/Lot'), array('Macro' => '%SerialNumber%'));
$grid->NewColumn('FieldMapper', _('Quantity'),
        array('Macro' => '%Quantity% %Product.MeasuringUnit%'));


$sn = array();
$pieces = array();

$assembledSNArray = array();  // Les SN resultats des assemblages
$ccpMapper = Mapper::singleton('ConcreteComponent');

for($k = 0; $k < $assemblyNb; $k++) {
    $pieces[$k] = 1;
    // Si pas de tracingMode
    // On affiche les donnees du modele, alors on n'a pas de ccp, ni cp
	if ((!$tracingMode) && $nomenclature->levelTwoCanExist()) {
        $currentColl = $childComponentCollection;
    }else {
        // Le CP en sortie
        if (Tools::isEmptyObject($cpColl)) {
            Template::errorDialog('TracingModes incohérents....', $returnURL);
            exit;
        }
        $assembledCP = $cpColl->getItem($k);
        if (Tools::isEmptyObject($assembledCP)) {
            Template::errorDialog('Erreur: on ne sauve pas assez d\'info lors des assemblages pour réafficher le détail....', $returnURL);
            exit;
        }
        $sn[] = $assembledCP->getSerialNumber();
        $currentColl = new Collection();

    	for($i = 0; $i < $count; $i++) {
    		$childComponent = $childComponentCollection->getItem($i);
            //Fonctionne car la collection est correctement triée
            $ccpCollection = $ccpMapper->loadCollection(
                    array('Id' => $ccpColl->getItemIds(),
                          'Parent' => $assembledCP->getId(),
                          'ConcreteProduct.Product' => $childComponent->getProductId()),
                    array('ConcreteProduct.SerialNumber' => SORT_ASC));

            if (!Tools::isEmptyObject($ccpCollection)) {
                $jcount = $ccpCollection->getCount();
                for($j = 0; $j < $jcount; $j++){
                    $ccp = $ccpCollection->getItem($j);
                    $childComponent->setQuantity($ccp->getQuantity());
                    // Un peu abusif, mais on affiche via un getter directement
                    //ds le grid...
                    $childComponent->SerialNumber = Tools::getValueFromMacro($ccp,
                            '%ConcreteProduct.SerialNumber%');
                    // Pour etre sûr de ne pas instancier plusieurs fois le meme
                    $ccpColl->removeItemById($ccp->getId());
                    $currentColl->setItem($childComponent);
                }
            }
    	}
    }
	$gridResults[] = $grid->render($currentColl, false, array(),
			array('Product.BaseReference'=>SORT_ASC), 'GridLite.html');
}



/*  Formulaire */
$smarty = new Template();
$smarty->assign('CommandNo', $commandNo);
$smarty->assign('BaseReference', $product->getBaseReference());
$smarty->assign('MeasuringUnit', $product->getMeasuringUnit());
$smarty->assign('TracingModeName', $tracingModeName);
$smarty->assign('TracingMode', $tracingMode);
$smarty->assign('Quantity', $ack->getAssembledQuantity());
$smarty->assign('realQuantity', $assemblyNb);
$smarty->assign('ccpGridArray', $gridResults);
$smarty->assign('returnURL', $returnURL);
$smarty->assign('cancelLabel', _('Back'));
// Utile car le même template est utilise pour 2 scripts
$smarty->assign('history', 1);
$smarty->assign('readonly', ' readonly="readonly"');

$taskname = ($ack->getTaskId() == TASK_ASSEMBLY)?_('of assembly'):_('of material tracking');
$smarty->assign('TaskName', $taskname);
$smarty->assign('Pieces', $pieces);
$smarty->assign('SN', $sn);

$pageContent = $smarty->fetch('ActivatedChainTask/AssemblyEdit.html');
Template::page(_('Details ') . $taskname, $pageContent,
        array('js/includes/AssemblyEdit.js'));

?>
