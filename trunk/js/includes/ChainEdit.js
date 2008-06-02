if(DynAPI.librarypath == ''){
	DynAPI.setLibraryPath('js/dynapi/src/lib/');
	DynAPI.include('dynapi.api.*');
	DynAPI.include('dynapi.ext.inline.js');
}
// DynAPI.include('dynapi.event.*');

NO_POPUP_MSG = "Impossible d'ouvrir la fenêtre du détail de la tache.\nVous utilisez peut-être un logiciel de blocage de popup.\nLe cas échéant, vous devez le désactiver pendant l'utilisation de GLAO.\n\nMerci.";

DynObject.prototype.assign = function(aVar,aContent) {
	this.pattern = "(\\[\\$JS_" + aVar + "\\])";
	this.re = new RegExp(this.pattern,"g");
	this.setHTML(this.getHTML().replace(this.re,aContent));
};

var windowTaskDetails;

DynAPI.onLoad = function(){
	LoadSettings();
	renderAll();
	/*ChainActorList = new TChainActorList(OperationBlockList);

	// Affecte les observers
	for (i=0; i<OperationBlockList.getCount(); i++){
		//OperationBlockList.getItem(i).AttachObserver(ChainActorList,ChainActorList.CanActorChange);
		OperationBlockList.getItem(i).AttachObserver(ChainActorList);
	}

	ChainActorList.AttachObserver(checkAlertedActor);*/

}

DynAPI.onUnload = function(){
	if((typeof(windowTaskDetails) != "undefined") && (windowTaskDetails.closed == false)){
		windowTaskDetails.close();
	}
}



function DeleteOperation(index){
	if(OperationBlockList.getCount() == 1){
		alert('Une chaine doit contenir au moins une opération !');
	} else {
		OperationBlockList.removeItem(index);
		OperationBlockList.render();
		OperationBlockList.refresh();
	}
}

function DeleteTask(operationIndex, taskIndex){
	if(OperationBlockList.getItem(operationIndex).getCount() == 1){
		alert('Une opération doit contenir au moins une tâche !');
	} else {
		OperationBlockList.getItem(operationIndex).removeItem(taskIndex);
		renderAll();
	}
}

function renderAll(){
	OperationBlockList.render();
	OperationBlockList.refresh();
}

_currentTaskIndex = 0;
_currentOperationIndex = 0;

function TaskDetail(operationIndex, taskIndex, winheight){
    _currentOperationIndex = operationIndex;
    _currentTaskIndex = taskIndex;
    var popupParams = "location=no,status=yes,width=800,height=465,scrollbars=yes";
    var detailURL = "ChainTaskDetails.php";
    var taskID = OperationBlockList.getItem(operationIndex).getItem(taskIndex).id;
    if (true == GLAO.isTransportTask(taskID) || GLAO.isChainActivationTask(taskID)) {
        var popupParams = "location=no,status=yes,width=800,height=680,scrollbars=yes";
    }
    if (true == GLAO.isProductionTask(taskID)) {
        detailURL += '?taskID=' + taskID;
    }

    if(taskID == 0){
        alert(ChainEdit_0);
        return false;
    }
    if (GLAO.isChainActivationTask(taskID)) {
        detailURL = "ChainActivationTaskDetail.php?activationTask=1";
    } else if (GLAO.isInternalStockTask(taskID)) {
        detailURL = "ChainActivationTaskDetail.php";
        var popupParams = "location=no,status=yes,width=800,height=420,scrollbars=yes";
    }
    var windowTaskDetails = window.open(detailURL, "TaskDetails", popupParams);

    if(!windowTaskDetails) {
        alert(NO_POPUP_MSG);
    }
}

function doOk(){
	document.forms[0].elements['PivotOp'].value = OperationBlockList.getPivotOp();
	document.forms[0].elements['PivotTask'].value = OperationBlockList.getPivotTask();
}


function ConfirmAlertTroubles(widget,index){
	ActorToDelete = OperationBlockList.getItem(index).getActorId();

	if (confirm(ChainEdit_1) == false) {
		fw.dom.selectOptionByValue(widget.id, ActorToDelete);
	}else {
		// Création des User
		UserArray = new Array;
		for (user=0; user<UserList.getCount(); user++){
			UserArray[UserList.getItem(user).id] = UserList.getItem(user);
		}

		for (op=0; op<OperationBlockList.getCount(); op++){
			for (tsk=0; tsk<OperationBlockList.getItem(op).getCount(); tsk++){
				// Récupération de la string des alerted users
				AlertedUsersString = OperationBlockList.getItem(op).getItem(tsk).getAlertedUsers();
				if (AlertedUsersString!=""){
					// Récupération du tableau des ids des acteurs avertits
					AlertedUsersArray = AlertedUsersString.split("|");
					// Initialisation de la nouvelle String (sans les users de l'acteur effacé)
					NewAlertedUsersString = "";
					padding = "";

					// Parcours des acteurs avertits
					for (us=0; us < AlertedUsersArray.length; us++){
						// Récupération de l'objet User
						user = UserArray[AlertedUsersArray[us]];
						if (user.Actor != ActorToDelete){
							NewAlertedUsersString += padding + user.id;
						}
					} // for us
					AlertedUsersString = OperationBlockList.getItem(op).getItem(tsk).setAlertedUsers(NewAlertedUsersString);
				}
			} // for tsk
		} // for op

		// Change l'acteur de l'opération
		OperationBlockList.getItem(index).forceSetActorId(widget.value);

	} // if confirm
}

function checkAndSubmit() {
    var countOp = OperationBlockList.getCount();
    for(var i=0 ; i<countOp ; i++) {
        var op = OperationBlockList.getItem(i);
        // toutes les operations doivent etre affectees à un acteur
        if(0 == op.getActorId()) {
            //alert('All operations must be assigned to an actor');
            alert(ChainEdit_2);
            return false;
        }
        // toutes les taches doivent avoir une duree
        var countTask = op.getCount();
	    for(var j=0 ; j<countTask ; j++) {
            var task = op.getItem(j);
            if(0 == task.getDuration()) {
                //alert('All tasks must have a duration');
                alert(ChainEdit_3);
                return false;
            }
        }
    }
    // une des tache doit etre la tache pivot
	if(-1 == OperationBlockList.getPivotOp() && -1 == OperationBlockList.getPivotTask()) {
        //alert('You must put a deadline on one of the tasks.');
        alert(ChainEdit_4);
        return false;
    }
	document.forms[0].elements['PivotOp'].value = OperationBlockList.getPivotOp();
	document.forms[0].elements['PivotTask'].value = OperationBlockList.getPivotTask();

    $('FormSubmitted').value==true;
    $('ChainBuild').submit();
    return true;
}
