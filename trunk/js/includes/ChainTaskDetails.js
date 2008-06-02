/**
 *
 * @version $Id: ChainTaskDetails.js,v 1.12 2008-05-07 16:36:38 ben Exp $
 * @copyright 2006 ATEOR
 */

if(DynAPI.librarypath == ''){
	DynAPI.setLibraryPath('js/dynapi/src/lib/');
	DynAPI.include('dynapi.api.*');
	DynAPI.include('dynapi.ext.inline.js');
}
// DynAPI.include('dynapi.event.*');

DynObject.prototype.assign = function(aVar,aContent) {
	this.pattern = "(\\[\\$JS_" + aVar + "\\])";
	this.re = new RegExp(this.pattern,"g");
	this.setHTML(this.getHTML().replace(this.re,aContent));
};

var departureDateWidget, departureHourWidget, departureDayWidget, arrivalDateWidget, arrivalHourWidget, arrivalDayWidget;
var isPivotDate = true;

DynAPI.onLoad = function(){
	//document.forms[0].elements['AutoAlert'].value = 0;
	// charge les layers de date fixe
	departureDateWidget = DynObject.all["LDepartureFixedDate"];
	departureHourWidget = DynObject.all["LDepartureFixedTime"];
	departureDayWidget  = DynObject.all["LDepartureWeeklyDay"];

	arrivalDateWidget   = DynObject.all["LArrivalFixedDate"];
	arrivalHourWidget   = DynObject.all["LArrivalFixedTime"];
	arrivalDayWidget    = DynObject.all["LArrivalWeeklyDay"];

	TriggerDeltaLayer   = DynObject.all["TriggerDelta"];
	TransportTaskLayer  = DynObject.all["TransportTask"];
	ProductionTaskLayer = DynObject.all["ProductionTask"];

	UsersLayer = DynObject.all["Users"];
	// end

	var Layer = DynObject.all["TaskDetails"];
	Layer.assign("OpName", getCurrentOperationCaption());
	Layer.assign("OpRanking", getCurrentOperationPosition());
	Layer.assign("OpPlace", getCityNameOfActor(getActorOfOperation(opener._currentOperationIndex)));
	Layer.assign("TaskName", getCurrentTaskCaption());
	Layer.assign("TaskRanking", getCurrentTaskPosition());
	Layer.assign("PreviousTaskName", getPreviousTaskCaption());
	Layer.assign("PreviousTaskPlace", getCityNameOfActor(getActorOfOperation(getOperationIndexOfPreviousTask())));
	Layer.assign("NextTaskName", getNextTaskCaption());
	Layer.assign("NextTaskPlace", getCityNameOfActor(getActorOfOperation(getOperationIndexOfNextTask())));

	var departureActor;
	var arrivalActor;
	with(opener.OperationBlockList.getItem(opener._currentOperationIndex).getItem(opener._currentTaskIndex)){
			departureActor = getDepartureActor();
			arrivalActor = getArrivalActor();
	}

	with(opener.OperationBlockList.getItem(opener._currentOperationIndex).getItem(opener._currentTaskIndex)){

		if(true == GLAO.isTransportTask(getId())) {
			TransportTaskLayer.css.display = 'block';
		}
		if(true == GLAO.isProductionTask(getId())) {
			ProductionTaskLayer.css.display = 'block';
		}

		with(document.forms[0]){
			// Tache pivot ?
			var curTask = opener.OperationBlockList.getItem(opener._currentOperationIndex).getItem(opener._currentTaskIndex);
			if(opener.OperationBlockList.getPivotObject() == curTask){
				PivotDate.selectedIndex = opener.OperationBlockList.getPivotDate();
			}

			// Checkbox auto alerte
				if (getAutoAlert() == 1){
					elements['AutoAlert'].checked = true;
					UsersLayer.css.display = 'block';
				}
				else {
					elements['AutoAlert'].checked = false;
					UsersLayer.css.display = 'none';
				}
			// tache de production
			if(true == GLAO.isProductionTask(getId())) {
				var nomenclature = getNomenclature();
				if (nomenclature > 0) {
					SelectItemByValue(document.forms[0].elements['Nomenclature'], nomenclature);
                    var d = fw.ajax.updateSelect('Nomenclature', 'Component', 'Component', 'Nomenclature');
				    var myCallback = function () {
                        fw.dom.selectOptionByValue('Component', getComponent());
                    }
                    d.addCallback(myCallback);
                }
			}
			// Reconstruction des objets acteurs à travers un tableau de correspondance
			ActorArray = new Array;
			for (act=0; act<ActorList.getCount(); act++){
				ActorArray[ActorList.getItem(act).id] = ActorList.getItem(act);
			}
			ChainActorList = window.opener.ChainActorList;
			ChainActorIdCollection = ChainActorList.GetChainActorCollection();
			ChainActorCollection = new TCollection();
			for (actId=0; actId<ChainActorIdCollection.getCount(); actId++){
				ChainActorCollection.addItem(ActorArray[ChainActorIdCollection.getItem(actId)]);
			}

			// bloc anomalie actors
			FillWithSpecificActor(elements['AlertedActors[]'],ChainActorCollection);
			var dd = fw.ajax.updateSelect('AlertedActors', 'AlertedUsers', 'UserAccount', 'Actor', true);
            var myCallback = function () {
                if(getAlertedUsers() != '') {
                    var AlertedUsersString = getAlertedUsers();
                    fw.dom.selectOptionByValue('AlertedUsers', AlertedUsersString.split('|'));
                }
            }
            dd.addCallback(myCallback);

			// Departure Zone
			SelectItemByValue(elements['DepartureZone'], getDepartureZone());
			// Departure Actor
			var d1=fw.ajax.updateSelectCustom('DepartureZone', 'DepartureActor', 'Actor', 'Zone', 'zone_getActors');
			var depZoneId = elements['DepartureZone'].options[elements['DepartureZone'].selectedIndex].value;
///            UpdateActorList(elements['DepartureActor'], depZoneId);
            d1.addCallback(function () {
			    SelectItemByValue(elements['DepartureActor'], getDepartureActor());
			    // Departure Site
			    var d2 = UpdateSiteList('DepartureActor', 'DepartureSite', depZoneId);
                if (d2) {
                    d2.addCallback(function () {
			            SelectItemByValue(elements['DepartureSite'], getDepartureSite());
                    });
                }
            });
			// Arrival Zone
			SelectItemByValue(elements['ArrivalZone'], getArrivalZone());
			// Arrival Actor
			var d3 = fw.ajax.updateSelectCustom('ArrivalZone', 'ArrivalActor', 'Actor', 'Zone', 'zone_getActors');
            d3.addCallback(function () {
			    SelectItemByValue(elements['ArrivalActor'], getArrivalActor());
			    var arrZoneId = elements['ArrivalZone'].options[elements['ArrivalZone'].selectedIndex].value;
///			UpdateActorList(elements['ArrivalActor'], arrZoneId);
			    // Arrival Site
			    var d4 = UpdateSiteList('ArrivalActor', 'ArrivalSite', arrZoneId);
                if (d4) {
                    d4.addCallback(function () {
			            SelectItemByValue(elements['ArrivalSite'], getArrivalSite());
                    });
                }
            });
			SelectItemByValue(elements['DurationType'], getDurationType());
			SelectItemByValue(elements['CostType'], getCostType());
			SelectItemByValue(elements['RessourceGroup'], getRessourceGroup());
		}

		document.forms[0].KilometerNumber.value = getKilometerNumber();
		document.forms[0].WorkInstructions.value = getInstructions();

		for(i=0; i<document.forms[0].InteruptibleTask.length; i++){
			if(document.forms[0].InteruptibleTask[i].value == getInteruptibleTask()) {
				document.forms[0].InteruptibleTask[i].checked = true;
			}
		};

		document.forms[0].TaskDurationHour.value  = parseInt(getDuration()/3600);
		document.forms[0].TaskDurationMinute.value  = parseInt ((getDuration()-(document.forms[0].TaskDurationHour.value*3600))/60);

		document.forms[0].TaskCost.value = getCost();

		for(i=0; i<document.forms[0].TriggerMode.length; i++){
			if(document.forms[0].TriggerMode[i].value == getTriggerMode()) {
				document.forms[0].TriggerMode[i].checked = true;
			}
		};

		// Trigger Delta
		document.forms[0].TriggerDeltaSign.selectedIndex = (getTriggerDelta()>0)?0:1;

//alert(getTriggerDelta());
		for(i=0; i<document.forms[0].WidgetHour3.options.length; i++){
			if(document.forms[0].WidgetHour3.options[i].value == Math.floor(Math.abs(getTriggerDelta()) / 60)) {
				document.forms[0].WidgetHour3.selectedIndex = i;
			}
		};

		for(i=0; i<document.forms[0].WidgetMin3.options.length; i++){
			if(document.forms[0].WidgetMin3.options[i].value == Math.abs(getTriggerDelta()) - (Math.floor(Math.abs(getTriggerDelta()) / 60) * 60)) {
				document.forms[0].WidgetMin3.selectedIndex = i;
			}
		};

		// si mode de déclenchement manuel affiche le delta

		if(document.forms[0].elements["TriggerMode"][2].checked == true) {
			TriggerDeltaLayer.css.display = 'block';
		}
		// end Trigger delta
//
//		for(i=0; i<document.forms[0].CostType.options.length; i++){
//			if(document.forms[0].CostType.options[i].value == getCostType()) {
//				document.forms[0].CostType.selectedIndex = i;
//			}
//		};

        // départ
		switchDepartureDateVisibility(getAbstractDepartureType());
		for(i=0; i<document.forms[0].FixedDeparture.length; i++){
			if(document.forms[0].FixedDeparture[i].value == getAbstractDepartureType()) {
				document.forms[0].FixedDeparture[i].checked = true ;
			}
		};
		if(getAbstractDepartureType() == 1) {
			SelectItemByValue(document.forms[0].WidgetDay1, getDepartureInstantDate().substr(8,2));
			SelectItemByValue(document.forms[0].WidgetMonth1, getDepartureInstantDate().substr(5,2));
			SelectItemByValue(document.forms[0].WidgetYear1, getDepartureInstantDate().substr(0,4));
			SelectItemByValue(document.forms[0].WidgetHour1, getDepartureInstantDate().substr(11,2));
			SelectItemByValue(document.forms[0].WidgetMin1, getDepartureInstantDate().substr(14,2));
		}
		if(getAbstractDepartureType() > 1) {
			SelectItemByValue(document.forms[0].DepartureWeeklyDay, getDepartureWeeklyInstantDay());
		    SelectItemByValue(document.forms[0].WidgetHour1, getDepartureWeeklyInstantTime().substr(0,2));
		    SelectItemByValue(document.forms[0].WidgetMin1, getDepartureWeeklyInstantTime().substr(3,2));
		}

        // arrivée
		switchArrivalDateVisibility(getAbstractArrivalType());
		for(i=0; i<document.forms[0].FixedArrival.length; i++){
			if(document.forms[0].FixedArrival[i].value == getAbstractArrivalType()) {
				document.forms[0].FixedArrival[i].checked = true ;
			}
		};
		if(getAbstractArrivalType() == 1) {
			SelectItemByValue(document.forms[0].WidgetDay2, getArrivalInstantDate().substr(8,2));
			SelectItemByValue(document.forms[0].WidgetMonth2, getArrivalInstantDate().substr(5,2));
			SelectItemByValue(document.forms[0].WidgetYear2, getArrivalInstantDate().substr(0,4));
			SelectItemByValue(document.forms[0].WidgetHour2, getArrivalInstantDate().substr(11,2));
			SelectItemByValue(document.forms[0].WidgetMin2, getArrivalInstantDate().substr(14,2));
		}
		if(getAbstractArrivalType() > 1) {
			SelectItemByValue(document.forms[0].ArrivalWeeklyDay, getArrivalWeeklyInstantDay());
		    SelectItemByValue(document.forms[0].WidgetHour2, getArrivalWeeklyInstantTime().substr(0,2));
		    SelectItemByValue(document.forms[0].WidgetMin2, getArrivalWeeklyInstantTime().substr(3,2));
		}

	}

}

