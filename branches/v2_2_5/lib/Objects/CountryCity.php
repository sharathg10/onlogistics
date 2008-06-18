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

class CountryCity extends _CountryCity {
    // Constructeur {{{

    /**
     * CountryCity::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    /**
     *
     * @access public
     * @return void
     **/
    function toString(){
        $city = $this->getCityName();
        $country = $this->getCountry();
        $zip = $this->getZip();
        $cityname = false!=$city?$city->getName():'';
        $countryname = false!=$country?$country->getName():'';
        $zipcode = false!=$zip?$zip->getCode():'';
        return sprintf("%s %s (%s)", $zipcode, $cityname, $countryname);
    }

    /**
     * Retourne le nom des attributs représentant l'objet, pointés par toString()
     *
     * @static
     * @return array of strings
     * @access public
     */
    public function getToStringAttribute() {
        return array('CityName', 'Country', 'Zip');
    }

    /**
     * Retourne la requête appropriée pour la méthode getCSVData() du
     * serveur rpc.
     *
     * @access public
     * @static
     * @return string
     **/
    function getCSVDataSQL() {
        $sql  = 'SELECT ctc._Id as id, zip._Code as code, ';
        $sql .= 'ctn._Name as name, cty._Name as countryname ';
        $sql .= 'FROM CountryCity ctc, CityName ctn, Zip zip, Country cty ';
        $sql .= 'WHERE ctn._Id=ctc._CityName AND zip._Id=ctc._Zip ';
        $sql .= 'AND cty._Id=ctc._Country';
        return $sql;
    }

    /**
     * Retourne true s'il n'existe déjà une CountryCity
     * avec le meme triplet (Country, CityName.Name, Zip.Code)
     * ou une string s'il existe déjà une ou des CountryCity ayant le meme couple
     * (Country, CityName.Name)
     * ou false sinon
     * @access public
     * @return boolean or string
     **/
    function alreadyExistInCountry($name, $zipCode) {
        $errorMsg = '';
        $filterComponentArray = array(); // Tableau de filtres
        $filterComponentArray[] = SearchTools::NewFilterComponent('Id', '', 'NotEquals', $this->getId(), 1);
        $filterComponentArray[] = SearchTools::NewFilterComponent('Country', '', 'Equals', $this->getCountryId(), 1);
        $filterComponentArray[] = SearchTools::NewFilterComponent('Name', 'CityName.Name', 'Equals', $name, 1);
        $Filter = SearchTools::FilterAssembler($filterComponentArray); //  Création du filtre complet

        $mapper = Mapper::singleton(get_class($this));
        $CountryCityCollection = $mapper->loadCollection($Filter);

        if (!Tools::isEmptyObject($CountryCityCollection)) {
            $count = $CountryCityCollection->getCount();
            for($i = 0; $i < $count; $i++) {
                $CountryCity = $CountryCityCollection->getItem($i);
                // Si le zipCode est aussi identique, on sort
                $code = Tools::getValueFromMacro($CountryCity, '%Zip.Code%');
                if ($zipCode == $code) {
                    return true;
                }
                $errorMsg .= '<li>' . $name . ' / ' . $code . '</li>';
                unset($CountryCity, $code);
            }
        }
        return ($errorMsg == '')?false:$errorMsg;
    }

    /**
     * Sauve aussi les Zip et CityName si necessaire
     * A appeler dans une transaction
     * @param array $datas : donnees a sauver
     * @access public
     * @return void
     **/
    function saveAll($datas) {
        // Si le ZipCode est saisi, et n'existe pas, on cree un Zip
        $ZipMapper = Mapper::singleton('Zip');
        if ($datas['ZipCode'] != '') {
            $Zip = $ZipMapper->load(array('Code' => $datas['ZipCode']));
            if (Tools::isEmptyObject($Zip)) {
                $Zip = Object::load('Zip');
                $Zip->setCode($datas['ZipCode']);
                $Zip->save();
            }
            $this->setZip($Zip);
        }

        // Le CityName
        $CityNameMapper = Mapper::singleton('CityName');
        $CityName = $CityNameMapper->load(
                            array('Name' => $datas['Name'],
                                  'Department' => $datas['Department']));
        if (Tools::isEmptyObject($CityName)) {
            $CityName = Object::load('CityName');
            $CityName->setName($datas['Name']);
            $CityName->setDepartment($datas['Department']);
            $CityName->save();
        }
        $this->setCityName($CityName);

        // Le CountryCity
        $this->save();
    }

}

?>