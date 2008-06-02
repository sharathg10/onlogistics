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

class AbstractInstant extends Object {
    
    // Constructeur {{{

    /**
     * AbstractInstant::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'AbstractInstant';
    }

    // }}}
    // getObjectLabel() {{{

    /**
     * Retourne le "label" de la classe.
     *
     * @static
     * @access public
     * @return string
     */
    public static function getObjectLabel() {
        return _('None');
    }

    // }}}
    // getProperties() {{{

    /**
     * Retourne le tableau des propriétés.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getProperties() {
        $return = array();
        return $return;
    }

    // }}}
    // getLinks() {{{

    /**
     * Retourne le tableau des entités liées.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getLinks() {
        $return = array(
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'DepartureInstant',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask_1'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ArrivalInstant',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'DepartureInstant',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask_1'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'ArrivalInstant',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ));
        return $return;
    }

    // }}}
    // getUniqueProperties() {{{

    /**
     * Retourne le tableau des propriétés qui ne peuvent prendre la même valeur
     * pour 2 occurrences.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getUniqueProperties() {
        $return = array();
        return $return;
    }

    // }}}
    // getEmptyForDeleteProperties() {{{

    /**
     * Retourne le tableau des propriétés doivent être "vides" (0 ou '') pour
     * qu'une occurrence puisse être supprimée en base de données.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getEmptyForDeleteProperties() {
        $return = array();
        return $return;
    }

    // }}}
    // getFeatures() {{{

    /**
     * Retourne le tableau des "fonctionalités" pour l'objet en cours.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getFeatures() {
        return array();
    }

    // }}}
    // getMapping() {{{

    /**
     * Retourne le mapping nécessaires aux composants génériques.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getMapping() {
        $return = array();
        return $return;
    }

    // }}}
    // useInheritance() {{{

    /**
     * Détermine si l'entité est une entité qui utilise l'héritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entités
     * dans le mapper car classes filles et parentes sont mappées dans la même
     * table.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function useInheritance() {
        return true;
    }

    // }}}
    // AbstractInstant::mutate() {{{

    /**
     * "Mutation" d'un objet parent en classe fille et vice-versa.
     * Cela permet par exemple dans un formulaire de modifier la classe d'un
     * objet via un select.
     *
     * @access public
     * @param string type le type de l'objet vers lequel 'muter'
     * @return object
     **/
    public function mutate($type){
        // on instancie le bon objet
        require_once('Objects/' . $type . '.php');
        $mutant = new $type();
        if(!($mutant instanceof AbstractInstant)) {
            trigger_error('Invalid classname provided.', E_USER_ERROR);
        }
        // propriétés fixes
        $mutant->hasBeenInitialized = $this->hasBeenInitialized;
        $mutant->readonly = $this->readonly;
        $mutant->setId($this->getId());
        // propriétés simples
        $properties = $this->getProperties();
        foreach($properties as $property=>$type){
            $getter = 'get' . $property;
            $setter = 'set' . $property;
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        // relations
        $links = $this->getLinks();
        foreach($links as $property=>$data){
            $getter = 'get' . $property . 'Collection';
            $setter = 'set' . $property . 'Collection';
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        return $mutant;
    }

    // }}}
}

?>