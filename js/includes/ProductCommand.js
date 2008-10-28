/**
 * ProductCommand.php
 *
 * @since 06/11/2002
 * @version $Id$
 */

var totalHT=0, totalTTC=0.
var doc = currentDocument();
var args = parseQueryString(doc.URL, true);
var SELLUNITTYPE_UB = 1;  // Unite de base
var SELLUNITTYPE_UE = 2;  // Unite d'emballage


connect(window, 'onload', function() {
    if ($('CalendarAwareOfPlanning').value != 0) {
        initializeCustomerPlanningAndUnavailabilities();
    }
	if (!document.forms[0].elements['WishedDate'][1].checked) {
        hideElement('WishedEndDate');
    }
    if (document.forms[0].elements["cmdtype"]) {
        onCadencedOrderCheckboxClicked();
	    DisplayRemExcep();
        // Si c'est une commande client, on peut modifier l'Expeditor
        if (document.forms[0].elements["cmdtype"].value == 1) {
            var d = changeExpeditor();
            var myCallback2 = function () {
                var fields = new Array("qty", "hdg", "cmdExpeditor", "cmdExpeditorSite", "cmdDestinator", "cmdDestinatorSite", "Port", "Emballage", "Assurance", "GlobalHanding", "cmdIncoterm");
                if ($('ueQtyPref').value == '1') {
                    fields.push("ueQty");
                }
                var ddd = fw.ajax.restoreWidgetsFromSession(fields);  //  "CommandItemDate",
                var myCallback3 = function () {
                    RecalculateTotal(true);
                }
                ddd.addCallback(myCallback3);
            }
            d.addCallback(myCallback2);
	    } else {
	        subd = fw.ajax.restoreWidgetsFromSession(
    	        new Array("qty", "hdg", "CommandItemDate", "cmdExpeditor", "cmdExpeditorSite", "cmdDestinator", "cmdDestinatorSite", "Port", "Emballage", "Assurance", "GlobalHanding", "cmdIncoterm")
            );
            var subdCB = function() {
                RecalculateTotal(true);
            }
            subd.addCallback(subdCB);
	    }
    }
    connect(document.forms[0], 'onsubmit', validation);
});

/**
 * Sur le onchange du select cmdExpeditor: selectionne le mainsite associe
 * @return object Deferred
 **/
function changeExpeditor() {
    var def = fw.ajax.updateSelect('cmdExpeditor', 'cmdExpeditorSite', 'Site', 'Owner', true);
    var myCallback = function () {
        var dd = fw.ajax.call(
            'productCommand_getSelectedExpeditorSite',
            $('cmdExpeditor').value,
            $('cmdDestinatorSite').value
        );
        var onSuccess = function (data) {
            fw.dom.selectOptionByValue('cmdExpeditorSite', data);
        }
        dd.addCallback(onSuccess);
    }
    def.addCallback(myCallback);
    return def;
}

function DisplayRemExcep() {
	if (document.forms[0].elements["cmdtype"].value != 1) {  // si ce n'est pas une commande client
		document.getElementById("DisplayRemExcepLabel").style.display="none";
		document.getElementById("DisplayRemExcep").style.display="none";
	}
}

function RecalculateUpdateIncur() {
	UpdateIncur = fw.i18n.extractNumber(document.forms[0].elements["HiddenUpdateIncur"]);
	Installment = fw.i18n.extractNumber(document.forms[0].elements["Installment"]);
	UpdateIncur = subs(UpdateIncur, Installment);
	// arrondi: inutile, suite a la troncature
    document.forms[0].elements["UpdateIncur"].value = isNaN(UpdateIncur)?"---":troncature(UpdateIncur, true);
}

