/**
 *
 * @version $Id$
 */

var totalHT=0, totalTTC=0. 
 
connect(window, 'onload', function() {
	RecalculateTotal();
	RecalculateToPay();
});


function RecalculateTotal() {
	var fPort, fEmballage, fAssurance;
	totalHT=0;
	totalTTC=0;
	with (document.forms[0]) {
		// necessaire, car ProductGrid.html non 'dispo'
////		elements["RemExcep"].value = elements["HiddenRemExcep"].value; 

		var linesLength = elements["PriceHT[]"].length;
		if (!linesLength) {
			var itemResult = RecalculateItemTotal(-1);
			totalHT  = itemResult[0];
			totalTTC = parseFloat(mul(totalHT, add(1, div(itemResult[1], 100))));
		} else {
	    	for(var i=0; i < linesLength; i++) {   
				var itemResult = RecalculateItemTotal(i);
				totalHT  = add(totalHT, itemResult[0]);
				totalTTC = parseFloat(add(totalTTC, add(itemResult[0], mul(itemResult[0], div(itemResult[1], 100)))));

	    	}
		}
	
		/*  Les differents frais  */	
		if(elements["Port"].value != ''){
			fPort = fw.i18n.extractNumber(elements["Port"].value);
			totalHT  = add(totalHT, fPort);
			totalTTC = add(totalTTC, mul(fPort, (add(1, div(elements["port_tva_rate"].value, 100)))));
		}
		if(elements["Emballage"].value != ''){
			fEmballage = fw.i18n.extractNumber(elements["Emballage"].value);
			totalHT  = add(totalHT, fEmballage);
			totalTTC = add(totalTTC, mul(fEmballage, (add(1, div(elements["packing_tva_rate"].value, 100)))));
		}
		if(elements["Assurance"].value != '' ){
			fAssurance = fw.i18n.extractNumber(elements["Assurance"].value);
			totalHT  = add(totalHT, fAssurance);
			totalTTC = add(totalTTC, mul(fAssurance, (add(1, div(elements["insurance_tva_rate"].value, 100)))));
		}
		
    	// Gestion de la taxe Fodec
    	if (isNaN(totalHT)) {
    	    elements["fodecTax"].value = "---";
    	} else {
    	    var fodecTRate = parseFloat(elements["fodecTaxRate"].value);
    	    var fodecTaxMontant = mul(totalHT, div(fodecTRate, 100));
    	    totalTTC = add(totalTTC, fodecTaxMontant);
    	    elements["fodecTax"].value = troncature(fodecTaxMontant, true);
    	}
    	// Gestion du timbe fiscal
    	var taxStampValue = parseFloat(elements["taxStamp"].value);
	    totalTTC = add(totalTTC, taxStampValue);
        elements["TotalHT"].value = isNaN(totalHT)?"---":troncature(totalHT, true);
        elements["TotalTTC"].value = isNaN(totalTTC)?"---":troncature(totalTTC, true);
	}
}

function RecalculateUpdateIncur() {
	UpdateIncur = fw.i18n.extractNumber(document.forms[0].elements["HiddenUpdateIncur"].value);
	Instalment = fw.i18n.extractNumber(document.forms[0].elements["Instalment"].value);
	UpdateIncur = subs(UpdateIncur, Instalment);
    document.forms[0].elements["UpdateIncur"].value = isNaN(UpdateIncur)?"---":troncature(UpdateIncur, true);
}

function validation() {
	with (document.forms[0]) {
		if (elements["TotalTTC"].value == " ---") {
			alert(ProductCommand_0);
			return false;
		}
		var linesLength = elements["PriceHT[]"].length;
		var errorMsg = ProductCommand_1;
		if (!linesLength) {
			if (parseInt(elements["PriceHT[]"].value) < 1) {
				alert(errorMsg);
				return false;
			}			
		} else {
			for(var i=0;i < linesLength; i++) {
				if (parseInt(elements["PriceHT[]"][i].value) < 1) {
					alert(errorMsg);
					return false;
				}
			}
		}
		elements["valider"].disabled = true;//empeche de valider plusieurs fois la meme commande
	}
	return true;
}

function RecalculateToPay() {
	var ToPay = 0;
	var TotalTTC = fw.i18n.extractNumber(document.forms[0].elements["TotalTTC"].value);
	var Instalment = fw.i18n.extractNumber(document.forms[0].elements["Instalment"].value);
										
	if (Instalment < 0) {
		alert(ProductCommand_2);
		document.forms[0].elements["Instalment"].value = 0;
		RecalculateUpdateIncur();
	}
	
	ToPay = subs(TotalTTC, Instalment);
	if (ToPay < 0) {
		alert(ProductCommand_3);
		document.forms[0].elements["Instalment"].value = 0;
		RecalculateUpdateIncur();
		ToPay = TotalTTC;
	}
    document.forms[0].elements["ToPay"].value = isNaN(ToPay)?"---":troncature(ToPay, true);
}

