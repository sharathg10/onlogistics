/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 fdm=marker */

/**
 * Fonctions js nécessaire à la commande de transport (ChainCommand.php)
 * 
 * $Source: /home/cvs/onlogistics/js/includes/ChainCommand.js,v $
 * @version    CVS: $Id: ChainCommand.js,v 1.12 2008-05-07 16:36:38 ben Exp $
 * @copyright  2002-2006 ATEOR - All rights reserved
 */


var server_data = new Array();
var SD_COVERTYPE   = 0;
var SD_PRODUCTTYPE = 1;
var SD_MASTERDIM   = 2;
var RETURN_URL = 'TransportChainList.php';

// connection des signaux/callbacks {{{

connect(window, 'onload', function() {
    initialize();
    updateExpeditorSite();
    updateDestinatorSite();
    switchDateWidgetVisibility(0);
    connect('AddItemButton', 'onclick', onAddItem);
    connect('UpdatePriceButton', 'onclick', function() {
        validateCommand(false);
    });
    connect('ValidateButton', 'onclick', function() {
        validateCommand(true);
    });
    connect('CancelButton', 'onclick', function() {
        window.location.href=RETURN_URL;
    });
    if ($('ChainCommand_Installment'))
        connect('ChainCommand_Installment', 'onkeyup', recalculateToPay);
    if ($('ChainCommand_RawHT')) {
        connect('ChainCommand_RawHT', 'onchange', function() {
            $('PriceModified').value = 1;
        });
    }
});

// }}}
// initialize() {{{
/**
 * Initialise le cache server_data
 *
 * @access public
 * @return void
 */
initialize = function() {
    deferreds = new Array();
    deferreds[SD_COVERTYPE] = fw.ajax.call(
        'loadCollection', 'CoverType', {});
    deferreds[SD_PRODUCTTYPE] = fw.ajax.call(
        'loadCollection', 'ProductType', {});
    deferreds[SD_MASTERDIM] = fw.ajax.call(
        'callStaticMethod', 'CommandItem', 'getMasterDimensionConstArray');
    l = new MochiKit.Async.DeferredList(deferreds);
    var onSuccess = function(datalist) {
        server_data[SD_COVERTYPE]    = datalist[SD_COVERTYPE][1];
        server_data[SD_PRODUCTTYPE] = datalist[SD_PRODUCTTYPE][1];
        server_data[SD_MASTERDIM]   = datalist[SD_MASTERDIM][1];
        onServerDataInitialized();
    };
    var onError = function(err) {
        alert(E_Error_Title + ": " + err);
    };
    l.addCallbacks(onSuccess, onError);
}

// }}}
// onAddItem() {{{

/**
 * Ajoute un item au tableau des command items.
 *
 * @access public
 * @return void
 */
onAddItem = function() {
    var tbody = $('items_table_body');
    var index = tbody.childNodes.length;
    var item  = newItem(index);
    setStyle(item, {'opacity':0.0}); 
    appendChildNodes(tbody, item);
    Opacity(item, {from: 0.0, to: 1.0});
}

// }}}
// onDelItem() {{{

/**
 * Supprime un item du tableau des command items
 *
 * @param nodeID id de la ligne à supprimer
 * @access public
 * @return void
 */
onDelItem = function(nodeID) {
    removeElement($(nodeID));
}

// }}}
// newItem() {{{

/**
 * Crée une ligne (TR) du tableau des command items
 *
 * @param int index l'index de la ligne
 * @access public
 * @return node
 */
