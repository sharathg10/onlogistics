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
require_once('Objects/ProductType.inc.php');
require_once('Objects/Command.const.php');
require_once('Objects/Command.php');
require_once('Objects/Product.inc.php');
require_once('lib-functions/ProductCommandTools.php');

$auth = Auth::Singleton();
$auth->checkProfiles();
//Database::connection()->debug = true;
$errorBody  = _('You have no catalogue defined, please contact your administrator.');

// Gestion de l'edition du devis si necessaire
// ouverture d'un popup en arriere-plan, impression du contenu (pdf), et fermeture de ce popup
if (isset($_REQUEST['editEstimate']) && isset($_REQUEST['estId'])) {
	$editEstimate = "
	<SCRIPT language=\"javascript\">
	function kill() {
		window.open(\"KillPopup.html\",'popback','width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no');
	}
	function TimeToKill(sec) {
		setTimeout(\"kill()\",sec*1000);
	}
	var w=window.open(\"EstimateEdit.php?estId=" . $_REQUEST['estId']
        . "\",\"popback\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
	w.blur();
	TimeToKill(12);
	</SCRIPT>";
} else {
    $editEstimate = '';
}

// On récupère le user connecté et son catalogue
$user = $auth->getUser();
$catalog = $user->getSupplierCatalog();
/**
 * Si pas de catalogue défini on renvoie un message d'erreur
 **/
if (!($catalog instanceof Catalog)) {
    Template::errorDialog($errorBody, 'home.php');
    exit;
}

// Si reinitialisation de toute la commande
if (isset($_REQUEST['new'])) {
    SearchTools::cleanDataSession('noPrefix');
}

// Sert a reafficher le bon grid en cas de retour en arriere depuis ProductCommand
if (isset($_REQUEST['supplier'])) {
    $supplierId = $_REQUEST['supplier'];
} else if (isset($_SESSION['supplier'])) {
    $supplierId = $_SESSION['supplier'];
}
$disabled = '';
if (isset($supplierId)) {
    $session = Session::Singleton();
    $session->register('supplier', $supplierId, 3);
    $disabled = 'disabled';
}


/*  Contruction du formulaire de recherche */
$form = new SearchForm('Product');
// Pas de bouton reset ici
$form->withResetButton = false;
$form->setQuickFormAttributes(
        array('name' => 'SupplierCatalog',
        'onsubmit'=>'return SupplierCatalogSubmit();'));
$SupplierArray = SearchTools::createArrayIDFromCollection(
        array('Supplier', 'AeroSupplier'),
        array('Active' => 1), _('Select a supplier'));
$form->addElement('select', 'supplier', _('Supplier'),
        array($SupplierArray, $disabled), array('Disable'=>true));
$form->addBlankElement();
$form->addAction(
    array(
        'URL' => 'SupplierCatalog.php?new=1',
        'Caption' => _('Reset all')
    )
);

$customArray = array(
    'SupplierReference'=>array(
        'Name'=>'SupplierReference',
        'SearchOptions'=>array(
            'Path'=>'ActorProduct().AssociatedProductReference',
            'Operator'=>'Like'
        )
    ),
    'BuyUnitType'=>array(
        'Name'=>'BuyUnitType',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::CreateArrayIDFromCollection('SellUnitType', array(),
                _('Select one or more items')),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'ActorProduct().BuyUnitType.Id',
            'Operator'=>'In'
        )
    )
);

$catalog->buildSearchForm($form, array('Supplier'), $customArray);
$defaultValues = SearchTools::DataInSessionToDisplay();

if (isset($_SESSION['supplier']) && $_SESSION['supplier'] != '##') {
    $ActorMapper = Mapper::singleton('Actor');
    $defaultValues = array_merge($defaultValues,
            array('supplier' => $_SESSION['supplier']));
    $supplierInstance = $ActorMapper->load(array('Id' => $_SESSION['supplier']));
    $form->setDefaultValues($defaultValues);
/*  Semble fonctionner avec la version 3.2.6 de HTML::QuickForm
    // XXX Patch pour la version 3.2.5 de HTML::QuickForm
    // cf. http://pear.php.net/bugs/bug.php?id=5251
    $qform  = $form->_form;
    $select = $qform->getElement('supplier');
    if ($select instanceof html_quickform_select) {
        $select->setSelected($_SESSION['supplier']);
    }*/
} else {
    $form->setDefaultValues(array('supplier' => 0));
}


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Filtre lie aux criteres de recherche; 1 pour conserver les selections
    $filterArray = $form->buildFilterComponentArray(1);
    // Pour mettre en session les quantites saisies si necessaire
    insertQtiesIntoSession();

    // Filtres par defaut
    // Filtre sur les types de produits du catalogue
    $ptypes = array_keys($catalog->getProductTypeList());
    $filterArray[] = SearchTools::NewFilterComponent('ProductType', '', 'In', $ptypes, 1);
    $filterArray[] = SearchTools::NewFilterComponent('Activated', '', 'Equals', 1, 1);
    $filterArray[] = SearchTools::NewFilterComponent('Affected', '', 'Equals', 1, 1);

    // Patch pour gerer correctement la ref achat:
    // il faut faire le lien avec le supplier
    // XXX le &&...SupplierReference pose pb quand simple recherche sur supplier
    if (isset($supplierId)) {// && SearchTools::requestOrSessionExist('SupplierReference')) {
        $filterArray[] = SearchTools::NewFilterComponent(
            'Supplier', 'ActorProduct().Actor', 'Equals', $supplierId, 1, 'Product');
    }

    $filter = SearchTools::filterAssembler($filterArray);
    $order = array('BaseReference' => SORT_ASC);

    $grid = $catalog->buildGrid(
        array(
            'SellUnitType' => array(
                'ColumnType' => 'ProductSellUnitTypeQuantities',
                'Sortable' => false
            ),
            'RealQuantity' => array(
                'ColumnType' => 'ProductRealQuantity',
                'Sortable' => false
            ),
            'UBPrice' => array(
                'ColumnType' => 'ProductUBPrice',
                'actor' => isset($supplierInstance)?$supplierInstance:0,
                'Sortable' => false
            ),
            'SellUnitVirtualQuantity' => array('Sortable' => false),
            'Price'=>array(
                'ColumnType'=>'ProductPrice',
                'actor' => isset($supplierInstance)?$supplierInstance:0,
                'Sortable' => false
            ),
            'Category' => array(
                'ColumnType' => 'FieldMapperWithTranslation',
                'TranslationMap' => getCategoryArray(),
                'Sortable' => false
            ),
            'Supplier'=>array(
                'ColumnMacro'=>'%MainSupplier.Name%',
                'Sortable' => false
            ),
            'SupplierReference'=>array(
                'ColumnType'=>'SupplierReference',
                'actor' => isset($supplierInstance)?$supplierInstance:0,
                'Sortable' => false
            ),
            'BuyUnitType'=>array(
                'ColumnType'=>'ProductBuyUnitTypeQuantities',
                'actor' => isset($supplierInstance)?$supplierInstance:0,
                'Sortable' => false
            ),
            'CustomerReference'=>array(
                'ColumnType'=>'CustomerReference',
                'Sortable' => false
            )
        )
    );
    if (Preferences::get('ProductCommandQtyInCatalog')) {
        $supplier = Object::load('Actor', $supplierId);
        $grid->NewColumn('ProductCommandQuantity', _('Sold by'),
            array('supplier' => $supplier, 'onlyBuyUnitQty' => true, 'Sortable' => false));
        $grid->newColumn('CatalogQuantity', _('Quantity'));
    }
    
    // Actions
    $grid->NewAction('Redirect', array(
            'Caption'=>_('Ask for estimate'),
            'Profiles'=>array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES),
            'TransmitedArrayName' => 'pdt',
            'URL'=>'ProductCommandSupplier.php?isEstimate=1&cadencedOrder='
                . $catalog->getCadencedOrder())
        );
    $grid->NewAction('Redirect', array(
            'Caption' => _('Order selected items'),
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                                UserAccount::PROFILE_AERO_ADMIN_VENTES),
            'TransmitedArrayName' => 'pdt',
            'URL'=>'ProductCommandSupplier.php?cadencedOrder='
                . $catalog->getCadencedOrder())
    );
    $form->displayResult($grid, true, $filter, $order, '',
        array('js/lib-functions/ClientCatalog.js'),
        array('beforeForm' => $editEstimate)
    );
}
else {
    // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('',
        $editEstimate . $form->render() . '</form>',
        array('js/lib-functions/ClientCatalog.js')
    );
}
?>
