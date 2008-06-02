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

function executionGrid($TracingMode, $MvtTypeEntrieExit, $lpqCol, $quantity=0) {
	require_once('Objects/Product.php');
    $numRows = $lpqCol->getCount();
    $notEmptyGrid = $numRows > 0;
	$grid = new Grid();
	$grid->paged = false;
	$grid->displayCancelFilter = false;
	$grid->withNoCheckBox = true;
	$grid->withNoSortableColumn = true;
	// Un seul emplacement
    if ($quantity != 0 && $numRows == 1) {
        $lpq = $lpqCol->getItem(0);
        $locQuantity = $lpq->getRealQuantity();
        $quantity = ($locQuantity < $quantity && $MvtTypeEntrieExit == SORTIE)?
                $locQuantity:$quantity;
    }
    else {
        $quantity = 0;
    }
    // Ajout d'1 hidden par ligne pour controle js si sortie de stock:
    // On ne peut sortir plus que ce qu'il y a en stock
    $maxQtyPerLine = ($MvtTypeEntrieExit == SORTIE)?
            '<input type="hidden" name="maxQtyPerLine[]" value="%RealQuantity%" />':'';
    $onkeyup = ($MvtTypeEntrieExit == SORTIE)?
             ' onkeyup="checkMaxQtyPerLine(this);"':'';

    if ($TracingMode > 0 && $notEmptyGrid) {
	    $grid->NewAction('Submit', array('Caption' => _('Fill in SN or Lot'),
                'FormAction' => 'LocationConcreteProductList.php',
                'TargetPopup' => true));
	}

	/*  si deplacement, on explicite l'entete Emplacements  */
	$EmplactHeader = (isset($_REQUEST['MvtType'])
            && ENTREE_DEPLACEMT == $_REQUEST['MvtType'])?_(' of departure'):'';

	$grid->NewColumn('FieldMapper', _('Site'),
            array('Macro' => '%Location.Store.StorageSite.Name%' . $maxQtyPerLine));
	$grid->NewColumn('FieldMapper', _('Store'),
            array('Macro' => '%Location.Store.Name%'));
	$grid->NewColumn('FieldMapper', _('Location') . $EmplactHeader,
            array('Macro' => '%Location.Name%'));
	$grid->NewColumn('FieldMapperWithTranslationExpression', _('Quantity'),
            array('Macro' => '%Activated%',
			   	  'TranslationMap' => array(
                    0 => '<input type="text" name="disabledQuantityArray[]" '
                            . 'size="10" value="0" disabled="disabled" />',
                    1 => '<input type="hidden" name="lpqId[]" value="%Id%" />
                       <input type="hidden" name="locId[]" value="%Location.Id%" />
                       <input type="text" name="QuantityArray[]" size="10" value="'
                       . $quantity . '"' . $onkeyup . ' /> %Product.MeasuringUnit%')));

	if ($TracingMode == Product::TRACINGMODE_LOT && $MvtTypeEntrieExit == ENTREE) {
	    $grid->NewColumn('FieldMapperWithTranslationExpression', _('Lot number'),
			 array('Macro' => '%Activated%',
			   	   'TranslationMap' => array(
                        0 => '<input type="text" name="LotNumber[]" size="5" '
                                . 'value="0" disabled="disabled" />',
						1 => '<input type="text" name="LotNumber[]" size="5" value="0" />')));
	}

	$grid->NewColumn('ProductRealQuantity', 'Stock', array());
	return $grid;
}

/**
 * @access public
 * @param $LPQCollection Object: Collection des LPQ qu'il ne faut pas afficher
 * @param $ProfileId integer: Id du Profile du UserAccount connecte
 * @param $UserConnectedActorId integer: Id de l'Actor attache au UserAccount connecte
 * @return string
 **/