function SelectItemByValue(widget, value){
	for(var i = 0; i < widget.options.length; i++){
		if(widget.options[i].value == value) {
			widget.selectedIndex = i;
		}
	}
}

function getCityNameOfActor(actorId){
	if(actorId == 0){
		return TaskDetails_0;
	}
	var city = opener.ActorCityNameList.getItemById(actorId);
	if(city){
		return city.cityName;
	}
	return TaskDetails_0;
}

function getActorOfOperation(operationIndex){
	if(operationIndex == -1){
		return 0;
	}
	return opener.OperationBlockList.getItem(operationIndex).getActorId();
}

function getOperationIndexOfPreviousTask(){
	with(opener.OperationBlockList){
		with(getItem(opener._currentOperationIndex)){
			if(hasItemAtOffset(opener._currentTaskIndex - 1)){
				return opener._currentOperationIndex;
			}
		}
		if(hasItemAtOffset(opener._currentOperationIndex - 1)){
			return opener._currentOperationIndex - 1;
		}
	}
	return -1;
}

function getOperationIndexOfNextTask(){
	with(opener.OperationBlockList){
		with(getItem(opener._currentOperationIndex)){
			if(hasItemAtOffset(opener._currentTaskIndex + 1)){
				return opener._currentOperationIndex;
			}
		}
		if(hasItemAtOffset(opener._currentOperationIndex + 1)){
			return opener._currentOperationIndex + 1;
		}
	}
	return -1;
}

