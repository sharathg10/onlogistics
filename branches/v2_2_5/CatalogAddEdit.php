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
require_once('Objects/Catalog.php');
require_once('Objects/Catalog.const.php');
require_once('Objects/CatalogCriteria.php');
require_once('Objects/ProductType.php');
//Database::connection()->debug = true;

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'CatalogList.php';
$session = Session::Singleton();

/**
 * Messages
 */
$errorTitle = E_ERROR_TITLE;
$errorBody  = _('Catalogue cannot be saved.');
$okTitle    = I_CONFIRM_DO;
$okBody     = _('Catalogue "%s" was successfully saved.');
$pageTitle  = _('Add or update catalogue');

/**
 * Assignation des variables au formulaire avec smarty
 */
$smarty = new Template();
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);

/**
 * Si l'id est passée en paramètre on la charge sinon 
 * on instancie un nouvel objet
 */
$ctgID = false;
if(isset($_REQUEST['ctgId']) && !empty($_REQUEST['ctgId']) 
    && !isset($_POST['formSubmitted'])) {
	$ctgID = $_REQUEST['ctgId'];
	$catalog = Object::load('Catalog', $ctgID);
} elseif (isset($_SESSION['Catalog']) && $_SESSION['Catalog'] instanceof Catalog) {
    $catalog = $_SESSION['Catalog'];
} else {
	$catalog = Object::load('Catalog', $ctgID);
    $catalog->generateId();
}

/**
 * On check si l'objet est bien chargé, et on renvoie vers un dialogue 
 * d'erreur au cas où.
 */
if (Tools::isException($catalog)) {
    Template::errorDialog($catalog->getMessage(), $retURL);
    exit;
} 

/**
 * On le met en session pour 2 pages 
 */
$session->register('Catalog', $catalog, 2);

/**
 * Si les types de produits n'ont pas été choisis
 */
if (!isset($_REQUEST['productTypesChosen']) && !isset($_POST['formSubmitted'])) {
    $pTypesOptions = FormTools::WriteOptionsFromObject('ProductType', 
    		array_keys($catalog->getProductTypeList()), array('Generic'=>0), 
            array('Name' => SORT_ASC));
	$smarty->assign('ProductTypeOptions', implode("\n\t", $pTypesOptions));
    Template::page($pageTitle, $smarty->fetch('Catalog/CatalogAddEditPre.html'));
	exit;
} else { // sinon on assigne les types choisis au catalogue 
	if(isset($_REQUEST['Catalog_ProductType'])){
		$pdtTypeMapper = Mapper::singleton('ProductType');
		$collection = $pdtTypeMapper->loadCollection(
			    array('Id'=>array_values($_REQUEST['Catalog_ProductType'])));
		$catalog->setProductTypeCollection($collection);
	}
}

/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_POST['formSubmitted'])) {

    Database::connection()->startTrans();
    $catalogMapper = Mapper::singleton('Catalog');
    // gestion checkbox commande cadencée
    $catalog->setCadencedOrder(isset($_POST['Catalog_CadencedOrder']));
    // On remplit l'objet
    FormTools::autoHandlePostData($_POST, $catalog);
	
	/**
	 * On sauve les catalog criterias 
	 **/
	if(isset($_REQUEST['Criteria_Property'])){
		$criteriaMapper = Mapper::singleton('CatalogCriteria');
		$criteriaCol = $catalog->getCatalogCriteriaCollection();
        // suppression de tous les critères
        $count = $criteriaCol->getCount();
        for($i = 0; $i < $count; $i++){
            $criteria = $criteriaCol->getItem($i);
            if ($criteria instanceof CatalogCriteria) {
                deleteInstance($criteria, $retURL);
            }
        }
		for($i = 0; $i < count($_REQUEST['Criteria_Property']); $i++){
			$ptyID = $_REQUEST['Criteria_Property'][$i];
            // si le critère est "displayable" ou "searchable" on l'ajoute
            if (isset($_REQUEST['Criteria_Displayable'][$i]) 
             || isset($_REQUEST['Criteria_Searchable'][$i])) 
            {
				$criteria = new CatalogCriteria();
				$criteria->setProperty($ptyID);
                $toAdd = true;
			} else {
				continue;
			}
            
			if (isset($_REQUEST['Criteria_DisplayName'][$i])) {
			    $criteria->setDisplayName($_REQUEST['Criteria_DisplayName'][$i]);
			}
			$criteria->setDisplayable(isset($_REQUEST['Criteria_Displayable'][$i]));          
			$criteria->setSearchable(isset($_REQUEST['Criteria_Searchable'][$i]));
			if (isset($_REQUEST['Criteria_Index'][$i])) {
			    $criteria->setIndex($_REQUEST['Criteria_Index'][$i]);
			}
            if (isset($_REQUEST['Criteria_SearchIndex'][$i])) {
			    $criteria->setSearchIndex($_REQUEST['Criteria_SearchIndex'][$i]);
			}
            $criteria->setCatalog($catalog);
            saveInstance($criteria, $retURL);
		}
	}
    
    saveInstance($catalog, $retURL);
		
    if (Tools::isException($catalog)) {
        Template::errorDialog($catalog->getMessage(), $retURL);
        exit;
    } 
    /**
     * On commite la transaction, 
     * si la transaction a réussi, on redirige vers un message d'information
     * sinon vers un message d'erreur
     */
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog($errorBody, $retURL);
        Exit;
    } 
    Database::connection()->completeTrans();
    Tools::redirectTo('CatalogList.php');
    exit;
} 


// On assigne l'objet à la page
if ($catalog->getId() > 0) {
    $smarty->assign('Catalog', $catalog);
} 

/**
 * Construction du tableau de critères 
 **/
$criteriaArray = array();
$criteriaList = $catalog->getCatalogCriteriaList();

require_once('GetPropertyCollectionFromProductTypeCollection.php');
$pdtTypeCollection = $catalog->getProductTypeCollection();
$propertyCollection = getPropertyCollectionFromProductTypeCollection(
    										$pdtTypeCollection);

for($i = 0; $i < $propertyCollection->getCount(); $i++){
	$property = $propertyCollection->getItem($i);
	$propertyID = $property->getId();
	$criteriaArray[$i] = array();
	$criteriaArray[$i]['property'] = $propertyID;
	if (array_key_exists($propertyID, $criteriaList)) {
	    $criteria = $criteriaList[$propertyID];
	} else {
		$criteria = 0;
	}
	$criteriaArray[$i]['displayName'] = $criteria?
            $criteria->getDisplayName():$property->getDisplayName();
	$criteriaArray[$i]['displayable'] = $criteria?$criteria->getDisplayable():0;
	$criteriaArray[$i]['searchable'] = $criteria?$criteria->getSearchable():0;
	$criteriaArray[$i]['index'] = $criteria?$criteria->getIndex():0;
	$criteriaArray[$i]['searchIndex'] = $criteria?$criteria->getSearchIndex():0;
} // for

// il faut trier le tableau par index
function compare($a, $b) {
	if ($a['index'] == $b['index']) return 0;
	return ($a['index']>$b['index'])?1:-1;
}
usort($criteriaArray, 'compare');
$smarty->assign('CriteriaList', $criteriaArray);

$pageOptions = getPageOptions($catalog->getPage());
$smarty->assign('Catalog_Page_Options', implode("\n", $pageOptions));

/**
 * Et on affiche la page 
 **/
$pageContent = $smarty->fetch('Catalog/CatalogAddEdit.html');
Template::page($pageTitle, $pageContent, array('js/lib-functions/checkForm.js'));

?>
