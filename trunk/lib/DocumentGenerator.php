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
 tableHeader
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

// includes {{{

require_once('Pdf/PDFDocumentRender.php');
require_once('Numbers/Words.php');
require_once('Objects/Operation.const.php');
require_once('Objects/SupplierCustomer.php');
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');
require_once('Objects/AbstractDocument.php');
require_once('LangTools.php');

// }}}
// Constants {{{

// limite pour le changement de page sur les items
define('PAGE_HEIGHT_LIMIT', 270);
define('PAGE_HEIGHT_LIMIT_TO_TOTAL', 205);
define('PAGE_WIDTH', 190);
define('NUMBER_OF_CELLS_PER_TABLE', 5);
define('PAGE_HEIGHT_LIMIT_TO_CHANGE_LETTER', 90);
define('PAGE_HEIGHT_LIMIT_TO_LOGCARD_BARCODES', 222);

// }}}

// DocumentGenerator {{{

/**
 * DocumentGenerator.
 * Classe de base pour les autres documents pdf.
 *
 */
class DocumentGenerator
{ 
    // properties {{{

    /**
     * Le document PDF
     * @var Object PDFDocumentRender
     */
    public $pdf = false;

    /**
     * Le nom du document
     * @var string
     */
    public $docName = false;

    /**
     * Objet documentModel
     */
    public $documentModel = false;
    /**
     * propriétés de classe servant de raccourcis pour les diverses méthodes
     */
    public $document = false;
    public $command = false;
    public $expeditor = false;
    public $destinator = false;
    public $expeditorSite = false;
    public $destinatorSite = false;
    public $currency = false;
    public $editor = false;   // Acteur editeur du document

    // }}}
    // __construct() {{{

    /**
     * Constructor
     *
     * @param Object $document un AbstractDocument
     * @param boolean $isReedition true si reedition
     * @param boolean $autoPrint true pour impression automatique
     * @param object $currency devise (je sais pas trop ce que ca fait là!)
     * @param string $docName nom du pdf généré
     * @param string $orientation orientation du pdf
     * @param string $unit unité de mesure
     * @param string $format format du pdf
     * @return void
     */
    public function __construct($document, $isReedition = false,
                                $autoPrint = true, $currency = false,
                                $docName = '', $orientation='P',
                                $unit='mm', $format='A4') {
        $this->document = $document;
        $this->docName  = $docName;
        // un document doit être réimprimé dans sa langue originelle
        if ($this->document instanceof AbstractDocument && $this->document->getId() > 0) {
            $locale = $this->document->getLocale();
            if (empty($locale)) {
                $locale = I18N::getLocaleCode();
                $this->document->setLocale($locale);
                $this->document->save();
            }
            I18N::setLocale($locale);
        }
        //gestion de l'affichage ou non de la mention duplicata
        $dom = $this->document->getDocumentModel();
        if($dom instanceof DocumentModel) {
            $this->documentModel = $dom;
            if($dom->getDisplayDuplicata()==0) {
                $isReedition = false;
            }
        }
        // gestion devise
        $this->currency = $currency instanceof Currency?
            TextTools::entityDecode($currency->getSymbol()):'€';
        $this->currencyCode = $currency instanceof Currency?
            $currency->getName():_('Euro');
        $this->isReedition = $isReedition;

        // le doc pdf
        $this->pdf = new PDFDocumentRender(false, $autoPrint, $orientation,
            $unit, $format);
        $this->pdf->reedit = $isReedition;
        $this->pdf->Command = $this->command;
        $this->pdf->Expeditor = $this->expeditor;
        $this->pdf->ExpeditorSite = $this->expeditorSite;
        $this->pdf->footer = $this->document->getFooter();
    }

    // }}}
    // DocumentGenerator::formatNumber() {{{

    /**
     * Formatte un nombre conformement aux usages dans la langue courante.
     * Fait un appel à I18N::formatNumber(), avec le param $strict à true, pour 
     * afficher le separateur de milliers
     * 
     * @access public
     * @param mixed int, double or string $number le nombre à formatter
     * @param int $dec_num le nombre de décimales
     * @param boolean $skip_zeros "effacer" les zeros en fin de chaine
     * @static
     * @return string
     */
    public static function formatNumber($number, $dec_num=2, $skip_zeros=false) {
       return I18N::formatNumber($number, $dec_num, $skip_zeros, true);
    }

    // }}}
    // DocumentGenerator::formatCurrency() {{{

    /**
     * Formatte un montant en devise conformement aux usages dans la langue courante.
     * Fait un appel à I18N::formatCurrency(), avec le param $strict à true, pour 
     * afficher le separateur de milliers
     * 
     * @access public
     * @param  string $currency le symbole de la devise
     * @param mixed int, double or string $number le nombre à formatter
     * @param int $dec_num le nombre de décimales
     * @param boolean $skip_zeros "effacer" les zeros en fin de chaine
     * @static
     * @return string
     */
    public static function formatCurrency($currency, $number, $dec_num=2, $skip_zeros=false) {
       return I18N::formatCurrency($currency, $number, $dec_num, $skip_zeros, true);
    }

    // }}}
    // DocumentGenerator::formatPercent() {{{
    
    /**
     * Formatte un pourcentage conformement aux usages dans la langue courante.
     * Fait un appel à I18N::formatPercent(), avec le param $strict à true, pour 
     * afficher le separateur de milliers
     * 
     * @access public
     * @param mixed int, double or string $number le nombre à formatter
     * @param int $dec_num le nombre de décimales
     * @param boolean $skip_zeros "effacer" les zeros en fin de chaine
     * @static
     * @return string
     */
    public static function formatPercent($number, $dec_num=2, $skip_zeros=false) {
       return I18N::formatPercent($number, $dec_num, $skip_zeros, true);
    }

    // }}}
    // DocumentGenerator::render() {{{
        
    /**
     * Construit le document pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        trigger_error('Abstract method...', E_USER_WARNING);
    }

    // }}}
    // DocumentGenerator::renderHeader() {{{

    /**
     *
     * @access public
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * dans le meme pdf
     * @return void
     */
    public function renderHeader($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;
        $pdfDoc->docTitle = $this->docName . ' N° ' .
            $this->document->getDocumentNo();
        $pdfDoc->docDate =  I18n::formatDate($this->document->getEditionDate(), I18N::DATE_SHORT);
        if (!$pdfDoc->logo) {
            $pdfDoc->logo = base64_decode($this->document->getLogo());
        }
    }

    // }}}
    // DocumentGenerator::renderFooter() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function renderFooter() {
    }

    // }}}
    // DocumentGenerator::renderAddressesBloc() {{{

    /**
     * Render des blocs d'adresses
     * @access public
     * @return void
     */
    public function renderAddressesBloc() {
        $this->buildLeftAddress();
        $this->buildRightAddress();
        $this->pdf->addHeader();
    }

    // }}}
    // DocumentGenerator::buildLeftAddress() {{{

    /**
     * Affiche l'adresse de gauche (par defaut: adresse de livraison).
     *
     * @access protected
     * @return void
     */
    protected function buildLeftAddress() {
        $str = '';
        if ($this->expeditor instanceof Actor) {
            $str = $this->expeditor->getQualityForAddress() 
                 . $this->expeditor->getName() . "\n";
        }
        if ($this->expeditorSite instanceof Site) {
            $str = $this->expeditorSite->getName() . "\n" 
                 . $this->expeditorSite->getFormatAddressInfos("\n");
        }
        $this->pdf->leftAdressCaption = _('Shipper') . ': ';
        $this->pdf->leftAdress = $str;
    }

    // }}}
    // DocumentGenerator::buildRightAddress() {{{

    /**
     * Affiche l'adresse de droite (par defaut: adresse de facturation).
     *
     * @access public
     * @return void
     */
    protected function buildRightAddress() {
        $str = '';
        if ($this->destinator instanceof Actor) {
            $str = $this->destinator->getQualityForAddress() 
                 . $this->destinator->getName() . "\n";
        }
        if ($this->destinatorSite instanceof Site) {
            $str = $this->destinatorSite->getName() . "\n" 
                 . $this->destinatorSite->getFormatAddressInfos("\n");
        }
        $this->pdf->rightAdressCaption = _('Addressee') . ': ';
        $this->pdf->rightAdress = $str;
    }

    // }}}
    // DocumentGenerator::renderSNLotBloc() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function renderSNLotBloc() {
        trigger_error('Abstract method...', E_USER_WARNING);
    }

    // }}}
    // DocumentGenerator::numberWords() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function numberWords($float) {
        // faut ajouter un zero si on a qu'une décimale
        $array = explode('.', strval($float));
        if (isset($array[1]) && strlen($array[1]) == 1) {
            $array[1] .= '0';
            $float = strval($float) . '0';
        }
        $numberWords = new Numbers_Words();
        $str = $numberWords->toCurrency($float, getNumberWordsLangParam(), $this->currencyCode);
        if(is_string($str)) {
            return $str;
        }
        // pas de méthode pour la langue, on fait ca à l'ancienne
        // fonction définie dans lib-functions/LangTools.php
        $lparam = getNumberWordsLangParam();
        $int = $numberWords->toWords(intval($array[0]), $lparam);
        $dec = '';
        if (isset($array[1])) {
            $dec = $numberWords->toWords(intval($array[1]), $lparam);
        }
        // gestion du pluriel
        // Le nom de la devise prend un "s" au pluriel en france uniquement
        $currency = strtolower($this->currencyCode);
        if($lparam == 'fr' && $float > 1) {
            $e = explode(' ', $currency, 2);
            $currency = $e[0] . 's';
            if(isset($e[1])) {
                $currency .= ' ' . $e[1];
            }
        }
        return sprintf('%s %s %s', $int, $currency, $dec);
    }

    // }}}
    // DocumentGenerator::renderCustomsBlocs() {{{

    /**
     * Méthode qui crée les tableaux du haut en fonction des
     * DocumentModelProperty associés au DocumentModel utilisé.
     * Seules les DocumentModelProperty avec Property=0 sont affichés.
     * Un tableau ne peut contenir que 5 cellules, ont crée donc autant
     * de tableau que nécéssaire en triant les DocumentModelProperty selon
     * leur Order.
     *
     * @access public
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * dans le meme pdf
     * @return void
     */
    public function renderCustomsBlocs($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;
        require_once ('Objects/DocumentModelProperty.inc.php');

        $dom = $this->document->findDocumentModel();
        if($dom instanceof DocumentModel) {
            $domPropCol = $dom->getDocumentModelPropertyCollection(array('Property'=>0));
            $numberOfProperties = $domPropCol->getCount();
            $numberOfTable = ceil($numberOfProperties / NUMBER_OF_CELLS_PER_TABLE);

            $domMapper = Mapper::singleton('DocumentModelProperty');
            // pour chaque tableau :
            for ($i=1 ; $i<=$numberOfTable ; $i++) {
                $pdfDoc->ln(3);
                // récupérer les 5 documentModelProperty de la table dans l'ordre
                $domPropCol = $domMapper->loadCollection(
                    array('Property' => 0,
                          'DocumentModel' => $dom->getId()),
                    array('Order' => SORT_ASC),
                    array('PropertyType'), NUMBER_OF_CELLS_PER_TABLE, $i);

                $headerColumns = array();
                $dataColumns = array();
                $cells = $domPropCol->getCount();
                $cellsWidth = PAGE_WIDTH / $cells;
                for ($j=0 ; $j<$cells ; $j++) {
                    $property = $domPropCol->getItem($j);
                    // création de header
                    $index = getDocumentModelPropertyCellLabel(
                    $property->getPropertyType());
                    if(!isset($headerColumns[$index])) {
                        $headerColumns[$index] = $cellsWidth;
                        // création du contenu
                        $dataColumns[0][] = getDocumentModelPropertyCellValue(
                        $property->getPropertyType(), $this);
                    }
                }

                $pdfDoc->tableHeader($headerColumns, 1);
                $pdfDoc->tableBody($dataColumns);
                $pdfDoc->ln(3);
                unset($headerColumns, $dataColumns);
            }
        }
    }

    // }}}
    // DocumentGenerator::renderDescriptionOfGoodsField() {{{

    /**
     * Recupere le contenu du champ désignation personalise
     * dans le model de document
     *
     * @access public
     * @return string
     */
    public function renderDescriptionOfGoodsField($product) {
        $return = '';
        $dom = $this->document->findDocumentModel();
        if($dom instanceof DocumentModel) {
            $domPropertyCol = $dom->getDocumentModelPropertyCollection(
                    array('PropertyType'=>0), array('Order'=>SORT_ASC));
            $numberOfDomProps = $domPropertyCol->getCount();
            for ($i=0 ; $i<$numberOfDomProps ; $i++) {
                $domProperty = $domPropertyCol->getItem($i);
                $property = $domProperty->getProperty();
                if($product instanceof Product) {
                    $return .= ' '.
                        Tools::getValueFromMacro($product,
                            '%' . $property->getName() . '%');
                }
            }
        }
        return $return;
    }
    
    // }}}
}

// }}}
// CommandDocumentGenerator {{{

/**
 * CommandDocumentGenerator.
 * Classe de base pour les autres documents pdf.
 *
 */
class CommandDocumentGenerator extends DocumentGenerator
{
    // __construct() {{{

    /**
     * Constructor.
     *
     * @param Object $document AbstractDocument
     * @param boolean $isReedition true si reedition
     * @param boolean $autoPrint true si impression auto
     * @param string $docName nom du document
     */
    public function __construct($document, $isReedition = false,
                                $autoPrint = true, $docName = ''){
        $this->command = $document->getCommand();
        $this->expeditor = $this->command->getExpeditor();
        $this->expeditorSite = $this->command->getExpeditorSite();
        $this->destinator = $this->command->getDestinator();
        $this->destinatorSite = $this->command->getDestinatorSite();
        $this->supplierCustomer = $this->command->getSupplierCustomer();
        $cur = $this->command->getCurrency();
        parent::__construct($document, $isReedition, $autoPrint,
                            $cur, $docName);
    }

    // }}}
    // CommandDocumentGenerator::buildLeftAddress() {{{

    /**
     * Affiche l'adresse de gauche (par defaut: adresse de livraison).
     *
     * @access protected
     * @return void
     */
    protected function buildLeftAddress() {
        $site = $this->command->getDestinatorSite();
        if ($site instanceof Site) {
            $str = $site->getName() . "\n" . $site->getFormatAddressInfos("\n");
            $this->pdf->leftAdressCaption = _('Delivery address') . ': ';
            $this->pdf->leftAdress = $str;
        }
    }

    // }}}
    // CommandDocumentGenerator::buildRightAddress() {{{

    /**
     * Affiche l'adresse de droite (par defaut: adresse de facturation).
     *
     * @access public
     * @return void
     */
    protected function buildRightAddress() {
        $site = $this->destinator->getInvoicingSite();
        if ($site instanceof Site) {
            $str = $site->getName() . "\n" . $site->getFormatAddressInfos("\n");
            $this->pdf->rightAdressCaption = _('Billing address') . ': ';
            $this->pdf->rightAdress = $str;
        }
    }

    // }}}
    // CommandDocumentGenerator::renderSNLotBloc() {{{

    /**
     * Affiche untableau avec les details des infos des SN/Lot
     * récupérées par AbstractDocument::getSNLotArray()
     * Product Reference | Serial Number | Quantity
     * @access public
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * dans le meme pdf
     * @return void
     */
    public function renderSNLotBloc($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;
        $data = $this->document->getSNLotArray();
        if (count($data) > 0) {
            $pdfDoc->addPage();
            $pdfDoc->addHeader();
            $label = sprintf(_('Detail of delivered SN (%s No %s)'),
            $this->docName, $this->document->getDocumentNo());
            $pdfDoc->tableHeader(array($label=>190));
            $pdfDoc->ln(8);
            $pdfDoc->tableHeader(
                array(
                    _('Product Reference')=>50,
                    _('Serial Number')=>40,
                    _('Quantity')=>40
                    ),
                    1
                );
            $pdfDoc->tableBody($data);
            $this->pdf->ln(3);
        }
    }

    // }}}
    // CommandDocumentGenerator::renderComment() {{{

    /**
     * Ajoute le commentaire de la commande
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * @access protected
     * @return void
     */
    protected function renderComment($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;
        if (method_exists($this->document, 'getComment')) {
            $comment = $this->document->getComment();
        } else {
            $comment = $this->command->getComment();
        }
        if (!empty($comment)) {
            if ($pdfDoc->getY() >= PAGE_HEIGHT_LIMIT) {
                $pdfDoc->addPage();
                $pdfDoc->addHeader();
            }
            $this->pdf->tableHeader(array(_('Comment') => 190), 1);
            $this->pdf->tableBody(array(0 => array($comment)));
            $this->pdf->ln(3);
        }
    }

    // }}}
    // CommandDocumentGenerator::renderIncoterm() {{{

    /**
     * Ajoute le commentaire de la commande
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * @access protected
     * @return void
     */
    protected function renderIncoterm($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;
        $incoterm = $this->command->getIncoterm();
        if ($incoterm instanceof Incoterm) {
            $this->pdf->tableHeader(array(_('Incoterm') => 190), 1);
            $this->pdf->tableBody(array(0 => array($incoterm->getLabel())));
            $this->pdf->ln(3);
        }
    }

    // }}}
    // CommandDocumentGenerator::renderTermsOfPayment() {{{

    /**
     * Conditions de paiement.
     *
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * @access protected
     * @return void
     */
    protected function renderTermsOfPayment($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;
        $top = $this->command->getTermsOfPayment();
        if (!($top instanceof TermsOfPayment)) {
            return;
        }
        $title = _('Terms of payment') . ': ' . $top->getName();
        $pdfDoc->tableHeader(array($title => 190), 1);
        $pdfDoc->tableHeader(array(
            _('Date')      => 70,
            _('Amount')    => 50, 
            _('To pay to') => 70
        ), 1);
        $items = $top->getTermsOfPaymentItemCollection();
        foreach ($items as $item) {
            $event = $item->getPaymentEvent();
            $payments = $this->command->getInstalmentCollection();
            $instalmentsPaid = $this->command->getTotalInstalments();


            if ( ($event == TermsOfPaymentItem::ORDER 
                OR $event == TermsOfPaymentItem::BEFORE_ORDER 
                OR $event == TermsOfPaymentItem::BEFORE_DELIVERY)  
            AND ($this instanceof InvoiceGenerator )) {
                // Cas d'un acompte attendu 
                // On ne l'affiche pas du moment qu'on fait une facture car
                // soit il est deja regle 
                // soit on le zappe etant donné qu'on ne 
                // peut enregistrer de nouvel acompte si une facture est emise 
                // ... ( cqfd ... )
            } else {

                list($date, $amount, $to) = $item->getDateAndAmountForOrder($this->command);
                $toName = ($to instanceof Actor) ? $to->getName() : '';
                $date   = I18N::formatDate($date, I18N::DATE_LONG);
                $amount = $this->formatCurrency($this->currency, $amount);

                // if it's the last we need to adjust the amount
                // only for invoices ...
                if($this instanceof InvoiceGenerator ) {
                    $itemIds = $top->getTermsOfPaymentItemCollectionIds();
                    if ($item->getId() == array_pop($itemIds)) {
                        $tmpAmount = 0;
                        $realPayments = $this->command->getPaymentCollection();
                        if (!Tools::isEmptyObject($realPayments)) {
                            $jcount = $realPayments->getCount();
                            for($j = 0; $j < $jcount; $j++){
                                $Payment = $realPayments->getItem($j);
                                $tmpAmount += $Payment->getTotalPriceTTC();
                            }
                        }
                        // Test pour voir si c'est suffisant ...
                        $amount = $this->document->getToPayForDocument() ;
                        $amount = DocumentGenerator::formatCurrency($this->currency, $amount, 2) ;
                    }
                }
                $pdfDoc->tableBody(array(0 => array($date, $amount, $toName)));
            }
        }
        $pdfDoc->ln(3);
    }