newItem = function(index) {
    return TR(
        {id: 'ChainCommandItem_TR_' + index},
        TD(
            null,
            newSelect(SD_COVERTYPE, index, 'ChainCommandItem_CoverType_ID[]', false)
        ),
        TD(
            null,
            newSelect(SD_PRODUCTTYPE, index, 'ChainCommandItem_ProductType_ID[]', false)
        ),
        TD(
            null,
            INPUT({
                value: 1,
                type : 'text',
                name : 'ChainCommandItem_Quantity[]',
                id   : 'ChainCommandItem_Quantity' + index,
                size : 3
            })
        ),
        TD(
            null,
            INPUT({
                value: 0,
                type : 'text',
                name : 'ChainCommandItem_Weight[]',
                id   : 'ChainCommandItem_Weight' + index,
                size : 3
            })
        ),
        TD(
            null,
            INPUT({
                value: 0,
                type : 'text',
                name : 'ChainCommandItem_Width[]',
                id   : 'ChainCommandItem_Width' + index,
                size : 3
            })
        ),
        TD(
            null,
            INPUT({
                value: 0,
                type : 'text',
                name : 'ChainCommandItem_Length[]',
                id   : 'ChainCommandItem_Length' + index,
                size : 3
            })
        ),
        TD(
            null,
            INPUT({
                value: 0,
                type : 'text',
                name : 'ChainCommandItem_Height[]',
                id   : 'ChainCommandItem_Height' + index,
                size : 3
            })
        ),
        TD(
            null,
            INPUT({
                value: 1,
                type : 'text',
                name : 'ChainCommandItem_Gerbability[]',
                id   : 'ChainCommandItem_Gerbability' + index,
                size : 3
            })
        ),
        TD(
            null,
            newSelect(SD_MASTERDIM, index, 'ChainCommandItem_MasterDimension[]', false)
        ),
        TD(
            null,
            INPUT({
                type: 'button',
                class: 'button',
                value: A_Delete,
                onclick:'onDelItem("ChainCommandItem_TR_' + index + '")'
            })
        )
    );
}

// }}}
// newSelect() {{{

/**
 * Crée un select à partir des données du cache
 *
 * @param const constname la constante corrspondante à l'index du cache
 * @param int index l'index de la ligne
 * @param string wname le nom (attribut name) du widget select
 * @access public
 * @return node
 */
newSelect = function(constname, index, wname) {
    var nodes = new Array();
    var k = 0;
    var data = server_data[constname];
    for (var i=0; i<data.length; i++) {
        nodes[i] = createDOM('option', {value: data[i].id}, data[i].toString);
    }
    var select = createDOM('select', {'name': wname});
    appendChildNodes(select, nodes);
    return select;
}

// }}}
// validateCommand() {{{

/**
 * 
 * @access public
 * @param  object widget l'élément de formulaire concerné
 * @return float la valeur formattée
 */
var validateCommand = function(commit) {
    if (!validateForm()) {
        return false;
    }
    if (typeof(commit) == "undefined") {
        commit = false;
    }
    freezeForm();
    var d = fw.ajax.call("chaincommand_calculateprice", getFormData(), commit);
    var onError = function(data) {
        alert(E_Error_Title + ": " + data);
    }
    var onSuccess = function(data) {
        if (data.isError) {
            unfreezeForm();
            onError(data.errorMessage);
        } else if (typeof(data) == typeof("")) {
            unfreezeForm();
            alert(data);
        } else {
            setFormData(data);
            unfreezeForm();
            if (commit) {
                document.forms['ChainCommand'].elements['FormSubmitted'].value = 1;
                document.forms["ChainCommand"].submit();
            }
        }
    }
    d.addCallbacks(onSuccess, onError);
}

// }}}
// updateExpeditorSite() {{{

/**
 * Update le select du site expediteur en fonction de l'acteur sélectionné.
 * 
 * @access public
 * @return void
 */
var updateExpeditorSite = function(){
    fw.ajax.updateSelect(
        'ChainCommand_Expeditor_ID',
        'ChainCommand_ExpeditorSite_ID',
        'Site',
        'Owner',
        true
    );
}

// }}}
// updateDestinatorSite() {{{

/**
 * Update le select du site destinataire en fonction de l'acteur sélectionné.
 * 
 * @access public
 * @return void
 */
var updateDestinatorSite = function(){
    fw.ajax.updateSelect(
        'ChainCommand_Destinator_ID',
        'ChainCommand_DestinatorSite_ID',
        'Site',
        'Owner',
        true
    );
}

// }}}
// switchDateWidgetVisibility() {{{

/**
 * Met à jour le display des dates en fonction du choix date ou créneau.
 * 
 * @access public
 * @return void
 */
