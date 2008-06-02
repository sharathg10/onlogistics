Actor = function(id, name){
	this.id = id;
	this.name = name;
}

var ActorList = new TCollection();
function na(id, name){
	ActorList.addItem(new Actor(id, name));
}

function FillWithActor(widget, selectedActorIndex, allowUndefinedEntry, allowUnselected){
	widget.length = 0;
	if((selectedActorIndex <= 0) || !selectedActorIndex){
		selectedActorIndex = 0;
	}
	for(var i = 0; i < ActorList.getCount(); i++){
		if(!allowUndefinedEntry && ActorList.getItem(i).id <= 0){
			continue;
		}
		widget.options[widget.options.length] = new Option(ActorList.getItem(i).name, ActorList.getItem(i).id, false, (ActorList.getItem(i).id == selectedActorIndex));
	}
	if(allowUnselected!=true) ComboBox_EnsureThatAnItemIsSelected(widget);
}

function FillWithSpecificActor(widget, SpecificActor, selectedActorIndex, allowUndefinedEntry, allowUnselected){
	widget.length = 0;
	if((selectedActorIndex <= 0) || !selectedActorIndex){
		selectedActorIndex = 0;
	}
	for(var i = 0; i < SpecificActor.getCount(); i++){
		if(!allowUndefinedEntry && SpecificActor.getItem(i).id <= 0){
			continue;
		}
		widget.options[widget.options.length] = new Option(SpecificActor.getItem(i).name, SpecificActor.getItem(i).id, false, (SpecificActor.getItem(i).id == selectedActorIndex));
	}
	if(allowUnselected!=true) ComboBox_EnsureThatAnItemIsSelected(widget);
}

var Site = function(id, actorId, name){
	this.id = id;
	this.actorId = actorId;
	this.name = name;
}

SiteList = new TCollection();

function ns(id, actorId, name){
	SiteList.addItem(new Site(id, actorId, name));
}
//
//function GetSiteByID(id){
//	return SiteList.getItemById(id);
//}

function FillWithSite(widget, onlyForActorIndex, selectedSiteIndex){

	//var selectedSiteIndex = widget.selectedIndex;
	if((selectedSiteIndex <= 0) || (selectedSiteIndex == false)){
		selectedSiteIndex = 0;
	}
	if((onlyForActorIndex <= 0) || !onlyForActorIndex){
		onlyForActorIndex = 0;
	}
	widget.length = 0;
	for(var i = 0; i < SiteList.getCount(); i++){
		if(0 == onlyForActorIndex){
			widget.options[widget.options.length] = new Option(SiteList.getItem(i).name, SiteList.getItem(i).id, false, (SiteList.getItem(i).id == selectedSiteIndex));
		}else if(SiteList.getItem(i).actorId == onlyForActorIndex){
			widget.options[widget.options.length] = new Option(SiteList.getItem(i).name, SiteList.getItem(i).id, false, (SiteList.getItem(i).id == selectedSiteIndex));
		}
	}
	ComboBox_EnsureThatAnItemIsSelected(widget);
}
// liste des utilsateurs pour l'envoi d'alerte anomalie
User = function(id,name, TActor) {
	this.id = id;
	this.name = name;
	this.Actor = TActor;
}

var UserList = new TCollection();
function nu(id,name,TActor){
	UserList.addItem(new User(id, name, TActor));
}

/*
Job = function(id, name){
	this.id = id;
	this.name = name;
}


*/
/*var JobList = new TCollection();

function nj(id, name){
	JobList.addItem(new Job(id, name));
}
/*
function FillWithActorFromJob(widget, selectedActorIndex, allowUndefinedEntry, idjob){
	widget.length = 0;
	if((selectedActorIndex <= 0) || !selectedActorIndex){
		selectedActorIndex = 0;
	}
	for(var i = 0; i < ActorList.getCount(); i++){
		if(!allowUndefinedEntry && ActorList.getItem(i).id <= 0){
			continue;
		}
		// Un acteur a plusieurs métiers
		var nbjob = ActorList.getItem(i).TJobs.length;
		// parcours des jobs
		for(var j = 0; j < nbjob; j++){
			var myjob = ActorList.getItem(i).TJobs[j];
			if (myjob == idjob){
				widget.options[widget.options.length] = new Option(ActorList.getItem(i).name, ActorList.getItem(i).id, false, (ActorList.getItem(i).id == selectedActorIndex));
			}
		}
	}
	ComboBox_EnsureThatAnItemIsSelected(widget);
}
function FillWithUserFromActor(widget, Actor){
	widget.length = 0;
	for(var i = 0; i < Actor.length; i++) {
		for(var j = 0; j < UserList.getCount(); j++){
			var CurrentActor = UserList.getItem(j).Actor;
			if (CurrentActor == Actor[i]){
				widget.options[widget.options.length] = new Option(UserList.getItem(j).name, UserList.getItem(j).id, false, false);
			}
		}
	}
}
function FillWithUser(widget, User){
	widget.length = 0;
	for(var i = 0; i < User.length; i++) {
		for(var j = 0; j < UserList.getCount(); j++){
			var CurrentUser = UserList.getItem(j).id;
			if (CurrentUser == User[i]){
				widget.options[widget.options.length] = new Option(UserList.getItem(j).name, UserList.getItem(j).id, false, true);
			}
		}
	}
}*/
