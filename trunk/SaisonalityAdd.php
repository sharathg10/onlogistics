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
require_once('Objects/Property.inc.php');
$auth = Auth::Singleton();
//Database::connection()->debug = true;
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
define('PAGE_TITLE', _('Assign products to a seasonality'));

/*  1er acces a la page: Selection du ProductType et des ProductKind */
if ((!isset($_REQUEST['ProductType']))) {
    $smarty = new Template();
	require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
	$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

	require_once ('HTML/QuickForm.php');
	$form = new HTML_QuickForm('SaisonalityAdd', 'post', $_SERVER['PHP_SELF']);

	$ProductTypeArray = SearchTools::createArrayIDFromCollection('ProductType',
            array('Generic' => 0), _('Select a product type'));
	$form->addElement('select', 'ProductType', '', $ProductTypeArray, '');

	$ProductKindArray = SearchTools::createArrayIDFromCollection('ProductKind', array(),
            _('Select one or more features'));

    $form->addElement('select', 'ProductKindArray', '', $ProductKindArray,
            'multiple size="5"');
	// sera egal a 1 si on demande a ajouter des Ref supplementaires
	$form->addElement('hidden','AddRef', 0);
	$form->addElement('text', 'Rate', _('Ratio'));
	$form->addElement('date', 'StartDate', '',
            array('format'    => I18N::getHTMLSelectDateFormat(),
				  'minYear'   => date('Y'),
			      'maxYear'   => date('Y')+1));
	$form->addElement('date', 'EndDate', '',
            array('format'    => I18N::getHTMLSelectDateFormat(),  /*   H:i  */
			      'minYear'   => date('Y'),
			      'maxYear'   => date('Y')+1));
	$form->addElement('submit', 'AddReferences',
            _('Add additional references'),
            'onClick="this.form.elements[\'AddRef\'].value=1;"');

	$defaultValues = array(
            'EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
			'StartDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')));
	$defaultValues2 = SearchTools::dataInSessionToDisplay();
	// necessaire, sinon reste a 1 si retour sans avoir choisi de Product
	unset($defaultValues2['AddRef']);
	$defaultValues = array_merge($defaultValues, $defaultValues2);
	// affiche le form avec les valeurs par defaut
	$form->setDefaults($defaultValues);

	/*  Validation du formulaire: chps ProductType, Rate obligatoires  */
	$form->addRule('ProductType', _('Please select a product type.'),
            'numeric', '', 'client');
	$form->addRule('Rate', _('Please provide a ratio.'), 'required',
            '', 'client');
	$form->addRule('Rate', _('Please provide a numeric ratio.'),
            'numeric', '', 'client');
	$form->setJsWarnings(_('Error: '), _('Please correct.'));

	$form->accept($renderer); // affecte au form le renderer personnalise
	$smarty->assign('form', $renderer->toArray());
	$pageContent = $smarty->fetch('SaisonalityAdd.html');

    Template::page(_('Add a seasonality'), $pageContent);
	exit;
}


$dateError = _('Wrong date range provided.');
$StartDate = DateTimeTools::QuickFormDateToMySQL('StartDate');
$EndDate = DateTimeTools::QuickFormDateToMySQL('EndDate');
if ($StartDate > $EndDate) {
	SearchTools::inputDataInSession();
    // met les saisies en session, pour retomber sur le form rempli
    Template::errorDialog($dateError, $_SERVER['PHP_SELF']);
  	exit;
}

/*  Si on a clique sur OK apres le choix des Product,
* oubien sur OK apres saisie du coefficient (pas de ref supplemtaire)  */
if ((isset($_REQUEST['AddRef']) && $_REQUEST['AddRef'] == 0) || !empty($_REQUEST['pdtId'])) {
    // liste des Id de Product a lier a la saisonality, une fois l'id verifie
	$pdtIdArray = array();
	$Saisonality = Object::load('Saisonality');

	$Var = (isset($_POST['StartDate']))?'_REQUEST':'_SESSION';  // var dynamique
	$StartDate = DateTimeTools::QuickFormDateToMySQL('StartDate') . ' 00:00:00';
	$EndDate = DateTimeTools::QuickFormDateToMySQL('EndDate') . ' 23:59:59';
	$Saisonality->setStartDate($StartDate);
	$Saisonality->setEndDate($EndDate);
	$Saisonality->setRate(${$Var}['Rate']);

	/*  Si on vient de selectionner des Product: Recup de la var de session liee
     *  au grid de SaisonalityAdd.php. On n'utilise pas les id passes ds l'url,
     *  car ça peut depasser 255 caracteres!!  */
	foreach($_SESSION as $key => $value) {
		if (!(false === strpos($key, 'SaisonalityAdd.php_griditems'))) {
		    $itemsVarName = $key;
			break;
		}
	}
    $itemsVarName = SearchTools::getGridItemsSessionName('SaisonalityAdd');
	if (isset($_SESSION[$itemsVarName]) && !empty($_SESSION[$itemsVarName])) {
	    $Saisonality->setProductCollectionIds($_SESSION[$itemsVarName]);
	}

	/* on verifie que tous les Id passes ds l'url sont valides, avant de faire des setCollectionIds()
	if (!(false == UrlTools::checkObjectFromUrlQuery("Product", 'pdtId'))) {
	    $Saisonality->setProductCollectionIds(UrlTools::heckObjectFromUrlQuery("Product", 'pdtId'));
	}*/
	if (!(false == UrlTools::checkObjectFromUrlQuery('ProductKind', 'ProductKindArray'))) {
	    $Saisonality->setProductKindCollectionIds(
                UrlTools::checkObjectFromUrlQuery('ProductKind', 'ProductKindArray'));
	}
    saveInstance($Saisonality, $_SERVER['PHP_SELF']);
    Tools::redirectTo('SaisonalityList.php');
	exit;
}


