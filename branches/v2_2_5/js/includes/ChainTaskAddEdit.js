
/**
 * Fonctions relatives au formulaire CustomerAttractivityAddEdit.php.
 *
 * @version $Id$
 * @copyright © 2006 - ATEOR - All rights reserved.
 */

/**
 * Active ou désactive les widgets en fonction du type de planning selectionné
 *
 * @return void
 **/ 
function updateWidgetsState() {
    var theform = document.forms['ChainTaskAddEdit'];
    var fixed  = theform.elements['DepartureInstant_Type'][0].checked;
    var weekly = theform.elements['DepartureInstant_Type'][1].checked;
    var daily = theform.elements['DepartureInstant_Type'][2].checked;
    var fixedWidgets = new Array(
        'Instant_Date_Year', 
        'Instant_Date_Month', 
        'Instant_Date_Day', 
        'Instant_Date_Hour', 
        'Instant_Date_Minute', 
        'Instant_Date_Active'
    );
    var weeklyWidgets = new Array(
        'Instant_Time_Hour', 
        'Instant_Time_Minute', 
        'Instant_Day', 
        'Instant_DayTime_Active' 
    );
    var dailyWidgets = new Array(
        'Instant_DailyTime_Hour', 
        'Instant_DailyTime_Minute', 
        'Instant_DailyTime_Active' 
    );
    for (var i=0; i<fixedWidgets.length; i++) {
        if (fixedWidgets[i] == 'Instant_Date_Active') {
            theform.elements['Departure' + fixedWidgets[i]].disabled = weekly||daily;
            theform.elements['Arrival' + fixedWidgets[i]].disabled = weekly||daily;
            continue;
        }
        if (theform.elements['DepartureInstant_Date_Active'].checked) {
            theform.elements['Departure' + fixedWidgets[i]].disabled = weekly||daily;
        } else {
            theform.elements['Departure' + fixedWidgets[i]].disabled = true;
        }
        if (theform.elements['ArrivalInstant_Date_Active'].checked) {
            theform.elements['Arrival' + fixedWidgets[i]].disabled = weekly||daily;
        } else {
            theform.elements['Arrival' + fixedWidgets[i]].disabled = true;
        }
    }
    for (var i=0; i<weeklyWidgets.length; i++) {
        if (weeklyWidgets[i] == 'Instant_DayTime_Active') {
            theform.elements['Departure' + weeklyWidgets[i]].disabled = fixed||daily;
            theform.elements['Arrival' + weeklyWidgets[i]].disabled = fixed||daily;
            continue;
        }
        if (theform.elements['DepartureInstant_DayTime_Active'].checked) {
            theform.elements['Departure' + weeklyWidgets[i]].disabled = fixed||daily;
        } else {
            theform.elements['Departure' + weeklyWidgets[i]].disabled = true;
        }
        if (theform.elements['ArrivalInstant_DayTime_Active'].checked) {
            theform.elements['Arrival' + weeklyWidgets[i]].disabled = fixed||daily;
        } else {
            theform.elements['Arrival' + weeklyWidgets[i]].disabled = true;
        }
    }
    for (var i=0; i<dailyWidgets.length; i++) {
        if (dailyWidgets[i] == 'Instant_DailyTime_Active') {
            theform.elements['Departure' + dailyWidgets[i]].disabled = fixed||weekly;
            theform.elements['Arrival' + dailyWidgets[i]].disabled = fixed||weekly;
            continue;
        }
        if (theform.elements['DepartureInstant_DailyTime_Active'].checked) {
            theform.elements['Departure' + dailyWidgets[i]].disabled = fixed||weekly;
        } else {
            theform.elements['Departure' + dailyWidgets[i]].disabled = true;
        }
        if (theform.elements['ArrivalInstant_DailyTime_Active'].checked) {
            theform.elements['Arrival' + dailyWidgets[i]].disabled = fixed||weekly;
        } else {
            theform.elements['Arrival' + dailyWidgets[i]].disabled = true;
        }
    }
}

/**
 * Active ou désactive les widgets de date de départ/arrivée en fonction de
 * l'état de la checkbox correspondante.
 *
 * @param string type Departure ou Arrival
 * @return void
 **/ 
function switchDate(type) {
    var widgets = new Array(
        type + 'Instant_Date_Year', 
        type + 'Instant_Date_Month', 
        type + 'Instant_Date_Day', 
        type + 'Instant_Date_Hour', 
        type + 'Instant_Date_Minute'
    ); 
    var theform = document.forms['ChainTaskAddEdit'];
    active = theform.elements[type + 'Instant_Date_Active'].checked;
    for (var i=0; i<widgets.length; i++) {
        theform.elements[widgets[i]].disabled = !active;
    }
}

/**
 * Active ou désactive les widgets de jour/heure de départ/arrivée en fonction
 * de l'état de la checkbox correspondante.
 *
 * @param string type Departure ou Arrival
 * @return void
 **/ 
function switchDayTime(type) {
    var widgets = new Array(
        type + 'Instant_Time_Hour', 
        type + 'Instant_Time_Minute', 
        type + 'Instant_Day'
    ); 
    var theform = document.forms['ChainTaskAddEdit'];
    active = theform.elements[type + 'Instant_DayTime_Active'].checked;
    for (var i=0; i<widgets.length; i++) {
        theform.elements[widgets[i]].disabled = !active;
    }
}

/**
 * Active ou désactive les widgets de jour/heure de départ/arrivée en fonction
 * de l'état de la checkbox correspondante.
 *
 * @param string type Departure ou Arrival
 * @return void
 **/ 
function switchTime(type) {
    var widgets = new Array(
        type + 'Instant_DailyTime_Hour', 
        type + 'Instant_DailyTime_Minute' 
    ); 
    var theform = document.forms['ChainTaskAddEdit'];
    active = theform.elements[type + 'Instant_DailyTime_Active'].checked;
    for (var i=0; i<widgets.length; i++) {
        theform.elements[widgets[i]].disabled = !active;
    }
}

// appel de la fonction au chargement de la page
onload = function(){
    updateWidgetsState();
    switchDate('Departure');
    switchDate('Arrival');
    switchDayTime('Departure');
    switchDayTime('Arrival');
    switchTime('Departure');
    switchTime('Arrival');
}
