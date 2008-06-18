
// equivalent a la definition dans SupplierCustomer.php : pas totalemt clean!!
AVOIR = 5;

/**
 * Permet d'aficher dans le titre de la page les [No_facture (reste_a_regler_TTC €)]
 * separes par des ','
 */
function displaySelectedInvoice() {
    var openerForm = window.opener.document.forms[0].elements;
    var nbInvoice = openerForm["SelectedDocumentNo[]"].length;
    if (nbInvoice == undefined) {
        document.write(openerForm["SelectedDocumentNo[]"].value);
    } else {
        for(var i=0;i < nbInvoice ;i++) {
            document.write(openerForm["SelectedDocumentNo[]"][i].value);
            if (i < nbInvoice - 1) {
                document.write(",&nbsp;");
            }
        }
    }
}

/**
 * Permet d'aficher de cocher l'item selectionne precedemment
 * si re-ouverture du popup
 */
function SelectGridItems() {
    var openerForm = window.opener.document.forms[0].elements;
    var selfForm = document.forms[0].elements;
    if (openerForm["selectedToHaveId"].value != "0") {
        if(selfForm["gridItems[]"].length == undefined){ // un seul avoir ds le grid
            selfForm["gridItems[]"].checked = true;
            return true;
        }
        for(var i=0;i < selfForm["gridItems[]"].length;i++) {
            if (selfForm["gridItems[]"][i].value == openerForm["selectedToHaveId"].value){
                selfForm["gridItems[]"][i].checked = true;
                return true;
            }
        }
    }
    return false;
}

/**
 * Met a jour le chps texte 'Montant de l'avoir utilisable' de InvoiceCommandList.php
 */
function updateOpener() {
    var selfForm = document.forms[0].elements;
    var openerForm = window.opener.document.forms[0].elements;
    if(!selfForm["gridItems[]"].length && selfForm["gridItems[]"].checked == true) {
        floatRemainingTTC = fw.i18n.extractNumber(selfForm["RemainingTTC[]"]);
        InvoiceTotalPriceTTC = fw.i18n.extractNumber(openerForm["InvoiceTotalPriceTTC"]);
        // Total réglé: min(Avoir, TotalFactures), affiche sous forme de string formatee
        minTTC = (Math.min(floatRemainingTTC,InvoiceTotalPriceTTC)==floatRemainingTTC)?
            floatRemainingTTC : openerForm["InvoiceTotalPriceTTC"].value;
        openerForm["invoicepriceTTC"].value = fw.i18n.formatNumber(minTTC);
        openerForm["ToHaveRemainingTTC"].value = fw.i18n.formatNumber(floatRemainingTTC);
        openerForm["selectedToHaveId"].value = selfForm["gridItems[]"].value;
        updateOpenerSelects();
        window.close();
    } else {
        var nbChecked = 0;  // nb de gridItems selectionnes
        var selectedToHaveId = '0';
        var floatRemainingTTC = 0;
        for(var i=0;i < selfForm["gridItems[]"].length;i++) {
            if (selfForm["gridItems[]"][i].checked == true){
                nbChecked++;
                if (nbChecked > 1) {
                    alert(PaymentToHaveList_0);
                    return false;
                }
                floatRemainingTTC = fw.i18n.extractNumber(selfForm["RemainingTTC[]"][i]);
                selectedToHaveId = selfForm["gridItems[]"][i].value;
            }
        }
        if (nbChecked == 0) {
            alert(PaymentToHaveList_0);
            return true;
        } else {
            InvoiceTotalPriceTTC = fw.i18n.extractNumber(openerForm["InvoiceTotalPriceTTC"]);
            openerForm["ToHaveRemainingTTC"].value = fw.i18n.formatNumber(floatRemainingTTC);
            openerForm["selectedToHaveId"].value = selectedToHaveId;
            // Total réglé: min(Avoir, TotalFactures), affiche sous forme de string formatee
            minTTC = (Math.min(floatRemainingTTC,InvoiceTotalPriceTTC)==floatRemainingTTC)?
                floatRemainingTTC : InvoiceTotalPriceTTC;
            openerForm["invoicepriceTTC"].value = fw.i18n.formatNumber(minTTC);
            updateOpenerSelects();
            window.close();
        }
    }
}

/*
 * Met a jour les chps 'Paiement' et 'Banque' de InvoiceCommandList.php
 * @return void
 */
function updateOpenerSelects() {
    var openerDoc = window.opener.document;
    withDocument(openerDoc, function () {
        fw.dom.selectOptionByValue('modality', AVOIR);
        fw.dom.selectOptionByValue('actorbankdetail', 0);
        hideElement($("bankLabelDiv"));
        hideElement($("bankDiv"));
    });
}