/**
 * Recalcule le total HT et TTC pour un item de la commande
 * et retourne un tableau (prixHT, TVA)
 * 
 */
function RecalculateItemTotal(index) {
	with (document.forms[0]) {
		// Remise globale
		var globalHandingFloat = 0, minusquantity=0;  // initialisation
		if(elements["GlobalHanding"].value != '' ) {
			globalHandingFloat = fw.i18n.extractNumber(elements["GlobalHanding"].value);
			if ((globalHandingFloat < 0) || (globalHandingFloat > 100)) {
				alert(ProductCommand_4);
				elements["GlobalHanding"].value = '';
				globalHandingFloat = 0;
			}
		}
		if (index != -1) {
            //var quantityWidget = elements["qty[]"][index];
            //var quantity = quantityWidget.value.replace(',', '.');
            //quantity = quantity.replace(' ', '');
			var PUHT = fw.i18n.extractNumber(elements["PriceHT[]"][index].value);
			var TVA = fw.i18n.extractNumber(elements["HiddenTVA[]"][index].value);
			var handingWidget = elements["Handing[]"][index];
			var handing = elements["Handing[]"][index].value;
			var PTHTWidget = elements["PTHT[]"][index];
			var PriceHTWidget = elements["PriceHT[]"][index];
		} else {
            //var quantityWidget = elements["qty[]"];
            //var quantity = quantityWidget.value.replace(',', '.');
            //quantity = quantity.replace(' ', '');
			var PUHT = fw.i18n.extractNumber(elements["PriceHT[]"].value);  // deja tronque a 2 decimales cote serveur
			var TVA = fw.i18n.extractNumber(elements["HiddenTVA[]"].value);
			var handingWidget = elements["Handing[]"];
			var handing = elements["Handing[]"].value;
			var PTHTWidget = elements["PTHT[]"];
			var PriceHTWidget = elements["PriceHT[]"];
		}
		if(isNaN(TVA)) {
		    TVA = 0;
		}
	    /*if (!isNaN(quantity)) {
            quantityWidget.value = quantity;
        }*/
	
		if((handing != '') && (handing != '0')) {
			handing = fw.i18n.extractNumber(handing);
			len = handing.length - 1;
			if (/^[0-9.,]+%/.test(handing)) {
				// Remise en %
				handing = parseFloat(handing.substring(0, len));
				if ((handing < 0)||(handing > 100)){
					alert(ProductCommand_4);
					handingWidget.value = '';
					handing = 0; // PUHT inchange
				}
				PUHT = subs(PUHT, div(mul(PUHT, handing), 100));
			} else if ((/(\/)/.test(handing))) {
			    handingWidget.value="";
				// Remise en fraction: PUHT unitaire inchange dans ce cas
				//handing = handing.split('/');
				//if ((handing[1] != '') || (handing[1] != 0)) {
					//minusquantity = Math.floor(mul(quantity, div(handing[0], handing[1])));
				//	PUHT = mul(div(handing[0], handing[1]), PUHT);
				//}
			} else if (/^[0-9.,]/.test(handing)) {
				// Remise en euros
				handing = parseFloat(handing);
				if (handing > PUHT) {
					alert(ProductCommand_5);
					handingWidget.value = '';
					handing = 0; // PUHT inchange
				}
				PUHT = subs(PUHT, handing);
			} else {// mauvaise saisie	 
				PTHTWidget.value = " ---";
				//PriceHTWidget.value = " ---";
	        	return new Array("N/A", "N/A");
	        }
		}
		// On prefere tronquer a 2 decimales ici, au cas ou une remise style 2.564€ ait ete saisie

		//var newTotalHT = troncature(mul(mul(subs(quantity, minusquantity), PUHT), subs(1, div(globalHandingFloat, 100))));
		var newTotalHT = troncature(mul(PUHT, subs(1, div(globalHandingFloat, 100))));
		PTHTWidget.value = (isNaN(newTotalHT)?PUHT+" ---":troncature(newTotalHT, true));
		//PriceHTWidget.value = (isNaN(PUHT)?" ---":troncature(PUHT, true));
		return new Array(newTotalHT, TVA);
	}
}
