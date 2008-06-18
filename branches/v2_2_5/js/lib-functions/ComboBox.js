function ComboBox_EnsureThatAnItemIsSelected(widget){
	if(widget && (-1 == widget.selectedIndex) && (widget.options.length > 0)){
		widget.selectedIndex = 0;
	}
}