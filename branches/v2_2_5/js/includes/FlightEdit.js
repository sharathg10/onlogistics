/**
 * @version $Id$
 * @copyright 2005 ATEOR
 **/
 
// Correspond a ActivatedChainTaskDetail.const.php
var NATURE_NONE = 0; // pour les ack qui ne sont pas de vol
var NATURE_INST = 1; // pour Instruction
var NATURE_TP = 2;   // pour Transport public
var NATURE_TA = 3;   // pour Travail aérien
var NATURE_SIM = 4;  // pour Simulation

// Fonctions à bord
var PILOTE_ELEVE = 2;
var PILOTE_INSTRUCTEUR = 3;

/**
 * Determine les Fonctions à bord selon la nature du vol
 * 
 * @return void 
 **/
function selectNature() {
	with (document.forms[0]) {
		if (parseInt(elements["ActivatedChainTaskDetail_Nature"].options[elements["ActivatedChainTaskDetail_Nature"].selectedIndex].value) == NATURE_INST) {
			elements["ActivatedChainTaskDetail_CustomerSeat"].selectedIndex = PILOTE_ELEVE;
			if (elements["RealActor"].selectedIndex == 0) {
				elements["ActivatedChainTaskDetail_InstructorSeat"].selectedIndex = 0;
			}
			else {
				elements["ActivatedChainTaskDetail_InstructorSeat"].selectedIndex = PILOTE_INSTRUCTEUR;
			}
		}
	}
} 

/*
 * Juste pour verifier que la nature du vol ait bien ete selectionnee
 */
function validation() {
    if (document.forms[0].elements["ActivatedChainTaskDetail_Nature"].selectedIndex == 0) {
		alert(FlightEdit_0);
		return false;
	}
	return true;
}