/**
 *
 * $Id$
 * @copyright 2006 ATEOR
 */
/**
 *
 * @access public
 * @return void
 **/
function checkDate() {
	if ($("withdate").checked == true) {
		$("Date").style.display="block";
	}
	else {
		$("Date").style.display="none";
	}
}

connect(window, 'onload', checkDate);
