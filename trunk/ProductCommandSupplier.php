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
require_once('ProductCommandTools.php');
require_once('LangTools.php');
includeRequirements();

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                           UserAccount::PROFILE_AERO_ADMIN_VENTES));

/**
 * Prolonge les datas de recherche en session: param 1 pour conserver les cases
 * cochees ds le Catalog en cas de retour apres annulation
 * et met en session les saisies pour retour apres erreur
 **/
///SearchTools::prolongDataInSession(1);
SearchTools::inputDataInSession(1, '', false);
// Provenance: SupplierCatalog ou SupplyingOptimization
$fromOptimappro = (isset($_REQUEST['from']) && $_REQUEST['from'] == 'optimappro');

// Détermination du type de commande
$commandType = Command::TYPE_SUPPLIER;
// On peut venir de la liste des devis ou du catalog fournisseur, ou d'optimappro
if ($fromOptimappro) {
    $from = 'optimappro';
} elseif (isset($_REQUEST['from']) && $_REQUEST['from'] == 'estimate') {
    $from = 'estimate';
} else {
    $from = '';
}
$catalogPage = getReturnURL($commandType);


// Controles
checkBeforeDisplay($commandType);

$Supplier = Object::load('Actor', $_SESSION['supplier']);
$session = Session::Singleton();
$session->prolong('supplier', 3);
$pdtVarName = getProductSessionVarName($commandType);
$session->prolong($pdtVarName, 3);
$currency = $Supplier->getCurrency();
$curID  = $currency instanceof Currency?$currency->getId():1;
$curStr = $currency instanceof Currency?$currency->getSymbol():'&euro;';

// Gestion du SupplierCustomer
$SupplierCustomer = findSupplierCustomer($Supplier->getId(), $auth->getActorId());

$pdtVarName = getProductSessionVarName($commandType);
// ajout éventuel d'un commanditem
addProduct($pdtVarName);

// Les ids de product concernes sont en session
$pdtIds = $_SESSION[$pdtVarName];
$pdtMapper = Mapper::singleton('Product');

$pdtCollection = new Collection();
foreach ($pdtIds as $id) {
    $pdt = Object::load('Product', array('Id'=>$id));
    $pdtCollection->setItem($pdt);
}



// Suppression des produits sélectionnés si necessaire
deleteProducts($pdtCollection, $commandType);

// Validation des données du formulaire Si envoi du FORM
if (isset($_POST['commandButton']) || isset($_POST['estimateButton'])) {
    checkBeforeSubmit($commandType);
    // Si c'est un devis a passer en commande, il va falloir supprimer les acm, 
    // maj les qv...
    $fromEstimateId = (isset($_SESSION['fromEstimateId']))?$_SESSION['fromEstimateId']:'0';
    $command = handleCommand($commandType, $pdtCollection, $curID, 
        isset($_POST['estimateButton']), $fromEstimateId);
    SearchTools::cleanDataSession('noPrefix');
    // redirect vers le catalogue
    if (!DEBUG) {
        $msg = sprintf(
            I_COMMAND_OK . '.',
            $command->getIsEstimate() ? _('estimate') : _('order'),
            $command->getCommandNo()
        );
        $hbr = $command->getHandingByRangePercent();
        if ($hbr > 0) {
            $msg .= "<br/>" . sprintf(I_COMMAND_HANDING, $hbr);
        }
        if ($command->getIsEstimate()) {
            $catalogPage .= '&editEstimate=1&estId='.$command->getId();
        }
        Template::infoDialog($msg, $catalogPage);
    }
    exit;
}
// /submit

// Formulaire de saisie
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm($_SERVER['PHP_SELF'], 'post');


$form->addElement('text', 'cmdNumber', _('Order number'), 'style="width:100%"');
$IncotermArray = SearchTools::createArrayIDFromCollection('Incoterm', array(), '', 'Label');
$form->addElement('select', 'cmdIncoterm', _('Incoterm'),
        $IncotermArray, 'style="width:100%"');
