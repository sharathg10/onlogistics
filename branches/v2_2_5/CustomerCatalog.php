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
require_once('Objects/Product.inc.php');
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');
require_once('lib-functions/ProductCommandTools.php');

$auth = Auth::Singleton();
$auth->checkProfiles();
$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();
$tradeContext = Preferences::get('TradeContext', array());
//Database::connection()->debug = true;


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

/**
 * Messages
 */
$errorBody = _('You have no catalogue defined, please contact your administrator.');

$smarty = new Template();
/**
 * On récupère le user connecté et son catalogue
 */
$user = $auth->getUser();
$catalog = $user->getCatalog();
/**
 * Si pas de catalogue défini on renvoie un message d'erreur
 */
if (!($catalog instanceof Catalog)) {
    Template::errorDialog($errorBody, 'home.php');
    exit;
}

// Si reinitialisation de toute la commande
if (isset($_REQUEST['new'])) {
    SearchTools::cleanDataSession('noPrefix');
}

/*  Pour la liste de selection des Clients  */
$customerSelectMsg = _('Select a customer');
$clientIsConnected = false;
if ($ProfileId == UserAccount::PROFILE_CUSTOMER || $ProfileId == UserAccount::PROFILE_OWNER_CUSTOMER) {
    $CustomerFilter = array('Id' => $UserConnectedActorId, 'Active' => 1);
    $customerSelectMsg = '';
    $clientIsConnected = true;
}
elseif ($ProfileId == UserAccount::PROFILE_COMMERCIAL){
    $CustomerFilter = array('Commercial' => $auth->getUserId(), 'Active' => 1);
}
else {
    $CustomerFilter = array('Active' => 1);
}
 // si on veut faire une nouvelle recherche, on vide ces var en session
if (isset($_REQUEST['search'])) {
    $griditems = SearchTools::getGridItemsSessionName();
    unset($_SESSION['formSubmitted'], $_SESSION['customer'],
          $_SESSION['gridItems'], $_SESSION[$griditems]);
}

if (isset($_REQUEST['CustomerSelected'])) {
    $customer = $_REQUEST['CustomerSelected'];
} else if (isset($_SESSION['customer'])) {
    $customer = $_SESSION['customer'];
} elseif ($clientIsConnected) {
    $customer = $auth->getActorId();
}
$disabled = '';
if (isset($customer)) {
    $session = Session::Singleton();
    $session->register('customer', $customer, 3);
    $disabled = 'disabled';
}

if (in_array('readytowear', $tradeContext)) {
    $entityName = 'RTWProduct';
} else {
    $entityName = 'Product';
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm($entityName);
// Pas de bouton reset ici
$form->withResetButton = false;
// Pour detecter le onchange sur le select des Customer
$displayGrid = false;
if (Preferences::get('MercurialForClient') &&
    false === SearchTools::requestOrSessionExist('customerChanged')) {
    $form->buildHiddenField(array('customerChanged' => 0));
    $onchange = ' onChange=this.form.elements["customerChanged"].value=1;this.form.submit();';
    if($clientIsConnected) {
        $displayGrid = true;
    }
}
else $onchange= '';

$form->setQuickFormAttributes(
        array('name' => 'ClientCatalog', 'onsubmit'=>'return WPCustomerListSubmit();')
);
$CustomerArray = SearchTools::createArrayIDFromCollection(
        array('Customer', 'AeroCustomer'), $CustomerFilter, $customerSelectMsg);
$form->addElement('select', 'CustomerSelected', _('Customer'),
        array($CustomerArray, $disabled . $onchange), array('Disable' => true));
$form->addBlankElement();
$form->addAction(
    array(
        'URL' => 'CustomerCatalog.php?new=1',
        'Caption' => _('Reset all')
    )
);

$customArray = array(
    'CustomerReference'=>array(
        'Name'=>'CustomerReference',
        'SearchOptions'=>array(
            'Path'=>'ActorProduct().AssociatedProductReference',
            'Operator'=>'Like'
        )
    ),
    'Supplier'=>array(
        'Name'=>'Supplier',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(array('Supplier', 'AeroSupplier'),
                array(), _('Select one or more items')),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'ActorProduct().Actor.Id',
            'Operator'=>'In'
        )
    ),
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
            SearchTools::createArrayIDFromCollection('SellUnitType', array(),
                _('Select one or more items')),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'ActorProduct().BuyUnitType.Id',
            'Operator'=>'In'
        )
    ),
    'Owner'=>array(
        'Name'=>'Owner',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(
                'Actor',
                array('Active'=>1, 'Generic'=>0),
                _('Select one or more items')
            ),
            'multiple, size="6"'
        )
    ),
);
if (in_array('readytowear', $tradeContext)) {
    $customArray['PressName'] = array(
        'Name'=>'PressName',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(
                'RTWPressName',
                array(),
                _('Select one or more items')
            ),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'Model.PressName.Id',
            'Operator'=>'In'
        )
    );
    $customArray['Material1'] = array(
        'Name'=>'Material1',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(
                'RTWMaterial',
                array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL),
                _('Select one or more items'),
                'CommercialNameAndColor'
            ),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'Model.Material1.Id',
            'Operator'=>'In'
        )
    );
    $customArray['Material2'] = array(
        'Name'=>'Material2',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(
                'RTWMaterial',
                array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL),
                _('Select one or more items'),
                'CommercialNameAndColor'
            ),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'Model.Material2.Id',
            'Operator'=>'In'
        )
    );
    $customArray['Accessory1'] = array(
        'Name'=>'Accessory1',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(
                'RTWMaterial',
                array('MaterialType' => RTWMaterial::TYPE_ACCESSORY),
                _('Select one or more items'),
                'CommercialNameAndColor'
            ),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'Model.Accessory1.Id',
            'Operator'=>'In'
        )
    );
    $customArray['Accessory2'] = array(
        'Name'=>'Accessory2',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(
                'RTWMaterial',
                array('MaterialType' => RTWMaterial::TYPE_ACCESSORY),
                _('Select one or more items'),
                'CommercialNameAndColor'
            ),
            'multiple, size="6"'
        ),
        'SearchOptions'=>array(
            'Path'=>'Model.Accessory2.Id',
            'Operator'=>'In'
        )
    );
}
$searchForm = $catalog->buildSearchForm($form, array(), $customArray);

