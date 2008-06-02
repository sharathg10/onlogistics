var OperationBlockList = new TOperationBlockList();

function TOperationBlockList(ContainerName){
	this.BlockList = TBlockList;
	this.BlockList(ContainerName);
	this.addOperation = TOperationBlockList_addOperation;
	this.refresh = TOperationBlockList_refresh;
	this.getCost = TOperationBlockList_getCost;
	this.getDuration = TOperationBlockList_getDuration;
	
	// date Pivot
	
	this.getPivotOp = TOperationBlockList_getPivotOp;
	//this.setPivotOp = TOperationBlockList_setPivotOp;
	//this.pivotOp    = -1;
	
	this.getPivotTask = TOperationBlockList_getPivotTask;
	//this.setPivotTask = TOperationBlockList_setPivotTask;
	//this.pivotTask    = -1;
	
	this.getPivotDate = TOperationBlockList_getPivotDate;
	this.setPivotDate = TOperationBlockList_setPivotDate;
	this.pivotDate    = -1;
	
	this.getPivotObject = TOperationBlockList_getPivotObject;
	this.setPivotObject = TOperationBlockList_setPivotObject;
	this.pivotObject = null;

	// Une opération par défaut
	this.addOperation();
}

function TOperationBlockList_addOperation(afterIndex){
	var result = new TChainOperation(this);
	this.insertItem(afterIndex, result);
	
	// Définition d'un observateur
	result.AttachObserver(ChainActorList);
	
	return result;
}

function TOperationBlockList_refresh(){
//	DynObject.all["ChainCost"].setHTML("<nobr>" + this.getCost() + "&nbsp;&euro;</nobr>");
//	DynObject.all["ChainDuration"].setHTML("<nobr>" + BeautifyDuration(this.getDuration()) + "</nobr>");
	for(var i = 0; i < this.getCount(); i++){
		this.getItem(i).refresh();
	}
	with(document.forms["ChainBuild"]){	
		elements["PivotOp"].value = this.getPivotOp();
		elements["PivotTask"].value = this.getPivotTask();
		elements["PivotDateType"].value = this.getPivotDate();
	}
}

function TOperationBlockList_getCost(){
	var result = 0;
	for(var i = 0; i < this.getCount(); i++){
		result += this.getItem(i).getCost();
	}
	return result;
}

function TOperationBlockList_getDuration(){
	var result = 0;
	for(var i = 0; i < this.getCount(); i++){
		result += this.getItem(i).getDuration();
	}
	return result;
}

function TOperationBlockList_getPivotOp() {
	if (this.getPivotObject() != null){
		//alert("getPivotOp : " + this.getPivotObject().toString());
		return this.getPivotObject().getChainOperationOwner().index;
		}
	else {
		//alert("getPivotOp : pivotObject ==null");	
		return -1;
		}
}

function TOperationBlockList_setPivotOp(value) {
	alert("Illegal setPivotOp call " + value);			
	this.pivotOp = value;
}

function TOperationBlockList_getPivotTask() {
	if (this.pivotObject != null){
		//alert('getPivotTask : ' + this.getPivotObject().toString());
		return this.getPivotObject().index;		
		}
	else{
		//alert("getPivotTask : pivotObject ==null");
		return -1;
		}
}


function TOperationBlockList_setPivotTask(value) {
	alert("Illegal setPivotOp call " + value);
	this.pivotTask = value;
}

function TOperationBlockList_getPivotDate() {
	return this.pivotDate;
}

function TOperationBlockList_setPivotDate(value) {
	this.pivotDate = value;
}

function TOperationBlockList_getPivotObject(){
	return this.pivotObject;
}

function TOperationBlockList_setPivotObject(value){
	//alert ('setPivotObject : ' + value.toString());
	this.pivotObject = value;
}