var switchDateWidgetVisibility = function(choice) {
    if(choice == 0) {
        showElement('WishedStartDate');
        showElement('WishedStartHour');
        hideElement('WishedEndDate');
        hideElement('WishedEndHour');
    } else if(choice == 1) {
        showElement('WishedStartDate');
        showElement('WishedStartHour');
        showElement('WishedEndDate');
        showElement('WishedEndHour');
    }
}

// }}}
// validateForm() {{{

/**
 * Effectue les validations avant soumission du formulaire.
 * 
 * @access public
 * @return void
 */
var validateForm = function() {
    with(document.forms["ChainCommand"]) {
        if(!elements['ChainCommandItem_Quantity[]']) {
            alert(ChainCommand_0);
            return false;
        }
        if (elements["ChainCommand_WishedStartDate"].value == "" || elements["ChainCommand_WishedStartDate"].value == 0) {
            alert(ChainCommand_1);
            return false;
        }
        var handing = troncature(elements["ChainCommand_Handing"].value);
        elements["ChainCommand_Handing"].value = isNaN(handing)?0:handing;
        var packing = troncature(elements["ChainCommand_Packing"].value);
        elements["ChainCommand_Packing"].value = isNaN(packing)?0:packing;
        var insur = troncature(elements["ChainCommand_Insurance"].value);
        elements["ChainCommand_Insurance"].value = isNaN(insur)?0:insur;
        var deliv = troncature(elements["ChainCommand_DeliveryPayment"].value);
        elements["ChainCommand_DeliveryPayment"].value = isNaN(deliv)?0:deliv;
        var tva = troncature(elements["ChainCommand_TVA"].value);
        elements["ChainCommand_TVA"].value = isNaN(tva)?0:tva;
        var totalHT = troncature(elements["ChainCommand_TotalPriceHT"].value);
        elements["ChainCommand_TotalPriceHT"].value = isNaN(totalHT)?0:totalHT;
        var totalTTC = troncature(elements["ChainCommand_TotalPriceTTC"].value);
        elements["ChainCommand_TotalPriceTTC"].value = isNaN(totalTTC)?0:totalTTC;
        var toPay = troncature(elements["ChainCommand_ToPay"].value);
        elements["ChainCommand_ToPay"].value = isNaN(toPay)?0:toPay;
        var l = elements["ChainCommandItem_Quantity[]"].length;
        if (typeof(l) == 'undefined') {
            if (false == checkItemWidgets(-1)) return false;
        } else {
            for(var i=0; i<elements["ChainCommandItem_Quantity[]"].length; i++) {
                if (false == checkItemWidgets(i)) return false;
            }
        }
        if (elements["GrabPayment"][0].checked && elements["ChainCommand_DeliveryPayment"].value <= 0) {
            alert(ChainCommand_2);
            return false;
        }
    }    
    return true;
}

// }}}
// checkItemWidgets() {{{

/**
 * Vérifie les diverses propriétés de chaque item de la commande.
 * 
 * @access public
 * @param  int index l'index de l'item concerné
 * @return boolean true si le formulaire est valide ou false sinon
 */
var checkItemWidgets = function(index) {
    with(document.forms["ChainCommand"]) {
        var wquantity = index!=-1?
            elements["ChainCommandItem_Quantity[]"][index]:elements["ChainCommandItem_Quantity[]"];
        wquantity.value = troncature(wquantity.value);
        wquantity.value = isNaN(wquantity.value)?0:wquantity.value;
        if (wquantity.value <= 0) {
            alert(ChainCommand_3);
            return false;
        }
        var wweight = index!=-1?
            elements["ChainCommandItem_Weight[]"][index]:elements["ChainCommandItem_Weight[]"];
        wweight.value = troncature(wweight.value);
        wweight.value = isNaN(wweight.value)?0:wweight.value;
        if (wweight.value <= 0) {
            alert(ChainCommand_4);
            return false;
        }
        var wwidth = index!=-1?
            elements["ChainCommandItem_Width[]"][index]:elements["ChainCommandItem_Width[]"];
        wwidth.value = troncature(wwidth.value);
        wwidth.value = isNaN(wwidth.value)?0:wwidth.value;
        if (wwidth.value <= 0) {
            alert(ChainCommand_5);
            return false;
        }
        var wlength = index!=-1?
            elements["ChainCommandItem_Length[]"][index]:elements["ChainCommandItem_Length[]"];
        wlength.value = troncature(wlength.value);
        wlength.value = isNaN(wlength.value)?0:wlength.value;
        if (wlength.value <= 0) {
            alert(ChainCommand_6);
            return false;
        }
        var wheight = index!=-1?
            elements["ChainCommandItem_Height[]"][index]:elements["ChainCommandItem_Height[]"];
        wheight.value = troncature(wheight.value);
        wheight.value = isNaN(wheight.value)?0:wheight.value;
        if (wheight.value <= 0) {
            alert(ChainCommand_7);
            return false;
        }
    }
    return true;
}