    // }}}
    // CommandDocumentGenerator::renderAppendices() {{{

    /**
     * Ajoute les annexes au document.
     *
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * @access protected
     * @return void
     */
    protected function renderAppendices($pdfDoc = false) {
        $pdfDoc = $pdfDoc == false ? $this->pdf : $pdfDoc;
        $appendices = $this->destinator->getDocumentAppendixCollection();
        $count = 0;
        $inImage = false;
        foreach ($appendices as $appendix) {
            $img = $appendix->getImage();
            if (!empty($img)) {
                $infos = ImageManager::getFileInfo(md5($appendix->getImage()));
                if (is_array($infos) && !empty($infos['data'])) {
                    list(,$type) = explode('/', $infos['mimetype']);
                    $pdfDoc->addPage();
                    $pdfDoc->image('data://' . $infos['mimetype'] . ';base64,' 
                        . base64_encode($infos['data']), 0, 0, 210, 297, $type);
                }
                $inImage = true;
            } else {
                if ($inImage) {
                    // si il y a une annexe image avant
                    $pdfDoc->addPage();
                    $pdfDoc->ln();
                    $pdfDoc->ln();
                    $pdfDoc->ln();
                }
                $title = $appendix->getTitle();
                $body  = $appendix->getBody();
                if (!empty($title)) {
                    $pdfDoc->tableHeader(array($title => 190), 1);
                }
                if (!empty($body)) {
                    $pdfDoc->tableBody(array(0 => array($body)));
                }
                $pdfDoc->ln();
                $inImage = false;
            }
            $count++;
            $this->pdf->footer = '';
        }
    }

    // }}}
} // }}}
// DeliveryOrderGenerator {{{

/**
 * DeliveryOrderGenerator
 * Classe utilisee pour les bordereaux de livraison.
 *
 */
class DeliveryOrderGenerator extends CommandDocumentGenerator
{
    // __construct {{{

    /**
     * Constructor
     * @param Object $document DeliveryOrder
     * @param boolean $isReedition true si reedition
     * @return void
     */
    public function __construct($document, $isReedition = false) {
        $autoPrint = $isReedition?false:!DEV_VERSION;
        parent::__construct($document, $isReedition, $autoPrint,
                            _('Delivery order'));
        $date = $this->document->getEditionDate();
        $this->data = $this->command->getDataForBL($date);
    }

    // }}}
    // DeliveryOrder::render() {{{

    /**
     * Construit le document pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderCustomsBlocs();
        $this->_renderContent();
        $this->renderTotal1Bloc();
        $this->renderComment();
        $this->renderFooter();
        $this->renderSNLotBloc();
        return $this->pdf;
    }

    // }}}
    // DeliveryOrder::buildLeftAddress() {{{

    /**
     * On n'affiche pas l'adresse de facturation
     *
     * @access protected
     * @return void
     **/
    protected function buildLeftAddress()
    {
    }

    // }}}
    // DeliveryOrderGenerator::buildRightAddress() {{{

    /**
     * Affiche l'adresse de droite (ici adresse de livraison).
     *
     * @access protected
     * @return void
     */
    protected function buildRightAddress() {
        $site = $this->command->getDestinatorSite();
        if ($site instanceof Site) {
            $str = $site->getName() . "\n" . $site->getFormatAddressInfos("\n");
            $this->pdf->rightAdressCaption = _('Delivery address') . ': ';
            $this->pdf->rightAdress = $str;
        }
    }

    // }}}
    // DeliveryOrder::_renderContent() {{{

    /**
     * Render du contenu du doc
     * @access protected
     * @return void
     */
    function _renderContent() {
        //cellule désignation personnalisé dans Command.getDataForBL()
        $columns = array(
            _('Ordered products') => 60,
            _('Description of goods') => 70,
            _('Ordered qty') => 15,
            _('Selling unit') => 15,
            _('Delivered Qty') => 15,
            _('To deliver') => 15);
        $this->pdf->tableHeader($columns, 1);
        $this->pdf->tableBody($this->data[0], $columns);
        $this->pdf->ln(3);
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($this->data[0]);
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody(array($this->data[0][$count-1]), $columns);
            $this->pdf->ln(3);
        }
    }

    // }}}
    // DeliveryOrder::renderTotal1Bloc() {{{

    /**
     * Ajoute un tableau avec le total du bl
     * Number of parcels | Parcels total weight (Kg)
     * @access protected
     * @return void
     */
    protected function renderTotal1Bloc() {
        $this->pdf->tableHeader(
            array( _('Number of parcels') . ': ' . $this->data[1][0] => 190));
        $displayTotalWeight = $this->documentModel instanceof DocumentModel?
            $this->documentModel->getDisplayTotalWeight():true;
        if($displayTotalWeight) {
            $this->pdf->tableHeader(
                array(_('Parcels total weight (Kg)') . ': ' .
                $this->data[1][1] => 190));
        }
        $this->pdf->ln(3);
    }

    // }}}
    // DeliveryOrder::renderFooter() {{{

    /**
     * Affiche le pied de page
     * @access public
     * @return void
     **/
    public function renderFooter() {
        $content = _('Except written agreement of our share, our conditions of sale as signed by your care apply completely.');
        $this->pdf->addFooter($content, 60);
        parent::renderFooter();
    }

    // }}}
}

// }}}
// RTWDeliveryOrderGenerator {{{

/**
 * RTWDeliveryOrderGenerator
 * Classe utilisee pour les bordereaux de livraison de commandes produit client
 * en contexte pret a porter.
 *
 */
class RTWDeliveryOrderGenerator extends DeliveryOrderGenerator 
{
    // RTWDeliveryOrderGenerator::__construct {{{

    /**
     * Constructor
     * @param Object $document DeliveryOrder
     * @param boolean $isReedition true si reedition
     * @return void
     */
    public function __construct($document, $isReedition = false) {
        $autoPrint = $isReedition?false:!DEV_VERSION;
        parent::__construct($document, $isReedition, $autoPrint,
                            _('Delivery order'));
        $date = $this->document->getEditionDate();
        $this->data = $this->command->getDataForRTWBL($date);
    }

    // }}}
    // RTWDeliveryOrderGenerator::render() {{{

    /**
     * Construit le document pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderCustomsBlocs();
        $this->_renderContent();
        $this->renderTotal1Bloc();
        $this->renderComment();
        $this->renderFooter();
        return $this->pdf;
    }

    // }}}
    // RTWDeliveryOrderGenerator::_renderContent() {{{

    /**
     * Render du contenu du doc
     * @access protected
     * @return void
     */
    function _renderContent() {
        //cellule désignation personnalisé dans Command.getDataForBL()
        $this->pdf->addText(_('Number of ordered products') . ': ' 
            . $this->command->getNumberOfOrderedProducts());
        $columns = array(
            _('Reference') => 34,
            _('Description') => 103,
            _('Ordered qty') => 13,
            _('Selling unit') => 15,
            _('Delivered Qty') => 13,
            _('To deliver') => 13);
        $this->pdf->tableHeader($columns, 1);
        $this->pdf->tableBody($this->data[0], $columns);
        $this->pdf->ln(8);
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($this->data[0]);
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody(array($this->data[0][$count-1]), $columns);
            $this->pdf->ln(8);
        }
    }

    // }}}
}

// }}}
// InvoiceGenerator {{{

/**
 * InvoiceGenerator.
 * Classe utilisee pour les factures.
 *
 */
class InvoiceGenerator extends CommandDocumentGenerator
{
    // InvoiceGenerator::__construct() {{{

    /**
     * Constructor
     *
     * @param Object $document Invoice
     * @param boolean $isReedition mettre à true s'il s'agit d'une réédition
     * @param boolean $autoPrint true pour impression auto
     * @access protected
     */
    public function __construct($document, $isReedition = false, $autoPrint=false) {
        parent::__construct($document, $isReedition, $autoPrint,
                            _('Invoice'));
    }

    // }}}
    // InvoiceGenerator::render() {{{

    /**
     * Construit la facture pdf
     *
     * @access public
     * @param Object $container InvoiceCollectionGenerator utilise lors d'edition
     * de n factures dans le meme pdf
     * @return PDFDocumentRender Object
     */
    public function render($container=false) {
        $pdfDoc = (!$container)?$this->pdf:$container->pdf;
        $pdfDoc->setFillColor(220);
        $this->renderHeader($pdfDoc);
        $pdfDoc->addPage();  // Apres le renderHeader() !!!!
        if ($container === false) {
            $this->renderAddressesBloc();
        }else {
            $container->renderAddressesBloc();
        }
        $this->renderCustomsBlocs($pdfDoc);
        $this->_renderContent($pdfDoc);
        $this->renderTotal1Bloc($pdfDoc);
        $this->renderSNLotBloc($pdfDoc);
        $this->renderIncoterm($pdfDoc);
        $this->renderTermsOfPayment($pdfDoc);
        $this->renderComment($pdfDoc);
        //$this->renderAppendices($pdfDoc);
        return $pdfDoc;
    }
    // }}}
    // InvoiceGenerator::_renderContent() {{{

    /**
     * Construit le contenu du pdf
     * @access protected
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * @return void
     */
    protected function _renderContent($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;
        //cellule désignation personnalisé dans Invoice.DataForInvoice()
        $columns = array(
            _('Reference')=>25,
            _('Description of goods')=>84,
            _('Qty')=>10,
            _('Unit Price net of tax') . ' ' . $this->currency=>15,
            _('Disc')=>13,
            _('VAT %')=>15,
            _('Total Price net of tax') . ' ' . $this->currency=>28);
        $columnsData = $this->document->DataForInvoice($this->currency);
        $pdfDoc->tableHeader($columns, 1);
        $pdfDoc->tableBody($columnsData, $columns);
        $pdfDoc->ln(8);
        if ($pdfDoc->getY() >= PAGE_HEIGHT_LIMIT) {
            $pdfDoc->addPage();
            $pdfDoc->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($columnsData);
            $pdfDoc->tableHeader($columns, 1);
            $pdfDoc->tableBody(array($columnsData[$count-1]), $columns);
            $pdfDoc->ln(8);
        }
    }

    // }}}
    // InvoiceGenerator::renderTotal1Bloc() {{{

    /**
     * Affiche le premier tableau total de la facture
     * @access protected
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * @return void
     */
    protected function renderTotal1Bloc($pdfDoc=false) {
        require_once('InvoiceItemTools.php');
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;

        if ($pdfDoc->getY() >= PAGE_HEIGHT_LIMIT_TO_TOTAL) {
            $pdfDoc->addPage();
            $pdfDoc->addHeader();
        }
        $columns = array(
            _('Carriage cost') . ' ' . $this->currency          => 25,
            _('Packing charges') . ' ' . $this->currency           => 25,
            _('Insurance charges') . ' ' . $this->currency         => 25,
            _('Disc')                                           => 15,
            _('Total Price net of tax') . ' ' . $this->currency => 30,
            _('Total VAT') . ' ' . $this->currency              => 40,
            _('Total price') . ' ' . $this->currency            => 30
            );
        $pdfDoc->tableHeader($columns, 1);
        $handing = $this->document->getGlobalHanding();
        $handing = DocumentGenerator::formatPercent($handing);

        // Pour l'affichage du detail par taux de tva
        //$hasTVA = $this->document->hasTVA();
        $tvaRateArray = $this->document->getTVADetail();
        // Formatage pour l'affichage
        $tvaToDisplay = '';
        foreach($tvaRateArray as $key => $value) {
            $tvaToDisplay .= DocumentGenerator::formatPercent($key) . ': ' .
                DocumentGenerator::formatNumber($value) . "\n";
        }

        $pdfDoc->tableBody(array(
            array(
                DocumentGenerator::formatNumber($this->document->getPort()),
                DocumentGenerator::formatNumber($this->document->getPacking()),
                DocumentGenerator::formatNumber($this->document->getInsurance()),
                $handing,
                DocumentGenerator::formatNumber($this->document->getTotalPriceHT()),
                $tvaToDisplay,
                DocumentGenerator::formatNumber($this->document->getTotalPriceTTC())
                ))
            );
        $toPay = $this->document->getToPayForDocument();
        $remExcept = '';

        if(($customerRemExcept=$this->command->getCustomerRemExcep())>0){
            $remExcept = _('Personal discount') . " % : " . $customerRemExcept;
        }
        
        // Ajout d'une ligne s'il y a une taxe Fodec
        $fodecTaxRate = $this->document->getFodecTaxRate();
        if ($fodecTaxRate > 0) {
            $fodecTax = $this->document->getTotalPriceHT() * $fodecTaxRate / 100;
            $pdfDoc->tableHeader(
                array(
                    '' => 120,
                    _('FODEC tax') . ' (' . DocumentGenerator::formatPercent($fodecTaxRate) . '): ' 
                    . DocumentGenerator::formatCurrency($this->currency, $fodecTax) => 70
                )
            );
        }
        // Ajout d'une ligne s'il y a un timbre fiscal
        $taxStamp = $this->document->getTaxStamp();
        if ($taxStamp > 0) {
            $pdfDoc->tableHeader(
                array(
                    '' => 120,
                    _('Tax stamp') . ': ' 
                    . DocumentGenerator::formatCurrency($this->currency, $taxStamp) => 70
                )
            );
        }
        // Ajout d'une ligne s'il y a un acompte, et que c'est la 1ere facture
        // pour la commande associee
        $TotalInstalments = $this->command->getTotalInstalments() ;
        if ($TotalInstalments > 0 && $this->document->isFirstInvoiceForCommand()) {
            $pdfDoc->tableHeader(
                array(
                    '' => 120,
                    _('Instalment') . ': ' 
                    . DocumentGenerator::formatCurrency($this->currency, $TotalInstalments) => 70
                    )
                );
        }

        $pdfDoc->tableHeader(
            array(
                $remExcept=>120,
                _('Total to pay') . ': ' . DocumentGenerator::formatCurrency($this->currency, $toPay) => 70
                )
            );
        if (I18N::getLocaleCode() != 'tr_TR') {
            $pdfDoc->tableHeader(
                array(
                    _('In letters') . ': '  . $this->numberWords(
                    I18N::extractNumber(I18N::formatNumber($toPay))) => 190
                )
            );
        }
        if($this->document->getGlobalHanding() > 0) {
            $handingDetail = $this->document->getHandingDetail();
            $handingAmount = _('Global discount amount') . ': ' 
                . DocumentGenerator::formatCurrency($this->currency, $handingDetail['handing']);
            $htWithoutDiscount = _('Total excl. VAT before global discount') . ': ' 
                . DocumentGenerator::formatCurrency($this->currency, $handingDetail['ht']);
            // Le seul pas formatte pour les separateurs de milliers, mais ca semble etre un percent
            if ($handingDetail['handingbyrangepercent'] > 0) {
                $handingAmount .= ' (' . sprintf(I_COMMAND_HANDING, $handingDetail['handingbyrangepercent']) . ')';
            }
            $pdfDoc->tableHeader(
                array($handingAmount=>90, $htWithoutDiscount=>100));
        }
        $pdfDoc->ln(3);
    }

    // }}}
    // InvoiceGenerator::getExpeditorBankDetail() {{{
    /**
     * Récupère les detail de l'adresse de la banque
     * de l'expediteur
     * @access public
     * @return string
     **/
    public function getExpeditorBankDetail(){
        $abd = $this->expeditor->getActorBankDetail();
        if (!($abd instanceof ActorBankDetail)) {
            return '';
        }
        $continue = false;
        $top = $this->supplierCustomer->getTermsOfPayment();
        if (!$top instanceof TermsOfPayment) {
            return '';
        }
        $modalities = array(
            TermsOfPaymentItem::TRANSFER,
            TermsOfPaymentItem::DRAFT,
            TermsOfPaymentItem::PROMISSORY_NOTE
        );
        foreach ($top->getTermsOfPaymentItemCollection() as $item) {
            if (in_array($item->getPaymentModality(), $modalities)) {
                $continue = true;
                break;
            }
        }
        if (!$continue) {
            return '';
        }
        // streettype
        $array = $abd->getBankAddressStreetTypeConstArray();
        $streettype = isset($array[$abd->getBankAddressStreetType()])?
        $array[$abd->getBankAddressStreetType()]:'';
        $data  = sprintf(_("Bank: %s\n"), $abd->getBankName());
        $data .= sprintf("%s %s %s\n", $abd->getBankAddressNo(),
        $streettype, $abd->getBankAddressStreet());
        if ($abd->getBankAddressAdd() != '') {
            $data .= sprintf("%s\n", $abd->getBankAddressAdd());
        }
        $data .= sprintf(
            "%s %s %s\n",
            $abd->getBankAddressCity(),
            $abd->getBankAddressZipCode(),
            $abd->getBankAddressCountry()
        );
        return $data;
    }
    // }}}
    // InvoiceGenerator::getBLFieldValue() {{{

    /**
     * Retourne la valeur à afficher dans l'en-tête des données de la facture
     * pour le champs BL
     *
     * @access public
     * @return string
     **/
    function _getBLFieldValue() {
        $value = "";
        $doCollection = $this->document->getDeliveryOrderCollection();
        $count = $doCollection->getCount();
        for($i = 0; $i < $count; $i++){
            $item = $doCollection->getItem($i);
            $value .= sprintf(_("No %s from %s\n"), $item->getDocumentNo(),
                $item->getEditionDate('localedate_short'));
        }
        return $value;
    }
    // }}}
}

// }}}
// RTWInvoiceGenerator {{{

/**
 * RTWInvoiceGenerator.
 * Classe utilisee pour les factures de commandes produit client en contexte 
 * pret a porter.
 *
 */
class RTWInvoiceGenerator extends InvoiceGenerator
{
    // __construct() {{{

    /**
     * Construit la facture pdf
     *
     * @access public
     * @param Object $container InvoiceCollectionGenerator utilise lors d'edition
     * de n factures dans le meme pdf
     * @return PDFDocumentRender Object
     */
    public function render($container=false) {
        $pdfDoc = (!$container)?$this->pdf:$container->pdf;
        $pdfDoc->setFillColor(220);
        $this->renderHeader($pdfDoc);
        $pdfDoc->addPage();  // Apres le renderHeader() !!!!
        if ($container === false) {
            $this->renderAddressesBloc();
        }else {
            $container->renderAddressesBloc();
        }
        $this->renderCustomsBlocs($pdfDoc);
        $this->_renderContent($pdfDoc);
        $this->renderTotal1Bloc($pdfDoc);
        $this->renderIncoterm($pdfDoc);
        $this->renderTermsOfPayment($pdfDoc);
        $this->renderComment($pdfDoc);
        //$this->renderAppendices($pdfDoc);
        return $pdfDoc;
    }

    // }}}
    // RTWInvoiceGenerator::_renderContent() {{{

