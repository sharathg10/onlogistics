/**
 *
 * @version $Id$
 * @copyright 2007 ATEOR
 */

requiredFields = new Array(
	new Array('PassedWeekNumber', REQUIRED_AND_NOT_ZERO, 'int', SupplyingOptimization_0),
	new Array('FutureWeekNumber', REQUIRED_AND_NOT_ZERO, 'int', SupplyingOptimization_1),
	new Array('DefaultDeliveryDelay', REQUIRED, 'int', SupplyingOptimization_2)
);

/**
 * Verifie qu'une date a ete selectionnee
 * @return boolean
 **/
function checkDate() {
    var d=new Date();
    var month = ((d.getMonth()+1)<10)?'0' + (d.getMonth()+1):(d.getMonth()+1);
    var day = (d.getDate()<10)?'0' + d.getDate():d.getDate();
    var today = d.getFullYear() + '-' + month + '-' + day;
    if ($('f_date_c1').value == '' || $('f_date_c1').value < today) {
        alert(SupplyingOptimization_3);
        return false;
    }
    return true;
}