// Renvoie l'identifiant d'opération sélectionné correspondant à la PK de la base
function getCurrentSelectedOperationIndex(){
	return opener.OperationBlockList.getItem(opener._currentOperationIndex).getOperationId();
}

// Renvoie l'identifiant d'opération sélectionné correspondant à la PK de la base
function getNextOperationIndex(){
	if(opener.OperationBlockList.hasItemAtOffset(opener._currentOperationIndex + 1)){
		return opener.OperationBlockList.getItem(opener._currentOperationIndex + 1).getOperationId()
	}
	return 0;
}

// renvoie le libellé de l'opération suivant l'opération courante
function getNextOperationCaption(){
	var opIndex = getNextOperationIndex();
	if(0 == opIndex){
		return TaskDetails_1;
	}
	return opener.OperationList.getItemById(opIndex).name;
}

function getNextTaskIndex(){
	with(opener.OperationBlockList){
		with(getItem(opener._currentOperationIndex)){
			if(hasItemAtOffset(opener._currentTaskIndex + 1)){
				return getItem(opener._currentTaskIndex + 1).id;
			}
		}
		if(hasItemAtOffset(opener._currentOperationIndex + 1)){
			return getItem(opener._currentOperationIndex + 1).getItem(0).id;
		}
	}
	return 0;
}

