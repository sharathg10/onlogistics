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

require_once('View/AbstractView.php');
require_once('Objects/Site.php');

/**
 * Concrete view of an actor
 */
class ActorView extends View {
    /**
     * Constructor
     * 
     * @access protected 
     */
    function ActorView($id)
    {
        $this->View('Actor', $id);
        $this->site = $this->_entity->getMainSite();
        $this->siteCollection = $this->_entity->getSiteCollection();

        $this->deliverySite = $this->site;
        $this->invoiceSite = $this->site;

        if ($this->siteCollection instanceof Collection) {
            for($i = 0; $i < $this->siteCollection->getCount(); $i++) {
                $site = $this->siteCollection->getItem($i);
                if ($site->getType() == Site::SITE_TYPE_LIVRAISON) {
                    $this->deliverySite = $site;
                } 
                if ($site->getType() == Site::SITE_TYPE_FACTURATION) {
                    $this->invoiceSite = $site;
                } 
                if ($site->getType() == Site::SITE_TYPE_FACTURATION_LIVRAISON) {
                    $this->deliverySite = $site;
                    $this->invoiceSite = $site;
                } 
                unset($site);
            } 
        } 
    } 

    /**
     * ActorView::getId()
     * 
     * @access public 
     * @return integer
     */
    function getId()
    {
        return $this->_entity->getId();
    } 

    /**
     * ActorView::getName()
     * 
     * @access public 
     * @return string
     */
    function getName()
    {
        return $this->_entity->getName();
    } 

    /**
     * ActorView::getLogo()
     * 
     * @access public 
     * @return string
     */
    function getLogo()
    {
        return $this->_entity->getLogo();
    } 


    /**
     * ActorView::getStreetType()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getStreetType($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }        
        $array = $site->getStreetTypeConstArray();
        return isset($array[$site->getStreetType()])?
            $array[$site->getStreetType()]:'';
    } 

    /**
     * ActorView::getStreetNumber()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getStreetNumber($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }        
        return $site->getStreetNumber();
    } 

    /**
     * ActorView::getStreetName()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getStreetName($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }        
        return $site->getStreetName();
    } 

    /**
     * ActorView::getStreetAddons()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getStreetAddons($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }        
        return $site->getStreetAddons();
    } 

    /**
     * ActorView::getCedex()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getCedex($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }        
        return $site->getCedex();
    } 

    /**
     * ActorView::getZipCode()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getZipCode($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }
        $countryCity = $site->getcountryCity();
        if (!($countryCity instanceof CountryCity)) {
            return _('N/A');
        }
        $zip = $countryCity->getZip();
        if (!($zip instanceof Zip)) {
            return _('N/A');
        }
        return $zip->getCode();
    } 

    /**
     * ActorView::getCityName()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getCityName($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }
        $countryCity = $site->getcountryCity();
        if (!($countryCity instanceof CountryCity)) {
            return _('N/A');
        }
        $cityName = $countryCity->getCityName();
        if (!($cityName instanceof CityName)) {
            return _('N/A');
        }
        return $cityName->getName();
    } 

    /**
     * ActorView::getCountryName()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access public 
     * @return string
     */
    function getCountryName($sitetype = false)
    {
        $site = $this->_getSite($sitetype);
        if (!($site instanceof Site)) {
            return _('N/A');
        }        
        $countryCity = $site->getcountryCity();
        if (!($countryCity instanceof CountryCity)) {
            return _('N/A');
        }        
        $country = $countryCity->getCountry();
        if (!($country instanceof Country)) {
            return _('N/A');
        }        
        return $country->getName();
    } 

    /**
     * ActorView::_getSite()
     * 
     * @param string $sitetype (soit 'delivery' soit 'invoice')
     * @access private 
     * @return object Site
     */
    function _getSite($sitetype = false)
    {
        if (strtolower($sitetype) == 'invoice') {
            return $this->invoiceSite;
        } else if (strtolower($sitetype) == 'delivery') {
            return $this->deliverySite;
        } 
        return $this->site;
    }
    
    /**
     * ActorView::getQualityForaddress()
     *
     * @access public
     * @return string
     */
    function getQualityForAddress()
    {
        $this->_entity->getQualityForAddress();
    } 
} 

/**
 * 
 * @access public 
 * @return void 
 */
function ActorViewTest($id)
{
    $actor = new ActorView($id);
    printf("
		getStreetNumber = %s <br>
		getStreetType = %s <br>
		getStreetName = %s <br>
		getStreetAddons = %s <br>
		getCedex = %s <br>
		getZipCode = %s <br>
		getCityName = %s <br>
		getCountryName = %s <br>
	", $actor->getStreetNumber(),
        $actor->getStreetType(),
        $actor->getStreetName(),
        $actor->getStreetAddons(),
        $actor->getCedex(),
        $actor->getZipCode(),
        $actor->getCityName(),
        $actor->getCountryName());
} 

?>
