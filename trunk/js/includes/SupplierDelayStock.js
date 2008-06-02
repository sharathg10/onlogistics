function NoLineChecked() {

	for(var i=0;i < document.forms[0].elements["gridItems[]"].length;i++) {
		
		if ( (document.forms[0].elements["gridItems[]"][i].checked == true) ){
			document.forms[0].elements["NoLine[]"][i].value = i;
		}
	} 
}