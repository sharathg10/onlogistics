/**
 *
 * $Id: StockProductRealandVirtualList.js,v 1.5 2008-05-07 16:36:39 ben Exp $
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
