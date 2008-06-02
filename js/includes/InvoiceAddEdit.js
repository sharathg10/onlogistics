/**
 *
 * $Id: InvoiceAddEdit.js,v 1.17 2008-05-07 16:36:39 ben Exp $
 * @copyright 2006 ATEOR
 */

/**
 * Constantes pour l'etat d'un ActivatedMovement: correspond a l'attribut State
 * Correspond a celles definies dans ActivatedMovement.const.php
 **/
var CREE = 0;
/*var ACM_EN_COURS = 1;
var ACM_EXECUTE_TOTALEMENT = 2;*/
var ACM_EXECUTE_PARTIELLEMENT = 3;
//var BLOQUE = 4;

var totalHT=0, totalTTC=0
DynAPI.setLibraryPath('js/dynapi/src/lib/');
DynAPI.include('dynapi.api.*');

//DynAPI.onLoad = function() {
connect(window, 'onload', function() {
    RecalculateTotal(true);
	PageIsLoaded();
//}
});


/*
 * Permet de griser certains checkbox du Grid lorsque non facturable
 * Si Commande Client:
 * 	les ACM pas encore execute sont affiches mais on desactive le checkbox
 */
function PageIsLoaded() {
	var nbreCheckbox = 0;
	nbItems = (document.forms[0].elements["gridItems[]"].length == undefined)?1:document.forms[0].elements["gridItems[]"].length;
  	if (document.forms[0].elements["HiddenCmdType"].value == "Client") {
		for(var i=0;i < nbItems;i++) {
			HiddenActMvtState = (nbItems==1)?fw.i18n.extractNumber(document.forms[0].elements["HiddenActMvtState[]"].value):fw.i18n.extractNumber(document.forms[0].elements["HiddenActMvtState[]"][i].value);
			// Remarque: State != BLOQUE, car on fait la verif sur Cmde::State ds InvoiceAdEdit.php
			if (HiddenActMvtState == CREE/* || HiddenActMvtState == EXECUTE_PARTIELLEMENT*/) {
				if (nbItems ==1) document.forms[0].elements["gridItems[]"].disabled = true;
				else document.forms[0].elements["gridItems[]"][i].disabled = true;
				nbreCheckbox++;
			}
		}
  	}

	if (nbreCheckbox == nbItems) {
	  	document.forms[0].elements["CoutPortPackingInsurance"].disabled = true;
	}
}

/*
 * Appelee lors de click sur checkbox pour prendre en compte les frais
 * Affiche une alerte et ...
 **/
function CoutPortPackingInsuranceClick() {
	if (document.forms[0].elements["CoutPortPackingInsurance"].checked == true) {
		alert(InvoiceAddEdit_2);
	}
	else {
		alert(InvoiceAddEdit_3);
	}
	with (document.forms[0]) {
    	if (elements["CoutPortPackingInsurance"].checked == false) {
            if(elements["Port"].value != ''){
    			FPort = fw.i18n.extractNumber(elements["Port"]);
    			PortTVA = parseFloat(elements["port_tva_rate"].value);
                // Remplissage du detail TVA:
                if (PortTVA > 0) {
                    tvaTotalElement = getTVAwidget(PortTVA);
    			    tvaTotalElement.value = subs(fw.i18n.extractNumber(tvaTotalElement), troncature(div(mul(FPort, PortTVA), 100)));
                    elements["hiddenPortTVA"].value = 0;
                }
    		}
    		if(elements["Emballage"].value != '') {
    			FEmballage = fw.i18n.extractNumber(elements["Emballage"]);
    			EmballageTVA = parseFloat(elements["packing_tva_rate"].value);
    			// Remplissage du detail TVA:
    			if (EmballageTVA > 0) {
    			    tvaTotalElement = getTVAwidget(EmballageTVA);
    			    tvaTotalElement.value = subs(fw.i18n.extractNumber(tvaTotalElement), troncature(div(mul(FEmballage, EmballageTVA), 100)));
    			    elements["hiddenEmballageTVA"].value = 0;
                }
    		}
    		if(elements["Assurance"].value != '' ) {
    			FAssurance = fw.i18n.extractNumber(elements["Assurance"]);
    			AssuranceTVA = parseFloat(elements["insurance_tva_rate"].value);
    			// Remplissage du detail TVA:
    			if (AssuranceTVA > 0) {
    			    tvaTotalElement = getTVAwidget(AssuranceTVA);
    			    tvaTotalElement.value = subs(fw.i18n.extractNumber(tvaTotalElement), troncature(div(mul(FAssurance, AssuranceTVA), 100)));
                    elements["hiddenAssuranceTVA"].value = 0;
                }
    		}
    	}
	}
}

