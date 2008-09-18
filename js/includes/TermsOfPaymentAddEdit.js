/**
 * $Source: /home/cvs/onlogistics/js/includes/RessourceGroupAddEdit.js,v $
 * 
 * @version $Id: RessourceGroupAddEdit.js 9 2008-06-06 09:12:09Z izimobil $
 * @copyright © 2005 - ATEOR - All rights reserved.
 */

/**
 * Initialise le tableau des ressources avec les données du serveur.
 *
 * @access public
 * @return void
 */
initialize = function() {
    var objID = $('objID').value;
    var filter = {'TermsOfPayment': objID};
    var d = fw.ajax.call(
        'loadCollection',
        'TermsOfPaymentItem',
        filter,
        {},
        ['PercentOfTotal', 'PaymentDelay', 'PaymentOption', 'PaymentEvent', 'PaymentModality']
    );
    var onSuccess = function (data) {
        // contruire les options
        if (!data.length || data.length == 0) {
            return;
        }
        if (data.isError) {
            return onError(data.errorMessage+' (code: '+data.errorCode+')');
        }
        for(var i=0; i<data.length; i++) {
            onAddTOPI(false, data[i]);
        }
    }
    var onError = function (err) {
        alert('Erreur: ' + err);
    }
    d.addCallbacks(onSuccess, onError);
}

/**
 * Ajout d'un RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
onAddTOPI = function(eventObj, itemData) {
    var item = newTOPIItem(itemData);
    appendChildNodes($('TOPIUL'), item);
    hideElement(item);
    appear(item);
}

/**
 * Suppression d'un RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
onDelTOPI = function(nodeID) {
    //fade($('TOPILI'+nodeID), {duration: 0.3});
    removeElement($('TOPILI'+nodeID));
}

/**
 * Crée un élément de la liste des RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
newTOPIItem = function(itemData) {
    var index = $('TOPIUL').childNodes.length;
    if (typeof(itemData) != 'undefined') {
        var percentOfTotal  = itemData.percentOfTotal;
        var paymentDelay    = itemData.paymentDelay;
        var paymentOption   = itemData.paymentOption.value;
        var paymentEvent    = itemData.paymentEvent.value;
        var paymentModality = itemData.paymentModality.value;
    } else {
        var percentOfTotal  = '0';
        var paymentDelay    = '0';
        var paymentOption   = 0;
        var paymentEvent    = 0;
        var paymentModality = 0;
    }
    return LI(
        {id: 'TOPILI'+index, style: 'padding: 3px; border: 1px #fff outset;'},
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'TOPI_PercentOfTotal' + index}, TermsOfPaymentAddEdit_1 + ': '),
            INPUT({
                value: percentOfTotal,
                type : 'text',
                name : 'TOPI_PercentOfTotal[]',
                id   : 'TOPI_PercentOfTotal' + index
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'TOPI_PaymentDelay' + index}, TermsOfPaymentAddEdit_2 + ': '),
            INPUT({
                value: paymentDelay,
                type : 'text',
                name : 'TOPI_PaymentDelay[]',
                id   : 'TOPI_PaymentDelay' + index
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'TOPI_PaymentModality' + index}, TermsOfPaymentAddEdit_5 + ': '),
            newSelect(
                'TOPI_PaymentModality[]',
                'callStaticMethod',
                'TermsOfPaymentItem',
                'getPaymentModalityConstArray',
                paymentModality,
                false
            )
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'TOPI_PaymentOption' + index}, TermsOfPaymentAddEdit_3 + ': '),
            newSelect(
                'TOPI_PaymentOption[]',
                'callStaticMethod',
                'TermsOfPaymentItem',
                'getPaymentOptionConstArray',
                paymentOption,
                false
            )
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'TOPI_PaymentEvent' + index}, TermsOfPaymentAddEdit_4 + ': '),
            newSelect(
                'TOPI_PaymentEvent[]',
                'callStaticMethod',
                'TermsOfPaymentItem',
                'getPaymentEventConstArray',
                paymentEvent,
                false
            )
        ),
        SPAN(
            null,
            INPUT({
                'type': 'button',
                'class': 'button',
                'value': A_Delete,
                'onclick':'onDelTOPI('+index+')'
            })
        )
    );
}

/**
 * Crée un select avec les données serveur
 *
 * @access public
 * @return void
 */
newSelect = function(name, method, arg1, arg2, selected, addBlankItem) {
    if (arg2) {
        var d = fw.ajax.call(method, arg1, arg2);
    } else {
        var d = fw.ajax.call(method, arg1);
    }
    var index = $('TOPIUL').childNodes.length;
    var select = createDOM(
        'select', 
        {'name': name, 'id': name+index}
    );
    var onSuccess = function (data) {
        // contruire les options
        if (data.isError) {
            return onError(data.errorMessage+' (code: '+data.errorCode+')');
        }
        var nodes = new Array();
        for(var i=0; i<data.length; i++) {
            if (selected == data[i].id) {
                var attrs = {'value': data[i].id, 'selected': 'selected'};
            } else {
                var attrs = {'value': data[i].id};
            }
            nodes[i] = createDOM('option', attrs, data[i].toString);
        }
        if (addBlankItem) {
            item = createDOM('option', {'value': 0}, ProductPriceAddEdit_4);
            appendChildNodes($(name+index), new Array(item));
        }
        appendChildNodes($(name+index), nodes);
    }
    var onError = function (err) {
        alert(E_Error_Title + ': ' + err);
    }
    d.addCallbacks(onSuccess, onError);
    return select;
}

function checkAndFormat(evt) {
    var elts = $('TermsOfPaymentAddEdit').elements;
    var linesLength = elts["TOPI_PaymentOption[]"].length;
	if (!linesLength) {
		return true;
	}
    var subtotal = 0;
    for(var i = 0; i < linesLength; i++) {
        var percentOfTotal = elts["TOPI_PercentOfTotal[]"][i].value.replace(' ', '');
        var paymentDelay   = elts["TOPI_PaymentDelay[]"][i].value.replace(' ', '');
        if (!/^\d+[\.,]?\d*$/.test(percentOfTotal)) {
            alert(TermsOfPaymentAddEdit_6);
            return evt.stop();
        }
        if (!/^\d+$/.test(paymentDelay)) {
            alert(TermsOfPaymentAddEdit_7);
            return evt.stop();
        }
        percentOfTotal = fw.i18n.extractNumber(percentOfTotal);
        elts["TOPI_PercentOfTotal[]"][i].value = percentOfTotal;
        subtotal += percentOfTotal;
        if (subtotal > 100) {
            alert(TermsOfPaymentAddEdit_8);
            return evt.stop();
        }
    }
    if (subtotal != 100) {
        alert(TermsOfPaymentAddEdit_9);
        return evt.stop();
    }
    return true;
}

connect(window, 'onload', function() {
    initialize();
    connect('addTOPI', 'onclick', onAddTOPI);
    connect('TermsOfPaymentAddEdit', 'onsubmit', checkAndFormat);
});
