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

class Catalog extends _Catalog {
    // Constructeur {{{

    /**
     * Catalog::__construct()
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
     */
    public function getProductTypeList()
    {
        $temparray = array();
        $collection = $this->GetProductTypeCollection();
        if ($collection instanceof Collection) {
            for($i = 0; $i < $collection->getCount(); $i++) {
                $pdtType = $collection->getItem($i);
                $temparray[$pdtType->getId()] = $pdtType;
            } // for
        }
        return $temparray;
    }

    /**
     * Retourne un tableau d'objets property triées par index
     *
     * @access public
     * @return array
     */
    public function getCatalogCriteriaList($order=array('Index' => SORT_ASC))
    {
        $temparray = array();
        $collection = $this->getCatalogCriteriaCollection(
                array(), $order);
        if ($collection instanceof Collection) {
            $count = $collection->getCount();
            for($i = 0; $i < $count; $i++) {
                $criteria = $collection->getItem($i);
                $temparray[$criteria->getPropertyID()] = $criteria;
            } // for
        }
        return $temparray;
    }

    /**
     * Ajoute un critère au catalogue
     *
     * @access public
     * @param Property $property l'objet property à ajouter
     * @return void
     */
    public function addCatalogCriteria($criteria)
    {
        $collection = $this->getCatalogCriteriaCollection();
        if ($collection instanceof Collection) {
            $collection->setItem($criteria);
        }
    }

    /**
     * Methode addon pour faciliter la suppression d'un critère
     *
     * @access public
     * @param integer $propertyId : l'id de l'objet
     * @return boolean
     */
    public function removeCatalogCriteria($criteriaID)
    {
        if (false == $criteriaID) {
            return false;
        }
        // on charge la collection et le tableau d'ids
        $collection = $this->getCatalogCriteriaCollection();
        if ($collection instanceof Collection) {
            $ids = $collection->getItemIds();
            foreach($ids as $key => $id) {
                if ($criteriaID == $id) {
                    $collection->removeItem($key);
                    break;
                }
            }
        }
        return true;
    }

    /**
     * Retourne un tableau de tableaux représentant la structure du catalogue.
     *
     * Example de tableau retourné:
     * Array
     * (
     *     [0] => Array
     *         (
     *             [Type] => 1
     *             [DisplayName] => Référence
     *             [Accessor] => getBaseReference:0
     *          )
     *
     *     [1] => Array
     *         (
     *             [Type] => 1
     *             [DisplayName] => Famille
     *             [Accessor] => getFamily:0
     *          )
     *     ...
     * )
     * Type correspond aux constantes définies dans Object.php, DisplayName
     * représente le nom de colonne à afficher et Accessor est la méthode
     * (addon ou pas) de la classe Product correspondante ou encore une macro
     * qu'on peut passer à Tools::getValueFromMacro($product, ...), le booléen après
     * les deux points détermine si oui ou non on doit passer en argument le
     * customer.
     *
     * @access public
     * @param integer $customerID: l'id du client
     * @return array
     */
    public function getStructureArray($customerID=0) {
        // les colonnes pour lesquelles il y faut un accesseur particulier
        $custom = array(
            'SellUnitType'=>'%SellUnitQuantity% %SellUnitType.ShortName%:0',
            'Price'=>'getUnitHTForCustomerPriceWithDiscount:1',
            'Category'=>'%Category.Name%:0',
            'Supplier'=>'%MainSupplier.Name%:0',
            'CustomerReference'=>'getReferenceByActor:' . $customerID
        );
        $mapper = Mapper::singleton('Actor');
        // on parcours les critères du catalogue
        $ctList = $this->getCatalogCriteriaList();
        foreach($ctList as $i=>$criteria) {
            $criteria = $ctList[$i];
            if (!$criteria->getDisplayable()) {
                continue;
            }
            $property = $criteria->getProperty();
            $name     = $property->getName();
            $accessor = isset($custom[$name])?$custom[$name]:'get'.$name.':0';
            $array = array(
                'Type'=>$property->getType(),
                'DisplayName'=>$criteria->getDisplayName(),
                'Accessor'=>$accessor
            );
            // XXX pour la colonne prix il faut ajouter la devise du client
            if ($name == 'Price') {
                $cust = $mapper->load(array('Id'=>$customerID));
                $curStr = '';
                if ($cust instanceof Actor) {
                    $cur = $cust->getCurrency();
                    $curStr = $cur instanceof Currency?' '.$cur->getShortName():'';
                }
                $array['DisplayName'] = $criteria->getDisplayName() . $curStr;
            }
            $return[] = $array;
        }
        return $return;
    }

