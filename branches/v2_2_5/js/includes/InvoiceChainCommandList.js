/**
 *
 * @version $Id$
 * @copyright 2005 ATEOR - All rights reserved.
 **/

// XXX pour compat avec autres écrans
var RecalculateItemTotal = function() {};
var RecalculateTotal = function() {};
var getItemIndex = function() {};

/**
 * Calcul le prix hors taxe de la prestation en fonction
 * de la remise globale.
 */
function recalculatePriceHT(globalHanding)
{
    var prestHT   = fw.i18n.extractNumber('totalPrestHT');
    var packing   = fw.i18n.extractNumber('Packing');
    var insurance = fw.i18n.extractNumber('Insurance');
    var totalPriceHT = add(add(subs(prestHT,
        mul(prestHT, div(globalHanding,100))), packing), insurance);
    $('totalpriceHT').value = isNaN(totalPriceHT)?"---":fw.i18n.formatNumber(totalPriceHT);
}

/**
 * Recalcule la TVA
 */
function recalculateTVA(rate)
{
    var TVA = fw.i18n.extractNumber('tvaTotal');
    $('tvaTotal').value = isNaN(TVA)?"---":fw.i18n.formatNumber(TVA);
}

/**
* Calcul le prix total à payer
*/
function recalculateTotalPrice()
{
    var totalHT  = 0;
    var totalTTC = 0;

    /*remise globale apliquée sur le montant de la prestation HT*/
    var globalHanding = 0;
    if($('GlobalHanding').value != '' ) {
        globalHanding = fw.i18n.extractNumber('GlobalHanding');
        if (globalHanding < 0 || globalHanding > 100) {
            alert(InvoiceAddEdit_0);
            $('GlobalHanding').value = '';
            globalHanding = 0;
        }
    }
    recalculatePriceHT(globalHanding);
    recalculateTVA();

    var totalPriceHT = fw.i18n.extractNumber('totalpriceHT');
    var tvaTotal = fw.i18n.extractNumber('tvaTotal');

    totalPriceTTC = add(totalPriceHT, tvaTotal);
    var installment = fw.i18n.extractNumber('Installment');
    var toPay = subs(totalPriceTTC, installment);
    if (toPay < 0) {
        toPay = 0;
    }
    $('totalpriceTTC').value = isNaN(totalPriceTTC)?"---":fw.i18n.formatNumber(totalPriceTTC);
    $('ToPay').value = isNaN(toPay)?"---":fw.i18n.formatNumber(toPay);
    $('Installment').value = isNaN(installment)?"---":fw.i18n.formatNumber(installment);
}

function validation() {
    //empeche de regler plusieurs fois la meme facture
    document.forms[0].elements["envoyer"].disabled = true;
}
