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
    if ($('ActorProduct_ID')) {
        var filter = {'ActorProduct': $('ActorProduct_ID').value};
    } else {
        var filter = {'Product': objID};
    }
    var d = fw.ajax.call(
        'loadCollection',
        'PriceByCurrency',
        filter,
        {},
        ['RecommendedPrice', 'Price', 'Product', 'ActorProduct', 'Currency', 'PricingZone']
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
            onAddPBC(false, data[i]);
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
onAddPBC = function(eventObj, itemData) {
    var item = newPBCItem(itemData);
    appendChildNodes($('PBCUL'), item);
    hideElement(item);
    appear(item);
}

/**
 * Suppression d'un RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
onDelPBC = function(nodeID) {
    //fade($('PBCLI'+nodeID), {duration: 0.3});
    removeElement($('PBCLI'+nodeID));
}

/**
 * Crée un élément de la liste des RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
newPBCItem = function(itemData) {
    var index = $('PBCUL').childNodes.length;
    if (typeof(itemData) != 'undefined') {
        var rprice = itemData.recommendedPrice;
        var price  = itemData.price;
        var cur    = itemData.currency.id;
        var zone   = itemData.pricingZone.id;
    } else {
        var rprice = '';
        var price  = '';
        var cur    = 0;
        var zone   = 0;
    }
    return LI(
        {id: 'PBCLI'+index, style: 'padding: 3px; border: 1px #fff outset;'},
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'PBC_PricingZone' + index}, ProductPriceAddEdit_0 + ': '),
            newSelect(
                'PBC_PricingZone_ID[]',
                'loadCollection',
                'PricingZone',
                false,
                zone,
                true
            )
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'PBC_Currency' + index}, ProductPriceAddEdit_1 + ': '),
            newSelect(
                'PBC_Currency_ID[]',
                'loadCollection',
                'Currency',
                false,
                cur
            )
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'PBC_RecommendedPrice' + index}, ProductPriceAddEdit_2 + ': '),
            INPUT({
                value: rprice,
                type : 'text',
                name : 'PBC_RecommendedPrice[]',
                id   : 'PBC_RecommendedPrice' + index
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'PBC_Price' + index}, ProductPriceAddEdit_3 + ': '),
            INPUT({
                value: price,
                type : 'text',
                name : 'PBC_Price[]',
                id   : 'PBC_Price' + index
            })
        ),
        SPAN(
            null,
            INPUT({
                'type': 'button',
                'class': 'button',
                'value': A_Delete,
                'onclick':'onDelPBC('+index+')'
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
    if(arg2) {
        var d = fw.ajax.call(method, arg1, arg2);
    } else {
        var d = fw.ajax.call(method, arg1);
    }
    var index = $('PBCUL').childNodes.length;
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

connect(window, 'onload', function() {
    initialize();
    connect('addPBC', 'onclick', onAddPBC);
    if ($('ActorProduct_ID')) {
        oldvalue = 0;
        connect('ActorProduct_ID', 'onclick', function(e) {
            oldvalue = e.src().selectedIndex;
        });
        connect('ActorProduct_ID', 'onchange', function(e) {
            if (confirm(ProductPriceAddEdit_5)) {
                replaceChildNodes($('PBCUL'));
                initialize();
            } else {
                e.src().selectedIndex = oldvalue;
                stop(e);
            }
        });
    }
});