if (in_array('readytowear', $tradeContext)) {
    /*
    $optionIds = SearchTools::createArrayIDFromCollection('RTWOption',
        array(), _('Select one or more items')
    );
    $searchForm->addElement('select', 'SpecificOptions',
        _('Specific options'),
        array($optionIds, 'multiple, size="6"'),
        array('Path' => 'Model.Option().Id')
    );
    */
}

$defaultValues = SearchTools::dataInSessionToDisplay();

if (isset($_SESSION['customer']) && $_SESSION['customer'] != '##') {
    $ActorMapper = Mapper::singleton('Actor');
    $defaultValues = array_merge($defaultValues,
            array('CustomerSelected' => $_SESSION['customer']));
    $customerInstance = $ActorMapper->load(
            array('Id' => $_SESSION['customer']));
    $form->setDefaultValues($defaultValues);
/* Semble fonctionner avec la version 3.2.6 de HTML::QuickForm
    // XXX Patch pour la version 3.2.5 de HTML::QuickForm
    // cf. http://pear.php.net/bugs/bug.php?id=5251
    $qform  = $form->_form;
    $select = $qform->getElement('CustomerSelected');
    if ($select instanceof html_quickform_select) {
        $select->setSelected($_SESSION['customer']);
    }*/
} else {
    $form->setDefaultValues(array('CustomerSelected' => 0));
}