function validation() {
	if (document.forms[0].elements["totalpriceTTC"].value == " ---") {
		alert(InvoiceAddEdit_4);
		return false;
	}
	if (!confirm(InvoiceAddEdit_5)) {
	    return false;
	}
	document.forms[0].elements["envoyer"].disabled = true;
	return true;
}

/*
 * Recupere le widget correspondant au taux de tva passe en parametre
 *
 * @param float tvaRate
 * @return object "document.forms.element"
 **/
function getTVAwidget(tvaRate) {
	// Si le taux est vide on retourne un hidden bidon
	/*  Pour distinguer si 1 type de TVA ou plusieurs */
    if (document.forms[0].elements["tvaTotal[]"].length == undefined) {
	    return document.forms[0].elements["tvaTotal[]"];
    }
    nbTVAItems = document.forms[0].elements["tvaTotal[]"].length;

    for(i=0; i<nbTVAItems; i++){
	    if(document.forms[0].elements["tvaTotalRate[]"][i].value == tvaRate) {
		    return document.forms[0].elements["tvaTotal[]"][i]; // i;
	    }
    }
}

/*
 * Formate le contenu des widgets de tva pour affichage correct
 *
 * @return void
 **/
function beatifyTVAwidget() {
	/*  Pour distinguer si 1 type de TVA ou plusieurs */
    if (document.forms[0].elements["tvaTotal[]"]) {
	    if (document.forms[0].elements["tvaTotal[]"].length == undefined) {
		    initValue = document.forms[0].elements["tvaTotal[]"].value;
		    document.forms[0].elements["tvaTotal[]"].value = troncature(initValue, true)
	    }
    	nbTVAItems = document.forms[0].elements["tvaTotal[]"].length;

	    for(i=0; i<nbTVAItems; i++){
		    initValue = document.forms[0].elements["tvaTotal[]"][i].value;
		    document.forms[0].elements["tvaTotal[]"][i].value = troncature(initValue, true)
	    }
    }
}


/**
 * Recalcule le total HT et TTC pour un item de la facture
 * et retourne un tableau (prixHT, TVA)
 *
 */
