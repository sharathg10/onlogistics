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
$pageTitle = _('Material tracking details');

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
// Le composant en sortie d'assemblage
$component = $ack->getComponent();
$nomenclature = $component->getNomenclature();
$commandNo = Tools::getValueFromMacro($ack,
        '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%');
$product = $component->getProduct();
$tracingMode = $product->getTracingMode();
$tmArray = Product::getTracingModeConstArray();
$tracingModeName = $tmArray[$tracingMode];

// Ici, les ccp.Parent sont nuls!
$ccpColl = $ack->getConcreteComponentCollection(
        array(), array('ConcreteProduct.Product.BaseReference' => SORT_ASC)
);


// Le grid qui sera en boucle si plusieurs assemblages
$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;
$grid->withNoSortableColumn = true;

$grid->NewColumn('FieldMapper', _('Reference'),
        array('Macro' => '%ConcreteProduct.Product.BaseReference%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Tracing mode'),
		array('Macro' => '%ConcreteProduct.Product.TracingMode%',
              'TranslationMap' => $tmArray));
$grid->NewColumn('FieldMapper', _('SN/Lot'),
        array('Macro' => '%ConcreteProduct.SerialNumber%'));
$grid->NewColumn('FieldMapper', _('Quantity'),
        array('Macro' => '%Quantity% %ConcreteProduct.Product.MeasuringUnit%'));

$gridContent = $grid->render($ccpColl, false, array(),
		array('ConcreteProductProduct.BaseReference'=>SORT_ASC), 'GridLite.html');

/*  Formulaire */
$smarty = new Template();
$smarty->assign('CommandNo', $commandNo);
$smarty->assign('BaseReference', $product->getBaseReference());
$smarty->assign('MeasuringUnit', $product->getMeasuringUnit());
$smarty->assign('TracingModeName', $tracingModeName);
$smarty->assign('TracingMode', $tracingMode);
$smarty->assign('Quantity', $ack->getAssembledQuantity());
$smarty->assign('realQuantity', $assemblyNb);
$smarty->assign('returnURL', $returnURL);
$smarty->assign('gridContent', $gridContent);

$pageContent = $smarty->fetch('ActivatedChainTask/AssemblyDegradedModeDetail.html');
Template::page($pageTitle, $pageContent);

?>
