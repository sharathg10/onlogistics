/**
 *
 * @source
 * @version $Id$
 */

var server_data = new Object();
var server_data_lpq = new Object();

// connect() {{{

/**
 * connecte les évènements
 */
connect(window, 'onload', function() {
    initialize();
    
    connect('addPDT', 'onclick', function(eventObj) {
        onAddItem('Product', false);});
    connect('addCoverType', 'onclick', function(eventObj) {
        onAddItem('CoverType', false);});
});

// }}}
// initialize() {{{

/**
 * initialise le formulaire
 */
initialize = function() {
    var d = fw.ajax.updateSelect('cmdDestinator', 'destinatorSite', 'Site', 'Owner', true);
    var myCallback = function () {
        fw.dom.selectOptionByValue('destinatorSite', $('unusedSiteId').value);
    }
    d.addCallback(myCallback);
    initializeServerData();
    loadUsableLPQ();
};

// }}}
// checkQuantity() {{{

/**
 * vérifie que qty est < à min et > à max
 */
checkQuantity = function(qty, min, max) {
    if(!min) {
        min = 0;
    } else {
        min = fw.i18n.extractNumber(min);
    }
    qty = fw.i18n.extractNumber(qty);
    if(qty < min) {
        //alert(ForwardingForm_1);
        //'erreur, la quantitité doit être sup à '+min);
        return 1;
    }
    if(max) {
        max = fw.i18n.extractNumber(max);
        if(qty > max) {
            //alert('erreur, la quantitité doit être inf à '+max);
            return 2;
        }
    }
    return 0;
}

// }}}
// validateForm() {{{

/**
 * valide les données saisie
 */
validateForm = function() {
    // un site destinataire doit être saisie
    if($('destinatorSite').value == 0) {
        alert(ForwardingForm_0);
        return false;
    }
    if($('ConveyorDepartureSite').value == $('ConveyorArrivalSite').value &&
        $('ConveyorDepartureSite').value != '##') {
        alert(ForwardingForm_3);
        return false;
    }
    // les CoverTypeQty doivent être > 0
    with(document.forms[0]) {
        if(elements['CoverTypeQty[]']) {
	        if(!elements['CoverTypeQty[]'].length) {
                var error = checkQuantity(elements['CoverTypeQty[]']);
                if(error > 0) {
                    alert(ForwardingForm_1);
                    return false;
                }
            } else {
			    for(var i=0; i<elements['CoverTypeQty[]'].length; i++) {
                    var error = checkQuantity(elements['CoverTypeQty[]'][i]);
                    if(error > 0) {
                        alert(ForwardingForm_1);
                        return false;
                    }
                }
            }
        }
    }
    // les ProductQty doivent être > 0 et < au ProductLPQQty
    with(document.forms[0]) {
        if(elements['Product_ID[]']) {
            var msg = '';
            var lines = 1;
	        if(elements['Product_ID[]'].length) {
                lines = elements['Product_ID[]'].length;
            }
			for(var i=0; i<lines; i++) {
                if(elements['ProductQty['+i+'][]']) {
                    if(!elements['ProductQty['+i+'][]'].length) {
                        var error = checkQuantity(elements['ProductQty['+i+'][]'], 0,
                            elements['ProductLPQQty['+i+'][]']);
                        if(error == 1) {
                            alert(ForwardingForm_1);
                            return false;
                        } else if(error == 2) {
                            msg = msg + "\n" +
                                server_data['Product'][$('Product'+i).value] + 
                                " - " +
                                server_data['Location'][elements['ProductLocation['+i+'][]'].value];
                        }
                    } else {
                        for(var j=0; j<elements['ProductQty['+i+'][]'].length ; j++) {
                            var error = checkQuantity(elements['ProductQty['+i+'][]'][j], 0,
                                elements['ProductLPQQty['+i+'][]'][j]);
                            if(error == 1) {
                                alert(ForwardingForm_1);
                                return false;
                            } else if(error == 2) {
                                msg = msg + "\n" +
                                    server_data['Product'][$('Product'+i).value] + 
                                    " - " +
                                    server_data['Location'][elements['ProductLocation['+i+'][]'][j].value];
                            }
                        }
                    }
                }
            }
            if(msg != '') {
                alert(ForwardingForm_2 + msg);
                return false;
            }
        }
    }

    // ok, on poste le form
    $('FormSubmitted').value = true;
    document.forms[0].submit();
	return true;
};

