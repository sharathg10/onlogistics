function TOperation(id, name){
	this.id = id;
	this.name = name;
}

var OperationList = new TCollection();
function no(id, name){
	OperationList.addItem(new TOperation(id, name));
}

function FillWithOperation(widget, selectedOperationIndex){
//	alert(widget);
	widget.length = 0;
	if((selectedOperationIndex <= 0) || !selectedOperationIndex){
		selectedOperationIndex = 0;
	}
	for(var i = 0; i < OperationList.getCount(); i++){
//		alert(OperationList.getItem(i).name);
		widget.options[widget.options.length] = new Option(OperationList.getItem(i).name, OperationList.getItem(i).id, false, (OperationList.getItem(i).id == selectedOperationIndex));
	}
	if(widget.options.length == 0){
		widget.options[widget.options.length] = new Option(OperationTaskPopulateTools_0, 0, false, true);
	}
	ComboBox_EnsureThatAnItemIsSelected(widget);
}

function TTask(id, operationId, name){
	this.id = id;
	this.operationId = operationId;
	this.name = name;
}

var TaskList = new TCollection();
function nt(id, operationId, name){
	TaskList.addItem(new TTask(id, operationId, name));
}

function FillWithTask(widget, onlyForOperationIndex, selectedTaskIndex){
//	alert("FillWithTask: " + onlyForOperationIndex);
	widget.length = 0;
	if((selectedTaskIndex <= 0) || !selectedTaskIndex){
		selectedTaskIndex = 0;
	}
	if((onlyForOperationIndex <= 0) || !onlyForOperationIndex){
		onlyForOperationIndex = 0;
	}
//	window.status = "FillWithTask([...], " + onlyForOperationIndex + ", " + selectedTaskIndex+ ")";
	
	for(var i = 0; i < TaskList.getCount(); i++){
		var taskItem = TaskList.getItem(i);
		if(taskItem.operationId == onlyForOperationIndex){
			widget.options[widget.options.length] = new Option(taskItem.name, taskItem.id, false, /*(taskItem.id == selectedTaskIndex)*/ false);
		}
	}
	for(var i = 0; i < widget.length; i++){
		if(selectedTaskIndex == widget.options[i].value){
			widget.selectedIndex = i;
			break;
		}
	}
		
	if(widget.options.length == 0){
		widget.options[widget.options.length] = new Option(OperationTaskPopulateTools_1, 0, false, true);
	}
	ComboBox_EnsureThatAnItemIsSelected(widget);
}


var expression ="";
function explode(expression,separator,what)
{
	if (expression!=null){
		var pattern = new RegExp("([0-9][0-9])"+separator+"([0-9][0-9])"+separator+"([0-9][0-9])","ig");
		pattern.exec(expression);
		if(what==1) { return RegExp.$1; } else { return RegExp.$2; }
	}
}


