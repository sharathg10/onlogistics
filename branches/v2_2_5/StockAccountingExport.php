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
require_once('StockAccountingExportTools.php');
require_once('MixedObjects/StockAccountingQuantity.php');
require_once('Objects/MovementType.const.php');

$auth = Auth::Singleton();
$auth->checkProfiles();

SearchTools::prolongDataInSession();

$action = basename($_SERVER['PHP_SELF']);

/*  Si on a clique sur OK apres saisie*/
if (isset($_REQUEST['formSubmitted'])) {
    require_once('SQLRequest.php');
    //Database::connection()->debug = true;
    // Le Site contenant le ou les Store concernes
    $store = Object::load('Store', $_REQUEST['Store'][0]);
    $siteName = Tools::getValueFromMacro($store, '%StorageSite.Name%');

    $startDate = sprintf(
            '%s-%s-%s', $_POST['startYear'], $_POST['startMonth'], $_POST['startDay']);
    $endDate = sprintf(
            '%s-%s-%s', $_POST['endYear'], $_POST['endMonth'], $_POST['endDay']);
    $shortUnitTypes = getShortUnitTypeArray();
    $shortUnitType = $shortUnitTypes[$_REQUEST['unitType']];

    // Export csv
    header('Pragma: public');
	header('Content-type: application/force-download');
	header('Content-Disposition: attachment;filename=comptaMatiere.csv');
	$fp = fopen('php://stdout','wb');

    // Qtes en stock
    if ($_REQUEST['formSubmitted'] == 1) {
        $sql = request_StockAccountingQty();
        $rs = executeSQL($sql);

        $s = _('Store') . '(' . _('site') . ' ' . $siteName . ');';
        $s .= _('Property') . ';' . _('Stock') . ' (';
        $s .= $shortUnitType . ')' . "\r\n";
        // Si date du jour, pas besoin de remonter l'historique des mouvements
        if ($endDate >= date('Y-m-d')) {
            while ($rs && !$rs->EOF) {
        		$s .= $rs->fields['storeName'] . ';' . $rs->fields['propertyValue'];
                $s .= ';' . I18N::formatNumber(floatval($rs->fields['qty']), 3) . "\r\n";
        		$rs->moveNext();
        	}
        }
        // Il faut remonter l'historique des mouvements
        else {
            $Collection = new Collection();
            while ($rs && !$rs->EOF) {
    			$saq = new StockAccountingQuantity();
    			$saq->setStoreName($rs->fields['storeName']);
    			$saq->setPropertyValue($rs->fields['propertyValue']);
    			$saq->setQuantity($rs->fields['qty']);
                $Collection->setItem($saq);
			    unset($saq);
            	$rs->moveNext();
        	}

            $sql2 = request_StockAccountingLEM($endDate);
            $rs2 = Database::connection()->execute($sql2);
            $firstIndex = 0;  // Pour optim des perfs

    		while ($rs2 && !$rs2->EOF) {
                $search = getElementFromSAQCollection(
                        $Collection, $rs2->fields['storeName'],
                        $rs2->fields['propertyValue'],
                        $firstIndex);
    			$item = $search[0];
    			$newIndex = $search[1];

                // Si pas d'item trouve, on en cree un, avec qty=0
                // et on l'insere au bon index dans la collection
    			if (false === $item) {
    			    $item = new StockAccountingQuantity();
        			$item->setStoreName($rs2->fields['storeName']);
        			$item->setPropertyValue($rs2->fields['propertyValue']);
                    $Collection->insertItem($item, $newIndex);
    			}
    			// Si entree et non annulateur ou EXM associe est une SORTIE et lem annulateur
    			$coef = (($rs2->fields['entryExit'] == ENTREE && $rs2->fields['cancelledMvt'] == 0)
    					|| ($rs2->fields['entryExit'] == SORTIE && $rs2->fields['cancelledMvt'] > 0))?-1:1;

    			$item->setQuantity($item->getQuantity() + ($coef * $rs2->fields['qty']));
    			$firstIndex = $newIndex;
    			unset($item);
                $rs2->moveNext();
    		}

    		$count = $Collection->getCount();
    		for($i = 0; $i < $count; $i++) {
                $saq = $Collection->getItem($i);
                // On n'affiche pas les lignes avec une qty nulle
                if ($saq->getQuantity() == 0) {
                    continue;
                }
                $s .= $saq->getStoreName() . ';' . $saq->getPropertyValue() . ';';
                $s .= I18N::formatNumber($saq->getQuantity(), 3) . "\r\n";//"<br>";
    		}
        }
    }

    // Entrees/Sorties
    elseif ($_REQUEST['formSubmitted'] == 2) {
        $sql = request_StockAccountingLEM($startDate, $endDate);
        $rs = Database::connection()->execute($sql);
        // Pour recuperer le nom de la property
        $property = Object::load('Property', $_REQUEST['Property']);
        $propertyName = $property->getDisplayName();
        $s = _('Store') . '(' . _('site') . ' ' . $siteName . ');';
        $s .= $propertyName . ';' . _('Entries') . ' (';
        $s .= $shortUnitType . ')' . ';' . _('Issues') . ' (' ;
        $s .= $shortUnitType . ')' . "\r\n";

        $Collection = new Collection();
        $issueForDamageByStore = array();
        $item = false;
        while ($rs && !$rs->EOF) {
            // Si sortie casse, c'est comptabilise a part
            if ($rs->fields['mvtType'] == SORTIE_CASSE) {
                if (!isset($issueForDamageByStore[$rs->fields['storeName']])) {
                    $issueForDamageByStore[$rs->fields['storeName']] = 0;
                }
                $issueForDamageByStore[$rs->fields['storeName']] += $rs->fields['qty'];
                $rs->moveNext();
            }
            // Si pas d'item ayant meme couple (store, PropertyValue) dans la
            // Collection, on en cree un
			if (false === $item || ($item->getStoreName() != $rs->fields['storeName']
            || $item->getPropertyValue() != $rs->fields['propertyValue'])) {
                unset($item);
			    $item = new StockAccountingQuantity();
    			$item->setStoreName($rs->fields['storeName']);
    			$item->setPropertyValue($rs->fields['propertyValue']);
                $Collection->setItem($item);
			}
			// Le LEM est une entree ssi:
			// entree et non annulateur ou EXM associe est une SORTIE et lem annulateur
			$entry = (($rs->fields['entryExit'] == ENTREE && $rs->fields['cancelledMvt'] == 0)
					|| ($rs->fields['entryExit'] == SORTIE && $rs->fields['cancelledMvt'] > 0));

            if ($entry) {
                $item->setEntryQuantity($item->getEntryQuantity() + $rs->fields['qty']);
            }
            else {
                $item->setExitQuantity($item->getExitQuantity() + $rs->fields['qty']);
            }
            $rs->moveNext();
		}

		$count = $Collection->getCount();
        if ($count > 0) {
		    // currentStore sert a afficher une ligne de casse par Store
            // s'il y a de la casse
		    $currentStore = $Collection->getItem(0)->getStoreName();
		    for($i = 0; $i < $count; $i++) {
                $saq = $Collection->getItem($i);
                if ($saq->getStoreName() != $currentStore) {
                    if (isset($issueForDamageByStore[$currentStore])) {
                        $s .= ';;;' . _('Issue for damage') . ': ';
                        $s .= $issueForDamageByStore[$currentStore] . "\r\n";
                    }
                    $currentStore = $saq->getStoreName();
                };
                $s .= $currentStore . ';' . $saq->getPropertyValue() . ';';
                $s .= I18N::formatNumber($saq->getEntryQuantity(), 3) . ';';
                $s .= I18N::formatNumber($saq->getExitQuantity(), 3) . "\r\n";
		    }
		    // Idem pour le dernier Store...
		    if (isset($issueForDamageByStore[$currentStore])) {
                $s .= ';;;' . _('Issue for damage') . ': ';
                $s .= I18N::formatNumber($issueForDamageByStore[$currentStore], 3) . "\r\n";
            }
        }
    }
    echo $s;
	fclose($fp);
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once('HTML/QuickForm.php');
$form = new HTML_QuickForm('StockAccountingExport', 'post', $action);
unset($form->_attributes['name']);  // xhtml compliant

$siteFilter = (in_array($auth->getProfile(),
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)))?
        array():array('Owner' => $auth->getActor());
