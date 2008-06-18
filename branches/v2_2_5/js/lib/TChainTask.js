/**
 * TChainTask.js 
 *
 * @version $Id$
 * @copyright 2002 - ARECOM International 
 **/
 
/**
 * Constructor 
 *
 **/

function TChainTask(ownerChainOperation){

	this.isPivot = TChainTask_isPivot;
	this.getLayerName = TChainTask_getLayerName;
	this.render = TChainTask_render;
	this.refresh = TChainTask_refresh;
	
	this.getDuration = TChainTask_getDuration;
	this.setDuration = TChainTask_setDuration;
	this.duration = 0;
	
	this.getKilometerNumber = TChainTask_getKilometerNumber;
	this.setKilometerNumber = TChainTask_setKilometerNumber;
	this.kilometerNumber = 0;	
	
	this.getDepartureActor = TChainTask_getDepartureActor;
	this.setDepartureActor = TChainTask_setDepartureActor;
	this.departureActor = 0;

	this.getDepartureZone = TChainTask_getDepartureZone;
	this.setDepartureZone = TChainTask_setDepartureZone;
	this.departureZone = 0;
	
	this.getDepartureSite = TChainTask_getDepartureSite;
	this.setDepartureSite = TChainTask_setDepartureSite;
	this.departureSite = 0;

	
	this.getArrivalActor = TChainTask_getArrivalActor;
	this.setArrivalActor = TChainTask_setArrivalActor;
	this.arrivalActor = 0;
	
	this.getArrivalZone = TChainTask_getArrivalZone;
	this.setArrivalZone = TChainTask_setArrivalZone;
	this.arrivalZone = 0;
	
	this.getArrivalSite = TChainTask_getArrivalSite;
	this.setArrivalSite = TChainTask_setArrivalSite;
	this.arrivalSite = 0;
	
	this.getDepartureWeeklyInstantDay  = TChainTask_getDepartureWeeklyInstantDay;
	this.setDepartureWeeklyInstantDay  = TChainTask_setDepartureWeeklyInstantDay;
	this.departureWeeklyInstantDay = 0;
	
	this.getDepartureWeeklyInstantTime = TChainTask_getDepartureWeeklyInstantTime;
	this.setDepartureWeeklyInstantTime = TChainTask_setDepartureWeeklyInstantTime;
	this.departureWeeklyInstantTime = 0;
	
	this.getDepartureInstantDate = TChainTask_getDepartureInstantDate;
	this.setDepartureInstantDate = TChainTask_setDepartureInstantDate;
	this.departureInstantDate = 0;
	
	this.getArrivalWeeklyInstantDay = TChainTask_getArrivalWeeklyInstantDay;
	this.setArrivalWeeklyInstantDay = TChainTask_setArrivalWeeklyInstantDay;
	this.arrivalWeeklyInstantDay = 0;
	
	this.getArrivalWeeklyInstantTime = TChainTask_getArrivalWeeklyInstantTime;	
	this.setArrivalWeeklyInstantTime = TChainTask_setArrivalWeeklyInstantTime;
	this.arrivalWeeklyInstantTime = 0;
	
	this.getArrivalInstantDate = TChainTask_getArrivalInstantDate;
	this.setArrivalInstantDate = TChainTask_setArrivalInstantDate;
	this.arrivalInstantDate = 0;		

	this.getDurationType = TChainTask_getDurationType;
	this.setDurationType = TChainTask_setDurationType;
	//correspond au mode de calcul	
	this.durationType = 0;
	// Uniquement pour la compréhension au nioveau de l'affichage
	this.getDurationTypeLabel = TChainTask_getDurationTypeLabel;
	this.setDurationTypeLabel = TChainTask_setDurationTypeLabel;
	this.durationTypeLabel = TChainTask_0;
	
	this.getCost = TChainTask_getCost;
	this.setCost = TChainTask_setCost;
	this.cost = 0;
	
	this.getCostType = TChainTask_getCostType;
	this.setCostType = TChainTask_setCostType;
	//correspond au mode de calcul
	this.costType = 0;
	// Uniquement pour la compréhension au nioveau de l'affichage
	this.getCostTypeLabel = TChainTask_getCostTypeLabel;
	this.setCostTypeLabel = TChainTask_setCostTypeLabel;
	this.costTypeLabel = TChainTask_0;

	// Modèle de ressource
	this.ressourceGroup = 0;
	this.getRessourceGroup = TChainTask_getRessourceGroup;
	this.setRessourceGroup = TChainTask_setRessourceGroup;

	this.getPivotDate = TChainTask_getPivotDate; 
	this.setPivotDate = TChainTask_setPivotDate;
	
	this.getAbstractDepartureType = TChainTask_getAbstractDepartureType;
	this.setAbstractDepartureType = TChainTask_setAbstractDepartureType;
	this.abstractDepartureType    = 0;

	this.getAbstractArrivalType = TChainTask_getAbstractArrivalType;
	this.setAbstractArrivalType = TChainTask_setAbstractArrivalType;
	this.abstractArrivalType    = 0;
	
	this.getInteruptibleTask = TChainTask_getInteruptibleTask;
	this.setInteruptibleTask = TChainTask_setInteruptibleTask;
	this.interuptibleTask = 1;
	
	this.getTriggerMode = TChainTask_getTriggerMode;
	this.setTriggerMode = TChainTask_setTriggerMode;
	this.triggerMode = 0;

	this.getTriggerDelta = TChainTask_getTriggerDelta;
	this.setTriggerDelta = TChainTask_setTriggerDelta;
	this.triggerDelta = "+0000";

	this.setId = TChainTask_setId;
	this.getId = TChainTask_getId;
	this.id = 0;
	
	this.getInstructions = TChainTask_getInstructions;
	this.setInstructions = TChainTask_setInstructions;
	this.instructions = "";
	
	if(!ownerChainOperation) alert("TChainTask:: pas de operation owner !");
	this.ownerChainOperation = ownerChainOperation;
		
	this.setChainOperationOwner = TChainTask_setChainOperationOwner;
	this.getChainOperationOwner = TChainTask_getChainOperationOwner;
	
	this.getAutoAlert = TChainTask_getAutoAlert;
	this.setAutoAlert = TChainTask_setAutoAlert;
	this.autoAlert = 0;
	
	this.getAlertedUsers = TChainTask_getAlertedUsers;
	this.setAlertedUsers = TChainTask_setAlertedUsers;
	this.alertedUsers = '';	
	
	// Tache d'activation
	this.getChainToActivate = TChainTask_getChainToActivate;
	this.setChainToActivate = TChainTask_setChainToActivate;
	this.ChainToActivate = 0;
	
	this.getHasProductCommandType = TChainTask_getHasProductCommandType;
	this.setHasProductCommandType = TChainTask_setHasProductCommandType;
	this.hasProductCommandType = -1;
	
	this.getProductCommandType = TChainTask_getProductCommandType;
	this.setProductCommandType = TChainTask_setProductCommandType;
	this.productCommandType = 0;
	
	this.getChainDepartureActor = TChainTask_getChainDepartureActor;
	this.setChainDepartureActor = TChainTask_setChainDepartureActor;
	this.ChainDepartureActor = 0;
	
	this.getChainDepartureSite = TChainTask_getChainDepartureSite;
	this.setChainDepartureSite = TChainTask_setChainDepartureSite;
	this.ChainDepartureSite = 0;
	
	this.getChainArrivalActor = TChainTask_getChainArrivalActor;
	this.setChainArrivalActor = TChainTask_setChainArrivalActor;
	this.ChainArrivalActor = 0;
	
	this.getChainArrivalSite = TChainTask_getChainArrivalSite;
	this.setChainArrivalSite = TChainTask_setChainArrivalSite;
	this.ChainArrivalSite = 0;
	
	this.getWishedDateType = TChainTask_getWishedDateType;
	this.setWishedDateType = TChainTask_setWishedDateType;
	this.WishedDateType = 0;
	
	this.getDelta = TChainTask_getDelta;
	this.setDelta = TChainTask_setDelta;
	this.Delta = 0;
	
	this.getNomenclature = TChainTask_getNomenclature;
	this.setNomenclature = TChainTask_setNomenclature;
	this.nomenclature = 0;
	
	this._componentArray = new Array();
	
	this.getComponentQuantityRatio = TChainTask_getComponentQuantityRatio;
	this.setComponentQuantityRatio = TChainTask_setComponentQuantityRatio;
	this.componentQuantityRatio = 1;
	
	this.getActivationPerSupplier = TChainTask_getActivationPerSupplier;
	this.setActivationPerSupplier = TChainTask_setActivationPerSupplier;
	this.activationPerSupplier = 0;
	
	this.getComponent = TChainTask_getComponent;
	this.setComponent = TChainTask_setComponent;
	this.component = new Array();
}

