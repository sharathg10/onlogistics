/**
 * Fonctions js necessaires a l'ajout/edition de Category
 * 
 * $Source: /home/cvs/onlogistics/js/includes/CategoryAddEdit.js,v $
 * @version    CVS: $Id: CategoryAddEdit.js,v 1.7 2008-05-07 16:36:38 ben Exp $
 * @copyright  2002-2007 ATEOR - All rights reserved
 */

// Donnees sur les currency pour construire le select du grid
var serverData = {};

// initialise les données ajax sur le onload
connect(window, 'onload', initialize);

// initialize() {{{

/**
 * Initialise le cache serverData
 *
 * @access public
 * @return void
 */
function initialize() {
    connect('CategoryAddEdit', 'onsubmit', checkCurrencies);
    connect('CategoryAddEdit', 'onsubmit', checkAndFormatHandingByRange);
    deferreds = new Array();
    // Ajouter ici autant de lignes que de donnees a recuperer
    deferreds[0] = fw.ajax.call('getCollectionForSelect', 'Currency');
    l = new MochiKit.Async.DeferredList(deferreds);
    var onSuccess = function(datalist) {
        serverData['Currency'] = datalist[0][1];
    };
    var onError = function(err) {
        alert(E_Error_Title + ": " + err);
    };
    l.addCallbacks(onSuccess, onError);
}

// }}}
// addMiniAmountToOrderItem() {{{

/**
 * Ajoute un item au tableau des command items.
 *
 * @access public
 * @return void
 */
addMiniAmountToOrderItem = function() {
    var tbody = $('MiniAmountToOrder_grid_Body');
    // On supprime la ligne 'Aucun enregistrement' si mode Add
    if ($('MiniAmountToOrder_grid_TR_none')) {
        removeElement($('MiniAmountToOrder_grid_TR_none'));
    }
    var index = tbody.childNodes.length;
    // On ne recherche ces infos qu'une seule fois
    var item  = newMiniAmountToOrderItem(index);
    setStyle(item, {'opacity':0.0}); 
    appendChildNodes(tbody, item);
    Opacity(item, {from: 0.0, to: 1.0});
}

// }}}
// addHandingByRangeItem() {{{

/**
 * Ajoute un item au tableau des handing by range.
 *
 * @access public
 * @return void
 */
addHandingByRangeItem = function() {
    var tbody = $('HandingByRange_grid_Body');
    // On supprime la ligne 'Aucun enregistrement' si mode Add
    if ($('HandingByRange_grid_TR_none')) {
        removeElement($('HandingByRange_grid_TR_none'));
    }
    var index = tbody.childNodes.length;
    // On ne recherche ces infos qu'une seule fois
    var item  = newHandingByRangeItem(index);
    setStyle(item, {'opacity':0.0}); 
    appendChildNodes(tbody, item);
    Opacity(item, {from: 0.0, to: 1.0});
}

// }}}
// newMiniAmountToOrderItem() {{{

/**
 * Ajopute une ligne (TR) au tbody du grid
 *
 * @param int index l'index de la ligne
 * @access public
 * @return node
 */
newMiniAmountToOrderItem = function(index) {
    return TR(
        {id: 'MiniAmountToOrder_grid_TR_Added_' + index},
        TD(
            null,
            newSelect('Currency', 'MiniAmountToOrder_Currency[]')
        ),
        TD(
            null,
            INPUT({
                type : 'text',
                name : 'MiniAmountToOrder_Amount[]',
                size : 12
            })
        ),
        TD(
            {class: 'grid_checkbox_column'},
            INPUT({
                type : 'hidden',
                name : 'MiniAmountToOrder_Id[]',
                value: 0
            }),
            INPUT({
                type: 'button',
                class: 'button',
                value: A_Delete,
                onclick:'removeElement($("MiniAmountToOrder_grid_TR_Added_' + index + '"))'
            })
        )
    );
}

// }}}
// newHandingByRangeItem() {{{

/**
 * Ajopute une ligne (TR) au tbody du grid
 *
 * @param int index l'index de la ligne
 * @access public
 * @return node
 */
