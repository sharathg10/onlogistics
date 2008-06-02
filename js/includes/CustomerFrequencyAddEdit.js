/**
 * Fonctions relatives au formulaire CustomerFrequencyAddEdit.php.
 *
 * @version $Id: CustomerFrequencyAddEdit.js,v 1.7 2008-05-07 16:36:38 ben Exp $
 * @copyright © 2006 - ATEOR - All rights reserved.
 */

/**
 * Update les widgets readonly:
 * PotentialMinValue, PotentialMaxValue et PotentialUnitType du formulaire
 * CustomerFrequencyAddEdit.php. (appel ajax)
 *
 * @param potentialID le potentiel selectionné
 * @return void
 **/
function updatePotentialWidgets() {
    var filter = new Object();
    filter['Id'] = $('potentialID').value;
    var fields = ['MinValue', 'MaxValue', 'UnitType'];
    var d = fw.ajax.call('load', 'CustomerPotential', filter, fields);
    var onSuccess = function (potential) {
        if (!potential.id) {
            // pas de potentiel trouvé
            return;
        }
        if (potential.isError) {
            return onError(potential.errorMessage+' (code: '+potential.errorCode+')');
        }
        $('minValue').value = potential.minValue;
        $('maxValue').value = potential.maxValue;
        $('unitType').value = potential.unitType.label;
    }
    var onError = function (err) {
        alert('Erreur: ' + err);
    }
    d.addCallbacks(onSuccess, onError);
}

/**
 * Update l'état des widgets de date (begin et end) du formulaire
 * CustomerFrequencyAddEdit.php en fonction du type de fréquence sélectionné.
 *
 * @return void
 **/
function updateDateWidgetsState() {
    var theform = document.forms['CustomerFrequencyAddEdit'];
    var frequencyType   = theform.elements['CustomerFrequency_Type'].value;
    var beginDateWidget_d = theform.elements['CustomerFrequency_BeginDate[d]'];
    var beginDateWidget_m = theform.elements['CustomerFrequency_BeginDate[m]'];
    var beginDateWidget_y = theform.elements['CustomerFrequency_BeginDate[Y]'];
    var endDateWidget_d   = theform.elements['CustomerFrequency_EndDate[d]'];
    var endDateWidget_m   = theform.elements['CustomerFrequency_EndDate[m]'];
    var endDateWidget_y   = theform.elements['CustomerFrequency_EndDate[Y]'];
    beginDateWidget_d.disabled = frequencyType != 2 && frequencyType != 3;
    beginDateWidget_m.disabled = frequencyType != 2 && frequencyType != 3;
    beginDateWidget_y.disabled = frequencyType != 2 && frequencyType != 3;
    endDateWidget_d.disabled   = frequencyType != 2;
    endDateWidget_m.disabled   = frequencyType != 2;
    endDateWidget_y.disabled   = frequencyType != 2;
}

// appel des fonctions au chargement de la page
onLoad = function() {
    updatePotentialWidgets();
    updateDateWidgetsState();
}
connect(window, 'onload', onLoad);