$siteArray = SearchTools::createArrayIDFromCollection('StorageSite', $siteFilter);
$form->addElement('select', 'StorageSite', _('Site'), $siteArray,
        'onchange="fw.ajax.updateSelectCustom(\'storageSite\', \'store\', \'Store\', \'StorageSite\', \'getStoresForStorageSite\');"'
        .' style="width:100%" id="storageSite"');
$productTypeArray = SearchTools::createArrayIDFromCollection('ProductType');
$form->addElement('select', 'ProductType', _('Product type'), $productTypeArray,
		' onchange="fw.ajax.updateSelectCustom(\'productType\', \'property\', '
        . '\'Property\', \'ProductType\', \'stockAccountingExport_getCollection\');" '
		. 'style="width:100%" id="productType"');
$unitTypeArray = getUnitTypeArray();
$form->addElement('select', 'unitType', _('Unit statistics'), $unitTypeArray,
		'style="width:100%"');
$form->addElement('select', 'ProductOwner', _('Product owner'),
    SearchTools::createArrayIDFromCollection('Actor', array('Active'=>1), '',
    'toString', array('Name'=>SORT_ASC))
);

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$smarty->assign('dateFormat', I18N::getHTMLSelectDateFormat());
$pageContent = $smarty->fetch('Stock/StockAccountingExport.html');

Template::page('', $pageContent,
		array('js/lib-functions/checkForm.js',
              'JS_AjaxTools.php', 'js/lib-functions/StockAccountingExport.js'));

?>
