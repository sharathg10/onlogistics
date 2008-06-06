/**
 * $Source: /home/cvs/onlogistics/js/includes/AccountAddEdit.js,v $
 *
 * @version   CVS: $Id$
 * @copyright 2002-2007 ATEOR - All rights reserved
 */

updateTVASelect = function() {
    if($('Account_BreakdownType').value != 1) {
        $('Account_TVA_ID').disabled=true;
    } else {
        $('Account_TVA_ID').disabled=false;
    } 
}

connect(window, 'onload', function() {
    updateTVASelect();
});
