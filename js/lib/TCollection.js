function TCollection(){
	// Attachement des méthodes
	this.addItem = TCollection_addItem;
	this.removeItem = TCollection_removeItem;
	this.getItem = TCollection_getItem;
	this.getItemById = TCollection_getItemById;
	this.setItem = TCollection_setItem;
	this.getCount = TCollection_getCount;
	this.insertItem = TCollection_insertItem;
	this.pop = TCollection_pop;
	this.exchange = TCollection_exchange;
	this.hasItemAtOffset = TCollection_hasItemAtOffset;
	this.clear = TCollection_clear;
	// Initialization
	this.clear();
}

/**
 * Ajoute un nouvel élément à la collection /Jsunit ok
 **/ 
function TCollection_addItem(item) {
  	this.setItem(this.getCount(), item);
};

function TCollection_insertItem(afterIndex, item){
	this.addItem(item);
	for(var i = this.getCount() - 1; i > afterIndex + 1; i--) {
		this.exchange(i, i - 1);
	}
}

/**
 * Supprime l'élément itemIndex de la collection /Jsunit ok
 **/ 
function TCollection_removeItem(itemIndex) {
	if(!this.hasItemAtOffset(itemIndex)){
		return false;
	}

	for(var i = itemIndex; i < this.getCount() - 1; i++) {
		this.exchange(i, i + 1);
	}
    this.pop();
	return true;
}

/**
 * Accesseur en lecture sur les éléments de la collection /Jsunit  ok
 **/
function TCollection_getItem(itemIndex) {
	return this.items[itemIndex];
}

/**
 * Accesseur en lecture sur les éléments de la collection /Jsunit ok
 **/
function TCollection_getItemById(id) {
	for(var i = 0; i < this.getCount(); i++){
		var currentItem = this.getItem(i);
		if(currentItem.id == id){
			return currentItem;
		}
	}
	return false;
}

/**
 * Accesseur en écriture sur les éléments de la collection /Jsunit ok
 **/
function TCollection_setItem(itemIndex, newValue) {
	this.items[itemIndex] = newValue;
	this.items[itemIndex].index = itemIndex;
//	alert("TCollection::setItem(): " + itemIndex);
}

/**
 * Retourne le nombre d'éléments de la collection /Jsunit ok
 **/
function TCollection_getCount() {
	return this.items.length;
}

/**
 * Supprimer et renvoie le dernier élément de la collection / Jsunit ok
 **/
function TCollection_pop(){
	if(this.getCount() == 0){
		return false;
	}
	var popedItem = this.getItem(this.getCount() - 1);
	this.items.length--;
	return popedItem;
}

/**
 * Echange les éléments aux positions index1 et index2 dans la collection / Jsunit 
 **/
function TCollection_exchange(index1, index2) {
// alert("Exchange("+index1+", "+index2+")");
	if(!this.hasItemAtOffset(index1) || !this.hasItemAtOffset(index2)){
		return false;
	}
	var tmpStorage = this.getItem(index1);
	this.setItem(index1, this.getItem(index2));
	this.setItem(index2, tmpStorage);
}

/**
 * Permet de déterminer si un offset est contenu dans la collection / Jsunit ok
 **/
function TCollection_hasItemAtOffset(offset) {
	return (offset >= 0) && (offset < this.getCount());
}

/**
 * Supprime tous les éléments de la liste
 **/
function TCollection_clear(){
	this.items = new Array();
}