function validation(evt) {
    if (!checkDateInputFields()) {
        return evt.stop();
    }
	with (document.forms[0]) {
		if (elements['StartDate'] && elements['StartDate'].value == '0') {
			alert(ProductCommand_11);
            return evt.stop();
        }
		if (elements['WishedDate'][1].checked && elements['EndDate'].value == '0') {
			alert(ProductCommand_12);
            return evt.stop();
        }
		var linesLength = elements["qty[]"].length;
		var errorMsg = ProductCommand_1;
		if (!linesLength) {
            var qty = parseInt(elements["qty[]"].value);
			if (isNaN(qty) || qty < 1) {
				alert(errorMsg);
                return evt.stop();
			}
		} else {
			for(var i=0;i < linesLength; i++) {
                var qty = parseInt(elements["qty[]"][i].value);
				if (isNaN(qty) || qty < 1) {
					alert(errorMsg);
                    return evt.stop();
				}
			}
		}
		if (elements["TotalTTC"].value.replace(' ', '') == "---") {
			alert(ProductCommand_0);
            return evt.stop();
		}
		// Controle du MiniAmountToOrder
		if (fw.i18n.extractNumber($('TotalHT').value) < $('MiniAmountToOrder').value) {
		    alert(ProductCommand_14 + ' (' + fw.i18n.formatNumber($('MiniAmountToOrder').value) + ' ' + $('Currency').value + '). ' + ProductCommand_15);
            return evt.stop();
		}
        // check des BuyUnitquantity
        var buyUnitQties = elements['HiddenBuyUnitQty[]'];
        if (buyUnitQties) {
            buyUnitQties = typeof(buyUnitQties.length) == 'undefined'?[buyUnitQties]:buyUnitQties;
            var qties = elements['qty[]'];
            qties = typeof(qties.length) == 'undefined'?[qties]:qties;
            for (var i=0; i < qties.length; i++) {
                var buyUnitQty = parseFloat(buyUnitQties[i].value);
                // extractNumber car c'est une saisie
                var qty = fw.i18n.extractNumber(qties[i]);
                // Si on n'a pas saisi un multiple
                if (Math.floor(div(qty, buyUnitQty)) < div(qty, buyUnitQty)) {
                    alert(ProductCommand_13 + buyUnitQty);
                    return evt.stop();
                }
            }
        }
        // controle site expediteur
        if (!elements['cmdExpeditorSite'] || !elements['cmdExpeditorSite'].value) {
            alert(ProductCommand_17);
            return evt.stop();
        }
        // controle site destinataire
        if (!elements['cmdDestinatorSite'] || !elements['cmdDestinatorSite'].value) {
            alert(ProductCommand_18);
            return evt.stop();
        }

	}
	return true;
}


