/**
 * Valide les champs d'un formulaire via javascript côté client
 * EXEMPLE:
 *	 requiredFields = new Array(
 *		new Array('Actor_Name', REQUIRED, NONE, 'nom'),
 *		new Array('SupplierCustomer_TotalDays', NONE, 'int', 'nombre de jours'),
 *		new Array('SupplierCustomer_MaxIncur', NONE, 'int', 'encours maximum')
 *		);
 * 	 var isValid = checkForm(document.forms[0], requiredFields);
 *   if(isValid) {
 *   	...
 *   } else {
 *      ...
 *   }
 * @version $Id: checkForm.js,v 1.4 2008-05-07 16:36:39 ben Exp $
 * @copyright 2004 Global-Logistics
 */

NONE = 0;
REQUIRED = 1;
REQUIRED_AND_NOT_ZERO = 2;
REQUIRED_AND_NOT_DIESE = 3;  // Sert pour les select issus de CreateArrayIDFromCollection()

function checkForm(form, fields){
	var formIsValid = true;
	var errors = checkForm_0 + ":";
	for(var i=0; i<fields.length; i++){
		var mapref = fields[i];
		var field = form.elements[mapref[0]];
		if(field.value == '' && mapref[1] == REQUIRED){
			formIsValid = false;
			errors += "\n- " + checkForm_1 + " \"" + mapref[3] + "\" " + checkForm_2 + ".";
		}
		if((field.value == '' || field.value == '0') && mapref[1] == REQUIRED_AND_NOT_ZERO){
			formIsValid = false;
			errors += "\n- " + checkForm_1 + " \"" + mapref[3] + "\" " + checkForm_2 + ".";
		}
		if((field.value == '' || field.value == '##') && mapref[1] == REQUIRED_AND_NOT_DIESE){
			formIsValid = false;
			errors += "\n- " + checkForm_1 + " \"" + mapref[3] + "\" " + checkForm_2 + ".";
		}
		if(mapref[2] == 'int'){
			var formattedValue = parseInt(field.value);
			if (field.value != ''){
				if(isNaN(formattedValue)){
					formIsValid = false;
					errors += "\n- " + checkForm_1 + " \"" + mapref[3] + "\" " + checkForm_3 + ".";
					field.value = '';
				} else {
					field.value = formattedValue;
				}
			}
		}
		if(mapref[2] == 'time'){
			var formattedValue = parseInt(field.value);
			if (field.value != ''){
				if(isNaN(formattedValue)){
					formIsValid = false;
					errors += "\n- " + checkForm_1 + " \"" + mapref[3] + "\" " + checkForm_4 + ".";
					field.value = '00';
				} else {
					field.value = field.value;
				}
			}
		}
		if(mapref[2] == 'float'){
			if (field.value != ''){
			    var formattedValue = fw.i18n.extractNumber(field.value);
				if(isNaN(formattedValue) && field.value != ''){
					formIsValid = false;
					errors += "\n- " + checkForm_1 + " \"" + mapref[3] + "\" " + checkForm_5 + ".";
					field.value = '';
				} else {
					field.value = formattedValue;
				}
			}
		}
	}
	if(formIsValid == true){
		form.submit();
		return false;
	}
	alert(errors);
	return false;
}