function RecalculateItemTotal(index, globalHandingFloat) {
	with (document.forms[0]) {
		var minusquantity=0;  // initialisation
		// Remise globale
		// Si globalHandingFloat pas renseigne, c'est le cas ou on ne recalcule
		// qu'une ligne de comde, donc le bloc suivant n'est pas execute en boucle
		if(!globalHandingFloat) {
            var globalHandingFloat = 0
    		if (elements["GlobalHanding"].value != '') {
                globalHandingFloat = fw.i18n.extractNumber(elements["GlobalHanding"]);
    		}
		}
		if (index != -1) {
            var quantityWidget = elements["qty[]"][index];
            var quantity = fw.i18n.extractNumber(quantityWidget.value);
			var PUHT = elements["PriceHT[]"][index].value;
			var TVA = parseFloat(elements["HiddenTVA[]"][index].value);
			var handingWidget = elements["hdg[]"][index];
			var handing = elements["hdg[]"][index].value;
			var PTHTWidget = elements["PTHT[]"][index];
			var PriceHTWidget = elements["PriceHT[]"][index];
			var TTVAWidget = elements["TTVA[]"][index];
		} else {
            var quantityWidget = elements["qty[]"];
            var quantity = fw.i18n.extractNumber(quantityWidget.value);
            // deja tronque a 2 decimales cote serveur
			var PUHT = elements["PriceHT[]"].value;
			var TVA = parseFloat(elements["HiddenTVA[]"].value);
			var handingWidget = elements["hdg[]"];
			var handing = elements["hdg[]"].value;
			var PTHTWidget = elements["PTHT[]"];
			var PriceHTWidget = elements["PriceHT[]"];
			var TTVAWidget = elements["TTVA[]"];
		}
	    if (!isNaN(quantity)) {
            quantityWidget.value = quantity;
        }

        /* prix ht avant remise globale */
        var totalBeforeHanding = troncature(mul(quantity, PUHT));
        handing = handing.replace(' ', '');
		if(handing != '' && handing != '0' && handing != '%'){
			len = handing.length - 1;
			if (/^[0-9.,]+%/.test(handing)) {
				// Remise en %
			    handing = fw.i18n.extractNumber(handing);
				if ((handing < 0)||(handing > 100)){
					alert(ProductCommand_4);
					handingWidget.value = '';
					handing = 0; // PUHT inchange
				}
				PUHT = subs(PUHT, div(mul(PUHT, handing), 100));
			} else if ((/(\/)/.test(handing))) {
				// Remise en fraction: PUHT unitaire inchange dans ce cas
				handing = handing.split('/');
				if ((handing[1] != '') || (handing[1] != 0)) {
					minusquantity = Math.floor(mul(quantity, div(handing[0], handing[1])));
				}
			} else if (/^[0-9.,]/.test(handing)) {
				// Remise en euros
			    handing = fw.i18n.extractNumber(handing);
				if (handing > PUHT) {
					alert(ProductCommand_5);
					handingWidget.value = '';
					handing = 0; // PUHT inchange
				}
				PUHT = subs(PUHT, handing);
			} else {
                handingWidget.value = '';
            }
        }
		// On prefere tronquer a 2 decimales ici, au cas ou une remise style 2.564€ ait ete saisie
        //var totalBeforeHanding = troncature(mul(subs(quantity, minusquantity), PUHT));
		var newTotalHT = troncature(mul(mul(subs(quantity, minusquantity), PUHT), subs(1, div(globalHandingFloat, 100))));
		PTHTWidget.value = (isNaN(newTotalHT)?" ---":troncature(newTotalHT, true));

        // Pour le remplissage du detail TVA:
        var ttva = troncature(div(mul(newTotalHT, TVA), 100))
        if (TVA > 0) {
            tvaTotalElement = getTVAwidget(TVA);
            if ((index == -1 && elements["gridItems[]"].checked == true)
    			|| (index > -1 && elements["gridItems[]"][index].checked == true)) {
        		// Oblige de calculer la tva par ligne, car les taux peuvent etre
                // differents pour 2 lignes
        		// On retranche avant l'ancien TTVA
        		tvaTotalElement.value = add(subs(fw.i18n.extractNumber(tvaTotalElement), fw.i18n.extractNumber(TTVAWidget.value)), ttva);
                // Total TTVA pour la ligne
                TTVAWidget.value = ttva
            }
            else {
                tvaTotalElement.value = subs(fw.i18n.extractNumber(tvaTotalElement), fw.i18n.extractNumber(TTVAWidget))
                TTVAWidget.value = 0
            }
        }
        return new Array(newTotalHT, add(newTotalHT, ttva), totalBeforeHanding);
	}
}


/*
 * @param boolean updateAll : si true, recalcule chaque ligne
 **/
