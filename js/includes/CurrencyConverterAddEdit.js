/**
 * @version $Id: CurrencyConverterAddEdit.js,v 1.2 2008-05-07 16:36:38 ben Exp $
 * @copyright 2008 ATEOR
 **/

connect(window, 'onload', function() {
    connect('CurrencyConverterAddEdit', 'onsubmit', checkBeforeSubmit);
});

/**
 * Divers controles:
 *  - La Meme Currency n'est pas selectionnee 2 fois
 *
 **/
checkBeforeSubmit = function(evt) {
	if ($('CurrencyConverter_FromCurrency_ID').value == $('CurrencyConverter_ToCurrency_ID').value) {
		alert(CurrencyConverterAddEdit_0);
        return evt.stop();
	}
	return true;
}