// }}}
// getFormData() {{{

/**
 * Récupère les élements du formulaire avec les données saisies.
 *
 * @access public
 * @return array un tableau associatif (nom_element=>valeur)
 */
var getFormData = function() {
    var formdata = new Object();
    formdata['ChainCommandItems'] = new Array();
    with(document.forms["ChainCommand"]) {
        for(var i=0; i< elements.length; i++){
            elt = document.forms["ChainCommand"].elements[i];
            if (elt.name.substr(0, 13) == "ChainCommand_") {
                formdata[elt.name] = elt.value;
            }
        }
        var l = elements["ChainCommandItem_Quantity[]"].length;
        var fields = [
            'ChainCommandItem_Quantity',
            'ChainCommandItem_CoverType_ID',
            'ChainCommandItem_ProductType_ID',
            'ChainCommandItem_Width',
            'ChainCommandItem_Height',
            'ChainCommandItem_Length',
            'ChainCommandItem_Weight',
            'ChainCommandItem_Gerbability',
            'ChainCommandItem_MasterDimension'
        ];
        if (typeof(l) == 'undefined') {
            formdata['ChainCommandItems'][0] = new Object();
            for(var j=0; j<fields.length; j++) {
                formdata['ChainCommandItems'][0][fields[j]] = elements[fields[j]+'[]'].value;
            }
        } else {
            for(var i=0; i<elements["ChainCommandItem_Quantity[]"].length; i++) {
                formdata['ChainCommandItems'][i] = new Object();
                for(var j=0; j<fields.length; j++) {
                    formdata['ChainCommandItems'][i][fields[j]] = elements[fields[j]+'[]'][i].value;
                }
            }
        }
        formdata['PriceModified'] = elements['PriceModified'].value;
        if ($('isEstimate')) {
            formdata['isEstimate'] = 1;
        }
    }
    return formdata;
}

// }}}
// setFormData() {{{

/**
 * Assign au formulaire les valeurs contenues dans formdata.
 * formdata est un tableau associatif (nom_element=>valeur).
 *
 * @access public
 * @param array formdata
 * @return void
 */
var setFormData = function(formdata) {
    for(var i=0; i< document.forms["ChainCommand"].elements.length; i++){
        elt = document.forms["ChainCommand"].elements[i];
        if (typeof(formdata[elt.name]) != "undefined") {
            elt.value = troncature(formdata[elt.name]);
        }
    }
}

// }}}
// recalculateToPay() {{{

/**
 * Recalcule le net à payer en fonction du total ttc et de l'éventuel accompte
 * saisi.
 *
 * @access public
 * @return void
 */
var recalculateToPay = function() {
    with(document.forms["ChainCommand"]) {
        var installment = troncature(elements['ChainCommand_Installment'].value);
        var ttc = troncature(elements['ChainCommand_TotalPriceTTC'].value);
        elements['ChainCommand_ToPay'].value = troncature(ttc - installment);
    }
}

// }}}
// freezeForm() {{{

/**
 * Désactive tous les éléments du formulaire.
 *
 * @access public
 * @return void
 */
var freezeForm = function() {
    for(var i=0; i< document.forms["ChainCommand"].elements.length; i++){
        document.forms["ChainCommand"].elements[i].disabled = true;
    }
}

// }}}
// unfreezeForm() {{{

/**
 * Re-active tous les éléments du formulaire.
 *
 * @access public
 * @return void
 */
var unfreezeForm = function() {
    for(var i=0; i< document.forms["ChainCommand"].elements.length; i++){
        document.forms["ChainCommand"].elements[i].disabled = false;
    }
}

// }}}