    /**
     * Construit le contenu du pdf
     * @access protected
     * @param Object $pdfDoc PDFDocumentRender utilise lors d'edition de n factures
     * @return void
     */
    protected function _renderContent($pdfDoc=false) {
        $pdfDoc = (!$pdfDoc)?$this->pdf:$pdfDoc;

        $pdfDoc->addText(_('Number of ordered products') . ': ' 
            . $this->command->getNumberOfOrderedProducts());
        //cellule désignation personnalisé dans Invoice.DataForInvoice()
        $columns = array(
            _('Reference')=>34,
            _('Description')=>90,
            _('Qty')=>13,
            _('Unit Price net of tax') . ' ' . $this->currency=>15,
            _('Disc')=>13,
            _('Total Price net of tax') . ' ' . $this->currency=>25);
        list($columnsData, $sizes) = $this->document->dataForRTWInvoice($this->currency);
        $pdfDoc->tableHeader($columns, 1);
        // ce truc est vraiment perave, pfff...
        for ($i=0; $i<count($columnsData); $i++) {
            $this->pdf->updateTableInfos($columns);
            $pdfDoc->row($columnsData[$i], $columns);
            if (!isset($sizes[$i]) || empty($sizes[$i])) {
                continue;
            }
            ksort($sizes[$i]);
            $sColumns = array();
            $sColumns[_('Sizes')] = 34;
            $sColumns += array_fill_keys(array_keys($sizes[$i]), 10);
            $pdfDoc->tableHeader($sColumns, 0, 1, array(
                'align'      => 'C',
                'lineHeight' => 4,
                'fontSize'   => 7,
            ));
            $sData = array_values($sizes[$i]);
            array_unshift($sData, _('Quantities'));
            $pdfDoc->row($sData, $sColumns, array(
                'align'      => 'C',
                'lineHeight' => 4,
                'fontSize'   => 7,
            ));
        }
        $pdfDoc->ln(8);
        if ($pdfDoc->getY() >= PAGE_HEIGHT_LIMIT) {
            $pdfDoc->addPage();
            $pdfDoc->addHeader();
            // reaffiche la derniere ligne du tableau pour que le suivant ne
            // soit pas seul.
            $count = sizeof($columnsData);
            $pdfDoc->tableHeader($columns, 1);
            $pdfDoc->tableBody(array($columnsData[$count-1]), $columns);
            $pdfDoc->ln(8);
        }
    }

    // }}}
}

// }}}
// CourseCommandInvoiceGenerator {{{

/**
 * Classe fille pour les commandes de cours avec quelques spécificités
 *
 */
class CourseCommandInvoiceGenerator extends InvoiceGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @access protected
     */
    public function __construct($invoice) {
        parent::__construct($invoice);
    }

    // }}}
    // CourseCommandInvoiceGenerator::buildLeftAddress() {{{

    /**
     * Pas de d'addresse destinataire pour une commande de cours
     *
     * @access public
     * @return void
     */
    public function buildLeftAddress() {
    }

    // }}}
    // CourseCommandInvoiceGenerator::_renderContent() {{{

    /**
     * Le bloc contenant les items de la facture est légèrement différent.
     *
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        $columns = array(
            _('Service')=>117,
            _('Qty')=>17,
            _('Disc')=>17,
            _('VAT %')=>17,
            _('Total Price net of tax') . ' ' . $this->currency=>22);
        $columnsData = $this->document->DataForInvoice($this->currency);
        $this->pdf->tableHeader($columns, 1);
        $this->pdf->tableBody($columnsData, $columns);
        $this->pdf->ln(8);
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($columnsData);
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody(array($columnsData[$count-1]), $columns);
            $this->pdf->ln(8);
        }
    }

    // }}}
    // CourseCommandInvoiceGenerator::renderTotal1Bloc() {{{

    /**
     * Le bloc recapitulant le total est un peu different pour la commande de
     * cours.
     *
     * @access protected
     * @return void
     */
    protected function renderTotal1Bloc() {
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT_TO_TOTAL) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
        }

        $columns = array(
            '       '=>58,
            _('Global discount')=>30,
            _('Total Price net of tax') . ' '  . $this->currency=>33,
            _('Total VAT') . ' ' . $this->currency=>33,
            _('Total price') . ' ' . $this->currency=>36
            );
        $this->pdf->tableHeader($columns, 1);
        $handing = $this->document->getGlobalHanding();
        $handing = DocumentGenerator::formatPercent($handing);
        $this->pdf->tableBody(array(
            array(
                '',
                $handing,
                DocumentGenerator::formatNumber($this->document->getTotalPriceHT()),
                DocumentGenerator::formatNumber($this->document->getTotalPriceTTC() -
                        $this->document->getTotalPriceHT()),
                DocumentGenerator::formatNumber($this->document->getTotalPriceTTC())
                ))
            );
        $toPay = $this->document->getTotalPriceTTC() - $this->command->getTotalInstalments();
        
        // Ajout d'une ligne s'il y a une taxe Fodec
        $fodecTaxRate = $this->document->getFodecTaxRate();
        if ($fodecTaxRate > 0) {
            $fodecTax = $this->document->getTotalPriceHT() * $fodecTaxRate / 100;
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('FODEC tax') . ' (' . DocumentGenerator::formatPercent($fodecTaxRate) . '): ' 
                    . DocumentGenerator::formatCurrency($this->currency, $fodecTax)  => 70
                )
            );
        }
        // Ajout d'une ligne s'il y a un timbre fiscal
        $taxStamp = $this->document->getTaxStamp();
        if ($taxStamp > 0) {
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('Tax stamp') . ': ' 
                    . DocumentGenerator::formatCurrency($this->currency, $taxStamp) => 70
                )
            );
        }
        $this->pdf->tableHeader(
            array(
                '' => 128,
                _('Total to pay') . ': ' 
                . DocumentGenerator::formatCurrency($this->currency, $toPay) => 62
                )
            );
        if (I18N::getLocaleCode() != 'tr_TR') {
            $this->pdf->tableHeader(
                array(
                    _('In letters') . ': '  . $this->numberWords(
                    I18N::extractNumber(I18N::formatNumber($toPay))) => 190
                )
            );
        }
        $this->pdf->ln(3);
    }

    // }}}
} 

// }}}
// ChainCommandInvoiceGenerator {{{

/**
 * ChainCommandInvoiceGenerator.
 * classe utilisée pour des factures de commande de transport.
 *
 */
class ChainCommandInvoiceGenerator extends InvoiceGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @access protected
     */
    public function __construct($invoice) {
        //$this->InvoiceGenerator($invoice);
        parent::__construct($invoice);
    }

    // }}}
    // render() {{{

    /**
     * Construit la facture pdf
     *
     * @access public
     * @param Object $container InvoiceCollectionGenerator utilise lors d'edition
     * de n factures dans le meme pdf
     * @return PDFDocumentRender Object
     */
    public function render($container=false) {
        $pdfDoc = (!$container)?$this->pdf:$container->pdf;
        $pdfDoc->setFillColor(220);
        $this->renderHeader($pdfDoc);
        $pdfDoc->addPage();  // Apres le renderHeader() !!!!
        if ($container === false) {
            $this->renderAddressesBloc();
        }else {
            $container->renderAddressesBloc();
        }
        $this->renderCustomsBlocs($pdfDoc);
        $this->_renderContent($pdfDoc);
        $this->renderPrestationDetail();
        $this->renderTotal1Bloc($pdfDoc);
        $this->renderSNLotBloc($pdfDoc);
        $this->renderIncoterm($pdfDoc);
        $this->renderTermsOfPayment($pdfDoc);
        $this->renderComment($pdfDoc);
        return $pdfDoc;
    }

    // }}}
    // _renderContent() {{{

    /**
     * Le bloc contenant les items de la facture est légèrement différent.
     *
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        $columns = array(
            _('Parcel type')=>40,
            _('Product type')=>40,
            _('Dimensions')=>30,
            _('Unit weight (Kg)')=>35,
            _('Number of parcels')=>45
        );
        $chainCmdItemCol = $this->command->getCommandItemCollection();
        $count = $chainCmdItemCol->getCount();
        $columnsData = array();
        for ($i=0 ; $i<$count ; $i++) {
            $chainCmdItem = $chainCmdItemCol->getItem($i);
            $coverType = $chainCmdItem->getCoverType();
            $productType = $chainCmdItem->getProductType();
            $columnsData[] = array(
                $coverType->getName(),
                $productType->getName(),
                $chainCmdItem->getHeight() .
                    ' * ' . $chainCmdItem->getWidth() .
                    ' * ' . $chainCmdItem->getLength(),
                $chainCmdItem->getWeight(),
                $chainCmdItem->getQuantity()
            );
        }
        $this->pdf->tableHeader($columns, 1);
        $this->pdf->tableBody($columnsData, $columns);
        $this->pdf->ln(8);
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($columnsData);
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody(array($columnsData[$count-1]), $columns);
            $this->pdf->ln(8);
        }
    }

    // }}}
    // renderTotal1Bloc() {{{

    /**
     *
     * @access protected
     * @return void
     */
    protected function renderTotal1Bloc() {
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT_TO_TOTAL) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
        }

        $columns = array(
            _('Packing charges') . ' ' . $this->currency=>30,
            _('Insurance charges') . ' ' . $this->currency=>30,
            _('Disc')=>15,
            _('Total Price net of tax') . ' '  . $this->currency=>45,
            _('Total VAT') . ' ' . $this->currency=>35,
            _('Total incl. VAT') . ' ' . $this->currency=>35
            );
        $this->pdf->tableHeader($columns, 1);
        $handing = $this->document->getGlobalHanding();
        $handing = DocumentGenerator::formatPercent($handing);
        $this->pdf->tableBody(array(
            array(
                DocumentGenerator::formatNumber($this->document->getPacking()),
                DocumentGenerator::formatNumber($this->document->getInsurance()),
                $handing,
                DocumentGenerator::formatNumber($this->document->getTotalPriceHT()),
                DocumentGenerator::formatNumber($this->document->getTotalPriceTTC() -
                        $this->document->getTotalPriceHT()),
                DocumentGenerator::formatNumber($this->document->getTotalPriceTTC())
                ))
            );
        $toPay = $this->document->getToPayForDocument();
        $remExcept = '';

        if(($customerRemExcept=$this->command->getCustomerRemExcep())>0) {
            $remExcept = _('Personal discount') . " % : " .
            $customerRemExcept;
        }
        
        // Ajout d'une ligne s'il y a une taxe Fodec
        $fodecTaxRate = $this->document->getFodecTaxRate();
        if ($fodecTaxRate > 0) {
            $fodecTax = $this->document->getTotalPriceHT() * $fodecTaxRate / 100;
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('FODEC tax') . ' (' . DocumentGenerator::formatPercent($fodecTaxRate) . '): ' 
                    . DocumentGenerator::formatCurrency($this->currency, $fodecTax) => 70
                )
            );
        }
        // Ajout d'une ligne s'il y a un timbre fiscal
        $taxStamp = $this->document->getTaxStamp();
        if ($taxStamp > 0) {
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('Tax stamp') . ': ' 
                    . DocumentGenerator::formatCurrency($this->currency, $taxStamp) => 70
                )
            );
        }
        // Ajout d'une ligne s'il y a un acompte, et que c'est la 1ere facture
        // pour la commande associee
        $instalment = $this->command->getTotalInstalments();
        if ($instalment > 0 && $this->document->isFirstInvoiceForCommand()) {
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('Instalment') . ': ' 
                    . DocumentGenerator::formatCurrency($this->currency, $instalment) => 70
                    )
                );
        }
        $this->pdf->tableHeader(
            array(
                $remExcept => 128,
                _('Total to pay') . ': ' 
                . DocumentGenerator::formatCurrency($this->currency, $toPay) => 62
                )
            );
        if (I18N::getLocaleCode() != 'tr_TR') {
            $this->pdf->tableHeader(
                array(
                    _('In letters') . ': '  . $this->numberWords(
                    I18N::extractNumber(I18N::formatNumber($toPay))) => 190
                )
            );
        }
        $this->pdf->ln(3);
    }

    // }}}
    // renderPrestationDetail() {{{

    /**
     * renderPrestationDetail 
     * 
     * @access public
     * @return void
     */
    public function renderPrestationDetail() {
        $columns = array(
            _('Service')=>60,
            _('Qty')=>24,
            _('Unit Price net of tax') . ' ' . $this->currency=>24,
            _('Disc')=>24,
            _('VAT %')=>24,
            _('Total Price net of tax') . ' ' . $this->currency=>34
        );
        $invoiceItemCol = $this->document->getInvoiceItemCollection();
        $data = array();
        foreach($invoiceItemCol as $ivItem) {
            $tva = $ivItem->getTVA();
            $data[] = array(
                $ivItem->getName(),
                $ivItem->getQuantity(),
                $ivItem->getPrestationCost(),
                $ivItem->getHanding(),
                $tva instanceof TVA ? $tva->getRate(): '',
                $ivItem->getUnitPriceHT());
        }
        if(empty($data)) {
            return true;
        }
        $this->pdf->tableHeader($columns, 1);
        $this->pdf->tableBody($data, $columns);
        $this->pdf->ln(8);
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($data);
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody(array($data[$count-1]), $columns);
            $this->pdf->ln(8);
        }
    }

    // }}}
}

// }}}
// PrestationInvoiceGenerator {{{

/**
 * PrestationInvoiceGenerator.
 * classe utilisée pour des factures de prestation.
 *
 */
class PrestationInvoiceGenerator extends InvoiceGenerator
{
    // PrestationInvoiceGenerator::__construct() {{{

    /**
     * Constructor
     *
     * @param Object PrestationInvoice $invoice la facture de prestation
     * @param boolean $isreedition true si réédition
     */
    public function __construct($invoice, $isReedition=false) {
        //$this->InvoiceGenerator($invoice, $isReedition);
        parent::__construct($invoice, $isReedition);
    }

    // }}}
    // PrestationInvoiceGenerator::render() {{{