/**
 * Accessors  
 *
 **/
 
function TChainTask_getPivotDate(){
	return this.pivotDate;
}

function TChainTask_setPivotDate(value){
	this.pivotDate = value;
}

function TChainTask_getAbstractDepartureType(){
	return this.abstractDepartureType;
}

function TChainTask_setAbstractDepartureType(value){
	this.abstractDepartureType = value;
}

function TChainTask_getAbstractArrivalType(){
	return this.abstractArrivalType;
}

function TChainTask_setAbstractArrivalType(value){
	this.abstractArrivalType = value;
}

function TChainTask_getInteruptibleTask(){
	return this.interuptibleTask;
}

function TChainTask_setInteruptibleTask(value){
	this.interuptibleTask = value;
}

function TChainTask_getTriggerMode(){
	return this.triggerMode;
}

function TChainTask_setTriggerMode(value){
	this.triggerMode = value;
}

function TChainTask_getTriggerDelta(){
	return this.triggerDelta;
}

function TChainTask_setTriggerDelta(value){
	this.triggerDelta = value;
}


function TChainTask_setId(value){
	this.id = value;
}

function TChainTask_getId(){
	return this.id;
}

function TChainTask_getCost(){
	return this.cost;
}

function TChainTask_setCost(value){
	this.cost = value;
}

