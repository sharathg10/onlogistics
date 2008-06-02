/*
 * Teste, en cas de mode de suivi par SN ou Lot,
 * si le popup des ConcreteProduct a bien ete renseigne
 *
 * @param string withWithout : 'With' si avec Prevision et 'Without' si sans prev.
 * @return bolean
 */
function checkBeforeSubmit(withWithout) {
	restoreFormParams(withWithout)
	if (document.forms[0].elements["TracingMode"].value == 0) {
		return true;
	}
	// Si le popup a ete ouvert et rempli, ou si qte mvtee est nulle
	else if (document.forms[0].elements["fillPopup"].value == 1 || getTotalQuantity() == 0) {
		return true;
	}

	alert(ActivatedMovementAdd_0);
	$('submitForm').disabled=false;
	return false;
}

/*
 * Calcule le Nbre de ConcreteProduct a choisir:
 * Somme des Quantites saisies dans le Grid
 */
function getTotalQuantity() {
	with (document.forms[0]) {
		var nbLPQ = elements["lpqId[]"].length;
		if (nbLPQ == undefined) {
			return parseFloat(elements["QuantityArray[]"].value);
		}
		else {
			var result = 0;
			for(var i=0;i < nbLPQ ;i++) {
				result += parseFloat(elements["QuantityArray[]"][i].value);
			}
			return result;
		}
	}
}

/*
 * Restaure les params par defaut du form
 *
 * @param string withWithout : 'With' si avec Prevision et 'Without' si sans prev.
 * @return void
 */
function restoreFormParams(withWithout) {
	document.forms[0].target = '_self';
	document.forms[0].action = 'ActivatedMovementAdd' + withWithout + 'PrevisionToBeExecuted.php';
    document.forms[0].method = 'POST';
}

/**
 * Verifie que la qté de Product pour une ligne est inférieure à la qté en stock
 * pour une sortie de stock
 *
 * @param object dom element
 * @return boolean
 **/
function checkMaxQtyPerLine(obj) {
    var maxQty = lineQty = 0;
    var linesLength = document.forms[0].elements["maxQtyPerLine[]"].length;
	if (!linesLength) {
		maxQty = document.forms[0].elements["maxQtyPerLine[]"].value;
		lineQty = fw.i18n.extractNumber(document.forms[0].elements["QuantityArray[]"].value);
	} else {
    for(i = 0; i < document.forms[0].elements[obj.name].length; i++)
        if (obj == document.forms[0].elements[obj.name][i]) {
            maxQty = document.forms[0].elements["maxQtyPerLine[]"][i].value;
            lineQty = fw.i18n.extractNumber(document.forms[0].elements["QuantityArray[]"][i].value);
            break;
        }
    }
    if (lineQty > maxQty) {
         alert(ActivatedMovementAdd_1);
         obj.value = 0;
     }
}