SearchTools::inputDataInSession();  // met les saisies en session

/*  Recup des ProductKind liees au ProductType ou a son GenericProductType
    s'il existe, et non selectionnees precedemment  */
$ProductType = Object::load('ProductType', $_REQUEST['ProductType']);
$GenericProductType = $ProductType->getGenericProductType();

$filter = new FilterComponent();
$filter->setItem(new FilterRule('ProductType',
        FilterRule::OPERATOR_EQUALS, $_REQUEST['ProductType']));

if (!Tools::isEmptyObject($GenericProductType)) {
	$filter->operator = FilterComponent::OPERATOR_OR;
	$filter->setItem(new FilterRule('ProductType',
            FilterRule::OPERATOR_EQUALS,
			$ProductType->getGenericProductTypeId()));
}
$FilterComponents[] = $filter;

/*  Recup des ProductKind selectionnees : sert a construire le select */
if (isset($_REQUEST['ProductKindArray'])) {
	$FilterComponents[] = SearchTools::NewFilterComponent(
            'Id', '', 'NotIn', $_REQUEST['ProductKindArray'], 1);
}
$PdtKindFilter = SearchTools::filterAssembler($FilterComponents);

// Recup des Property liees au ProductType ou a son GenericProductType s'il existe
$PropertyArray = $ProductType->getPropertyArray();


/*  Contruction du formulaire de recherche */
$form = new SearchForm('Product');
// construit des var a passer ds l'url si besoin
$query = UrlTools::buildURLFromRequest('ProductKindArray');
$form->setQuickFormAttributes(array('action' => $_SERVER['PHP_SELF'].'?'.$query));
$form->addElement('hidden', 'ProductType', $_REQUEST['ProductType']);

foreach($PropertyArray as $key => $value) {
	$Property = $PropertyArray[$key];
    // si c'est une Cle etrangere? on ne s'en occupe pas (pb de path)
	if ($Property->getType() == Property::OBJECT_TYPE) {
	    continue;
	}  // addElement
	$form->addDynamicElement('text', $Property->getName(),
            $Property->getDisplayName(), array(), array('Operator' => 'Like'));
}
$ProductKindArray = SearchTools::createArrayIDFromCollection('ProductKind',
    $PdtKindFilter, _('Select one or more features'));
$form->addDynamicElement('select', 'ProductKind', 'Type',
	array($ProductKindArray, 'multiple size="5"'), array('PropertyType'=>'IntValue'));

$form->addAction(array('URL' => $_SERVER['PHP_SELF'], 'Caption' => A_CANCEL));


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {

	/*  Construction du filtre  */
	$FilterComponentArray[] = $filter;


	/*  Pour les ProductKind selectionnees : on ne peut pas choisir de Product
    ayant ces ProductKind */
	if (isset($_REQUEST['ProductKindArray'])) {
		$FilterComponent = SearchTools::NewFilterComponentOverDynamicProperty(
                'ProductKind', 'IntValue', 'NotEquals', $_REQUEST['ProductKindArray']);
		$FilterComponentArray[] = $FilterComponent;
	}
	$FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

	define('PRODUCTLIST_ITEMPERPAGE', 350);
	$grid = new Grid();
	$grid->itemPerPage = PRODUCTLIST_ITEMPERPAGE;

	$grid->NewAction('Redirect', array('Caption' => _('Assign to seasonality'),
            'TransmitedArrayName' => 'pdtId',
            'URL' => $_SERVER['PHP_SELF'].'?ProductType='.$_REQUEST['ProductType'].$query));
	$grid->NewAction('Redirect', array('Caption' => A_CANCEL,
	       'URL' => 'SaisonalityAdd.php'));

	$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%BaseReference%'));
	$grid->NewColumn('FieldMapper', _('Designation'),array('Macro' => '%Name%'));

	$Order = array('BaseReference' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order, PAGE_TITLE);
} // fin FormSubmitted

else {   //  on n'affiche que le formulaire de recherche, pas le Grid
    Template::page(PAGE_TITLE, $form->render() . '</form>');
}
?>
