function ActorCityName(actorId, cityName){
	this.id = actorId;
	this.cityName = cityName;
}

var ActorCityNameList = new TCollection();
function nac(actorId, cityName){
	ActorCityNameList.addItem(new ActorCityName(actorId, cityName));
}