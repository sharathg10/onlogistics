/**
 * $Source: /home/cvs/onlogistics/js/includes/RessourceGroupAddEdit.js,v $
 * 
 * @version $Id: RessourceGroupAddEdit.js,v 1.12 2008-05-07 16:36:39 ben Exp $
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
    var d = fw.ajax.call(
        'loadCollection',
        'RessourceRessourceGroup',
        {'RessourceGroup': objID}, 
        {'Ressource.Name': 'SORT_ASC'},
        ['Ressource', 'Rate']
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
            onAddRRG(false, data[i]);
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
onAddRRG = function(eventObj, itemData) {
    var item = newRRGItem(itemData);
    appendChildNodes($('rrgUL'), item);
    hideElement(item);
    appear(item);
}

/**
 * Suppression d'un RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
onDelRRG = function(nodeID) {
    //fade($('rrgLI'+nodeID), {duration: 0.3});
    removeElement($('rrgLI'+nodeID));
}

/**
 * Crée un élément de la liste des RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
newRRGItem = function(itemData) {
    var index = $('rrgUL').childNodes.length;
    if (typeof(itemData) != 'undefined') {
        var res = itemData.ressource.id;
        var rate = itemData.rate;
    } else {
        var res = 0;
        var rate = 0;
    }
    return LI(
        {id: 'rrgLI'+index, style: 'padding: 3px; border: 1px #fff outset;'},
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'RRG_Ressource' + index}, RessourceGroupAddEdit_0 + ': '),
            newSelect(
                'RRG_Ressource_ID[]',
                'loadCollection',
                'Ressource',
                false,
                res
            )
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'RRG_Rate' + index}, RessourceGroupAddEdit_1 + ': '),
            INPUT({
                value: rate,
                type : 'text',
                name : 'RRG_Rate[]',
                id   : 'RRG_Rate' + index
            })
        ),
        SPAN(
            null,
            INPUT({
                'type': 'button',
                'class': 'button',
                'value': A_Delete,
                'onclick':'onDelRRG('+index+')'
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
newSelect = function(name, method, arg1, arg2, selected) {
    if(arg2) {
        var d = fw.ajax.call(method, arg1, arg2);
    } else {
        var d = fw.ajax.call(method, arg1);
    }
    var index = $('rrgUL').childNodes.length;
    var select = createDOM(
        'select', 
        {'name': name, 'id': name+index}
    );
    var onSuccess = function (data) {
        // contruire les options
        if (!data.length || data.length == 0) {
            return;
        }
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
    connect('addRRG', 'onclick', onAddRRG);
});