// }}}
// onDelItem() {{{

onDelItem = function(entity, nodeID) {
    removeElement($(entity+'TR'+nodeID));
}

// }}}
// onAddItem() {{{

/**
 * Ajoute un item à une collection
 */
onAddItem = function(entity, eventObj, itemData) {
    var item = newItem(entity, itemData);
    var table = $(entity+'TABLE');
    var tbody = table.getElementsByTagName('tbody')[0];
    setStyle(item, {'opacity':0.0}); 
    appendChildNodes(tbody, item);
    Opacity(item, {from: 0.0, to: 1.0});
}

// }}}
// newItem() {{{

/**
 * Ajoute un item à la collection
 */
newItem = function(entity, itemData) {
    var table = $(entity+'TABLE');
    var tbody = table.getElementsByTagName('tbody')[0];
    var index = tbody.childNodes.length;
    if (typeof(itemData) != 'undefined') {
        var itemId = itemData.id;
    } else {
        var itemId = 0;
    }
    if(entity == 'Product') {
        var row = TR({id: entity+'TR'+index, style: 'display: table-row;'});
        appendChildNodes(row, 
            TD(null, newSelect('Product', index, 'Product[]', false, true)),
            TD({id: 'locationTD'+index, colspan:3})
        );
    } else if(entity == 'CoverType') {
        var row = TR({id: entity+'TR'+index, style: 'display: table-row;'});
        appendChildNodes(row, 
            TD(null, newSelect('CoverType', index, 'CoverType[]')),
            TD(null, INPUT({type:'text', name:'CoverTypeQty[]'}))
        );
    }
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

// }}}
// newSelect() {{{

/**
 * Crée un select
 */
newSelect = function(colName, index, widgetName, selected, allowEmpty) {
    var nodes = new Array();
    var k=0;
    var onChangeContent = '';
    if(colName == 'Product') {
        onChangeContent = 'updateLocation('+index+');';
    }
    var select = createDOM(
        'select', 
        {'name': widgetName, 
        'id': widgetName.substring(0, widgetName.length-2)+index, 
        'style': 'width:90%;', 
        'onchange':onChangeContent}
    );
    if(allowEmpty) {
        nodes[k] = createDOM('option', {'value': 0}, 'Sélectionner');
        k++;
    }
    if(colName == 'Product') {
        data = server_data['AvailableProducts'];
    } else {
        data = server_data[colName];
    }
    for(var i=0 ; i<data.length ; i++) {
        if(colName == 'Product') {
            if (selected == data[i]) {
                var attrs = {'value': data[i], 'selected': 'selected'};
            } else {
                var attrs = {'value': data[i]};
            }
            nodes[k] = createDOM('option', attrs, server_data['Product'][data[i]]);
        } else {
            if (selected == data[i].id) {
                var attrs = {'value': data[i].id, 'selected': 'selected'};
            } else {
                var attrs = {'value': data[i].id};
            }
            nodes[k] = createDOM('option', attrs, data[i].toString);
        }
        k++;
    }
    appendChildNodes(select, nodes);
    return select;
}

// }}}
// loadUsableLPQ() {{{

/**
 * récupère les lpq utilisable en fonctio nde l'acteur de l'utilisateur connecté
 * ou du site de départ du transporteur choisi
 */
loadUsableLPQ = function() {
    var filter = new Object();
    filter['Product.Activated'] = 1;
    filter['Product.ProductType'] = 2000;
    filter['Location.Activated'] = 1;
    if ($('ConveyorDepartureSite').value == '##') {
        filter['Location.Store.StorageSite.Owner'] = $('SiteOwner').value;
    } else {
        filter['Location.Store.StorageSite'] = $('ConveyorDepartureSite').value;
    }
    var d = fw.ajax.call(
        'loadCollection',
        'LocationProductQuantities',
        filter, 
        {'Id':'SORT_ASC'},
        ['Product', 'Location', 'RealQuantity']
    );
    var onSuccess = function(data) {
        server_data['AvailableProducts'] = new Array();
        for(var i=0; i<data.length; i++) {
            var lpq = new Object();
            lpq['location'] = data[i].location.id;
            lpq['quantity'] = data[i].realQuantity;
            if(!server_data_lpq[data[i].product.id]) {
                server_data_lpq[data[i].product.id] = new Array();
            }
            server_data_lpq[data[i].product.id].push(lpq);
            if(findValue(server_data['AvailableProducts'], data[i].product.id) == -1) {
                server_data['AvailableProducts'].push(data[i].product.id);
            }
        }
    };
    var onError = function(err) {
        alert("Erreur: " + err);
    };
    d.addCallbacks(onSuccess, onError);
}

// }}}
// initializeServerData() {{{

/**
 * récupère les pdt et emplacement et els mets en cache
 */
initializeServerData = function() {
    var filter = new Object();
    filter['Activated'] = 1;
    filter['ProductType'] = 2000;
    var d=new Array();
    d[0] = fw.ajax.call(
        'loadCollection',
        'Product',
        {'Activated':1, 'ProductType':2000}, 
        {'BaseReference':'SORT_ASC'},
        ['BaseReference', 'Name']
    );
    d[1] = fw.ajax.call(
        'forwardingForm_getLocations'
    );
    d[2] = fw.ajax.call(
        'loadCollection',
        'CoverType',
        {},
        {'Name':'SORT_ASC'}
    );
    l = new MochiKit.Async.DeferredList(d);
    var onSuccess = function(datalist) {
        if (datalist[0][0]) {
            var objectArray = new Object();
            for(var j=0 ; j<datalist[0][1].length ; j++) {
                var pdt = datalist[0][1][j];
                objectArray[pdt.id] = pdt.baseReference + ' - ' + pdt.name;
            }
            server_data['Product'] = objectArray;
        }
        if (datalist[1][0]) {
            var objectArray = new Object();
            for(var i=0; i<datalist[1][1].length; i++) {
                objectArray[datalist[1][1][i].id] = datalist[1][1][i].toString;
            }
            server_data['Location'] =objectArray;
        }
        if(datalist[2][0]) {
            server_data['CoverType'] = datalist[2][1];
        }
    };
    var onError = function(err) {
        alert("Erreur: " + err);
    };
    l.addCallbacks(onSuccess, onError);
}

// }}}
// updateLocation() {{{

/**
 * Affiche les emplacements dispo lors du select d'un pdt consigné
 */
updateLocation = function(index) {
    var pdtId = $('Product'+index).value;
    if(!server_data_lpq[pdtId]) {
        return;
    }
    var tbody = TBODY(null);
    for(var i=0 ; i<server_data_lpq[pdtId].length ; i++) {
        var lpq = server_data_lpq[pdtId][i];
        var row = TR(null, 
            TD({width:'30%'}, 
                INPUT({type:'hidden', value:lpq.location, name:'ProductLocation['+index+'][]'}), 
                server_data['Location'][lpq.location]
            ), 
            TD({width:'35%'}, 
                INPUT({type:'text', value:'0', name:'ProductQty['+index+'][]',
                    style:'width:70%;'})
            ), 
            TD({width:'35%'}, 
                INPUT({type:'hidden', value:lpq.quantity,
                    name:'ProductLPQQty['+index+'][]'}),
                lpq.quantity
            )
        );
        appendChildNodes(tbody, row);
    }
    var container = $('locationTD'+index);
    var table = TABLE({width: '100%'});
    appendChildNodes(table, tbody);
    replaceChildNodes(container, table);
}

// }}}