    /**
     * lance la construction du pdf.
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderCustomsBlocs();
        $this->_renderContent();
        /*$displayProductDetail = $this->documentModel instanceof DocumentModel?
            $this->documentModel->getDisplayProductDetail():false;
        if($displayProductDetail) {
            $this->_renderDetailForProducts();
        }
        $this->_renderTransportDetails();*/
        $this->renderTotal1Bloc();
        $this->renderComment();
        //$this->_renderACOList();
        //$this->_renderStockageList();
        $this->renderFooter();
        $this->renderDetails();
        return $this->pdf;
    }

    // }}}
    // PrestationInvoiceGenerator::_renderContent() {{{

    /**
     * Le bloc contenant les items de la facture est légèrement différent.
     *
     * @return void
     */
    protected function _renderContent() {
        $columns = array(
            _('Service')=>60,
            _('Qty')=>24,
            _('Unit Price net of tax') . ' ' . $this->currency=>24,
            _('Disc')=>24,
            _('VAT %')=>24,
            _('Total Price net of tax') . ' ' . $this->currency=>34
            );

        $columnsData = $this->document->DataForInvoice($this->currency);
        $this->pdf->tableHeader($columns, 1);
        $this->pdf->tableBody($columnsData, $columns);
        $this->pdf->ln(8);
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($columnsData);
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody(array($columnsData[$count-1]), $columns);
            $this->pdf->ln(8);
        }
    }

    // }}}
    // PrestationInvoiceGenerator::_renderACOList() {{{

    /**
     * Affiche la liste des aco
     *
     * @return void
     */
    private function _renderACOList() {
        $columnsData = $this->document->getDataForACOList();
        if(!empty($columnsData)) {
            $this->pdf->addPage();
            $this->pdf->addHeader();

            $columns = array(
                _('Service') => 30,
                _('Order number')=>33,
                _('Departure actor')=>33,
                _('Arrival actor')=>33,
                _('Date') => 21,
                _('Weight (kg)') => 20,
                _('Volume') => 20
            );
            $this->pdf->Ln();
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody($columnsData, $columns);
            $this->pdf->ln(8);
            if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
                $this->pdf->addPage();
                $this->pdf->addHeader();
                /* reaffiche la derniere ligne du tableau pour que le suivant ne
                * soit pas seul.
                */
                $count = sizeof($columnsData);
                $this->pdf->tableHeader($columns, 1);
                $this->pdf->tableBody(array($columnsData[$count-1]), $columns);
                $this->pdf->ln(8);
            }
        }
    }

    // }}}
    // PrestationInvoiceGenerator::_renderStockageList() {{{

    /**
     * Affiche un tableau si prestation de stockage:
     * (StoreName, LocationName)
     *
     * @return void
     */
    private function _renderStockageList() {
        if (!$this->document->isWithStock()) {
            return true;
        }
        // Les InvoiceItem pour le Stockage (au plus 1 seule, a priori...)
        $invoiItemColl = $this->document->getInvoiceItemCollection(
                array('Prestation.Type' => Prestation::PRESTATION_TYPE_STOCKAGE),
                array(), array('Prestation'));
        $count = $invoiItemColl->getCount();
        for($i = 0; $i < $count; $i++){
            $invItem = $invoiItemColl->getItem($i);
            $olColl = $invItem->getOccupiedLocationCollection(
                    array(),
                    array('Location.Store' => SORT_ASC, 'Location' => SORT_ASC),
                    array('Location'));  // lazy
        }
        $columns = array(_('Store') => 95, _('Location')=> 95);

        $this->pdf->Ln();
        $this->pdf->addText(
            _('Details of locations charged in storage service.'));
        $this->pdf->tableHeader($columns, 1);
        $columnsData = $this->document->getStockageLocationList();
        if (!empty($columnsData)) {
            $this->pdf->tableBody($columnsData, $columns);
        }
    }

    // }}}
    // PrestationInvoiceGenerator::_renderDetailForProducts() {{{

    /**
     * Affiche un détail des produits facturés.
     *
     * Récupère tous les InvoiceItem.LEM.Product et pour chacun d'entre eux
     * regarde si il existe une ProductPrestationCost. Si oui on ajoute un
     * tableau détaillant le calcul du prix pour le Product
     *
     * @return void
     */
    private function _renderDetailForProducts() {
        $header = array(
            _('Order number') => 30,
            _('Reference') => 80,
            _('Moved quantity') => 50,
            _('Unit price excl. VAT')  . ' ' . $this->currency => 30);

        /* pour chaque invoiceItem de la facture, si il est associé à des 
         * mouvements (InvoiceItem.LEM), on affiche un tableau détaillant les 
         * produits mouvementés (LEM.Product)
         */
        $invoiceItemCol = $this->document->getInvoiceItemCollection();
        $counti = $invoiceItemCol->getCount();
        $data = array();
        for($i=0 ; $i<$counti ; $i++) {
            $invoiceItem = $invoiceItemCol->getItem($i);
            $prestation = $invoiceItem->getPrestation();
            $lemCol = $invoiceItem->getLocationExecutedMovementCollection();
            $countj = $lemCol->getCount();
            for($j=0 ; $j<$countj ; $j++) {
                $lem = $lemCol->getItem($j);
                $product = $lem->getProduct();
                $cmdNo = Tools::getValueFromMacro($lem,
                        '%ExecutedMovement.ActivatedMovement.ProductCommand.CommandNo%');
                $data[] = array(
                    $cmdNo,
                    $product->getBaseReference(),
                    $lem->getQuantity(),
                    DocumentGenerator::formatNumber($invoiceItem->getPrestationCost())
                );
            }
        }
        $return = array();
        if(!empty($data)) {
            $return = array(
                'title' => _('Details of products moved for the service ') .
                $prestation->getName(),
                'header' => $header,
                'data' => $data);
        }
        return $return;
    }

    // }}}
    // PrestationInvoiceGenerator::_renderTransportDetails() {{{

    /**
     * _renderTransportDetails 
     * 
     * @access private
     * @return void
     */
    private function _renderTransportDetails() {
        require_once('Objects/ActivatedChainOperation.inc.php');
        require_once('Objects/Task.inc.php');
        
        $invoiceItemCol = $this->document->getInvoiceItemCollection();
        $data = array();
        foreach($invoiceItemCol as $invoiceItem) {
            //echo '### invoiceItemId: ' . $invoiceItem->getId();
            $acoCol = $invoiceItem->getActivatedChainOperationFacturedCollection();
            // #*# CORRECTION A TESTER... experimental...
            // Dans certains cas (!!) on n'a pas de aco, mais des lem!!
/*            if ($acoCol->getCount() == 0) {
                $lemColl = $invoiceItem->getLocationExecutedMovementCollection();
                $lemQties = array();
                foreach($lemColl as $lem) {
                    $cmdNo = Tools::getValueFromMacro($lem,
                        '%ExecutedMovement.ActivatedMovement.ProductCommand.CommandNo%');
                    if (!isset($lemQties[$cmdNo])) {
                        $lemQties[$cmdNo] = 0;
                    }
                    $lemQties[$cmdNo] += $lem->getQuantity();
                    // La date du dernier LEM trouve: <gerard> pour l'instant on ne fait pas mieux
                    $date = $lem->getDate();
                }
                foreach($lemQties as $cmdNo=>$qty) {
                    $data[] = array(
                        $cmdNo,
                        I18n::formatDate($date, I18N::DATE_LONG),
                        '',
                        '',
                        '',
                        $qty,
                        DocumentGenerator::formatNumber($invoiceItem->getPrestationCost()));
                }
            }*/
            // #*# /fin CORRECTION A TESTER... experimental...
            foreach($acoCol as $aco) {
                $depZone = Tools::getValueFromMacro($aco,
                    '%FirstTask.ActorSiteTransition.DepartureSite.Zone%');
                $arrZone = Tools::getValueFromMacro($aco,
                    '%LastTask.ActorSiteTransition.ArrivalSite.Zone%');
                $arrSiteName = Tools::getValueFromMacro($aco,
                    '%LastTask.ActorSiteTransition.ArrivalSite.Name%');
                $date = Tools::getValueFromMacro($aco, '%FirstTask.Begin%');
                $ach = $aco->getActivatedChain();
                $cmiCol = $ach->getCommandItemCollection();
                $cmi = $cmiCol->getItem(0);
                $cmdNo  = $cmi->getCommand()->getCommandNo();
                $qty = $invoiceItem->getQuantity();
// / Debut de bloc commenté le 07/12/2007 pour 'une' correction, et finalement correction annulee le 11/02/2208 
                $qty = 0; 
                // box
                $filter = array(
                    SearchTools::newFilterComponent('ActivatedChainOperation', 
                    'ActivatedChainTask().ActivatedOperation.ActivatedChain.Id',
                        'Equals', $ach->getId(), 1, 'Box'),
                    SearchTools::newFilterComponent('PrestationFactured', '', 'Equals', 1, 1),
                    SearchTools::newFilterComponent('InvoicePrestation', '', 'Equals', 
                        $this->document->getId(), 1));
                $filter = SearchTools::filterAssembler($filter);
                $boxCol = Object::loadCollection('Box', $filter);
                $qty = count($boxCol);
                // lem
                $filter = array(
                    SearchTools::newFilterComponent('LEM',
                        'ExecutedMovement.ActivatedMovement.ActivatedChainTask.ActivatedOperation.ActivatedChain.Id',
                        'Equals', $ach->getId(), 1, 'LocationExecutedMovement'),
                    SearchTools::newFilterComponent('TransportPrestationFactured', '',
                        'Equals', 1, 1),
                    SearchTools::newFilterComponent('InvoicePrestation', '', 'Equals', 
                        $this->document->getId(), 1));
                $filter = SearchTools::filterAssembler($filter);
                $lemCol = Object::loadCollection('LocationExecutedMovement', $filter);
                foreach($lemCol as $lem) {
                    $qty += $lem->getQuantity();
                }

                //echo '  (si ==0 on va chercher le contenu de invoiceItem.qty) qty: ' . $qty . '<br>'; 
                //echo '$qty = $invoiceItem->getQuantity(): ' . $invoiceItem->getQuantity() . '<br>';             
                if($qty == 0) {
                    $qty = $invoiceItem->getQuantity();
                }
// / Fin de bloc commenté pour 'une' correction            
                $data[] = array(
                    $cmdNo,
                    I18n::formatDate($date, I18N::DATE_LONG),
                    $depZone ? $depZone : '',
                    $arrZone ? $arrZone : '',
                    $arrSiteName,
                    $qty,
                    DocumentGenerator::formatNumber($invoiceItem->getPrestationCost()));
            }
        }

        $return = array();
        if(!empty($data)) {
            $header = array(
                _('Order number') => 25,
                _('Date') => 20,
                _('Departure zone') => 35,
                _('Arrival zone') => 35,
                _('Arrival site') => 30,
                _('Qty') => 20,
                _('Unit Price net of tax') . ' ' . $this->currency=>25);
            $return = array(
                'title' => _('Transport operations details'),
                'header' => $header,
                'data' => $data);
        }
        return $return;
    }

    // }}} 
    // PrestationInvoiceGenerator::buildLeftAddress() {{{

    /**
     * pas d'addresse de livraison pour la facture de prestation
     *
     * @access public
     * @return void
     */
    public function buildLeftAddress() {
    }

    // }}}
    // PrestationInvoiceGenerator::renderTotal1Bloc() {{{

    /**
     * Affiche le premier tableau total de la facture
     * @access protected
     * @return void
     */
    protected function renderTotal1Bloc() {
        require_once('InvoiceItemTools.php');

        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT_TO_TOTAL) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
        }
        $columns = array(
            _('Carriage cost') . ' ' . $this->currency          => 25,
            _('Packing charges') . ' ' . $this->currency           => 25,
            _('Insurance charges') . ' ' . $this->currency         => 25,
            _('Disc')                                           => 15,
            _('Total Price net of tax') . ' ' . $this->currency => 30,
            _('Total VAT') . ' ' . $this->currency              => 40,
            _('Total price incl. VAT') . ' ' . $this->currency            => 30
            );
        $this->pdf->tableHeader($columns, 1);
        $handing = $this->document->getGlobalHanding();
        $handing = DocumentGenerator::formatPercent($handing);

        // Pour l'affichage du detail par taux de tva
        //$hasTVA = $this->document->hasTVA();
        $tvaRateArray = $this->document->getTVADetail();
        // Formatage pour l'affichage
        $tvaToDisplay = '';
        foreach($tvaRateArray as $key => $value) {
            $tvaToDisplay .= DocumentGenerator::formatPercent($key) . ': ' .
                DocumentGenerator::formatNumber($value) . "\n";
        }

        $this->pdf->tableBody(array(
            array(
                DocumentGenerator::formatNumber($this->document->getPort()),
                DocumentGenerator::formatNumber($this->document->getPacking()),
                DocumentGenerator::formatNumber($this->document->getInsurance()),
                $handing,
                DocumentGenerator::formatNumber($this->document->getTotalPriceHT()),
                $tvaToDisplay,
                DocumentGenerator::formatNumber($this->document->getTotalPriceTTC())
                ))
            );
        $toPay = $this->document->getToPayForDocument();
        $remExcept = '';

        if(($customerRemExcept=$this->command->getCustomerRemExcep())>0){
            $remExcept = _('Personal discount') . " % : " . $customerRemExcept;
        }
        
        // Ajout d'une ligne s'il y a une taxe Fodec
        $fodecTaxRate = $this->document->getFodecTaxRate();
        if ($fodecTaxRate > 0) {
            $fodecTax = $this->document->getTotalPriceHT() * $fodecTaxRate / 100;
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('FODEC tax') . ' (' . DocumentGenerator::formatPercent($fodecTaxRate) . '): ' 
                    . DocumentGenerator::formatCurrency($this->currency, $fodecTax) => 70
                )
            );
        }
        // Ajout d'une ligne s'il y a un timbre fiscal
        $taxStamp = $this->document->getTaxStamp();
        if ($taxStamp > 0) {
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('Tax stamp') . ': ' 
                    . DocumentGenerator::formatCurrency($this->currency, $taxStamp) => 70
                )
            );
        }
        // Ajout d'une ligne s'il y a un acompte, et que c'et la 1ere facture
        // pour la commande associee
        $instalment = $this->command->getTotalInstalments();
        if ($instalment > 0 && $this->document->isFirstInvoiceForCommand()) {
            $this->pdf->tableHeader(
                array(
                    '' => 120,
                    _('Instalment') . ': ' 
                    . DocumentGenerator::formatCurrency($this->currency, $instalment) => 70
                    )
                );
        }

        $this->pdf->tableHeader(
            array(
                $remExcept=>120,
                _('Total to pay') . ': ' 
                . DocumentGenerator::formatCurrency($this->currency, $toPay) =>70
                )
            );
        if (I18N::getLocaleCode() != 'tr_TR') {
            $this->pdf->tableHeader(
                array(
                    _('In letters') . ': '  . $this->numberWords(
                    I18N::extractNumber(I18N::formatNumber($toPay)))=>190
                )
            );
        }
        if($this->document->getGlobalHanding()>0){
            $handingDetail = $this->document->getHandingDetail();
            $handingAmount = _('Global discount amount') .': ' 
                . DocumentGenerator::formatCurrency($this->currency, $handingDetail['handing']);
            $htWithoutDiscount = _('Total excl. VAT before global discount') . ': ' 
                . DocumentGenerator::formatCurrency($this->currency, $handingDetail['ht']);
            $this->pdf->tableHeader(
                array($handingAmount=>90, $htWithoutDiscount=>100));
        }
        $this->pdf->ln(3);
    }

    // }}}
    // PrestationInvoiceGenerator::renderDetails() {{{

    public function renderDetails() {
        $details = array();
        $displayProductDetail = $this->documentModel instanceof DocumentModel?
            $this->documentModel->getDisplayProductDetail():false;
        if($displayProductDetail) {
            $result = $this->_renderDetailForProducts();
            if(!empty($result)) {
                $details[] = $result;
            }
        }
        $result = $this->_renderTransportDetails();
        if(!empty($result)) {
            $details[] = $result;
        }
        if(!empty($details)) {
            $this->pdf->addPage();
            $this->renderAddressesBloc();
            foreach($details as $index=>$detail) {
                if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT-17) {
                    $this->pdf->addPage();
                    $this->pdf->addHeader();
                }
                $this->pdf->addText($detail['title'], 
                    array('fontSize'=>11, 'lineHeight'=>5));
                $this->pdf->tableHeader($detail['header'], 1);
                $this->pdf->tableBody($detail['data'], $detail['header']);
                $this->pdf->ln();
            }
        }
    }

    // }}} 
}

// }}}
// ToHaveGenerator {{{

/**
 * ToHaveGenerator
 * Classe utilisée pour les avoirs.
 *
 */
class ToHaveGenerator extends DocumentGenerator
{
    // properties {{{

    /**
     * proprietes de classe servant de raccourcis pour les diverses methodes
     */
    public $Customer = false;

    // }}}
    // __construct() {{{

    /**
     * Constructor
     *
     * @param Object $invoice l'objet Invoice
     * @param boolean $isReedition mettre à true s'il s'agit d'une réédition
     * @access protected
     */
    public function __construct($document, $isReedition = false) {
        $this->supplierCustomer = $document->getSupplierCustomer();
        $this->expeditor = $this->supplierCustomer->getSupplier();
        $this->expeditorSite = $this->expeditor->getMainSite();
        $this->destinator = $this->supplierCustomer->getCustomer();
        $this->destinatorSite = $this->destinator->getMainSite();
        $cur = $document->getCurrency();
        parent::__construct($document, $isReedition, true, $cur, _('Credit note'));
    }

    // }}}
    // ToHaveGenerator::render() {{{

    /**
     * Construit la facture pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderCustomsBlocs();
        $this->_renderContent();
        $this->renderFooter();
        return $this->pdf;
    }

    // }}}
    // ToHaveGenerator::buildLeftAddress() {{{

    /**
     * Pas de d'addresse destinataire pour un avoir
     *
     * @access public
     * @return void
     */
    public function buildLeftAddress() {
    }

    // }}}
    // ToHaveGenerator::buildRightAddress() {{{

    /**
     * Affiche l'adresse de droite (par defaut: adresse de facturation).
     *
     * @access public
     * @return void
     */
    protected function buildRightAddress() {
        $site = $this->destinator->getInvoicingSite();
        if ($site instanceof Site) {
            $str = $site->getName() . "\n" . $site->getFormatAddressInfos("\n");
            $this->pdf->rightAdressCaption = _('Billing address') . ': ';
            $this->pdf->rightAdress = $str;
        }
    }

    // }}}
    // ToHaveGenerator::_renderContent() {{{

    /**
     * Tableau 'principal'
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        $columns = array(
            _('Product Reference') => 33,
            _('Description of goods') => 62,
            _('Qty') => 16,
            _('Unit Price net of tax') . ' ' . $this->currency => 23,
            _('Disc') => 10,
            _('Total VAT') . ' ' . $this->currency => 25 ,
            _('Total incl. VAT') . ' ' . $this->currency => 23
            );

        // calcul de la tva
        $tvaRate = Tools::getValueFromMacro($this->document, '%TVA.Rate%');
        $tva = DocumentGenerator::formatNumber($this->document->getTotalPriceHT() * $tvaRate / 100);
        $tvaStr = DocumentGenerator::formatPercent($tvaRate) .' : ' . $tva;

        $columnsData = array(array($this->document->getDocumentNo(),
            _('Credit note'),
            1,
            DocumentGenerator::formatNumber($this->document->getTotalPriceHT()),
            '',
            $tvaStr,
            DocumentGenerator::formatNumber($this->document->getTotalPriceTTC())));
        $this->pdf->tableHeader($columns, 1);
        $this->pdf->tableBody($columnsData, $columns);
        $this->pdf->ln(5);
        if ($this->pdf->getY() >= PAGE_HEIGHT_LIMIT) {
            $this->pdf->addPage();
            $this->pdf->addHeader();
            /* reaffiche la derniere ligne du tableau pour que le suivant ne
            * soit pas seul.
            */
            $count = sizeof($columnsData);
            $this->pdf->tableHeader($columns, 1);
            $this->pdf->tableBody(array($columnsData[$count-1]), $columns);
            $this->pdf->ln(5);
        }
        $this->pdf->tableHeader(array(_('Credit note reason') => 192), 1);
        $this->pdf->tableBody(array(array($this->document->getComment())));
    }

    // }}}
}

// }}}
// PackingListGenerator {{{

/**
 * PackingListGenerator.
 * Classe utilisée pour les listes de colisage.
 *
 */
class PackingListGenerator extends DocumentGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @param Object $invoice l'objet Invoice
     * @param boolean $isReedition mettre à true s'il s'agit d'une réédition
     * @access protected
     */
    public function __construct($document, $isReedition = false) {
        $this->supplierCustomer = $document->getSupplierCustomer();
        $this->expeditor = $this->supplierCustomer->getSupplier();
        $this->expeditorSite = $this->expeditor->getMainSite();
        $this->destinator = $this->supplierCustomer->getCustomer();
        $this->destinatorSite = $this->destinator->getMainSite();
        $this->totalWeight = 0;
        $this->totalVolume = 0;
        parent::__construct($document, $isReedition, true, false, _('Packing list'));
    }

    // }}}
    // PackingListGenerator::render() {{{

    /**
     * Construit la facture pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderCustomsBlocs();
        $this->_renderContent();
        $this->_renderTotalBloc();
        $this->renderFooter();
        return $this->pdf;
    }

    // }}}
    // PackingListGenerator::_renderContent() {{{

    /**
     * Tableau 'principal'
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        //cellule description personalisée dans Box.getContentInfoForPackingList()
        $columns = array(
            _('Reference') => 50,
            _('Description') => 40,
            _('Order') => 20,
            _('Quantity') => 20,
            _('Weight (kg)')  => 20,
            _('Dimensions') => 20,
            _('Volume (l)') => 20,
        );
        $this->pdf->tableHeader($columns, 1);
        $boxCol = $this->document->getBoxCollection();
        foreach ($boxCol as $box) {
            $childrenData = array();
            $totalQty = $totalVolume = $totalWeight = 0;
            $data = $box->getDataForDocument();
            foreach ($data['children'] as $childData) {
                if (!$childData['cmi'] instanceof CommandItem) {
                    continue;
                }
                $reference = $childData['reference'];
                $totalQty += $childData['quantity'];
                $weight = $childData['cmi']->getWeight($childData['quantity']);
                $volume = $childData['cmi']->getVolume($childData['quantity']);
                $totalWeight += $weight;
                $totalVolume += $volume;
                $dimensions = $childData['cmi']->getLength() . 'x' 
                            . $childData['cmi']->getWidth() . 'x' 
                            . $childData['cmi']->getHeight();
                $childrenData[] = array(
                    $childData['reference'],
                    Tools::getValueFromMacro($childData['cmi'], '%Product.ProductType.Name%'),
                    Tools::getValueFromMacro($childData['cmi'], '%Command.CommandNo%'),
                    $childData['quantity'],
                    $totalWeight,
                    $dimensions,
                    $totalVolume,
                );
            }
            $boxWeight = $box->getWeight();
            $boxVolume = $box->getVolume();
            $this->pdf->tableBody(
                array(0 => array(
                    $data['reference'],
                    '',
                    '',
                    $totalQty,
                    $boxWeight ? $boxWeight : $totalWeight,
                    $box->getDimensions(),
                    $boxVolume ? $boxVolume : $totalVolume
                )), 
                $columns, 
                array('fontStyle' => 'B')
            );
            $this->pdf->tableBody($childrenData, $columns);
            $this->totalVolume += $totalVolume;
            $this->totalWeight += $totalWeight;
        }
        $this->pdf->ln(5);
    }

    // }}}
    // PackingListGenerator::_renderTotalBloc() {{{

    /**
     * Affiche le total du poids et du volume
     * @access private
     * @return void
     */
    private function _renderTotalBloc() {
        $this->pdf->tableHeader(
            array(_('Total weight (kg)') . ': ' . DocumentGenerator::formatNumber($this->totalWeight) => 192)
            );
        $this->pdf->tableHeader(
            array(_('Total volume (m3)') . ': ' . DocumentGenerator::formatNumber($this->totalVolume / 1000, 3) => 192)
            );
        $this->pdf->ln(3);
    }

    // }}}
    // PackingListGenerator::renderCustomsBlocs() {{{

    /**
     * On surcharge la méthode DocumentGenerator::renderCustomsBlocs
     * pour afficher les infos de toutes les commandes. Il y a une ligne
     * de donnée par commande. Le contenu des cellules listé dans $unique
     * n'apparait que sur la première ligne.
     *
     * @access public
     * @return void
     */
    public function renderCustomsBlocs() {
        require_once ('Objects/DocumentModelProperty.inc.php');
        $unique = array(DocumentModelProperty::CELL_NO_DOC);
        $dom = $this->document->findDocumentModel();
        $cmdIds = array();
        if($dom instanceof DocumentModel) {
            $boxCol = $this->document->getBoxCollection();
            $CommandCollection = new Collection('Command', false);
            foreach ($boxCol as $box) {
                $tmpcol = $box->getCommandCollection();
                $CommandCollection = $CommandCollection->merge($tmpcol);
            }
            $commandCount = $CommandCollection->getCount();

            $domPropCol = $dom->getDocumentModelPropertyCollection(array('Property'=>0));
            $numberOfProperties = $domPropCol->getCount();
            $numberOfTable = ceil($numberOfProperties / NUMBER_OF_CELLS_PER_TABLE);

            $domMapper = Mapper::singleton('DocumentModelProperty');
            // pour chaque tableau :
            for ($i=1 ; $i<=$numberOfTable ; $i++) {
                // récupérer les 5 documentModelProperty de la table dans l'ordre
                $domPropCol = $domMapper->loadCollection(
                    array('Property' => 0,
                      'DocumentModel' => $dom->getId()),
                    array('Order' => SORT_ASC),
                    array('PropertyType'), NUMBER_OF_CELLS_PER_TABLE, $i);

                $headerColumns = array();
                $dataColumns = array();
                $cells = $domPropCol->getCount();
                $cellsWidth = PAGE_WIDTH / $cells;
                for ($j=0 ; $j<$cells ; $j++) {
                    $property = $domPropCol->getItem($j);
                    // création du header
                    $headerColumns[getDocumentModelPropertyCellLabel(
                    $property->getPropertyType())] = $cellsWidth;
                    // création du contenu
                    for($k=0 ; $k<$commandCount ; $k++) {
                        if ($k>0 && in_array($property->getPropertyType(), $unique)) {
                            $dataColumns[$k][] = '';
                            continue;
                        }
                        $cmd = $CommandCollection->getItem($k);
                        $dataColumns[$k][] = getDocumentModelPropertyCellValue(
                        $property->getPropertyType(), $this, $cmd);
                    }
                }
                $this->pdf->tableHeader($headerColumns, 1);
                $this->pdf->tableBody($dataColumns);
                $this->pdf->ln(3);
                unset($headerColumns, $dataColumns);
            }
        }
    }

    // }}}
}

