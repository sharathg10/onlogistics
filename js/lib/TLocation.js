/**
 * $Id: TLocation.js,v 1.2 2008-05-07 16:36:39 ben Exp $
 * Classe definissant l'objet location (lieu)
 *
 */
function TLocation(){
	this.Id = false;
	this.Name = false;
}
/**
 * Setter de Id
 * @param Id
 */
function TLocation_setId(value){
	this.Id = value;
}
TLocation.prototype.setId = TLocation_setId;
/**
 * Getter de Id
 */
function TLocation_getId(){
	return this.Id;
}
TLocation.prototype.getId = TLocation_getId;
/**
 * Setter de Name
 * @param Name
 */
function TLocation_setName(value){
	this.Name = value;
}
TLocation.prototype.setName = TLocation_setName;
/**
 * Getter de Name
 */
function TLocation_getName(){
	return this.Name;
}
TLocation.prototype.getName = TLocation_getName;
/**
 * Debug function
 * Dump in a message the content of the object
 *
 **/
function debug() {
	var msg = "+TLocation Constructor : " + this.constructor + "\n+ Properties : \n";
	msg += " Property  = " + typeof(this.getId()) + " : " + this.getId() + "\n";
	msg += " Property  = " + typeof(this.getName()) + " : " + this.getName() + "\n";
	if(!confirm(msg)) {
		return false;
	}
}
TLocation.prototype.debug = debug;
