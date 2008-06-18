/**
 * $Source: /home/cvs/onlogistics/js/includes/FlowAddEdit.js,v $
 * Fonctions JS pour le script d'ajout/edition de charges/recettes
 *
 * @version $Id$
 * @copyright © 2006 - ATEOR - All rights reserved.
 */

function calculHT(ht, handingWidget) {
    var handing = handingWidget.value;
    handing = handing.replace(' ', '');
    var newHT = ht;
    if(handing != '' && handing != '0' && handing != '%'){
        len = handing.length - 1;
        if (/^[0-9.,]+%/.test(handing)) {
            // Remise en %
            handing = fw.i18n.extractNumber(handing);
            if ((handing < 0)||(handing > 100)){
                alert(ProductCommand_4);
                handingWidget.value = '';
                handing = 0; // PUHT inchange
            }
            newHT = subs(ht, div(mul(ht, handing), 100));
        } else if (/^[0-9.,]/.test(handing)) {
            // Remise en euros
            handing = fw.i18n.extractNumber(handing);
            if (handing > ht) {
                alert(ProductCommand_5);
                handingWidget.value = '';
                handing = 0; // PUHT inchange
            }
            newHT = subs(ht, handing);
        } else {
            handingWidget.value = '';
        }
    }
    // On prefere tronquer a 2 decimales ici, au cas ou une remise style 2.564€ ait ete saisie
    //var totalBeforeHanding = troncature(mul(subs(quantity, minusquantity), PUHT));
    return troncature(newHT);

}
// updateTotal() {{{

/**
 * Met à jour le contenu des widgets de total TTC et total TVA, en fonction du
 * montant HT et de la tva sélectionnée.
 *
 * @return void
 */
function updateTotal() {
    with (document.forms['FlowAddEdit']) {
        // màj du total ht
        if (elements["FlowTypeItem_Name[]"] != null) {
            var flowTypeItemLength = elements["FlowTypeItem_Name[]"].length;
            var totalHT = 0;
            if(flowTypeItemLength==1 || flowTypeItemLength==undefined) {
                totalHT  = add(totalHT, fw.i18n.extractNumber('FlowItemTotalHT[]'));
                // gestion FlowItem_Handing
                totalHT = calculHT(totalHT, elements['FlowItemHanding[]']);
            } else {
                for(var i=0 ; i<flowTypeItemLength ; i++) {
                    if(elements["FlowItem_TotalHT[]"][i].value != '') {
                        var puHT = fw.i18n.extractNumber(elements["FlowItemTotalHT[]"][i]);
                        // gestion FlowItem_Handing
                        puHT = calculHT(puHT, elements['FlowItemHanding[]'][i]);
                        totalHT = add(totalHT, puHT);
                    }
                }
            }
        } else {
            totalHT = 0;
        }
        // gestion Flow.Hanging
        totalHT = calculHT(totalHT, elements['FlowHanding']);
        elements['Unused_TotalHT'].value = fw.i18n.formatNumber(totalHT);

        // màj des totaux tva des flowTypeItems et des totaux tva groupés par taux
        var totalTVA = 0;
        if(elements["TotalTVA_Rate[]"] != null) {
            var TotalTVALength = elements["TotalTVA_Rate[]"].length;
            if(TotalTVALength == undefined) {TotalTVALength=1;}
            for( var i=0 ; i<TotalTVALength ; i++) {
                var tva = 0;
                if (elements["FlowTypeItem_Name[]"] != null) {
                    var flowTypeItemLength = elements["FlowTypeItem_Name[]"].length;
                    if(flowTypeItemLength == undefined) {flowTypeItemLength=1;}
                    for(var j=0 ; j<flowTypeItemLength ; j++) {
                        var flowTypeItemTVA = 0;
                        var tvaRate = 0;
                        flowTypeItemTVA = (flowTypeItemLength==1)?parseFloat(elements["FlowTypeItem_TVA[]"].value):parseFloat(elements["FlowTypeItem_TVA[]"][j].value);
                        tvaRate = (TotalTVALength==1)?parseFloat(elements["TotalTVA_Rate[]"].value):parseFloat(elements["TotalTVA_Rate[]"][i].value);
                        if(flowTypeItemTVA == tvaRate) {
                            var ht=0;
                            ht = (flowTypeItemLength==1)?fw.i18n.extractNumber(elements["FlowItemTotalHT[]"]):fw.i18n.extractNumber(elements["FlowItemTotalHT[]"][j]);
                            var itemTVA = div(mul(ht, tvaRate), 100);
                            if(isNaN(itemTVA)) {
                                itemTVA = 0;
                            }

                            if(flowTypeItemLength==1) {
                                elements["FlowItem_TotalTVA[]"].value = troncature(itemTVA, true);
                            } else {
                                elements["FlowItem_TotalTVA[]"][j].value = troncature(itemTVA, true);
                            }
                            tva = add(tva, itemTVA);
                            //alert(tva);
                        }
                    }
                    if(isNaN(tva)) {
                        tva = 0;
                    }
                    //alert(tva);
                    totalTVA = add(totalTVA, tva);
                    if(TotalTVALength==1) {
                        elements['TotalTVA_Value[]'].value = troncature(tva, true);
                    } else {
                        elements['TotalTVA_Value[]'][i].value = troncature(tva, true);
                    }
                }
            }
            elements['Unused_TotalTVA'].value = troncature(totalTVA, true);
        } else {
            elements['Unused_TotalTVA'].value = 0;
        }

        // màj du total ttc
        totalTTC = add(fw.i18n.extractNumber('UnusedTotalTVA'),fw.i18n.extractNumber('UnusedTotalHT'));
        elements['Flow_TotalTTC'].value = troncature(totalTTC, true);
    }
    // mise à jour du to pay
    updateToPay();
}