// }}}
// InvoicesListGenerator {{{

/**
 * InvoicesListGenerator.
 * Sert à générer les relevé de factures simples ou avec lettre de change.
 *
 */
class InvoicesListGenerator extends DocumentGenerator
{
    // properties {{{

    private $_withChangeLetter;
    public $supplierCustomer;

    // }}}
    // __construct() {{{

    /**
      * Constructeur
      *
      * @param boolean $full true pour un relevé avec lettre de change
      * @access public
      * @return void
      */
    public function __construct($document, $full=false) {
        $this->_withChangeLetter = $full;
        $this->supplierCustomer = $document->getSupplierCustomer();
        $this->expeditor = $this->supplierCustomer->getSupplier();
        $this->expeditorSite = $this->expeditor->getMainSite();
        $this->destinator = $this->supplierCustomer->getCustomer();
        $this->destinatorSite = $this->destinator->getMainSite();
        $this->bigTotals = array();
        $this->bigTotals['ht'] = 0;
        $this->bigTotals['ttc'] = 0;
        $this->bigTotals['tva'] = 0;
        $this->bigTotals['toPay'] = 0;
        $cur = $document->getCurrency();
        parent::__construct($document, false, true, $cur,
                            _('Statement of invoices'));
    }

    // }}}
    // InvoicesListGenerator::render() {{{

    /**
      * Effectue le render du doc.
      *
      * @access public
      * @return Object PDFDocumentRender
      */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderCustomsBlocs();
        $this->_renderContent();
        if($this->_withChangeLetter) {
            $this->_renderChangeLetter();
        }
        $this->renderFooter();
        return $this->pdf;
    }

    // }}}
    // InvoicesListGenerator::renderHeader() {{{

    /**
      * Le header contient le logo du supplier (acteur connecté)
      * et le nom du document.
      *
      * @return void
      * @access public
      */
    public function renderHeader() {
        $this->pdf->logo = base64_decode($this->expeditor->getLogo());
        $this->pdf->docTitle = $this->docName;
        //$this->pdf->header(); // inutile: appele par addPage
    }

    // }}}
    // InvoicesListGenerator::buildLeftAddress() {{{

    /**
     * On affiche pas l'adresse de livraison
     *
     * @access public
     * @return void
     */
    public function buildLeftAddress() {
    }

    // }}}
    // InvoicesListGenerator::buildRightAddress() {{{

    /**
     * Affiche l'adresse de droite (par defaut: adresse de facturation).
     *
     * @access public
     * @return void
     */
    protected function buildRightAddress() {
        $site = $this->destinator->getInvoicingSite();
        if ($site instanceof Site) {
            $str = $site->getName() . "\n" . $site->getFormatAddressInfos("\n");
            $this->pdf->rightAdressCaption = _('Billing address') . ': ';
            $this->pdf->rightAdress = $str;
        }
    }

    // }}}
    // InvoicesListGenerator::_renderContent() {{{

    /**
     * Effectue le render du tableau des factures
     *
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        $text = _('Dear customer, please find enclosed details for invoices remaining to pay');
        $endTextFormat = _('for period from %s to %s.');
        $startDate=$this->document->getBeginDate();
        $endDate=$this->document->getEndDate();
        if($startDate && $endDate) {
            $text .= ' ' . sprintf($endTextFormat,
                $this->document->getBeginDate(),
                $this->document->getEndDate());
        }
        $this->pdf->addText($text);

        $columnsHeader = array(
            _('Edition Date') => 30,
            _('Number') => 32,
            _('Total Price net of tax') . ' ' . $this->currency => 32,
            _('Total VAT') . ' ' . $this->currency=> 32,
            _('Total price') . ' ' . $this->currency => 32,
            _('To pay') . ' ' . $this->currency => 32);

        $columnsData = array();

        $invoiceCol = $this->document->getInvoiceCollection();
        $count = $invoiceCol->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $invoice = $invoiceCol->getItem($i);
            $ht = $invoice->getTotalPriceHT();
            $ttc = $invoice->getTotalPriceTTC();
            $tva = $ttc - $ht;
            $toPay = $invoice->getToPay();
            $this->bigTotals['ht'] += $ht;
            $this->bigTotals['ttc'] += $ttc;
            $this->bigTotals['tva'] += $tva;
            $this->bigTotals['toPay'] += $toPay;
            $columnsData[$i] = array(
                I18N::formatDate($invoice->getEditionDate(), I18N::DATE_LONG),
                $invoice->getDocumentNo(),
                DocumentGenerator::formatNumber($ht),
                DocumentGenerator::formatNumber($tva),
                DocumentGenerator::formatNumber($ttc),
                DocumentGenerator::formatNumber($toPay));
        }

        $this->pdf->tableHeader($columnsHeader, 1);
        $this->pdf->tableBody($columnsData, $columnsHeader);

        foreach ($this->bigTotals as $key=>$value) {
            $this->bigTotals[$key] = DocumentGenerator::formatNumber($value);
        }

        $this->pdf->tableHeader(array(
            _('Total') => 62,
            $this->bigTotals['ht'] => 32,
            $this->bigTotals['tva'] => 32,
            $this->bigTotals['ttc'] . ' ' => 32,
            $this->bigTotals['toPay'] . '  ' => 32));
    }

    // }}}
    // InvoicesListGenerator::_renderChangeLetter() {{{

    /**
     * Effectue le render de la lettre de change
     *
     * @access private
     * @return void
     */
    private function _renderChangeLetter() {
        require_once('Objects/ActorBankDetail.php');

        if($this->pdf->getY() > PAGE_HEIGHT_LIMIT_TO_CHANGE_LETTER) {
            $this->pdf->addPage();
        }
        $x = $this->pdf->getX();
        $y = $this->pdf->getY();

        $this->pdf->Cell(PAGE_WIDTH, 140, '', 1);
        $y +=5;
        $x +=5;
        $this->pdf->setXY($x, $y);
        if ($this->pdf->logo != '') {
            $this->pdf->Image($this->pdf->logo, $x, $y, 0, 17, 'png');
        }

        $text = _('Please pay amount stated below for this bill of exchange (that excludes charges) to the account of');

        $this->pdf->setXY(90, $y);
        $this->pdf->addText($text, array('width'=>50, 'border'=>1));

        // ActorBankDetail
        $abdId = Tools::getValueFromMacro($this->destinator,
        '%AccountingType.ActorBankDetail.Id%');
        $actorBankDetail = Object::load('ActorBankDetail', $abdId);
        if (!Tools::isEmptyObject($actorBankDetail)) {
            $streetTypes = ActorBankDetail::getBankAddressStreetTypeConstArray();
            $strType = isset($streetTypes[$actorBankDetail->getBankAddressStreetType()])?
                $streetTypes[$actorBankDetail->getBankAddressStreetType()]:'';
            $addressBankStr = $actorBankDetail->getBankName() . "\n" .
            $actorBankDetail->getBankAddressNo() .', ' .
            $strType . ', ' .
            $actorBankDetail->getBankAddressStreet() . "\n" .
            $actorBankDetail->getBankAddressCity() . ' ' .
            $actorBankDetail->getBankAddressZipCode() . "\n" .
            $actorBankDetail->getBankAddressCountry();
        } else {
            $addressBankStr = '';
        }

        $this->pdf->setXY(145, $y);
        $this->pdf->addText($addressBankStr, array('width'=>50, 'border'=>1));
        $this->pdf->ln();


        $x = $this->pdf->getX();
        $y = $this->pdf->getY();
        $text = 'montant en ' . $this->currency;
        $this->pdf->setXY(170, $y);
        $this->pdf->addText($text);
        $this->pdf->setX(170);
        $this->pdf->addText($this->bigTotals['toPay'], array('border'=>1, 'width'=>25));

        $this->pdf->setXY($x+5, $this->pdf->getY()+5);
        $headerColumns = array(
            _('Amount for control') . ' ' . $this->currency => 25,
            _('Creation date') => 25,
            _('Deadline') => 25,
            _('Reference') => 25);
        $dataColumns[0] = array(
            $this->bigTotals['toPay'],
            date('d/m/Y'),
            ' ',
            $this->destinator->getCode());

        $this->pdf->tableHeader($headerColumns, 1);
        $shapeY = $this->pdf->getY();
        $this->pdf->setX($this->pdf->getX()+5);
        $this->pdf->tableBody($dataColumns);
        $this->pdf->ln();
        $x = $this->pdf->getX();
        $y = $this->pdf->getY();

        // shape
        $this->pdf->setXY(120, $shapeY);
        $this->pdf->cell(6, 5, ' ', 'LBR');
        $this->pdf->setXY(131, $shapeY);
        $this->pdf->cell(6, 5, ' ', 'LBR');
        $this->pdf->setXY(142, $shapeY);
        $this->pdf->cell(23, 5, ' ', 'LBR', 1);

        $this->pdf->setXY($x+5, $y);
        $this->pdf->cell(5, 5, ' ', 'TLB');
        $this->pdf->setXY($x+60, $y);
        $this->pdf->cell(5, 5, ' ', 'TBR');
        $this->pdf->setXY($x+70, $y);
        $this->pdf->cell(5, 5, ' ', 'TBL');
        $this->pdf->setXY($x+120, $y);
        $this->pdf->cell(5, 5, ' ', 'TBR');
        $this->pdf->setXY($x+130, $y);
        $this->pdf->cell(5, 5, ' ', 'TBL');
        $this->pdf->setXY($x+150, $y);
        $this->pdf->cell(5, 5, ' ', 'TBR', 1);

        $this->pdf->ln(3);
        $x = $this->pdf->getX();
        $y = $this->pdf->getY();
        $this->pdf->setX($x+5);
        $headerColumns = array(
            _('Banking house') => 25,
            _('Branch number') => 20,
            _('Account') => 20,
            _('Key') => 10);
        $this->pdf->setX($x+5);
        $dataColumns[0] = array(
            ' ', ' ', ' ', ' ');
        $this->pdf->tableHeader($headerColumns, 1);
        $this->pdf->setX($x+5);
        $this->pdf->tableBody($dataColumns);

        $this->pdf->setXY(95, $y);
        $headerColumns = array(_('Name and address')=>45);
        $dataColumns[0] = array($this->pdf->rightAdress);
        $this->pdf->tableHeader($headerColumns, 1);
        $this->pdf->setX(95);
        $this->pdf->tableBody($dataColumns);

        $this->pdf->setXY(150, $y);
        $headerColumns = array(_('SIRET number')=>45);
        $dataColumns[0] = array($this->destinator->getSiret());
        $this->pdf->tableHeader($headerColumns, 1);
        $this->pdf->setX(150);
        $this->pdf->tableBody($dataColumns);
        $this->pdf->ln(20);

        $text = _('Value in: ');
        $x = $this->pdf->getX();
        $y = $this->pdf->getY();
        $this->pdf->addText($text);
        $this->pdf->Line($x+20, $y+4, $x+50, $y+4);
        $shapeY = $this->pdf->getY();
        $shapeX = $this->pdf->getX();

        $text = _('Acceptance or endorsement');
        $this->pdf->addText($text);

        $text = _('Registered address') . "\n" . _('Stamp duties and signature');
        $this->pdf->setXY(95, $y);
        $this->pdf->addText($text, array('border'=>1,
            'width'=>100,
            'align'=>'C',
            'lineHeight'=>20));

        $this->pdf->SetLineWidth(1);
        $this->pdf->Line($shapeX+40, $shapeY, $shapeX+40, $shapeY+4);
        $this->pdf->Line($shapeX+40, $shapeY+4, $shapeX+42, $shapeY+2);
        $this->pdf->Line($shapeX+40, $shapeY+4, $shapeX+38, $shapeY+2);
    }

    // }}}
}

// }}}
// LogCardGenerator {{{

/**
 * LogCardGenerator.
 * Classe utilisée pour les fiches suiveuses.
 * Traduction du terme "Fiche suiveuse":
 * http://www.dassault-aviation.com/outils/traducteur_resultat.cfm?op=fr&id=F
 *
 */
class LogCardGenerator extends DocumentGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @param Object $ack l'objet ActivatedChainTask
     * @access protected
     */
    public function __construct($command, $achId) {
        // doc fictif car on ne sauve pas ces listes suiveuses
        $document = new AbstractDocument();
        $this->command = $command;
        $docName = _('Log card') . ' (' . $command->getCommandNo() . ')';
        $cur = false; // pas important ici...
        parent::__construct($document, false, true, $cur, $docName);
        $this->pdf->showExpeditor = false;
        $this->ackData = array();

        $this->activatedChain = Object::load('ActivatedChain', $achId);
    }

    // }}}
    // LogCardGenerator::render() {{{

    /**
     * Construit le doc pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->_renderCommandBloc();
        $this->_renderContent();
        //$this->_renderBarcodes();
        return $this->pdf;
    }

    // }}}
    // LogCardGenerator::renderHeader() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function renderHeader() {
        $this->pdf->docTitle = $this->docName;
        $this->pdf->fontSize['HEADER'] = 30;
        //$this->pdf->header();  // inutile: appele par addPage()
    }

    // }}}
    // LogCardGenerator::_renderCommandBloc() {{{

    /**
     * affiche les infos sur la facture et le client et le commercial
     * @access private
     * @return void
     */
    private function _renderCommandBloc() {
        $this->pdf->setXY(10, 28);
        $columnsData = array();  // Les donnees a afficher
        $columns = array(
            _('Order number') => 48,
            _('Wished date') => 48,
            _('Reference(s)') => 47,
            _('Ordered quantity') => 47
        );
        $this->pdf->defaultFontSize['DEFAULT'] = 12;
        $this->pdf->tableHeader($columns, 1);
        $cmiCol = $this->command->getCommandItemCollection();
        $count  = $cmiCol->getCount();
        $columnsData = array();
        for($i = 0; $i < $count; $i++) {
            $cmi = $cmiCol->getItem($i);
            if ($i == 0) {
                $currentData = array(
                    0 => $this->command->getCommandNo(),
                    1 => I18N::formatDate($this->command->getWishedStartDate())
                );
            } else {
                $currentData = array(0 => '', 1 => '');
            }
            $currentData[2]  = Tools::getValueFromMacro($cmi, '%Product.BaseReference%');
            $currentData[3]  = $cmi->getQuantity();
            $columnsData[$i] = $currentData;
        }
        $this->pdf->tableBody($columnsData);
        $this->pdf->ln(5);
        $this->pdf->defaultFontSize['DEFAULT'] = 10;
    }

    // }}}
    // LogCardGenerator::_renderContent() {{{

    /**
     * Tableau 'principal'
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        //$ach = $this->command->getActivatedChain();
        $ach = $this->activatedChain;
        if (!($ach instanceof ActivatedChain)) {
            Template::errorDialog(_('Error: invalid order'), 'javascript:window.close();', BASE_POPUP_TEMPLATE);
            exit(1);
        }
        $consultingContext = in_array('consulting',
            Preferences::get('TradeContext', array()));
        require_once('ProductionTaskValidationTools.php');
        $filter = getValidationTaskFilter();
        $ackCol = $ach->getActivatedChainTaskCollection($filter);
        $count  = $ackCol->getCount();
        for($i = 0; $i < $count; $i++) {
            if($this->pdf->getY() > PAGE_HEIGHT_LIMIT_TO_LOGCARD_BARCODES) {
                $this->pdf->addPage();
                $this->pdf->setXY(10, 28);
            }
            $ack  = $ackCol->getItem($i);
            $ackID = $ack->getId();
            $tsk = $ack->getTask();
            $tskname = $tsk->getName() . ' ' . _('number') . ' ' . $ackID;
            $this->pdf->SetFillColor(220);
            $this->pdf->tableHeader(array($tskname=>190), 1);
            $this->pdf->SetFillColor(240);
            $this->pdf->tableHeader(
                array(
                    _('Parts number')  => 20,
                    _('Expected date') => 25,
                    _('Expected duration') => 23,
                    _('Effective duration') => 23,
                    _('Date and operator') => 33,
                    _('Observations') => 33,
                    _('Used material') => 33,
                ),
                1
            );
            $columnsData = array(
                0  => $ack->getRealQuantity(),
                1  => I18N::formatDate($ack->getBegin()),
                2  => I18N::formatDuration($ack->getDuration()),
                3  => '', // champs libre
                4  => '', // champs libre
                5  => '', // champs libre
                6  => '', // champs libre
            );
            $this->pdf->Row($columnsData, array(), array('lineHeight'=>8));
            // pas de codes barre pour les taches non validables ou si contexte 
            // consulting

            if ($consultingContext || !$tsk->getToBeValidated() || 
                $tsk->getId() == TASK_ASSEMBLY || $tsk->getId() == TASK_SUIVI_MATIERE) {
                $this->pdf->SetFillColor(220);
                $this->pdf->ln(4);
                continue;
            }
            $this->pdf->tableHeader(
                array(
                    _('Start') => 47,
                    _('Pause') => 47,
                    _('Restart') => 48,
                    _('Finish') => 48
                ),
                1
            );
            $lh = 22;
            $this->pdf->Row(array('', '', '', ''), array(), array('lineHeight'=>$lh));
            $this->pdf->SetFillColor(0);
            $y = $this->pdf->getY() - $lh + 1;
            $this->pdf->EAN13(12, $y, sprintf('10%010d', $ackID));
            $this->pdf->EAN13(59, $y, sprintf('11%010d', $ackID));
            $this->pdf->EAN13(107, $y, sprintf('12%010d', $ackID));
            $this->pdf->EAN13(155, $y, sprintf('13%010d', $ackID));
            $instructions = $ack->getInstructions();
            if (!empty($instructions)) {
                $this->pdf->tableHeader(array(_('Instructions: ') . $instructions => 190));
            }
            $this->pdf->SetFillColor(220);
            $this->pdf->ln(4);
        }
    }

    // }}}
}

// }}}
// ForwardingFormGenerator {{{

/**
 * class ForwardingFormGenerator
 * Génère les bordereaux d'expedition
 */
