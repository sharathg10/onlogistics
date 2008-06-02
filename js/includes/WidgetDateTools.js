// @todo faire en sorte de personnaliser le n) du formulaire pour une réutilisabilité
function DateWidgetToTimeStamp(index, mode){
	var Hour= eval("document.forms[0].WidgetHour" + index +".value");
	var Minute= eval("document.forms[0].WidgetMin" + index +".value");

	if( mode == "time"){
		var result = Hour + ":" + Minute;
		//alert("format 00:00:00="+result);
	}else{
		var Day= eval("document.forms[0].WidgetDay" + index +".value");
		var Month= eval("document.forms[0].WidgetMonth" + index +".value");
		var Year= eval("document.forms[0].WidgetYear" + index +".value");
  		var result = Year + "-" + Month + "-" + Day + " " + Hour + ":" + Minute;
		//alert("format 0000-00-00 00:00:00="+result);
	}

	return result;
}

function switchDepartureDateVisibility(choice) {
	if(choice == 0) {
		departureDateWidget.css.display = 'none';
		departureHourWidget.css.display = 'none';
		departureDayWidget.css.display  = 'none';			
	} else if(choice == 1) {
		departureDateWidget.css.display = 'block';
		departureHourWidget.css.display = 'block';
		departureDayWidget.css.display  = 'none';			
	} else if(choice == 3) {
		departureDateWidget.css.display = 'none';
		departureHourWidget.css.display = 'block';
		departureDayWidget.css.display  = 'none';
	} else {
		departureDateWidget.css.display = 'none';	
		departureHourWidget.css.display = 'block';
		departureDayWidget.css.display  = 'block';	
	}
}

function switchArrivalDateVisibility(choice) {
	if(choice == 0) {
		arrivalDateWidget.css.display = 'none';
		arrivalHourWidget.css.display = 'none';	
		arrivalDayWidget.css.display  = 'none';		
	} else if(choice == 1) {
		arrivalDateWidget.css.display = 'block';
		arrivalHourWidget.css.display = 'block';
		arrivalDayWidget.css.display  = 'none';
	} else if(choice == 3) {
		arrivalDateWidget.css.display = 'none';
		arrivalHourWidget.css.display = 'block';
		arrivalDayWidget.css.display  = 'none';
	} else {
		arrivalDateWidget.css.display = 'none';		
		arrivalHourWidget.css.display = 'block';
		arrivalDayWidget.css.display  = 'block';
	}
}			