// }}}
// updateToPay() {{{

/**
 * Met à jour le widget contenant le montant restant à régler en fonction du
 * montant entré dans le widget "total réglé".
 *
 * @return void
 */
function updateToPay() {
    with (document.forms['FlowAddEdit']) {
        var alreadyPaid = fw.i18n.extractNumber(elements['AlreadyPaid']);
        if(isNaN(alreadyPaid)) {
            alreadyPaid = 0;
        }
        var paid = fw.i18n.extractNumber(elements['FlowPaid']);
        if (!isNaN(paid)) {
            var ttc   = fw.i18n.extractNumber(elements['Flow_TotalTTC']);
            var topay = subs(subs(ttc, alreadyPaid), parseFloat(paid));
            topay = isNaN(topay)?ttc:topay;
            elements['Unused_ToPay'].value = troncature(topay, true);
        } else {
            var ttc   = fw.i18n.extractNumber(elements['Flow_TotalTTC']);
            elements['Flow_Paid'].value = '';
            elements['Unused_ToPay'].value = troncature(subs(ttc, alreadyPaid), true);
        }
    }
}

// }}}
// connect {{{

/**
 * appel des fonctions au chargement de la page
 */
connect(window, 'onload', function() {
    displayFlowTypeItems();
    if(!$('flowTypeItem')) {
        waitAndUpdateTotal();
    }
});

// }}}
// waitAndUpdateTotal() {{{

/**
 * Attend que les tableaux des flowtypes et des tva soient construits et met à
 * jour les tva.
 */
function waitAndUpdateTotal() {
    if (!$('tvaTotalsItems')) {
        setTimeout('waitAndUpdateTotal()', 300);
        return false;
    }
    updateTotal();
    updateToPay();
}

// }}}
// displayFlowTypeItems() {{{

/**
 * Affiche dans le bon <div> via Sajax un tablau de flowTypeItem
 */
