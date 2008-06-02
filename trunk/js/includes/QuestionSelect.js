/**
 * js de l'ecran de selection d'une question
 * @version $Id: QuestionSelect.js,v 1.6 2008-05-07 16:36:39 ben Exp $
 * @copyright © 2006 - ATEOR - All rights reserved.
 */

/**
 * update le select des question en fonction de ceux des Thèmes et des
 * catégories.
 */ 
function displayQuestionSelect() {
    var d = fw.ajax.call('questionSelect_getQuestions',
        $('ThemeID').value, $('CategoryID').value);
    var onSuccess = function (data) {
        // contruire les options
        if (data.isError) {
            alert('Erreur (code ' + data.errorCode +'): ' + data.errorMessage);
            return;
        }
        if (!data.length || data.length == 0) {
            var opt = createDOM('option', {'value': 0}, 'N/A');
            replaceChildNodes($('QuestionID'), opt);
            return;
        }
        var nodes = new Array();
        for(var i=0; i<data.length; i++) {
            //createDOM('option', {'value': data[i].id}, data[i].toString);
            nodes[i+1] = createDOM('option', {'value': data[i].id}, data[i].toString);
        }
        replaceChildNodes($('QuestionID'), nodes)
    }
    var onError = function (err) {
        alert('Erreur: ' + err);
    }
    d.addCallbacks(onSuccess, onError);
    return d;
}
