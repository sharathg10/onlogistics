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
require_once('CommandManager.php');
require_once('LangTools.php');
includeRequirements();

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_CUSTOMER,
    UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES,
    UserAccount::PROFILE_OWNER_CUSTOMER, UserAccount::PROFILE_PRODUCT_MANAGER));

$consultingContext = in_array('consulting', Preferences::get('TradeContext', array()));
$customerProfiles  = array(UserAccount::PROFILE_CUSTOMER,UserAccount::PROFILE_OWNER_CUSTOMER);

/**
 * Prolonge les datas de recherche en session: param 1 pour conserver les cases
 * cochees ds CustomerCatalog en cas de retour apres annulation
 * et met en session les saisies pour retour apres erreur
 */
//SearchTools::prolongDataInSession(1);
SearchTools::inputDataInSession(1, '', false);

// Détermination du type de commande
$commandType = Command::TYPE_CUSTOMER;

//Database::connection()->debug=true;
// Controles
checkBeforeDisplay($commandType);

$customer = Object::load('Actor', $_SESSION['customer']);
$session = Session::Singleton();
$session->prolong('customer', 3);
$pdtVarName = getProductSessionVarName($commandType);
$session->prolong($pdtVarName, 3);
$currency = $customer->getCurrency();
$curID  = $currency instanceof Currency?$currency->getId():1;
$curStr = $currency instanceof Currency?$currency->getSymbol():'&euro;';
$catalogPage = getReturnURL($commandType);

// gestion du suppliercustomer
$SupplierCustomer = findSupplierCustomer($auth->getActor(), $customer);

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
// L'expediteur: un choix d'acteurs des chaines activées par les produits
// Le destinataire: le client selectionne
try {
    $expeditorList = getExpeditorList($pdtCollection, $customer);
} catch (Exception $e) {
    Template::errorDialog($e->getMessage(), $catalogPage);
    exit(1);
}
$expeditorArray = SearchTools::createArrayIDFromCollection(
    'Actor', array('Id' => $expeditorList), '', 'Name');
$form->addElement('select', 'cmdExpeditor', _('Shipper'),
    $expeditorArray,
    'onchange="changeExpeditor();" id="cmdExpeditor" style="width:100%"');

$destinatorArray = SearchTools::createArrayIDFromCollection(
    'Actor', array('Id' => $customer->getId()), '', 'Name');
$form->addElement('select', 'cmdDestinator', _('Addressee'),
    $destinatorArray, 'id="cmdDestinator" style="width:100%"');
$form->addElement('select', 'cmdExpeditorSite', _('Shipper site'),
        array(), 'id="cmdExpeditorSite" style="width:80%"');
$custSiteArray = SearchTools::createArrayIDFromCollection(
    'Site',
    array(
        'Owner' => $customer->getId(),
        'Type'  => array(Site::SITE_TYPE_LIVRAISON, Site::SITE_TYPE_FACTURATION_LIVRAISON)
    ),
    '',
    'Name'
);
$form->addElement('select', 'cmdDestinatorSite', _('Addressee site'),
    $custSiteArray,
    'onchange="changeExpeditor();" id="cmdDestinatorSite" style="width:80%"');
$commercialArray = SearchTools::createArrayIDFromCollection(
        'UserAccount', array('Profile' => UserAccount::PROFILE_COMMERCIAL),
        _('None'), 'Identity');
$commercialSelect = $form->addElement('select', 'cmdCommercial', _('Salesman'),
        $commercialArray, 'style="width:100%"');
$Commercial = $customer->getCommercial();
if ($Commercial instanceof UserAccount) {
    $commercialSelect->setValue($Commercial->getId());
}

// en contexte consulting on rajoute un select des chefs de projets
if ($consultingContext) {
    $projectManagerArray = SearchTools::createArrayIDFromCollection(
        'ProjectManager', array('Generic' => 0), _('None'), 'Name');
    $form->addElement('select', 'cmdProjectManager', _('Project manager'),
        $projectManagerArray, 'style="width:80%"');
}

