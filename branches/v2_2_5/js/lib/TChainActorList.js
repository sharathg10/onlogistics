/** 
 * Liste des acteurs de la chaine 
 * 
 */ 
function TChainActorList(){
	this.Observers = new TCollection();
}


/**
 * return vrai si il possible de changer l'acteur, faux sinon 
 * @param  
 * @param  
 */
function TChainActorList_CanActorChange(oldActor){
	if (this.GetActorOccurenceCount(oldActor)>1) {
		return true;
		}else {
			return this.NotifyAllThatActorIsBeeingRemoved(oldActor);
		}
}
TChainActorList.prototype.CanActorChange = TChainActorList_CanActorChange;


/**
 *  Rajoute un observer à la collection observer
 * @param  
 */
function TChainActorList_AttachObserver(Observer){
	this.Observers.addItem(Observer);
}
TChainActorList.prototype.AttachObserver = TChainActorList_AttachObserver;


/**
 *  Supprime l'observer de la collection
 * @param  
 */
function TChainActorList_DetachObserver(Observer){
	for (i=0;i<this.Observers.getCount();i++){
		if (this.Observers.getItem(i)==Observer){
			this.Observers.removeItem(i);
			return true;
		}
	}
	return false;
}
TChainActorList.prototype.DetachObserver = TChainActorList_DetachObserver;


/**
 *  Notifie que à tous les observers l'acteur va être supprimé de la liste
 */
function TChainActorList_NotifyAllThatActorIsBeeingRemoved(oldActor){
	var value_returned = true;
	for (i=0;i<this.Observers.getCount();i++){
		var fct = this.Observers.getItem(i);
		value_returned = value_returned && eval (fct(OperationBlockList,oldActor));
	}
	return value_returned;
}
TChainActorList.prototype.NotifyAllThatActorIsBeeingRemoved = TChainActorList_NotifyAllThatActorIsBeeingRemoved;


/**
 *  renvoie le ieme acteur de la liste
 * @param  
 */
function TChainActorList_GetItem(index){
	var actorArray = new Array;
	
	//Parcours de la Collection d'opérations
	for (var i=0; i< OperationBlockList.getCount(); i++){
		var currentActor = OperationBlockList.getItem(i).getActorId();
		// Vérification des doublons dans le tableau des acteurs déjà parsés
		for (var j=0; j<actorArray.length; j++){
			if (actorArray[j] == currentActor){
				break;
			}
		} 
		if (j==actorArray.length){
			// On n'a pas trouvé de doublon
			// on met l'acteur dans le tableau des acteurs parsé
			actorArray[actorArray.length] = currentActor;
			if ((actorArray.length-1) == index) {
				// On a atteint l'index que l'on souhaitait
				return currentActor;
			}
		}
	}
	return false;
}
TChainActorList.prototype.GetItem = TChainActorList_GetItem;


/**
 *  renvoie le nombre d'éléments de la liste
 */
function TChainActorList_GetCount(){
	var actorArray = new Array;
	//Parcours de la Collection d'opération
	for (var i=0; i< OperationBlockList.getCount(); i++){
		var currentActor = OperationBlockList.getItem(i).getActorId();
		// Vérification des doublons dans le tableau des acteurs déjà parsés
		for (var j=0; j<actorArray.length; j++){
			if (actorArray[j] == currentActor){
				break;
			}
		} 
		if (j==actorArray.length){
			// On n'a pas trouvé de doublon
			// on met l'acteur dans le tableau des acteurs parsé
			actorArray[actorArray.length] = currentActor;
		}
	}
	// On renvoie la longueur du tableau des acteurs parsés
	return actorArray.length;
}
TChainActorList.prototype.GetCount = TChainActorList_GetCount;


/**
 *  renvoie le nombre d'occurence du même acteur dans la liste (chaine) 
 * @param  
 */
function TChainActorList_GetActorOccurenceCount(actorId){
	var occurence = 0;
	for (i=0; i<OperationBlockList.getCount(); i++){
		if (actorId == OperationBlockList.getItem(i).getActorId()){
			occurence++;
		}
	}
	return occurence;
}
TChainActorList.prototype.GetActorOccurenceCount = TChainActorList_GetActorOccurenceCount;


/**
 *  renvoie une collection des Acteurs de la chaine
 * @param  
 */
function TChainActorList_GetChainActorCollection(){
	var actorCollection = new TCollection;
	
	//Parcours de la Collection d'opération
	for (var i=0; i< OperationBlockList.getCount(); i++){
		var currentActor = OperationBlockList.getItem(i).getActorId();
		// Vérification des doublons dans le tableau des acteurs déjà parsés
		for (var j=0; j<actorCollection.getCount(); j++){
			if (actorCollection.getItem(j) == currentActor){
				break;
			}
		} 
		if (j==actorCollection.getCount()){
			// On n'a pas trouvé de doublon
			// on met l'acteur dans le tableau des acteurs parsé
			actorCollection.addItem(currentActor);
		}
	}
	// On renvoie la longueur du tableau des acteurs parsés
	return actorCollection;
}
TChainActorList.prototype.GetChainActorCollection = TChainActorList_GetChainActorCollection;


/**
 * Setter de Observers
 * @param Observers
 */
function TChainActorList_setObservers(value){
	this.Observers = value;
}
TChainActorList.prototype.setObservers = TChainActorList_setObservers;


/**
 * Getter de Observers
 */
function TChainActorList_getObservers(){
	return this.Observers;
}
TChainActorList.prototype.getObservers = TChainActorList_getObservers;


/**
 * Debug function
 * Dump in a message the content of the object
 *
 **/
function debug() {
	var msg = "+TChainActorList Constructor : " + this.constructor + "\n+ Properties : \n";
	msg += " Property  = " + typeof(this.getObservers()) + " : " + this.getObservers() + "\n";
	if(!confirm(msg)) {
		return false;
	}
}
TChainActorList.prototype.debug = debug;

var ChainActorList = new TChainActorList();
ChainActorList.AttachObserver(checkAlertedActor);

