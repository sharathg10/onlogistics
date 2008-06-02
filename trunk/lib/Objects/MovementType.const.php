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

define('SORTIE_NORMALE', 1);//--------------->SORTIE_NORMALE
define('ENTREE_NORMALE', 2);//--------------->ENTREE_NORMALE
define('SORTIE_CASSE', 3);  //--------------->SORTIE_CASSE
define('ENTREE_SURPLUS', 4);//--------------->ENTREE_SURPLUS
define('SORTIE_INVENT', 5);  //--------------->SORTIE_INVENTAIRE
define('ENTREE_INVENT', 6);  //--------------->ENTREE_INVENTAIRE
////define('ENTREE_REFUS_CLIENT', 7);  //--------------->ENTREE_REFUS_CLIENT  // DEPRECATED!!!!!!!
define('SORTIE_DEPLACEMT', 8);  //--------------->SORTIE_DEPLACEMT
define('ENTREE_DEPLACEMT', 9);  //--------------->ENTREE_DEPLACEMT
define('SORTIE_ECHANTILLON', 10);  //--------------->SORTIE_ECHANTILLON
define('ENTREE_ECHANTILLON', 11);  //--------------->ENTREE_ECHANTILLON
define('SORTIE_MODIF_COND', 12);  //--------------->SORTIE_MODIF_CONDITIONNEMENT
define('ENTREE_MODIF_COND', 13);  //--------------->ENTREE_MODIF_CONDITIONNEMENT
define('ENTREE_INTERNE', 14);     //--------------->ENTREE_INTERNE
define('SORTIE_INTERNE', 15);     //--------------->SORTIE_INTERNE

define('SORTIE', 1);  //--------------->SORTIE QUELCONQUE
define('ENTREE', 0);  //--------------->ENTREE QUELCONQUE

$MovementTypeArray = array(
    SORTIE_NORMALE => _('Normal issue'), 
    ENTREE_NORMALE => _('Normal entry'),
    SORTIE_CASSE => _('Issue for damage'),
    ENTREE_SURPLUS => _('Entry for surplus'),
    SORTIE_INVENT => _('Issue for inventory'),
    ENTREE_INVENT => _('Entry for inventory'),
    SORTIE_DEPLACEMT => _('Issue for move'), 
    ENTREE_DEPLACEMT => _('Entry for move'),
    SORTIE_ECHANTILLON => _('Issue for sample'),
    ENTREE_ECHANTILLON => _('Entry for sample'),
    SORTIE_MODIF_COND => _('Issue for packaging modification'),
    ENTREE_MODIF_COND => _('Entry for packaging modification'),
    ENTREE_INTERNE => _('Internal entry'),
    SORTIE_INTERNE => _('Internal issue')

);

/**
 * Mappe les constantes de l'appli zaurus avec celles définies ici
 * 
 * @access public
 * @param integer type le type renvoyée par le zaurus
 * @return integer le type traduit
 **/
function getMovementTypeForZaurus($type){
    $translationTable = array(
        1  => SORTIE_CASSE, 
        2  => ENTREE_SURPLUS, 
        3  => SORTIE_INVENT, 
        4  => ENTREE_INVENT, 
        5  => SORTIE_DEPLACEMT, 
        6  => ENTREE_DEPLACEMT, 
        7  => SORTIE_ECHANTILLON, 
        8  => ENTREE_ECHANTILLON, 
        9  => SORTIE_MODIF_COND, 
        10 => ENTREE_MODIF_COND
    );
    $type = intval($type);
    if (array_key_exists($type, $translationTable)) {
        return $translationTable[$type];
    }
    return 0;
}

?>
