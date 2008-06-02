/**
 * $Source: /home/cvs/onlogistics/js/includes/PrestationCostAddEdit.js,v $
 * 
 * @todo rendre le tout dynamique
 * @version   CVS: $Id: PrestationCostAddEdit.js,v 1.6 2008-05-07 16:36:39 ben Exp $
 * @author    Guillaume <guillaume@ateor.com>
 * @copyright 2002-2007 ATEOR - All rights reserved
 */
var mainEntity = '';
var entities = new Array();
var entitiesFields = new Object();

// todo dynamic!
mainEntity = 'PrestationCost';
entities.push('CostRange');
entitiesFields['CostRange'] = ['Cost', 'CostType', 'BeginRange', 'EndRange',
    'DepartureZone', 'ArrivalZone', 'Store', 'ProductType', 'UnitType'];

var server_data = new Object();
var server_collections = ['CostRange', 'CostType', 'Zone', 'Store','ProductType', 'SellUnitType'];

/**
 * Initialise les collections.
 *
 * @access public
 * @return void
 */
initialize = function() {
    var objID = $('objID').value;
    var filter = new Object();
    filter[mainEntity] = objID;
    /* charge les collections en mémoire */
    var d = new Array();
    for(var i=0 ; i< entities.length ; i++) {
        d[i] = fw.ajax.call(
            'loadCollection',
            entities[i],
            filter, 
            {'Id': 'SORT_ASC'},
            entitiesFields[entities[i]]);
    }
    d[1] = fw.ajax.call('callStaticMethod', 'CostRange','getCostTypeConstArray');
    d[2] = fw.ajax.call('loadCollection', 'Zone');
    d[3] = fw.ajax.call('loadCollection', 'Store', {'Activated':1},
        {'Name':'SORT_ASC'});
    d[4] = fw.ajax.call('loadCollection', 'ProductType');
    d[5] = fw.ajax.call('loadCollection', 'SellUnitType');
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
        for(var i=0; i<entities.length; i++) {
            var data = server_data[entities[i]];
            for (var j=0; j<data.length; j++) {
                onAddItem(entities[i], false, data[j]);
            }
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
onAddItem = function(entity, eventObj, itemData) {
    var item = newItem(entity, itemData);
    var table = $(entity+'TABLE');
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
onDelItem = function(entity, nodeID) {
    removeElement($(entity+'TR'+nodeID));
}

/**
 * Ajoute un item à la collection
 *
 * @param entity ClassName de la collection
 * @param itemData item à ajouter
 * @access public
 * @return node
 */
newItem = function(entity, itemData) {
    var table = $(entity+'TABLE');
    var tbody = table.getElementsByTagName('tbody')[0];
    var index = tbody.childNodes.length;
    if (typeof(itemData) != 'undefined') {
        var itemId = itemData.id;
        
        // todo dynamic!
        if(entity=='CostRange') {
            var cost = itemData.cost;
            var costType = itemData.costType.value;
            var beginRange = itemData.beginRange;
            var endRange = itemData.endRange;
            var departureZone = itemData.departureZone.id;
            var arrivalZone = itemData.arrivalZone.id;
            var store = itemData.store.id;
            var productType = itemData.productType.id;
            var unitType = itemData.unitType.id
        }
        if(entity=='PrestationCustomer') {
            var actor=itemData.actor.id;
            var name=itemData.name;
        }
    } else {
        var itemId = 0;
        // todo dynamic!
        var cost = 0;
        var costType = 0;
        var beginRange = 0;
        var endRange = 0;
        var departureZone = 0;
        var arrivalZone = 0;
        var store = 0;
        var productType = 0;
        var unitType = 0;
        var actor=0;
        var name='';
    }
    var row = TR({id: entity+'TR'+index, style: 'display: table-row;'});

    // todo dynamic!
    if(entity=='CostRange') {
        appendChildNodes(row, TD(
                null,
                INPUT({
                    value: cost,
                    type : 'text',
                    name : 'CostRange_Cost[]',
                    id   : 'CostRange_Cost' + index,
                    size : 5
                })
            ),
            TD(
                null,
                newSelect('CostType', index, 'CostRange_CostType[]', costType)
            ),
            TD(
                null,
                INPUT({
                    value: beginRange,
                    type : 'text',
                    name : 'CostRange_BeginRange[]',
                    id   : 'CostRange_BeginRange' + index,
                    size : 5
                })
            ),
            TD(
                null,
                INPUT({
                    value: endRange,
                    type : 'text',
                    name : 'CostRange_EndRange[]',
                    id   : 'CostRange_EndRange' + index,
                    size : 5
                })
            ),
            TD(
                null,
                newSelect('Zone', index, 'CostRange_DepartureZone[]', departureZone, true)
            ),
            TD(
                null,
                newSelect('Zone', index, 'CostRange_ArrivalZone[]', arrivalZone, true)
            ),
            TD(
                null,
                newSelect('Store', index, 'CostRange_Store[]', store, true)
            ),
            TD(
                null,
                newSelect('ProductType', index, 'CostRange_ProductType[]',
                    productType, true)
            ),
            TD(
                null,
                newSelect('SellUnitType', index, 'CostRange_UnitType[]',
                    unitType, true)
            )
        );
    }
    if(entity=='PrestationCustomer') {
        appendChildNodes(row, TD(
                null,
                newSelect('Actor', index, 'PrestationCustomer_Actor[]', actor)
            ),
            TD(
                null,
                INPUT({
                    value: name,
                    type : 'text',
                    name : 'PrestationCustomer_Name[]',
                    id   : 'PrestationCustomer_Name' + index
                })
            )
        );

    }
    
    // add del button and item id
    appendChildNodes(row, TD(
        null,
        INPUT({
            type: 'button',
            class: 'button',
            value: 'Supprimer',
            onclick:'onDelItem("'+entity+'", '+index+')'
        }),
        INPUT({
            value: itemId,
            type : 'hidden',
            name : entity+'_ID[]',
            id   : entity+'_ID' + index,
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
 * Connections
 *
 */
connect(window, 'onload', function() {
    initialize();
    
    // todo dynamic!
    connect('addCostRange', 'onclick', function(eventObj) {
        onAddItem('CostRange', false);});
    connect('addPrestationCustomer', 'onclick', function() {
        onAddItem('PrestationCustomer', false);});
});
