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
 * @version   SVN: $Id: AssemblyTools.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * Generates the EAN13 code for the product.
 * This method assumes that EAN13Prefix and EAN13Sequence are correctly 
 * configured in the preferences.
 *
 * @return void
 * @throws Exception
 */
function generateEAN13Code()
{
    // get the 7 digits prefix and sequence number
    $prefix = Preferences::get('EAN13Prefix', '');
    $seq    = Preferences::get('EAN13Sequence', 0);

    // the prefix must be a 6 digits number
    if (strlen($prefix) !== 6) {
        throw new Exception(_('The EAN13 prefix must be a 6 digits number'));
    }

    // if the sequence number exceeds 9999, we have a problem
    if ($seq > 999999) {
        throw new Exception(_('No more available EAN13 barcodes, you must configure a new EAN13 prefix and reset the EAN13 sequence number'));
    }
    
    // compute 12 digits code
    $digits = sprintf('%s%06d', $prefix, $seq);

    // calculate checksum digit
    // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
    $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
    // 2. Multiply this result by 3.
    $even_sum_three = $even_sum * 3;
    // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
    $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
    // 4. Sum the results of steps 2 and 3.
    $total_sum = $even_sum_three + $odd_sum;
    // 5. The check character is the smallest number which, when added to the result in step 4, produces a multiple of 10.
    $next_ten = (ceil($total_sum/10))*10;
    $check_digit = $next_ten - $total_sum;

    // ok now we must increment the EAN13 sequence
    Preferences::set('EAN13Sequence', $seq+1);
    Preferences::save();

    return $digits . $check_digit;
}
