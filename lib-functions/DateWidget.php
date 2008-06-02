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

function listDays($index, $selected = false, $disabled='')
{
    $widgetDay = "\n<select name=\"WidgetDay$index\" $disabled>";
    for($i = 1; $i < 32; $i++) {
        if (false != $selected) {
            if ($i == $selected) {
                $widgetDay .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%02d</option>", $i, $i);
            } else {
                $widgetDay .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i, $i);
            }
        }else {
            if ($i == date("d", time())) {
                $widgetDay .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%02d</option>", $i, $i);
            } else {
                $widgetDay .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i, $i);
            }
        }
    }
    return $widgetDay . "\n</select>\n";
}

/**
 * Retourne un select menu contenant les mois
 * @param int $ index		l'index du select menu s'il y en a plusieurs
 * @param int $selected l'index de l'item selectionne
 * @param string $disabled
 */

function listMonth($index, $selected = false, $disabled='')
{
    $textMonth = array_values(I18N::getMonthesArray());

    $widgetMonth = "\n<select name=\"WidgetMonth$index\" $disabled>";
    for($i = 1; $i < 13; $i++) {
//echo ("<br> index =".$index."<br>");
//echo ("<br> selected =".$selected."<br>");
        if (false != $selected) {
            if ($i == $selected) {
                $widgetMonth .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%s</option>", $i, $textMonth[$i - 1]);
            } else {
                $widgetMonth .= sprintf("\n\t<option value=\"%02d\">%s</option>", $i, $textMonth[$i - 1]);
            }
        }
		if (false ==$selected) {


		if ($i == date("m", time()))
				{
                $widgetMonth .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%s</option>", $i, $textMonth[$i - 1]);
            	} else {
                $widgetMonth .= sprintf("\n\t<option value=\"%02d\">%s</option>", $i, $textMonth[$i - 1]);
           		 }
        	}
     }
    return $widgetMonth . "\n</select>\n";
}

/**
 * Retourne un select menu contenant les années
 * @todo "feature" Prendre l'année en cours et faire une boucle sur 5 ans
 * @param int $ index		l'index du select menu s'il y en a plusieurs
 * @param int $selected l'index de l'item selectionne
 * @param string $disabled
 * @param int $FirstYear	1ere annee a afficher
 */
function listYears($index, $selected=false, $disabled='', $FirstYear=2001)
{
    $widgetYear = "\n<select name=\"WidgetYear$index\" $disabled>";
    for($i = $FirstYear; $i < date("Y")+3; $i++) {
        if (false != $selected) {
			if ($i==$selected) {
            $widgetYear .= sprintf("\n\t<option value=\"%04d\" selected=\"selected\">%04d</option>", $i, $i);
                         } else
						 {
		          		$widgetYear .= sprintf("\n\t<option value=\"%04d\">%04d</option>", $i, $i);
						}

       		 }
		else {
			if( $i == date("Y", time()))
			{
	            $widgetYear .= sprintf("\n\t<option value=\"%04d\" selected=\"selected\">%04d</option>", $i, $i);
        	}
			else 	{
          		$widgetYear .= sprintf("\n\t<option value=\"%04d\">%04d</option>", $i, $i);
					}
				}
    }
    return $widgetYear . "\n</select>\n";
}

/**
 * Retourne un select menu contenant les heurs
 *
 * @param int $ index		l'index du select menu s'il y en a plusieurs
 */
function listHours($index, $defaultvalue = "now", $disabled='')
{
    $widgetHour = "\n<select name=\"WidgetHour$index\" $disabled>";
    for($i = 0; $i < 24; $i++) {
	    if ($defaultvalue != "now") {
            if ($i == $defaultvalue) {
                $widgetHour .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%02d</option>", $i, $i);
            } else {
                $widgetHour .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i, $i);
            }
	   } else if ($defaultvalue == "now") {
            if ($i == date("G", time())) {
                $widgetHour .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%02d</option>", $i, $i);
            } else {
                $widgetHour .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i, $i);
            }
        } else {
            $widgetHour .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i, $i);
        }
    }
    return $widgetHour . "\n</select>\n";
}

/**
 * Retourne un select menu contenant les minutes
 *
 * @param int $index l'index du select menu s'il y en a plusieurs
 */

function listMin($index, $defaultvalue, $disabled='')
{
    $widgetMin = "\n<select name=\"WidgetMin$index\" $disabled>";

    if ($defaultvalue == "now") {
        for($i = 0; $i < 4; $i++) {
            if (($i * 15) > date("i", time())) {
                $widgetMin .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%02d</option>", $i * 15, $i * 15);
            } else {
                $widgetMin .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i * 15, $i * 15);
            }
        }
    } else if ($defaultvalue != "now") {
        for($i = 0; $i < 4; $i++) {
            if (($i * 15) ==$defaultvalue) {
                $widgetMin .= sprintf("\n\t<option value=\"%02d\" selected=\"selected\">%02d</option>", $i * 15, $i * 15);
            } else {
                $widgetMin .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i * 15, $i * 15);
            }
        }
    } else {
        for($i = 0; $i < 4; $i++) {
            $widgetMin .= sprintf("\n\t<option value=\"%02d\">%02d</option>", $i * 15, $i * 15);
        }
    }
    return $widgetMin . "\n</select>\n";
}

/**
 * concatène tous les selects
 * @param int $ index		l'index du select menu s'il y en a plusieurs
 * @param boolean $selected		pas d'item a selectionner ici: false
 * @param int $FirstYear	1ere annee a afficher
 */
function dateWidgetRender($index=1, $selected=false,  $FirstYear=2001)
{
    return datetoDateWidgetRender($index, false, false, false, '', $FirstYear);
}

function hourWidgetRender($index=1, $defaultvalue="now")
{
    return listHours($index, $defaultvalue) . "H" . listMin($index, $defaultvalue);
}

function datetoDateWidgetRender($index=1, $selectedday=false, $selectedmonth=false,
$selectedyear=false, $disabled='', $FirstYear=2001)
{
    $dateFormat = I18N::getHTMLSelectDateFormat();
    $dateData = array(
            'Y' => listYears($index, $selectedyear, $disabled, $FirstYear),
            'm' => listMonth($index, $selectedmonth, $disabled),
            'd' => listDays($index, $selectedday, $disabled));
    return $dateData[$dateFormat[0]] . " " . $dateData[$dateFormat[1]] . " "
            . $dateData[$dateFormat[2]];
}

function hourtoHourWidgetRender($index=1, $selectedhour, $selectedmn, $disabled='')
{
    return listHours($index, $selectedhour, $disabled) . "H"
            . listMin($index, $selectedmn, $disabled);
}

?>