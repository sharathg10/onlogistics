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

require_once('fpdf/fpdf.php');

class FpdfJS extends FPDF {

    var $javascript;
    var $n_js;
    var $T128;                                             // tableau des codes 128
    var $ABCset="";                                        // jeu des caractères éligibles au C128
    var $Aset="";                                          // Set A du jeu des caractères éligibles
    var $Bset="";                                          // Set B du jeu des caractères éligibles
    var $Cset="";                                          // Set C du jeu des caractères éligibles
    var $SetFrom;                                          // Convertisseur source des jeux vers le tableau
    var $SetTo;                                            // Convertisseur destination des jeux vers le tableau
    var $JStart = array("A"=>103, "B"=>104, "C"=>105);     // Caractères de sélection de jeu au début du C128
    var $JSwap = array("A"=>101, "B"=>100, "C"=>99);       // Caractères de changement de jeu
    // FpdfJS::IncludeJS() {{{

    /**
     * IncludeJS
     *
     * @param mixed $script
     * @access public
     * @return void
     */
    public function includeJS($script) {
        $this->javascript=$script;
    }

    // }}}
    // FpdfJS::_putjavascript() {{{

    /**
     * _putjavascript
     *
     * @access protected
     * @return void
     */
    protected function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R ]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }

    // }}}
    // FpdfJS::_putresources() {{{

    /**
     * _putresources
     *
     * @access protected
     * @return void
     */
    function _putresources() {  // protected erreur ds TCPDF (?)
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    // }}}
    // FpdfJS::_putcatalog() {{{

    /**
     * _putcatalog
     *
     * @access protected
     * @return void
     */
    public function _putcatalog() {
        parent::_putcatalog();
        if (isset($this->javascript)) {
            $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }

    // }}}
    // FpdfJS::AutoPrint() {{{

    /**
     * AutoPrint
     *
     * @param mixed $dialog
     * @access public
     * @return void
     */
    public function AutoPrint($dialog=false) {
        //Ajoute du JavaScript pour lancer la boite d'impression ou imprimer immediatement
        $param=($dialog ? 'true' : 'false');
        $script="print($param);"; /// permet de fermer le doct ensuite: marche pas avec le plugin IE ou Moz
        $this->includeJS($script);
    }

    // }}}
    // FpdfJS::Code128() {{{

    function Code128($x,$y,$code,$w,$h) {
        $this->T128[] = array(2, 1, 2, 2, 2, 2);           //0 : [ ]               // composition des caractères
        $this->T128[] = array(2, 2, 2, 1, 2, 2);           //1 : [!]
        $this->T128[] = array(2, 2, 2, 2, 2, 1);           //2 : ["]
        $this->T128[] = array(1, 2, 1, 2, 2, 3);           //3 : [#]
        $this->T128[] = array(1, 2, 1, 3, 2, 2);           //4 : [$]
        $this->T128[] = array(1, 3, 1, 2, 2, 2);           //5 : [%]
        $this->T128[] = array(1, 2, 2, 2, 1, 3);           //6 : [&]
        $this->T128[] = array(1, 2, 2, 3, 1, 2);           //7 : [']
        $this->T128[] = array(1, 3, 2, 2, 1, 2);           //8 : [(]
        $this->T128[] = array(2, 2, 1, 2, 1, 3);           //9 : [)]
        $this->T128[] = array(2, 2, 1, 3, 1, 2);           //10 : [*]
        $this->T128[] = array(2, 3, 1, 2, 1, 2);           //11 : [+]
        $this->T128[] = array(1, 1, 2, 2, 3, 2);           //12 : [,]
        $this->T128[] = array(1, 2, 2, 1, 3, 2);           //13 : [-]
        $this->T128[] = array(1, 2, 2, 2, 3, 1);           //14 : [.]
        $this->T128[] = array(1, 1, 3, 2, 2, 2);           //15 : [/]
        $this->T128[] = array(1, 2, 3, 1, 2, 2);           //16 : [0]
        $this->T128[] = array(1, 2, 3, 2, 2, 1);           //17 : [1]
        $this->T128[] = array(2, 2, 3, 2, 1, 1);           //18 : [2]
        $this->T128[] = array(2, 2, 1, 1, 3, 2);           //19 : [3]
        $this->T128[] = array(2, 2, 1, 2, 3, 1);           //20 : [4]
        $this->T128[] = array(2, 1, 3, 2, 1, 2);           //21 : [5]
        $this->T128[] = array(2, 2, 3, 1, 1, 2);           //22 : [6]
        $this->T128[] = array(3, 1, 2, 1, 3, 1);           //23 : [7]
        $this->T128[] = array(3, 1, 1, 2, 2, 2);           //24 : [8]
        $this->T128[] = array(3, 2, 1, 1, 2, 2);           //25 : [9]
        $this->T128[] = array(3, 2, 1, 2, 2, 1);           //26 : [:]
        $this->T128[] = array(3, 1, 2, 2, 1, 2);           //27 : [;]
        $this->T128[] = array(3, 2, 2, 1, 1, 2);           //28 : [<]
        $this->T128[] = array(3, 2, 2, 2, 1, 1);           //29 : [=]
        $this->T128[] = array(2, 1, 2, 1, 2, 3);           //30 : [>]
        $this->T128[] = array(2, 1, 2, 3, 2, 1);           //31 : [?]
        $this->T128[] = array(2, 3, 2, 1, 2, 1);           //32 : [@]
        $this->T128[] = array(1, 1, 1, 3, 2, 3);           //33 : [A]
        $this->T128[] = array(1, 3, 1, 1, 2, 3);           //34 : [B]
        $this->T128[] = array(1, 3, 1, 3, 2, 1);           //35 : [C]
        $this->T128[] = array(1, 1, 2, 3, 1, 3);           //36 : [D]
        $this->T128[] = array(1, 3, 2, 1, 1, 3);           //37 : [E]
        $this->T128[] = array(1, 3, 2, 3, 1, 1);           //38 : [F]
        $this->T128[] = array(2, 1, 1, 3, 1, 3);           //39 : [G]
        $this->T128[] = array(2, 3, 1, 1, 1, 3);           //40 : [H]
        $this->T128[] = array(2, 3, 1, 3, 1, 1);           //41 : [I]
        $this->T128[] = array(1, 1, 2, 1, 3, 3);           //42 : [J]
        $this->T128[] = array(1, 1, 2, 3, 3, 1);           //43 : [K]
        $this->T128[] = array(1, 3, 2, 1, 3, 1);           //44 : [L]
        $this->T128[] = array(1, 1, 3, 1, 2, 3);           //45 : [M]
        $this->T128[] = array(1, 1, 3, 3, 2, 1);           //46 : [N]
        $this->T128[] = array(1, 3, 3, 1, 2, 1);           //47 : [O]
        $this->T128[] = array(3, 1, 3, 1, 2, 1);           //48 : [P]
        $this->T128[] = array(2, 1, 1, 3, 3, 1);           //49 : [Q]
        $this->T128[] = array(2, 3, 1, 1, 3, 1);           //50 : [R]
        $this->T128[] = array(2, 1, 3, 1, 1, 3);           //51 : [S]
        $this->T128[] = array(2, 1, 3, 3, 1, 1);           //52 : [T]
        $this->T128[] = array(2, 1, 3, 1, 3, 1);           //53 : [U]
        $this->T128[] = array(3, 1, 1, 1, 2, 3);           //54 : [V]
        $this->T128[] = array(3, 1, 1, 3, 2, 1);           //55 : [W]
        $this->T128[] = array(3, 3, 1, 1, 2, 1);           //56 : [X]
        $this->T128[] = array(3, 1, 2, 1, 1, 3);           //57 : [Y]
        $this->T128[] = array(3, 1, 2, 3, 1, 1);           //58 : [Z]
        $this->T128[] = array(3, 3, 2, 1, 1, 1);           //59 : [[]
        $this->T128[] = array(3, 1, 4, 1, 1, 1);           //60 : [\]
        $this->T128[] = array(2, 2, 1, 4, 1, 1);           //61 : []]
        $this->T128[] = array(4, 3, 1, 1, 1, 1);           //62 : [^]
        $this->T128[] = array(1, 1, 1, 2, 2, 4);           //63 : [_]
        $this->T128[] = array(1, 1, 1, 4, 2, 2);           //64 : [`]
        $this->T128[] = array(1, 2, 1, 1, 2, 4);           //65 : [a]
        $this->T128[] = array(1, 2, 1, 4, 2, 1);           //66 : [b]
        $this->T128[] = array(1, 4, 1, 1, 2, 2);           //67 : [c]
        $this->T128[] = array(1, 4, 1, 2, 2, 1);           //68 : [d]
        $this->T128[] = array(1, 1, 2, 2, 1, 4);           //69 : [e]
        $this->T128[] = array(1, 1, 2, 4, 1, 2);           //70 : [f]
        $this->T128[] = array(1, 2, 2, 1, 1, 4);           //71 : [g]
        $this->T128[] = array(1, 2, 2, 4, 1, 1);           //72 : [h]
        $this->T128[] = array(1, 4, 2, 1, 1, 2);           //73 : [i]
        $this->T128[] = array(1, 4, 2, 2, 1, 1);           //74 : [j]
        $this->T128[] = array(2, 4, 1, 2, 1, 1);           //75 : [k]
        $this->T128[] = array(2, 2, 1, 1, 1, 4);           //76 : [l]
        $this->T128[] = array(4, 1, 3, 1, 1, 1);           //77 : [m]
        $this->T128[] = array(2, 4, 1, 1, 1, 2);           //78 : [n]
        $this->T128[] = array(1, 3, 4, 1, 1, 1);           //79 : [o]
        $this->T128[] = array(1, 1, 1, 2, 4, 2);           //80 : [p]
        $this->T128[] = array(1, 2, 1, 1, 4, 2);           //81 : [q]
        $this->T128[] = array(1, 2, 1, 2, 4, 1);           //82 : [r]
        $this->T128[] = array(1, 1, 4, 2, 1, 2);           //83 : [s]
        $this->T128[] = array(1, 2, 4, 1, 1, 2);           //84 : [t]
        $this->T128[] = array(1, 2, 4, 2, 1, 1);           //85 : [u]
        $this->T128[] = array(4, 1, 1, 2, 1, 2);           //86 : [v]
        $this->T128[] = array(4, 2, 1, 1, 1, 2);           //87 : [w]
        $this->T128[] = array(4, 2, 1, 2, 1, 1);           //88 : [x]
        $this->T128[] = array(2, 1, 2, 1, 4, 1);           //89 : [y]
        $this->T128[] = array(2, 1, 4, 1, 2, 1);           //90 : [z]
        $this->T128[] = array(4, 1, 2, 1, 2, 1);           //91 : [{]
        $this->T128[] = array(1, 1, 1, 1, 4, 3);           //92 : [|]
        $this->T128[] = array(1, 1, 1, 3, 4, 1);           //93 : [}]
        $this->T128[] = array(1, 3, 1, 1, 4, 1);           //94 : [~]
        $this->T128[] = array(1, 1, 4, 1, 1, 3);           //95 : [DEL]
        $this->T128[] = array(1, 1, 4, 3, 1, 1);           //96 : [FNC3]
        $this->T128[] = array(4, 1, 1, 1, 1, 3);           //97 : [FNC2]
        $this->T128[] = array(4, 1, 1, 3, 1, 1);           //98 : [SHIFT]
        $this->T128[] = array(1, 1, 3, 1, 4, 1);           //99 : [Cswap]
        $this->T128[] = array(1, 1, 4, 1, 3, 1);           //100 : [Bswap]                
        $this->T128[] = array(3, 1, 1, 1, 4, 1);           //101 : [Aswap]
        $this->T128[] = array(4, 1, 1, 1, 3, 1);           //102 : [FNC1]
        $this->T128[] = array(2, 1, 1, 4, 1, 2);           //103 : [Astart]
        $this->T128[] = array(2, 1, 1, 2, 1, 4);           //104 : [Bstart]
        $this->T128[] = array(2, 1, 1, 2, 3, 2);           //105 : [Cstart]
        $this->T128[] = array(2, 3, 3, 1, 1, 1);           //106 : [STOP]
        $this->T128[] = array(2, 1);                       //107 : [END BAR]
    
        for ($i = 32; $i <= 95; $i++) {                                            // jeux de caractères
            $this->ABCset .= chr($i);
        }
        $this->Aset = $this->ABCset;
        $this->Bset = $this->ABCset;
        for ($i = 0; $i <= 31; $i++) {
            $this->ABCset .= chr($i);
            $this->Aset .= chr($i);
        }
        for ($i = 96; $i <= 126; $i++) {
            $this->ABCset .= chr($i);
            $this->Bset .= chr($i);
        }
        $this->Cset="0123456789";
    
        for ($i=0; $i<96; $i++) {                                                  // convertisseurs des jeux A & B  
            @$this->SetFrom["A"] .= chr($i);
            @$this->SetFrom["B"] .= chr($i + 32);
            @$this->SetTo["A"] .= chr(($i < 32) ? $i+64 : $i-32);
            @$this->SetTo["B"] .= chr($i);
        }
    
        $Aguid="";                                                                      // Création des guides de choix ABC
        $Bguid="";
        $Cguid="";
        for ($i=0; $i < strlen($code); $i++) {
            $needle=substr($code,$i,1);
            $Aguid .= ((strpos($this->Aset,$needle)===FALSE) ? "N" : "O"); 
            $Bguid .= ((strpos($this->Bset,$needle)===FALSE) ? "N" : "O"); 
            $Cguid .= ((strpos($this->Cset,$needle)===FALSE) ? "N" : "O");
        }
    
        $SminiC = "OOOO";
        $IminiC = 4;
    
        $crypt = "";
        while ($code > "") {
                                                                                        // BOUCLE PRINCIPALE DE CODAGE
            $i = strpos($Cguid,$SminiC);                                                // forçage du jeu C, si possible
            if ($i!==FALSE) {
                $Aguid [$i] = "N";
                $Bguid [$i] = "N";
            }
    
            if (substr($Cguid,0,$IminiC) == $SminiC) {                                  // jeu C
                $crypt .= chr(($crypt > "") ? $this->JSwap["C"] : $this->JStart["C"]);  // début Cstart, sinon Cswap
                $made = strpos($Cguid,"N");                                             // étendu du set C
                if ($made === FALSE) $made = strlen($Cguid);
                if (fmod($made,2)==1) $made--;                                          // seulement un nombre pair
                for ($i=0; $i < $made; $i += 2) $crypt .= chr(strval(substr($code,$i,2))); // conversion 2 par 2
                $jeu = "C";
            } else {
                $madeA = strpos($Aguid,"N");                                            // étendu du set A
                if ($madeA === FALSE) $madeA = strlen($Aguid);
                $madeB = strpos($Bguid,"N");                                            // étendu du set B
                if ($madeB === FALSE) $madeB = strlen($Bguid);
                $made = (($madeA < $madeB) ? $madeB : $madeA );                         // étendu traitée
                $jeu = (($madeA < $madeB) ? "B" : "A" );                                // Jeu en cours
                $jeuguid = $jeu . "guid";
    
                $crypt .= chr(($crypt > "") ? $this->JSwap["$jeu"] : $this->JStart["$jeu"]); // début start, sinon swap
    
                $crypt .= strtr(substr($code, 0,$made), $this->SetFrom[$jeu], $this->SetTo[$jeu]); // conversion selon jeu
    
            }
            $code = substr($code,$made);                                           // raccourcir légende et guides de la zone traitée
            $Aguid = substr($Aguid,$made);
            $Bguid = substr($Bguid,$made);
            $Cguid = substr($Cguid,$made);
        }                                                                          // FIN BOUCLE PRINCIPALE
    
        $check=ord($crypt[0]);                                                     // calcul de la somme de contrôle
        for ($i=0; $i<strlen($crypt); $i++) {
            $check += (ord($crypt[$i]) * $i);
        }
        $check %= 103;
    
        $crypt .= chr($check) . chr(106) . chr(107);                               // Chaine Cryptée complète
    
        $i = (strlen($crypt) * 11) - 8;                                            // calcul de la largeur du module
        $modul = $w/$i;
    
        for ($i=0; $i<strlen($crypt); $i++) {                                      // BOUCLE D'IMPRESSION
            $c = $this->T128[ord($crypt[$i])];
            for ($j=0; $j<count($c); $j++) {
                $this->Rect($x,$y,$c[$j]*$modul,$h,"F");
                $x += ($c[$j++]+$c[$j])*$modul;
            }
        }
    }

    // }}}
    // FpdfJS::EAN13() {{{

    /**
     * EAN13
     *
     * Addons pour la gestion des barcodes:
     * http://www.fpdf.org/fr/script/script5.php
     * Exemple:
     * $pdf=new PDF();
     * $pdf->Open();
     * $pdf->AddPage();
     * $pdf->EAN13(80, 40, '123456789012');
     * $pdf->Output();
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $barcode
     * @param mixed $x
     * @param mixed $y
     * @param mixed $barcode
     * @param int $h
     * @param float $w
     * @access public
     * @return void
     */
    public function EAN13($x, $y,$barcode,$h=16,$w=.35) {
        $this->Barcode($x, $y,$barcode,$h,$w,13);
    }

    // }}}
    // FpdfJS::UPC_A() {{{

    /**
     * UPC_A
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $barcode
     * @param int $h
     * @param float $w
     * @access public
     * @return void
     */
    public function UPC_A($x, $y,$barcode,$h=16,$w=.35) {
        $this->Barcode($x, $y,$barcode,$h,$w,12);
    }

    // }}}
    // FpdfJS::GetCheckDigit() {{{

    /**
     * GetCheckDigit
     *
     * @param mixed $barcode
     * @access public
     * @return void
     */
    public function GetCheckDigit($barcode) {
        //Compute the check digit
        $sum=0;
        for($i=1;$i<=11;$i+=2)
            $sum+=3*$barcode{$i};
        for($i=0;$i<=10;$i+=2)
            $sum+=$barcode{$i};
        $r=$sum%10;
        if($r>0)
            $r=10-$r;
        return $r;
    }

    // }}}
    // FpdfJS::TestCheckDigit() {{{

    /**
     * TestCheckDigit
     *
     * @param mixed $barcode
     * @access public
     * @return void
     */
    public function TestCheckDigit($barcode) {
        //Test validity of check digit
        $sum=0;
        for($i=1;$i<=11;$i+=2)
            $sum+=3*$barcode{$i};
        for($i=0;$i<=10;$i+=2)
            $sum+=$barcode{$i};
        return ($sum+$barcode{12})%10==0;
    }

    // }}}
    // FpdfJS::Barcode() {{{

    /**
     * Barcode
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $barcode
     * @param mixed $h
     * @param mixed $w
     * @param mixed $len
     * @access public
     * @return void
     */
    public function Barcode($x, $y,$barcode,$h,$w,$len){
        //Padding
        $barcode=str_pad($barcode, $len-1,'0',STR_PAD_LEFT);
        if($len==12)
            $barcode='0'.$barcode;
        //Add or control the check digit
        if(strlen($barcode)==12)
            $barcode.=$this->GetCheckDigit($barcode);
        elseif(!$this->TestCheckDigit($barcode))
            $this->Error('Incorrect check digit');
        //Convert digits to bars
        $codes=array(
            'A'=>array(
                '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
                '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'
            ),
            'B'=>array(
                '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
                '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'
            ),
            'C'=>array(
                '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
                '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100'
            )
        );
        $parities=array(
            '0'=>array('A','A','A','A','A','A'),
            '1'=>array('A','A','B','A','B','B'),
            '2'=>array('A','A','B','B','A','B'),
            '3'=>array('A','A','B','B','B','A'),
            '4'=>array('A','B','A','A','B','B'),
            '5'=>array('A','B','B','A','A','B'),
            '6'=>array('A','B','B','B','A','A'),
            '7'=>array('A','B','A','B','A','B'),
            '8'=>array('A','B','A','B','B','A'),
            '9'=>array('A','B','B','A','B','A')
        );
        $code='101';
        $p=$parities[$barcode{0}];
        for($i=1;$i<=6;$i++)
            $code.=$codes[$p[$i-1]][$barcode{$i}];
        $code.='01010';
        for($i=7;$i<=12;$i++)
            $code.=$codes['C'][$barcode{$i}];
        $code.='101';
        //Draw bars
        for($i=0;$i<strlen($code);$i++) {
            if($code{$i}=='1')
                $this->Rect($x+$i*$w, $y,$w,$h,'F');
        }
        //Print text uder barcode
        $this->SetFont('Arial','',12);
        $this->Text($x, $y+$h+11/$this->k,substr($barcode,-$len));
    }

    // }}}
    // FpdfJS::Image() {{{

    /**
     * Overrided method, see FPDF::Image().
     *
     * Add an Image to the Pdf.
     *
     * @access public
     * @param string $file Image file path.
     * @param float $x Abscisse of upper left corner.
     * @param float $y Ordinate of upper left corner.
     * @param float $w Image width in page.
     * @param float $h Image height in page.
     * @param string $type Image type [jpg|jpeg|png].
     * @param mixed $link URL or identifiant returned by FPDF::AddLink().
     * @param bool $isMask If true the image is used like mask for another
     * image ($x, $y, $w and $h are ignored).
     * @param int $maskImg Image number return by a previous call to this method
     * method with $isMask at true, the image will be used like mask.
     * @return mixed
     */
    public function image($file,$x,$y,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0) {
        //Put an image on the page
        if(!isset($this->images[$file]))
        {
            //First use of image, get info
            if($type=='') {
                $pos=strrpos($file,'.');
                if(!$pos) {
                    $this->Error(
                        'Image file has no extension and no type was specified: ' .
                        $file);
                }
                $type=substr($file,$pos+1);
            }
            $type=strtolower($type);
            $mqr=get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            if($type=='jpg' || $type=='jpeg') {
                $info=$this->_parsejpg($file);
            } elseif($type=='png') {
                $info=$this->_parsepng($file);
                if($info=='alpha') {
                    return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link);
                }
            } else {
                //Allow for additional formats
                $mtd='_parse'.$type;
                if(!method_exists($this,$mtd)) {
                    $this->Error('Unsupported image type: '.$type);
                }
                $info=$this->$mtd($file);
            }
            set_magic_quotes_runtime($mqr);

            if($isMask){
                if(in_array($file,$this->_tmpFiles)) {
                    $info['cs']='DeviceGray'; //hack necessary as GD can't produce gray scale images
                }
                if($info['cs']!='DeviceGray') {
                    $this->Error('Mask must be a gray scale image');
                }
                if($this->PDFVersion<'1.4') {
                    $this->PDFVersion='1.4';
                }
            }
            $info['i']=count($this->images)+1;
            if($maskImg>0)
                $info['masked'] = $maskImg;
            $this->images[$file]=$info;
        } else {
            $info=$this->images[$file];
        }
        //Automatic width and height calculation if needed
        if($w==0 && $h==0) {
            //Put image at 72 dpi
            $w=$info['w']/$this->k;
            $h=$info['h']/$this->k;
        }
        if($w==0) {
            $w=$h*$info['w']/$info['h'];
        }
        if($h==0) {
            $h=$w*$info['h']/$info['w'];
        }

        if(!$isMask) {
            $this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',
                $w*$this->k,$h*$this->k,$x*$this->k,
                ($this->h-($y+$h))*$this->k,$info['i']));
        }
        if($link) {
            $this->Link($x,$y,$w,$h,$link);
        }

        return $info['i'];
    }

    // }}}
    // FpdfJS::imagePngWithAlpha() {{{

    /**
     * Add a png image width aplha channel.
     *
     * Needs GD 2.x extension
     * (pixel-wise operation, not very fast)
     *
     * @access public
     * @param string $file Image file or string.
     * @param float $x Abscisse of the upper left corner.
     * @param float $y Ordinate of the upper left corner
     * @param float $w Width of image in page.
     * @param float $h Height of image in page
     * @param string $link Link or identifiant returned by FPDF::AddLink().
     * @return void
     */
    public function imagePngWithAlpha($file, $x, $y, $w=0, $h=0, $link='') {
        $tmp_alpha = tempnam('.', 'mska');
        $this->_tmpFiles[] = $tmp_alpha;
        $tmp_plain = tempnam('.', 'mskp');
        $this->_tmpFiles[] = $tmp_plain;

        if(!is_file($file)) {
            $img = @imagecreatefromstring($file);
            if(!$img) {
                return;
            }
            $wpx = imagesx($img);
            $hpx = imagesy($img);
        } else {
            list($wpx, $hpx) = getimagesize($file);
            $img = @imagecreatefrompng($file);
            if(!$img) {
                return;
            }
        }
        $alpha_img = imagecreate($wpx, $hpx);
        if (!$alpha_img)
            return;

        // generate gray scale pallete
        for($c=0;$c<256;$c++) {
            ImageColorAllocate($alpha_img, $c, $c, $c);
        }

        // extract alpha channel
        $xpx=0;
        while ($xpx<$wpx){
            $ypx = 0;
            while ($ypx<$hpx){
                $color_index = imagecolorat($img, $xpx, $ypx);
                $col = imagecolorsforindex($img, $color_index);
                imagesetpixel($alpha_img, $xpx, $ypx, $this->_gamma( (127-$col['alpha'])*255/127) );
                ++$ypx;
            }
            ++$xpx;
        }

        imagepng($alpha_img, $tmp_alpha);
        imagedestroy($alpha_img);

        // extract image without alpha channel
        $plain_img = imagecreatetruecolor ( $wpx, $hpx );
        imagecopy($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx );
        imagepng($plain_img, $tmp_plain);
        imagedestroy($plain_img);

        //first embed mask image (w, h, x, will be ignored)
        $maskImg = $this->Image($tmp_alpha, 0,0,0,0, 'PNG', '', true);

        //embed image, masked with previously embedded mask
        $this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg);
    }

    // }}}
    // FpdfJS::_parsepng() {{{

    /**
     * Override to accept string. (like a blob). And to accept png with alpha
     * channel.
     *
     * @access protected
     * @param string $file file path or string
     * @return array
     */
    public function _parsepng($file){
        if (is_file($file) || substr($file, 0, 7) == 'data://') {
            return parent::_parsepng($file);
        }
        //Check signature
        $f = strval($file);
        $stringChar = 0;  // rang du caractere sur lequel est le pointeur
        if(($tmp = substr($f, 0, 8))!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
            $this->Error('Not a PNG file: '.$file);
        }
        $stringChar += 8;
        //Read header chunk
        $stringChar += 4;
        if(substr($f, $stringChar, 4)!='IHDR') {
            $this->Error('Incorrect PNG file: '.$file);
        }
        $stringChar += 4;

        $a = unpack('Ni',substr($f, $stringChar, 4));
        $w = $a['i'];
        $stringChar += 4;
        $b = unpack('Ni',substr($f, $stringChar, 4));
        $h = $b['i'];
        $stringChar += 4;
        unset($a, $b);

        $bpc=ord(substr($f, $stringChar, 1));
        $stringChar += 1;
        if($bpc>8) {
            $this->Error('16-bit depth not supported: '.$file);
        }
        $ct=ord(substr($f, $stringChar, 1));
        $stringChar += 1;
        if($ct==0) {
            $colspace='DeviceGray';
        } elseif($ct==2) {
            $colspace='DeviceRGB';
        } elseif($ct==3) {
            $colspace='Indexed';
        } else {
            return 'alpha';
        }

        if(ord(substr($f, $stringChar, 1))!=0) {
            $this->Error('Unknown compression method: '.$file);
        }
        $stringChar += 1;
        if(ord(substr($f, $stringChar, 1))!=0) {
            $this->Error('Unknown filter method: '.$file);
        }
        $stringChar += 1;
        if(ord(substr($f, $stringChar, 1))!=0) {
            $this->Error('Interlacing not supported: '.$file);
        }
        $stringChar += 1;
        $stringChar += 4;
        $parms='/DecodeParms <</Predictor 15 /Colors ' .
            ($ct==2 ? 3 : 1) . ' /BitsPerComponent ' . $bpc . ' /Columns ' . $w . '>>';
        //Scan chunks looking for palette, transparency and image data
        $pal='';
        $trns='';
        $data='';
        do {
            $a = unpack('Ni',substr($f, $stringChar, 4));
            $n = $a['i'];
            $stringChar += 4;
            $type = substr($f, $stringChar, 4);
            $stringChar += 4;
            if($type=='PLTE') {
                //Read palette
                $pal = substr($f, $stringChar, $n);
                $stringChar += $n;
                $stringChar += 4;
            } elseif($type=='tRNS') {
                //Read transparency info
                $t = substr($f, $stringChar, $n);
                $stringChar += $n;
                if($ct==0) {
                    $trns=array(ord(substr($t,1,1)));
                } elseif($ct==2) {
                    $trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
                } else {
                    $pos=strpos($t,chr(0));
                    if($pos!==false) {
                        $trns=array($pos);
                    }
                }
                $stringChar += 4;
            } elseif($type=='IDAT') {
                //Read image data block
                $data .= substr($f, $stringChar, $n);
                $stringChar += $n;
                $stringChar += 4;
            } elseif($type=='IEND') {
                break;
            } else {
                $stringChar += $n + 4;
            }
        } while($n);
        if($colspace=='Indexed' && empty($pal)) {
            $this->Error('Missing palette in '.$file);
        }
        return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc,
            'f'=>'FlateDecode', 'parms'=>$parms, 'pal'=>$pal,
            'trns'=>$trns, 'data'=>$data);
}

