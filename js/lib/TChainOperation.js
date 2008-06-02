function TChainOperation(ownerList){
	this.Collection = TCollection;
	this.Collection();
	this.getCost = TChainOperation_getCost;
	this.getDuration = TChainOperation_getDuration;
	this.addTask = TChainOperation_addTask;

	this.operationId = 0;
	this.getOperationId = TChainOperation_getOperationId;
	this.setOperationId = TChainOperation_setOperationId;
	
	this.getLayerName = TChainOperation_getLayerName;
	this.render = TChainOperation_render;
	this.renderChildren = TChainOperation_renderChildren;
	this.refresh = TChainOperation_refresh;
	this.collapse = TChainOperation_collapse;
	this.expand = TChainOperation_expand;
	this.isCollapsed = TChainOperation_isCollapsed;
	
	this.moveUp = TChainOperation_moveUp;
	this.moveDown = TChainOperation_moveDown;
	
	if(!ownerList) alert("TChainOperation::TChainOperation(): pas de ownerList !");
	this.ownerList = ownerList;
	
	
	this.actorId =0;
	this.setActorId = TChainOperation_setActorId;
	this.forceSetActorId = TChainOperation_forceSetActorId;
	this.getActorId = TChainOperation_getActorId;
	this._isCollapsed = true;
	
	this.AttachObserver = TChainOperation_AttachObserver;
	this.DetachObserver = TChainOperation_DetachObserver;
	this.NotifyAllThatActorIsBeeingChanged = TChainOperation_NotifyAllThatActorIsBeeingChanged;
	this.Observers = new TCollection();
	this.addTask();
}

function TChainOperation_getCost(){
	var result = 0;
	for(var i = 0; i < this.getCount(); i++){
		result += this.getItem(i).getCost();
	}
	return result;
}

function TChainOperation_getDuration(){
	var result = 0;
	for(var i = 0; i < this.getCount(); i++){
		result += this.getItem(i).getDuration();
	}
	return result;
}

function TChainOperation_addTask(afterIndex){
	var result = new TChainTask(this);
	this.insertItem(afterIndex, result);
	return result;
}

function TChainOperation_setOperationId(newValue){
	if(newValue != this.getOperationId()){
		this.clear();
		this.operationId = newValue;
		this.addTask();
	}
}

function TChainOperation_setActorId(value){
	if (this.NotifyAllThatActorIsBeeingChanged(this.actorId) == true){
		this.actorId = value;
		return true;
	}
	else {
		return false;
	}
}

function TChainOperation_forceSetActorId(value){
	this.actorId = value;
}

function TChainOperation_getActorId(){
	return this.actorId;
}

function TChainOperation_setOperationId(value){
	this.operationId = value;
}

function TChainOperation_getOperationId(){
	return this.operationId;
}

function TChainOperation_getLayerName(){
	return "Operation" + this.index;
}

function TChainOperation_isCollapsed(){
	if(this.getCount() > 0){
		var theLayer = DynObject.all[this.getItem(0).getLayerName()];
		if(theLayer){
			return (theLayer.css.display == "none");
		}
	}
	return false;
}

function TChainOperation_collapse(){
	this._isCollapsed = true;
	for(var i = 0; i < this.getCount(); i++){
		var theLayer = DynObject.all[this.getItem(i).getLayerName()];
		if(theLayer){
			theLayer.css.display = "none";
		}		
	}
}

function TChainOperation_expand(){
	this._isCollapsed = false;
	for(var i = 0; i < this.getCount(); i++){
		var theLayer = DynObject.all[this.getItem(i).getLayerName()];
		if(theLayer){
			theLayer.css.display = "block";
		}
	}
}

function TChainOperation_render(){
	var theLayer = new DynLayer(this.getLayerName());
	var layerContent = DynObject.all["tplOperationBlock"].getHTML();
	theLayer.setHTML(layerContent/* + "<xmp>" + layerContent + "</xmp>"*/);
	theLayer.assign("i", this.index);
	return theLayer;
}

function TChainOperation_renderChildren(){
	var theLayer = DynObject.all[this.getLayerName()];
    var height = 0;
	for(var i = 0; i < this.getCount(); i++){
        var child = this.getItem(i);
		theLayer.addChild(child.render());
	    var childLayer = DynObject.all[child.getLayerName()];
        if (childLayer.css && !this._isCollapsed)
            height += parseInt(childLayer.css.height);
	}
    theLayer.css.height = parseInt(theLayer.css.height) + height;
}

function TChainOperation_refresh(){
	var theLayer = DynObject.all[this.getLayerName()];
	theLayer.assign("Operation" + this.index + "Cost", this.getCost());
	theLayer.assign("Operation" + this.index + "Duration", BeautifyDuration(this.getDuration()));
	theLayer.assign("OperationNo", this.index + 1);
	this.renderChildren();
	
	with(document.forms["ChainBuild"]){
		var operationWidget = elements["Operation" + this.index];
		FillWithOperation(operationWidget, this.operationId);
		this.operationId = operationWidget.options[operationWidget.selectedIndex].value;
		
		var actorWidget = elements["Operation" + this.index + "Actor"];
		FillWithActor(actorWidget, this.actorId, true);
		this.actorId = actorWidget.options[actorWidget.selectedIndex].value;
	}

	for(var i = 0; i < this.getCount(); i++){
		this.getItem(i).refresh();
	}
	if(true == this._isCollapsed){
		this.collapse();
	}else{
		this.expand();
	}
}


function TChainOperation_moveUp(index){
	// l'ordre d'échange est inversé entre la gestion interne 
	// à l'aide d'un tableau et l'affichage, ce qui explique le - 1
	return this.exchange(index, index - 1);
}

function TChainOperation_moveDown(index){
	// l'ordre d'échange est inversé entre la gestion interne 
	// à l'aide d'un tableau et l'affichage, ce qui explique le + 1
	return this.exchange(index, index + 1);
}


/**
 *  Rajoute un observer à la collection observer
 * @param  
 */
function TChainOperation_AttachObserver(Observer){
	this.Observers.addItem(Observer);
}
TChainOperation.prototype.AttachObserver = TChainOperation_AttachObserver;


/**
 *  Supprime l'observer de la collection
 * @param  
 */
function TChainOperation_DetachObserver(Observer){
	for (i=0;i<this.Observers.getCount();i++){
		if (this.Observers.getItem(i)==Observer){
			this.Observers.removeItem(i);
			return true;
		}
	}
	return false;
}
TChainOperation.prototype.DetachObserver = TChainOperation_DetachObserver;


/**
 *  Notifie que à tous les observers l'acteur va être supprimé de la liste
 */
function TChainOperation_NotifyAllThatActorIsBeeingChanged(old_value){
	var value_returned = true;
	for (i=0;i<this.Observers.getCount();i++){
		//var fct = this.Observers.getItem(i);
		//value_returned = value_returned && fct.call(this,old_value);
		value_returned = value_returned && this.Observers.getItem(i).CanActorChange(old_value);
	}
	return value_returned;
}
TChainOperation.prototype.NotifyAllThatActorIsBeeingChanged = TChainOperation_NotifyAllThatActorIsBeeingChanged;