$onKeyUp = 'onKeyUp="RecalculateTotal();"';
if (!in_array($auth->getProfile(), $customerProfiles)) {
    $form->addElement('text', 'Port', _('Forwarding charges'), $onKeyUp);
    $form->addElement('text', 'Emballage', _('Handling charges'), $onKeyUp);
    $form->addElement('text', 'Assurance', _('Insurances charges'), $onKeyUp);
    $form->addElement('text', 'GlobalHanding', _('Global discount'),
        'onKeyUp="RecalculateTotal(true);"'); // true pour recalculer les lignes
}
$form->addElement('text', 'Instalment', _('Instalment'),
        'onKeyUp="RecalculateToPay();RecalculateUpdateIncur();" '
        . 'style="width:80%" class="FieldWhite" value="0"');

$form->addElement('select', 'InstalmentModality', 
    _('Instalment').' - '._('Means of payment'), 
    TermsOfPaymentItem::getPaymentModalityConstArray(), 'style="width:80%"');

$form->addElement('textarea', 'cmdComment', _('Comment'), 'cols="100%" rows="3"');

// Site expediteur: par defaut le MainSite de Expeditor est selectionne
// Expeditor etant celui selectionne, c'est a dire le 1er de la liste
// Site destinataire: par defaut le MainSite de Destinator est selectionne
$selExpeditor = Object::load('Actor', $expeditorList[0]);
$defaultValue =  array(
        'cmdExpeditorSite' => $selExpeditor->getMainSiteId(),
        'cmdDestinatorSite' => $customer->getMainSiteId()
);
// Construit a partir de $_REQUEST, si retour apres erreur
$defaultValue = array_merge($defaultValue, SearchTools::createDefaultValueArray());
if (!isset($defaultValue['cmdIncoterm'])) {
    $defaultValue['cmdIncoterm'] = $customer->getIncotermID();
}

$form->setDefaults($defaultValue);
$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
// /form

// Le Grid
$grid = new Grid();
$grid->withNoSortableColumn = true;
// Construction de la grille
$grid->NewColumn('ProductReference', _('Reference'),
        array('cmdType'=>Command::TYPE_CUSTOMER, 'cmdDestinator' => $customer));
$grid->NewColumn('FieldMapper', _('Name'),
    array('Macro' => '<input type="hidden" name="ProductId[]" value="%Id%"/>%Name%'));
$grid->NewColumn('ProductCommandPriceWithDiscount', _('Basis excl. VAT ') . $curStr,
        array('actor' => $customer));
$grid->NewColumn('ProductCommandTVA', _('% VAT'),
        array('SupplierCustomer' => $SupplierCustomer));
// Selon l'etat de la preference, on affiche ou pas une colonne pour la qte d'UE
$qtyCaption = (Preferences::get('ProductCommandUEQty'))?
        _('Qty pack.') . '&nbsp;&nbsp;' . _('Qty'):_('Quantity');
$grid->NewColumn('ProductCommandQuantity', $qtyCaption);
$grid->NewColumn('ProductCommandMinimumQuantity', _('Minimum qty'),
        array('customer' => $customer));
$grid->NewColumn('ProductCommandPromotion', _('Offer on sale'), array('customer' => $customer));
if (!in_array($auth->getProfile(), $customerProfiles)) {
    $grid->NewColumn('FieldMapper', _('Discount'),
        array('Macro' => '<input type="text" name="hdg[]" value="" size="5" '
                       . 'onKeyUp="RecalculateItemTotal(getItemIndex(this));'
                       . 'RecalculateTotal();" />'));
}
$grid->NewColumn('ProductCommandPriceWithHanding',
        sprintf(_('%s price excl. VAT'), $curStr), array('actor' => $customer));
$grid->NewColumn('FieldMapper', sprintf(_('Total price excl. VAT %s'), $curStr),
        array('Macro' => '<input type="text" name="PTHT[]" size="10" '
                       . 'class="ReadOnlyField FieldBold" readonly="readonly" />'));
if (isset($_REQUEST['cadencedOrder']) && $_REQUEST['cadencedOrder'] == 1) {
    $grid->NewColumn('FieldMapper', _('Wished date (dd/mm/yy hh:mm)'),
        array('Macro' => '<input type="text" name="CommandItemDate[]" size="15" '
            . 'disabled="disabled" />&nbsp;<input type="submit" '
            . 'name="AddCommandItem[]" '
            . 'onclick="document.forms[0].elements[\'ProductToAdd\'].value=\'%Id%\'" '
            . 'value="+" disabled="disabled" /><input type="hidden" name="hiddenIds[]" '
            . 'value="%Id%" />'));
}

$cmdProductGrid = $grid->render($pdtCollection, false, array(),
        array('BaseReference' => SORT_ASC), 'Product/ProductGrid.html');