function RecalculateToPay(){
	var ToPay = 0;
	var TotalTTC = fw.i18n.extractNumber(document.forms[0].elements["TotalTTC"]);
	var installment = fw.i18n.extractNumber(document.forms[0].elements["Installment"]);

	if (installment < 0) {
		alert(ProductCommand_2);
		document.forms[0].elements["Installment"].value = 0;
		RecalculateUpdateIncur();
	}

	ToPay = subs(TotalTTC, installment);
	if (ToPay < 0) {
		alert(ProductCommand_3);
		document.forms[0].elements["Installment"].value = 0;
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
function RecalculateItemTotal(index, globalHandingFloat) {
	with (document.forms[0]) {
		var minusquantity=0;  // initialisation
		// Remise globale
		// Si globalHandingFloat pas renseigne, c'est le cas ou on ne recalcule
		// qu'une ligne de comde, donc le bloc suivant n'est pas execute en boucle
		if(!globalHandingFloat) {
            var globalHandingFloat = 0
    		if (elements['GlobalHanding'] && elements["GlobalHanding"].value != '') {
                globalHandingFloat = fw.i18n.extractNumber(elements["GlobalHanding"]);
    		}
		}
		if (index != -1) {
            var quantityWidget = elements["qty[]"][index];
            var quantity = fw.i18n.extractNumber(quantityWidget.value);
			var PUHT = parseFloat(elements["HiddenPrice[]"][index].value);
			var TVA = parseFloat(elements["HiddenTVA[]"][index].value);
			var handingWidget = elements["hdg[]"][index];
			var handing = handingWidget ? handingWidget.value : '';
			var PTHTWidget = elements["PTHT[]"][index];
			var PriceHTWidget = elements["PriceHT[]"][index];
		} else {
            var quantityWidget = elements["qty[]"];
            var quantity = fw.i18n.extractNumber(quantityWidget.value);
			var PUHT = parseFloat(elements["HiddenPrice[]"].value);  // deja tronque a 2 decimales cote serveur
			var TVA = parseFloat(elements["HiddenTVA[]"].value);
			var handingWidget = elements["hdg[]"];
			var handing = handingWidget ? handingWidget.value : '';
			var PTHTWidget = elements["PTHT[]"];
			var PriceHTWidget = elements["PriceHT[]"];
		}
	    if (!isNaN(quantity)) {
            quantityWidget.value = quantity;
        }
        handing = handing.replace(' ', '');
		if((handing != '') && (handing != '0')){
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
			} else {// mauvaise saisie
				PTHTWidget.value = " ---";
				PriceHTWidget.value = " ---";
	        	return new Array("N/A", "N/A");
	        }
		}
		// On prefere tronquer a 2 decimales ici, au cas ou une remise style 2.564€ ait ete saisie
		var newTotalHT = mul(mul(subs(quantity, minusquantity), PUHT), subs(1, div(globalHandingFloat, 100)));
		PTHTWidget.value = (isNaN(newTotalHT)?" ---":troncature(newTotalHT, true));
		PriceHTWidget.value = (isNaN(PUHT)?" ---":troncature(PUHT, true));
        return new Array(newTotalHT, TVA);
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

/*
 * @param boolean updateAll : si true, recalcule chaque ligne
 **/
function RecalculateTotal(updateAll) {
	var fPort, fEmballage, fAssurance;
	totalHT=0;
	totalTTC=0;
	with (document.forms[0]) {
		// necessaire, car ProductGrid.html non 'dispo'
		elements["RemExcep"].value = elements["HiddenRemExcep"].value;
		var linesLength = elements["HiddenPrice[]"].length;

		// Remise globale
		var globalHandingFloat = 0;  // initialisation
		if (elements["GlobalHanding"] && elements["GlobalHanding"].value != '' ) {
			globalHandingFloat = fw.i18n.extractNumber(elements["GlobalHanding"]);
			if ((globalHandingFloat < 0) || (globalHandingFloat > 100)) {
				alert(ProductCommand_4);
				elements["GlobalHanding"].value = '';
				globalHandingFloat = 0;
			}
		}

		// Si on n'a pas besoin de refaire les calculs pour chaque ligne
        if (!updateAll) {
            if (!linesLength) {
    		    var tva = parseFloat(elements["HiddenTVA[]"].value)
    		    totalHT = fw.i18n.extractNumber(elements["PTHT[]"].value);
    			totalTTC = troncature(mul(totalHT, add(1, div(tva, 100))));
    		} else {
    	    	for(var i=0; i < linesLength; i++) {
                    var tva = parseFloat(elements["HiddenTVA[]"][i].value);
    				var ptht = fw.i18n.extractNumber(elements["PTHT[]"][i].value);
    				totalHT  = add(totalHT, ptht);  // tronque forcement
    				// totalTTC aussi tronque forcement
    				totalTTC = add(totalTTC, troncature(add(ptht, mul(ptht, div(tva, 100)))));
    	    	}
    		}
        }
        else {  // Il faut refaire les calculs pour chaque ligne
            if (!linesLength) {
                if ($('ueQtyPref').value == '1') {
                    getItemNbUV(-1);
                }
    			var itemResult = RecalculateItemTotal(-1, globalHandingFloat);
    			totalHT  = itemResult[0];  // deja tronque
    			totalTTC = troncature(mul(totalHT, add(1, div(itemResult[1], 100))));
    		} else {
    	    	for(var i=0; i < linesLength; i++) {
                    if ($('ueQtyPref').value == '1') {
                        getItemNbUV(i);
                    }
    				var itemResult = RecalculateItemTotal(i, globalHandingFloat);
    				totalHT  = add(totalHT, itemResult[0]);  // tronque forcement
    				// totalTTC aussi tronque forcement
    				totalTTC = add(totalTTC, troncature(add(itemResult[0], mul(itemResult[0], div(itemResult[1], 100)))));
    	    	}
    		}
        }

		/*  Les differents frais  */
		if (elements["Port"] && elements["Port"].value != ''){
			fPort = fw.i18n.extractNumber(elements["Port"]);
			totalHT  = add(totalHT, fPort);
			totalTTC = add(totalTTC, troncature(mul(fPort, (add(1, div(elements["port_tva_rate"].value, 100))))));
		}
		if (elements["Emballage"] && elements["Emballage"].value != ''){
			fEmballage = fw.i18n.extractNumber(elements["Emballage"]);
			totalHT  = add(totalHT, fEmballage);
			totalTTC = add(totalTTC, troncature(mul(fEmballage, (add(1, div(elements["packing_tva_rate"].value, 100))))));
		}
		if (elements["Assurance"] && elements["Assurance"].value != '' ){
			fAssurance = fw.i18n.extractNumber(elements["Assurance"]);
			totalHT  = add(totalHT, fAssurance);
			totalTTC = add(totalTTC, troncature(mul(fAssurance, (add(1, div(elements["insurance_tva_rate"].value, 100))))));
		}
        document.forms[0].elements["TotalHT"].value = isNaN(totalHT)?"---":troncature(totalHT, true);
        document.forms[0].elements["TotalTTC"].value = isNaN(totalTTC)?"---":troncature(totalTTC, true);
	}
	RecalculateToPay()
}

/**
 * methode Addon : Calcule les quantites reelle et virtuelle totales
 * Methode SUPPRIMEE tant qu'on ne gere pas de virtualquantity par Location
 * 
 * @param integer index indice de ligne du grid
 * @return void
 */
function getItemNbUV(index) {
    with (document.forms[0]) {
		if (index != -1) {
		    var suq = parseFloat(elements["suq[]"][index].value);
		    var ueQtyWidget = elements["ueQty[]"][index];
		    var qtyWidget = elements["qty[]"][index];
		    var unipWidget = elements["unip[]"][index];
		    var sut = parseInt(elements["sut[]"][index].value);
		} else {
            var suq = parseFloat(elements["suq[]"].value);
		    var ueQtyWidget = elements["ueQty[]"];
		    var qtyWidget = elements["qty[]"];
		    var unipWidget = elements["unip[]"];
		    var sut = parseInt(elements["sut[]"].value);
		}
		// On ne peut saisir que des entiers pour la qte d'UE
		// ueQtyWidget.value.length > 0 && isNaN(ueQty)) || 
		if (ueQtyWidget.value.length > 0 && isNaN(parseInt(ueQtyWidget.value[ueQtyWidget.value.length - 1])) ) {
            ueQtyWidget.value = 1;
		    alert(ProductCommand_16);
		}
		var ueQty = fw.i18n.extractNumber(ueQtyWidget.value);
		var qty = div(ueQty, suq);
		// Si SELLUNITTYPE_UE, ce sera qty, sinon:
        if (sut == SELLUNITTYPE_UB) {
            var unip = parseFloat(unipWidget.value);
            qty = mul(qty, unip);
        }
        qtyWidget.value = qty;
    }
}


function onCadencedOrderCheckboxClicked() {
    var triggerCB = document.getElementById('cadencedOrderCB');
    if (!triggerCB) {
        return true;
    }
    with (document.forms[0]) {
        var dateElts = elements['CommandItemDate[]'];
        dateElts = typeof(dateElts.length) == 'undefined'?[dateElts]:dateElts;
        var buttonElts = elements['AddCommandItem[]'];
        buttonElts = typeof(buttonElts.length) == 'undefined'?[buttonElts]:buttonElts;
        for (var i=0; i < dateElts.length; i++) {
            var dateElt = dateElts[i];
            var buttonElt = buttonElts[i];
            dateElt.disabled = triggerCB.checked?false:true;
            buttonElt.disabled = triggerCB.checked?false:true;
        }
    }
}

function checkDateInputFields() {
    var triggerCB = document.getElementById('cadencedOrderCB');
    if (!triggerCB || !triggerCB.checked) {
        return true;
    }
    var dateElts = document.forms[0].elements['CommandItemDate[]'];
    dateElts = typeof(dateElts.length) == 'undefined'?[dateElts]:dateElts;
    var found = 0;
    for (var i=0; i < dateElts.length; i++) {
        var dateElt = dateElts[i];
        value = dateElt.value;
        if (value == '') {
            continue;
        }
        if (!(/\d{1,2}\/\d{1,2}\/\d{4} \d{1,2}:\d{1,2}/.test(value))) {
            alert(ProductCommand_9);
            return false;
        }
        found++;
    }
    if (!found) {
        alert(ProductCommand_10);
        return false;
    }
    return true;
}

/**
 * Globales contenant les données des planning et indispos du client
 */
var planningArray = new Array();
var unavailabilityArray = new Array();

/**
 * Fonction qui récupère les planning et indisponibilités pour le site du
 * client et les stocke dans les 2 globales ci-dessus.
 *
 * @return void
 */
var initializeCustomerPlanningAndUnavailabilities = function() {
    var onSuccess = function (data) {
        if (data.isError) {
            return onError(data.errorMessage+' (code: '+data.errorCode+')');
        }
        if (data instanceof Object) {
            planningArray = data.planning;
            unavailabilityArray = data.unavailabilities;
        }
    }
    var onError = function (err) {
        alert('Erreur: ' + err);
    }
    var d = fw.ajax.call(
        'productCommand_getSitePlanningAndUnavailabilities',
        document.forms[0].elements['cmdDestinatorSite'].value
    );
    d.addCallbacks(onSuccess, onError);
}

/**
 * Fonction qui grise les jours du calendrier en fonction du planning et des
 * indisponibilités du site du client.
 * Cette fonction est le callback du paramètre dateStatusFunc du setup de js
 * calendar. 
 * Elle appelle aussi les dateStatusHandler définis dans js/jscalendar/lang/*
 * pour hightlighter les jours fériés comme c'est le cas à l'heure actuelle.
 *
 * @param  date object d
 * @param  int year optionnel (inutilisé)
 * @param  int month optionnel (inutilisé)
 * @param  int day optionnel (inutilisé)
 * @return boolean true si la date doit être grisée et false sinon
 */
var calendarDisableFunc = function(d, year, month, day) {
    // on grise toute date inférieure à la date courante
    var today = new Date();
    today.setHours(0);
    today.setMinutes(0);
    today.setSeconds(0);
    if (d.getTime() < today.getTime()) {
        return true;
    }
    // appelle la fonction définie par défaut pour les jours fériés
    var result = dateStatusHandler(d, year, month, day);
    if (result != false) {
        return result;
    }
    if ($('CalendarAwareOfPlanning').value == 0) {
        return false;
    }
    // vérifie que la date est bien dans le planning
    if (planningArray.length > 0) {
        var planning = planningArray[d.getDay()];
        if (typeof(planning) == 'undefined' || planning == null) {
            // hm... pas de planning défini pour ce jour
            return true;
        }
        if (planning[0] == 0 && planning[3] == 0) {
            // ne travaille pas ce jour la
            return true;
        }
        // vérifie que la date n'est pas dans une plage d'indisponibilité
        if (unavailabilityArray.length > 0) {
            for (var i = 0; i < unavailabilityArray.length; i++) {
                var uStart = unavailabilityArray[i][0];
                var uEnd = unavailabilityArray[i][1];
                var pStart = new Date(d.getFullYear(), d.getMonth(), d.getDate());
                pStart = parseInt(pStart.getTime().toString().substring(0, 10))
                       + parseInt(planning[0]);
                var pEnd = new Date(d.getFullYear(), d.getMonth(), d.getDate());
                pEnd = parseInt(pEnd.getTime().toString().substring(0, 10))
                     + parseInt(planning[3]);
                if (uStart <= pStart && uEnd >= pEnd) {
                    return true;
                }
            }
        }
    }
    return '';
}