// L'expéditeur: le fournisseur selectionné
// Le destinataire: un choix d'acteurs des chaines activees par les produits
$expeditorArray = SearchTools::createArrayIDFromCollection(
        'Actor', array('Id' => $Supplier->getId()), '', 'Name');
$form->addElement('select', 'cmdExpeditor', _('Shipper'),
        $expeditorArray, 'id="cmdExpeditor" style="width:100%"');
// onchange inutile, car un seul Actor dans la liste!
// 'onchange="fw.ajax.updateSelect(\'cmdExpeditor\', \'expeditorSite\', \'Site\', \'Owner\');" '
try {
    $destinatorList = getDestinatorList($pdtCollection, $Supplier);
} catch (Exception $exc) {
    Template::errorDialog($exc->getMessage(), $catalogPage . '&formSubmitted=1');
    exit(1);
}

$destinatorArray = SearchTools::createArrayIDFromCollection(
        'Actor', array('Id' => $destinatorList), '', 'Name');
$form->addElement('select', 'cmdDestinator', _('Addressee'),
        $destinatorArray, 'style="width:100%" id="cmdDestinator"');
$supplSiteArray = SearchTools::createArrayIDFromCollection(
        'Site', array('Owner' => $Supplier->getId()), '', 'Name');

$form->addElement('select', 'cmdExpeditorSite', _('Shipper site'),
    $supplSiteArray,
    'onchange="preselectDestinatorSite();" id="cmdExpeditorSite" style="width:80%"');
$custSiteArray = SearchTools::createArrayIDFromCollection(
        'Site', array('Owner' => $destinatorList), '', 'Name');
$form->addElement('select', 'cmdDestinatorSite', _('Addressee site'),
        $custSiteArray, 'style="width:80%" id="cmdDestinatorSite"');

$onKeyUp = 'onkeyup="RecalculateTotal();"';
$form->addElement('text', 'Port', _('Forwarding charges'), $onKeyUp);
$form->addElement('text', 'Emballage', _('Handling charges'), $onKeyUp);
$form->addElement('text', 'Assurance', _('Insurances charges'), $onKeyUp);
$form->addElement('text', 'GlobalHanding', _('Global discount'),
        'onKeyUp="RecalculateTotal(true);"'); // true pour recalculer les lignes
$form->addElement('text', 'Instalment', _('Instalment'),
        'onKeyUp="RecalculateToPay();RecalculateUpdateIncur();" '
        . 'style="width:80%" class="FieldWhite" value="0"');
$form->addElement('select', 'InstalmentModality', 
    _('Instalment').' - '._('Means of payment'), 
    Instalment::getModalityConstArray(), 'style="width:80%"');

$form->addElement('textarea', 'cmdComment', _('Comment'), 'cols="100%" rows="3"');

// Site expediteur: par defaut le MainSite de Expeditor est selectionne
// Site destinataire: par defaut le MainSite de Destinator est selectionne
// Destinator etant celui selectionne, c'est a dire le 1er de la liste
$selDestinator = Object::load('Actor', $destinatorList[0]);
$defaultValue =  array('cmdExpeditorSite' => $Supplier->getMainSiteId(),
        'cmdDestinatorSite' => $selDestinator->getMainSiteId());

// A partir de $_REQUEST, si retour apres erreur
$defaultValue = array_merge($defaultValue, SearchTools::createDefaultValueArray());
if (!isset($defaultValue['cmdIncoterm'])) {
    $defaultValue['cmdIncoterm'] = $auth->getActor()->getIncotermID();
}

$form->setDefaults($defaultValue);
$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
// /form

// Le Grid
$grid = new Grid();
$grid->withNoSortableColumn = true;
// Construction de la grille
$grid->NewColumn('ProductReference', _('Reference'), array('cmdType'=>Command::TYPE_SUPPLIER));
$grid->NewColumn('FieldMapper', _('Name'),
    array('Macro' => '<input type="hidden" name="ProductId[]" value="%Id%"/>%Name%'));
$grid->NewColumn('ProductCommandPriceWithHandingSupplier',
        sprintf(_('%s price excl. VAT'), $curStr), array('actor'=>$Supplier));
$grid->NewColumn('ProductCommandTVA', _('% VAT'),
        array('SupplierCustomer' => $SupplierCustomer));