class ForwardingFormGenerator extends DocumentGenerator
{
    // ForwardingFormGenerator::__construct() {{{

    /**
     * ForwardingFormGenerator::ForwardingFormGenerator
     * @param Object $forwardingForm
     * @param boolean $reedit
     * @access public
     * @return void
     */
    public function __construct($forwardingForm, $reedit=false) {
        //Database::connection()->debug=true;
        parent::__construct($forwardingForm, $reedit, false);
        $this->supplierCustomer = $this->document->getSupplierCustomer();
        $this->expeditor = $this->supplierCustomer->getSupplier();
        $this->destinator = $this->supplierCustomer->getCustomer();
        $this->destinatorSite = $this->document->getDestinatorSite();
        $this->expeditorSite = $this->expeditor->getMainSite();
        $this->pdf->Expeditor = $this->expeditor;
        $this->pdf->ExpeditorSite = $this->expeditorSite;

    }

    // }}}
    // ForwardingFormGenerator::render() {{{

    /**
     * Construit la facture pdf
     *
     * @access public
     * @return Object PDFDocumentRender
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->pdf->defaultFontSize['DEFAULT'] = 10;
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderCustomsBlocs();
        $this->_renderContent();
        return $this->pdf;
    }

    // }}}
    // ForwardingFormGenerator::_renderContent() {{{

    protected function _renderContent() {
        $data = array();
        $productIds = array();
        $FFP_products = array();
        $totalWeight = 0;
        $realWeight = 0;

        $transporter = $this->document->getTransporter();
        if($transporter instanceof Actor) {
            $this->pdf->addText(_('Carrier') . ' : ' . $transporter->getName());
        }

        $ffpCol = $this->document->getForwardingFormPackingCollection(
            array('CoverType'=>0));
        $SecondData = array();
        $count = $ffpCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $ffp = $ffpCol->getItem($i);
            $product = $ffp->getProduct();
            if (!($product instanceof Product)) {
                continue;
            }
            $FFP_products[] = $ffp->getProductId();
            $SecondData[] = array($product->getBaseReference(), $ffp->getQuantity());
            $realWeight += $product->getSellUnitWeight() * $ffp->getQuantity();
        }

        $lemCollection = $this->document->getLocationExecutedMovementCollection();
        $count = $lemCollection->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $lem = $lemCollection->getItem($i);
            $product = $lem->getProduct();
            if(!in_array($product->getId(), $FFP_products)) {
                $productIds[$product->getId()] = $product->getBaseReference();
                $qty = $lem->getQuantity();
                $coeff=-1;
                if($lem->getCancelledMovementId()==0) {
                    $coeff=1;
                }
                if(isset($data[$product->getId()])) {
                    $data[$product->getId()]['qty'] += $coeff * $qty;
                } else {
                    $data[$product->getId()]['qty'] = $coeff * $qty;
                }
                $data[$product->getId()]['description'] =
                    $this->renderDescriptionOfGoodsField($product);
                $data[$product->getId()]['unitWeight'] = $product->getSellUnitWeight();
            }
        }

        $formatedData = array();
        foreach ($data as $key=>$value) {
            $formatedData[] = array($productIds[$key],
                $value['description'], $value['qty']);
            $totalWeight += $value['qty'] * $value['unitWeight'];
        }
        $totalWeight = ceil($totalWeight);
        $realWeight = ceil($realWeight) + $totalWeight;

        $header = array(
            _('Reference')  => 60,
            _('Description of goods') => 80,
            _('Quantity') => 50);
        $this->pdf->tableHeader($header, 1);
        $this->pdf->TableBody($formatedData, $header);

        $this->pdf->ln();

        $ffpCol = $this->document->getForwardingFormPackingCollection(array('Product'=>0));
        $data = array();
        $count = $ffpCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $ffp = $ffpCol->getItem($i);
            $coverType = $ffp->getCoverType();
            if ($coverType instanceof CoverType) {
                $data[] = array($coverType->getName(), $ffp->getQuantity());
            }
        }
        if(count($data)>0) {
            $header = array(_('Parcel type') => 100,
                        _('Quantity') => 90);
            $this->pdf->tableHeader($header, 1);
            $this->pdf->TableBody($data, $header);
            $this->pdf->ln();
        }

        if(count($SecondData)>0) {
            $header = array(_('Deposited packings') => 100,
                            _('Quantity') => 90);
            $this->pdf->tableHeader($header, 1);
            $this->pdf->TableBody($SecondData, $header);
        }

        // affichage du poid total des colis et emballages
        $this->pdf->ln();
        $displayTotalWeight = $this->documentModel instanceof DocumentModel?
            $this->documentModel->getDisplayTotalWeight():true;
        if($displayTotalWeight) {
            $this->pdf->tableHeader(
                array(_('Net total weight (Kg)') . ': ' .
                $totalWeight => 190));
            $this->pdf->tableHeader(
                array(_('Raw total weight (Kg)') . ': ' .
                $realWeight => 190));
        }
    }

    // }}}
    // ForwardingFormGenerator::renderCustomsBlocs() {{{

    /**
     * On surcharge la méthode DocumentGenerator::renderCustomsBlocs
     * pour afficher les infos de toutes les commandes. Il y a une ligne
     * de donnée par commande. Le contenu des cellules listé dans $unique
     * n'apparait que sur la première ligne.
     *
     * @access public
     * @return void
     */
    public function renderCustomsBlocs() {
        require_once ('Objects/DocumentModelProperty.inc.php');
        $unique = array(DocumentModelProperty::CELL_NO_DOC);
        $dom = $this->document->findDocumentModel();
        if($dom instanceof DocumentModel) {
            $FFP_products = array();
            $ffpCol = $this->document->getForwardingFormPackingCollection(
                array('CoverType'=>0));
            $count = $ffpCol->getCount();
            for($i=0 ; $i<$count ; $i++) {
                $ffp = $ffpCol->getItem($i);
                $FFP_products[] = $ffp->getProductId();
            }
        // LEM.EXM.ACM.ProductCommand
            $CommandCollection = new Collection();
            $CommandCollection->acceptDuplicate=false;
            $lemCol = $this->document->getLocationexecutedMovementCollection();

            $lemCount = $lemCol->getCount();
            for($i=0 ; $i<$lemCount ; $i++) {
                $lem = $lemCol->getItem($i);
                if(!in_array($lem->getProductId(), $FFP_products)) {
                    $cmdId = Tools::getValueFromMacro($lem, '%ExecutedMovement.ActivatedMovement.ProductCommand.Id%');
                    $productCommand = Object::load('Command',$cmdId);
                    if($cmdId==0) {
                        $productCommand->setCommandNo($this->document->getCommandNo());
                    }
                    $CommandCollection->setItem($productCommand);
                    unset($productCommand);
                }
            }
            $commandCount = $CommandCollection->getCount();

            $domPropCol = $dom->getDocumentModelPropertyCollection(array('Property'=>0));
            $numberOfProperties = $domPropCol->getCount();
            $numberOfTable = ceil($numberOfProperties / NUMBER_OF_CELLS_PER_TABLE);

            $domMapper = Mapper::singleton('DocumentModelProperty');
            // pour chaque tableau :
            for ($i=1 ; $i<=$numberOfTable ; $i++) {
                // récupérer les 5 documentModelProperty de la table dans l'ordre
                $domPropCol = $domMapper->loadCollection(
                    array('Property' => 0,
                      'DocumentModel' => $dom->getId()),
                    array('Order' => SORT_ASC),
                    array('PropertyType'), NUMBER_OF_CELLS_PER_TABLE, $i);

                $headerColumns = array();
                $dataColumns = array();
                $cells = $domPropCol->getCount();
                $cellsWidth = PAGE_WIDTH / $cells;
                for ($j=0 ; $j<$cells ; $j++) {
                    $property = $domPropCol->getItem($j);
                    // création du header
                    $headerColumns[getDocumentModelPropertyCellLabel(
                    $property->getPropertyType())] = $cellsWidth;
                    // création du contenu
                    for($k=0 ; $k<$commandCount ; $k++) {
                        if ($k>0 && in_array($property->getPropertyType(), $unique)) {
                            $dataColumns[$k][] = '';
                            continue;
                        }
                        $cmd = $CommandCollection->getItem($k);
                        $dataColumns[$k][] = getDocumentModelPropertyCellValue(
                        $property->getPropertyType(), $this, $cmd);
                    }
                }
                $this->pdf->tableHeader($headerColumns, 1);
                $this->pdf->tableBody($dataColumns);
                $this->pdf->ln(3);
                unset($headerColumns, $dataColumns);
            }
        }
    }

    // }}}
}

// }}}
// CommandReceiptGenerator {{{

/**
 * classe de génération des récépissés de commandes.
 *
 */
class CommandReceiptGenerator extends CommandDocumentGenerator
{ 
    // CommandReceiptGenerator::__construct() {{{

    /**
     * Constructeur.
     *
     * @param object $command commande
     * @return void
     */
    public function __construct($document, $reedit=false) {
        parent::__construct($document, $reedit, false);
        $commandType = $this->command->getType();
        if ($commandType == Command::TYPE_CUSTOMER) {
            $this->docName = _('Order receipt');
        } else {
            $this->docName = _('Order');
            $this->pdf->logo = base64_decode($this->destinator->getLogo());
        }
        $this->pdf->showExpeditor = false;
    }

    // }}}
    // CommandReceiptGenerator::render() {{{

    /**
     * construit le récépissé
     *
     * @return Object PdfDocumentRender
     */
    public function render() {
        $this->pdf->setFillColor(220);
        $this->renderHeader();
        $this->pdf->addPage(); // apres le renderHeader()!
        $this->renderAddressesBloc();
        $this->renderContent();
        $this->renderTotalBlock();
        $this->renderIncoterm();
        $this->renderTermsOfPayment();
        $this->renderComment();
        $this->renderAppendices();
        return $this->pdf;
    }

    // }}}
    // CommandReceiptGenerator::renderContent() {{{

    /**
     * Génére le contenu du doc :
     * date souhaité, incoterm et tableau des infos de la commande.
     *
     * @return void
     */
    public function renderContent() {
        $wishedDate = I18N::formatDate($this->command->getWishedStartDate());

        if ($this->command->getWishedEndDate() != 0 &&
            $this->command->getWishedEndDate() != 'NULL') {
            $wishedDate = sprintf(
                _('between %s and %s'),
                $wishedDate,
                I18N::formatDate($this->command->getWishedEndDate()
            ));
        }
        $this->pdf->addText(_('Wished date') . ' : ' . $wishedDate, 
            array('fontSize'=>10, 'fontStyle'=>'B'));
        $header = array(_('Reference') => 30,
                        _('Description of goods') => 75,
                        _('Qty')  => 15,
                        _('Unit Price net of tax') . ' ' . $this->currency=>15,
                        _('Disc.')    => 15,
                        _('Amount excl. VAT') . ' ' . $this->currency   => 20,
                        _('Amount incl. VAT') . ' ' . $this->currency  => 20);
        $data = $this->getData();
        $this->pdf->tableHeader($header, 1);
        $this->pdf->tableBody($data, $header);
        $this->pdf->Ln(8);
    }

    // }}}
    // CommandReceiptGenerator::getData() {{{

    protected function getData() {
        $commandType = $this->command->getType();
        $supplierCustomer = $this->command->getSupplierCustomer();
        $supplier = $supplierCustomer->getSupplier();
        $commandItemCol = $this->command->getCommandItemCollection();
        $count = $commandItemCol->getcount();
        $data = array();
        for($i=0 ; $i<$count ; $i++) {
            $commandItem = $commandItemCol->getItem($i);
            $product = $commandItem->getProduct();
            //$productRef = $commandType==Command::TYPE_CUSTOMER?
            //    $product->getBaseReference():
            //    $product->getReferenceByActor($supplier);
            $productRef = $product->getBaseReference();
            $unitType = $product->getBuyUnitType();
            if ($unitType instanceof SellUnitType) {
                $unitType = ' ' . $unitType->getShortName();
            } else {
                $unitType = '';
            }
            $productName = $product->getName();
            if ($commandType == Command::TYPE_SUPPLIER) {
                $productName .= "\n" . _('Purchase reference') . ': '
                              . $product->getReferenceByActor($supplier);
            }
            $data[] = array(
                $productRef,
                $productName,
                $commandItem->getQuantity() . $unitType,
                $commandItem->getPriceHT(),
                /* XXX Commenté par david cf bug 0002626
                $promoRate . ' ' . $symbol,*/
                $commandItem->getHanding(),
                $commandItem->getTotalHT(true),
                $commandItem->getTotalTTC(true)
            );
        }
        foreach ($data as &$array) {
            $array[3] = I18N::formatNumber($array[3]);
            $array[4] = I18N::formatNumber($array[4]);
            $array[5] = I18N::formatNumber($array[5]);
            $array[6] = I18N::formatNumber($array[6]);
        }
        return $data;
    }
    
    // }}}
    // CommandReceiptGenerator::renderTotalBlock() {{{

    /**
     * génère le tableau avec le total, affiche l'accompte et
     * le montant à régler.
     *
     * @return void
     */
    public function renderTotalBlock() {
        $header = array(_('Delivery expenses') . ' ' . $this->currency =>30,
                        _('Packing') . ' ' . $this->currency =>30,
                        _('Insurance') . ' ' . $this->currency =>30,
                        _('Global discount') => 30,
                        _('Amount excl. VAT') . ' ' . $this->currency =>35,
                        _('Amount incl. VAT'). ' ' . $this->currency =>35);
        $ttcTotalPrice = $this->command->getTotalPriceTTC();
        $data = array(array(
            $this->command->getPort(),
            $this->command->getPacking(),
            $this->command->getInsurance(),
            DocumentGenerator::formatPercent($this->command->getHanding()),
            DocumentGenerator::formatNumber($this->command->getTotalPriceHT()),
            DocumentGenerator::formatNumber($ttcTotalPrice)));

        $this->pdf->tableHeader($header, 1);
        $this->pdf->tableBody($data, $header);

        $instalment = DocumentGenerator::formatNumber($this->command->getTotalInstalments());
        if ($ttcTotalPrice < $instalment) {
            $toPay = 0;
        } else {
            $toPay = $ttcTotalPrice - $instalment;
        }
        $this->pdf->setX(150);
        $this->pdf->tableHeader(array(
            _('Instalment') . ' ' . $this->currency . ' : ' . $instalment=>50));
        $this->pdf->setX(150);
        $this->pdf->tableHeader(array(
            _('To pay') . ' ' . $this->currency .' : ' . $toPay=>50));
    }

    // }}}
    // CommandDocumentGenerator::buildRightAddress() {{{

    /**
     * Affiche l'adresse de droite (par defaut: adresse de facturation).
     *
     * @access public
     * @return void
     */
    protected function buildRightAddress() {
        $site = $this->command->getExpeditorSite();
        if ($site instanceof Site) {
            $str = $site->getName() . "\n" . $site->getFormatAddressInfos("\n");
            $this->pdf->additionalRightAddress = $str;
        }
        parent::buildRightAddress();
    }

    // }}}
}

// }}}
// RTWCommandReceiptGenerator {{{

/**
 * classe de génération des récépissés de commandes.
 *
 */
class RTWCommandReceiptGenerator extends CommandReceiptGenerator
{ 
    // RTWCommandReceiptGenerator::renderContent() {{{

    /**
     * Génére le contenu du doc :
     * date souhaité, incoterm et tableau des infos de la commande.
     *
     * @return void
     */
    public function renderContent() {
        $wishedDate = I18N::formatDate($this->command->getWishedStartDate());

        if ($this->command->getWishedEndDate() != 0 &&
            $this->command->getWishedEndDate() != 'NULL') {
            $wishedDate = sprintf(
                _('between %s and %s'),
                $wishedDate,
                I18N::formatDate($this->command->getWishedEndDate()
            ));
        }
        $this->pdf->addText(_('Wished date') . ' : ' . $wishedDate,
            array('fontSize'=>10, 'fontStyle'=>'B'));
        $this->pdf->addText(_('Season') . ' : ' . 
            Tools::getValueFromMacro(
                $this->command,
                '%CommandItem()[0].Product.Model.Season.Name%'
            )
        );
        $this->pdf->addText(_('Number of ordered products') . ': ' 
            . $this->command->getNumberOfOrderedProducts());

        $columns = array(_('Reference') => 30,
                        _('Description of goods') => 75,
                        _('Qty')  => 15,
                        _('Unit Price net of tax') . ' ' . $this->currency=>15,
                        _('Disc.')    => 15,
                        _('Amount excl. VAT') . ' ' . $this->currency   => 20,
                        _('Amount incl. VAT') . ' ' . $this->currency  => 20);
        list($columnsData, $sizes) = $this->getData();
        $this->pdf->tableHeader($columns, 1);
        
        // ce truc est vraiment perave, pfff...
        for ($i=0; $i<count($columnsData); $i++) {
            $this->pdf->updateTableInfos($columns);
            $this->pdf->row($columnsData[$i], $columns);
            if (!isset($sizes[$i]) || empty($sizes[$i])) {
                continue;
            }
            ksort($sizes[$i]);
            $sColumns = array();
            $sColumns[_('Sizes')] = 30;
            $sColumns += array_fill_keys(array_keys($sizes[$i]), 10);
            $this->pdf->tableHeader($sColumns, 0, 1, array(
                'align'      => 'C',
                'lineHeight' => 4,
                'fontSize'   => 7,
            ));
            $sData = array_values($sizes[$i]);
            array_unshift($sData, _('Quantities'));
            
            $this->pdf->row($sData, $sColumns, array(
                'align'      => 'C',
                'lineHeight' => 4,
                'fontSize'   => 7,
            ));
        }
        $this->pdf->Ln(8);
    }

    // }}}
    // RTWCommandReceiptGenerator::getData() {{{

    protected function getData() {
        $commandType = $this->command->getType();
        $supplierCustomer = $this->command->getSupplierCustomer();
        $supplier = $supplierCustomer->getSupplier();
        $commandItemCol = $this->command->getCommandItemCollection();
        $count = $commandItemCol->getcount();
        $registry = array();
        $sizes = array();
        for($i=0 ; $i<$count ; $i++) {
            $commandItem = $commandItemCol->getItem($i);
            $product = $commandItem->getProduct();
            if ($product instanceof RTWMaterial) {
                // commande matière: on appelle getData plutôt
                return array(parent::getData(), array());
            }
            $model   = $product->getModel();
            $productRef = $model->getStyleNumber() . "\n" . $model->getPressName()->toString();
            if (!(($size = $product->getSize()) instanceof RTWSize)) {
                $size = false;
            }
            if (isset($registry[$model->getId()])) {
                $registry[$model->getId()][2] += $commandItem->getQuantity();
                //$registry[$model->getId()][3] += $commandItem->getPriceHT();
                $registry[$model->getId()][4] += $commandItem->getHanding();
                $registry[$model->getId()][5] += $commandItem->getTotalHT();
                $registry[$model->getId()][6] += $commandItem->getTotalTTC();
            } else {
                $registry[$model->getId()] = array(
                    $productRef,
                    $product->getName(),
                    $commandItem->getQuantity(),
                    $commandItem->getPriceHT(),
                    $commandItem->getHanding(),
                    $commandItem->getTotalHT(),
                    $commandItem->getTotalTTC()
                );
            }
            if ($size) {
                $sizes[$model->getId()][$size->getName()] = $commandItem->getQuantity();
            }
        }
        foreach ($registry as $i=>&$array) {
            $model = Object::load('RTWModel', $i);
            if ($commandType == Command::TYPE_CUSTOMER) {
                $legalMentions = $model->getLegalMentions();
                if (!empty($legalMentions)) {
                    $array[1] .= "\n\n" . $legalMentions;
                }
            }
            $array[3] = I18N::formatNumber($array[3]);
            $array[4] = I18N::formatNumber($array[4]);
            $array[5] = I18N::formatNumber($array[5]);
            $array[6] = I18N::formatNumber($array[6]);
        }
        return array(array_values($registry), array_values($sizes));
    }
    
