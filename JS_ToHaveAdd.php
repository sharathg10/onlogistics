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

// Sert a appliquer le taux de TVA selectionne au montant HT
$TVAMapper = Mapper::singleton('TVA');
$TVACollection = $TVAMapper->loadCollection(array(), array('Category' => SORT_ASC), array('Rate'));
$count = $TVACollection->getCount();
$TVARateArray[0] = 0;

for($i = 0; $i < $count; $i++){
	$item = $TVACollection->getItem($i);
	$TVARateArray[$i+1] = $item->getRate();
}

$nbTVA = count($TVARateArray);

echo '
function calculTTC() {
	var TVARateArray = new Array;';
	
for ($i=0; $i<$nbTVA; $i++) {
	echo '
	TVARateArray['.$i.'] = ' . $TVARateArray[$i] . ';';
}

echo '
	tva = TVARateArray[document.forms[0].elements["TH_TVA"].selectedIndex];
    ht = fw.i18n.extractNumber(document.forms[0].elements["TH_TotalPriceHT"].value);
    if(ht<0) {
        alert("' . _('You cannot issue negatives credit notes.') . '");
        document.forms[0].elements["TH_TotalPriceHT"].value = -ht
        ht = -ht;
    }
	ttc = troncature(add(ht, div(mul(ht, tva), 100)));
    if (!isNaN(ttc)) {
	    document.forms[0].elements["TH_TotalPriceTTC"].value = fw.i18n.formatNumber(ttc);
    }
}
';

// Verification qu'un DocumentNo saisi n'est pas deja attribue a un Avoir
$ToHaveDocumentNoArray = array(); // Contient tous les No des Avoirs deja crees
$ToHaveMapper = Mapper::singleton('ToHave');
$ToHaveCollection = $ToHaveMapper->loadCollection(array(), 
    array('DocumentNo' => SORT_ASC), array('DocumentNo'));
$count = $ToHaveCollection->getCount();

for($i = 0; $i < $count; $i++){
	$item = $ToHaveCollection->getItem($i);
	$ToHaveDocumentNoArray[] = $item->getDocumentNo();
}

echo '

function onTypeSelectChanged(doNotWarnIfEmpty) {
    var typeSelect   = document.forms[0].elements["TH_Type"];
    var tvaSelect    = document.forms[0].elements["TH_TVA"];
    var htTextfield  = document.forms[0].elements["TH_TotalPriceHT"];
    var ttcTextfield = document.forms[0].elements["TH_TotalPriceTTC"];
    if (typeSelect.value == "'.ToHave::TOHAVE_DISCOUNT_ANNUAL_TURNOVER.'") {
        htTextfield.value = "";
        ttcTextfield.value = "";
        // check du fournisseur
        var supplierSelect = document.forms[0].elements["TH_Supplier"]; 
	    if (supplierSelect.value == "##") {
            if (!doNotWarnIfEmpty) {
		        alert("' . _('Please select a supplier.') . '");
                fw.dom.selectOptionByIndex(typeSelect, 0);
            }
		    return false;
	    }
        // check du client
        var customerSelect = document.forms[0].elements["TH_Customer"];
	    if (customerSelect.value == "##") {
            if (!doNotWarnIfEmpty) {
		        alert("' . _('Please select a customer.') . '");
                fw.dom.selectOptionByIndex(typeSelect, 0);
            }
		    return false;
	    }
        document.forms[0].elements["TH_TotalPriceHT"].readonly = "readonly";
        var d = fw.ajax.call(
            "toHaveAdd_updateTotalHT",
            supplierSelect.value,
            customerSelect.value
        );
        var onSuccess = function (data) {
            if (data.isError) {
                alert("Error (code " + data.errorCode +"): " + data.errorMessage);
                return;
            }
            htTextfield.setAttribute("readonly","readonly");
            ttcTextfield.setAttribute("readonly","readonly");
            tvaSelect.setAttribute("disabled", "disabled");
            fw.dom.selectOptionByIndex(tvaSelect, 0);
            htTextfield.value = data;
            calculTTC();
        }
        var onError = function (err) {
            alert("Error: " + err);
        }
        d.addCallbacks(onSuccess, onError);
    } else {
        htTextfield.removeAttribute("readonly");
        ttcTextfield.removeAttribute("readonly");
        tvaSelect.removeAttribute("disabled");
        htTextfield.value = "";
        ttcTextfield.value = "";
    }
}

function checkBeforeSubmit(){
    // check du fournisseur
	if (document.forms[0].elements["TH_Supplier"].value == "##") {
		alert(\'' . _('Please select a supplier.') . '\');
		return false;
	}
    // check du type
	if (document.forms[0].elements["TH_Type"].value == 0) {
		alert(\'' . _('Please select a type.') . '\');
		return false;
	}
    // check du client
	if (document.forms[0].elements["TH_Customer"].value == "##") {
		alert(\'' . _('Please select a customer.') . '\');
		return false;
	}
	if (document.forms[0].elements["TH_TotalPriceHT"].value == "" || 
        document.forms[0].elements["TH_TotalPriceHT"].value == "0" ) {
		alert(\'' . _('Please provide an amount excl. VAT.') . '\');
		return false;
	}
	if (document.forms[0].elements["TH_DocumentNo"].value == "") return true;
	
	var ToHaveDocumentNoArray = new Array("' . implode('", "', $ToHaveDocumentNoArray) . '");
	for (i=0; i<' . $count . '; i++) {
		if (ToHaveDocumentNoArray[i] == document.forms[0].elements["TH_DocumentNo"].value) {
			alert("' . _('A credit note exists with the same number, please correct.') . '");
			return false;
		}
		if (ToHaveDocumentNoArray[i] > document.forms[0].elements["TH_DocumentNo"].value) {
			return true;  // optim algo
		}
	}
	return true;
}
';

?>
