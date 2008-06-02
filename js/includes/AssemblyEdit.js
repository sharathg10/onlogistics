/**
 * @version $Id: AssemblyEdit.js,v 1.3 2008-05-07 16:36:38 ben Exp $
 * @copyright 2005 ATEOR
 **/

connect(window, 'onload', function() {
	updateScreen();
});

/**
 * Affiche ou dissimule les paves de l'assemblage en fonction
 * de la RealQuantity saisie
 *
 * @return void
 **/
function updateScreen() {
	with (document.forms[0]) {
		var realQty = 0;
		var waitedQty = parseInt(elements["waitedQuantity"].value);
//		var QtyLength = elements["Quantity[]"].length;

		if (!isNaN(parseInt(elements["realQuantity"].value))) {
			realQty = parseInt(elements["realQuantity"].value);
			if (realQty > waitedQty) {
				alert(AssemblyEdit_0);
				realQty = 0;
			}
		}
		// On affiche les bons blocs
		for(var i=0;i < realQty;i++) {
			document.getElementById("piece_" + i).style.display="block";
		}
		// On masque les autres blocs
		for(var i=waitedQty-1;i >= realQty;i--) {
			document.getElementById("piece_" + i).style.display="none";
		}
	}
}

/**
 * Divers controles:
 *  - Le Meme No de SN/Lot n'est pas saisi 2 fois pour le meme
 *  assemblage, et pour le meme Product.
 *  - Si la piece en sortie d'assemblage est au SN, et si plusieurs assemblages,
 *  tous les SN doivent etre differents.
 *  - Un SN/Lot doit etre saisi pour chaque assemblage.
 *
 **/
function checkBeforeSubmit() {
	with (document.forms[0]) {
		var linesLength = elements["SerialNumber[]"].length;
		var linesByAssembly = linesLength / parseInt(elements["waitedQuantity"].value);
		var realQty = parseInt(elements["realQuantity"].value);
		var tMode = parseInt(elements["tracingMode"].value);  // tracingMode
		var resultSNArray = new Array; // Les SN en sortie d'assemblage (si mode de suivi)

        // Si pas de mode de suivi pour la piece en sortie d'assemblage,
        // pas de controle a faire sur les SN saisis
        if (tMode == 0) {
            return true;
        }

		for(var i=0;i < realQty;i++) {
			if (typeof(elements["SerialNumber_" + i]) != "undefined") {
				if (elements["SerialNumber_" + i].value == '') {
					alert(AssemblyEdit_2);
					return false;
				}
			}
			if (tMode == 1) {
				resultSNArray.push(elements["SerialNumber_" + i].value);
			}
			var snArray = new Array;
			for(var j=0;j < linesByAssembly;j++) {
				// 2 ConcreteProducts lies a 2 Product differents suivis au SN
				// peuvent avoir le meme SN!!!
				str = elements["itemIds[]"][(i * linesByAssembly) + j].value;
				str += "##" + elements["SerialNumber[]"][(i * linesByAssembly) + j].value;
				snArray.push(str);
			}
			snArray.sort();
			// On verifie que pas 2 elements identiques (non vides)
			for(var k=1;k < snArray.length;k++) {
				var indice = snArray[k].indexOf("##")
				if (snArray[k] == snArray[k-1] && snArray[k].length != indice + 2) {
					alert(AssemblyEdit_1 + snArray[k]);
					return false;
				}
			}
		}
		// Verif sur les SN en sortie
		if (tMode == 1) {
			resultSNArray.sort();
			// On verifie que pas 2 elements identiques (non vides)
			for(var k=1;k < resultSNArray.length;k++) {
				if (resultSNArray[k] == resultSNArray[k-1]) {
					alert(AssemblyEdit_3);
					return false;
				}
			}
		}
	}
	return true;
}
