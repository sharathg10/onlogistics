
function TransmitToParent(){
	window.opener.document.forms[0].elements['localisation'].value = document.forms[0].elements['localisationaera'].value;
	window.opener.VBlock_HandleScan('AQUITMNT');
	window.close();
}