CoverType = function(id, name){
	this.id = id;
	this.name = name;
}

var CoverTypeList = new TCollection();
function na(id, name){
	CoverTypeList.addItem(new CoverType(id, name));
}

function FillWithCoverType(widget, selectedCoverTypeIndex, allowUndefinedEntry){
	widget.length = 0;
	if((selectedCoverTypeIndex <= 0) || !selectedCoverTypeIndex){
		selectedCoverTypeIndex = 0;
	}
	for(var i = 0; i < CoverTypeList.getCount(); i++){
		if(!allowUndefinedEntry && CoverTypeList.getItem(i).id <= 0){
			continue;
			
		}

		widget.options[widget.options.length] = new Option(CoverTypeList.getItem(i).name, CoverTypeList.getItem(i).id, false, (CoverTypeList.getItem(i).id == selectedCoverTypeIndex));
	}
	ComboBox_EnsureThatAnItemIsSelected(widget);
}