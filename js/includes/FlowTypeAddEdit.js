/**
 * $Source: /home/cvs/onlogistics/js/includes/FlowTypeAddEdit.js,v $
 *
 * Ajoute deux grids éditables, pour Prestation.CostRange et
 * Prestation.PrestationCustomer.
 *
 * @todo rendre le tout dynamique
 * @version   CVS: $Id: FlowTypeAddEdit.js,v 1.3 2008-05-07 16:36:38 ben Exp $
 * @author    Guillaume <guillaume@ateor.com>
 * @copyright 2002-2007 ATEOR - All rights reserved
 */

var mainEntity = '';
var entities = new Array();
var entitiesFields = new Object();

// todo dynamic!
mainEntity = 'FlowType';
entities.push('FlowTypeItem');
entitiesFields['FlowTypeItem'] = ['Name', 'TVA']

var server_data = new Object();
var server_collections = ['FlowTypeItem', 'TVA'];

/**
 * Initialise les collections.
 *
 * @access public
 * @return void
 */
initialize = function() {
    var objID = $('objID').value;
    /* charge les collections en mémoire */
    var d = new Array();
    d[0] = fw.ajax.call(
        'loadCollection',
        'FlowTypeItem',
        {'FlowType':objID}, 
        {'Name': 'SORT_ASC'},
        ['Name','TVA', 'FlowType']);
    d[1] = fw.ajax.call('loadCollection', 'TVA');
    //, {},
    //    {'Category':'SORT_ASC'}, ['Category', 'Rate']);
    l = new MochiKit.Async.DeferredList(d);
    var onSuccess = function(datalist) {
        for (var i=0; i<datalist.length; i++) {
            if (datalist[i][0]) {
                var t = server_collections[i];
                server_data[t] = datalist[i][1];
            }
        }
        onServerDataInitialized();
    };
    var onError = function(err) {
        alert("Erreur: " + err);
    };
    l.addCallbacks(onSuccess, onError);
}

var onServerDataInitialized = function() {
    var objID = $('objID').value;
    // process
    if(objID > 0) {
        var data = server_data['FlowTypeItem'];
        for (var j=0; j<data.length; j++) {
            onAddItem(false, data[j]);
        }
    }
}

/**
 * Ajoute un item à une collection
 *
 * @param entity ClassName de la collection
 * @param eventObj caller
 * @param itemData item à ajouter
 * @access public
 * @return void
 */
onAddItem = function(eventObj, itemData) {
    var item = newItem(itemData);
    var table = $('FlowTypeItem_TABLE');
    var tbody = table.getElementsByTagName('tbody')[0];
    setStyle(item, {'opacity':0.0}); 
    appendChildNodes(tbody, item);
    Opacity(item, {from: 0.0, to: 1.0});
}

/**
 * Supprime un item d'une collection
 *
 * @param entity ClassName de la collection
 * @param nodeID id de la ligne à supprimer
 * @access public
 * @return void
 */
onDelItem = function(nodeID) {
    removeElement($('FlowTypeItem_TR'+nodeID));
}

/**
 * Ajoute un item à la collection
 *
 * @param entity ClassName de la collection
 * @param itemData item à ajouter
 * @access public
 * @return node
 */
newItem = function(itemData) {
    var table = $('FlowTypeItem_TABLE');
    var tbody = table.getElementsByTagName('tbody')[0];
    var index = tbody.childNodes.length;
    if (typeof(itemData) != 'undefined') {
        var itemId = itemData.id;
        var name = itemData.name;
        var tva = itemData.tVA.id;
    } else {
        var itemId = 0;
        var name = '';
        var tva = 0;
    }
    var row = TR({id: 'FlowTypeItem_TR'+index, style: 'display: table-row;'});

    // todo dynamic!
    appendChildNodes(row, TD(
            null,
            INPUT({
                value: name,
                type : 'text',
                name : 'FlowTypeItem_Name[]',
                id   : 'FlowTypeItem_Name' + index
            })
        ),
        TD(
            null,
            newSelect(
                'TVA', index, 'FlowTypeItem_TVA[]', tva, true)
        )
    );
    
    // add del button and item id
    appendChildNodes(row, TD(
        null,
        INPUT({
            type: 'button',
            class: 'button',
            value: 'Supprimer',
            onclick:'onDelItem('+index+')'
        }),
        INPUT({
            value: itemId,
            type : 'hidden',
            name : 'FlowTypeItem_ID[]',
            id   : 'FlowTypeItem_ID' + index,
            size : 5
        })
    ));
    return row;
}

/**
 * Crée un select avec les données serveur
 *
 * @access public
 * @return void
 */
newSelect = function(colName, index, widgetName, selected, allowEmpty) {
    var nodes = new Array();
    var k=0;
    var select = createDOM(
        'select', 
        {'name': widgetName, 'id': widgetName.substring(0, widgetName.length-2)+index, 'style': 'width:100%;'}
    );
    if(allowEmpty) {
        nodes[k] = createDOM('option', {'value': 0}, 'Sélectionner');
        k++;
    }
    data = server_data[colName];
    for(var i=0 ; i<data.length ; i++) {
        if (selected == data[i].id) {
            var attrs = {'value': data[i].id, 'selected': 'selected'};
        } else {
            var attrs = {'value': data[i].id};
        }
        nodes[k] = createDOM('option', attrs, data[i].toString);
        k++;
    }
    // jusqu'ici tout va bien
    appendChildNodes(select, nodes);
    return select;
}

/**
 * onSubmit 
 * 
 * @param evt $evt 
 * @access public
 * @return void
 */
onSubmit = function(evt) {
    if($('FlowType_InvoiceType').value == 0 && $('FlowTypeAddEdit').elements['FlowTypeItem_ID[]']==null) {
        alert(FlowTypeAddEdit_0);
        return evt.stop();
    }
    return true;
}

/*
 *
 * Connections
 *
 */
connect(window, 'onload', function() {
    initialize();
    // todo dynamic!
    connect('addFlowTypeItem', 'onclick', function(eventObj) {
        onAddItem(false);});
    connect('FlowTypeAddEdit', 'onsubmit', onSubmit);
});

