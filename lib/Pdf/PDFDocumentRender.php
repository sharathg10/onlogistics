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

require('Pdf/FpdfJS.php');

define('FPDF_FONTPATH', dirname(__FILE__) . '/font/');
define('PDF_FONTSIZE_TITLE',11);
define('PDF_FONTSIZE_SUBTITLE', 9);
define('PDF_FONTSIZE_FOOTER', 8);
define('PDF_FONTSIZE_DEFAULT', 8);
define('PDF_FONTSIZE_FILIGREE', 90);
define('PDF_FONTSIZE_HEADER', 9);
define('PDF_FONTSIZE_ADDRESS', 10);
define('PDF_FAMILYFONT', 'Arial');

class PDFDocumentRender extends FpdfJS {

    /**
     * Angle avec l'horizontale pour le curseur
     * Sens inverse des aiguilles
     *
     * @access protected
     */
    protected $_Angle = 0;

    /**
     * Tableau des fonts
     * @var array
     */
    public $defaultFontSize = array('TITLE' => PDF_FONTSIZE_TITLE,
					       'SUBTITLE' => PDF_FONTSIZE_SUBTITLE,
					       'FOOTER' => PDF_FONTSIZE_FOOTER,
					       'HEADER' => PDF_FONTSIZE_HEADER,
					       'DEFAULT' => PDF_FONTSIZE_DEFAULT,
					       'FILIGREE' => PDF_FONTSIZE_FILIGREE);

    /**
     * Fammille de font
     * @var string
     */
    public $defaultFamilyFont = PDF_FAMILYFONT; // synomyme de Helvetica

    /**
     * style de la font [B|U|I]
     * @var string
     */

    public $defaultStyleFont = '';

    /**
	 * Nombre de colonne du tableau de donnees s'il y en a un
     * @access private
     * @var integer
	 */
    private $_NbDataColumns = 0;

    /**
	 * Largeur des colonnes du tableau de donnees s'il y en a un
     * @access private
     * @var array of integer
	 */
    private $_ColumnWidths = array();

    /**
	 * Cable sur DocumentModel->GetFooter()
     * @access public
     * @var array of integer
	 */
    public $footer;

	/**
	 * Contient "Facture No XXX" ou "Bordereau de livraison No XXX"
     * @access public
     * @var string
	 */
    public $docTitle;

	/**
	 * Date d'edition du document formatee
     * @access public
     * @var string
	 */
    public $docDate;

	/**
	 * Contient un logo en base64 ou string vide
     * @access public
     * @var string
	 */
    public $logo = '';

	/**
	 * Contient la Command attachee au doc
     * @access public
     * @var object
	 */
    public $Command = null;

	/**
	 * Contient l'acteur expediteur/fournisseur
     * @access public
     * @var object
	 */
    public $Expeditor = null;

	/**
	 * Contient le site dont il faut afficher l'adresse en dessous du logo
     * @access public
     * @var object
	 */
    public $ExpeditorSite = null;

	/**
	 * Intitule de l'adresse affichee a gauche dans AddHeader()
     * @access public
     * @var string
	 */
    public $leftAdressCaption = '';

	/**
	 * Intitule de l'adresse affichee a droite dans AddHeader()
     * @access public
     * @var string
	 */
    public $rightAdressCaption = '';

	/**
	 * Contenu de l'adresse affichee a gauche dans AddHeader()
     * @access public
     * @var string
	 */
    public $leftAdress = '';

	/**
	 * Contenu de l'adresse affichee a droite dans AddHeader()
     * @access public
     * @var string
	 */
    public $rightAdress = '';

	/**
	 * Vaut 0 par defaut, et 1 si reedition
     * @access public
     * @var integer
	 */
    public $reedit = 0;

    /**
     * true si affichage de l'expediteur dans le header
     * @var boolean
     */
    public $showExpeditor = false;

    /**
     * Mettre a false pour masquer les numeros de page
     * @var boolean
     */
    public $showPageNumbers = true;

    /**
     * Mettre a false pour masquer la date d'edition en haut
     * @var boolean
     */
    public $showEditionDate = true;

    /**
     * Indicating whether a new group was requested
     * @var boolean
     */
    var $NewPageGroup;
    /**
     * The number of pages of the groups
     * @var integer
     */
    var $PageGroups;
    /**
     * The alias of the current page group
     * @var integer
     */
    var $CurrPageGroup;