function getNextTaskCaption(){
	return getTaskCaption(getNextTaskIndex());
}

function getPreviousTaskIndex(){
	with(opener.OperationBlockList){
		with(getItem(opener._currentOperationIndex)){
			if(hasItemAtOffset(opener._currentTaskIndex - 1)){
				return getItem(opener._currentTaskIndex - 1).id;
			}
		}
		if(hasItemAtOffset(opener._currentOperationIndex - 1)){
			with(getItem(opener._currentOperationIndex - 1)){
				return getItem(getCount() - 1).id;
			}
		}
	}
	return 0;
}

function getPreviousTaskCaption(){
	return getTaskCaption(getPreviousTaskIndex());
}

function getTaskCaption(taskIndex){
	if(taskIndex != 0){
		return opener.TaskList.getItemById(taskIndex).name;
	}
	return TaskDetails_2;
}

// Renvoie l'identifiant de tâche sélectionné correspondant à la PK de la base
function getCurrentSelectedTaskIndex(){
	return opener.OperationBlockList.getItem(opener._currentOperationIndex).getItem(opener._currentTaskIndex).id;
}

// Renvoie le libellé de l'opération sélectionnée
function getCurrentOperationCaption(){
	return opener.OperationList.getItemById(getCurrentSelectedOperationIndex()).name;
}