$grid->NewColumn('ProductCommandQuantity', _('Quantity'), array('supplier' => $Supplier,
        'supplierCustomer' => $SupplierCustomer));
$grid->NewColumn('FieldMapper', _('Discount'),
        array('Macro' => '<input type="text" name="hdg[]" value="" '
                      . 'size="5" onkeyup="RecalculateItemTotal(getItemIndex(this));'
                      . 'RecalculateTotal()" />'));
$grid->NewColumn('FieldMapper', sprintf(_('Total price excl. VAT %s'), $curStr),
        array('Macro' => '<input type="text" name="PTHT[]" size="10" '
                      . 'class="ReadOnlyField FieldBold" readonly="readonly" />'
                      . '<input type="hidden" name="HiddenNumberUBInUV[]" '
                      . 'value="%NumberUBInUV%" />'));
if (isset($_REQUEST['cadencedOrder']) && $_REQUEST['cadencedOrder'] == 1) {
    $grid->NewColumn('FieldMapper', _('Wished date (dd/mm/yy hh:mm)'),
        array('Macro' => '<input type="text" name="CommandItemDate[]" size="15" '
            . 'disabled="disabled" />&nbsp;<input type="submit" '
            . 'name="AddCommandItem[]" '
            . 'onclick="document.forms[0].elements[\'ProductToAdd\'].value=\'%Id%\'" '
            . 'value="+" disabled="disabled" /><input type="hidden" name="hiddenIds[]" '
            . 'value="%Id%" />'));
}

$cmdProductGrid = $grid->render($pdtCollection, false, array(), array(),
        'Product/ProductGrid.html');

if (isset($_REQUEST['cadencedOrder'])) {
    $smarty->assign('cadencedOrder', $_REQUEST['cadencedOrder']);
}
$smarty->assign('cadencedOrderChecked', isset($_REQUEST['cadencedOrderCB']));
$smarty->assign('Currency', $curStr);
$smarty->assign('from', $from);
$smarty->assign('returnURL', $catalogPage . '?formSubmitted=1');
$smarty->assign('commandType', $commandType);
$smarty->assign('PlanningComment', $Supplier->getPlanningComment());
$smarty->assign('cmdProductGrid', $cmdProductGrid);
if (isset($_REQUEST['isEstimate'])) {
    $smarty->assign('isEstimate', true);
}

// date de début et de fin
restoreDates(
    $smarty,
    isset($_REQUEST['WishedDate'])?$_REQUEST['WishedDate']:'',
    isset($_REQUEST['StartDate'])?$_REQUEST['StartDate']:time(),
    isset($_REQUEST['EndDate'])?$_REQUEST['EndDate']:time()
);


// L'encours...
if (isset($SupplierCustomer) && $SupplierCustomer instanceof SupplierCustomer) {
    // Attention: number_format(NULL) = 0 !!, d'ou le test suivant
    $MaxIncur = (is_null($SupplierCustomer->getMaxIncur()))?
        _('Undefined'):I18N::formatNumber($SupplierCustomer->getMaxIncur());
    $smarty->assign('MaxIncur', $MaxIncur);
     $smarty->assign('UpdateIncur',
            I18N::formatNumber($SupplierCustomer->getUpdateIncur()));
    if ($SupplierCustomer->getHasTVA()) {
        $smarty->assign('port_tva_rate', getTVARateByCategory(TVA::TYPE_DELIVERY_EXPENSES));
        $smarty->assign('packing_tva_rate', getTVARateByCategory(TVA::TYPE_PACKING));
        $smarty->assign('insurance_tva_rate', getTVARateByCategory(TVA::TYPE_INSURANCE));
    }
}

$JSRequirements = array(
        'JS_AjaxTools.php',
        'js/lib-functions/FormatNumber.js',
        'js/includes/ProductCommand.js', 'js/jscalendar/calendar.js',
        getJSCalendarLangFile(), 'js/jscalendar/calendar-setup.js');

Template::page('', $smarty->fetch('Product/ProductCommand.html'), $JSRequirements);

?>
