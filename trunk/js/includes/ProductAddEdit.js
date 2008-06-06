/**
 *
 * @version $Id$
 * @copyright 2007 ATEOR
 */

// constantes XXX Attention au modifs en bdd
MODE_LOT = 2;
DECIMAL_UNIT = 5; // correspond au premier Id des SellUnitType Kg, Litre...

requiredFields = new Array(
	new Array('BaseReference', REQUIRED, NONE, ProductAddEdit_0),
	new Array('Name', REQUIRED, NONE, ProductAddEdit_18),
	new Array('ProductType', REQUIRED_AND_NOT_ZERO, 'int', ProductAddEdit_1),
	new Array('SellUnitQuantity', REQUIRED_AND_NOT_ZERO, 'float', ProductAddEdit_2),
	new Array('SellUnitMinimumStoredQuantity', NONE, 'float', ProductAddEdit_3),
	new Array('SellUnitType', REQUIRED_AND_NOT_ZERO, 'int', ProductAddEdit_4),
	new Array('SellUnitLength', NONE, 'float', ProductAddEdit_5),
	new Array('SellUnitWidth', NONE, 'float', ProductAddEdit_6),
	new Array('SellUnitHeight', NONE, 'float', ProductAddEdit_7),
	new Array('SellUnitWeight', NONE, 'float', ProductAddEdit_8),
	new Array('UnitNumberInConditioning', NONE, 'int', ProductAddEdit_9),
	new Array('UnitNumberInPackaging', NONE, 'int', ProductAddEdit_10),
	new Array('UnitNumberInGrouping', NONE, 'int', ProductAddEdit_11),
	new Array('SellUnitGerbability', NONE, 'int', ProductAddEdit_12),
	new Array('ConditioningGerbability', NONE, 'int', ProductAddEdit_13),
	new Array('PackagingGerbability', NONE, 'int', ProductAddEdit_14),
	new Array('GroupingGerbability', NONE, 'int', ProductAddEdit_15)
	);

// pre-check avant checkForm
function checkProductForm(form) {
    var sut = form.elements['SellUnitType'].value;
    var sut_qty = form.elements['SellUnitQuantity'].value;
    var sut_min_qty = form.elements['SellUnitMinimumStoredQuantity'].value;
    // si l'uv selectionnée n'est pas de type kg, litre etc... il faut que ce
    // soit un entier et non un float
    if (parseInt(sut) < DECIMAL_UNIT) {
        if (/[0-9]+[,.][0-9]+/.test(sut_qty)) {
            alert(ProductAddEdit_16);
            return false;
        }
         if (/[0-9]+[,.][0-9]+/.test(sut_min_qty)) {
            alert(ProductAddEdit_17);
            return false;
        }
    }
    checkForm(form, requiredFields);
}

function enableTracingModeRanges(value, form){
	var tmbegin = form.elements['TracingModeBeginRange'];
	var tmend = form.elements['TracingModeEndRange'];
	tmbegin.disabled = value==MODE_LOT?false:true;
	tmend.disabled = value==MODE_LOT?false:true;
}