// Renvoie la position dans la chaine de l'opération sélectionnée
function getCurrentOperationPosition(){
	return opener._currentOperationIndex + 1;
}

// Renvoie le libellé de la tâche sélectionnée
function getCurrentTaskCaption(){
	return opener.TaskList.getItemById(getCurrentSelectedTaskIndex()).name;
}

// Renvoie la position dans la chaine de la tâche sélectionnée
function getCurrentTaskPosition(){
	return opener._currentTaskIndex + 1;
}


function issetPivotDate() {
	var curOp = opener.OperationBlockList;
	if(curOp.getPivotOp() != -1 && curOp.getPivotTask() != -1 && curOp.getPivotDate() != -1) {
		return true;
	} else {
		return false;
	}
}

function modifyPivotDate(value) {
	with(document.forms["TaskDetails"]) {
        var aFixedDeparture = 0;
		for(i=0; i<elements["FixedDeparture"].length; i++){
			if(elements["FixedDeparture"][i].checked == true) {
				aFixedDeparture = elements["FixedDeparture"][i].value;
			};
		};
        var aFixedArrival = 0;
		for(i=0; i<elements["FixedArrival"].length; i++){
			if(elements["FixedArrival"][i].checked == true) {
				aFixedArrival = elements["FixedArrival"][i].value;
			};
		};
        if (aFixedDeparture > 0 || aFixedArrival > 0) {
            alert(TaskDetails_3);
			elements["PivotDate"].selectedIndex = 0;
			isPivotDate = false;
            return false;
        }
	    if(issetPivotDate() && elements["PivotDate"].selectedIndex != 0) {
		    if(!confirm(TaskDetails_4)) {
			    elements["PivotDate"].selectedIndex = 0;
				isPivotDate = false;
		    }
	    }
    }
}

// event handler du bouton OK


