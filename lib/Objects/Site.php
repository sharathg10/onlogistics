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

class Site extends _Site {
    // Constructeur {{{

    /**
     * Site::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Site::isGeneric() {{{

    /**
     * this is an alias
     */
    function isGeneric()
    {
        return $this->GetGeneric();
    }

    // }}}
    // Site::getChildren() {{{

    /**
     * this is an alias
     */
    function getChildren()
    {
        return $this->getSiteCollection();
    }

    // }}}
    // Site::hasChildren() {{{

    /**
     * this is an alias
     */
    function hasChildren()
    {
        $col = $this->getSiteCollection();
        return ($col > 0);
    }

    // }}}
    // Site::getCityName() {{{

    function getCityName()
    {
        $CountryCity = $this->GetCountryCity();
        if (!($CountryCity instanceof CountryCity)) {
            return false;
        }
        return $CountryCity->GetCityName();
    }

    // }}}
    // Site::getCountry() {{{

    function getCountry()
    {
        $CountryCity = $this->GetCountryCity();
        if (!($CountryCity instanceof CountryCity)) {
            return false;
        }
        return $CountryCity->GetCountry();
    }

    // }}}
    // Site::getCityNameId() {{{

    function GetCityNameId()
    {
        $CountryCity = $this->GetCountryCity();
        if (!($CountryCity instanceof CountryCity)) {
            return false;
        }
        return $CountryCity->GetCityNameID();
    }

    // }}}
    // Site::getAddressInfos() {{{

    /**
     * Recupere ds un tableau les infos de l'adresse d'un site
     *
     */
    function getAddressInfos() {
        $array = $this->getStreetTypeConstArray();
        $streettype = isset($array[$this->getStreetType()])?
            $array[$this->getStreetType()]:'';
        $SiteAdressInfos = array(
            "StreetNumber" => $this->getStreetNumber(),
            "StreetType" => $streettype,
            "StreetName" => $this->getStreetName(),
            "StreetAddons" => $this->getStreetAddons(),
            "Zip" => Tools::getValueFromMacro($this, "%CountryCity.Zip.Code%"),
            "CityName" => Tools::getValueFromMacro($this, "%CountryCity.CityName.Name%"),
            "Cedex" => $this->getCedex(),
            "Country" => Tools::getValueFromMacro($this, "%CountryCity.Country.Name%"));
        return $SiteAdressInfos;
    }

    // }}}
    // Site::getFormatAddressInfos() {{{

    /**
     * Recupere les infos de l'adresse d'un site formatees avec un saut de ligne:
     * <br /> si HTML
     * \n si pdf
     * @param $saut string: type de saut de ligne
     * @return string
     */
    function getFormatAddressInfos($saut='<br>') {
        $SiteAdressInfos = $this->getAddressInfos();
        $FormatedAdress = '';
        if (!empty($SiteAdressInfos['StreetNumber'])) {
             $FormatedAdress .= $SiteAdressInfos['StreetNumber']. ' ';
        }
        if (!empty($SiteAdressInfos['StreetType'])) {
            $FormatedAdress .=  $SiteAdressInfos['StreetType'] . ' ';
        }
        if (!empty($SiteAdressInfos['StreetName'])) {
            $FormatedAdress .=  $SiteAdressInfos['StreetName'] . $saut;
        }
        if (!empty($SiteAdressInfos['StreetAddons'])) {
            $FormatedAdress .=  $SiteAdressInfos['StreetAddons'] . $saut;
        }
        $FormatedAdress .= $SiteAdressInfos['Zip'] . ' ' . $SiteAdressInfos['CityName'] . ' ';
        $FormatedAdress .= $SiteAdressInfos['Cedex'] . $saut . $SiteAdressInfos['Country'] . ' ';
        // au cas ou des donnees soient vides en bado, evite des sauts de ligne
        return str_replace($saut.$saut, $saut, $FormatedAdress);
    }

    // }}}
    // Site::addContact() {{{

    /**
     * Methode addon pour faciliter l'ajout d'un contact à un site
     *
     * @access public
     * @param Contact $contact: l'objet contact que l'on désire ajouter au site
     * @return boolean
     **/
    function addContact($contact){
        if (!($contact instanceof Contact)) {
            return false;
        }
        // on charge la collection et on la passe en mode "sans replicats"
        $ctcCollection = $this->GetContactCollection();
        $ctcCollection->acceptDuplicate = false;
        // on y ajoute notre contact
        $ctcCollection->setItem($contact);
        return true;
    }

    // }}}
    // Site::removeContact() {{{

    /**
     * Methode addon pour faciliter la suppression d'un contact pour un site
     *
     * @access public
     * @param Contact $contact: l'objet contact que l'on désire ajouter au site
     * @return boolean
     **/
    function removeContact($contactId){
        if (false == $contactId) {
            return false;
        }
        // on charge la collection et le tableau d'ids
        $ctcCollection = $this->GetContactCollection();
        foreach($ctcCollection->getItemIds() as $key=>$id){
            if ($contactId == $id) {
                $ctcCollection->removeItem($key);
                return true;
            }
        }
        return true;
    }

    // }}}
    // Site::delete() {{{

    /**
     * Supprime un site.
     * Pour être supprimable le dite ne doit pas être lié à une commande.
     *
     * @access public
     * @return boolean
     * @throws Exception
     */
    function delete($fake = false) {
        $filter = array(
            SearchTools::newFilterComponent('DestinatorSite', '', 'Equals', $this->getId(), 1),
            SearchTools::newFilterComponent('ExpeditorSite', '', 'Equals', $this->getId(), 1)
        );
        $filter = SearchTools::filterAssembler($filter, 'OR');
        $cmdCol = Object::loadCollection('Command', $filter);
        if($cmdCol->getCount() > 0) {
            throw new Exception(sprintf(
                _('Site "%s" could not be deleted because it is in use in a order.'),
                $this->getName()
            ));
        }
        if ($fake) {
            return true;
        }
        // suppression du planning
        $planning = $this->getPlanning();
        if ($planning instanceof WeeklyPlanning) {
            $planning->delete();
        }
        return parent::delete();
    }

    // }}}
    // Site::onAfterImport() {{{

    /**
     * Fonction appelée après import de données via glao-import.
     * Appelée par le script d'import xmlrpc.
     *
     * @access public
     * @param  array $params un tableau de paramètres optionnel
     * @return boolean
     **/
    function onAfterImport($params = array()) {
        // si le site n'a pas de planning on en crée un vide
        if ($this->getPlanningId() == 0) {
            require_once('Objects/WeeklyPlanning.php');
            $planning = WeeklyPlanning::createEmptyPlanning();
            $this->setPlanning($planning);
            $this->save();
        }
        return true;
    }

    // }}}

}

?>