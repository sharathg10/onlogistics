/**
 * $Source: /home/cvs/onlogistics/js/includes/SpreadSheetAddEdit.js,v $
 * 
 * @version $Id: SpreadSheetAddEdit.js,v 1.9 2008-05-07 16:36:39 ben Exp $
 * @copyright © 2005 - ATEOR - All rights reserved.
 */

// cache pour les données serveur
var server_data = null;

// initialize {{{

/**
 * Initialise le tableau des ressources avec les données du serveur.
 *
 * @access public
 * @return void
 */
initialize = function(reset) {
    var onSuccess = function (data) {
        if (data.isError) {
            return onError(data.errorMessage+' (code: '+data.errorCode+')');
        }
        if (!data.length || data.length == 0) {
            return;
        }
        // met en cache les données serveur pour les selects sur propriétés
        server_data = data;
        if (reset) return;
        var objID = $('objID').value;
        var addItemDeferred = fw.ajax.call(
            'loadCollection',
            'SpreadSheetColumn',
            {'SpreadSheet': objID?objID:null}, 
            {'Order': 'SORT_ASC'},
            ['Name', 'PropertyName', 'FkeyPropertyName', 'PropertyType',
             'PropertyClass', 'Order', 'Comment', 'Default', 'Width', 'Required']
        );
        var onAddItemSuccess = function (addItemData) {
            if (!addItemData.length || addItemData.length == 0) {
                return;
            }
            if (addItemData.isError) {
                return onAddItemError(addItemData.errorMessage);
            }
            for(var i=0; i<addItemData.length; i++) {
                onAddSSC(false, addItemData[i]);
            }
        }
        var onAddItemError = function (err) {
            alert('Erreur: ' + err);
        }
        addItemDeferred.addCallbacks(onAddItemSuccess, onAddItemError);
    }
    var onError = function (err) {
        alert('Erreur: ' + err);
    }
    var entityname = $('SpreadSheet_Entity_ID').value;
    var d = fw.ajax.call('spreadsheetaddedit_getPropertyTypeList', entityname);
    d.addCallbacks(onSuccess, onError);
}

// }}}
// reinitialize {{{

reinitialize = function() {
    replaceChildNodes($('sscUL'), []);
    initialize(true);
}

// }}}
// onAddSSC {{{

/**
 * Ajout d'un RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
onAddSSC = function(eventObj, itemData) {
    var item = newSSCItem(itemData);
    appendChildNodes($('sscUL'), item);
    hideElement(item);
    appear(item);
    var index = $('sscUL').childNodes.length - 1;
    if (itemData) {
        updateFkeyPropertyNameSelect(
            index,
            itemData.propertyClass,
            itemData.fkeyPropertyName
        );
    } else {
        updateFkeyPropertyNameSelect(
            index, 
            $('SSC_PropertyClass_'+index).value,
            $('SSC_FkeyPropertyName_' + index).value
        );
    }
}

// }}}
// onDelSSC {{{

/**
 * Suppression d'un RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
onDelSSC = function(nodeID) {
    removeElement($('sscLI'+nodeID));
}

// }}}
// newSSCItem {{{

/**
 * Crée un élément de la liste des RessourceRessourceGroup
 *
 * @access public
 * @return void
 */