function doOk(){
	with(document.forms["TaskDetails"]){
		var curTask = opener.OperationBlockList.getItem(opener._currentOperationIndex).getItem(opener._currentTaskIndex);
		if (elements["TaskDurationHour"].value=="") heure=0;
		else heure = parseInt(elements["TaskDurationHour"].value);
		if (elements["TaskDurationMinute"].value=="") minute=0;
		else minute = parseInt(elements["TaskDurationMinute"].value);
		duree = heure * 3600 + minute *60;
		curTask.setDuration(duree);
		curTask.setCost(parseFloat(elements["TaskCost"].value));
		curTask.setDurationType(parseInt(elements["DurationType"].value));
		curTask.setCostType(parseInt(elements["CostType"].value));
		curTask.setRessourceGroup(parseInt(elements["RessourceGroup"].value));
		curTask.setDurationTypeLabel(elements["DurationType"].options[elements["DurationType"].selectedIndex].text);
		curTask.setCostTypeLabel(elements["CostType"].options[elements["CostType"].selectedIndex].text);
		curTask.setInstructions(elements["WorkInstructions"].value);
		curTask.setKilometerNumber(elements["KilometerNumber"].value);
		//---------
		if(elements["DepartureActor"].selectedIndex != -1) {
			curTask.setDepartureActor(elements["DepartureActor"].options[elements["DepartureActor"].selectedIndex].value);
		}else{
			curTask.setDepartureActor(0);
		}
		if(elements["DepartureZone"].selectedIndex !=-1) {
			curTask.setDepartureZone(elements["DepartureZone"].options[elements["DepartureZone"].selectedIndex].value);
		}else{
			curTask.setDepartureZone(0);
		}
		if(elements["DepartureSite"].selectedIndex != -1) {
			curTask.setDepartureSite(elements["DepartureSite"].options[elements["DepartureSite"].selectedIndex].value);
		}else{
			curTask.setDepartureSite(0);
		}

		if(elements["ArrivalActor"].selectedIndex != -1) {
			curTask.setArrivalActor(elements["ArrivalActor"].options[elements["ArrivalActor"].selectedIndex].value);
		}else{
			curTask.setArrivalActor(0);
		}

		if(elements["ArrivalZone"].selectedIndex != -1) {
			curTask.setArrivalZone(elements["ArrivalZone"].options[elements["ArrivalZone"].selectedIndex].value);
		}else{
			curTask.ArrivalZone(0);
		}

		if(elements["ArrivalSite"].selectedIndex != -1) {
			curTask.setArrivalSite(elements["ArrivalSite"].options[elements["ArrivalSite"].selectedIndex].value);
		}else{
			curTask.setArrivalSite(0);
		}

		curTask.setInstructions(elements["WorkInstructions"].value);

		if (isPivotDate) {
            var tsk = opener.OperationBlockList.getItem(opener._currentOperationIndex).getItem(opener._currentTaskIndex);
            if (elements["PivotDate"].value != -1) {
			    opener.OperationBlockList.setPivotObject(tsk);
			    opener.OperationBlockList.setPivotDate(elements["PivotDate"].value);
            } else if (tsk == opener.OperationBlockList.getPivotObject()) {
			    opener.OperationBlockList.setPivotObject(null);
			    opener.OperationBlockList.setPivotDate(null);
            }
		}


		//debut modif tache 2.3

		for(i=0; i<elements["FixedDeparture"].length; i++){
			if(elements["FixedDeparture"][i].checked == true) {
				var aFixedDeparture = elements["FixedDeparture"][i].value;
			};
		};

		for(i=0; i<elements["FixedArrival"].length; i++){
			if(elements["FixedArrival"][i].checked == true) {
				var aFixedArrival = elements["FixedArrival"][i].value;
			};
		};

		if(aFixedDeparture == 1){
			curTask.setDepartureInstantDate(DateWidgetToTimeStamp(1, 'date'));
		} else if(aFixedDeparture > 1){
			curTask.setDepartureWeeklyInstantDay(elements["DepartureWeeklyDay"].value);
			curTask.setDepartureWeeklyInstantTime(DateWidgetToTimeStamp(1, 'time'));
		}

		if(aFixedArrival == 1){
			curTask.setArrivalInstantDate(DateWidgetToTimeStamp(2, 'date'));
		} else if(aFixedArrival > 1){
			curTask.setArrivalWeeklyInstantDay(elements["ArrivalWeeklyDay"].value);
			curTask.setArrivalWeeklyInstantTime(DateWidgetToTimeStamp(2, 'time'));
		}

		curTask.setAbstractDepartureType(aFixedDeparture);
		curTask.setAbstractArrivalType(aFixedArrival);

		for(i=0; i<elements["InteruptibleTask"].length; i++){
			if(elements["InteruptibleTask"][i].checked == true) {
				curTask.setInteruptibleTask(elements["InteruptibleTask"][i].value);
			}
		};
		var CurrentTriggerMode = null;
		for(i=0; i<elements["TriggerMode"].length; i++){
			if(elements["TriggerMode"][i].checked == true) {
				curTask.setTriggerMode(elements["TriggerMode"][i].value);
				CurrentTriggerMode = elements["TriggerMode"][i].value;
			}
		};

		// TriggerDelta
		if(CurrentTriggerMode == 0) {
			curTask.setTriggerDelta(0);
		} else {
			var deltaSign = parseInt(elements["TriggerDeltaSign"].value + '1');
			var absDeltaDuration = parseInt(elements["WidgetHour3"].value)* 60  + parseInt(elements["WidgetMin3"].value);
			curTask.setTriggerDelta(deltaSign * absDeltaDuration);
		}

		// fin modif tache 2.3

		if(elements["DepartureActor"].selectedIndex != -1){
			curTask.setDepartureActor(elements["DepartureActor"].options[elements["DepartureActor"].selectedIndex].value);
		}
		if(elements["ArrivalActor"].selectedIndex != -1){
			curTask.setArrivalActor(elements["ArrivalActor"].options[elements["ArrivalActor"].selectedIndex].value);
		}


		if (elements["AutoAlert"].checked == true){
			curTask.setAutoAlert(1);
			// Utilisateurs qui doivent êtres alertés
			var AlertedUsersWidget = elements["AlertedUsers[]"];
			var AlertedUsersString = '';
			var Padding = '';
			for(i=0; i<AlertedUsersWidget.length; i++){
				if(AlertedUsersWidget.options[i].selected) {
					AlertedUsersString += Padding + AlertedUsersWidget.options[i].value;
					Padding = '|';
				};
			};
			curTask.setAlertedUsers(AlertedUsersString);
		}
		else {
			curTask.setAutoAlert(0);
			curTask.setAlertedUsers("");
		}
		if (elements["Nomenclature"].value > 0) {
			curTask.setNomenclature(elements["Nomenclature"].value);
		}
		if (elements["Component"].value > 0) {
			curTask.setComponent(elements["Component"].value);
		}
	}

	opener.renderAll();
	self.close();

}

