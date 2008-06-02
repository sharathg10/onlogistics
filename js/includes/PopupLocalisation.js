function popupLocalisation(){
	var Width = 400;
	var Height = 300;
	var Browser = navigator.appName;
	if(Browser.indexOf('Netscape')!=-1) {
		screenX = (window.outerWidth / 2) - (Width/2);
		screenY = (window.outerHeight / 2) - (Height/2);
	}
	else {
		screenX = (window.screen.availWidth / 2) - 300;
		screenY = (document.body.offsetHeight / 2) - 100;
	}
	
	var properties = "width=" + Width + ",height=" + Height + ",left=" + screenX + ",top=" + screenY;
	window.open('AquitmentLocalisationPopup.php',"Localisation", "sizeable,location=no,status=yes,scrollbars=no," + properties);
}
