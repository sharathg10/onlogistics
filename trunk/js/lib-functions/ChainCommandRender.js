DefaultChainCommandItem = new TChainCommandItem();
var TableItems = new TCollection();
TableItems.addItem(DefaultChainCommandItem);

function ItemsCommandRender(){
	document.forms['ChainCommand'].elements['cciCoverType'].value = TableItems.getItem(0).CoverType;
	document.forms['ChainCommand'].elements['cciGerbabylity'].value = TableItems.getItem(0).Gerbability;
	document.forms['ChainCommand'].elements['cciQuantity'].value = TableItems.getItem(0).Quantity;
	document.forms['ChainCommand'].elements['cciLength'].value = TableItems.getItem(0).Length;
	document.forms['ChainCommand'].elements['cciWidth'].value = TableItems.getItem(0).Width;
	document.forms['ChainCommand'].elements['cciHeigth'].value = TableItems.getItem(0).Heigth;
	document.forms['ChainCommand'].elements['cciPriorityDim'].value = TableItems.getItem(0).PriorityDim;
	document.forms['ChainCommand'].elements['cciWeight'].value = TableItems.getItem(0).Weight;
	document.forms['ChainCommand'].elements['cciComment'].value = TableItems.getItem(0).Comment;
};

function AddLineItem(){


}

