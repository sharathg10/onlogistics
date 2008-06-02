function TBlockList(ContainerName){
	this.Collection = TCollection;
	this.Collection();
	this.render = TBlockList_render;

	this.ContainerName = !ContainerName?"Main":ContainerName;
	this.getContainerName = TBlockList_getContainerName;
	this.setContainerName = TBlockList_setContainerName;
	
	this.moveUp = TBlockList_moveUp;
	this.moveDown = TBlockList_moveDown;
}

function TBlockList_getContainerName(){
	return this.ContainerName;
}

function TBlockList_setContainerName(newName){
	this.ContainerName = newName;
}

function TBlockList_render(){
	var mainLayer = DynObject.all[this.getContainerName()];
	mainLayer.setHTML("");
	
	for(var i = 0; i < this.getCount(); i++){
		var theChild = this.getItem(i);
		mainLayer.addChild(theChild.render());
//		theChild.renderChildren();
	}
}

function TBlockList_moveUp(index){
	// l'ordre d'échange est inversé entre la gestion interne 
	// à l'aide d'un tableau et l'affichage, ce qui explique le - 1
	return this.exchange(index, index - 1);
}

function TBlockList_moveDown(index){
	// l'ordre d'échange est inversé entre la gestion interne 
	// à l'aide d'un tableau et l'affichage, ce qui explique le + 1
	return this.exchange(index, index + 1);
}
