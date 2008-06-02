/*requiredFields = new Array(
	new Array('qty[]', NONE, 'float', SiteAddEdit_3)
);*/

function WPCustomerListSubmit() {
	theform = document.forms['ClientCatalog'];
	if (theform.elements['CustomerSelected'].value == '##') {
		alert(ClientCatalog_0);
		return false;
	}
	return true;
}

function SupplierCatalogSubmit() {
	theform = document.forms['SupplierCatalog'];
	if (theform.elements['supplier'].value == '##') {
		alert(SupplierCatalog_0);
		return false;
	}
	return true;
}


/**
 * Permet de cocher la case de grid correspondante si necessaire
 * @param integer pdtId
 * @return void
 **/
function checkUncheck(pdtId) {
        field = document.forms[0].elements['qty_' + pdtId];
        var formattedValue = fw.i18n.extractNumber(field.value);
        checkBoxItem = getCheckBoxItem(pdtId);
        checkBoxItem.checked = !(isNaN(formattedValue) || field.value == '' || formattedValue == 0);
}


/**
 * Retourne l'index du Grid pour un element de form donne
 * @param integer pdtId
 * @return dom element or false
 **/
function getCheckBoxItem(pdtId) {
    var linesLength = document.forms[0].elements["gridItems[]"].length;
	if (!linesLength) {
		return document.forms[0].elements["gridItems[]"];
	} else {
    for(i = 0; i < document.forms[0].elements["gridItems[]"].length; i++)
        if (pdtId == parseInt(document.forms[0].elements["gridItems[]"][i].value)) {
            return document.forms[0].elements["gridItems[]"][i];
        }
    }
    return false;
}
