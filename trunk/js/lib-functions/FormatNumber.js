/**
 *
 * @since 06/11/2002
 * @version $Id$
 */

/**
 * Pour contourner le bug de Math.floor
 * Renvoie la troncature au nombre de decimales dec_num
 * de la valeur val...
 * Exemples:
 *         - troncature('12,5699') => 12.56
 *         - troncature(12) => 12
 *         - troncature(12,5699, false, 3) => 12.569
 *         - troncature(12,5699, true) => '12,56'
 *         - troncature(12,56, true, 3) => '12,560'
 *
 * @param mixed val une chaine, un entier ou un float
 * @param boolean format_value si ce paramètre est à true, le résultat est
 * formatté pour l'affichage (ex: 12,00)
 * @param integer dec_num le nombre de décimales (facultatif)
 * @return mixed float, string (si format_string) ou false
 **/
function troncature(val, format_value, dec_num){
    try {
        dec_num = dec_num?dec_num:2;
        var a = fw.i18n.extractNumber(val.toString());
        var factor = Math.pow(10, dec_num);
        var result = parseFloat(Math.round(a * factor)/factor);
    } catch(exc) {
        return false;
    }
    return format_value?fw.i18n.formatNumber(result, dec_num):result;
}
/**
 *
 * @access public
 * @return void
 **/
function add(num1, num2){
    return _add_subs_mul_div(num1, num2, '+');
}

/**
 *
 * @access public
 * @return void
 **/
function subs(num1, num2){
    return _add_subs_mul_div(num1, num2, '-');
}

/**
 *
 * @access public
 * @return void
 **/
function mul(num1, num2){
    return _add_subs_mul_div(num1, num2, '*');
}

/**
 *
 * @access public
 * @return void
 **/
function div(num1, num2){
    return _add_subs_mul_div(num1, num2, '/');
}

/**
 *
 * @access public
 * @return void
 **/
function _add_subs_mul_div(num1, num2, operator){
    var dec_num, res, num3;
    try {
        dec_num = num1.toString().split('.')[1].length+1;
        dec_num = dec_num<5?5:dec_num;
    } catch(exc) {
        dec_num = 5;
    }
    try {
        res = eval(num1.toString() + operator + num2.toString());
    } catch(exc) {
        return NaN;
    }
    try {
        num3 = Math.pow(10, dec_num);
        return eval("Math.round(" + res.toString() + "*" + num3 + ")/" + num3);
    } catch(exc) {
        return res;
    }
}