// }}}
// FpdfJS::_putimages() {{{

/**
 * Overrided method for alpha channel support.
 *
 * @access protected
 * @return void
 */
public function _putimages() {
    $filter = $this->compress ? '/Filter /FlateDecode ' : '';
    reset($this->images);
    while(list($file,$info)=each($this->images)) {
        $this->_newobj();
        $this->images[$file]['n'] = $this->n;
        $this->_out('<</Type /XObject');
        $this->_out('/Subtype /Image');
        $this->_out('/Width ' . $info['w']);
        $this->_out('/Height ' . $info['h']);

        if(isset($info['masked'])) {
            $this->_out('/SMask ' . ($this->n-1) . ' 0 R');
        }

        if($info['cs']=='Indexed') {
            $this->_out('/ColorSpace [/Indexed /DeviceRGB ' .
                (strlen($info['pal'])/3-1) . ' ' . ($this->n+1) . ' 0 R]');
        } else {
            $this->_out('/ColorSpace /' . $info['cs']);
            if($info['cs']=='DeviceCMYK') {
                $this->_out('/Decode [1 0 1 0 1 0 1 0]');
            }
        }
        $this->_out('/BitsPerComponent ' . $info['bpc']);
        if(isset($info['f'])) {
            $this->_out('/Filter /' . $info['f']);
        }
        if(isset($info['parms'])) {
            $this->_out($info['parms']);
        }
        if(isset($info['trns']) && is_array($info['trns'])) {
            $trns='';
            for($i=0 ; $i<count($info['trns']) ; $i++) {
                $trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
            }
            $this->_out('/Mask [' . $trns . ']');
        }
        $this->_out('/Length ' . strlen($info['data']) . '>>');
        $this->_putstream($info['data']);
        unset($this->images[$file]['data']);
        $this->_out('endobj');
        //Palette
        if($info['cs']=='Indexed') {
            $this->_newobj();
            $pal = $this->compress ? gzcompress($info['pal']) : $info['pal'];
            $this->_out('<<' . $filter . '/Length ' . strlen($pal) . '>>');
            $this->_putstream($pal);
            $this->_out('endobj');
        }
    }
}

// }}}
    // FpdfJS::_gamma() {{{

    /**
     * GD seems to use a different gamma,
     * this method is used to correct it again
     *
     * @access private
     * @param float $v Gamma value.
     * @return float
     */
    private function _gamma($v){
        return pow ($v/255, 2.2) * 255;
    }

    // }}}
    // FpdfJS::Close() {{{

    private $_tmpFiles = array();

    /**
     * Overrided method.
     *
     * Call the method Close() of the parent base class and unlink the files
     * stored in $_tmpFiles.
     *
     * @return void
     */
    public function close() {
        parent::close();
        // clean up tmp files
        foreach($this->_tmpFiles as $tmp) {
            @unlink($tmp);
        }
    }

    // }}}
}
?>
