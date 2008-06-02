var TRACINGMODE_SN = 1; // Equivalent a la definition dans ConcreteProduct.const.php
var TRACINGMODE_LOT = 2;
var SORTIE = 1;

/*
 * Met a jour le chps texte 'Montant de l'avoir utilisable' de InvoiceCommandList.php
 */
function checkAndSubmit() {
    // Si SN: nbre de saisies attendues et si LOT, somme des Qtes saisies attendue
    var waitedCPdtNumber = getWaitedCPdtNumber();

    // Si entree, verif des saisies de dates
    if (document.forms[0].elements["MvtTypeEntrieExit"].value != SORTIE
        && document.forms[0].elements["EndOfLifeDate[]"]) {
        // un seuldocument.forms[0].elements["EndOfLifeDate[]"])
        if (document.forms[0].elements["EndOfLifeDate[]"].length == undefined) {
            dateWidget = document.forms[0].elements["EndOfLifeDate[]"];
            if (dateWidget.disabled == false && checkDate(dateWidget.value) == false) {
                alert(LocationConcreteProductList_6);
                return false;
            }
        }
        else {
            for (var i=0;i < document.forms[0].elements["EndOfLifeDate[]"].length;i++) {
                dateWidget = document.forms[0].elements["EndOfLifeDate[]"][i];
                if (dateWidget.disabled == false && checkDate(dateWidget.value) == false) {
                    alert(LocationConcreteProductList_6);
                    return false;
                }
            }
        }
    }

    // Si tracing au SN
    if (window.opener.document.forms[0].elements["TracingMode"].value == TRACINGMODE_SN) {
        var nbFilled = 0;  // nb de SN saisis
        // un seul LCP ds le grid
        if(document.forms[0].elements["SerialNumber[]"].length == undefined){
            if (document.forms[0].elements["SerialNumber[]"].value != ''){
                nbFilled = 1;
            }
        }
        else {
            for(var i=0;i < document.forms[0].elements["SerialNumber[]"].length;i++) {
                if (document.forms[0].elements["SerialNumber[]"][i].value != ''){
                    for(var j=0;j < document.forms[0].elements["SerialNumber[]"].length;j++) {
                        if (document.forms[0].elements["SerialNumber[]"][i].value == document.forms[0].elements["SerialNumber[]"][j].value
                                && i != j) {
                            alert(LocationConcreteProductList_0);
                            return false
                        }
                    }
                    nbFilled++;
                }
            }
        }
        if (nbFilled == 0){
            alert(LocationConcreteProductList_1);
            return false;
        }
        else if (nbFilled < waitedCPdtNumber) {
            if (!confirm(LocationConcreteProductList_2)) {
                return false;
            }
        }
        return document.forms[0].submit();
    }

    // Si tracing au LOT
    if (window.opener.document.forms[0].elements["TracingMode"].value == TRACINGMODE_LOT) {
        var tooMuchQty = false;
        var qtyFilled = 0;  // somme des Qtes saisies
        if(document.forms[0].elements["CPQuantity[]"].length == undefined){ // un seul LCP ds le grid
            qtyFilled = troncature(document.forms[0].elements["CPQuantity[]"].value);
            // Qte saisie impossible car superieure au max en stock
            if (document.forms[0].elements["MvtTypeEntrieExit"].value == SORTIE
                    && qtyFilled > troncature(document.forms[0].elements["Quantity[]"].value)) {
                tooMuchQty = true;
            }
        }
        else {
            for(var i=0;i < document.forms[0].elements["CPQuantity[]"].length;i++) {
                if (troncature(document.forms[0].elements["CPQuantity[]"][i].value) != 0){
                    qtyFilled += troncature(document.forms[0].elements["CPQuantity[]"][i].value);
                    // Qte saisie impossible car superieure au max en stock
                    if (document.forms[0].elements["MvtTypeEntrieExit"].value == SORTIE
                            && troncature(document.forms[0].elements["CPQuantity[]"][i].value)
                            > troncature(document.forms[0].elements["Quantity[]"][i].value)) {
                        tooMuchQty = true;
                    }
                }
            }
        }
        if (tooMuchQty){
            alert(LocationConcreteProductList_3);
            return false;
        }
        else if (qtyFilled == 0){
            alert(LocationConcreteProductList_4);
            return false;
        }
        else if (qtyFilled != waitedCPdtNumber) {
            if (!confirm(LocationConcreteProductList_5)) {
                return false;
            }
        }
        document.forms[0].submit();
        return true;
    }
}

/*
 * Calcule le Nbre de ConcreteProduct a choisir
 */
function getWaitedCPdtNumber() {
    var result = 0;
    var nbLPQ = window.opener.document.forms[0].elements["lpqId[]"].length;
    if (nbLPQ == undefined) {
       result = troncature(window.opener.document.forms[0].elements["QuantityArray[]"].value);
    }
    else {
        for(var i=0;i < nbLPQ ;i++) {
            result += troncature(window.opener.document.forms[0].elements["QuantityArray[]"][i].value);
        }
    }
    return result;
}