if (isset($_REQUEST['cadencedOrder'])) {
    $smarty->assign('cadencedOrder', $_REQUEST['cadencedOrder']);
}
$smarty->assign('consultingContext', $consultingContext);
$smarty->assign('cadencedOrderChecked', isset($_REQUEST['cadencedOrderCB']));
$smarty->assign('Currency', $curStr);
$smarty->assign('returnURL', $catalogPage);
$smarty->assign('commandType', $commandType);
$smarty->assign('PlanningComment', $customer->getPlanningComment());
$smarty->assign('RemExcep', $customer->GetRemExcep());
$smarty->assign('DisplayRemExcep', 'block');
$DeliveryZoneLabel = Tools::getValueFromMacro($customer, '%MainSite.Zone.Name%');
if ($DeliveryZoneLabel != 'N/A') {
 $smarty->assign('DeliveryZoneLabel', $DeliveryZoneLabel);
}
$smarty->assign('cmdProductGrid', $cmdProductGrid);
$smarty->assign('customerId', $customer->getId());

// On peut venir de la liste des devis ou du catalog client
if (isset($_REQUEST['from']) && $_REQUEST['from'] == 'estimate') {
    $est = Object::load('Command', $_SESSION['fromEstimateId']) ;
    $from = 'estimate';
    restoreDates($smarty, 
        $est->getWishedEndDate()>0, 
        $est->getWishedStartDate(), 
        $est->getWishedEndDate()
    );
} else {
    $from = '';
    restoreDates(
        $smarty,
        isset($_REQUEST['WishedDate'])?$_REQUEST['WishedDate']:'',
        isset($_REQUEST['StartDate'])?$_REQUEST['StartDate']:time(),
        isset($_REQUEST['EndDate'])?$_REQUEST['EndDate']:time()
    );
}

$smarty->assign('from', $from);

// Pour les saisies de qte d'UE
$smarty->assign('ueQtyPref', (Preferences::get('ProductCommandUEQty'))?1:0);
if (isset($_REQUEST['isEstimate'])) {
    $smarty->assign('isEstimate', true);
}

// Affichage du seuil mini HT de commande autorise, si profile 'client'
if (in_array($auth->getProfile(), $customerProfiles)) {
    // Pour mettre dans un hidden, pour le controle js
    $smarty->assign('MiniAmountToOrder', $customer->getMiniAmountToOrder($currency));
    // Pour l'affichage de l'info
    $smarty->assign('FormattedMiniAmountToOrder', 
            I18N::formatNumber($customer->getMiniAmountToOrder($currency)));
}

// Attention: number_format(NULL) = 0 !!, d'ou le test suivant
$MaxIncur = (is_null($SupplierCustomer->getMaxIncur()))?
    _('Undefined'):I18N::formatNumber($SupplierCustomer->getMaxIncur());
$smarty->assign('MaxIncur', $MaxIncur);
$smarty->assign('CalendarAwareOfPlanning', Preferences::get('CalendarAwareOfPlanning', 0));
$smarty->assign('UpdateIncur',
    I18N::formatNumber($SupplierCustomer->getUpdateIncur()));
$top = $SupplierCustomer->getTermsOfPayment();
if ($top instanceof TermsOfPayment) {
    $smarty->assign('TermsOfPayment', $top->getName());
}
if ($SupplierCustomer->getHasTVA()) {
    $smarty->assign('port_tva_rate', getTVARateByCategory(TVA::TYPE_DELIVERY_EXPENSES));
    $smarty->assign('packing_tva_rate', getTVARateByCategory(TVA::TYPE_PACKING));
    $smarty->assign('insurance_tva_rate', getTVARateByCategory(TVA::TYPE_INSURANCE));
}
// remise client par % du ca annuel
if ($SupplierCustomer->getAnnualTurnoverDiscountPercent()) {
    $smarty->assign('AnnualTurnoverDiscount',
        I18N::formatNumber($SupplierCustomer->getAnnualTurnoverDiscountTotal()));
}

$JSRequirements = array(
        'JS_AjaxTools.php',
        'js/lib-functions/FormatNumber.js',
        'js/includes/ProductCommand.js', 'js/jscalendar/calendar.js',
        getJSCalendarLangFile(), 'js/jscalendar/calendar-setup.js');

Template::page('', $smarty->fetch('Product/ProductCommand.html'), $JSRequirements);
?>
