/**
 * @version $Id$
 * @copyright 2002-2006 ATEOR - All rights reserved.
 */

/*
 * Verifie le format JJ/MM/AAAA saisi et la validite de la date.
 * @param string dateString date qu'il faut checker
 */
function checkDate(dateString) {
    if (dateString == "") {
        return true;
    }

    var d = dateString.split("/");
    if ((d.length != 3) || isNaN(parseInt(d[0])) || isNaN(parseInt(d[1]))
            || isNaN(parseInt(d[2])) || d[0].length != 2 || d[1].length != 2
            || d[2].length != 4) {
        return false;
    }

    // Attention, les mois vont de 0 à 11
    var datetest = new Date(eval(d[2]),eval(d[1])-1,eval(d[0]));
    // Reste a verifier que jour, mois et année obtenus sont ceux saisis par l'user.
    if ((datetest.getDate() != eval(d[0])) || (datetest.getMonth() != eval(d[1])-1)
            || (datetest.getFullYear() != eval(d[2]))) {
        return false;
    }
    return true;
}
