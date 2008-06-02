/**
 * Fonction permettant de transformer une durée en minutes en chaine 
 * plus lisible décrivant un nombre de jour, heure et minutes.
 *
 * Ex: 
 *  60    = 1h. 
 *  150   = 2h. 30min.
 *
 * $Id: TimeTools.js,v 1.2 2008-05-07 16:36:39 ben Exp $
 */
function BeautifyDuration(rawDuration){

	rawDuration = rawDuration / 60; // Pour la passer des secondes en minutes
	var days = Math.floor(rawDuration / (24 * 60));
	var hours = Math.floor((rawDuration - 24 * 60 * days) / 60);
	var minutes = rawDuration - 24 * 60 * days - 60 * hours;
	var result= "";
	if(days != 0){
		result += days + TimeTools_0 + ". ";
	}
	if(hours != 0){
		result += hours + TimeTools_1 + ". ";
	}
	if(minutes != 0){
		result += minutes + TimeTools_2 + ".";
	}
	if(result == ""){
		result = "0 " + TimeTools_2 + ".";
	}

	return result;
}