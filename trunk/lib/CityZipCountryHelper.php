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

class CityZipCountryHelper {
	/**
     * Constructor
     * @access public
     */
	public function __construct($smarty, $zip, $country, $city = ''){
		$this->smarty = $smarty;
		$this->_zip = trim($zip);
		$this->_country = trim($country);
        if (!empty($city)) {
            $this->_city = $this->__handleSaintWord(trim($city));
        }
		$this->_result = $this->__findResults();
	}

	/**
	 * Le nom de la ville
	 *
	 * @access private
	 * @var string $_city
	 **/
	private $_city = '';

	/**
	 * Le code postal
	 *
	 * @access private
	 * @var string $_zip
	 **/
	private $_zip = '';

	/**
	 * Le nom du pays
	 *
	 * @access private
	 * @var string $_country
	 **/
	private $_country = '';

	/**
	 * Le résultat de la recherche
	 *
	 * @access private
	 * @var string $_result Collection or Exception
	 **/
	private $_result = false;

	/**
	 * Le template dans lequel afficher les resultats
	 *
	 * @access public
	 * @var object $smarty instance of Template
	 **/
	public $smarty = false;

	/**
	 * CountryCityZipHelper::findExactMatch()
	 * essaie de trouver le country city correspondant à la saisie de
	 * l'utilisateur, si il y a plusieurs résultats possibles, la fonction
	 * retourne false, si aucun resultat n'est possible: elle retourne une
	 * Exception.
	 *
	 * @access public
	 * @return mixed CountryCity, false or Exception
	 **/
	public function findExactMatch(){
		if ($this->_result instanceof Collection && $this->_result->getCount() == 1) {
			return $this->_result->getItem(0);
		}
		if ($this->_result instanceof Exception) {
		    return $this->_result;
		}
		return false;
	}

	/**
	 * CountryCityZipHelper::findMatches()
	 * retourne tous les résultats possibles.
	 *
	 * @access public
	 * @return mixed CountryCityCollection, false or Exception
	 **/
	public function findMatches(){
		if ($this->_result instanceof Collection && $this->_result->getCount() > 0) {
			return $this->_result;
		}
		if ($this->_result instanceof Exception) {
		    return $this->_result;
		}
		return false;
	}

	/**
	 * CountryCityZipHelper::showSuggestions()
	 * Affiche les suggestions possibles dans un popup
	 *
	 * @param collection $suggestions: la collection retournée par __findResults()
	 * @param string $formAction: action de form
	 * @param string $retURL: url de retour
	 * @access public
	 * @return void
	 **/
	public function showSuggestions($suggestions, $formAction, $retURL){
		if ($suggestions instanceof Collection) {
			$suggestionList = array();
			for($i = 0; $i < $suggestions->getCount(); $i++){
				$suggestion = $suggestions->getItem($i);
				$suggestionList[] = $suggestion;
				unset($suggestion);
			} // for
			$this->smarty->assign('SuggestionList', $suggestionList);
			$this->smarty->assign('FormAction', $formAction);
			$this->smarty->assign('retURL', $retURL);
			$tpl = 'CityZipCountryHelper/CityZipCountryHelper.html';
			$content = $this->smarty->fetch($tpl);
			$title = _('Please select a result.');
			$tpl = isset($_SESSION['asPopup'])? BASE_POPUP_TEMPLATE:BASE_TEMPLATE;
            Template::page($title, $content, array(), array(), $tpl);
			exit;
		}
		return false;
	}

	/**
	 * CountryCityZipHelper::__handleSaintWord()
	 * Transforme les nom commençant par "saint ", "saint-", "sainte ", "sainte-"
	 * par "st " ou "ste " de manière insensible à la casse.
	 * Exemple: "Saint glinglin" deviendra "st glinglin"
	 *
	 * @access private
	 * @param string $name le nom à transformer
	 * @return string le nom transformé
	 **/
	private function __handleSaintWord($name){
		return preg_replace("/^(saint)(e?)(-| )(.*)$/ei",
            "strtolower('st\\2 \\4')", $name);
	}


	/**
	 * CountryCityZipHelper::guessCountryCity()
	 *
	 * @access private
	 * @return mixed collection or Exception
	 **/
	private function __findResults(){
        $filters = array(
            $this->__buildCityZipCountryFilterExactMatch(),
		    $this->__buildCityZipCountryFilter(),
		    $this->__buildCityZipFilter()
        );
        if (!empty($this->_city)) {
            $filters[] = $this->__buildCityFilter();
        }

		$countryCityMapper = Mapper::singleton('CountryCity');
        foreach($filters as $filter){
    		$col = $countryCityMapper->loadCollection($filter);
    		if ($col instanceof Collection && $col->getCount() > 0) {
    		    return $col;
    		}
        }
		return new Exception(
            _('No match found with these city name/zip code/country name.'));
	}


	/**
	 * CountryCityZipHelper::__buildCityFilter()
	 * Contruit un filtre pour chercher les résultats par ville
	 *
	 * @access private
	 * @return FilterComponent $component
	 **/
	private function __buildCityFilter(){
		$component = new FilterComponent();
		$cityRule = new FilterRule(
			'CityName.Name',
			FilterRule::OPERATOR_LIKE,
			sprintf("%s%%", $this->_city)
		);
		$component->setItem($cityRule);
		return $component;
	}

	/**
	 * CountryCityZipHelper::__buildCityZipFilter()
	 * Contruit un filtre pour chercher les résultats code postal
	 *
	 * @access private
	 * @return FilterComponent $component
	 **/
	private function __buildCityZipFilter(){
		$component = $this->__buildCityFilter();
		$zipRule = new FilterRule(
			'Zip.Code',
			FilterRule::OPERATOR_EQUALS,
			$this->_zip
		);
		$component->setItem($zipRule);
		$component->operator = FilterComponent::OPERATOR_AND;
		return $component;
	}
	/**
	 * CountryCityZipHelper::__buildCityZipCountryFilter()
	 * Contruit un filtre pour chercher les résultats par pays et code postal
	 *
	 * @access private
	 * @return FilterComponent $component
	 **/
	private function __buildCityZipCountryFilter(){
		$component = $this->__buildCityFilter();
		$zipRule = new FilterRule(
			'Zip.Code',
			FilterRule::OPERATOR_LIKE,
			sprintf("%s%%", $this->_zip)
		);
		$component->setItem($zipRule);
		$countryRule = new FilterRule(
			'Country.Id',
			FilterRule::OPERATOR_LIKE,
			sprintf("%%%s%%", $this->_country)
		);
		$component->setItem($countryRule);
		$component->operator = FilterComponent::OPERATOR_AND;
		return $component;
	}
	/**
	 * CountryCityZipHelper::__buildCityZipCountryFilterExactMatch()
	 * Contruit un filtre pour chercher les résultats par ville, pays
	 * et code postal
	 *
	 * @access private
	 * @return FilterComponent $component
	 **/
	private function __buildCityZipCountryFilterExactMatch(){
		$component = new FilterComponent();
		$cityRule = new FilterRule(
			'CityName.Name',
			FilterRule::OPERATOR_EQUALS,
			$this->_city
		);
		$component->setItem($cityRule);
		$zipRule = new FilterRule(
			'Zip.Code',
			FilterRule::OPERATOR_EQUALS,
			$this->_zip
		);
		$component->setItem($zipRule);
		$countryRule = new FilterRule(
			'Country.Id',
			FilterRule::OPERATOR_EQUALS,
			$this->_country
		);
		$component->setItem($countryRule);
		$component->operator = FilterComponent::OPERATOR_AND;
		return $component;
	}

}

?>