    /**
     * Construit le grid avec les propriétés dynamiques
     *
     * @access public
     * @return Grid l'objet grille construit
     */
    public function buildGrid($customParams = array())
    {
        require_once('Property.inc.php');
        $grid = new Grid();
        $itemPerPage = $this->getItemPerPage()?$this->getItemPerPage():30;
        $grid->itemPerPage = $itemPerPage;
        /**
         * Colonnes
         */
        $criteriaList = $this->getCatalogCriteriaList();

        foreach($criteriaList as $criteria) {
            $property = $criteria->getProperty();
            if (!($property instanceof Property) || !$criteria->getDisplayable()) {
                continue;
            }
            $name = $property->getName();
            $type = $property->getType();
            $ctype = 'FieldMapper';
            $cname = $criteria->getDisplayName();
            $array = array();
            $array['Macro'] = '%' . $name . '%';
            // On recherche dans le tableau des affichages customisés si on doit
            // personaliser l'affichage pour le critère en cours
            if (isset($customParams[$name])) {
                if (isset($customParams[$name]['ColumnType'])) {
                    $ctype = $customParams[$name]['ColumnType'];
                }
                if (isset($customParams[$name]['ColumnName'])) {
                    $cname = $customParams[$name]['ColumnName'];
                }
                if (isset($customParams[$name]['ColumnMacro'])) {
                    $array['Macro'] = $customParams[$name]['ColumnMacro'];
                }
                if (isset($customParams[$name]['TranslationMap'])) {
                    $array['TranslationMap'] = $customParams[$name]['TranslationMap'];
                }
                if (isset($customParams[$name]['Sortable'])) {
                    $array['Sortable'] = $customParams[$name]['Sortable'];
                }
                if (isset($customParams[$name]['SortField'])) {
                    $array['SortField'] = $customParams[$name]['SortField'];
                }
                if (isset($customParams[$name]['actor'])) {
                    $array['actor'] = $customParams[$name]['actor'];
                }
                if (isset($customParams[$name]['Func'])) {
                    $array['Func'] = $customParams[$name]['Func'];
                }
                if (isset($customParams[$name]['Args'])) {
                    $array['Args'] = $customParams[$name]['Args'];
                }
            } else {
                if ($type == Property::BOOL_TYPE) {
                    $ctype = 'FieldMapperWithTranslation';
                    $array['TranslationMap'] = array(1=>_('yes'), 0=>_('no'));
                }
                if ($property->isDynamic()) {
                    // le tri sur les propriétés dynamiques ne marche pas...
                    $array['Sortable'] = false;
                }
            }
            $grid->NewColumn($ctype, $cname, $array);
        }
        return $grid;
    }

    /**
     * Construit les élements du formulaire de recherche passé en paramètre
     * en fonction de l'attribut seachable des property du catalogue.
     *
     * @param $searchForm l'objet SearchForm, modifié par référence
     * @access public
     * @return void
     **/
    public function buildSearchForm($searchForm, $excludes = array(), $customParams=array()){
        require_once('Property.inc.php');
        require_once('Objects/CatalogCriteria.const.php');
        require_once('Objects/Product.php');
        if (!($searchForm instanceof SearchForm)) {
            return false;
        }
        $criteriaList = $this->getCatalogCriteriaList(array('SearchIndex' => SORT_ASC));
        $props = Product::getPropertiesByContext();

        if (count($criteriaList) == 0) {
            return false;
        }
        foreach($criteriaList as $criteria) {
            $property = $criteria->getProperty();
            if (!($property instanceof Property)) {
                continue;
            }
            $name  = $property->getName();
            if (!$criteria->getSearchable() || in_array($name, $excludes)) {
                continue;
            }
            $type  = $property->getType();
            $ptype = getPropertyTypeColumn($type);
            $displayName = $criteria->getDisplayName();
            // par défaut
            $params = array();
            $searchOptions = array();
            $searchOptions['Operator'] = 'Like';
            $etype = 'text';

            if (isset($customParams[$name])) {
                if (isset($customParams[$name]['Type'])) {
                    $etype = $customParams[$name]['Type'];
                }
                if (isset($customParams[$name]['Name'])) {
                    $name = $customParams[$name]['Name'];
                }
                if (isset($customParams[$name]['DisplayName'])) {
                    $displayName = $customParams[$name]['DisplayName'];
                }
                if (isset($customParams[$name]['Params'])) {
                    $params = $customParams[$name]['Params'];
                }
                if (isset($customParams[$name]['SearchOptions'])) {
                    $searchOptions = $customParams[$name]['SearchOptions'];
                }
            } else {
                switch($type){
                    case Property::BOOL_TYPE:
                        $params[] = array(
                            '##' => _('Any'),
                            1    => _('yes'),
                            0    => _('no'));
                        $params[] = 'multiple size="3"';
                        $etype = 'select';
                        break;
                    case Property::OBJECT_TYPE:
                        $entityName = isset($props[$name])?$props[$name]:$name;
                        $selectArray = SearchTools::CreateArrayIDFromCollection(
                            $entityName,
                            array(),
                            _('Select one or more items')
                        );
                        $params[] = $selectArray;
                        $params[] = 'multiple size="5"';
                        $etype = 'select';
                        break;
                    /* case Property::DATE_TYPE: TODO */
                    default:
                        $etype = 'text';
                        $searchOptions['Operator'] = 'Like';
                    } // switch
                if ($property->isDynamic()) {
                    $searchOptions['PropertyType'] = $ptype;
                    $searchForm->addDynamicElement($etype, $name, $displayName,
                        $params, $searchOptions);
                    continue;
                }
            }
            $searchForm->addElement($etype, $name, $displayName, $params,
                    $searchOptions);
        }
        return $searchForm;
    }

}

?>
