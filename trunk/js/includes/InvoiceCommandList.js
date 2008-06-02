var AVOIR = 5;  // equivalent a la definition dans SupplierCustomer.php : pas totalemt clean!!

function CheckedAll() {

	if (document.forms[0].elements["gridItems"].checked == true) {
		if (document.forms[0].elements["gridItems[]"].length == undefined) {
			document.forms[0].elements["gridItems[]"].checked = true;
		}
		else {
			for(var i=0;i < document.forms[0].elements["gridItems[]"].length ;i++) {
				document.forms[0].elements["gridItems[]"][i].checked = true;
			}
		}
	}
	else {
		if (document.forms[0].elements["gridItems[]"].length == undefined) {
			document.forms[0].elements["gridItems[]"].checked = false;
		}
		else {
			for(var i=0;i < document.forms[0].elements["gridItems[]"].length ;i++) {
				document.forms[0].elements["gridItems[]"][i].checked = false;
			}
		}
	}
}

function RecalculateTotalInvoiceTTC() {
	var TotalPriceTTC = 0;
	if(document.forms[0].elements["gridItems[]"].length == undefined){ // une seule facture pour la commande
		if ((document.forms[0].elements["gridItems[]"].checked == true)){
			TotalPriceTTC = fw.i18n.extractNumber(document.forms[0].elements["ToPayTTC[]"]);
			document.forms[0].elements["NoLine[]"].value = 0;
			// Sert a afficher le titre dans le popup des Avoirs
			document.forms[0].elements["SelectedDocumentNo[]"].value = document.forms[0].elements["DocumentNo[]"].value
					+ " (" + fw.i18n.formatNumber(document.forms[0].elements["ToPayTTC[]"].value) + " €)";
		}
	}
	else {
		for(var i=0;i < document.forms[0].elements["gridItems[]"].length;i++) {
			if ((document.forms[0].elements["gridItems[]"][i].checked == true)){
				document.forms[0].elements["NoLine[]"][i].value = i;
				ToPayTTC = fw.i18n.extractNumber(document.forms[0].elements["ToPayTTC[]"][i]);
				TotalPriceTTC = add(TotalPriceTTC, ToPayTTC);
				// Sert a afficher le titre dans le popup des Avoirs
				document.forms[0].elements["SelectedDocumentNo[]"][i].value = document.forms[0].elements["DocumentNo[]"][i].value
						+ " (" + fw.i18n.formatNumber(document.forms[0].elements["ToPayTTC[]"][i].value) + " €)";
			}
		}
	}

    $('invoicepriceTTC').value = isNaN(TotalPriceTTC)?"---":fw.i18n.formatNumber(TotalPriceTTC);
	$('displayButton').style.display=(TotalPriceTTC == 0)?'none':'block';
}

function imprimer() {
	$('Hiddenprint').value = 1;
}

function numOfItemChecked() {
	if(document.forms[0].elements["gridItems[]"].length == undefined){
		return document.forms[0].elements["gridItems[]"].checked==true?1:0;
	} else {
		var num = 0;
		for(var i=0;i < document.forms[0].elements["gridItems[]"].length;i++) {
			if ((document.forms[0].elements["gridItems[]"][i].checked == true)){
				num++;
			}
		}
		return num;
	}
	return 0;
}

function uncheckAll() {
	if (document.forms[0].elements["gridItems[]"].length == undefined) {
		document.forms[0].elements["gridItems[]"].checked = false;
	} else {
		for(var i=0;i < document.forms[0].elements["gridItems[]"].length ;i++) {
			document.forms[0].elements["gridItems[]"][i].checked = false;
		}
	}
}

function validation() {
	$('valider').disabled = true;//empeche de regler plusieurs fois la meme facture

	if ($('Hiddenprint').value == 1) {
		if (numOfItemChecked() != 1) {
			alert(InvoiceCommandList_0);
			$('Hiddenprint').value = 0;
			uncheckAll();
			$('valider').disabled = false;
			return false;
		}
		return true;
	}
	if ($('HiddenCommandState').value == 8) {// correspond a un etat REGLEMT_TOTAL
		alert(InvoiceCommandList_1);
		$('valider').disabled = false;
		return false;
	}

	var invoicepriceTTC = fw.i18n.extractNumber($('invoicepriceTTC'));
	var RemainingPaymentPriceTTC = fw.i18n.extractNumber($('RemainingPaymentPriceTTC'));
	if (invoicepriceTTC < 0) {
		alert(InvoiceCommandList_2);
		$('valider').disabled = false;
		return false;

	}
	if (invoicepriceTTC < RemainingPaymentPriceTTC) {
		if (confirm(InvoiceCommandList_3) == false) {
			$('valider').disabled = false;
			return false;
		}
	}
	if (invoicepriceTTC > RemainingPaymentPriceTTC) {
		alert(InvoiceCommandList_4);
		$('valider').disabled = false;
		return false;
	}


	//}

	// CONTROLES RELATIFS A L'AVOIR
	// on considere qu'on utilise un avoir si les 2 conditions suivantes sont remplies:
	// * Le mode de payment Avoir est selectionne
	// * On a bien selectionne un Avoir (c a d ToHaveRemainingTTC != 0)
	// test si on utilise un avoir: le montant regle est <= celui restant de l'avoir
	// Le mode de Payment selectionne doit etre "Avoir"
	var ToHaveRemainingTTC = fw.i18n.extractNumber($('ToHaveRemainingTTC'));

	if ($('modality').value == AVOIR && ToHaveRemainingTTC != 0 && invoicepriceTTC > ToHaveRemainingTTC) {
		alert(InvoiceCommandList_5);
		$('valider').disabled = false;
		return false;
	}

	if (ToHaveRemainingTTC == 0 && $('modality').value == AVOIR) {
		alert(InvoiceCommandList_6);
		$('valider').disabled = false;
		return false;
	}

	if (numOfItemChecked() == 0) {
		alert (InvoiceCommandList_7);
		$('valider').disabled = false;
		return false;
	}
	return true;
}

/**
 * Masque le select des ActorBankDetail si payment avec un avoir
 * @return void
 **/
function showHiddenBank() {
    $('bankLabelDiv').style.display=($('modality').value == AVOIR)?'none':'block';
    $('bankDiv').style.display=($('modality').value == AVOIR)?'none':'block';
}