    /**
     * PDFDocumentRender::__construct()
     * Constructor
     *
     * @param boolean $print impression du document - default: false
     * @param boolean $autoPrint impression automatique ou non - default: true
     * @param string $orientation orientation du doc
     *                            [P(portrait)|L(landscape)] - default P
     * @param string $unit unité des mesures [mm|cm|in|] - default mm
     * @param stinrg $format format de la page [A4|A3|A5|lettre|legal]- default A4
     * @param string $unit
     * @return void
     */
    public function __construct($print = false, $autoPrint=true,
        $orientation='P', $unit='mm', $format='A4') { // {{{
        parent::__construct($orientation, $unit, $format, false, "iso-8859-1");
        if (I18N::getLocaleCode() == 'tr_TR') {
            $this->defaultFamilyFont = 'arial_iso-8859-9';
            $this->AddFont('arial_iso-8859-9');
            $this->AddFont('arial_iso-8859-9', 'B');
        }

		// a commenter si on veut des documents a ne pas imprimer automatiqt
        if($autoPrint) {
		    parent::AutoPrint($print);
        }
        $this->setAuthor('GLAO pilote vos flux');
        // Permet de ne pas ecraser le footer
        $this->setAutoPageBreak(true, 35);
    } // }}}

    /**
     * Methode Output surchargee pour gérer l'affichage sous https
     *
     * @param string $name nom du document - default doc.pdf
     * @param string mode d'affichage [D|F|I] - default I
     * @access public
     * @return void or string  string si $dest='S'
     */
    public function output($name = '', $dest = '') { // {{{
        require_once('config.inc.php');
        // Output PDF to some destination
        // Finish document if necessary
        if ($this->state < 3)
            $this->close();
        // Normalize parameters
        if (is_bool($dest))
            $dest = $dest ? 'D' : 'F';
        $dest = strtoupper($dest);
        if ($dest == '') {
            if ($name == '') {
                $name = 'doc.pdf';
                $dest = 'I';
            } else
                $dest = 'F';
        }

        switch ($dest) {
            case 'I':
                // Send to standard output
                if (ob_get_contents())
                    $this->Error('Some data has already been output, can\'t send PDF file');
                if (php_sapi_name() != 'cli') {
                    // We send to a browser
                    if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
						header('Cache-control: private');
                        header('Pragma: public');
                    } else {
                        header('Cache-Control: no-cache, must-revalidate');
                        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    }
					header('Content-Type: application/force-download');
                    header('Content-Type: application/pdf');
                    if (headers_sent()) {
                        $this->Error('Some data has already been output to browser, can\'t send PDF file');
                    }
                    header('Content-Length: ' . strlen($this->buffer));
                    header('Content-disposition: inline; filename="' . $name . '"');
                }
                echo $this->buffer;
                break;
            default:
                return parent::output($name, $dest);
                break;
        }
        return;
    } // }}}

    /**
	 * Retablit la font par defaut
	 * @return void
	 */
    public function restoreFont() { // {{{
        $this->SetFont($this->defaultFamilyFont, $this->defaultStyleFont,
                       $this->defaultFontSize['DEFAULT']);
    } // }}}

    /**
     * Selectionne la font de titre avant ecriture
     * @return void
     */
    public function setTitleFont() { // {{{
        $this->SetFont($this->defaultFamilyFont, 'B', $this->defaultFontSize['TITLE']);
    } // }}}

    /**
	 * Methode surchargee pour gerer les filigrammes si reedition
	 * Appelle notamment header()
	 * @access public
	 * @return void
	 **/
	public function addPage() { // {{{
		parent::AddPage();
		if ($this->reedit == 1) {
		    $this->Filigree('- DUPLICATA -');
		}
	} // }}}

	/**
	 * Ajoute un header en haut de toutes les pages, automatiquement
     *
	 * @access public
	 * @return void
	 **/
	public function header() { // {{{
		// XXX FIXME: Attention: seul le type png est géré ici, il faudra à terme
		// le gérer via un paramètre $type, ou bien le détecter
		if ($this->logo != '') { // image sous forme de string
			$this->image($this->logo, 10, 8, 0, 17, 'png');
        }
		$this->setXY(10, 25);
	    $this->setFont($this->defaultFamilyFont, '', $this->defaultFontSize['HEADER']);
		// evite les sauts de ligne inutiles
        if ($this->showExpeditor) {
		    $Command = $this->Command;
		    $Expeditor = $this->Expeditor;
		    require_once('GetAddressItems.php');
	        $expSite = $this->ExpeditorSite;
	        $ExpeditorSiteAdressArray = getAddressItems($expSite,
                $expSite->getPhone());

            $ExpeditorSiteAdress = $Expeditor->getQualityForAddress()
                . $Expeditor->getName() . "\n";
            $addrInfos = array('StreetNo', 'StreetType', 'StreetName');
            foreach ($addrInfos as $info) {
                if(!empty($ExpeditorSiteAdressArray[$info])) {
                    $ExpeditorSiteAdress .= $ExpeditorSiteAdressArray[$info] . ' ' ;
                }
            }
            $ExpeditorSiteAdress .= "\n";
            if(!empty($ExpeditorSiteAdressArray['StreetAddons'])) {
                $ExpeditorSiteAdress .=
                    $ExpeditorSiteAdressArray['StreetAddons'] . "\n";
            }
            $zip = isset($ExpeditorSiteAdressArray['Zip']) ?
                $ExpeditorSiteAdressArray['Zip'] . ' ': '';
		    $ExpeditorSiteAdress .= $zip .
                $ExpeditorSiteAdressArray['City'] . ' ' .
                $ExpeditorSiteAdressArray['Country'];
            if(!empty($ExpeditorSiteAdressArray['Phone'])) {
                $ExpeditorSiteAdress .= "\n" . $ExpeditorSiteAdressArray['Phone'];
            }
	        $this->MultiCell(57, 4, trim(str_replace("\n\n", "\n", $ExpeditorSiteAdress)));
        }
        $this->setFont($this->defaultFamilyFont,'B', $this->defaultFontSize['HEADER']);//PDF_FONTSIZE_HEADER);
		$this->setXY(100, 15);
		$this->Cell(90, 3,  $this->docTitle);
        if ($this->showEditionDate) {
		    $this->setXY(100, 19);
		    $this->Cell(90, 3,  _('Edition date') . ': ' . $this->docDate);
        }

		$this->setXY(100,23);
		$this->setFont($this->defaultFamilyFont,'B', $this->defaultFontSize['HEADER']);//PDF_FONTSIZE_HEADER);

		// Selon si plusieurs factures dans le même pdf ou non
        if ($this->showPageNumbers) {
		    if (!empty($this->PageGroups)) {
		        $this->Cell(90, 3, _('Page') . ' ' . $this->GroupPageNo()
                    . '/' . $this->PageGroupAlias());
		    } else {
                $this->Cell(90, 3, _('Page') . ' ' .  $this->PageNo() . '/{nb}');
            }
        }

/*        if($this->groupPageNo() == 1) {  // $this->PageNo()
            $this->addHeader();
        } else {
            $this->setY(52);
        }*/
	} // }}}

    /**
	 * Cree un header en plus de celui par defaut, et sur 1 seule page
	 * avec les addresses
	 * @param $setY int: distance du haut de page, si besoin d'ecrire qqchose au dessus du header
	 * @param object $parentDoc object PDFDocumentRender si $this sert a la
     * construction d'un pdf contenant n factures
	 * @return void
	 */
    public function addHeader($setY = 0, $parentDoc=false) { // {{{
        if ($setY != 0) {
            $this->SetXY(10, $setY);
        } else {
            $this->SetXY(10, 52);
        }
        // adresse de livraison
        $this->SetFont($this->defaultFamilyFont, 'bU', $this->defaultFontSize['TITLE']);
        $this->Cell(45, 3, $this->leftAdressCaption, 0, 0, 'L');
        $this->SetXY(10, 58);
        $this->RestoreFont();
        $this->MultiCell(57, 3.5, trim(str_replace("\n\n", "\n", $this->leftAdress)));
        $this->SetXY(130, 52);
        $this->SetFont($this->defaultFamilyFont, 'bU', $this->defaultFontSize['TITLE']);
        $this->Cell(45, 3, $this->rightAdressCaption, 0, 0, 'L');
        $this->SetXY(130, 58);
        $this->RestoreFont();
        $this->MultiCell(57, 3.5, trim(str_replace("\n\n" , "\n" , $this->rightAdress)));
        $this->Ln(10);
    } // }}}

    /**
	 * Par defaut, ajoute juste le No de page en bas de page, centre
	 * @return void
	 */
    public function footer() { // {{{
        $this->AliasNbPages();
        $this->SetFont($this->defaultFamilyFont, '', $this->defaultFontSize['FOOTER']);
        $content = $this->footer;
		$jump = ($this->NbLines(192, $content) > 4)?35:30;
        $this->SetY(-$jump);
        $this->MultiCell(192, 3, $content, 0, 'C');
    } // }}}

    /**
	 * Cree un footer pour le document
	 * @param string $content contenu a placer ds le footer
	 * @param integer $Y distance du bas de la page
	 * @return void
	 */
    public function addFooter($content, $Y) { // {{{
        $this->SetAutoPageBreak(false); // disables the automatic page breaking mode
        $this->SetY(- $Y);
        $this->RestoreFont();
        $this->MultiCell(192, 3, $content, 0, 'C');
    } // }}}

    /**
	 * Cree les entetes d'un tableau des donnees a afficher
	 * @param $columns array of strings: tableau associatif: contenuCellule => largeur
	 * @param $color = 0 par defaut 1 pour afficher une couleur de fond
	 * exple: $columns = array("Référence \ncommandée" => 30,
				 			  "Désignation" => 75,
				 			  "Quantité \ncommandée" => 27);

	 * @return integer : nombre de colonnes
	 */
    public function tableHeader($columns, $color = 0, $border = 1) { // {{{
        $this->_NbDataColumns = count($columns);
        $return = false;
        $ColumnHeader = array();
        $ColumnWidth = array(); // va contenir les largeurs des cellules
        foreach($columns as $label => $width) {
            $ColumnWidth[] = $width;
            $data[] = $label;
        }
        $this->_ColumnWidths = $ColumnWidth;
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Row($data, array(),
                   array('color'=>$color, 'font'=>$this->defaultFamilyFont,
                         'fontStyle'=>'B',
                         'fontSize'=>$this->defaultFontSize['SUBTITLE'],
                         'align'=>'J', 'border'=>$border, 'lineHeight'=>6));
        $this->restoreFont();
        return(count($data) + 1);
    } // }}}

    /**
	 * Cree le corps d'un tableau de donnees
	 * @param array $data contenu du tableau
	 * @param array $headerTable header du tableau
	 * @return void
	 */
    public function tableBody($data, $headerTable=array()) { // {{{
        for ($i = 0; $i < count($data); $i++) {
            $this->Row($data[$i], $headerTable);
        }
    } // }}}

    /**
	 * Calcule la hauteur que doit avoir une ligne, en prenant en compte
	 * toutes ses cellules et ajoute cette ligne au tableau
	 * @param array $data les donnees pour une ligne
	 * @param array $headerTable le header du tableau
	 * @param array $params paramètres de font
	 * @return void
	 */
    public function row($data, $headerTable=array(), $params=array()) { // {{{
        $defaultCellParams = array(
            'align' => 'L',
            'color' => '',
            'font' => $this->defaultFamilyFont,
            'fontSize' => $this->defaultFontSize['DEFAULT'],//defaultSizeFont,
            'fontStyle' => $this->defaultStyleFont,
            'border' => 0,
            'lineHeight'=>5);
        foreach ($defaultCellParams as $key => $value) {
            if(empty($params[$key])) {
                $params[$key] = $defaultCellParams[$key];
            }
        }
        $this->SetFont($params['font'], $params['fontStyle'] , $params['fontSize']);
        $nb = 0;
        $nbOfCells = count($data);
        for($i = 0;$i < $nbOfCells;$i++) {
            $nb = max($nb, $this->NbLines($this->_ColumnWidths[$i], $data[$i]));
        }
        $h = $params['lineHeight'] * $nb;
        $this->CheckPageBreak($h, $headerTable); //Issue a page break first if needed
        // Draw the cells of the row
        for($i = 0;$i < $nbOfCells;$i++) {
            $w = $this->_ColumnWidths[$i];
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            if($params['color']==1) {
                $this->Rect($x, $y, $w, $h, 'FD'); //Draw the border
            } else {
                $this->Rect($x, $y, $w, $h); //Draw the border
            }
            $this->MultiCell($w, $params['lineHeight'], $data[$i],
                             0,//$params['border'],
                             $params['align'],
                             0);//$params['color']);
            $this->SetXY($x + $w, $y); //Put the position to the right of the cell
        }
        $this->Ln($h); //Go to the next line
    } // }}}

    /**
	 * Cree un saut de page si necessaire
	 * @param float $h
	 * @param array $inTable réaffiche le contenue de inTable comme un header de tableau
	 * @return void
	 */
    public function checkPageBreak($h, $inTable=array()) { // {{{
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
            if($this->PageNo() == 1) {
			    $this->AddHeader(); // ces donnees d'adresse dans toutes les pages
            } else {
                $this->setY(52);
            }
			$this->SetFont('', '' , 9);
			if(!empty($inTable)) {
			    $this->tableHeader($inTable, 1);
			}
        }
    } // }}}

    /**
	 * Retourne le nombre de lignes de texte necessaire pour une cellule
	 * en fonction du contenu a y mettre
	 * @param float $w largeur de la cellule
	 * @param string $txt contenu de la cellule
	 * @return int
	 */
    public function nbLines($w, $txt) { // {{{
        $cw = $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w-2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb-1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    } // }}}

    /**
	 * Effectue une rotation du curseur
	 * @param $angle int: angle (sens des aiguilles)
	 * @param $x int: abscisse a laquelle on pose le curseur (si -1: position courante)
	 * @param $y int: ordonnee a laquelle on pose le curseur (si -1: position courante)
	 * @return void
	 */
    public function rotate($angle, $x = -1, $y = -1) { // {{{
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->_Angle != 0)
            $this->_out('Q');
        $this->_Angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',
								$c, $s, - $s, $c, $cx, $cy, - $cx, - $cy));
        }
    } // }}}

    /**
	 * Ajoute un filigrane en travers des pages (dupplicata,...)
	 * @param $txt string: contenu de la cellule
	 * @return void
	 */
    public function filigree($text) { // {{{
        $this->SetFont($this->defaultFamilyFont, 'B', $this->defaultFontSize['FILIGREE']);
        $this->SetTextColor(235, 235, 235); // 203
        $this->Rotate(45, 35, 240); // 55,190
        $this->Text(35, 240, $text);
        $this->Rotate(0);
        $this->SetTextColor(0, 0, 0);
    } // }}}

    /**
     * Ajoute du texte au document
     * <code>
     * $pdf = new PDFDocumentRender();
     * $pdf->addText('Mon texte à moi', array(
     *       'align'      => 'L',     // alignement du texte
     *       'font'       => 'times', // police
     *       'fontSize'   => 12,      // taille de la police
     *       'fontStyle'  => 'BU',    // style de la police
     *       'border'     => 1,       // bordure autour de la zone de texte
     *       'lineHeight' => 5,       // hauteur des lignes
     *       'width'      => 100      // largeur de la zone de texte
     * ));
     * </code>
     * @param string $text le texte à ajouter
     * @param array $params différents paramètres pour personaliser le texte :
     * @return void
     */
    public function addText($text, $params=array()) { // {{{
        $defaultTextParams = array(
            'align' => 'L',
            'font' => $this->defaultFamilyFont,
            'fontSize' => $this->defaultFontSize['DEFAULT'],//defaultSizeFont,
            'fontStyle' => $this->defaultStyleFont,
            'border' => 0,
            'lineHeight' => 5,
            'width' => PAGE_WIDTH);
        foreach ($defaultTextParams as $key => $value) {
            if(empty($params[$key])) {
                $params[$key] = $defaultTextParams[$key];
            }
        }
        $this->SetFont($params['font'], $params['fontStyle'], $params['fontSize']);
        $this->MultiCell($params['width'], $params['lineHeight'],
                $text, $params['border'], $params['align'], 0);
        //$this->Ln($params['lineHeight']);
        $this->restoreFont();
    } // }}}

    /**
	 * Create a new page group; call this before calling AddPage()
	 * http://fpdf.org/fr/script/script57.php
	 * @return void
	 */
    function startPageGroup() { // {{{
        $this->NewPageGroup=true;
    }

    /**
	 * Current page in the group
	 * http://fpdf.org/fr/script/script57.php
	 * @return integer
	 */
    function groupPageNo() { // {{{
        return $this->PageGroups[$this->CurrPageGroup];
    }

    /**
	 * Alias of the current page group -- will be replaced by the total number
     * of pages in this group http://fpdf.org/fr/script/script57.php
	 * @return integer
	 */
    function pageGroupAlias() { // {{{
        return $this->CurrPageGroup;
    }

    /**
     * http://fpdf.org/fr/script/script57.php
	 * @param $orientation string: orientation du doc
     *        [P(portrait)|L(landscape)] - default P
	 * @return void
	 */
    function _beginpage($orientation) { // {{{
        parent::_beginpage($orientation);
        if($this->NewPageGroup)
        {
            // start a new group
            $n = sizeof($this->PageGroups)+1;
            $alias = "{nb$n}";
            $this->PageGroups[$alias] = 1;
            $this->CurrPageGroup = $alias;
            $this->NewPageGroup=false;
        }
        elseif($this->CurrPageGroup)
            $this->PageGroups[$this->CurrPageGroup]++;
    }

    /**
     * http://fpdf.org/fr/script/script57.php
	 * @return void
	 */
    function _putpages() { // {{{
        $nb = $this->page;
        if (!empty($this->PageGroups))
        {
            // do page number replacement
            foreach ($this->PageGroups as $k => $v)
            {
                for ($n = 1; $n <= $nb; $n++)
                {
                    $this->pages[$n]=str_replace($k, $v, $this->pages[$n]);
                }
            }
        }
        parent::_putpages();
    }
}
?>