    // }}}
}

// }}}
// ChainCommandReceiptGenerator {{{

/**
 * classe de génération des récépissés de commandes de transport.
 *
 */
class ChainCommandReceiptGenerator extends CommandReceiptGenerator
{
    // ChainCommandReceiptGenerator::__construct() {{{

    /**
     * Constructeur.
     *
     * @param object $command ChainCommand
     */
    public function __construct($document, $reedit=false) {
        parent::__construct($document, $reedit);
        $this->commandNoLabel = _('Order number');
    }

    // }}}
    // ChainCommandReceiptGenerator::renderContent() {{{

    /**
     * Génére le contenu du doc :
     * date souhaité, incoterm et tableau des infos de la commande.
     *
     * @return void
     */
    public function renderContent() {
        require_once('Objects/CommandItem.inc.php');
        require_once('FormatNumber.php');
        // informations :
        $this->pdf->addText($this->commandNoLabel . ' : ' .
            $this->command->getCommandNo());
        // date souhaitée
        $cmdDate = I18N::formatDate($this->command->getCommandDate());
        $startDate = I18N::formatDate($this->command->getWishedStartDate());
        $endDate = $this->command->getWishedEndDate();
        $endDate = $endDate==0?false:I18N::formatDate($endDate);
        $wishedDate = $endDate?
            sprintf(_("between %s and %s"), $startDate, $endDate):
            $startDate;
        if($this->command->getDateType()==DATE_TYPE_DELIVERY) {
            $this->pdf->addText(_('Wished collection date') . ' : ' .
                $wishedDate);
        } else {
            $this->pdf->addText(_('Wished delivery date') . ' : ' .
                $wishedDate);
        }
        // N° d'imputation
        $this->pdf->addText(_('Imputation number or account number') .
            ' : ' . $this->command->getInputationNo());
        // montant à récupérer à la livraison
        $this->pdf->addText(_('Amount to recover on delivery') .
            ' : ' . $this->command->getDeliveryPayment());

        $this->pdf->ln();
        $header = array(
            _('Parcel type')            => 35,
            _('Quantity')                 => 30,
            _('Unit weight') .' (Kg.)' => 30,
            _('Dimensions') . ' (m)'      => 30,
            _('Stackable ratio')              => 30,
            _('Priority dimension')    => 35);
        // items
        $data = array();
        $cmiCollection = $this->command->getCommandItemCollection();
        $count = $cmiCollection->getCount();
        for($i = 0; $i < $count; $i++){
        	$cmi = $cmiCollection->getItem($i);
            $type = $cmi->getCoverType();
            $data[] = array(
                $type instanceof CoverType?$type->toString():'',
                $cmi->getQuantity(),
                $cmi->getWeight(),
                sprintf('%sx%sx%s', $cmi->getWidth(), $cmi->getLength(),
                    $cmi->getHeight()),
                $cmi->getGerbability(),
                getMasterDimensionLabel($cmi->getMasterDimension()));
        }

        $this->pdf->tableHeader($header, 1);
        $this->pdf->tableBody($data, $header);
        $this->pdf->Ln(8);
    }

    // }}}
    // ChainCommandReceiptGenerator::renderTotalBlock() {{{

    /**
     * génère le tableau avec le total, affiche l'accompte et
     * le montant à régler.
     *
     * @return void
     */
    public function renderTotalBlock() {
        $header = array(
            _('Packing') . ' ' . $this->currency => 30,
            _('Insurance') . ' ' . $this->currency => 30,
            _('VAT') . ' ' . $this->currency => 31,
            _('Amount incl. VAT') . ' ' . $this->currency => 33,
            _('Instalment') . ' ' . $this->currency => 33,
            _('To pay') . ' ' . $this->currency => 33);
        $instalment = $this->command->getTotalInstalments();
        $toPay = DocumentGenerator::formatNumber($this->command->getTotalPriceTTC()-$instalment);
        $data = array(array(
            $this->command->getPacking(),
            $this->command->getInsurance(),
            DocumentGenerator::formatNumber($this->command->getTotalPriceTTC() -
                $this->command->getTotalPriceHT()),
            DocumentGenerator::formatNumber($this->command->getTotalPriceTTC()),
            $instalment?DocumentGenerator::formatNumber($instalment):'0',
            $toPay));

        $this->pdf->tableHeader($header, 1);
        $this->pdf->tableBody($data, $header);
    }

    // }}}
}

// }}}
// EstimateGenerator {{{

/**
 * classe de génération des devis pour les commandes produits
 *
 */
class EstimateGenerator extends CommandReceiptGenerator
{
    // EstimateGenerator::__construct() {{{

    /**
     * Constructeur.
     *
     * @param object $command commande
     * @return void
     */
    public function __construct($document, $reedit=false) {
        parent::__construct($document, $reedit);
        $this->docName = _('Estimate');
    }

    // }}}
}

// }}}
// RTWEstimateGenerator {{{

/**
 * classe de génération des devis pour les commandes produits
 *
 */
class RTWEstimateGenerator extends RTWCommandReceiptGenerator
{
    // RTWEstimateGenerator::__construct() {{{

    /**
     * Constructeur.
     *
     * @param object $command commande
     * @return void
     */
    public function __construct($document, $reedit=false) {
        parent::__construct($document, $reedit);
        $this->docName = _('Estimate');
    }

    // }}}
}

// }}}
// ChainCommandEstimateGenerator {{{

/**
 * classe de génération des devis pour les commandes de transport
 *
 */
class ChainCommandEstimateGenerator extends ChainCommandReceiptGenerator
{
    // ChainCommandEstimateGenerator::__construct() {{{

    /**
     * Constructeur.
     *
     * @param object $command commande
     * @return void
     */
    public function __construct($document, $reedit=false) {
        parent::__construct($document, $reedit);
        $this->docName = _('Estimate');
        $this->commandNoLabel = _('Estimate number');
    }

    // }}}
}

// }}}
// InvoiceCollectionGenerator {{{

/**
 * InvoiceCollectionGenerator.
 * Classe utilisée pour imprimer une série de factures dans le meme pdf, avec
 * gestion correcte de la pagination par facture.
 *
 */
class InvoiceCollectionGenerator extends CommandDocumentGenerator
{
    // properties {{{

    /**
     * La collection de factures a imprimer dans le meme pdf
     * @var string
     */
    public $invoiceColl = false;

    // }}}
    // __construct() {{{

    /**
     * Constructor
     *
     * @param Object $document Collection of Invoice
     * @param boolean $isReedition mettre à true s'il s'agit d'une réédition
     * @param boolean $autoPrint true pour impression auto
     * @access protected
     */
    public function __construct($invoiceColl) {
        $this->invoiceColl = $invoiceColl;
        $this->pdf = new PDFDocumentRender(false, false);
    }

    // }}}
    // InvoiceCollectionGenerator::render() {{{

    /**
     * Construit la facture pdf
     *
     * @access public
     * @return PDFDocumentRender Object
     */
    public function render() {
        $documentColl = $this->invoiceColl;
        $count = $documentColl->getCount();
        for($i = 0; $i < $count; $i++) {
            $this->pdf->StartPageGroup();  // pour les pageGroup
            $invoice = $documentColl->getItem($i);
            $generator = new InvoiceGenerator($invoice);
            // Les 4 lignes suivantes pour la construction de header
            $this->document = $invoice;
            $this->command = $invoice->getCommand();
            $this->expeditor = $this->command->getExpeditor();
            $this->expeditorSite = $this->command->getExpeditorSite();
            $this->destinator = $this->command->getDestinator();
            $this->destinatorSite = $this->command->getDestinatorSite();
            $this->supplierCustomer = $this->command->getSupplierCustomer();
            $this->pdf->Command = $generator->command;
            $this->pdf->Expeditor = $generator->expeditor;
            $this->pdf->ExpeditorSite = $generator->expeditorSite;

            // On passe ici le $generator en param au render() pour ne pas agir
            // sur $generator->pdf, mais $this->pdf
            $generator->render($this);  // $this->pdf
        }
        return $this->pdf;
    }

    // }}}
}

// }}}
// WorksheetGenerator {{{

/**
 * WorksheetGenerator.
 * Classe utilisée pour les fiches techniques.
 *
 */
class WorksheetGenerator extends DocumentGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @param  object $model l'objet RTWModel
     * @access protected
     */
    public function __construct($modelCollection) {
        // doc fictif car on ne sauve pas ces fiches suiveuses
        $document = new AbstractDocument();
        $cur = false; // pas important ici...
        parent::__construct($document, false, false, $cur, '');
        $this->pdf->showExpeditor   = false;
        $this->pdf->showPageNumbers = false;
        $this->pdf->showEditionDate = false;
        $this->pdf->setAutoPageBreak(true, 10);
        $this->modelCollection = $modelCollection;
        $this->model = false;
    }

    // }}}
    // WorksheetGenerator::render() {{{

    /**
     * Construit le doc pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        foreach ($this->modelCollection as $model) {
            $this->model = $model;
            $this->renderHeader();
            $this->pdf->addPage(); // apres le renderHeader()!
            $infos = ImageManager::getFileInfo(md5($this->model->getImage()));
            if (is_array($infos) && !empty($infos['data'])) {
                list(,$type) = explode('/', $infos['mimetype']);
		        $this->pdf->image($infos['data'], 90, 10, 110, 0, $type);
            }
            $this->_renderContent();
        }
        return $this->pdf;
    }

    // }}}
    // WorksheetGenerator::renderHeader() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function renderHeader() {
        $this->pdf->docTitle = $this->docName;
        $this->pdf->fontSize['HEADER'] = 30;
        $dbOwner = Auth::getDatabaseOwner();
        $this->pdf->logo = base64_decode($dbOwner->getLogo());
        //$this->pdf->header();  // inutile: appele par addPage()
    }

    // }}}
    // WorksheetGenerator::_renderContent() {{{

    /**
     * Tableau 'principal'
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        $this->pdf->Ln();
        $this->pdf->addText(
            _('Worksheet') . ' ' . $this->model->toString(),
            array('fontSize'=>13, 'lineHeight'=>6)
        );
        $this->pdf->Ln(50);
        $this->pdf->addText(
            _('Date') . ': ' . I18N::formatDate(time(), I18N::DATE_LONG),
            array('fontSize'=>10, 'lineHeight'=>4)
        );
        if ($this->model->getSeason() instanceof RTWSeason) {
            $this->pdf->addText(
                _('Season') . ': ' . $this->model->getSeason()->toString(),
                array('fontSize'=>10, 'lineHeight'=>4)
            );
        }
        if ($this->model->getManufacturer() instanceof Actor) {
            $this->pdf->addText(
                _('Manufacturer') . ': ' . $this->model->getManufacturer()->toString(),
                array('fontSize'=>10, 'lineHeight'=>4)
            );
        }
        $this->pdf->addText(
            _('Style number') . ': ' . $this->model->getStyleNumber(),
            array('fontSize'=>10, 'lineHeight'=>4)
        );
        $this->pdf->addText(
            _('Description') . ': ' . $this->model->getDescription(),
            array('fontSize'=>10, 'lineHeight'=>4)
        );
        $this->pdf->Ln();
        $items = array(
            'ConstructionType' => _('Construction type'),
            'ConstructionCode' => _('Construction code'),
            'Shape'            => _('Shape'),
        );
        foreach ($items as $k => $v) {
            $getter = 'get' . $k;
            if (is_object($this->model->$getter()) && !($this->model->$getter() instanceof Exception)) {
                $this->pdf->tableHeader(array(
                    $v => 35, 
                    $this->model->$getter()->toString() => 155),
                    0, 1, array('fontSize' => 8));
            }
        }
        $items = $this->model->getMaterialProperties();
        $products = $this->model->getRTWProductCollection();
        foreach ($items as $attrName => $label) {
            $getter = 'get' . $attrName;
            $mat    = $this->model->$getter();
            if ($mat instanceof RTWMaterial) {
                $value = $mat->toString();
                $qtyGetter = 'get' . $attrName . 'Quantity';
                $qty = method_exists($this->model, $qtyGetter) ? $this->model->$qtyGetter() : '';
                $unitType = $mat->getBuyUnitType();
                if ($unitType instanceof SellUnitType) {
                    /*
                    $unitQty = $mat->getBuyUnitQuantity();
                    if (is_numeric($qty) && $unitQty > 0) {
                        $qty *= $unitQty;
                    }
                    */
                    $qty .= ' ' . $unitType->getShortName();
                }
            } else {
                $value = _('N/A');
                $qty   =  '';
            }
            $this->pdf->tableHeader(array($label => 35, $value => 130, (string)$qty => 25),
                0, 1, array('fontSize' => 8));
        }
        if ($this->model->getLabel() instanceof RTWLabel) {
            $this->pdf->tableHeader(array(
                _('Label (griffe)') => 35, 
                $this->model->getLabel()->toString() => 155),
                0, 1, array('fontSize' => 8));
        }
        $sizes = $this->model->getSizeCollection();
        if (count($sizes) > 0) {
            $this->pdf->tableHeader(array(
                _('Available sizes') => 35,
                implode(', ', array_values($sizes->toArray())) => 155), 
                0, 1, array('fontSize' => 8));
        }
        $this->pdf->tableHeader(array(
            _('Observations') => 35,
            $this->model->getComment() => 155), 
            0, 1, array('fontSize' => 8));
    }

    // }}}
}

// }}}
// LookbookGenerator {{{

/**
 * LookbookGenerator.
 * Classe utilisée pour les fiches techniques.
 *
 */
class LookbookGenerator extends WorksheetGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @param  object $model l'objet RTWModel
     * @access protected
     */
    public function __construct($modelCollection, $zoneId) {
        // doc fictif car on ne sauve pas ces fiches suiveuses
        $document = new AbstractDocument();
        $cur = false; // pas important ici...
        parent::__construct($document, false, false, $cur, '');
        $this->pdf->showExpeditor   = false;
        $this->pdf->showPageNumbers = false;
        $this->pdf->showEditionDate = false;
        $this->pdf->setAutoPageBreak(true, 10);
        $this->modelCollection = $modelCollection;
        $this->zoneId = $zoneId;
        $this->model = false;
    }

    // }}}
    // LookbookGenerator::render() {{{

    /**
     * Construit le doc pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $this->pdf->SetFillColor(220);
        $this->renderHeader();
        foreach ($this->modelCollection as $model) {
            $this->pdf->addPage(); // apres le renderHeader()!
            $this->model = $model;
            $infos = ImageManager::getFileInfo(md5($this->model->getColorImage()));
            if (is_array($infos) && !empty($infos['data'])) {
                list(,$type) = explode('/', $infos['mimetype']);
		        $this->pdf->image($infos['data'], 90, 8, 110, 0, $type);
            }
            $this->_renderContent();
        }
        return $this->pdf;
    }

    // }}}
    // LookbookGenerator::renderHeader() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function renderHeader() {
        $this->pdf->docTitle = $this->docName;
        $this->pdf->fontSize['HEADER'] = 30;
        $dbOwner = Auth::getDatabaseOwner();
        $this->pdf->logo = base64_decode($dbOwner->getLogo());
        //$this->pdf->header();  // inutile: appele par addPage()
    }

    // }}}
    // LookbookGenerator::_renderContent() {{{

    /**
     * Tableau 'principal'
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->addText(
            _('Lookbook') . ' ' . $this->model->toString(),
            array('fontSize'=>14, 'lineHeight'=>8)
        );
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->addText(
            _('Date') . ': ' . I18N::formatDate(time(), I18N::DATE_LONG),
            array('fontSize'=>12, 'lineHeight'=>5)
        );
        $items = array(
            'Season'      => _('Season'),
            'Shape'       => _('Shape'),
            'PressName'   => _('Press name'),
            'Description' => _('Description'),
            'HeelHeight'  => _('Heel Height'),
        );
        foreach ($items as $k => $v) {
            $getter = 'get' . $k;
            $obj = $this->model->$getter();
            if ($obj instanceof Object || is_string($obj)) {
                $this->pdf->addText(
                     $v . ': ' . (($obj instanceof Object) ? $obj->toString() : $obj),
                     array('fontSize'=>12, 'lineHeight'=>5)
                );
            }
        }
        $sizes = $this->model->getSizeCollection();
        if (count($sizes) > 0) {
            $this->pdf->addText(
                 _('Available sizes') . ': ' .
                 implode(', ', array_values($sizes->toArray())), 
                 array('fontSize'=>12, 'lineHeight'=>5)
            );
        }
        $this->pdf->Ln();
        $this->pdf->Ln();
        $products = $this->model->getRTWProductCollection();
        if (count($products) > 0) {
            $product = $products->getItem(0);
            $this->pdf->tableHeader(array(
                _('Style number')      => 18,
                _('Material 1')        => 24,
                _('Material 2')        => 24,
                _('Material 3')        => 24,
                _('Accessory 1')       => 24,
                _('Accessory 2')       => 24,
                _('Accessory 3')       => 24,
                _('Price')             => 14,
                _('Recommended price') => 14
            ), 0);
            $pbcCol = $product->getPriceByCurrencyCollection(array(
                'PricingZone' => $this->zoneId
            ));
            $padding = '';
            $rprice  = '';
            $price   = '';
            foreach ($pbcCol as $pbc) {
                $rprice .= $padding . I18N::formatCurrency(
                    TextTools::entityDecode($pbc->getCurrency()->getSymbol()),
                    $pbc->getRecommendedPrice()
                );
                $price  .= I18N::formatCurrency(
                    TextTools::entityDecode($pbc->getCurrency()->getSymbol()),
                    $pbc->getPrice()
                );
                $padding = "\n";
            }
            $mat1 = ($m = $this->model->getMaterial1()) instanceof RTWMaterial ?
                $m->getCommercialNameAndColor() : '';
            $mat2 = ($m = $this->model->getMaterial2()) instanceof RTWMaterial ?
                $m->getCommercialNameAndColor() : '';
            $mat3 = ($m = $this->model->getMaterial3()) instanceof RTWMaterial ?
                $m->getCommercialNameAndColor() : '';
            $acc1 = ($m = $this->model->getAccessory1()) instanceof RTWMaterial ?
                $m->getCommercialNameAndColor() : '';
            $acc2 = ($m = $this->model->getAccessory2()) instanceof RTWMaterial ?
                $m->getCommercialNameAndColor() : '';
            $acc3 = ($m = $this->model->getAccessory3()) instanceof RTWMaterial ?
                $m->getCommercialNameAndColor() : '';

            $this->pdf->tableBody(array(0 => array(
                $this->model->getStyleNumber(),
                $mat1,
                $mat2,
                $mat3,
                $acc1,
                $acc2,
                $acc3,
                $price,
                $rprice
            )));
        }
    }

    // }}}
}

// }}}
// ProductLabelGenerator {{{

/**
 * ProductLabelGenerator.
 * Classe utilisée pour les fiches techniques.
 *
 */
