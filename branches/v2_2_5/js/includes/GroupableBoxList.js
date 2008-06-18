var updateExpeditorSite = function() {
	fw.ajax.updateSelectCustom('boxExpeditor', 'boxExpeditorSite', 'Site',
        'Owner', 'chainaddedit_getSiteCollection', true);
}

var updateDestinatorSite = function() {
	fw.ajax.updateSelectCustom('boxDestinator', 'boxDestinatorSite', 'Site',
        'Owner', 'chainaddedit_getSiteCollection', true);
}

requiredFields = new Array(
	new Array("boxVolume", NONE, "float", GroupableBoxList_0),
	new Array("boxExpeditor", REQUIRED_AND_NOT_ZERO, "int", GroupableBoxList_1),
	new Array("boxExpeditorSite", REQUIRED_AND_NOT_ZERO, "int", GroupableBoxList_2),
	new Array("boxDestinator", REQUIRED_AND_NOT_ZERO, "int", GroupableBoxList_3),
	new Array("boxDestinatorSite", REQUIRED_AND_NOT_ZERO, "int", GroupableBoxList_4),
	new Array("boxCoverType", REQUIRED_AND_NOT_ZERO, "int", GroupableBoxList_5)
);

connect(window, 'onload', function() {
    connect('boxExpeditor', 'onchange', updateExpeditorSite);
    connect('boxDestinator', 'onchange', updateDestinatorSite);
});