newSSCItem = function(itemData) {
    var index = $('sscUL').childNodes.length;
    var isNew = typeof(itemData) == 'undefined';
    var name    = isNew?'':itemData.name;
    var pname   = isNew?'':itemData.propertyName;
    var pclass  = isNew?'':itemData.propertyClass;
    var ptype   = isNew?'':itemData.propertyType;
    var comment = isNew?'':itemData.comment;
    var order   = isNew?'':itemData.order;
    var fkey    = isNew?'':itemData.fkeyPropertyName;
    var deflt   = isNew?'':itemData['default'];
    var width   = isNew?'':itemData.width;
    var req     = isNew?0:itemData.required;
    var checkbox = INPUT({
        value: 1,
        type : 'checkbox',
        name : 'SSC_Required['+index+']',
        id   : 'SSC_Required_' + index
    });
    if (req) {
        checkbox.checked = 'checked';
    }
    return LI(
        {
            id: 'sscLI'+index,
            style: 'padding: 3px; border: 1px #fff outset;white-space:nowrap;'
        },
        SPAN(
            {style:'padding: 5px;'},
            INPUT({type: 'hidden', id: 'SSC_PropertyClass_' + index, value: pclass}),
            newPropertyTypeSelect(ptype, pname)
        ),
        SPAN(
            {style:'padding: 5px;'},
            SELECT({
                name : 'SSC_FkeyPropertyName[]',
                id   : 'SSC_FkeyPropertyName_' + index,
                style: 'width: 120px;'
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'SSC_Name_' + index}, SpreadSheetAddEdit_6 + ': '),
            INPUT({
                value: name,
                type : 'text',
                name : 'SSC_Name[]',
                id   : 'SSC_Name_' + index,
                size : '10'
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'SSC_Comment_' + index}, SpreadSheetAddEdit_7 + ': '),
            INPUT({
                value: comment,
                type : 'text',
                name : 'SSC_Comment[]',
                id   : 'SSC_Comment_' + index,
                size : '10'
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'SSC_Default_' + index}, SpreadSheetAddEdit_8 + ': '),
            INPUT({
                value: deflt,
                type : 'text',
                name : 'SSC_Default[]',
                id   : 'SSC_Default_' + index,
                size : '4'
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'SSC_Width_' + index}, SpreadSheetAddEdit_9 + ': '),
            INPUT({
                value: width,
                type : 'text',
                name : 'SSC_Width[]',
                id   : 'SSC_Width_' + index,
                size : '1'
            })
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'SSC_Required_' + index}, SpreadSheetAddEdit_10 + ': '),
            checkbox
        ),
        SPAN(
            {style:'padding: 5px;'},
            LABEL({'for': 'SSC_Order_' + index}, SpreadSheetAddEdit_11 + ': '),
            INPUT({
                value: order,
                type : 'text',
                name : 'SSC_Order[]',
                id   : 'SSC_Order_' + index,
                size : '1'
            })
        ),
        SPAN(
            null,
            INPUT({
                'type': 'button',
                'class': 'button',
                'value': '-',
                'onclick':'onDelSSC('+index+')'
            })
        )
    );
}

// }}}
// newPropertyTypeSelect {{{

/**
 * Crée un select avec les données serveur
 *
 * @access public
 * @return void
 */
newPropertyTypeSelect  = function(ptype, pname, fkeyname) {
    var index = $('sscUL').childNodes.length;
    var select = createDOM(
        'select', 
        {
            style: 'width: 120px;',
            name: 'SSC_PropertyType[]',
            id: 'SSC_PropertyType_' + index,
            onchange: 'updateFkeyPropertyNameSelect('+index+')'
        }
    );
    var nodes = new Array();
    for(var i=0; i<server_data.length; i++) {
        var attrs = {
            value: server_data[i].name + ':' + server_data[i].type + ':' + server_data[i].class
        }
        if (ptype == server_data[i].type && pname == server_data[i].name) {
            attrs.selected = 'selected';
        }
        attrs.onchange = 'updateFkeyPropertyNameSelect(' + index + ', \'' 
            + server_data[i].class + '\', \'' + fkeyname + '\');';
        nodes[i] = createDOM('option', attrs, server_data[i].name);
    }
    appendChildNodes(select, nodes);
    return select;
}

// }}}
// updateFkeyPropertyNameSelect {{{

/**
 * Crée un select avec les données serveur
 *
 * @access public
 * @return void
 */
updateFkeyPropertyNameSelect = function(index, cls, fk) {
    if (arguments.length == 1) {
        var tokens = $('SSC_PropertyType_' + index).value.split(':');
        cls = tokens[2];
    }
    var widgetId = 'SSC_FkeyPropertyName_' + index;
    if(!cls) {
        var opt = createDOM('option', {'value': 0}, 'N/A');
        replaceChildNodes($(widgetId), opt);
        return;
    }
    var d = fw.ajax.call('spreadsheetaddedit_getPropertyTypeList', cls, false);
    var onSuccess = function (data) {
        // contruire les options
        if (data.isError) {
            return onError(data.errorMessage+' (code: '+data.errorCode+')');
        }
        if (!data.length || data.length == 0) {
            return;
        }
        var nodes = new Array(
            createDOM('option', {value: 'toString'}, 'toString')
        );
        for(var i=0; i<data.length; i++) {
            var attrs = {value: data[i].name}
            if (fk == data[i].name) {
                attrs.selected = 'selected';
            }
            nodes[i+1] = createDOM('option', attrs, data[i].name);
        }
        replaceChildNodes($(widgetId), nodes);
    }
    var onError = function (err) {
        alert('Erreur: ' + err);
    }
    d.addCallbacks(onSuccess, onError); 
}
// }}}

// lance le tout sur onload
connect(window, 'onload', function() {
    initialize();
    connect('SpreadSheet_Entity_ID', 'onchange', reinitialize);
    connect('addSSC', 'onclick', onAddSSC);
});