class ProductLabelGenerator extends DocumentGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @access protected
     */
    public function __construct($productInfo) {
        // doc fictif car on ne sauve pas ces fiches suiveuses
        $document = new AbstractDocument();
        $cur = false; // pas important ici...
        parent::__construct($document, false, false, $cur, '');
        $this->pdf->showExpeditor   = false;
        $this->pdf->showPageNumbers = false;
        $this->pdf->showEditionDate = false;
        $this->pdf->setAutoPageBreak(true, 0);
        $this->productInfo = $productInfo;
    }

    // }}}
    // ProductLabelGenerator::render() {{{

    /**
     * Construit le doc pdf
     *
     * @access public
     * @return void
     */
    public function render() {

        $i = 0;
        $cache = array();

        $PageHeight = 296 ;
        $PageWidth = 210 ;

        $PageMargeTop = 4 ;
        $PageMargeBottom = 4 ;
        $PageMargeLeft = 4 ;
        $PageMargeRight = 4 ;

        $StickerMargeLeft = 2 ;
        $StickerMargeRight= 2 ;
        $StickerMargeTop = 2 ;
        $StickerMargeBottom = 2 ;
        $StickerPerLine = 2 ;
        $StickerPerRow = 6 ;
        $StickerSpacing = 0 ;

        $CodeHeight = 3 ;
        $CodeWidth = 50 ;
        $LineHeight = 3 ;

        $DocumentWidth = $PageWidth - ($PageMargeLeft + $PageMargeRight) ;
        $StickerWidth = $DocumentWidth / $StickerPerLine ;
        $StickerRealWidth = $StickerWidth - ($StickerMargeLeft + $StickerMargeRight) ;
        $PictureWidth = $StickerRealWidth - $CodeWidth - 20 ;

        $DocumentHeight = $PageHeight - $PageMargeTop - $PageMargeBottom ;
        $StickerHeight = ($DocumentHeight - (($StickerPerRow - 1) * $StickerSpacing )) / 6 ;
        $StickerRealHeight = $StickerHeight - $StickerMargeTop - $StickerMargeBottom ;
        $PictureHeight = $StickerRealHeight - 20 ;

        foreach ($this->productInfo as $array) {

            list($product, $qty) = $array;
            for ($j=0; $j<$qty; $j++) {

                if ($i == 0 || ($i%12==0)) {
                    $x = $PageMargeLeft ;
                    $y  = $PageMargeTop +$StickerMargeTop ;
                    $newPage = true;
                    $this->pdf->addPage();
                } else {
                    $newPage = false;
                }


                if ($i % 2 == 0) {
                    $x = $PageMargeLeft ;
                    if (!$newPage) $y += $StickerHeight+$StickerMargeTop ;
                } else {
                    $x = $PageWidth / 2 ;
                }

                $x += $StickerMargeLeft ;

                $i++;

                if (($model = $product->getModel()) instanceof RTWModel) {
                    $mid = $model->getId();
                    if (!isset($cache[$mid])) {
                        $cache[$mid] = array();

                        // image du produit
                        if ($model->getImage()) {
                            $infos = ImageManager::getFileInfo(md5($model->getImage()));
                            if ($infos['width'] > $infos['height']) {
                                $cache[$mid]['h'] = 0;
                            } else {
                                $cache[$mid]['w'] = 0;
                            }

                            $cache[$mid]['w'] = $PictureWidth;
                            $cache[$mid]['h'] = $PictureHeight;

                            if (is_array($infos) && !empty($infos['data'])) {
                                list(,$type) = explode('/', $infos['mimetype']);
                                $cache[$mid]['type'] = $type;
                                $cache[$mid]['data'] = $infos['data'];
                            }
                        }

                        // style number
                        $cache[$mid]['pressname'] = $model->getPressName()->toString();
                    }

                    // Image
                    $this->pdf->setXY($x, $y);
                    if ($model->getImage()) {
                        $this->pdf->image($cache[$mid]['data'], $x, $y,
                            $PictureWidth, $PictureHeight, $cache[$mid]['type']);
                    }

                    // Nom Presse
                    $this->pdf->setXY($x+$PictureWidth-1 , $y);
                    $this->pdf->addText(
                        trim($cache[$mid]['pressname']),
                        array('fontSize' => 8, 'lineHeight' => 3, 'align' => 'L')
                    ); 
                    
                }

                // Taille
                if (($size = $product->getSize()) instanceof RTWSize) {
                    $this->pdf->setXY($x + $PictureWidth + $CodeWidth , $y);
                    $this->pdf->addText( 
                        $size->getName(),
                        array('fontSize' => 10, 'lineHeight' => $LineHeight , 'fontStyle' => 'B', 'align' => 'L')
                    );
                }
                // reference 
                $this->pdf->setXY($x + $PictureWidth -1, $y + $LineHeight-0.3 );
                $this->pdf->addText( 
                    $product->getBaseReference(),
                    array('fontSize' => 7, 'lineHeight' => $LineHeight-0.3, 'align' => 'L')
                );

                // codes barre Code 128 + EAN 13
                $this->pdf->Code128($x + $PictureWidth, $y + 6, $product->getBaseReference(), $CodeWidth , $CodeHeight );
                if ($product->getEAN13Code()) {
                    $this->pdf->EAN13($x + $PictureWidth, $y + 10, $product->getEAN13Code(), 10);
                }

                // ligne descriptive ...
                $this->pdf->setXY($x, $y+$PictureHeight);
                $this->pdf->addText(
                    $product->getName(),
                    array('fontSize' => 7, 'lineHeight' => $LineHeight, 'width' => $StickerRealWidth, 'align' => 'C')
                );
            }
        }
        return $this->pdf;
    }

    // }}}
    // ProductLabelGenerator::_renderContent() {{{

    /**
     * Tableau 'principal'
     * @access protected
     * @return void
     */
    protected function _renderContent() {
        $this->pdf->Ln();
        $this->pdf->addText(
            _('Worksheet') . ' ' . $this->model->toString(),
            array('fontSize'=>14, 'lineHeight'=>8)
        );
        $this->pdf->Ln(50);
        $this->pdf->addText(
            _('Date') . ': ' . I18N::formatDate(time(), I18N::DATE_LONG),
            array('fontSize'=>12, 'lineHeight'=>5)
        );
        if ($this->model->getSeason() instanceof RTWSeason) {
            $this->pdf->addText(
                _('Season') . ': ' . $this->model->getSeason()->toString(),
                array('fontSize'=>12, 'lineHeight'=>5)
            );
        }
        if ($this->model->getManufacturer() instanceof Actor) {
            $this->pdf->addText(
                _('Manufacturer') . ': ' . $this->model->getManufacturer()->toString(),
                array('fontSize'=>12, 'lineHeight'=>5)
            );
        }
        $this->pdf->addText(
            _('Style number') . ': ' . $this->model->getStyleNumber(),
            array('fontSize'=>12, 'lineHeight'=>5)
        );
        $this->pdf->addText(
            _('Description') . ': ' . $this->model->getDescription(),
            array('fontSize'=>12, 'lineHeight'=>5)
        );
        $this->pdf->Ln();
        $items = array(
            'ConstructionType' => _('Construction type'),
            'ConstructionCode' => _('Construction code'),
            'Shape'            => _('Shape'),
        );
        foreach ($items as $k => $v) {
            $getter = 'get' . $k;
            if (is_object($this->model->$getter()) && !($this->model->$getter() instanceof Exception)) {
                $this->pdf->tableHeader(array(
                    $v => 35, 
                    $this->model->$getter()->toString() => 155),
                0);
            }
        }
        $items = $this->model->getMaterialProperties();
        $products = $this->model->getRTWProductCollection();
        foreach ($items as $attrName => $label) {
            $getter = 'get' . $attrName;
            $mat    = $this->model->$getter();
            if ($mat instanceof RTWMaterial) {
                $value = $mat->toString();
                $qtyGetter = 'get' . $attrName . 'Quantity';
                $qty = method_exists($this->model, $qtyGetter) ? $this->model->$qtyGetter() : '';
                $unitType = $mat->getBuyUnitType();
                if ($unitType instanceof SellUnitType) {
                    /*
                    $unitQty = $mat->getBuyUnitQuantity();
                    if (is_numeric($qty) && $unitQty > 0) {
                        $qty *= $unitQty;
                    }
                    */
                    $qty .= ' ' . $unitType->getShortName();
                }
            } else {
                $value = _('N/A');
                $qty   =  '';
            }
            $this->pdf->tableHeader(array($label => 35, $value => 130, (string)$qty => 25), 0);
        }
        if ($this->model->getLabel() instanceof RTWLabel) {
            $this->pdf->tableHeader(array(
                _('Label (griffe)') => 35, 
                $this->model->getLabel()->toString() => 155),
            0);
        }
        $sizes = $this->model->getSizeCollection();
        if (count($sizes) > 0) {
            $this->pdf->tableHeader(array(
                _('Available sizes') => 35,
                implode(', ', array_values($sizes->toArray())) => 155), 
            0);
        }
        $this->pdf->tableHeader(array(
            _('Observations') => 35,
            $this->model->getComment() => 155), 
        0);
    }

    // }}}
}

// }}}
// BoxLabelGenerator {{{

/**
 * ProductLabelGenerator.
 * Classe utilisée pour les fiches techniques.
 *
 */
class BoxLabelGenerator extends DocumentGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @access protected
     */
    public function __construct($boxCollection) {
        // doc fictif car on ne sauve pas ces fiches suiveuses
        $document = new AbstractDocument();
        $cur = false; // pas important ici...
        parent::__construct($document, false, false, $cur, '');
        $this->pdf->showExpeditor   = false;
        $this->pdf->showPageNumbers = false;
        $this->pdf->showEditionDate = false;
        $this->pdf->setAutoPageBreak(true, 0);
        $this->boxCollection = $boxCollection;
    }

    // }}}
    // ProductLabelGenerator::render() {{{

    /**
     * Construit le doc pdf
     *
     * @access public
     * @return void
     */
    public function render() {
        $i = 0;
        $cache = array();
        foreach ($this->boxCollection as $box) {
            $x = 4;
            if ($i % 2 == 0) {
                $this->pdf->addPage();
                $y = 2;
            } else {
                $y = 150;
            }
            $i++;
            
            $data  = $box->getDataForDocument();
            $exp   = $data['expeditor'];
            $expId = $exp->getId();
            if (($logo = $exp->getLogo()) != '') {
                if (!isset($cache[$expId])) {
                    $cache[$expId] = base64_decode($logo);
                }
                $this->pdf->image($cache[$expId], $x, $y, 0, 17, 'png');
            }
            $this->pdf->setXY($x, $y+20);

            if ($data['expeditorSite'] instanceof Site) {
                $str  = $data['expeditorSite']->getFormatAddressInfos(" - ");
                if (($phone = $data['expeditorSite']->getPhone()) != '') {
                    $str .= ' - ' . _('Phone') . ': ' . $phone;
                }
                if (($email = $data['expeditorSite']->getEmail()) != '') {
                    $str .= ' - ' . _('Email') . ': ' . $email;
                }
                $this->pdf->addText(
                    $str,
                    array('fontSize' => 8, 'lineHeight' => 3)
                );
                $this->pdf->ln(7);
            }

            if ($data['destinatorSite'] instanceof Site) {
                $str = _('Customer') . ":\n\n"
                 . $data['destinatorSite']->getName() . "\n" 
                 . $data['destinatorSite']->getFormatAddressInfos("\n");
                if (($phone = $data['destinatorSite']->getPhone()) != '') {
                    $str .= "\n" . _('Phone') . ': ' . $phone;
                }
                if (($email = $data['destinatorSite']->getEmail()) != '') {
                    $str .= "\n" . _('Email') . ': ' . $email;
                }
                $this->pdf->addText(
                    $str,
                    array('fontSize' => 10, 'lineHeight' => 4, 'border' => 1, 'width'=>100)
                );
            }
            $this->pdf->setXY($x+160, $y+5);
            if (($coverType = $box->getCoverType()) instanceof Covertype) {
                $this->pdf->addText(
                    $coverType->toString() . ' (' . $i . '/' . count($this->boxCollection) . ')',
                    array('fontSize' => 10, 'lineHeight' => 6)
                );
            }
            $this->pdf->Code128($x+120, $y+35, $data['reference'], 50, 15);
            $this->pdf->setXY($x+120, $y+50);
            $this->pdf->addText(
                $data['reference'],
                array('fontSize' => 10, 'lineHeight' => 6)
            );
            $headers = array(
                _('Order number') => 50,
                _('Reference') => 50,
                _('Construction code') => 45,
                _('Size') => 25,
                _('Quantity') => 20,
            );
            $this->pdf->ln(13);
            $this->pdf->tableHeader($headers);
            foreach ($data['children'] as $itemData) {
                $this->pdf->tableBody(array(0 => array(
                    Tools::getValueFromMacro($itemData['cmi'], '%Command.CommandNo%'),
                    Tools::getValueFromMacro($itemData['cmi'], '%Product.BaseReference%'),
                    Tools::getValueFromMacro($itemData['cmi'], '%Product.Model.ConstructionCode%'),
                    Tools::getValueFromMacro($itemData['cmi'], '%Product.Size%'),
                    $itemData['quantity'],
                )));
            }
            $this->pdf->ln(3);
            $this->pdf->addText(
                _('Made in CE'),
                array('fontSize' => 10, 'lineHeight' => 6, 'align' => 'R')
            );
            /*
            $this->pdf->addText(
                    $cache[$mid]['pressname'],
                    array('fontSize' => 10, 'lineHeight' => 4)
                    );
                if (($model = $product->getModel()) instanceof RTWModel) {
                    $mid = $model->getId();
                    if (!isset($cache[$mid])) {
                        $cache[$mid] = array();
                        // image du produit
                        $infos = ImageManager::getFileInfo(md5($model->getImage()));
                        if ($infos['width'] > $infos['height']) {
                            $cache[$mid]['w'] = 40;
                            $cache[$mid]['h'] = 0;
                        } else {
                            $cache[$mid]['w'] = 0;
                            $cache[$mid]['h'] = 40;
                        }
                        if (is_array($infos) && !empty($infos['data'])) {
                            list(,$type) = explode('/', $infos['mimetype']);
                            $cache[$mid]['type'] = $type;
                            $cache[$mid]['data'] = $infos['data'];
                        }
                        // style number
                        $cache[$mid]['pressname'] = $model->getPressName()->toString();
                    }
                    $this->pdf->setXY($x, $y);
                    $this->pdf->image($cache[$mid]['data'], $x, $y,
                        $cache[$mid]['w'], $cache[$mid]['h'], $cache[$mid]['type']);
                    $this->pdf->setXY($x+50, $y);
                    $this->pdf->addText(
                        $cache[$mid]['pressname'],
                        array('fontSize' => 10, 'lineHeight' => 4)
                    );
                }
                $this->pdf->setXY($x+86, $y);
                if (($size = $product->getSize()) instanceof RTWSize) {
                    // taille
                    $this->pdf->addText(
                        $size->getName(),
                        array('fontSize' => 12, 'lineHeight' => 4, 'fontStyle' => 'B')
                    );
                }
                $this->pdf->setXY($x+50, $y+5);
                // reference
                $this->pdf->addText(
                    $product->getBaseReference(),
                    array('fontSize' => 8, 'lineHeight' => 4)
                );
                // code barre
                $this->pdf->Code128($x+50, $y+10, $product->getBaseReference(), 50, 10);
                $this->pdf->setXY($x, $y+37);
                $this->pdf->addText(
                    $product->getName(),
                    array('fontSize' => 8, 'lineHeight' => 4, 'width' => 105, 'align' => 'C')
                );
             */
        }
        return $this->pdf;
    }

    // }}}
}

// }}}
// MovementLabelGenerator {{{

/**
 * MovementLabelGenerator {{{.
 * Classe utilisee pour les impressions code barres commandes/mouvements
 *
 */
class MovementLabelGenerator extends DocumentGenerator
{
    // __construct() {{{

    /**
     * Constructor
     *
     * @access protected
     */
    public function __construct($mvtInfo) {
        // doc fictif car on ne sauve pas ces etiquettes ...
        $document = new AbstractDocument();
        $cur = false; // pas important ici...
        parent::__construct($document, false, false, $cur, '');
        $this->pdf->showExpeditor   = false;
        $this->pdf->showPageNumbers = false;
        $this->pdf->showEditionDate = false;
        $this->pdf->setAutoPageBreak(true, 0);
        $this->movementInfo = $mvtInfo;
    }

    // }}}
    // ProductLabelGenerator::render() {{{

    /**
     * Construit le doc pdf
     *
     * @access public
     * @return void
     */
    public function render() {

        // Document dimensions
        // should act on parent object ...
        $pageHeight = 297 ;
        $pageWidth = 210 ;

        $codeHeight = 10 ;
        $codeWidth = 80 ;

        $pageMargeTop = 5 ;
        $pageMargeBottom = 4 ;
        $pageMargeLeft = 4 ;
        $pageMargeRight = 4 ;

        // Spacing between each elements
        $spacing = 2 ;
        $lineHeight = 4;

        $nperpage = ($pageHeight - ($pageMargeTop+$pageMargeBottom)) 
                            / ($codeHeight+(4*$spacing) + $lineHeight);

        $nperpage = floor($nperpage);
        $i = 0;
        $cache = array();

        foreach ($this->movementInfo as $array) {

            list($mvt_date, $cmd_expeditor, $cmd_destinator, $cmd_ref) = $array;
            $x = $pageMargeLeft ;
            if ($i == 0 || ($i%$nperpage==0)) {
                    $y = $pageMargeTop + $spacing ;
                    $this->pdf->addPage();
            } else {
                    $y += $codeHeight + $lineHeight + (4 * $spacing ) ;
            }

            $x += $spacing ;

            $i++;
                    
            $this->pdf->setXY($x, $y ) ;
            $this->pdf->addText( _('Date').": ".$mvt_date ,
                                array('fontSize' => 10, 'lineHeight' => $lineHeight)
                            ); 

            $this->pdf->setXY($x, $y + $lineHeight) ;
            $this->pdf->addText( _('Expeditor').": ".$cmd_expeditor,
                                array('fontSize' => 10, 'lineHeight' => $lineHeight)
                            ); 

            $this->pdf->setXY($x, $y + $lineHeight + $lineHeight) ;
            $this->pdf->addText(  _('Destinator').": ".$cmd_destinator,
                                array('fontSize' => 10, 'lineHeight' => $lineHeight)
                            ); 


            // code barre no commande
            $code_x = $x + 110 ;
            $code_y = $y ;
            $this->pdf->Code128($code_x , $code_y , $cmd_ref , $codeWidth , $codeHeight );
            $this->pdf->setXY($code_x, $code_y + $codeHeight + $spacing);
            $this->pdf->addText( $cmd_ref ,
                                array('fontSize' => 10, 'lineHeight' => $lineHeight)
                            ); 

            $line_y = $y + $codeHeight + $lineHeight + (3*$spacing) ;
            $this->pdf->Line(0,$line_y ,210,$line_y );
        }

        return $this->pdf;
    }
    // }}}

}
// }}}
// }}}

?>
