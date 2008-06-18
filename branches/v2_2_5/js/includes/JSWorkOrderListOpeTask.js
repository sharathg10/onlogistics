/**
 * Permet de changer de vue entre les opérations et les tâches d'un ordre de travail.
 * @version $Id$
 */
function toggle(value, OtId){
	var choice = document.forms['formSession'].elements['choice']
	return window.location.href = 'WorkOrderOpeTaskList.php?choice=' + 
   		choice.options[choice.selectedIndex].value + '&OtId=' + OtId;
}