function getHTMLLocationSelect($LPQCollection, $ProfileId, $UserConnectedActorId, $siteIds=array()) {
	$WithProductLocationArray = array();
	for($i = 0; $i < $LPQCollection->getCount(); $i++){
		$item = $LPQCollection->getItem($i);
		$WithProductLocationArray[$item->getLocationID()] =
                Tools::getValueFromMacro($item, '%Location.Name%');
		unset($item);
	}
    $LocationMapper = Mapper::singleton('Location');
    if(empty($siteIds)) {
	    $filter = (!in_array($ProfileId, array(UserAccount::PROFILE_ADMIN,
                UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)))?
            array('Store.StorageSite.Owner' => $UserConnectedActorId):array();
    } else {
        $filter = array('Store.StorageSite' => $siteIds);
    }
	$LocationCollection = $LocationMapper->loadCollection(
            $filter, array('Name' => SORT_ASC));

	$LocationArray = array();
	$NotActivatedLocationArray = array();
	for($i = 0; $i < $LocationCollection->getCount(); $i++){
  		$item = $LocationCollection->getItem($i);
		if ($item->getActivated() == 0) {  // Les Location desactives seront a griser
		    ///$NotActivatedLocationArray[] = $item->getId();
		    // remplace par la ligne ci dessous, car IE ne gere pas les
            // <option disabled="disabled">
			$NotActivatedLocationArray[$item->getId()] = $item->getName();
		}
		$LocationArray[$item->getId()] = $item->getName();
		unset($item);
	}
    // si le produit a deja des emplacements on les enleve pour l'affichage dans
    // le select, sauf si c pour un deplacement
    if (!(isset($_REQUEST['MvtType']) && ENTREE_DEPLACEMT == $_REQUEST['MvtType'])) {
		$LocationArray = array_diff_assoc($LocationArray, $WithProductLocationArray);
    }
    // Construire un select, avec les refs des Locations desactives en grise
	require_once('ListItems.php');
	// La ligne suivante ne marche pas sous IE qui ne gere pas les
    // <option disabled="disabled"> => remplacee par les 2 lignes suivantes
	//$HTMLLocationSelect = itemsArrayToHtml($LocationArray, NULL, array(),
    // '', $NotActivatedLocationArray);
	$LocationArray = array_diff_assoc($LocationArray, $NotActivatedLocationArray);

	if (!empty($LocationArray)) {
	    $LocationCollection = $LocationMapper->loadCollection(
                array('Id' => array_keys($LocationArray)),
                array('Store.StorageSite.Name' => SORT_ASC,
                      'Store.Name' => SORT_ASC,
	                  'Name' => SORT_ASC));
	}
	else {
		return '';  // collection vide
	}
	return buildHTMLLocationSelect($LocationCollection);
}

/**
 * Construit vraiment le select pour les Location, classes par Site, puis store,
 * avec presentation en sous-menus
 * @access public
 * @param $LocationCollection Object: Collection des Location a afficher
 * @return string
 **/
function buildHTMLLocationSelect($LocationCollection) {
	$html_code = '';   // Contiendra le HTML
	$FirstLocation = $LocationCollection->getItem(0);
	$StorageSiteName = Tools::getValueFromMacro(
            $FirstLocation, '%Store.StorageSite.Name%');
	$StoreName = Tools::getValueFromMacro($FirstLocation, '%Store.Name%');
	$StorageSiteNameArray = array($StorageSiteName);
	$SiteAndStoreNameArray = array($StorageSiteName . ' ' . $StoreName);
	$html_code .= '\n\t<optgroup label="' . $StorageSiteName . '">';
	$html_code .= '\n\t<optgroup label=" -> ' . $StoreName . '">';

	for($i = 0; $i < $LocationCollection->getCount(); $i++) {
  		$Location = $LocationCollection->getItem($i);
		$StoreName = Tools::getValueFromMacro($Location, '%Store.Name%');
		$StorageSiteName = Tools::getValueFromMacro(
                $Location, '%Store.StorageSite.Name%');

        // Si nouveau Site
		if (!in_array($StorageSiteName, $StorageSiteNameArray)) {
			$html_code .= '\n\t</optgroup>';
			$html_code .= '\n\t</optgroup>';
			$html_code .= '\n\t<optgroup label="' . $StorageSiteName . '">';
			$html_code .= '\n\t<optgroup label=" -> ' . $StoreName . '">';
			$StorageSiteNameArray[] = $StorageSiteName;
			$SiteAndStoreNameArray[] = $StorageSiteName.' '.$StoreName;
		}
		// Si nouveau Store pour un Site
		elseif (!in_array($StorageSiteName.' '.$StoreName, $SiteAndStoreNameArray)) {
			$html_code .= '\n\t</optgroup>';
			$html_code .= '\n\t<optgroup label=" -> ' . $StoreName . '">';
			$SiteAndStoreNameArray[] = $StorageSiteName.' '.$StoreName;
		}
		$html_code .= "\n\t<option value=\"" . $Location->getId() . "\">"
                . $Location->getName()."</option>";
	}
	$html_code .= '\n\t</optgroup>';
	$html_code .= '\n\t</optgroup>';
	return $html_code;
}


/**
 * Recupere des var POST et les met en session
 * @param $items array of strings
 * @param $session object Session
 * $nbPages integer: Duree de vie des vars de session en pages
 * $default string: valeur par defaut des vars de session
 * @access public
 * @return void
 **/
function post2Session($items, $session, $nbPages, $default=""){
	for($i = 0; $i < count($items); $i++){
		if (isset($_POST[$items[$i]])) {
		    $session->register($items[$i], $_POST[$items[$i]], $nbPages);
		} else {
		    $session->register($items[$i], $default, $nbPages);
		}
	}
}

/**
 * Recupere des var POST et les met en session
 * @param $items array of strings
 * $nbPages integer: Duree de vie des vars de session en pages
 * $default string: valeur par defaut des vars de session
 * @access public
 * @return void
 **/
function session2Post($items){
	for($i = 0; $i < count($items); $i++){
		if (isset($_SESSION[$items[$i]])) {
		    $_POST[$items[$i]] = $_SESSION[$items[$i]];
		}
	}
}

?>