/*  Affichage du Grid  */
if (true === $form->displayGrid($displayGrid)) {

    // filtres du form
    // 1 pour conserver les selections
    $filterArray = $form->buildFilterComponentArray(1);
    // Pour mettre en session les quantites saisies si necessaire
    insertQtiesIntoSession();

    // filtres de base
    // filtre sur les types de produits du catalogue
    $ptypes = array_keys($catalog->getProductTypeList());
    $filterArray[] = SearchTools::NewFilterComponent('ProductType', '', 'In', $ptypes, 1);
    $filterArray[] = SearchTools::NewFilterComponent('Activated', '', 'Equals', 1, 1);
    $filterArray[] = SearchTools::NewFilterComponent('Affected', '', 'Equals', 1, 1);

    // Patch pour gerer correctement la ref client:
    // il faut faire le lien avec le customer
    if (SearchTools::requestOrSessionExist('CustomerReference')) {
        $filterArray[] = SearchTools::NewFilterComponent(
            'Customer', 'ActorProduct().Actor', 'Equals', $customer, 1, 'Product');
    }

    if ($ProfileId == UserAccount::PROFILE_OWNER_CUSTOMER) {
        $filterArray[] = SearchTools::NewFilterComponent(
                'Owner', '', 'Equals', $UserConnectedActorId, 1);
    }
    // On recupere les derniers Produits commandes, si necessaire, ou
    // sinon on n'affiche aucun Product
    // XXX $clientIsConnected commenté par david, ne marche pas de toutes 
    // façons et cause un bug à chaque fois qd client connecté => aucun produit 
    // ne s'affiche, faut revoir tout ça
    if (Preferences::get('MercurialForClient')
        && (isset($_REQUEST['customerChanged']) && $_REQUEST['customerChanged'] == 1)/*|| $clientIsConnected*/) {
        $actor = Object::load('Actor', $UserConnectedActorId);
        $lastPdtIds = $actor->getLastProductIdsOrdered($customerInstance);
        $filterArray[] = (!empty($lastPdtIds))?
                SearchTools::NewFilterComponent('Id', '', 'In', $lastPdtIds, 1):
                SearchTools::NewFilterComponent('Id', '', 'Equals', -1, 1);
    }
    $filter = SearchTools::filterAssembler($filterArray);

    $grid = $catalog->buildGrid(
        array(
            'SellUnitType' => array(
                'ColumnType' => 'ProductSellUnitTypeQuantities',
                'Sortable'=>false
             ),
            'RealQuantity' => array(
                'ColumnType' => 'ProductRealQuantity',
                'Sortable'=>false
            ),
            'UBPrice' => array(
                'ColumnType' => 'ProductUBPrice',
                'actor' => isset($customerInstance)?$customerInstance:0,
                'Sortable'=>false
            ),
            'SellUnitVirtualQuantity' => array('Sortable' => false),
            'Price' => array(
                'ColumnType' => 'ProductCommandPriceWithDiscount',
                'actor' => isset($customerInstance)?$customerInstance:0,
                'Sortable' => false
            ),
            'Category' => array(
                'ColumnType' => 'FieldMapperWithTranslation',
                'TranslationMap' => getCategoryArray(),
                'Sortable' => false,
            ),
            'SupplierReference'=>array(
                 'ColumnType'=>'SupplierReference',
                 'Sortable' => false
            ),
            'BuyUnitType'=>array(
                'ColumnType' => 'ProductBuyUnitTypeQuantities',
                'Sortable' => false
            ),
            'Supplier' => array(
                'ColumnMacro' => '%MainSupplier.Name%',
                'Sortable' => false
            ),
            'CustomerReference'=>array(
                'ColumnType'=>'CallBack',
                'Func' => 'getReferenceByActor',
                'Args' => array(isset($customerInstance)?$customerInstance:0),
                'ColumnMacro' => '',
                'Sortable' => false
            ),
            'Material1'  => array('ColumnMacro' => '%Model.Material1.CommercialNameAndColor%'),
            'Material2'  => array('ColumnMacro' => '%Model.Material2.CommercialNameAndColor%'),
            'Accessory1' => array('ColumnMacro' => '%Model.Accessory1.CommercialNameAndColor%'),
            'Accessory2' => array('ColumnMacro' => '%Model.Accessory2.CommercialNameAndColor%'),
         )
     );
    if (in_array('readytowear', $tradeContext)) {
        /*
        $grid->newColumn('FieldMapper', _('Specific options'), array('Macro'=>'%Model.Option().Name%'));
         */
        $order = array(
            'Model.StyleNumber' => SORT_ASC,
            'Size.Name'         => SORT_ASC
        );
    } else {
        $order = array('BaseReference' => SORT_ASC);
    }
    // Si la preference est activee, on permet de saisir des qtes ds le catalog
    // Si ce sont des qtes d'UE (autre pref activee), on n'affiche pas la qte mini, 
    // car ce sont des UV, et cela serait source de confusion pour le user
    if (Preferences::get('ProductCommandQtyInCatalog')) {
        if (!Preferences::get('ProductCommandUEQty')) {
            $grid->NewColumn('ProductCommandMinimumQuantity', _('Minimum qty'),
                array('customer' => $customer, 'Sortable' => false));
        }
        $qtyCaption = (Preferences::get('ProductCommandUEQty'))?
           _('Selling unit qty'):_('Quantity');
        $grid->newColumn('CatalogQuantity', $qtyCaption);
    }
    

    /**
     * Actions
     **/
    $grid->NewAction('Redirect', array(
            'Caption'=>_('Ask for estimate'),
            'Profiles'=>array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES),
            'TransmitedArrayName' => 'pdt',
            'URL'=>'ProductCommand.php?isEstimate=1&cadencedOrder='
                . $catalog->getCadencedOrder())
        );
    $grid->NewAction('Redirect', array(
            'Caption'=>_('Order selected items'),
            'Profiles'=>array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_CUSTOMER, UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_ADMIN_VENTES,
                UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_OWNER_CUSTOMER),
            'TransmitedArrayName' => 'pdt',
            'URL'=>'ProductCommand.php?cadencedOrder='
                . $catalog->getCadencedOrder())
        );

    $form->displayResult($grid, true, $filter, $order, '',
        array('js/lib-functions/checkForm.js', 'js/lib-functions/ClientCatalog.js'),
        array('beforeForm' => $editEstimate)
    );
}
else {
    // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $editEstimate . $form->render().'</form>', 
        array('js/lib-functions/checkForm.js', 'js/lib-functions/ClientCatalog.js'));
}

?>
