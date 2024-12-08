<?php
/*******************************************************************************
 * FPDF                                                                         *
 *                                                                              *
 * Version: 1.86                                                                *
 * Date:    2023-06-25                                                          *
 * Author:  Olivier PLATHEY                                                     *
 *******************************************************************************/

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!defined('PARAGRAPH_STRING')) define('PARAGRAPH_STRING', '~~~');

require_once ('classes/StringTags.php');

class Fpdf
{
    const VERSION = '1.86';
    protected $page;               // current page number
    protected $n;                  // current object number
    protected $offsets;            // array of object offsets
    protected $buffer;             // buffer holding in-memory PDF
    protected $pages;              // array containing pages
    protected $state;              // current document state
    protected $compress;           // compression flag
    protected $iconv;              // whether iconv is available
    protected $k;                  // scale factor (number of points in user unit)
    protected $DefOrientation;     // default orientation
    protected $CurOrientation;     // current orientation
    protected $StdPageSizes;       // standard page sizes
    protected $DefPageSize;        // default page size
    protected $CurPageSize;        // current page size
    protected $CurRotation;        // current page rotation
    protected $PageInfo;           // page-related data
    protected $wPt, $hPt;          // dimensions of current page in points
    protected $w, $h;              // dimensions of current page in user unit
    protected $lMargin;            // left margin
    protected $tMargin;            // top margin
    protected $rMargin;            // right margin
    protected $bMargin;            // page break margin
    protected $cMargin;            // cell margin
    protected $x, $y;              // current position in user unit
    protected $lasth;              // height of last printed cell
    protected $LineWidth;          // line width in user unit
    protected $fontpath;           // directory containing fonts
    protected $CoreFonts;          // array of core font names
    protected $fonts;              // array of used fonts
    protected $FontFiles;          // array of font files
    protected $encodings;          // array of encodings
    protected $cmaps;              // array of ToUnicode CMaps
    protected $FontFamily;         // current font family
    protected $FontStyle;          // current font style
    protected $underline;          // underlining flag
    protected $CurrentFont;        // current font info
    protected $FontSizePt;         // current font size in points
    protected $FontSize;           // current font size in user unit
    protected $DrawColor;          // commands for drawing color
    protected $FillColor;          // commands for filling color
    protected $TextColor;          // commands for text color
    protected $ColorFlag;          // indicates whether fill and text colors are different
    protected $WithAlpha;          // indicates whether alpha channel is used
    protected $ws;                 // word spacing
    protected $images;             // array of used images
    protected $PageLinks;          // array of links in pages
    protected $links;              // array of internal links
    protected $AutoPageBreak;      // automatic page breaking
    protected $PageBreakTrigger;   // threshold used to trigger page breaks
    protected $InHeader;           // flag set when processing header
    protected $InFooter;           // flag set when processing footer
    protected $AliasNbPages;       // alias for total number of pages
    protected $ZoomMode;           // zoom display mode
    protected $LayoutMode;         // layout display mode
    protected $metadata;           // document properties
    protected $CreationDate;       // document creation date
    protected $PDFVersion;         // PDF version number



    var $wt_Current_Tag;
    var $wt_FontInfo;//tags font info
    var $wt_DataInfo;//parsed string data info
    var $wt_DataExtraInfo;//data extra INFO
    var $wt_TempData; //some temporary info

    /*******************************************************************************
     *                               Public methods                                 *
     *******************************************************************************/

    //---------------------------------------- Barcode ------------------------------

    protected $T128;                                         // Tableau des codes 128
    protected $ABCset = "";                                  // jeu des caractères éligibles au C128
    protected $Aset = "";                                    // Set A du jeu des caractères éligibles
    protected $Bset = "";                                    // Set B du jeu des caractères éligibles
    protected $Cset = "";                                    // Set C du jeu des caractères éligibles
    protected $SetFrom;                                      // Convertisseur source des jeux vers le tableau
    protected $SetTo;                                        // Convertisseur destination des jeux vers le tableau
    protected $JStart = array("A" => 103, "B" => 104, "C" => 105); // Caractères de sélection de jeu au début du C128
    protected $JSwap = array("A" => 101, "B" => 100, "C" => 99);   // Caractères de changement de jeu

    private $angle=0;

    //------------------------------------------ Barcode end here-----------------------------

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        // Initialization of properties
        $this->state = 0;
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = array();
        $this->PageInfo = array();
        $this->fonts = array();
        $this->FontFiles = array();
        $this->encodings = array();
        $this->cmaps = array();
        $this->images = array();
        $this->links = array();
        $this->InHeader = false;
        $this->InFooter = false;
        $this->lasth = 0;
        $this->FontFamily = '';
        $this->FontStyle = '';
        $this->FontSizePt = 12;
        $this->underline = false;
        $this->DrawColor = '0 G';
        $this->FillColor = '0 g';
        $this->TextColor = '0 g';
        $this->ColorFlag = false;
        $this->WithAlpha = false;
        $this->ws = 0;
        $this->iconv = function_exists('iconv');
        // Font path
        if (defined('FPDF_FONTPATH'))
            $this->fontpath = FPDF_FONTPATH;
        else
            //$this->fontpath = dirname(__FILE__).'/font/';
            $this->fontpath = '\\font\\';
        // Core fonts
        $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
        // Scale factor
        if ($unit == 'pt')
            $this->k = 1;
        elseif ($unit == 'mm')
            $this->k = 72 / 25.4;
        elseif ($unit == 'cm')
            $this->k = 72 / 2.54;
        elseif ($unit == 'in')
            $this->k = 72;
        else
            $this->Error('Incorrect unit: ' . $unit);
        // Page sizes
        $this->StdPageSizes = array('a3' => array(841.89, 1190.55), 'a4' => array(595.28, 841.89), 'a5' => array(420.94, 595.28),
            'letter' => array(612, 792), 'legal' => array(612, 1008));
        $size = $this->_getpagesize($size);
        $this->DefPageSize = $size;
        $this->CurPageSize = $size;
        // Page orientation
        $orientation = strtolower($orientation);
        if ($orientation == 'p' || $orientation == 'portrait') {
            $this->DefOrientation = 'P';
            $this->w = $size[0];
            $this->h = $size[1];
        } elseif ($orientation == 'l' || $orientation == 'landscape') {
            $this->DefOrientation = 'L';
            $this->w = $size[1];
            $this->h = $size[0];
        } else
            $this->Error('Incorrect orientation: ' . $orientation);
        $this->CurOrientation = $this->DefOrientation;
        $this->wPt = $this->w * $this->k;
        $this->hPt = $this->h * $this->k;
        // Page rotation
        $this->CurRotation = 0;
        // Page margins (1 cm)
        $margin = 28.35 / $this->k;
        $this->SetMargins($margin, $margin);
        // Interior cell margin (1 mm)
        $this->cMargin = $margin / 10;
        // Line width (0.2 mm)
        $this->LineWidth = .567 / $this->k;
        // Automatic page break
        $this->SetAutoPageBreak(true, 2 * $margin);
        // Default display mode
        $this->SetDisplayMode('default');
        // Enable compression
        $this->SetCompression(true);
        // Metadata
        $this->metadata = array('Producer' => 'FPDF ' . self::VERSION);
        // Set default PDF version number
        $this->PDFVersion = '1.3';

        //------------------------- Barcode Initialization ------------------------------------------------------

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
        for ($i = 96; $i <= 127; $i++) {
            $this->ABCset .= chr($i);
            $this->Bset .= chr($i);
        }
        for ($i = 200; $i <= 210; $i++) {                                           // controle 128
            $this->ABCset .= chr($i);
            $this->Aset .= chr($i);
            $this->Bset .= chr($i);
        }
        $this->Cset = "0123456789" . chr(206);

