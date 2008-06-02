/**
 * @version $Id: FlightPreparationEdit.js,v 1.3 2008-05-07 16:36:38 ben Exp $
 * @copyright 2005 ATEOR
 **/

var KILOGRAMME = 0;
var LITRE = 1;
var GALLON = 2;
var PERCENT = 3;
 
var volumeUnitConversions = new Array;
fromKilo = new Array;
fromKilo[LITRE] = 1.3888;
fromGallon = new Array;
fromGallon[LITRE] = 3.7854;
fromLitre = new Array;
fromLitre[KILOGRAMME] = 0.72;
fromLitre[GALLON] = 0.2642;

volumeUnitConversions[KILOGRAMME] = fromKilo;
volumeUnitConversions[GALLON] = fromGallon;
volumeUnitConversions[LITRE] = fromLitre;



/**
 * Convertit les unites de volume
 * 
 * @param float $value : valeur a convertir
 * @param integer $inUnit : unite en entree
 * @param integer $outUnit : unite en sortie
 * Ces parametres peuvent prendre comme valeur: KILOGRAMME, LITRE, GALLON, PERCENT...
 * @access public
 * @return float 
 **/
function convertInLiters(value, inUnit) {
	// Pas besoin de conversion dans ce cas
	if (parseInt(inUnit) == LITRE) {
	    return value;
	}
	if (parseInt(inUnit) == PERCENT) {
		// Ici, la capacite du reservoir intervient
		var TankCapacity = parseFloat(document.forms[0].elements["TankCapacity"].value);
		
		if (isNaN(TankCapacity) || TankCapacity == 0) {
			alert(FlightPreparationEdit_0);
			return 0;
		}
		value = value * TankCapacity / 100;
		// Si la capacite max est definie en litres
		if (parseInt(document.forms[0].elements["TankUnitType"].value) == LITRE) {
			return value;
		}
		else {
			inUnit = parseInt(document.forms[0].elements["TankUnitType"].value);
		}
	}
	var conversions = volumeUnitConversions[parseInt(inUnit)];
	return value * conversions[LITRE];
}

/*
 * CarburantTotal = CarburantRest + CarburantAdded
 */
function RecalculateCarburantTotal() {
	var CarburantRest, CarburantAdded;
	with (document.forms[0]) {
		CarburantRest = fw.i18n.extractNumber(elements["ActivatedChainTaskDetail_CarburantRest"].value);
		CarburantAdded = fw.i18n.extractNumber(elements["ActivatedChainTaskDetail_CarburantAdded"].value);
		
		CarburantRest = (isNaN(CarburantRest))?0:CarburantRest;
		CarburantAdded = (isNaN(CarburantAdded))?0:CarburantAdded;

		CarburantRest = convertInLiters(CarburantRest, elements["CarburantRestUnitType"].options[elements["CarburantRestUnitType"].selectedIndex].value);
		CarburantAdded = convertInLiters(CarburantAdded, elements["CarburantAddedUnitType"].options[elements["CarburantAddedUnitType"].selectedIndex].value);
		
		CarburantRest = troncature(CarburantRest, false, 2);
		CarburantAdded = troncature(CarburantAdded, false, 2);

		// Sert a ne pas avoir besoin de refaire le calcul cote serveur
		elements["ConvertedCarburantRest"].value = CarburantRest;
		elements["ConvertedCarburantAdded"].value = CarburantAdded;
		// resultats tronques a 2 decimales
		elements["ActivatedChainTaskDetail_CarburantTotal"].value = troncature(CarburantRest + CarburantAdded, true, 2);
	}
}