function RecalculateTotal(updateAll) {
	var fPort, fEmballage, fAssurance;
	totalHT=totalTTC=totalHTBeforeHanding=0;
	
	with (document.forms[0]) {
		var linesLength = elements["PriceHT[]"].length;

		// Remise globale
		var globalHandingFloat = 0;  // initialisation
		if(elements["GlobalHanding"].value != '' ) {
			globalHandingFloat = fw.i18n.extractNumber(elements["GlobalHanding"]);
			if ((globalHandingFloat < 0) || (globalHandingFloat > 100)) {
				alert(ProductCommand_4);
				elements["GlobalHanding"].value = '';
				globalHandingFloat = 0;
			}
		}

		// Si on n'a pas besoin de refaire les calculs pour chaque ligne
        if (!updateAll) {
            if (!linesLength && elements["gridItems[]"].checked) {
    		    var tva = elements["HiddenTVA[]"].value
    		    totalHT = fw.i18n.extractNumber(elements["PTHT[]"].value);
    		    var quantityWidget = elements["qty[]"];
                var quantity = fw.i18n.extractNumber(quantityWidget);
			    var PUHT = fw.i18n.extractNumber(elements["PriceHT[]"].value);
                totalHTBeforeHanding = troncature(mul(quantity, PUHT));
    			totalTTC = troncature(mul(totalHT, add(1, div(tva, 100))));
    		} else {
    	    	for(var i=0; i < linesLength; i++) {
    	    	    if (elements["gridItems[]"][i].checked) {
        	    	    var tva = parseFloat(elements["HiddenTVA[]"][i].value);
        				var ptht = fw.i18n.extractNumber(elements["PTHT[]"][i].value);
        				totalHT  = add(totalHT, ptht);  // tronque forcement
                		var quantityWidget = elements["qty[]"][i];
                        var quantity = fw.i18n.extractNumber(quantityWidget);
        			    var PUHT = fw.i18n.extractNumber(elements["PriceHT[]"][i].value);
                        totalHTBeforeHanding = add(totalHTBeforeHanding, troncature(mul(quantity, PUHT)));
        				// totalTTC aussi tronque forcement
        				totalTTC = add(totalTTC, troncature(add(ptht, mul(ptht, div(tva, 100)))));
    	    	    }
    	    	}
    		}
        }
        else {  // Il faut refaire les calculs pour chaque ligne
            if (!linesLength) {
    			itemResult = RecalculateItemTotal(-1, globalHandingFloat);
    			if (elements["gridItems[]"].checked) {
    			    totalHT  = itemResult[0];  // deja tronque
    			    totalTTC = itemResult[1];
    			    totalHTBeforeHanding = itemResult[2];
    			}
    		} else {
    	    	for(var i=0; i < linesLength; i++) {
    				itemResult = RecalculateItemTotal(i, globalHandingFloat);
    				if (elements["gridItems[]"][i].checked) {
        				totalHT  = add(totalHT, itemResult[0]);  // tronque forcement
        				// totalTTC aussi tronque forcement
        				totalTTC = add(totalTTC, itemResult[1]);
        				totalHTBeforeHanding = add(totalHTBeforeHanding, itemResult[2]);
        			}
    	    	}
    		}
        }

        /* calcul du montant de la remise */
        var handingAmount = mul(div(totalHTBeforeHanding, 100), globalHandingFloat);

        /*  Si on doit prendre en compte des frais  */
    	if (elements["CoutPortPackingInsurance"].checked == true) {
    		if(elements["Port"].value != ''){
    			FPort = fw.i18n.extractNumber(elements["Port"]);
    			PortTVA = parseFloat(elements["port_tva_rate"].value);
    			totalHT = add(totalHT, FPort);
    			totalHTBeforeHanding = add(totalHTBeforeHanding, FPort);
    			totalTTC = add(totalTTC, troncature(mul(FPort, (add(1, div(PortTVA, 100))))));
    			// Remplissage du detail TVA:
    			if (PortTVA > 0) {
        			tvaTotalElement = getTVAwidget(PortTVA);
        			initTVAvalue = elements["hiddenPortTVA"].value;
        			// On retranche d'abord l'ancien montant de TVA correspondant
        			tvaValue = fw.i18n.extractNumber(tvaTotalElement) - initTVAvalue;
        			newTVAvalue = troncature(div(mul(FPort, PortTVA), 100));
        			tvaTotalElement.value = add(tvaValue, newTVAvalue);
    			    elements["hiddenPortTVA"].value = newTVAvalue;
                }
    		}
    		if(elements["Emballage"].value != '') {
    			FEmballage = fw.i18n.extractNumber(elements["Emballage"]);
    			EmballageTVA = parseFloat(elements["packing_tva_rate"].value);
    			totalHT = add(totalHT, FEmballage);
    			totalHTBeforeHanding = add(totalHTBeforeHanding, FEmballage);
    			totalTTC = add(totalTTC, troncature(mul(FEmballage, (add(1, div(EmballageTVA, 100))))));
    			// Remplissage du detail TVA:
    			if (EmballageTVA > 0) {
    			    tvaTotalElement = getTVAwidget(EmballageTVA);
        			initTVAvalue = elements["hiddenEmballageTVA"].value;
        			// On retranche d'abord l'ancien montant de TVA correspondant
        			tvaValue = fw.i18n.extractNumber(tvaTotalElement) - initTVAvalue;
        			newTVAvalue = troncature(div(mul(FEmballage, EmballageTVA), 100));
        			tvaTotalElement.value = add(tvaValue, newTVAvalue);
    			    elements["hiddenEmballageTVA"].value = newTVAvalue;
                }

    		}
    		if(elements["Assurance"].value != '' ) {
    			FAssurance = fw.i18n.extractNumber(elements["Assurance"]);
    			AssuranceTVA = parseFloat(elements["insurance_tva_rate"].value);
    			totalHT = add(totalHT, FAssurance);
    			totalHTBeforeHanding = add(totalHTBeforeHanding, FAssurance);
    			totalTTC = add(totalTTC, troncature(mul(FAssurance, (add(1, div(AssuranceTVA, 100))))));
    			// Remplissage du detail TVA:
    			if (AssuranceTVA > 0) {
    			    tvaTotalElement = getTVAwidget(AssuranceTVA);
        			initTVAvalue = elements["hiddenAssuranceTVA"].value;
        			// On retranche d'abord l'ancien montant de TVA correspondant
        			tvaValue = fw.i18n.extractNumber(tvaTotalElement) - initTVAvalue;
        			newTVAvalue = troncature(div(mul(FAssurance, AssuranceTVA), 100));
        			tvaTotalElement.value = add(tvaValue, newTVAvalue);
    			    elements["hiddenAssuranceTVA"].value = newTVAvalue;
    			}
    		}
    	}
  
    	// Gestion de la taxe Fodec
    	if (isNaN(totalHT)) {
    	    elements["fodecTax"].value = "---";
    	} else {
    	    var fodecTRate = parseFloat(elements["fodecTaxRate"].value);
    	    var fodecTaxMontant = mul(totalHT, div(fodecTRate, 100));
    	    totalTTC = add(totalTTC, troncature(fodecTaxMontant));
    	    elements["fodecTax"].value = troncature(fodecTaxMontant, true);
    	}
    	
		///TVAtotal = subs(troncature(totalTTC, false), troncature(totalHT, false));
    	// Gestion du timbe fiscal
    	var taxStampValue = parseFloat(elements["taxStamp"].value);
	    totalTTC = add(totalTTC, taxStampValue);
        elements["totalpriceHT"].value = isNaN(totalHT)?"---":troncature(totalHT, true);
        elements["totalpriceTTC"].value = isNaN(totalTTC)?"---":troncature(totalTTC, true);
        elements["totalpriceHTBeforeDiscount"].value = isNaN(totalHTBeforeHanding)?"---":troncature(totalHTBeforeHanding, true);
        elements["GlobalDiscount"].value = isNaN(handingAmount)?"---":troncature(handingAmount, true);
        beatifyTVAwidget();

    	if (elements["hiddenHasInvoice"].value == 1) {
    		Ins = fw.i18n.extractNumber(elements["Installment"]);
    		totalTTC = fw.i18n.extractNumber(elements["totalpriceTTC"]);
    		toPayVal = subs(totalTTC, Ins);

    		if (toPayVal < 0) {
    			toPayVal = 0;
    		}
            document.forms[0].elements["ToPay"].value = isNaN(toPayVal)?"---":troncature(toPayVal, true);
            document.forms[0].elements["Installment"].value = isNaN(Ins)?"---":troncature(Ins, true);
    	}
	}
}

/**
 * Retourne l'index du Grid pour un element de form donne
 * @param integer pdtId
 * @return void
 **/
function getItemIndex(obj) {
    var linesLength = document.forms[0].elements["gridItems[]"].length;
    	if (!linesLength) {
    		return -1;
    	} else {
        for(i = 0; i < document.forms[0].elements[obj.name].length; i++)
            if (obj == document.forms[0].elements[obj.name][i]) return i;
        }
    return -1;
}

function changeCheckedStateOfAllItemsCustom(ownerForm, newState){
    if(ownerForm.elements['gridItems[]'].length) {
    	for(var i = 0; i < ownerForm.elements['gridItems[]'].length; i++){
    	    if(ownerForm.elements['gridItems[]'][i].disabled==false) {
    		    ownerForm.elements['gridItems[]'][i].checked = newState;
    		    RecalculateItemTotal(i);
    	    }
    	}
    } else {
        if(ownerForm.elements['gridItems[]'].disabled==false) {
		    ownerForm.elements['gridItems[]'].checked = newState;
		    RecalculateItemTotal(getItemIndex(ownerForm.elements['gridItems[]']));
	    }
    }
}
