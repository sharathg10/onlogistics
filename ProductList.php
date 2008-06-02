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
require_once('Objects/Product.inc.php');
//Database::connection()->debug = true;
$auth = Auth::Singleton();
$auth->checkProfiles();
/* Cleanage des variables produit en session */
SearchTools::cleanDataSession('pdt');
unset($_SESSION['ccp']);

/* Catalogue */
$catMapper = Mapper::singleton('Catalog');
$catalog = $catMapper->load(array('Page'=>basename($_SERVER['PHP_SELF'])));
if (Tools::isException($catalog)) {
    Template::errorDialog(
        _('You must configure a catalogue and assign it to this screen.'),
        'javascript:history.go(-1);'
    );
    exit;
}
/*  Contruction du formulaire de recherche */
$form = new SearchForm('Product');
$customArray = array(
    'Supplier'=>array(
        'Name'=>'Supplier',
        'Type'=>'select',
        'Params'=> array(
            SearchTools::createArrayIDFromCollection(array('Supplier', 'AeroSupplier'),
                array('Active'=>1, 'Generic' => 0), _('Select one or more items')),
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
            'multiple="multiple" size="6"'
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
    )
);
$res = $catalog->buildSearchForm($form, array(), $customArray);
if (false == $res) {
    Template::errorDialog(
        _('A catalogue must contain at least a criterion.'),
        'javascript:history.go(-1);'
    );
    exit;
}
$form->addAction(array('URL'=>'ProductAddEdit.php?returnURL=ProductList.php'));


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    //  Création du filtre complet
    $Filter = SearchTools::filterAssembler($form->buildFilterComponentArray());
    define('PRODUCTLIST_ITEMPERPAGE', $catalog->getItemPerPage());
    $actConnected = $auth->getActor();
    $cur = $actConnected->getCurrency();
    $curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';
    $grid = $catalog->buildGrid(
        array(
            'BaseReference'=>array(
                'Sortable' => true,
                'ColumnMacro' => '<a href="ProductAddEdit.php?pdtId=%Id%'.
                    '&returnURL=ProductList.php">%BaseReference%</a>',
                'SortField' => 'BaseReference'
            ),
            'Name'=>array('Sortable' => true, 'SortField' => 'Name'),
            'SellUnitType' => array(
                'ColumnType' => 'ProductSellUnitTypeQuantities',
                'Sortable' => false
            ),
            'Supplier' => array(
                'ColumnType' => 'FieldMapper',
                'ColumnMacro' => '%MainSupplier.Name%',
                'Sortable' => false
            ),
            'Category' => array(
                'ColumnType' => 'FieldMapperWithTranslation',
                'TranslationMap' => getCategoryArray(),
                'Sortable' => true
            ),
            'Price' => array(
                'ColumnName'  => _('Unit price') . ' ' . $curStr,
                'ColumnType'  => 'FieldMapper',
                'ColumnMacro' => '%PriceByActor|formatnumber%',
                'Sortable' => false
            ),
            'SupplierReference'=>array(
                'ColumnType'=>'SupplierReference',
                'Sortable' => false
            ),
            'BuyUnitType'=>array(
                'ColumnType'=>'ProductBuyUnitTypeQuantities',
                'Sortable' => false
            )
        )
     );
    $grid->itemPerPage = PRODUCTLIST_ITEMPERPAGE;

    $grid->NewAction('AddEdit', array(
        'Action' => 'Add',
        'EntityType' => 'Product',
        'Query' => 'returnURL=ProductList.php'
        )
    );
    $grid->NewAction('Delete', array(
        'TransmitedArrayName' => 'ProductId',
        'EntityType' => 'Product'
        )
    );
    $grid->NewAction('Redirect', array(
        'Caption' => _('SN/Lot'),
        'Title' => _('Add SN/Lot'),
        'URL' => 'ConcreteProductAddEdit.php?pdtId=%d&retURL=ProductList.php'
        )
    );
    $grid->NewAction('Redirect', array(
        'Caption' => _('Assign'),
        'Title' => _('Assign to chain'),
        'TransmitedArrayName' => 'p',
        'URL' => 'dispatcher.php?entity=Chain',
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
        )
     );
    $grid->NewAction('Redirect', array(
        'Caption' => _('Order quantities'),
        'Title' => _('Manage ordered quantities'),
        'URL' => 'ProductQuantityByCategoryList.php?pdtId=%d',
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
        )
    );
    $grid->NewAction('Print');
    $grid->NewAction('Export', array('FileName' => 'Produits'));

    $Order = array('BaseReference' => SORT_ASC);

    $form->displayResult($grid, true, $Filter, $Order);
}

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>