function TChainTask_getCostType(){
	return this.costType;
}

function TChainTask_setCostType(value){
	this.costType = value;
}

function TChainTask_getCostTypeLabel(){
	return this.costTypeLabel;
}

function TChainTask_setCostTypeLabel(value){
	this.costTypeLabel = value;
}

function TChainTask_getRessourceGroup(){
	return this.ressourceGroup;
}

function TChainTask_setRessourceGroup(value){
	this.ressourceGroup = value;
}

function TChainTask_getKilometerNumber(){
	return this.kilometerNumber;
}

function TChainTask_setKilometerNumber(value){
	this.kilometerNumber = value;
}

function TChainTask_getDuration(){
	return this.duration;
}

function TChainTask_setDuration(value){
	this.duration = value;
}

function TChainTask_getDurationType(){
	return this.durationType;
}

function TChainTask_setDurationType(value){
	this.durationType = value;
}

function TChainTask_getDurationTypeLabel(){
	return this.durationTypeLabel;
}

function TChainTask_setDurationTypeLabel(value){
	this.durationTypeLabel = value;
}

function TChainTask_getInstructions(){
	return unescape(this.instructions).replace(/\+/g, ' ');
}

function TChainTask_setInstructions(value){
	this.instructions = value;
}

function TChainTask_getDepartureActor(){
	return this.departureActor;
}

function TChainTask_setDepartureActor(value){
	this.departureActor = value;
}

function TChainTask_getDepartureZone(){
	return this.departureZone;
}

function TChainTask_setDepartureZone(value){
	this.departureZone = value;
}

function TChainTask_getArrivalActor(){
	return this.arrivalActor;
}

function TChainTask_setArrivalActor(value){
	this.arrivalActor = value;
}

function TChainTask_getArrivalZone(){
	return this.arrivalZone;
}

function TChainTask_setArrivalZone(value){
	this.arrivalZone = value;
}

function TChainTask_getDepartureSite(){
	return this.departureSite;
}

function TChainTask_setDepartureSite(value){
	this.departureSite = value;
}

function TChainTask_getArrivalSite(){
	return this.arrivalSite;
}

function TChainTask_setArrivalSite(value){
	this.arrivalSite = value;
}

function TChainTask_getDepartureWeeklyInstantDay(){
	return this.departureWeeklyInstantDay;
}

function TChainTask_setDepartureWeeklyInstantDay(value){
	this.departureWeeklyInstantDay = value;
}

function TChainTask_getDepartureWeeklyInstantTime(){
	return this.departureWeeklyInstantTime;
}