        for ($i = 0; $i < 96; $i++) {                                                   // convertisseurs des jeux A & B
            @$this->SetFrom["A"] .= chr($i);
            @$this->SetFrom["B"] .= chr($i + 32);
            @$this->SetTo["A"] .= chr(($i < 32) ? $i + 64 : $i - 32);
            @$this->SetTo["B"] .= chr($i);
        }
        for ($i = 96; $i < 107; $i++) {                                                 // contrôle des jeux A & B
            @$this->SetFrom["A"] .= chr($i + 104);
            @$this->SetFrom["B"] .= chr($i + 104);
            @$this->SetTo["A"] .= chr($i);
            @$this->SetTo["B"] .= chr($i);
        }

    }

    function Code128($x, $y, $code, $w, $h)
    {
        $Aguid = "";                                                                      // Création des guides de choix ABC
        $Bguid = "";
        $Cguid = "";
        for ($i = 0; $i < strlen($code); $i++) {
            $needle = substr($code, $i, 1);
            $Aguid .= ((strpos($this->Aset, $needle) === false) ? "N" : "O");
            $Bguid .= ((strpos($this->Bset, $needle) === false) ? "N" : "O");
            $Cguid .= ((strpos($this->Cset, $needle) === false) ? "N" : "O");
        }

        $SminiC = "OOOO";
        $IminiC = 4;

        $crypt = "";
        while ($code > "") {
            // BOUCLE PRINCIPALE DE CODAGE
            $i = strpos($Cguid, $SminiC);                                                // forçage du jeu C, si possible
            if ($i !== false) {
                $Aguid [$i] = "N";
                $Bguid [$i] = "N";
            }

            if (substr($Cguid, 0, $IminiC) == $SminiC) {                                  // jeu C
                $crypt .= chr(($crypt > "") ? $this->JSwap["C"] : $this->JStart["C"]);  // début Cstart, sinon Cswap
                $made = strpos($Cguid, "N");                                             // étendu du set C
                if ($made === false) {
                    $made = strlen($Cguid);
                }
                if (fmod($made, 2) == 1) {
                    $made--;                                                            // seulement un nombre pair
                }
                for ($i = 0; $i < $made; $i += 2) {
                    $crypt .= chr(strval(substr($code, $i, 2)));                          // conversion 2 par 2
                }
                $jeu = "C";
            } else {
                $madeA = strpos($Aguid, "N");                                            // étendu du set A
                if ($madeA === false) {
                    $madeA = strlen($Aguid);
                }
                $madeB = strpos($Bguid, "N");                                            // étendu du set B
                if ($madeB === false) {
                    $madeB = strlen($Bguid);
                }
                $made = (($madeA < $madeB) ? $madeB : $madeA);                         // étendu traitée
                $jeu = (($madeA < $madeB) ? "B" : "A");                                // Jeu en cours

                $crypt .= chr(($crypt > "") ? $this->JSwap[$jeu] : $this->JStart[$jeu]); // début start, sinon swap

                $crypt .= strtr(substr($code, 0, $made), $this->SetFrom[$jeu], $this->SetTo[$jeu]); // conversion selon jeu

            }
            $code = substr($code, $made);                                           // raccourcir légende et guides de la zone traitée
            $Aguid = substr($Aguid, $made);
            $Bguid = substr($Bguid, $made);
            $Cguid = substr($Cguid, $made);
        }                                                                          // FIN BOUCLE PRINCIPALE

        $check = ord($crypt[0]);                                                   // calcul de la somme de contrôle
        for ($i = 0; $i < strlen($crypt); $i++) {
            $check += (ord($crypt[$i]) * $i);
        }
        $check %= 103;

        $crypt .= chr($check) . chr(106) . chr(107);                               // Chaine cryptée complète

        $i = (strlen($crypt) * 11) - 8;                                            // calcul de la largeur du module
        $modul = $w / $i;

        for ($i = 0; $i < strlen($crypt); $i++) {                                      // BOUCLE D'IMPRESSION
            $c = $this->T128[ord($crypt[$i])];
            for ($j = 0; $j < count($c); $j++) {
                $this->Rect($x, $y, $c[$j] * $modul, $h, "F");
                $x += ($c[$j++] + $c[$j]) * $modul;
            }
        }
    }

    function WriteTextWithRotation($x_pos,$y_pos,$txt,$angle){

        $this->Rotate($angle,$x_pos,$y_pos);
        $this->WriteText($txt);
        $this->Rotate(0);
    }



    function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }

    function RotatedImage($file,$x,$y,$w,$h,$angle)
    {
        //Image rotated around its upper-left corner
        $this->Rotate($angle,$x,$y);
        $this->Image($file,$x,$y,$w,$h);
        $this->Rotate(0);
    }

    function Rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }

    function _endpage()
    {
        if($this->angle!=0)
        {
            $this->angle=0;
            $this->_out('Q');
        }
        $this->state = 1;
    }


    //------------------------------------------------------- Line Charts ----------------------------------------------------

    function LineGraph($w, $h, $data, $options = '', $colors = null, $maxVal = 0, $nbDiv = 4, $show_title_groups = TRUE,$show_x_values = TRUE, $show_y_values = TRUE)
    {
        /*******************************************
         * Explain the variables:
         * $w = the width of the diagram
         * $h = the height of the diagram
         * $data = the data for the diagram in the form of a multidimensional array
         * $options = the possible formatting options which include:
         * 'V' = Print Vertical Divider lines
         * 'H' = Print Horizontal Divider Lines
         * 'kB' = Print bounding box around the Key (legend)
         * 'vB' = Print bounding box around the values under the graph
         * 'gB' = Print bounding box around the graph
         * 'dB' = Print bounding box around the entire diagram
         * $colors = A multidimensional array containing RGB values
         * $maxVal = The Maximum Value for the graph vertically
         * $nbDiv = The number of vertical Divisions
         *******************************************/
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);
        $keys = array_keys($data);
        $ordinateWidth = 10;
        $w -= $ordinateWidth;
        $valX = $this->getX() + $ordinateWidth;
        $valY = $this->getY();
        $margin = 1;
        $titleH = 8;
        $titleW = $w;
        $lineh = 5;
        $keyH = count($data) * $lineh;
        $keyW = $w / 5;
        $graphValH = 5;
        $graphValW = $w - $keyW - 3 * $margin;
        $graphH = $h - (3 * $margin) - $graphValH;
        $graphW = $w - (2 * $margin) - ($keyW + $margin);
        $graphX = $valX + $margin;
        $graphY = $valY + $margin;
        $graphValX = $valX + $margin;
        $graphValY = $valY + 2 * $margin + $graphH;
        $keyX = $valX + (2 * $margin) + $graphW;
        $keyY = $valY + $margin + .5 * ($h - (2 * $margin)) - .5 * ($keyH);
        //draw graph frame border
        if (strstr($options, 'gB')) {
            $this->Rect($valX, $valY, $w, $h);
        }
        //draw graph diagram border
        if (strstr($options, 'dB')) {
            $this->Rect($valX + $margin, $valY + $margin, $graphW, $graphH);
        }
        //draw key legend border
        if (strstr($options, 'kB')) {
            $this->Rect($keyX, $keyY, $keyW, $keyH);
        }
        //draw graph value box
        if (strstr($options, 'vB')) {
            $this->Rect($graphValX, $graphValY, $graphValW, $graphValH);
        }
        //define colors
        if ($colors === null) {
            $safeColors = array(0, 51, 102, 153, 204, 225);
            for ($i = 0; $i < count($data); $i++) {
                $colors[$keys[$i]] = array($safeColors[array_rand($safeColors)], $safeColors[array_rand($safeColors)], $safeColors[array_rand($safeColors)]);
            }
        }
        //form an array with all data values from the multi-demensional $data array
        $ValArray = array();
        foreach ($data as $key => $value) {
            foreach ($data[$key] as $val) {
                $ValArray[] = $val;
            }
        }
        //define max value
        if ($maxVal < ceil(max($ValArray))) {
            $maxVal = ceil(max($ValArray));
        }
        //draw horizontal lines
        $vertDivH = $graphH / $nbDiv;
        if (strstr($options, 'H')) {
            for ($i = 0; $i <= $nbDiv; $i++) {
                if ($i < $nbDiv) {
                    $this->Line($graphX, $graphY + $i * $vertDivH, $graphX + $graphW, $graphY + $i * $vertDivH);
                } else {
                    $this->Line($graphX, $graphY + $graphH, $graphX + $graphW, $graphY + $graphH);
                }
            }
        }
        //draw vertical lines
        $horiDivW = floor($graphW / (count($data[$keys[0]]) - 1));
        if (strstr($options, 'V')) {
            for ($i = 0; $i <= (count($data[$keys[0]]) - 1); $i++) {
                if ($i < (count($data[$keys[0]]) - 1)) {
                    $this->Line($graphX + $i * $horiDivW, $graphY, $graphX + $i * $horiDivW, $graphY + $graphH);
                } else {
                    $this->Line($graphX + $graphW, $graphY, $graphX + $graphW, $graphY + $graphH);
                }
            }
        }
        //draw graph lines
        foreach ($data as $key => $value) {
            $this->setDrawColor($colors[$key][0], $colors[$key][1], $colors[$key][2]);
            $this->SetLineWidth(0.8);
            $valueKeys = array_keys($value);
            for ($i = 0; $i < count($value); $i++) {
                if ($i == count($value) - 2) {
                    $this->Line(
                        $graphX + ($i * $horiDivW),
                        $graphY + $graphH - ($value[$valueKeys[$i]] / $maxVal * $graphH),
                        $graphX + $graphW,
                        $graphY + $graphH - ($value[$valueKeys[$i + 1]] / $maxVal * $graphH)
                    );
                } else if ($i < (count($value) - 1)) {
                    $this->Line(
                        $graphX + ($i * $horiDivW),
                        $graphY + $graphH - ($value[$valueKeys[$i]] / $maxVal * $graphH),
                        $graphX + ($i + 1) * $horiDivW,
                        $graphY + $graphH - ($value[$valueKeys[$i + 1]] / $maxVal * $graphH)
                    );
                }
            }
            //Set the Key (legend)
            $this->SetFont('Courier', '', 10);
            if (!isset($n)) $n = 0;
            //show title groups
            if ($show_title_groups) {
                /* $this->Line($keyX+1,$keyY+$lineh/2+$n*$lineh,$keyX+8,$keyY+$lineh/2+$n*$lineh);
                 $this->SetXY($keyX+8,$keyY+$n*$lineh);
                 $this->Cell($keyW,$lineh,$key,0,1,'L');*/
            }
            $n++;
        }
        //print the abscissa values
        if ($show_x_values){
        /* foreach($valueKeys as $key => $value){
             if($key==0){
                 $this->SetXY($graphValX,$graphValY);
                 $this->Cell(30,$lineh,$value,0,0,'L');
             } else if($key==count($valueKeys)-1){
                 $this->SetXY($graphValX+$graphValW-30,$graphValY);
                 $this->Cell(30,$lineh,$value,0,0,'R');
             } else {
                 $this->SetXY($graphValX+$key*$horiDivW-15,$graphValY);
                 $this->Cell(30,$lineh,$value,0,0,'C');
             }
         }*/

        }
        //print the ordinate values
        if($show_y_values){
        /* for($i=0;$i<=$nbDiv;$i++){
             $this->SetXY($graphValX-10,$graphY+($nbDiv-$i)*$vertDivH-3);
             $this->Cell(8,6,sprintf('%.1f',$maxVal/$nbDiv*$i),0,0,'R');
         }*/
        }
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);
    }


    //-------------------------------------------------------- Up * Line Charts -----------------------------------------------

    function SetDash($black = null, $white = null)
    {
        if ($black !== null)
            $s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
        else
            $s = '[] 0 d';
        $this->_out($s);
    }

    function TextWithDirection($x, $y, $txt, $direction = 'R')
    {
        if ($direction == 'R')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 1, 0, 0, 1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'L')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', -1, 0, 0, -1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'U')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, 1, -1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'D')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, -1, 1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        else
            $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        if ($this->ColorFlag)
            $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
        $this->_out($s);
    }

    function WriteText($text)
    {
        $intPosIni = 0;
        $intPosFim = 0;
        if (strpos($text, '<') !== false && strpos($text, '[') !== false) {
            if (strpos($text, '<') < strpos($text, '[')) {
                $this->Write(5, substr($text, 0, strpos($text, '<')));
                $intPosIni = strpos($text, '<');
                $intPosFim = strpos($text, '>');
                $this->SetFont('', 'B');
                $this->Write(5, substr($text, $intPosIni + 1, $intPosFim - $intPosIni - 1));
                $this->SetFont('', '');
                $this->WriteText(substr($text, $intPosFim + 1, strlen($text)));
            } else {
                $this->Write(5, substr($text, 0, strpos($text, '[')));
                $intPosIni = strpos($text, '[');
                $intPosFim = strpos($text, ']');
                $w = $this->GetStringWidth('a') * ($intPosFim - $intPosIni - 1);
                $this->Cell($w, $this->FontSize + 0.75, substr($text, $intPosIni + 1, $intPosFim - $intPosIni - 1), 1, 0, '');
                $this->WriteText(substr($text, $intPosFim + 1, strlen($text)));
            }
        } else {
            if (strpos($text, '<') !== false) {
                $this->Write(5, substr($text, 0, strpos($text, '<')));
                $intPosIni = strpos($text, '<');
                $intPosFim = strpos($text, '>');
                $this->SetFont('', 'B');
                $this->WriteText(substr($text, $intPosIni + 1, $intPosFim - $intPosIni - 1));
                $this->SetFont('', '');
                $this->WriteText(substr($text, $intPosFim + 1, strlen($text)));
            } elseif (strpos($text, '[') !== false) {
                $this->Write(5, substr($text, 0, strpos($text, '[')));
                $intPosIni = strpos($text, '[');
                $intPosFim = strpos($text, ']');
                $w = $this->GetStringWidth('a') * ($intPosFim - $intPosIni - 1);
                $this->Cell($w, $this->FontSize + 0.75, substr($text, $intPosIni + 1, $intPosFim - $intPosIni - 1), 1, 0, '');
                $this->WriteText(substr($text, $intPosFim + 1, strlen($text)));
            } else {
                $this->Write(5, $text);
            }

        }
    }

    /************************************************************
     *                                                           *
     *    MultiCell with bullet (array)                          *
     *                                                           *
     *    Requires an array with the following  keys:            *
     *                                                           *
     *        Bullet -> String or Number                         *
     *        Margin -> Number, space between bullet and text    *
     *        Indent -> Number, width from current x position    *
     *        Spacer -> Number, calls Cell(x), spacer=x          *
     *        Text -> Array, items to be bulleted                *
     *                                                           *
     ************************************************************/

    function MultiCellBltArray($w, $h, $blt_array, $border = 0, $align = 'J', $fill = false)
    {
        if (!is_array($blt_array)) {
            die('MultiCellBltArray requires an array with the following keys: bullet,margin,text,indent,spacer');
            exit;
        }

        //Save x
        $bak_x = $this->x;

        for ($i = 0; $i < sizeof($blt_array['text']); $i++) {
            //Get bullet width including margin
            $blt_width = $this->GetStringWidth($blt_array['bullet'] . $blt_array['margin']) + $this->cMargin * 2;

            // SetX
            $this->SetX($bak_x);

            //Output indent
            if ($blt_array['indent'] > 0)
                $this->Cell($blt_array['indent']);

            //Output bullet
            $this->Cell($blt_width, $h, $blt_array['bullet'] . $blt_array['margin'], 0, '', $fill);

            //Output text
            $this->MultiCell($w - $blt_width, $h, $blt_array['text'][$i], $border, $align, $fill);

            //Insert a spacer between items if not the last item
            if ($i != sizeof($blt_array['text']) - 1)
                $this->Ln($blt_array['spacer']);

            //Increment bullet if it's a number
            if (is_numeric($blt_array['bullet']))
                $blt_array['bullet']++;
        }

        //Restore x
        $this->x = $bak_x;
    }


    function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle = 0)
    {
        $font_angle += 90 + $txt_angle;
        $txt_angle *= M_PI / 180;
        $font_angle *= M_PI / 180;

        $txt_dx = cos($txt_angle);
        $txt_dy = sin($txt_angle);
        $font_dx = cos($font_angle);
        $font_dy = sin($font_angle);

        $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', $txt_dx, $txt_dy, $font_dx, $font_dy, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        if ($this->ColorFlag)
            $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
        $this->_out($s);
    }


    function SetMargins($left, $top, $right = null)
    {
        // Set left, top and right margins
        $this->lMargin = $left;
        $this->tMargin = $top;
        if ($right === null)
            $right = $left;
        $this->rMargin = $right;
    }

    function SetLeftMargin($margin)
    {
        // Set left margin
        $this->lMargin = $margin;
        if ($this->page > 0 && $this->x < $margin)
            $this->x = $margin;
    }

    function SetTopMargin($margin)
    {
        // Set top margin
        $this->tMargin = $margin;
    }

    function SetRightMargin($margin)
    {
        // Set right margin
        $this->rMargin = $margin;
    }

    function SetAutoPageBreak($auto, $margin = 0)
    {
        // Set auto page break mode and triggering margin
        $this->AutoPageBreak = $auto;
        $this->bMargin = $margin;
        $this->PageBreakTrigger = $this->h - $margin;
    }

    function SetDisplayMode($zoom, $layout = 'default')
    {
        // Set display mode in viewer
        if ($zoom == 'fullpage' || $zoom == 'fullwidth' || $zoom == 'real' || $zoom == 'default' || !is_string($zoom))
            $this->ZoomMode = $zoom;
        else
            $this->Error('Incorrect zoom display mode: ' . $zoom);
        if ($layout == 'single' || $layout == 'continuous' || $layout == 'two' || $layout == 'default')
            $this->LayoutMode = $layout;
        else
            $this->Error('Incorrect layout display mode: ' . $layout);
    }

    function SetCompression($compress)
    {
        // Set page compression
        if (function_exists('gzcompress'))
            $this->compress = $compress;
        else
            $this->compress = false;
    }

    function SetTitle($title, $isUTF8 = false)
    {
        // Title of document
        $this->metadata['Title'] = $isUTF8 ? $title : $this->_UTF8encode($title);
    }

    function SetAuthor($author, $isUTF8 = false)
    {
        // Author of document
        $this->metadata['Author'] = $isUTF8 ? $author : $this->_UTF8encode($author);
    }

    function SetSubject($subject, $isUTF8 = false)
    {
        // Subject of document
        $this->metadata['Subject'] = $isUTF8 ? $subject : $this->_UTF8encode($subject);
    }

    function SetKeywords($keywords, $isUTF8 = false)
    {
        // Keywords of document
        $this->metadata['Keywords'] = $isUTF8 ? $keywords : $this->_UTF8encode($keywords);
    }

    function SetCreator($creator, $isUTF8 = false)
    {
        // Creator of document
        $this->metadata['Creator'] = $isUTF8 ? $creator : $this->_UTF8encode($creator);
    }

    function AliasNbPages($alias = '{nb}')
    {
        // Define an alias for total number of pages
        $this->AliasNbPages = $alias;
    }

    function Error($msg)
    {
        // Fatal error
        throw new Exception('FPDF error: ' . $msg);
    }

    function Close()
    {
        // Terminate document
        if ($this->state == 3)
            return;
        if ($this->page == 0)
            $this->AddPage();
        // Page footer
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;
        // Close page
        $this->_endpage();
        // Close document
        $this->_enddoc();
    }

    function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        // Start a new page
        if ($this->state == 3)
            $this->Error('The document is closed');
        $family = $this->FontFamily;
        $style = $this->FontStyle . ($this->underline ? 'U' : '');
        $fontsize = $this->FontSizePt;
        $lw = $this->LineWidth;
        $dc = $this->DrawColor;
        $fc = $this->FillColor;
        $tc = $this->TextColor;
        $cf = $this->ColorFlag;
        if ($this->page > 0) {
            // Page footer
            $this->InFooter = true;
            $this->Footer();
            $this->InFooter = false;
            // Close page
            $this->_endpage();
        }
        // Start new page
        $this->_beginpage($orientation, $size, $rotation);
        // Set line cap style to square
        $this->_out('2 J');
        // Set line width
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w', $lw * $this->k));
        // Set font
        if ($family)
            $this->SetFont($family, $style, $fontsize);
        // Set colors
        $this->DrawColor = $dc;
        if ($dc != '0 G')
            $this->_out($dc);
        $this->FillColor = $fc;
        if ($fc != '0 g')
            $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
        // Page header
        $this->InHeader = true;
        $this->Header();
        $this->InHeader = false;
        // Restore line width
        if ($this->LineWidth != $lw) {
            $this->LineWidth = $lw;
            $this->_out(sprintf('%.2F w', $lw * $this->k));
        }
        // Restore font
        if ($family)
            $this->SetFont($family, $style, $fontsize);
        // Restore colors
        if ($this->DrawColor != $dc) {
            $this->DrawColor = $dc;
            $this->_out($dc);
        }
        if ($this->FillColor != $fc) {
            $this->FillColor = $fc;
            $this->_out($fc);
        }
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
    }

    function Header()
    {
        // To be implemented in your own inherited class
    }

    function Footer()
    {
        // To be implemented in your own inherited class
    }

    function PageNo()
    {
        // Get current page number
        return $this->page;
    }

    function SetDrawColor($r, $g = null, $b = null)
    {
        // Set color for all stroking operations
        if (($r == 0 && $g == 0 && $b == 0) || $g === null)
            $this->DrawColor = sprintf('%.3F G', $r / 255);
        else
            $this->DrawColor = sprintf('%.3F %.3F %.3F RG', $r / 255, $g / 255, $b / 255);
        if ($this->page > 0)
            $this->_out($this->DrawColor);
    }

    function SetFillColor($r, $g = null, $b = null)
    {
        // Set color for all filling operations
        if (($r == 0 && $g == 0 && $b == 0) || $g === null)
            $this->FillColor = sprintf('%.3F g', $r / 255);
        else
            $this->FillColor = sprintf('%.3F %.3F %.3F rg', $r / 255, $g / 255, $b / 255);
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
        if ($this->page > 0)
            $this->_out($this->FillColor);
    }

    function SetTextColor($r, $g = null, $b = null)
    {
        // Set color for text
        if (($r == 0 && $g == 0 && $b == 0) || $g === null)
            $this->TextColor = sprintf('%.3F g', $r / 255);
        else
            $this->TextColor = sprintf('%.3F %.3F %.3F rg', $r / 255, $g / 255, $b / 255);
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
    }

    function GetStringWidth($s)
    {
        // Get width of a string in the current font
        $cw = $this->CurrentFont['cw'];
        $w = 0;
        $s = (string)$s;
        $l = strlen($s);
        for ($i = 0; $i < $l; $i++)
            $w += $cw[$s[$i]];
        return $w * $this->FontSize / 1000;
    }

    function SetLineWidth($width)
    {
        // Set line width
        $this->LineWidth = $width;
        if ($this->page > 0)
            $this->_out(sprintf('%.2F w', $width * $this->k));
    }

    function Line($x1, $y1, $x2, $y2)
    {
        // Draw a line
        $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k));
    }

    function Rect($x, $y, $w, $h, $style = '')
    {
        // Draw a rectangle
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x * $this->k, ($this->h - $y) * $this->k, $w * $this->k, -$h * $this->k, $op));
    }

    function AddFont($family, $style = '', $file = '', $dir = '')
    {
        // Add a TrueType, OpenType or Type1 font
        $family = strtolower($family);
        if ($file == '')
            $file = str_replace(' ', '', $family) . strtolower($style) . '.php';
        $style = strtoupper($style);
        if ($style == 'IB')
            $style = 'BI';
        $fontkey = $family . $style;
        if (isset($this->fonts[$fontkey]))
            return;
        if (strpos($file, '/') !== false || strpos($file, "\\") !== false)
            $this->Error('Incorrect font definition file name: ' . $file);
        if ($dir == '')
            $dir = $this->fontpath;
        if (substr($dir, -1) != '/' && substr($dir, -1) != '\\')
            $dir .= '/';
        $info = $this->_loadfont($dir . $file);
        $info['i'] = count($this->fonts) + 1;
        if (!empty($info['file'])) {
            // Embedded font
            $info['file'] = $dir . $info['file'];
            if ($info['type'] == 'TrueType')
                $this->FontFiles[$info['file']] = array('length1' => $info['originalsize']);
            else
                $this->FontFiles[$info['file']] = array('length1' => $info['size1'], 'length2' => $info['size2']);
        }
        $this->fonts[$fontkey] = $info;
    }

    function SetFont($family, $style = '', $size = 0)
    {
        // Select a font; size given in points
        if ($family == '')
            $family = $this->FontFamily;
        else
            $family = strtolower($family);
        $style = strtoupper($style);
        if (strpos($style, 'U') !== false) {
            $this->underline = true;
            $style = str_replace('U', '', $style);
        } else
            $this->underline = false;
        if ($style == 'IB')
            $style = 'BI';
        if ($size == 0)
            $size = $this->FontSizePt;
        // Test if font is already selected
        if ($this->FontFamily == $family && $this->FontStyle == $style && $this->FontSizePt == $size)
            return;
        // Test if font is already loaded
        $fontkey = $family . $style;
        if (!isset($this->fonts[$fontkey])) {
            // Test if one of the core fonts
            if ($family == 'arial')
                $family = 'helvetica';
            if (in_array($family, $this->CoreFonts)) {
                if ($family == 'symbol' || $family == 'zapfdingbats')
                    $style = '';
                $fontkey = $family . $style;
                if (!isset($this->fonts[$fontkey]))
                    $this->AddFont($family, $style);
            } else
                $this->Error('Undefined font: ' . $family . ' ' . $style);
        }
        // Select it
        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSizePt = $size;
        $this->FontSize = $size / $this->k;
        $this->CurrentFont = $this->fonts[$fontkey];
        if ($this->page > 0)
            $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
    }

    function SetFontSize($size)
    {
        // Set font size in points
        if ($this->FontSizePt == $size)
            return;
        $this->FontSizePt = $size;
        $this->FontSize = $size / $this->k;
        if ($this->page > 0 && isset($this->CurrentFont))
            $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
    }

    function AddLink()
    {
        // Create a new internal link
        $n = count($this->links) + 1;
        $this->links[$n] = array(0, 0);
        return $n;
    }

    function SetLink($link, $y = 0, $page = -1)
    {
        // Set destination of internal link
        if ($y == -1)
            $y = $this->y;
        if ($page == -1)
            $page = $this->page;
        $this->links[$link] = array($page, $y);
    }

    function Link($x, $y, $w, $h, $link)
    {
        // Put a link on the page
        $this->PageLinks[$this->page][] = array($x * $this->k, $this->hPt - $y * $this->k, $w * $this->k, $h * $this->k, $link);
    }

    function Text($x, $y, $txt)
    {
        // Output a string
        if (!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $txt = (string)$txt;
        $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        if ($this->underline && $txt !== '')
            $s .= ' ' . $this->_dounderline($x, $y, $txt);
        if ($this->ColorFlag)
            $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
        $this->_out($s);
    }

    function AcceptPageBreak()
    {
        // Accept automatic page break or not
        return $this->AutoPageBreak;
    }

    function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        // Output a cell
        $k = $this->k;
        if ($this->y + $h > $this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()) {
            // Automatic page break
            $x = $this->x;
            $ws = $this->ws;
            if ($ws > 0) {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->AddPage($this->CurOrientation, $this->CurPageSize, $this->CurRotation);
            $this->x = $x;
            if ($ws > 0) {
                $this->ws = $ws;
                $this->_out(sprintf('%.3F Tw', $ws * $k));
            }
        }
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $s = '';
        if ($fill || $border == 1) {
            if ($fill)
                $op = ($border == 1) ? 'B' : 'f';
            else
                $op = 'S';
            $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
        }
        if (is_string($border)) {
            $x = $this->x;
            $y = $this->y;
            if (strpos($border, 'L') !== false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y + $h)) * $k);
            if (strpos($border, 'T') !== false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);
            if (strpos($border, 'R') !== false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
            if (strpos($border, 'B') !== false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
        }
        $txt = (string)$txt;
        if ($txt !== '') {
            if (!isset($this->CurrentFont))
                $this->Error('No font has been set');
            if ($align == 'R')
                $dx = $w - $this->cMargin - $this->GetStringWidth($txt);
            elseif ($align == 'C')
                $dx = ($w - $this->GetStringWidth($txt)) / 2;
            else
                $dx = $this->cMargin;
            if ($this->ColorFlag)
                $s .= 'q ' . $this->TextColor . ' ';
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x + $dx) * $k, ($this->h - ($this->y + .5 * $h + .3 * $this->FontSize)) * $k, $this->_escape($txt));
            if ($this->underline)
                $s .= ' ' . $this->_dounderline($this->x + $dx, $this->y + .5 * $h + .3 * $this->FontSize, $txt);
            if ($this->ColorFlag)
                $s .= ' Q';
            if ($link)
                $this->Link($this->x + $dx, $this->y + .5 * $h - .5 * $this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
        }
        if ($s)
            $this->_out($s);
        $this->lasth = $h;
        if ($ln > 0) {
            // Go to next line
            $this->y += $h;
            if ($ln == 1)
                $this->x = $this->lMargin;
        } else
            $this->x += $w;
    }

    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        // Output text with automatic or explicit line breaks
        if (!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            } else {
                $b2 = '';
                if (strpos($border, 'L') !== false)
                    $b2 .= 'L';
                if (strpos($border, 'R') !== false)
                    $b2 .= 'R';
                $b = (strpos($border, 'T') !== false) ? $b2 . 'T' : $b2;
            }
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            if ($c == "\n") {
                // Explicit line break
                if ($this->ws > 0) {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border && $nl == 2)
                    $b = $b2;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                    if ($this->ws > 0) {
                        $this->ws = 0;
                        $this->_out('0 Tw');
                    }
                    $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                } else {
                    if ($align == 'J') {
                        $this->ws = ($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0;
                        $this->_out(sprintf('%.3F Tw', $this->ws * $this->k));
                    }
                    $this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border && $nl == 2)
                    $b = $b2;
            } else
                $i++;
        }
        // Last chunk
        if ($this->ws > 0) {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        if ($border && strpos($border, 'B') !== false)
            $b .= 'B';
        $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
        $this->x = $this->lMargin;
    }

    function Write($h, $txt, $link = '')
    {
        // Output text in flowing mode
        if (!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            if ($c == "\n") {
                // Explicit line break
                $this->Cell($w, $h, substr($s, $j, $i - $j), 0, 2, '', false, $link);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                if ($nl == 1) {
                    $this->x = $this->lMargin;
                    $w = $this->w - $this->rMargin - $this->x;
                    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                }
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1) {
                    if ($this->x > $this->lMargin) {
                        // Move to next line
                        $this->x = $this->lMargin;
                        $this->y += $h;
                        $w = $this->w - $this->rMargin - $this->x;
                        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                        $i++;
                        $nl++;
                        continue;
                    }
                    if ($i == $j)
                        $i++;
                    $this->Cell($w, $h, substr($s, $j, $i - $j), 0, 2, '', false, $link);
                } else {
                    $this->Cell($w, $h, substr($s, $j, $sep - $j), 0, 2, '', false, $link);
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                if ($nl == 1) {
                    $this->x = $this->lMargin;
                    $w = $this->w - $this->rMargin - $this->x;
                    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                }
                $nl++;
            } else
                $i++;
        }
        // Last chunk
        if ($i != $j)
            $this->Cell($l / 1000 * $this->FontSize, $h, substr($s, $j), 0, 0, '', false, $link);
    }

    function Ln($h = null)
    {
        // Line feed; default value is the last cell height
        $this->x = $this->lMargin;
        if ($h === null)
            $this->y += $this->lasth;
        else
            $this->y += $h;
    }

    function Image($file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '')
    {
        // Put an image on the page
        if ($file == '')
            $this->Error('Image file name is empty');
        if (!isset($this->images[$file])) {
            // First use of this image, get info
            if ($type == '') {
                $pos = strrpos($file, '.');
                if (!$pos)
                    $this->Error('Image file has no extension and no type was specified: ' . $file);
                $type = substr($file, $pos + 1);
            }
            $type = strtolower($type);
            if ($type == 'jpeg')
                $type = 'jpg';
            $mtd = '_parse' . $type;
            if (!method_exists($this, $mtd))
                $this->Error('Unsupported image type: ' . $type);
            $info = $this->$mtd($file);
            $info['i'] = count($this->images) + 1;
            $this->images[$file] = $info;
        } else
            $info = $this->images[$file];

        // Automatic width and height calculation if needed
        if ($w == 0 && $h == 0) {
            // Put image at 96 dpi
            $w = -96;
            $h = -96;
        }
        if ($w < 0)
            $w = -$info['w'] * 72 / $w / $this->k;
        if ($h < 0)
            $h = -$info['h'] * 72 / $h / $this->k;
        if ($w == 0)
            $w = $h * $info['w'] / $info['h'];
        if ($h == 0)
            $h = $w * $info['h'] / $info['w'];

        // Flowing mode
        if ($y === null) {
            if ($this->y + $h > $this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()) {
                // Automatic page break
                $x2 = $this->x;
                $this->AddPage($this->CurOrientation, $this->CurPageSize, $this->CurRotation);
                $this->x = $x2;
            }
            $y = $this->y;
            $this->y += $h;
        }

        if ($x === null)
            $x = $this->x;
        $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k, $info['i']));
        if ($link)
            $this->Link($x, $y, $w, $h, $link);
    }

    function GetPageWidth()
    {
        // Get current page width
        return $this->w;
    }

    function GetPageHeight()
    {
        // Get current page height
        return $this->h;
    }

    function GetX()
    {
        // Get x position
        return $this->x;
    }

    function SetX($x)
    {
        // Set x position
        if ($x >= 0)
            $this->x = $x;
        else
            $this->x = $this->w + $x;
    }

    function GetY()
    {
        // Get y position
        return $this->y;
    }

    function SetY($y, $resetX = true)
    {
        // Set y position and optionally reset x
        if ($y >= 0)
            $this->y = $y;
        else
            $this->y = $this->h + $y;
        if ($resetX)
            $this->x = $this->lMargin;
    }

    function SetXY($x, $y)
    {
        // Set x and y positions
        $this->SetX($x);
        $this->SetY($y, false);
    }

    function Output($dest = '', $name = '', $isUTF8 = false)
    {
        // Output PDF to some destination
        $this->Close();
        if (strlen($name) == 1 && strlen($dest) != 1) {
            // Fix parameter order
            $tmp = $dest;
            $dest = $name;
            $name = $tmp;
        }
        if ($dest == '')
            $dest = 'I';
        if ($name == '')
            $name = 'doc.pdf';
        switch (strtoupper($dest)) {
            case 'I':
                // Send to standard output
                $this->_checkoutput();
                if (PHP_SAPI != 'cli') {
                    // We send to a browser
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; ' . $this->_httpencode('filename', $name, $isUTF8));
                    header('Cache-Control: private, max-age=0, must-revalidate');
                    header('Pragma: public');
                }
                echo $this->buffer;
                break;
            case 'D':
                // Download file
                $this->_checkoutput();
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; ' . $this->_httpencode('filename', $name, $isUTF8));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $this->buffer;
                break;
            case 'F':
                // Save to local file
                if (!file_put_contents($name, $this->buffer))
                    $this->Error('Unable to create output file: ' . $name);
                break;
            case 'S':
                // Return as a string
                return $this->buffer;
            default:
                $this->Error('Incorrect output destination: ' . $dest);
        }
        return '';
    }

    /*******************************************************************************
     *                              Protected methods                               *
     *******************************************************************************/

    protected function _checkoutput()
    {
        if (PHP_SAPI != 'cli') {
            if (headers_sent($file, $line))
                $this->Error("Some data has already been output, can't send PDF file (output started at $file:$line)");
        }
        if (ob_get_length()) {
            // The output buffer is not empty
            if (preg_match('/^(\xEF\xBB\xBF)?\s*$/', ob_get_contents())) {
                // It contains only a UTF-8 BOM and/or whitespace, let's clean it
                ob_clean();
            } else
                $this->Error("Some data has already been output, can't send PDF file");
        }
    }

    protected function _getpagesize($size)
    {
        if (is_string($size)) {
            $size = strtolower($size);
            if (!isset($this->StdPageSizes[$size]))
                $this->Error('Unknown page size: ' . $size);
            $a = $this->StdPageSizes[$size];
            return array($a[0] / $this->k, $a[1] / $this->k);
        } else {
            if ($size[0] > $size[1])
                return array($size[1], $size[0]);
            else
                return $size;
        }
    }

    protected function _beginpage($orientation, $size, $rotation)
    {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->PageLinks[$this->page] = array();
        $this->state = 2;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
        $this->FontFamily = '';
        // Check page size and orientation
        if ($orientation == '')
            $orientation = $this->DefOrientation;
        else
            $orientation = strtoupper($orientation[0]);
        if ($size == '')
            $size = $this->DefPageSize;
        else
            $size = $this->_getpagesize($size);
        if ($orientation != $this->CurOrientation || $size[0] != $this->CurPageSize[0] || $size[1] != $this->CurPageSize[1]) {
            // New size or orientation
            if ($orientation == 'P') {
                $this->w = $size[0];
                $this->h = $size[1];
            } else {
                $this->w = $size[1];
                $this->h = $size[0];
            }
            $this->wPt = $this->w * $this->k;
            $this->hPt = $this->h * $this->k;
            $this->PageBreakTrigger = $this->h - $this->bMargin;
            $this->CurOrientation = $orientation;
            $this->CurPageSize = $size;
        }
        if ($orientation != $this->DefOrientation || $size[0] != $this->DefPageSize[0] || $size[1] != $this->DefPageSize[1])
            $this->PageInfo[$this->page]['size'] = array($this->wPt, $this->hPt);
        if ($rotation != 0) {
            if ($rotation % 90 != 0)
                $this->Error('Incorrect rotation value: ' . $rotation);
            $this->PageInfo[$this->page]['rotation'] = $rotation;
        }
        $this->CurRotation = $rotation;
    }

   /* protected function _endpage()
    {
        $this->state = 1;
    }*/

    protected function _loadfont($path)
    {
        // Load a font definition file
        include($path);
        if (!isset($name))
            $this->Error('Could not include font definition file: ' . $path);
        if (isset($enc))
            $enc = strtolower($enc);
        if (!isset($subsetted))
            $subsetted = false;
        return get_defined_vars();
    }

    protected function _isascii($s)
    {
        // Test if string is ASCII
        $nb = strlen($s);
        for ($i = 0; $i < $nb; $i++) {
            if (ord($s[$i]) > 127)
                return false;
        }
        return true;
    }

    protected function _httpencode($param, $value, $isUTF8)
    {
        // Encode HTTP header field parameter
        if ($this->_isascii($value))
            return $param . '="' . $value . '"';
        if (!$isUTF8)
            $value = $this->_UTF8encode($value);
        return $param . "*=UTF-8''" . rawurlencode($value);
    }

    protected function _UTF8encode($s)
    {
        // Convert ISO-8859-1 to UTF-8
        if ($this->iconv)
            return iconv('ISO-8859-1', 'UTF-8', $s);
        $res = '';
        $nb = strlen($s);
        for ($i = 0; $i < $nb; $i++) {
            $c = $s[$i];
            $v = ord($c);
            if ($v >= 128) {
                $res .= chr(0xC0 | ($v >> 6));
                $res .= chr(0x80 | ($v & 0x3F));
            } else
                $res .= $c;
        }
        return $res;
    }

    protected function _UTF8toUTF16($s)
    {
        // Convert UTF-8 to UTF-16BE with BOM
        $res = "\xFE\xFF";
        if ($this->iconv)
            return $res . iconv('UTF-8', 'UTF-16BE', $s);
        $nb = strlen($s);
        $i = 0;
        while ($i < $nb) {
            $c1 = ord($s[$i++]);
            if ($c1 >= 224) {
                // 3-byte character
                $c2 = ord($s[$i++]);
                $c3 = ord($s[$i++]);
                $res .= chr((($c1 & 0x0F) << 4) + (($c2 & 0x3C) >> 2));
                $res .= chr((($c2 & 0x03) << 6) + ($c3 & 0x3F));
            } elseif ($c1 >= 192) {
                // 2-byte character
                $c2 = ord($s[$i++]);
                $res .= chr(($c1 & 0x1C) >> 2);
                $res .= chr((($c1 & 0x03) << 6) + ($c2 & 0x3F));
            } else {
                // Single-byte character
                $res .= "\0" . chr($c1);
            }
        }
        return $res;
    }

    protected function _escape($s)
    {
        // Escape special characters
        if (strpos($s, '(') !== false || strpos($s, ')') !== false || strpos($s, '\\') !== false || strpos($s, "\r") !== false)
            return str_replace(array('\\', '(', ')', "\r"), array('\\\\', '\\(', '\\)', '\\r'), $s);
        else
            return $s;
    }

    protected function _textstring($s)
    {
        // Format a text string
        if (!$this->_isascii($s))
            $s = $this->_UTF8toUTF16($s);
        return '(' . $this->_escape($s) . ')';
    }

    protected function _dounderline($x, $y, $txt)
    {
        // Underline text
        $up = $this->CurrentFont['up'];
        $ut = $this->CurrentFont['ut'];
        $w = $this->GetStringWidth($txt) + $this->ws * substr_count($txt, ' ');
        return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, ($this->h - ($y - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
    }

    protected function _parsejpg($file)
    {
        // Extract info from a JPEG file
        $a = getimagesize($file);
        if (!$a)
            $this->Error('Missing or incorrect image file: ' . $file);
        if ($a[2] != 2)
            $this->Error('Not a JPEG file: ' . $file);
        if (!isset($a['channels']) || $a['channels'] == 3)
            $colspace = 'DeviceRGB';
        elseif ($a['channels'] == 4)
            $colspace = 'DeviceCMYK';
        else
            $colspace = 'DeviceGray';
        $bpc = isset($a['bits']) ? $a['bits'] : 8;
        $data = file_get_contents($file);
        return array('w' => $a[0], 'h' => $a[1], 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'DCTDecode', 'data' => $data);
    }

    protected function _parsepng($file)
    {
        // Extract info from a PNG file
        $f = fopen($file, 'rb');
        if (!$f)
            $this->Error('Can\'t open image file: ' . $file);
        $info = $this->_parsepngstream($f, $file);
        fclose($f);
        return $info;
    }

    protected function _parsepngstream($f, $file)
    {
        // Check signature
        if ($this->_readstream($f, 8) != chr(137) . 'PNG' . chr(13) . chr(10) . chr(26) . chr(10))
            $this->Error('Not a PNG file: ' . $file);

        // Read header chunk
        $this->_readstream($f, 4);
        if ($this->_readstream($f, 4) != 'IHDR')
            $this->Error('Incorrect PNG file: ' . $file);
        $w = $this->_readint($f);
        $h = $this->_readint($f);
        $bpc = ord($this->_readstream($f, 1));
        if ($bpc > 8)
            $this->Error('16-bit depth not supported: ' . $file);
        $ct = ord($this->_readstream($f, 1));
        if ($ct == 0 || $ct == 4)
            $colspace = 'DeviceGray';
        elseif ($ct == 2 || $ct == 6)
            $colspace = 'DeviceRGB';
        elseif ($ct == 3)
            $colspace = 'Indexed';
        else
            $this->Error('Unknown color type: ' . $file);
        if (ord($this->_readstream($f, 1)) != 0)
            $this->Error('Unknown compression method: ' . $file);
        if (ord($this->_readstream($f, 1)) != 0)
            $this->Error('Unknown filter method: ' . $file);
        if (ord($this->_readstream($f, 1)) != 0)
            $this->Error('Interlacing not supported: ' . $file);
        $this->_readstream($f, 4);
        $dp = '/Predictor 15 /Colors ' . ($colspace == 'DeviceRGB' ? 3 : 1) . ' /BitsPerComponent ' . $bpc . ' /Columns ' . $w;

        // Scan chunks looking for palette, transparency and image data
        $pal = '';
        $trns = '';
        $data = '';
        do {
            $n = $this->_readint($f);
            $type = $this->_readstream($f, 4);
            if ($type == 'PLTE') {
                // Read palette
                $pal = $this->_readstream($f, $n);
                $this->_readstream($f, 4);
            } elseif ($type == 'tRNS') {
                // Read transparency info
                $t = $this->_readstream($f, $n);
                if ($ct == 0)
                    $trns = array(ord(substr($t, 1, 1)));
                elseif ($ct == 2)
                    $trns = array(ord(substr($t, 1, 1)), ord(substr($t, 3, 1)), ord(substr($t, 5, 1)));
                else {
                    $pos = strpos($t, chr(0));
                    if ($pos !== false)
                        $trns = array($pos);
                }
                $this->_readstream($f, 4);
            } elseif ($type == 'IDAT') {
                // Read image data block
                $data .= $this->_readstream($f, $n);
                $this->_readstream($f, 4);
            } elseif ($type == 'IEND')
                break;
            else
                $this->_readstream($f, $n + 4);
        } while ($n);

        if ($colspace == 'Indexed' && empty($pal))
            $this->Error('Missing palette in ' . $file);
        $info = array('w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'FlateDecode', 'dp' => $dp, 'pal' => $pal, 'trns' => $trns);
        if ($ct >= 4) {
            // Extract alpha channel
            if (!function_exists('gzuncompress'))
                $this->Error('Zlib not available, can\'t handle alpha channel: ' . $file);
            $data = gzuncompress($data);
            $color = '';
            $alpha = '';
            if ($ct == 4) {
                // Gray image
                $len = 2 * $w;
                for ($i = 0; $i < $h; $i++) {
                    $pos = (1 + $len) * $i;
                    $color .= $data[$pos];
                    $alpha .= $data[$pos];
                    $line = substr($data, $pos + 1, $len);
                    $color .= preg_replace('/(.)./s', '$1', $line);
                    $alpha .= preg_replace('/.(.)/s', '$1', $line);
                }
            } else {
                // RGB image
                $len = 4 * $w;
                for ($i = 0; $i < $h; $i++) {
                    $pos = (1 + $len) * $i;
                    $color .= $data[$pos];
                    $alpha .= $data[$pos];
                    $line = substr($data, $pos + 1, $len);
                    $color .= preg_replace('/(.{3})./s', '$1', $line);
                    $alpha .= preg_replace('/.{3}(.)/s', '$1', $line);
                }
            }
            unset($data);
            $data = gzcompress($color);
            $info['smask'] = gzcompress($alpha);
            $this->WithAlpha = true;
            if ($this->PDFVersion < '1.4')
                $this->PDFVersion = '1.4';
        }
        $info['data'] = $data;
        return $info;
    }

    protected function _readstream($f, $n)
    {
        // Read n bytes from stream
        $res = '';
        while ($n > 0 && !feof($f)) {
            $s = fread($f, $n);
            if ($s === false)
                $this->Error('Error while reading stream');
            $n -= strlen($s);
            $res .= $s;
        }
        if ($n > 0)
            $this->Error('Unexpected end of stream');
        return $res;
    }

    protected function _readint($f)
    {
        // Read a 4-byte integer from stream
        $a = unpack('Ni', $this->_readstream($f, 4));
        return $a['i'];
    }

    protected function _parsegif($file)
    {
        // Extract info from a GIF file (via PNG conversion)
        if (!function_exists('imagepng'))
            $this->Error('GD extension is required for GIF support');
        if (!function_exists('imagecreatefromgif'))
            $this->Error('GD has no GIF read support');
        $im = imagecreatefromgif($file);
        if (!$im)
            $this->Error('Missing or incorrect image file: ' . $file);
        imageinterlace($im, 0);
        ob_start();
        imagepng($im);
        $data = ob_get_clean();
        imagedestroy($im);
        $f = fopen('php://temp', 'rb+');
        if (!$f)
            $this->Error('Unable to create memory stream');
        fwrite($f, $data);
        rewind($f);
        $info = $this->_parsepngstream($f, $file);
        fclose($f);
        return $info;
    }

    protected function _out($s)
    {
        // Add a line to the current page
        if ($this->state == 2)
            $this->pages[$this->page] .= $s . "\n";
        elseif ($this->state == 0)
            $this->Error('No page has been added yet');
        elseif ($this->state == 1)
            $this->Error('Invalid call');
        elseif ($this->state == 3)
            $this->Error('The document is closed');
    }

    protected function _put($s)
    {
        // Add a line to the document
        $this->buffer .= $s . "\n";
    }

    protected function _getoffset()
    {
        return strlen($this->buffer);
    }

    protected function _newobj($n = null)
    {
        // Begin a new object
        if ($n === null)
            $n = ++$this->n;
        $this->offsets[$n] = $this->_getoffset();
        $this->_put($n . ' 0 obj');
    }

    protected function _putstream($data)
    {
        $this->_put('stream');
        $this->_put($data);
        $this->_put('endstream');
    }

    protected function _putstreamobject($data)
    {
        if ($this->compress) {
            $entries = '/Filter /FlateDecode ';
            $data = gzcompress($data);
        } else
            $entries = '';
        $entries .= '/Length ' . strlen($data);
        $this->_newobj();
        $this->_put('<<' . $entries . '>>');
        $this->_putstream($data);
        $this->_put('endobj');
    }

    protected function _putlinks($n)
    {
        foreach ($this->PageLinks[$n] as $pl) {
            $this->_newobj();
            $rect = sprintf('%.2F %.2F %.2F %.2F', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3]);
            $s = '<</Type /Annot /Subtype /Link /Rect [' . $rect . '] /Border [0 0 0] ';
            if (is_string($pl[4]))
                $s .= '/A <</S /URI /URI ' . $this->_textstring($pl[4]) . '>>>>';
            else {
                $l = $this->links[$pl[4]];
                if (isset($this->PageInfo[$l[0]]['size']))
                    $h = $this->PageInfo[$l[0]]['size'][1];
                else
                    $h = ($this->DefOrientation == 'P') ? $this->DefPageSize[1] * $this->k : $this->DefPageSize[0] * $this->k;
                $s .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>', $this->PageInfo[$l[0]]['n'], $h - $l[1] * $this->k);
            }
            $this->_put($s);
            $this->_put('endobj');
        }
    }

    protected function _putpage($n)
    {
        $this->_newobj();
        $this->_put('<</Type /Page');
        $this->_put('/Parent 1 0 R');
        if (isset($this->PageInfo[$n]['size']))
            $this->_put(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->PageInfo[$n]['size'][0], $this->PageInfo[$n]['size'][1]));
        if (isset($this->PageInfo[$n]['rotation']))
            $this->_put('/Rotate ' . $this->PageInfo[$n]['rotation']);
        $this->_put('/Resources 2 0 R');
        if (!empty($this->PageLinks[$n])) {
            $s = '/Annots [';
            foreach ($this->PageLinks[$n] as $pl)
                $s .= $pl[5] . ' 0 R ';
            $s .= ']';
            $this->_put($s);
        }
        if ($this->WithAlpha)
            $this->_put('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
        $this->_put('/Contents ' . ($this->n + 1) . ' 0 R>>');
        $this->_put('endobj');
        // Page content
        if (!empty($this->AliasNbPages))
            $this->pages[$n] = str_replace($this->AliasNbPages, $this->page, $this->pages[$n]);
        $this->_putstreamobject($this->pages[$n]);
        // Link annotations
        $this->_putlinks($n);
    }

    protected function _putpages()
    {
        $nb = $this->page;
        $n = $this->n;
        for ($i = 1; $i <= $nb; $i++) {
            $this->PageInfo[$i]['n'] = ++$n;
            $n++;
            foreach ($this->PageLinks[$i] as &$pl)
                $pl[5] = ++$n;
            unset($pl);
        }
        for ($i = 1; $i <= $nb; $i++)
            $this->_putpage($i);
        // Pages root
        $this->_newobj(1);
        $this->_put('<</Type /Pages');
        $kids = '/Kids [';
        for ($i = 1; $i <= $nb; $i++)
            $kids .= $this->PageInfo[$i]['n'] . ' 0 R ';
        $kids .= ']';
        $this->_put($kids);
        $this->_put('/Count ' . $nb);
        if ($this->DefOrientation == 'P') {
            $w = $this->DefPageSize[0];
            $h = $this->DefPageSize[1];
        } else {
            $w = $this->DefPageSize[1];
            $h = $this->DefPageSize[0];
        }
        $this->_put(sprintf('/MediaBox [0 0 %.2F %.2F]', $w * $this->k, $h * $this->k));
        $this->_put('>>');
        $this->_put('endobj');
    }

    protected function _putfonts()
    {
        foreach ($this->FontFiles as $file => $info) {
            // Font file embedding
            $this->_newobj();
            $this->FontFiles[$file]['n'] = $this->n;
            $font = file_get_contents($file);
            if (!$font)
                $this->Error('Font file not found: ' . $file);
            $compressed = (substr($file, -2) == '.z');
            if (!$compressed && isset($info['length2']))
                $font = substr($font, 6, $info['length1']) . substr($font, 6 + $info['length1'] + 6, $info['length2']);
            $this->_put('<</Length ' . strlen($font));
            if ($compressed)
                $this->_put('/Filter /FlateDecode');
            $this->_put('/Length1 ' . $info['length1']);
            if (isset($info['length2']))
                $this->_put('/Length2 ' . $info['length2'] . ' /Length3 0');
            $this->_put('>>');
            $this->_putstream($font);
            $this->_put('endobj');
        }
        foreach ($this->fonts as $k => $font) {
            // Encoding
            if (isset($font['diff'])) {
                if (!isset($this->encodings[$font['enc']])) {
                    $this->_newobj();
                    $this->_put('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $font['diff'] . ']>>');
                    $this->_put('endobj');
                    $this->encodings[$font['enc']] = $this->n;
                }
            }
            // ToUnicode CMap
            if (isset($font['uv'])) {
                if (isset($font['enc']))
                    $cmapkey = $font['enc'];
                else
                    $cmapkey = $font['name'];
                if (!isset($this->cmaps[$cmapkey])) {
                    $cmap = $this->_tounicodecmap($font['uv']);
                    $this->_putstreamobject($cmap);
                    $this->cmaps[$cmapkey] = $this->n;
                }
            }
            // Font object
            $this->fonts[$k]['n'] = $this->n + 1;
            $type = $font['type'];
            $name = $font['name'];
            if ($font['subsetted'])
                $name = 'AAAAAA+' . $name;
            if ($type == 'Core') {
                // Core font
                $this->_newobj();
                $this->_put('<</Type /Font');
                $this->_put('/BaseFont /' . $name);
                $this->_put('/Subtype /Type1');
                if ($name != 'Symbol' && $name != 'ZapfDingbats')
                    $this->_put('/Encoding /WinAnsiEncoding');
                if (isset($font['uv']))
                    $this->_put('/ToUnicode ' . $this->cmaps[$cmapkey] . ' 0 R');
                $this->_put('>>');
                $this->_put('endobj');
            } elseif ($type == 'Type1' || $type == 'TrueType') {
                // Additional Type1 or TrueType/OpenType font
                $this->_newobj();
                $this->_put('<</Type /Font');
                $this->_put('/BaseFont /' . $name);
                $this->_put('/Subtype /' . $type);
                $this->_put('/FirstChar 32 /LastChar 255');
                $this->_put('/Widths ' . ($this->n + 1) . ' 0 R');
                $this->_put('/FontDescriptor ' . ($this->n + 2) . ' 0 R');
                if (isset($font['diff']))
                    $this->_put('/Encoding ' . $this->encodings[$font['enc']] . ' 0 R');
                else
                    $this->_put('/Encoding /WinAnsiEncoding');
                if (isset($font['uv']))
                    $this->_put('/ToUnicode ' . $this->cmaps[$cmapkey] . ' 0 R');
                $this->_put('>>');
                $this->_put('endobj');
                // Widths
                $this->_newobj();
                $cw = $font['cw'];
                $s = '[';
                for ($i = 32; $i <= 255; $i++)
                    $s .= $cw[chr($i)] . ' ';
                $this->_put($s . ']');
                $this->_put('endobj');
                // Descriptor
                $this->_newobj();
                $s = '<</Type /FontDescriptor /FontName /' . $name;
                foreach ($font['desc'] as $k => $v)
                    $s .= ' /' . $k . ' ' . $v;
                if (!empty($font['file']))
                    $s .= ' /FontFile' . ($type == 'Type1' ? '' : '2') . ' ' . $this->FontFiles[$font['file']]['n'] . ' 0 R';
                $this->_put($s . '>>');
                $this->_put('endobj');
            } else {
                // Allow for additional types
                $mtd = '_put' . strtolower($type);
                if (!method_exists($this, $mtd))
                    $this->Error('Unsupported font type: ' . $type);
                $this->$mtd($font);
            }
        }
    }

    protected function _tounicodecmap($uv)
    {
        $ranges = '';
        $nbr = 0;
        $chars = '';
        $nbc = 0;
        foreach ($uv as $c => $v) {
            if (is_array($v)) {
                $ranges .= sprintf("<%02X> <%02X> <%04X>\n", $c, $c + $v[1] - 1, $v[0]);
                $nbr++;
            } else {
                $chars .= sprintf("<%02X> <%04X>\n", $c, $v);
                $nbc++;
            }
        }
        $s = "/CIDInit /ProcSet findresource begin\n";
        $s .= "12 dict begin\n";
        $s .= "begincmap\n";
        $s .= "/CIDSystemInfo\n";
        $s .= "<</Registry (Adobe)\n";
        $s .= "/Ordering (UCS)\n";
        $s .= "/Supplement 0\n";
        $s .= ">> def\n";
        $s .= "/CMapName /Adobe-Identity-UCS def\n";
        $s .= "/CMapType 2 def\n";
        $s .= "1 begincodespacerange\n";
        $s .= "<00> <FF>\n";
        $s .= "endcodespacerange\n";
        if ($nbr > 0) {
            $s .= "$nbr beginbfrange\n";
            $s .= $ranges;
            $s .= "endbfrange\n";
        }
        if ($nbc > 0) {
            $s .= "$nbc beginbfchar\n";
            $s .= $chars;
            $s .= "endbfchar\n";
        }
        $s .= "endcmap\n";
        $s .= "CMapName currentdict /CMap defineresource pop\n";
        $s .= "end\n";
        $s .= "end";
        return $s;
    }

    protected function _putimages()
    {
        foreach (array_keys($this->images) as $file) {
            $this->_putimage($this->images[$file]);
            unset($this->images[$file]['data']);
            unset($this->images[$file]['smask']);
        }
    }

    protected function _putimage(&$info)
    {
        $this->_newobj();
        $info['n'] = $this->n;
        $this->_put('<</Type /XObject');
        $this->_put('/Subtype /Image');
        $this->_put('/Width ' . $info['w']);
        $this->_put('/Height ' . $info['h']);
        if ($info['cs'] == 'Indexed')
            $this->_put('/ColorSpace [/Indexed /DeviceRGB ' . (strlen($info['pal']) / 3 - 1) . ' ' . ($this->n + 1) . ' 0 R]');
        else {
            $this->_put('/ColorSpace /' . $info['cs']);
            if ($info['cs'] == 'DeviceCMYK')
                $this->_put('/Decode [1 0 1 0 1 0 1 0]');
        }
        $this->_put('/BitsPerComponent ' . $info['bpc']);
        if (isset($info['f']))
            $this->_put('/Filter /' . $info['f']);
        if (isset($info['dp']))
            $this->_put('/DecodeParms <<' . $info['dp'] . '>>');
        if (isset($info['trns']) && is_array($info['trns'])) {
            $trns = '';
            for ($i = 0; $i < count($info['trns']); $i++)
                $trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
            $this->_put('/Mask [' . $trns . ']');
        }
        if (isset($info['smask']))
            $this->_put('/SMask ' . ($this->n + 1) . ' 0 R');
        $this->_put('/Length ' . strlen($info['data']) . '>>');
        $this->_putstream($info['data']);
        $this->_put('endobj');
        // Soft mask
        if (isset($info['smask'])) {
            $dp = '/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns ' . $info['w'];
            $smask = array('w' => $info['w'], 'h' => $info['h'], 'cs' => 'DeviceGray', 'bpc' => 8, 'f' => $info['f'], 'dp' => $dp, 'data' => $info['smask']);
            $this->_putimage($smask);
        }
        // Palette
        if ($info['cs'] == 'Indexed')
            $this->_putstreamobject($info['pal']);
    }

    protected function _putxobjectdict()
    {
        foreach ($this->images as $image)
            $this->_put('/I' . $image['i'] . ' ' . $image['n'] . ' 0 R');
    }

    protected function _putresourcedict()
    {
        $this->_put('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->_put('/Font <<');
        foreach ($this->fonts as $font)
            $this->_put('/F' . $font['i'] . ' ' . $font['n'] . ' 0 R');
        $this->_put('>>');
        $this->_put('/XObject <<');
        $this->_putxobjectdict();
        $this->_put('>>');
    }

    protected function _putresources()
    {
        $this->_putfonts();
        $this->_putimages();
        // Resource dictionary
        $this->_newobj(2);
        $this->_put('<<');
        $this->_putresourcedict();
        $this->_put('>>');
        $this->_put('endobj');
    }

    protected function _putinfo()
    {
        $date = @date('YmdHisO', $this->CreationDate);
        $this->metadata['CreationDate'] = 'D:' . substr($date, 0, -2) . "'" . substr($date, -2) . "'";
        foreach ($this->metadata as $key => $value)
            $this->_put('/' . $key . ' ' . $this->_textstring($value));
    }

    protected function _putcatalog()
    {
        $n = $this->PageInfo[1]['n'];
        $this->_put('/Type /Catalog');
        $this->_put('/Pages 1 0 R');
        if ($this->ZoomMode == 'fullpage')
            $this->_put('/OpenAction [' . $n . ' 0 R /Fit]');
        elseif ($this->ZoomMode == 'fullwidth')
            $this->_put('/OpenAction [' . $n . ' 0 R /FitH null]');
        elseif ($this->ZoomMode == 'real')
            $this->_put('/OpenAction [' . $n . ' 0 R /XYZ null null 1]');
        elseif (!is_string($this->ZoomMode))
            $this->_put('/OpenAction [' . $n . ' 0 R /XYZ null null ' . sprintf('%.2F', $this->ZoomMode / 100) . ']');
        if ($this->LayoutMode == 'single')
            $this->_put('/PageLayout /SinglePage');
        elseif ($this->LayoutMode == 'continuous')
            $this->_put('/PageLayout /OneColumn');
        elseif ($this->LayoutMode == 'two')
            $this->_put('/PageLayout /TwoColumnLeft');
    }

    protected function _putheader()
    {
        $this->_put('%PDF-' . $this->PDFVersion);
    }

    protected function _puttrailer()
    {
        $this->_put('/Size ' . ($this->n + 1));
        $this->_put('/Root ' . $this->n . ' 0 R');
        $this->_put('/Info ' . ($this->n - 1) . ' 0 R');
    }

    protected function _enddoc()
    {
        $this->CreationDate = time();
        $this->_putheader();
        $this->_putpages();
        $this->_putresources();
        // Info
        $this->_newobj();
        $this->_put('<<');
        $this->_putinfo();
        $this->_put('>>');
        $this->_put('endobj');
        // Catalog
        $this->_newobj();
        $this->_put('<<');
        $this->_putcatalog();
        $this->_put('>>');
        $this->_put('endobj');
        // Cross-ref
        $offset = $this->_getoffset();
        $this->_put('xref');
        $this->_put('0 ' . ($this->n + 1));
        $this->_put('0000000000 65535 f ');
        for ($i = 1; $i <= $this->n; $i++)
            $this->_put(sprintf('%010d 00000 n ', $this->offsets[$i]));
        // Trailer
        $this->_put('trailer');
        $this->_put('<<');
        $this->_puttrailer();
        $this->_put('>>');
        $this->_put('startxref');
        $this->_put($offset);
        $this->_put('%%EOF');
        $this->state = 3;
    }



    function _wt_Reset_Datas(){
        $this->wt_Current_Tag = "";
        $this->wt_DataInfo = array();
        $this->wt_DataExtraInfo = array(
            "LAST_LINE_BR" => "",		//CURRENT LINE BREAK TYPE
            "CURRENT_LINE_BR" => "",	//LAST LINE BREAK TYPE
            "TAB_WIDTH" => 10			//The tab WIDTH IS IN mm
        );

        //if another measure unit is used ... calculate your OWN
        $this->wt_DataExtraInfo["TAB_WIDTH"] *= (72/25.4) / $this->k;
        /*
            $this->wt_FontInfo - do not reset, once read ... is OK!!!
        */
    }//function _wt_Reset_Datas(){

    /**
    Sets current tag to specified style
    @param		$tag - tag name
    $family - text font family
    $style - text style
    $size - text size
    $color - text color
    @return 	nothing
     */
    function SetStyle2($tag,$family,$style,$size,$color)
    {

        if ($tag == "ttags") $this->Error (">> ttags << is reserved TAG Name.");
        if ($tag == "") $this->Error ("Empty TAG Name.");

        //use case insensitive tags
        $tag=trim(strtoupper($tag));
        $this->TagStyle[$tag]['family']=trim($family);
        $this->TagStyle[$tag]['style']=trim($style);
        $this->TagStyle[$tag]['size']=trim($size);
        $this->TagStyle[$tag]['color']=trim($color);
    }//function SetStyle


    /**
    Sets current tag style as the current settings
    - if the tag name is not in the tag list then de "DEFAULT" tag is saved.
    This includes a fist call of the function SaveCurrentStyle()
    @param		$tag - tag name
    @return 	nothing
     */
    function ApplyStyle($tag){

        //use case insensitive tags
        $tag=trim(strtoupper($tag));

        if ($this->wt_Current_Tag == $tag) return;

        if (($tag == "") || (! isset($this->TagStyle[$tag]))) $tag = "DEFAULT";

        $this->wt_Current_Tag = $tag;

        $style = & $this->TagStyle[$tag];

        if (isset($style)){
            $this->SetFont('Arial', $style['style'], $style['size']);
            //this is textcolor in FPDF format
            if (isset($style['textcolor_fpdf'])) {
                $this->TextColor = $style['textcolor_fpdf'];
                $this->ColorFlag=($this->FillColor!=$this->TextColor);
            }else
            {
                if ($style['color'] <> ""){//if we have a specified color
                    $temp = explode(",", $style['color']);
                    $this->SetTextColor($temp[0], $temp[1], $temp[2]);
                }//fi
            }
            /**/
        }//isset
    }//function ApplyStyle($tag){

    /**
    Save the current settings as a tag default style under the DEFAUTLT tag name
    @param		none
    @return 	nothing
     */
    function SaveCurrentStyle(){
        //*
        $this->TagStyle['DEFAULT']['family'] = $this->FontFamily;;
        $this->TagStyle['DEFAULT']['style'] = $this->FontStyle;
        $this->TagStyle['DEFAULT']['size'] = $this->FontSizePt;
        $this->TagStyle['DEFAULT']['textcolor_fpdf'] = $this->TextColor;
        $this->TagStyle['DEFAULT']['color'] = "";
        /**/
    }//function SaveCurrentStyle

    /**
    Divides $this->wt_DataInfo and returnes a line from this variable
    @param		$w - Width of the text
    @return     $aLine = array() -> contains informations to draw a line
     */
    function MakeLine($w){

        $aDataInfo = & $this->wt_DataInfo;
        $aExtraInfo = & $this->wt_DataExtraInfo;

        //last line break >> current line break
        $aExtraInfo['LAST_LINE_BR'] = $aExtraInfo['CURRENT_LINE_BR'];
        $aExtraInfo['CURRENT_LINE_BR'] = "";

        if($w==0)
            $w=$this->w - $this->rMargin - $this->x;

        $wmax = ($w - 2*$this->cMargin) * 1000;//max width

        $aLine = array();//this will contain the result
        $return_result = false;//if break and return result
        $reset_spaces = false;

        $line_width = 0;//line string width
        $total_chars = 0;//total characters included in the result string
        $space_count = 0;//numer of spaces in the result string
        $fw = & $this->wt_FontInfo;//font info array

        $last_sepch = ""; //last separator character

        foreach ($aDataInfo as $key => $val){

            $s = $val['text'];

            $tag = &$val['tag'];

            $bParagraph = false;
            if (($s == "\t") && ($tag == 'pparg')){
                $bParagraph = true;
                $s = "\t";//place instead a TAB
            }

            $s_lenght=strlen($s);

            $i = 0;//from where is the string remain
            $j = 0;//untill where is the string good to copy -- leave this == 1->> copy at least one character!!!
            $str = "";
            $s_width = 0;	//string width
            $last_sep = -1; //last separator position
            $last_sepwidth = 0;
            $last_sepch_width = 0;
            $ante_last_sep = -1; //ante last separator position
            $spaces = 0;

            //parse the whole string
            while ($i < $s_lenght){
                $c = $s[$i];

                if($c == "\n"){//Explicit line break
                    $i++; //ignore/skip this caracter
                    $aExtraInfo['CURRENT_LINE_BR'] = "BREAK";
                    $return_result = true;
                    $reset_spaces = true;
                    break;
                }

                //space
                if($c == " "){
                    $space_count++;//increase the number of spaces
                    $spaces ++;
                }

                //	Font Width / Size Array
                if (!isset($fw[$tag]) || ($tag == "")){
                    //if this font was not used untill now,
                    $this->ApplyStyle($tag);
                    $fw[$tag]['w'] = $this->CurrentFont['cw'];//width
                    $fw[$tag]['s'] = $this->FontSize;//size
                }

                $char_width = $fw[$tag]['w'][$c] * $fw[$tag]['s'];

                //separators
                if(is_int(strpos(" ,.:;",$c))){

                    $ante_last_sep = $last_sep;
                    $ante_last_sepch = $last_sepch;
                    $ante_last_sepwidth = $last_sepwidth;
                    $ante_last_sepch_width = $last_sepch_width;

                    $last_sep = $i;//last separator position
                    $last_sepch = $c;//last separator char
                    $last_sepch_width = $char_width;//last separator char
                    $last_sepwidth = $s_width;

                }

                if ($c == "\t"){//TAB
                    $c = $s[$i] = "";
                    $char_width = $aExtraInfo['TAB_WIDTH'] * 1000;
                }

                if ($bParagraph == true){
                    $c = $s[$i] = "";
                    $char_width = $this->wt_TempData['LAST_TAB_REQSIZE']*1000 - $this->wt_TempData['LAST_TAB_SIZE'];
                    if ($char_width < 0) $char_width = 0;
                }



                $line_width += $char_width;

                if($line_width > $wmax){//Automatic line break

                    $aExtraInfo['CURRENT_LINE_BR'] = "AUTO";

                    if ($total_chars == 0) {
                        /* This MEANS that the $w (width) is lower than a char width...
                            Put $i and $j to 1 ... otherwise infinite while*/
                        $i = 1;
                        $j = 1;
                        $return_result = true;//YES RETURN THE RESULT!!!
                        break;
                    }//fi

                    if ($last_sep <> -1){
                        //we have a separator in this tag!!!
                        //untill now there one separator
                        if (($last_sepch == $c) && ($last_sepch != " ") && ($ante_last_sep <> -1)){
                            /*	this is the last character and it is a separator, if it is a space the leave it...
                                Have to jump back to the last separator... even a space
                            */
                            $last_sep = $ante_last_sep;
                            $last_sepch = $ante_last_sepch;
                            $last_sepwidth = $ante_last_sepwidth;
                        }

                        if ($last_sepch == " "){
                            $j = $last_sep;//just ignore the last space (it is at end of line)
                            $i = $last_sep + 1;
                            if ( $spaces > 0 ) $spaces --;
                            $s_width = $last_sepwidth;
                        }else{
                            $j = $last_sep + 1;
                            $i = $last_sep + 1;
                            $s_width = $last_sepwidth + $last_sepch_width;
                        }

                    }elseif(count($aLine) > 0){
                        //we have elements in the last tag!!!!
                        if ($last_sepch == " "){//the last tag ends with a space, have to remove it

                            $temp = & $aLine[ count($aLine)-1 ];

                            if ($temp['text'][strlen($temp['text'])-1] == " "){

                                $temp['text'] = substr($temp['text'], 0, strlen($temp['text']) - 1);
                                $temp['width'] -= $fw[ $temp['tag'] ]['w'][" "] * $fw[ $temp['tag'] ]['s'];
                                $temp['spaces'] --;

                                //imediat return from this function
                                break 2;
                            }else{
                                #die("should not be!!!");
                            }//fi
                        }//fi
                    }//fi else

                    $return_result = true;
                    break;
                }//fi - Auto line break

                //increase the string width ONLY when it is added!!!!
                $s_width += $char_width;

                $i++;
                $j = $i;
                $total_chars ++;
            }//while

            $str = substr($s, 0, $j);

            $sTmpStr = & $aDataInfo[$key]['text'];
            $sTmpStr = substr($sTmpStr, $i, strlen($sTmpStr));

            if (($sTmpStr == "") || ($sTmpStr === FALSE))//empty
                array_shift($aDataInfo);

            if ($val['text'] == $str){
            }

            if (!isset($val['href'])) $val['href']='';
            if (!isset($val['ypos'])) $val['ypos']=0;

            //we have a partial result
            array_push($aLine, array(
                'text' => $str,
                'tag' => $val['tag'],
                'href' => $val['href'],
                'width' => $s_width,
                'spaces' => $spaces,
                'ypos' => $val['ypos']
            ));

            $this->wt_TempData['LAST_TAB_SIZE'] = $s_width;
            $this->wt_TempData['LAST_TAB_REQSIZE'] = (isset($val['size'])) ? $val['size'] : 0;

            if ($return_result) break;//break this for

        }//foreach

        // Check the first and last tag -> if first and last caracters are " " space remove them!!!"

        if ((count($aLine) > 0) && ($aExtraInfo['LAST_LINE_BR'] == "AUTO")){
            //first tag
            $temp = & $aLine[0];
            if ( (strlen($temp['text']) > 0) && ($temp['text'][0] == " ")){
                $temp['text'] = substr($temp['text'], 1, strlen($temp['text']));
                $temp['width'] -= $fw[ $temp['tag'] ]['w'][" "] * $fw[ $temp['tag'] ]['s'];
                $temp['spaces'] --;
            }

            //last tag
            $temp = & $aLine[count($aLine) - 1];
            if ( (strlen($temp['text'])>0) && ($temp['text'][strlen($temp['text'])-1] == " ")){
                $temp['text'] = substr($temp['text'], 0, strlen($temp['text']) - 1);
                $temp['width'] -= $fw[ $temp['tag'] ]['w'][" "] * $fw[ $temp['tag'] ]['s'];
                $temp['spaces'] --;
            }
        }

        if ($reset_spaces){//this is used in case of a "Explicit Line Break"
            //put all spaces to 0 so in case of "J" align there is no space extension
            for ($k=0; $k< count($aLine); $k++) $aLine[$k]['spaces'] = 0;
        }//fi

        return $aLine;
    }//function MakeLine

    /**
    Draws a MultiCell with TAG recognition parameters
    @param		$w - with of the cell
    $h - height of the cell
    $pData - string or data to be printed
    $border - border
    $align	- align
    $fill - fill
    $pDataIsString - true if $pData is a string
    - false if $pData is an array containing lines formatted with $this->MakeLine($w) function
    (the false option is used in relation with StringToLines, to avoid double formatting of a string

    These paramaters are the same and have the same behavior as at Multicell function
    @return     nothing
     */
    //function MultiCellTag($w, $h, $pData, $border=0, $align='J', $fill=0, $pDataIsString = true){
    function MultiCellTag($w, $h, $pData, $border=0, $align='J', $fill=0, $pDataIsString = true){

        //save the current style settings, this will be the default in case of no style is specified
        $this->SaveCurrentStyle();
        $this->_wt_Reset_Datas();

        //if data is string
        if ($pDataIsString === true) $this->DivideByTags($pData);

        $b = $b1 = $b2 = $b3 = '';//borders

        //save the current X position, we will have to jump back!!!!
        $startX = $this -> GetX();

        if($border)
        {
            if($border==1)
            {
                $border = 'LTRB';
                $b1 = 'LRT';//without the bottom
                $b2 = 'LR';//without the top and bottom
                $b3 = 'LRB';//without the top
            }
            else
            {
                $b2='';
                if(is_int(strpos($border,'L')))
                    $b2.='L';
                if(is_int(strpos($border,'R')))
                    $b2.='R';
                $b1=is_int(strpos($border,'T')) ? $b2 . 'T' : $b2;
                $b3=is_int(strpos($border,'B')) ? $b2 . 'B' : $b2;
            }

            //used if there is only one line
            $b = '';
            $b .= is_int(strpos($border,'L')) ? 'L' : "";
            $b .= is_int(strpos($border,'R')) ? 'R' : "";
            $b .= is_int(strpos($border,'T')) ? 'T' : "";
            $b .= is_int(strpos($border,'B')) ? 'B' : "";
        }

        $first_line = true;
        $last_line = false;

        if ($pDataIsString === true){
            $last_line = !(count($this->wt_DataInfo) > 0);
        }else {
            $last_line = !(count($pData) > 0);
        }

        while(!$last_line){
            if ($fill == 1){
                //fill in the cell at this point and write after the text without filling
                $this->Cell($w,$h,"",0,0,"",1);
                $this->SetX($startX);//restore the X position
            }

            if ($pDataIsString === true){
                //make a line
                $str_data = $this->MakeLine($w);
                //check for last line
                $last_line = !(count($this->wt_DataInfo) > 0);
            }else {
                //make a line
                $str_data = array_shift($pData);
                //check for last line
                $last_line = !(count($pData) > 0);
            }

            if ($last_line && ($align == "J")){//do not Justify the Last Line
                $align = "L";
            }

            //outputs a line
            $this->PrintLine($w, $h, $str_data, $align);


            //see what border we draw:
            if($first_line && $last_line){
                //we have only 1 line
                $real_brd = $b;
            }elseif($first_line){
                $real_brd = $b1;
            }elseif($last_line){
                $real_brd = $b3;
            }else{
                $real_brd = $b2;
            }

            if ($first_line) $first_line = false;

            //draw the border and jump to the next line
            $this->SetX($startX);//restore the X
            $this->Cell($w,$h,"",$real_brd,2);
        }//while(! $last_line){

        //APPLY THE DEFAULT STYLE
        $this->ApplyStyle("DEFAULT");

        $this->x=$this->lMargin;
    }//function MultiCellExt


    /**
    This method divides the string into the tags and puts the result into wt_DataInfo variable.
    @param		$pStr - string to be printed
    @return     nothing
     */

    function DivideByTags($pStr, $return = false){

        $pStr = str_replace("\t", "<ttags>\t</ttags>", $pStr);
        $pStr = str_replace(PARAGRAPH_STRING, "<pparg>\t</pparg>", $pStr);
        $pStr = str_replace("\r", "", $pStr);

        //initialize the String_TAGS class
        $sWork = new StringTags(5);

        //get the string divisions by tags
        $this->wt_DataInfo = $sWork->get_tags($pStr);

        if ($return) return $this->wt_DataInfo;
    }//function DivideByTags($pStr){

    /**
    This method parses the current text and return an array that contains the text information for
    each line that will be drawed.
    @param		$w - with of the cell
    $pStr - String to be parsed
    @return     $aStrLines - array - contains parsed text information.
     */
    //function StringToLines($w = 0, $pStr){
    // 20220219
    // <b>Deprecated</b>:  Required parameter $pStr follows optional parameter $w in <b>.../fpdf/class.multicelltag.php</b> on line <b>534</b><br />
    function StringToLines($pStr, $w = 0){

        //save the current style settings, this will be the default in case of no style is specified
        $this->SaveCurrentStyle();
        $this->_wt_Reset_Datas();

        $this->DivideByTags($pStr);

        $last_line = !(count($this->wt_DataInfo) > 0);

        $aStrLines = array();

        while (!$last_line){

            //make a line
            $str_data = $this->MakeLine($w);
            array_push($aStrLines, $str_data);

            //check for last line
            $last_line = !(count($this->wt_DataInfo) > 0);
        }//while(! $last_line){

        //APPLY THE DEFAULT STYLE
        $this->ApplyStyle("DEFAULT");

        return $aStrLines;
    }//function StringToLines


    /**
    Draws a line returned from MakeLine function
    @param		$w - with of the cell
    $h - height of the cell
    $aTxt - array from MakeLine
    $align - text align
    @return     nothing
     */
    function PrintLine($w, $h, $aTxt, $align='J'){

        if($w==0)
            $w=$this->w-$this->rMargin - $this->x;

        $wmax = $w; //Maximum width

        $total_width = 0;	//the total width of all strings
        $total_spaces = 0;	//the total number of spaces

        $nr = count($aTxt);//number of elements

        for ($i=0; $i<$nr; $i++){
            $total_width += ($aTxt[$i]['width']/1000);
            $total_spaces += $aTxt[$i]['spaces'];
        }

        //default
        $w_first = $this->cMargin;

        switch($align){
            case 'J':
                if ($total_spaces > 0)
                    $extra_space = ($wmax - 2 * $this->cMargin - $total_width) / $total_spaces;
                else $extra_space = 0;
                break;
            case 'L':
                break;
            case 'C':
                $w_first = ($wmax - $total_width) / 2;
                break;
            case 'R':
                $w_first = $wmax - $total_width - $this->cMargin;;
                break;
        }

        // Output the first Cell
        if ($w_first != 0){
            $this->Cell($w_first, $h, "", 0, 0, "L", 0);
        }

        $last_width = $wmax - $w_first;

        while (list($key, $val) = each($aTxt)) {

            $bYPosUsed = false;

            //apply current tag style
            $this->ApplyStyle($val['tag']);

            //If > 0 then we will move the current X Position
            $extra_X = 0;

            if ($val['ypos'] != 0){
                $lastY = $this->y;
                $this->y = $lastY - $val['ypos'];
                $bYPosUsed = true;
            }

            //string width
            $width = $this->GetStringWidth($val['text']);
            $width = $val['width'] / 1000;

            if ($width == 0) continue;// No width jump over!!!

            if($align=='J'){
                if ($val['spaces'] < 1) $temp_X = 0;
                else $temp_X = $extra_space;

                $this->ws = $temp_X;

                $this->_out(sprintf('%.3f Tw', $temp_X * $this->k));

                $extra_X = $extra_space * $val['spaces'];//increase the extra_X Space

            }else{
                $this->ws = 0;
                $this->_out('0 Tw');
            }//fi

            //Output the Text/Links
            $this->Cell($width, $h, $val['text'], 0, 0, "C", 0, $val['href']);

            $last_width -= $width;//last column width

            if ($extra_X != 0){
                $this -> SetX($this->GetX() + $extra_X);
                $last_width -= $extra_X;
            }//fi

            if ($bYPosUsed) $this->y = $lastY;

        }//while

        // Output the Last Cell
        if ($last_width != 0){
            $this->Cell($last_width, $h, "", 0, 0, "", 0);
        }//fi
    }//function PrintLine(



}

?>
