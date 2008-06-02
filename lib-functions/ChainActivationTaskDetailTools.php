<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require('sajax/php/Sajax.php');
/**
 * Contient une méthode relative à la lib sajax.
 * Méthodes statiques uniquement.
 * A supprimer lors de refactoring de ChainActivationTaskDetail sans sajax
 */
class SajaxTools {
    /**
     * Pour mettre ce qu'il faut entre les balises <head>, pour le remote
     * scripting.
     *
     * @static
     * @param string or array of strings $functionNames
     * @access public
     * @return void
     **/
    static function activeSajax($functionNames) {
        if (is_string($functionNames)) {
            $functionNames = array($functionNames);
        }

        sajax_init();
        $sajax_debug_mode = 1;
        $sajaxFunctions = "'" . implode("', '", $functionNames) . "'";
        eval("sajax_export(" . $sajaxFunctions . ");");
        sajax_handle_client_request();
    }
}
/**
 * Retourne le code HTML d'un select sur les State qui ont
 * le Country passe en parametre
 * @param integer $ActorId
 * @param string $type : 'Departure' ou 'Arrival'
 * @access public
 * @return array of string
 **/
function getActorSites($ActorId, $type) {
	// Si un Actor existant est passe, le select doit afficher
	// seulement les Site dont il est le Owner
	if ($ActorId > 0) {
	    $Actor = Object::load('Actor', $ActorId);
		$SiteCollection = $Actor->getSiteCollection(array(), array('Name' => SORT_ASC),
													 array('Name'));
		$siteOptions = FormTools::writeOptionsFromCollection($SiteCollection);
	}
	else {
		$SiteMapper = Mapper::singleton('Site');
		$SiteCollection = $SiteMapper->loadCollection(
				array(),
				array('Name' => SORT_ASC),
				array('Name'));
		$StorageSiteMapper = Mapper::singleton('StorageSite');
		$StorageSiteCollection = $StorageSiteMapper->loadCollection(
				array(),
				array('Name' => SORT_ASC),
				array('Name'));

		$SiteCol = $SiteCollection->merge($StorageSiteCollection);
		$SiteCol->sort('Name');
		$siteOptions = FormTools::writeOptionsFromCollection($SiteCol);
	}

	$return = '<select name="Chain' . $type . 'Site" id="Chain' . $type . 'Site">'
			  . implode("\n", $siteOptions) . "\n" . '</select>';

	// Les array ne semblent pas etre geres
	return $type . '_' . $return;
}

/**
 * Methode sajax pour renvoyer le type d'une chaine à partir de son id
 *
 * @access public
 * @param int $id l'id de la chaine
 * @return int le type de la chaine ou -1
 **/
function getChainType($id){
	$chainMapper = Mapper::singleton('Chain');
    $chain = $chainMapper->load(array('Id'=>$id));
    if ($chain instanceof Chain) {
        return $chain->getType();
    }
    return -1;
}

/**
 * Retourne un tableau ID=>toString des nomenclatures modèles
 * Remarque: il y a un filtre different suivant si la task est une tache de
 * suivi matiere ou d'assemblage:
 * Si suivi matiere, pas de filtre supplementaire;
 * Si assemblage, on ne prend pas les nomenclatures telles que:
 *  - le Product associe (level 0) a un tracingMode = 0, et
 *  - elle possede un Component C de level 1 tel que C.Product.tracingMode = LOT
 *
 * @param integer $taskId
 * @access public
 * @return array
 **/
function getNomenclatureArray($taskId=''){
    require_once('Objects/Task.const.php');
	$mapper = Mapper::singleton('Nomenclature');
    $col = $mapper->loadCollection(array(),
        array('Product.BaseReference'=>SORT_ASC), array('Product', 'Version'));
    $cnt = $col->getCount();
    $array = array(0 => _('None'));
    for($i = 0; $i < $cnt; $i++){
    	$nom = $col->getItem($i);
    	if ($taskId == TASK_ASSEMBLY && !$nom->levelTwoCanExist()) {
    	    continue;
    	}
        $pdt = $nom->getProduct();
        if ($pdt instanceof Product) {
            $array[$nom->getId()] = sprintf(
                '%s: %s (version %s)',
                $pdt->getBaseReference(),
                $pdt->getName(),
                $nom->getVersion()
            );
        }
    }
    return $array;
}

/**
 * getComponentOptions()
 * Retourne une chaine contenant des options de select de components en
 * fonction du tableau d'id nomenclature passé en paramètre.
 *
 * @return string
 **/
function getComponentOptions($ids = array(), $selected = '', $multiple=1) {
    $selected = explode(',', $selected);
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    if (empty($ids)) {
        return array();
    }
	$mapper = Mapper::singleton('Component');
    $col = $mapper->loadCollection(array('Nomenclature'=>$ids),
        array('Product.Name'=>SORT_ASC));
    $cnt = $col->getCount();
    $options = array("<option value=\"0\">"._('None')."</option>\n");
    for($i = 0; $i < $cnt; $i++){
    	$cpn = $col->getItem($i);
        $pdt = $cpn->getProduct();
        $options[] =  sprintf(
            "<option value=\"%s\"%s>%s: %s</option>\n",
            $cpn->getId(),
            in_array($cpn->getId(), $selected)?' selected':'',
            $pdt->getBaseReference(),
            $pdt->getName()
        );
    }
	return sprintf(
        "<select name=\"Component\"%s style=\"width:100%%;\">\n%s\n</select>",
        $multiple?' multiple size="6"':'',
        implode("\n", $options)
    );

}

?>