function TChainTask_setDepartureWeeklyInstantTime(value){
	this.departureWeeklyInstantTime = value;
}

function TChainTask_getDepartureInstantDate(){
	return this.departureInstantDate;
}

function TChainTask_setDepartureInstantDate(value){
	this.departureInstantDate = value;
}

function TChainTask_getArrivalWeeklyInstantDay(){
	return this.arrivalWeeklyInstantDay;
}

function TChainTask_setArrivalWeeklyInstantDay(value){
	this.arrivalWeeklyInstantDay = value;
}

function TChainTask_getArrivalWeeklyInstantTime(){
	return this.arrivalWeeklyInstantTime;
}

function TChainTask_setArrivalWeeklyInstantTime(value){
	this.arrivalWeeklyInstantTime = value;
}

function TChainTask_getArrivalInstantDate(){
	return this.arrivalInstantDate;
}

function TChainTask_setArrivalInstantDate(value){
	this.arrivalInstantDate = value;
}

function TChainTask_setChainOperationOwner(value){
	this.ownerChainOperation = value;
}
function TChainTask_getChainOperationOwner(){
	return this.ownerChainOperation;
}

function TChainTask_setAutoAlert (value){
	this.autoAlert = value;
}
function TChainTask_getAutoAlert(){
	return this.autoAlert;
}

function TChainTask_setAlertedUsers (value){
	this.alertedUsers = value;
}
function TChainTask_getAlertedUsers(){
	return this.alertedUsers;
}
/// Tache d'activation ///
function TChainTask_setChainToActivate(value){
	this.ChainToActivate = value;
}
function TChainTask_getChainToActivate(){
	return this.ChainToActivate;
}

function TChainTask_setHasProductCommandType(value){
	this.hasProductCommandType = value;
}
function TChainTask_getHasProductCommandType(){
	return this.hasProductCommandType;
}

function TChainTask_setProductCommandType(value){
	this.productCommandType = value;
}
function TChainTask_getProductCommandType(){
	return this.productCommandType;
}

function TChainTask_setChainDepartureActor(value){
	this.ChainDepartureActor = value;
}
function TChainTask_getChainDepartureActor(){
	return this.ChainDepartureActor;
}

function TChainTask_setChainDepartureSite(value){
	this.ChainDepartureSite = value;
}
function TChainTask_getChainDepartureSite(){
	return this.ChainDepartureSite;
}

function TChainTask_setChainArrivalActor(value){
	this.ChainArrivalActor = value;
}
function TChainTask_getChainArrivalActor(){
	return this.ChainArrivalActor;
}

function TChainTask_setChainArrivalSite(value){
	this.ChainArrivalSite = value;
}
function TChainTask_getChainArrivalSite(){
	return this.ChainArrivalSite;
}

function TChainTask_setWishedDateType(value){
	this.WishedDateType = value;
}
function TChainTask_getWishedDateType(){
	return this.WishedDateType;
}

function TChainTask_setDelta(value){
	this.Delta = value;
}
function TChainTask_getDelta(){
	return this.Delta;
}

function TChainTask_setComponent(value){
	this.component = value;
}
function TChainTask_getComponent(){
	return this.component;
}

function TChainTask_setComponentQuantityRatio(value){
	this.componentQuantityRatio = value;
}
function TChainTask_getComponentQuantityRatio(){
	return this.componentQuantityRatio;
}

function TChainTask_setActivationPerSupplier(value){
	this.activationPerSupplier = value;
}
function TChainTask_getActivationPerSupplier(){
	return this.activationPerSupplier;
}

function TChainTask_setNomenclature(value){
	this.nomenclature = value;
}
function TChainTask_getNomenclature(){
	return this.nomenclature;
}

/**
 * Methods 
 *
 **/
 
function TChainTask_isPivot() {
    try {
	    return this.ownerChainOperation.ownerList.getPivotObject().id == this.id;
    } catch(e) {
        return false;
    }
}

function TChainTask_getLayerName(){
	return "Task" + this.ownerChainOperation.index + "_" + this.index;
}

