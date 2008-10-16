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
require_once('ExecutionTools.php');
require_once('Objects/MovementType.const.php');
require_once('ExecutedMovementTools.php');
// utile ici pour la mise en session
require_once('Objects/LocationConcreteProduct.php');
require_once('Objects/ConcreteProduct.php');
require_once('Objects/AeroConcreteProduct.php');
require_once('Objects/Product.php');
require_once('Objects/AeroProduct.php');
require_once('Objects/Location.php');
$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR));

SearchTools::prolongDataInSession();
$closeWindow =  'javascript:window.close()';
$pagetitle = _('List of SN/Lots');

if (!isset($_REQUEST['locId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, $closeWindow, BASE_POPUP_TEMPLATE);
   	exit;
}
elseif ((array_sum($_REQUEST['QuantityArray']) == 0)
|| ($_REQUEST['TracingMode'] == Product::TRACINGMODE_LOT && (isset($_REQUEST['LotNumber'])
  && array_sum($_REQUEST['LotNumber'])) > array_sum($_REQUEST['QuantityArray']))) {
    $QtyErrorMsge = _('Wrong quantities provided, please correct.');
	Template::errorDialog($QtyErrorMsge, $closeWindow, BASE_POPUP_TEMPLATE);
   	exit;
}


// Donnees a passer d'une page a l'autre
$neededDatas = array('TracingMode', 'MvtTypeEntrieExit', 'locId', 'QuantityArray',
        'Product_id', 'lpqId', 'LotNumber', 'MvtType', 'CancellationType', 'LEM');

// Si on a clique sur OK
if (isset($_REQUEST['submittedForm'])) {
	unset($_SESSION['LCPCollection']);

	// Collection de LocationConcreteProduct qui sera mise en session
	$LCPCollection = new Collection();
	$session = Session::Singleton();

	$returnURL = basename($_SERVER['PHP_SELF']) . '?'
            . UrlTools::buildURLFromRequest($neededDatas);
	$error = 0;  // vaudra 1 si une erreur de saisie
	$LCPMapper = Mapper::singleton('LocationConcreteProduct');
	$Product = Object::load('Product', $_REQUEST['Product_id']);

	// Si reintegration en stock, traitement special
	if (isCancellation()) {
	    for($i=0 ; $i<count($_REQUEST['LocationId']) ; $i++) {
            $qty = isset($_REQUEST['Quantity'][$i])?$_REQUEST['Quantity'][$i]:0;  // hidden => non formate
	        $cpQty = isset($_REQUEST['CPQuantity'][$i])?$_REQUEST['CPQuantity'][$i]:1;
	        $cpQty = I18N::extractNumber($cpQty);
	        $result = createLCPForCancellation($_REQUEST['LEM'],
    	            $_REQUEST['LocationId'][$i], $_REQUEST['Product_id'],
    	            $_REQUEST['SerialNumber'][$i], $qty, $cpQty);
	        $error = $result['error'];
	        $LCP = $result['LCP'];
	        $LCP->setId(0); // comprend pas
	        $LCPCollection->setItem($LCP);
	        unset($LCP);
	    }
	} else {
	    $mvtTypeParams = array(
    	       'MvtTypeEntrieExit' => $_REQUEST['MvtTypeEntrieExit'],
    	       'MvtType' => isset($_REQUEST['MvtType'])?$_REQUEST['MvtType']:'');
	    $CPParams = array();
	    $CPParams['Product'] = $Product;
		for($i = 0; $i < count($_REQUEST['LocationId']); $i++) {
		    $EndOfLifeDate = '';
		    if (!empty($_REQUEST['EndOfLifeDate'][$i])) {
                $bdate = explode("/", $_REQUEST['EndOfLifeDate'][$i]);
                if (count($bdate) == 3) {
                    $EndOfLifeDate = $bdate[2] . "-" . $bdate[1]
                        . "-" . $bdate[0] . ' 00:00:00';
                } else {
                    $EndOfLifeDate = '';
                }
			}
			if(!empty($_REQUEST['SerialNumber'][$i])) {
			    $CPParams['SerialNumber'] = $_REQUEST['SerialNumber'][$i];
			}
			$CPParams['EndOfLifeDate'] = $EndOfLifeDate;

			$Location = Object::load('Location', $_REQUEST['LocationId'][$i]);
			$LCPParams = array(
    			    'Location'=>$Location,
    			    'LCPQuantity'=>isset($_REQUEST['CPQuantity'][$i])?
                            I18N::extractNumber($_REQUEST['CPQuantity'][$i]):1);
			$result = createOrUpdateLCP($_REQUEST['TracingMode'],
                    $mvtTypeParams, $CPParams, $LCPParams);
            $LCP = $result['LCP'];

            if($error==0) {
                $error = $result['error'];
            }
			$LCP->setId(0); /// comprend pas
			$LCPCollection->setItem($LCP);
			unset($LCP);
		} // for
    }

	if ($error > 0) {
		Template::errorDialog(getErrorMessage($error), $returnURL, BASE_POPUP_TEMPLATE);
		exit;
	}

	// Mise en session de la collection
	$session->register('LCPCollection', $LCPCollection, 10);

	// JS pour fermer automatiquement le popup
	$title = '<SCRIPT type="text/javascript">
				window.opener.document.forms[0].elements["fillPopup"].value = 1;
				self.close();
				</SCRIPT>";';
    Template::page($pagetitle, $title, array(), array(), BASE_POPUP_TEMPLATE);
	exit;
}


// 1er acces a la page
$qty = '';

// Si retour apres erreur ou reouverture du popup
if (isset($_SESSION['LCPCollection'])) {
    $LCPGridCollection = $_SESSION['LCPCollection'];
}
else {
	$LCPGridCollection = new Collection();

	for($i = 0; $i < count($_REQUEST['locId']); $i++) {
		// Si une ligne sans Qte saisie
		if (false == $_REQUEST['QuantityArray'][$i]) {
		    continue;
		}
		$Location = Object::load('Location', $_REQUEST['locId'][$i]);
		if (Tools::isEmptyObject($Location)) {
		    Template::errorDialog(E_MSG_TRY_AGAIN, $closeWindow, BASE_POPUP_TEMPLATE);
		  	exit;
		}

		// Pour la construction du Grid
		// Si tracing au SN, une ligne par product voulu
		if ($_REQUEST['TracingMode'] == Product::TRACINGMODE_SN) {
		// $_REQUEST['QuantityArray'][$i] est ici un entier, pas bersoin de I18N:extractNumber
			for($k = 0; $k < $_REQUEST['QuantityArray'][$i]; $k++){
				$LCP = Object::load('LocationConcreteProduct');
				$LCP->setLocation($Location);
				$LCPGridCollection->setItem($LCP);
			}
		}
		// Si tracing au lot,
		// Si Sortie: une ligne par ConcreteProduct trouve ds les Locations choisis
		// Si Entree: Nb de lignes depend de la saisie $_REQUEST['LotNumber']
		elseif ($_REQUEST['TracingMode'] == Product::TRACINGMODE_LOT) {
			if ($_REQUEST['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT || isChangeOfPosition()) {
			    $LCPCollection = $Location->getLocationConcreteProductCollection(
				        array('ConcreteProduct.Product.Id' => $_REQUEST['Product_id']));
				if (Tools::isEmptyObject($LCPCollection)) {
				    Template::errorDialog(E_MSG_TRY_AGAIN, $closeWindow,
                            BASE_POPUP_TEMPLATE);
				  	exit;
				}
				for($j = 0; $j < $LCPCollection->getCount(); $j++) {
					$LCP = $LCPCollection->getItem($j);
					$LCPGridCollection->setItem($LCP);
				}
				// Si sortie, on ajoute dans le title la quantite totale attendue
				if ($_REQUEST['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT ) {
				    $qty = '&nbsp;(' . _('Total quantity expected')
                        . ': <script type="text/javascript"><!--
                // <![CDATA[
				document.write(window.opener.document.forms[0].elements'
                . '["EnvisagedQuantity"].value);
                // ]]>
                -->
				</script>)';
				}
			}
			else {  // Entree
				for($j = 0; $j < $_REQUEST['LotNumber'][$i]; $j++) {
					$LCP = Object::load('LocationConcreteProduct');
					$LCP->setLocation($Location);
					$LCPGridCollection->setItem($LCP);
				}
			}
		}
		unset($Location, $LCP);
	}
}


$grid = new Grid();
$grid->displayCancelFilter = false;
$grid ->withNoCheckBox = true;
$grid->withNoSortableColumn = true;

$grid->NewAction('JS', array('jsActionArray' => array('checkAndSubmit()')));
$grid->NewAction('Close');

//  Colonnes du grid
$grid->NewColumn('FieldMapper', _('Site'),
        array('Macro' => '%Location.Store.StorageSite.Name%'));
$grid->NewColumn('FieldMapper', _('Store'), array('Macro' => '%Location.Store.Name%'));
$grid->NewColumn('FieldMapper', _('Location'),
        array('Macro' => '%Location.Name%<input type="hidden"'
					   . ' name="LocationId[]" value="%Location.Id%" />'));
if ($_REQUEST['TracingMode'] == Product::TRACINGMODE_LOT) {
	// Si entree, il faut aussi proposer le SN a saisir
	if ($_REQUEST['MvtTypeEntrieExit'] == MovementType::TYPE_ENTRY && false == isChangeOfPosition()) {
	    $grid->NewColumn('FieldMapper', _('Lot'),
            array('Macro' => '%ConcreteProduct.SerialNumber|editable@SerialNumber@15%'));
	}
	else { // les ConcreteProduct existent deja: on ne saisit que les Qtes
		$grid->NewColumn('FieldMapper', _('Lot'),
                array('Macro' => '%ConcreteProduct.SerialNumber%<input type='
                . '"hidden"' . ' name="SerialNumber[]" value="%ConcreteProduct.SerialNumber%" />'));
	}
	$qtyInInput = (isset($_SESSION['LCPCollection']))?'%Quantity%':0;
	$qtyInInput = I18N::formatNumber($qtyInInput, 3, true);
    $grid->NewColumn('FieldMapper', _('Quantity'),
        array('Macro' => '<input type="text" name="CPQuantity[]" size="10" value="'
                .$qtyInInput.'" />'));
	$grid->NewColumn('LocationConcreteProductList', 'Stock');
}
else {
	$grid->NewColumn('FieldMapper', _('SN'),
        array('Macro' => '%ConcreteProduct.SerialNumber|editable@SerialNumber@15%'));
}
// Si entree, il faut aussi proposer le EndOfLifeDate a saisir
// et si ni changemt de position, ni reintegration en stock
// Sinon, en lecture seule
$readonly = ($_REQUEST['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT
        || true == isChangeOfPosition() || true == isCancellation())?
                'readonly="readonly" disabled="disabled"':'';
$grid->NewColumn('FieldMapper', _('End of life'),
        array('Macro' => '<input type="text" name="EndOfLifeDate[]" size="10" '
        . $readonly . ' maxlength="10" '
        . 'value="%ConcreteProduct.EndOfLifeDate|formatdate@DATE_SHORT%" />'));


$result = $grid->render($LCPGridCollection, false);
$JSRequirements = array(
    'js/lib-functions/CheckDate.js',
    'js/lib-functions/FormatNumber.js',
    'js/includes/LocationConcreteProductList.js'
);

$title =
    '<table border="0" cellspacing="0" cellpadding="5" class="MenuContainer">
        <tr>
		  <td class="Title">
	        <img src="images/arrow.gif" border="0" alt=">" />&nbsp;<b>' .
            $pagetitle . $qty . '</b><br /><br />'
    . _('Please provide a end of life date in the following format: dd/mm/yyyy') . '<br />
		  	<input type="hidden" name="submittedForm" value="1" />
		  </td>
		</tr>
      </table>';

$title .= UrlTools::buildHiddenFieldsFromURL($neededDatas);

Template::page(
    $pagetitle,
    '<form action="LocationConcreteProductList.php">' . $title . $result . '</form>',
    $JSRequirements,
    array(),
    BASE_POPUP_TEMPLATE
);
?>