function popupInstructions(){
	var Width = 600;
	var Height = 320;
	var Browser = navigator.appName;
	if(Browser.indexOf('Netscape')!=-1) {
		screenX = (window.outerWidth / 2) - (Width/2);
		screenY = (window.outerHeight / 2) - (Height/2);
	} else {
		screenX = (window.screen.availWidth / 2) - (Width/2);
		screenY = (document.body.offsetHeight / 2) - (Height/2);
	}
	var properties = "width=" + Width + ",height=" + Height + ",left=" + screenX + ",top=" + screenY;
	window.open('ChainTextDetail.php',"Instructions", "sizeable,location=no,status=yes,scrollbars=no," + properties);
}

// event handler du bouton Cancel
function doCancel(){
	self.close();
}

/*function UpdateActorList(widget, zoneId){
	widget.length = 0; // Nettoyage
	widget.options[widget.length] = new Option(TaskDetails_5, -2, false, false);
	FillWithActorsInZone(widget, zoneId);
}*/

function UpdateSiteList(actorWidgetId, siteWidgetId, zoneId) {
    widget = $(siteWidgetId);
    widget.length = 0; // Nettoyage
    var actorId = $(actorWidgetId).value;
	if (actorId > 0) {
        var zoneFilter = zoneId==0?false:{'Zone':zoneId};
	    return fw.ajax.updateSelectCustom(actorWidgetId, siteWidgetId, 
            'Site', 'Owner', 'chainaddedit_getSiteCollection', true, zoneFilter);
    }
    else { // actorId == 0 : Aucun acteur
        widget.options[widget.length] = new Option(TaskDetails_6, 0, true, true);
    }
    return false;
}
/**
 * On met a jour 2 selects en cascade, et on attends entre les 2
 * @param string instant: 'Departure' ou 'Arrival'
 * @return void
 **/
function onUpdateZone(instant){
    var dd = fw.ajax.updateSelectCustom(instant + 'Zone', instant + 'Actor', 'Actor', 'Zone', 'zone_getActors');
    var myCallback = function () {
        UpdateSiteList(instant + 'Actor', instant + 'Site', $(instant + 'Zone').value);
    }
    dd.addCallback(myCallback);
}

function FindSelectedValues(widget){
	var return_values = new Array();
	var cpt = 0;
	for (var i=0;i<widget.length;i++){
		if(widget.options[i].selected == true) return_values[cpt++] = widget.options[i].value;
	}
	return return_values;
}