function TChainTask_render(){
	var theLayer = new DynLayer(this.getLayerName());
	var layerContent = DynObject.all["tplTaskBlock"].getHTML();
	theLayer.setHTML(layerContent/* + "<xmp>" + layerContent + "</xmp>"*/);
	theLayer.assign("i", this.ownerChainOperation.index);
	theLayer.assign("j", this.index);
	theLayer.assign("OperationNo", this.ownerChainOperation.index + 1);
	theLayer.assign("TaskNo", this.index + 1);
	return theLayer;
}

function TChainTask_refresh(){

	var theLayer = DynObject.all[this.getLayerName()];
	theLayer.assign("Task" + this.ownerChainOperation.index + "_"  + this.index + "CostTypeLabel", this.getCostTypeLabel());
	theLayer.assign("Task" + this.ownerChainOperation.index + "_"  + this.index + "DurationTypeLabel", this.getDurationTypeLabel());
	theLayer.assign("Task" + this.ownerChainOperation.index + "_"  + this.index + "Cost", this.getCost());
	theLayer.assign("Task" + this.ownerChainOperation.index + "_"  + this.index + "Duration", BeautifyDuration(this.getDuration()));
	theLayer.assign("Task" + this.ownerChainOperation.index + "_"  + this.index + "Css", (this.isPivot() ? 'grid_row_highlighted_odd' : 'grid_row_even'));
	
	with(document.forms["ChainBuild"]){	
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "Cost"].value = this.getCost();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "Duration"].value = this.getDuration();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "CostType"].value = this.getCostType();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "RessourceGroup"].value = this.getRessourceGroup();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "DurationType"].value = this.getDurationType();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "KilometerNumber"].value = this.getKilometerNumber();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "DepartureActor"].value = this.getDepartureActor();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "DepartureSite"].value = this.getDepartureSite();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "DepartureZone"].value = this.getDepartureZone();		
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ArrivalActor"].value = this.getArrivalActor();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ArrivalSite"].value = this.getArrivalSite();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ArrivalZone"].value = this.getArrivalZone();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "Instructions"].value = this.getInstructions();	

		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "AbstractDepartureType"].value = this.getAbstractDepartureType();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "DepartureWeeklyInstantDay"].value = this.getDepartureWeeklyInstantDay();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "DepartureWeeklyInstantTime"].value = this.getDepartureWeeklyInstantTime();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "DepartureInstantDate"].value = this.getDepartureInstantDate();		
						
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "AbstractArrivalType"].value = this.getAbstractArrivalType();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ArrivalWeeklyInstantDay"].value = this.getArrivalWeeklyInstantDay();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ArrivalWeeklyInstantTime"].value = this.getArrivalWeeklyInstantTime();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ArrivalInstantDate"].value = this.getArrivalInstantDate();		
									
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "InteruptibleTask"].value = this.getInteruptibleTask();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "TriggerMode"].value = this.getTriggerMode();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "TriggerDelta"].value = this.getTriggerDelta();
		
		// bloc alerte
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "AutoAlert"].value = this.getAutoAlert();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "AlertedUsers"].value = this.getAlertedUsers();
		
		// bloc Tache d'activation
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ChainToActivate"].value = this.getChainToActivate();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "HasProductCommandType"].value = this.getHasProductCommandType();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ProductCommandType"].value = this.getProductCommandType();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ChainDepartureActor"].value = this.getChainDepartureActor();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ChainDepartureSite"].value = this.getChainDepartureSite();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ChainArrivalActor"].value = this.getChainArrivalActor();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ChainArrivalSite"].value = this.getChainArrivalSite();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "WishedDateType"].value = this.getWishedDateType();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "Delta"].value = this.getDelta();
		
		var components = this._componentArray;
		try {
			var padding = '';
			for (var i=0; i<components.length; i++) {
				elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "Components"].value += padding + components[i];
				padding = '|';
			}
		} catch(e) {
		}
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ComponentQuantityRatio"].value = this.getComponentQuantityRatio();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "ActivationPerSupplier"].value = this.getActivationPerSupplier();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "Nomenclature"].value = this.getNomenclature();
		elements["Task" + this.ownerChainOperation.index + "_"  + this.index + "Component"].value = this.getComponent();
		
		var taskWidget = elements["Task" + this.ownerChainOperation.index + "_" + this.index];
		FillWithTask(taskWidget, this.ownerChainOperation.getOperationId(), this.getId());
		this.setId(taskWidget.options[taskWidget.selectedIndex].value);
	}
}	