function displayFlowTypeItems() {
    var r = fw.ajax.call('flowAddEdit_flowTypeItems', $('flowTypeId').value,
        $('flowId').value);
    var onSuccess = function (data) {
        if (data.isError) {
            alert('Erreur (code ' + data.errorCode +'): ' + data.errorMessage);
            return;
        }
        var count = data.length;
        var rows = new Array();
        var TVARateObjects = new Array();
        // construit le tableau des flowTypeItems
        for(var i=0 ; i<count ; i++) {
            if(findIdentical(TVARateObjects, data[i][3])==-1) {
                TVARateObjects.push(data[i][3]);
            }
            var line = TR(null, 
                TD({'width':'20%', 'nowrap':'nowrap'}, 
                    INPUT({'type':'text',
                        'class':'ReadOnlyField','readonly':'readonly', 
                        'name':'FlowTypeItem_Name[]','value':data[i][1]}),
                    INPUT({'type':'hidden', 'name':'FlowItem_FlowTypeItem_ID[]',
                        'value':data[i][0]})),
                TD(null, 
                    INPUT({'type':'text', 'name':'FlowItem_TotalHT[]',
                        'value':data[i][2], 'onkeyup':'updateTotal();',
                        'id':'FlowItemTotalHT[]'})),
                TD(null,
                    INPUT({'type':'text', 'name':'FlowItem_Handing[]',
                        'value':data[i][5], 'onkeyup':'updateTotal();',
                        'id':'FlowItemHanding[]'})),
                TD(null, 
                    INPUT({'type':'text', 'name':'FlowTypeItem_TVA[]',
                        'value':data[i][3], 'class':'ReadOnlyField',
                        'readonly':'readonly', 'id':'FlowTypeItemTVA[]'}),
                    INPUT({'type':'hidden', 'name':'FlowItem_TVA_ID[]',
                        'value':data[i][4]})),
                TD(null, 
                    INPUT({'type':'text', 'name':'FlowItem_TotalTVA[]',
                        'value':'0', 'class':'ReadOnlyField',
                        'readonly':'readonly', 'id':'FlowItemTotalTVA[]'})));
            rows.push(line);
        }
        if(count > 0) {
        // ajoute les lignes au tableau
        var newTable = TABLE({'class': 'form', 'width':'100%',
            'cellspacing':'0', 'cellpadding':'4'},
            THEAD(null,
                TR(null, 
                    map(partial(TD, null), ["Nom", "Total HT", "Remise", "TVA %",
                    "Total TVA"]))),
            TBODY(null, rows),
            INPUT({'type':'hidden', 'name':'flowTypeItem', 'id':'flowTypeItem',
                'value':'1'}));
        // ajoute le tableau à la div
        replaceChildNodes('FlowTypeItemDiv', newTable);
        }
        // construit les ligne du tableau des tva
        rows = new Array();
        for(var j=0 ; j<TVARateObjects.length; j++) {
            var line = TR(null, 
                TD({'width':'20%', 'nowrap':'nowrap'},
                    'Total TVA ' + TVARateObjects[j]),
                TD({'width':'80%', 'nowrap':'nowrap'}, 
                    INPUT({'type':'text', 'name':'TotalTVA_Rate[]',
                        'value':TVARateObjects[j], 'class':'ReadOnlyField',
                        'readonly':'readonly'}),
                    INPUT({'type':'text', 'name':'TotalTVA_Value[]',
                        'value':'0', 'class':'ReadOnlyField',
                        'readonly':'readonly', 'id':'TotalTVAValue[]'})));
            rows.push(line);
        }
        if(TVARateObjects.length > 0) {
        // ajoute les lignes au tableau
        var tvaTable = TABLE({'class':'form', 'width':'100%', 'cellspacing':'0',
            'cellpadding':'4'},
            TBODY(null, rows),
            INPUT({'type':'hidden', 'name':'tvaTotalsItems', 'value':'1',
                'id':'tvaTotalsItems'}));
        // ajoute le tableau à la div
        replaceChildNodes('TotalTVADiv', tvaTable);
        }
    }
    var onError = function (err) {
        alert('Erreur: ' + err);
    }
    r.addCallbacks(onSuccess, onError);
}

// }}}