newHandingByRangeItem = function(index) {
    return TR(
        {id: 'HandingByRange_grid_TR_Added_' + index},
        TD(
            null,
            INPUT({
                type : 'text',
                name : 'HandingByRange_Percent[]',
                size : 12
            })
        ),
        TD(
            null,
            INPUT({
                type : 'text',
                name : 'HandingByRange_Minimum[]',
                size : 12
            })
        ),
        TD(
            null,
            INPUT({
                type : 'text',
                name : 'HandingByRange_Maximum[]',
                size : 12
            })
        ),
        TD(
            null,
            newSelect('Currency', 'HandingByRange_Currency[]')
        ),
        TD(
            {class: 'grid_checkbox_column'},
            INPUT({
                type : 'hidden',
                name : 'HandingByRange_Id[]',
                value: 0
            }),
            INPUT({
                type: 'button',
                class: 'button',
                value: A_Delete,
                onclick:'removeElement($("HandingByRange_grid_TR_Added_' + index + '"))'
            })
        )
    );
}

// }}}
// newSelect() {{{

/**
 * Crée un select à partir des données du cache
 *
 * @param string entity le nom de l'entite qui est l'objet du select
 * @param string wname le nom (attribut name) du widget select
 * @access public
 * @return node
 */
newSelect = function(entity, wname) {
    var nodes = new Array();
    var data = serverData[entity];
    for (var i=0; i<data.length; i++) {
        nodes[i] = createDOM('option', {value: data[i].id}, data[i].toString);
    }
    var select = createDOM('select', {'name': wname});
    appendChildNodes(select, nodes);
    return select;
}

// }}}
// checkCurrencies() {{{

/**
 * Permet de bloquer la soumission du form tant que 2 lignes concernent la meme
 * Currency
 *
 * @return boolean
 */
function checkCurrencies(evt) {
    var currencies = new Array();
    // Si on cree une Category sans MiniAmountToOrder
    if (typeof $('CategoryAddEdit').elements["MiniAmountToOrder_Currency[]"] == 'undefined') {
        return true;
    }
    var linesLength = $('CategoryAddEdit').elements["MiniAmountToOrder_Currency[]"].length;
	if (!linesLength) {
		return true;
	}
    for(i = 0; i < linesLength; i++) {
        var currentCurrency = $('CategoryAddEdit').elements["MiniAmountToOrder_Currency[]"][i].value;
        if (findValue(currencies, currentCurrency) > -1) {
            alert(CategoryAddEdit_0);
            return evt.stop();
        }
        currencies.push(currentCurrency);
    }    
}

// }}}
// checkAndFormatHandingByRange() {{{

/**
 * Vérifie les lignes HandingByRange:
 *  - vérifie que le type de donnée de percent/minimum/maximum est bien float,
 *  - formate les données saisies,
 *  - vérifie que les créneaux ne se chevauchent pas.
 *
 * @return boolean
 */
function checkAndFormatHandingByRange(evt) {
    var linesLength = $('CategoryAddEdit').elements["HandingByRange_Currency[]"].length;
	if (!linesLength) {
		return true;
	}
    var regex = /\d+[\.,]?\d*/;
    var hdr = new Array();
    for(var i = 0; i < linesLength; i++) {
        var min = $('CategoryAddEdit').elements["HandingByRange_Minimum[]"][i].value;
        var max = $('CategoryAddEdit').elements["HandingByRange_Maximum[]"][i].value;
        var percent = $('CategoryAddEdit').elements["HandingByRange_Percent[]"][i].value;
        var currency = $('CategoryAddEdit').elements["HandingByRange_Currency[]"][i].value;
        if (!regex.test(min) || !regex.test(max) || !regex.test(percent)) {
            alert(CategoryAddEdit_1);
            return evt.stop();
        }
        // formate les widgets
        min = fw.i18n.extractNumber(min);
        $('CategoryAddEdit').elements["HandingByRange_Minimum[]"][i].value = min;
        max = fw.i18n.extractNumber(max);
        $('CategoryAddEdit').elements["HandingByRange_Maximum[]"][i].value = max;
        percent = fw.i18n.extractNumber(percent);
        $('CategoryAddEdit').elements["HandingByRange_Percent[]"][i].value = percent;
        // vérifie que les ranges s'overlappent pas
        for(var j = 0; j < hdr.length; j++) {
            var current_min = hdr[j][0];
            var current_max = hdr[j][1];
            var current_currency = hdr[j][2];
            if (currency == current_currency && 
               ((min >= current_min && min <= current_max)
             || (max >= current_min && max <= current_max)
             || (min < current_min && max > current_max))) {
                alert(CategoryAddEdit_2);
                return evt.stop();
            }
        }
        hdr[i] = new Array();
        hdr[i][0] = min;
        hdr[i][1] = max;
        hdr[i][2] = currency;
    }
    return true;
}

// }}}
