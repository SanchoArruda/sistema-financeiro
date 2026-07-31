<?php
/**
 * FPDF Version 1.86 — Free PDF generator
 * Official website: http://www.fpdf.org
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

define('FPDF_VERSION', '1.86');

class FPDF {
    protected $page;               // current page number
    protected $n;                  // current object number
    protected $offsets;            // array of object offsets
    protected $buffer;             // buffer holding in-memory PDF
    protected $pages;              // array containing pages
    protected $state;              // current document state
    protected $compress;           // compression flag
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
    protected $fontpath;           // path containing fonts
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
    protected $PageBreakTrigger;   // threshold to trigger page break
    protected $InHeader;           // flag set when processing header
    protected $InFooter;           // flag set when processing footer
    protected $AliasNbPages;       // alias for total number of pages
    protected $ZoomMode;           // zoom display mode
    protected $LayoutMode;         // layout display mode
    protected $metadata;           // document properties
    protected $pdf_version;        // PDF version number
    protected $resObjId;           // Resource object ID

    public function __construct($orientation='P', $unit='mm', $size='A4') {
        // Some initialization
        $this->state = 0;
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = [];
        $this->PageInfo = [];
        $this->fonts = [];
        $this->FontFiles = [];
        $this->encodings = [];
        $this->cmaps = [];
        $this->images = [];
        $this->links = [];
        $this->offsets = [];
        $this->InHeader = false;
        $this->InFooter = false;
        $this->AliasNbPages = '';
        $this->DefOrientation = $orientation;
        $this->fontpath = '';
        $this->CoreFonts = ['courier', 'helvetica', 'times', 'symbol', 'zapfdingbats'];

        // Scale factor
        if ($unit === 'pt') $this->k = 1;
        elseif ($unit === 'mm') $this->k = 72/25.4;
        elseif ($unit === 'cm') $this->k = 72/2.54;
        elseif ($unit === 'in') $this->k = 72;
        else $this->Error('Incorrect unit: '.$unit);

        // Page sizes
        $this->StdPageSizes = [
            'a3' => [841.89, 1190.55],
            'a4' => [595.28, 841.89],
            'a5' => [420.94, 595.28],
            'letter' => [612, 792],
            'legal' => [612, 1008]
        ];
        $size = $this->_getpagesize($size);
        $this->DefPageSize = $size;
        $this->CurPageSize = $size;
        $this->CurRotation = 0;

        // Page orientation
        $orientation = strtolower($orientation);
        if ($orientation === 'p' || $orientation === 'portrait') {
            $this->DefOrientation = 'P';
            $this->w = $size[0];
            $this->h = $size[1];
        } elseif ($orientation === 'l' || $orientation === 'landscape') {
            $this->DefOrientation = 'L';
            $this->w = $size[1];
            $this->h = $size[0];
        } else {
            $this->Error('Incorrect orientation: '.$orientation);
        }
        $this->CurOrientation = $this->DefOrientation;
        $this->wPt = $this->w * $this->k;
        $this->hPt = $this->h * $this->k;

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
        $this->compress = function_exists('gzcompress');

        // Set default PDF version
        $this->pdf_version = '1.3';
    }

    public function SetMargins($left, $top, $right=null) {
        $this->lMargin = $left;
        $this->tMargin = $top;
        if ($right === null) $right = $left;
        $this->rMargin = $right;
    }

    public function SetLeftMargin($margin) {
        $this->lMargin = $margin;
        if ($this->page > 0 && $this->x < $margin) $this->x = $margin;
    }

    public function SetTopMargin($margin) {
        $this->tMargin = $margin;
    }

    public function SetRightMargin($margin) {
        $this->rMargin = $margin;
    }

    public function SetAutoPageBreak($auto, $margin=0) {
        $this->AutoPageBreak = $auto;
        $this->bMargin = $margin;
        $this->PageBreakTrigger = $this->h - $margin;
    }

    public function SetDisplayMode($zoom, $layout='default') {
        if ($zoom === 'fullpage' || $zoom === 'fullwidth' || $zoom === 'real' || $zoom === 'default' || !is_string($zoom)) {
            $this->ZoomMode = $zoom;
        } else {
            $this->Error('Incorrect zoom display mode: '.$zoom);
        }

        if ($layout === 'single' || $layout === 'continuous' || $layout === 'two' || $layout === 'default') {
            $this->LayoutMode = $layout;
        } else {
            $this->Error('Incorrect layout display mode: '.$layout);
        }
    }

    public function SetCompression($compress) {
        if (function_exists('gzcompress')) $this->compress = $compress;
        else $this->compress = false;
    }

    public function SetTitle($title, $isUTF8=false) {
        $this->metadata['Title'] = $isUTF8 ? $title : $this->_UTF8toUTF16($title);
    }

    public function SetAuthor($author, $isUTF8=false) {
        $this->metadata['Author'] = $isUTF8 ? $author : $this->_UTF8toUTF16($author);
    }

    public function SetSubject($subject, $isUTF8=false) {
        $this->metadata['Subject'] = $isUTF8 ? $subject : $this->_UTF8toUTF16($subject);
    }

    public function SetKeywords($keywords, $isUTF8=false) {
        $this->metadata['Keywords'] = $isUTF8 ? $keywords : $this->_UTF8toUTF16($keywords);
    }

    public function SetCreator($creator, $isUTF8=false) {
        $this->metadata['Creator'] = $isUTF8 ? $creator : $this->_UTF8toUTF16($creator);
    }

    public function AliasNbPages($alias='{nb}') {
        $this->AliasNbPages = $alias;
    }

    public function Error($msg) {
        throw new Exception('FPDF error: '.$msg);
    }

    public function Close() {
        if ($this->state === 3) return;
        if ($this->page === 0) $this->AddPage();

        // Page footer
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;

        // Close page
        $this->_endpage();

        // Close document
        $this->_enddoc();
    }

    public function AddPage($orientation='', $size='', $rotation=0) {
        if ($this->state === 0) $this->_begindoc();

        $family = $this->FontFamily;
        $style = $this->FontStyle.($this->underline ? 'U' : '');
        $fontsize = $this->FontSizePt;
        $lw = $this->LineWidth;
        $dc = $this->DrawColor;
        $fc = $this->FillColor;
        $tc = $this->TextColor;
        $cf = $this->ColorFlag;

        if ($this->page > 0) {
            $this->InFooter = true;
            $this->Footer();
            $this->InFooter = false;
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
        if ($family) $this->SetFont($family, $style, $fontsize);

        // Set colors
        $this->DrawColor = $dc;
        if ($dc !== '0 G') $this->_out($dc);
        $this->FillColor = $fc;
        if ($fc !== '0 g') $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;

        // Page header
        $this->InHeader = true;
        $this->Header();
        $this->InHeader = false;

        // Restore line width
        if ($this->LineWidth !== $lw) {
            $this->LineWidth = $lw;
            $this->_out(sprintf('%.2F w', $lw * $this->k));
        }

        // Restore font
        if ($family) $this->SetFont($family, $style, $fontsize);

        // Restore colors
        if ($this->DrawColor !== $dc) {
            $this->DrawColor = $dc;
            $this->_out($dc);
        }
        if ($this->FillColor !== $fc) {
            $this->FillColor = $fc;
            $this->_out($fc);
        }
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
    }

    public function Header() {
        // To be implemented in child class
    }

    public function Footer() {
        // To be implemented in child class
    }

    public function PageNo() {
        return $this->page;
    }

    public function SetDrawColor($r, $g=null, $b=null) {
        if (($r == 0 && $g == 0 && $b == 0) || $g === null) {
            $this->DrawColor = sprintf('%.3F G', $r / 255);
        } else {
            $this->DrawColor = sprintf('%.3F %.3F %.3F RG', $r / 255, $g / 255, $b / 255);
        }
        if ($this->page > 0) $this->_out($this->DrawColor);
    }

    public function SetFillColor($r, $g=null, $b=null) {
        if (($r == 0 && $g == 0 && $b == 0) || $g === null) {
            $this->FillColor = sprintf('%.3F g', $r / 255);
        } else {
            $this->FillColor = sprintf('%.3F %.3F %.3F rg', $r / 255, $g / 255, $b / 255);
        }
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
        if ($this->page > 0) $this->_out($this->FillColor);
    }

    public function SetTextColor($r, $g=null, $b=null) {
        if (($r == 0 && $g == 0 && $b == 0) || $g === null) {
            $this->TextColor = sprintf('%.3F g', $r / 255);
        } else {
            $this->TextColor = sprintf('%.3F %.3F %.3F rg', $r / 255, $g / 255, $b / 255);
        }
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
    }

    public function GetStringWidth($s) {
        $s = (string)$s;
        $cw = &$this->CurrentFont['cw'];
        $w = 0;
        $l = strlen($s);
        for ($i = 0; $i < $l; $i++) {
            $w += $cw[$s[$i]] ?? 500;
        }
        return $w * $this->FontSize / 1000;
    }

    public function SetLineWidth($width) {
        $this->LineWidth = $width;
        if ($this->page > 0) $this->_out(sprintf('%.2F w', $width * $this->k));
    }

    public function Line($x1, $y1, $x2, $y2) {
        $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k));
    }

    public function Rect($x, $y, $w, $h, $style='') {
        if ($style === 'F') $op = 'f';
        elseif ($style === 'FD' || $style === 'DF') $op = 'B';
        else $op = 'S';
        $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x * $this->k, ($this->h - $y) * $this->k, $w * $this->k, -$h * $this->k, $op));
    }

    public function SetFont($family, $style='', $size=0) {
        if ($family === '') $family = $this->FontFamily;
        else $family = strtolower($family);
        
        if ($family === 'arial') $family = 'helvetica';
        $style = strtoupper($style);
        
        if (strpos($style, 'U') !== false) {
            $this->underline = true;
            $style = str_replace('U', '', $style);
        } else {
            $this->underline = false;
        }
        if ($style === 'IB') $style = 'BI';
        if ($size == 0) $size = $this->FontSizePt;

        // Test if font is already loaded
        $fontkey = $family.$style;
        if (!isset($this->fonts[$fontkey])) {
            if (in_array($family, $this->CoreFonts, true)) {
                if ($family === 'symbol' || $family === 'zapfdingbats') $style = '';
                $fontkey = $family.$style;
                if (!isset($this->fonts[$fontkey])) {
                    $this->_loadfont($family, $style);
                }
            } else {
                $this->Error('Undefined font: '.$family.' '.$style);
            }
        }

        // Select it
        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSizePt = $size;
        $this->FontSize = $size / $this->k;
        $this->CurrentFont = &$this->fonts[$fontkey];
        if ($this->page > 0) $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
    }

    public function SetFontSize($size) {
        $this->SetFont($this->FontFamily, $this->FontStyle, $size);
    }

    public function AddLink() {
        $n = count($this->links) + 1;
        $this->links[$n] = [0, 0];
        return $n;
    }

    public function SetLink($link, $y=0, $page=-1) {
        if ($y == -1) $y = $this->y;
        if ($page == -1) $page = $this->page;
        $this->links[$link] = [$page, $y];
    }

    public function Link($x, $y, $w, $h, $link) {
        $this->PageLinks[$this->page][] = [$x * $this->k, $this->hPt - $y * $this->k, $w * $this->k, $h * $this->k, $link];
    }

    public function Text($x, $y, $txt) {
        if (!isset($this->CurrentFont)) $this->Error('No font has been set');
        $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        if ($this->underline && $txt !== '') $s .= ' '.$this->_dounderline($x, $y, $txt);
        if ($this->ColorFlag) $s = 'q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
    }

    public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        if (!isset($this->CurrentFont)) $this->Error('No font has been set');
        $k = $this->k;
        if ($this->y + $h > $this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AutoPageBreak) {
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
        if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
        $s = '';
        if ($fill || $border == 1) {
            if ($fill) $op = ($border == 1) ? 'B' : 'f';
            else $op = 'S';
            $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
        }
        if (is_string($border)) {
            $x = $this->x;
            $y = $this->y;
            if (strpos($border, 'L') !== false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y + $h)) * $k);
            if (strpos($border, 'T') !== false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);
            if (strpos($border, 'R') !== false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
            if (strpos($border, 'B') !== false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
        }
        if ($txt !== '') {
            if ($align === 'R') $dx = $w - $this->cMargin - $this->GetStringWidth($txt);
            elseif ($align === 'C') $dx = ($w - $this->GetStringWidth($txt)) / 2;
            else $dx = $this->cMargin;

            if ($this->ColorFlag) $s .= 'q '.$this->TextColor.' ';
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x + $dx) * $k, ($this->h - ($this->y + .5 * $h + .3 * $this->FontSize)) * $k, $this->_escape($txt));
            if ($this->underline) $s .= ' '.$this->_dounderline($this->x + $dx, $this->y + .5 * $h + .3 * $this->FontSize, $txt);
            if ($this->ColorFlag) $s .= ' Q';
            if ($link) $this->Link($this->x + $dx, $this->y + .5 * $h - .5 * $this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
        }
        if ($s) $this->_out($s);
        $this->lasth = $h;
        if ($ln > 0) {
            $this->y += $h;
            if ($ln == 1) $this->x = $this->lMargin;
        } else {
            $this->x += $w;
        }
    }

    public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        if (!isset($this->CurrentFont)) $this->Error('No font has been set');
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] === "\n") $nb--;

        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            } else {
                $b2 = '';
                if (strpos($border, 'L') !== false) $b2 .= 'L';
                if (strpos($border, 'R') !== false) $b2 .= 'R';
                $b = (strpos($border, 'T') !== false) ? $b2.'T' : $b2;
            }
        }

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;

        while ($i < $nb) {
            $c = $s[$i];
            if ($c === "\n") {
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
                if ($border && $nl == 2) $b = $b2;
                continue;
            }
            if ($c === ' ') {
                $sep = $i;
                $ns++;
            }
            $l += $cw[$c] ?? 500;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) $i++;
                    if ($this->ws > 0) {
                        $this->ws = 0;
                        $this->_out('0 Tw');
                    }
                    $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                } else {
                    if ($align === 'J') {
                        $this->ws = ($ns > 1) ? ($wmax - $l + ($cw[' '] ?? 500)) / ($ns - 1) * $this->FontSize / 1000 : 0;
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
                if ($border && $nl == 2) $b = $b2;
            } else {
                $i++;
            }
        }

        if ($this->ws > 0) {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        if ($border && strpos($border, 'B') !== false) $b .= 'B';
        $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
        $this->x = $this->lMargin;
    }

    public function Ln($h=null) {
        $this->x = $this->lMargin;
        if ($h === null) $this->y += $this->lasth;
        else $this->y += $h;
    }

    public function GetX() { return $this->x; }
    public function SetX($x) {
        if ($x >= 0) $this->x = $x;
        else $this->x = $this->w + $x;
    }

    public function GetY() { return $this->y; }
    public function SetY($y, $resetX=true) {
        if ($y >= 0) $this->y = $y;
        else $this->y = $this->h + $y;
        if ($resetX) $this->x = $this->lMargin;
    }

    public function SetXY($x, $y) {
        $this->SetY($y, false);
        $this->SetX($x);
    }

    public function Output($dest='', $name='', $isUTF8=false) {
        $this->Close();

        if ($dest === '') {
            if ($name === '') {
                $name = 'doc.pdf';
                $dest = 'I';
            } else {
                $dest = 'F';
            }
        }
        $dest = strtoupper($dest);

        switch ($dest) {
            case 'I':
                $this->_checkoutput();
                if (PHP_SAPI !== 'cli') {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="'.$name.'"');
                    header('Cache-Control: private, max-age=0, must-revalidate');
                    header('Pragma: public');
                }
                echo $this->buffer;
                break;
            case 'D':
                $this->_checkoutput();
                header('Content-Type: application/x-download');
                header('Content-Disposition: attachment; filename="'.$name.'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $this->buffer;
                break;
            case 'F':
                $f = fopen($name, 'wb');
                if (!$f) $this->Error('Unable to create output file: '.$name);
                fwrite($f, $this->buffer, strlen($this->buffer));
                fclose($f);
                break;
            case 'S':
                return $this->buffer;
            default:
                $this->Error('Incorrect output destination: '.$dest);
        }
        return '';
    }

    // Protected / Private internal methods
    protected function _getpagesize($size) {
        if (is_string($size)) {
            $a = strtolower($size);
            if (!isset($this->StdPageSizes[$a])) $this->Error('Unknown page size: '.$size);
            return $this->StdPageSizes[$a];
        } else {
            if ($size[0] > $size[1]) return [$size[1], $size[0]];
            else return $size;
        }
    }

    protected function _beginpage($orientation, $size, $rotation) {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->PageInfo[$this->page] = [];

        if ($orientation === '') $orientation = $this->DefOrientation;
        else $orientation = strtoupper($orientation[0]);
        if ($size === '') $size = $this->DefPageSize;
        else $size = $this->_getpagesize($size);

        if ($orientation !== $this->CurOrientation || $size[0] !== $this->CurPageSize[0] || $size[1] !== $this->CurPageSize[1]) {
            if ($orientation === 'P') {
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
        $this->PageInfo[$this->page]['size'] = [$this->wPt, $this->hPt];
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
        $this->FontFamily = '';
    }

    protected function _endpage() {
        $this->state = 1;
    }

    protected function _loadfont($family, $style) {
        // Built-in core fonts metrics (Helvetica, Times, Courier)
        $cw = [];
        for ($i = 0; $i <= 255; $i++) {
            $cw[chr($i)] = 600; // default average width
        }
        // Specific adjustments for Latin characters for Helvetica
        if ($family === 'helvetica' || $family === 'courier' || $family === 'times') {
            $cw['i'] = 222; $cw['l'] = 222; $cw['f'] = 278; $cw['t'] = 278; $cw['r'] = 333;
            $cw['I'] = 278; $cw['j'] = 222; $cw['m'] = 833; $cw['w'] = 722; $cw['M'] = 833;
            $cw['W'] = 944; $cw[' '] = 278; $cw['.'] = 278; $cw[','] = 278; $cw[':'] = 278;
            $cw[';'] = 278; $cw['-'] = 333; $cw['/'] = 278; $cw['('] = 333; $cw[')'] = 333;
            for ($d = 48; $d <= 57; $d++) $cw[chr($d)] = 556; // numbers 0-9
        }

        $fontkey = $family.$style;
        $this->fonts[$fontkey] = [
            'i' => count($this->fonts) + 1,
            'type' => 'core',
            'name' => $this->_getcorefontname($family, $style),
            'up' => -100,
            'ut' => 50,
            'cw' => $cw
        ];
    }

    protected function _getcorefontname($family, $style) {
        if ($family === 'helvetica') return ($style === 'B') ? 'Helvetica-Bold' : (($style === 'I') ? 'Helvetica-Oblique' : (($style === 'BI') ? 'Helvetica-BoldOblique' : 'Helvetica'));
        if ($family === 'times') return ($style === 'B') ? 'Times-Bold' : (($style === 'I') ? 'Times-Italic' : (($style === 'BI') ? 'Times-BoldItalic' : 'Times-Roman'));
        if ($family === 'courier') return ($style === 'B') ? 'Courier-Bold' : (($style === 'I') ? 'Courier-Oblique' : (($style === 'BI') ? 'Courier-BoldOblique' : 'Courier'));
        return 'Helvetica';
    }

    protected function _escape($s) {
        $s = str_replace('\\', '\\\\', (string)$s);
        $s = str_replace('(', '\\(', $s);
        $s = str_replace(')', '\\)', $s);
        return str_replace("\r", '\\r', $s);
    }

    protected function _dounderline($x, $y, $txt) {
        $up = $this->CurrentFont['up'] ?? -100;
        $ut = $this->CurrentFont['ut'] ?? 50;
        $w = $this->GetStringWidth($txt) + $this->ws * substr_count($txt, ' ');
        return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, ($this->h - ($y - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
    }

    protected function _begindoc() {
        $this->state = 1;
        $this->_out('%PDF-'.$this->pdf_version);
    }

    protected function _enddoc() {
        $this->_putheader();
        $this->_putresources();
        $this->_putpages();
        $this->_putinfo();
        $this->_putcatalog();
        // Cross-reference table
        $offset = strlen($this->buffer);
        $this->_out('xref');
        $this->_out('0 '.($this->n + 1));
        $this->_out('0000000000 65535 f ');
        for ($i = 1; $i <= $this->n; $i++) {
            $this->_out(sprintf('%010d 00000 n ', $this->offsets[$i] ?? 0));
        }
        // Trailer
        $this->_out('trailer');
        $this->_out('<<');
        $this->_out('/Size '.($this->n + 1));
        $this->_out('/Root 1 0 R');
        $this->_out('/Info '.$this->n.' 0 R');
        $this->_out('>>');
        $this->_out('startxref');
        $this->_out($offset);
        $this->_out('%%EOF');
        $this->state = 3;
    }

    protected function _out($s) {
        if ($this->state === 2) {
            $this->pages[$this->page] .= $s."\n";
        } else {
            $this->buffer .= $s."\n";
        }
    }

    protected function _putheader() {
        // Already put in _begindoc
    }

    protected function _putpages() {
        $nb = $this->page;
        $resObjId = $this->resObjId ?? 3;
        for ($n = 1; $n <= $nb; $n++) {
            if ($this->AliasNbPages !== '') {
                $this->pages[$n] = str_replace($this->AliasNbPages, (string)$nb, $this->pages[$n]);
            }
        }
        for ($n = 1; $n <= $nb; $n++) {
            $this->_newobj();
            $this->PageInfo[$n]['n'] = $this->n;
            $this->_out('<</Type /Page');
            $this->_out('/Parent 2 0 R');
            if (isset($this->PageInfo[$n]['size'])) {
                $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->PageInfo[$n]['size'][0], $this->PageInfo[$n]['size'][1]));
            }
            $this->_out('/Resources '.$resObjId.' 0 R');
            $this->_out('/Contents '.($this->n + 1).' 0 R>>');
            $this->_out('endobj');

            // Page content stream
            $p = $this->compress ? gzcompress($this->pages[$n]) : $this->pages[$n];
            $this->_newobj();
            $this->_out('<</Length '.strlen($p).($this->compress ? ' /Filter /FlateDecode' : '').'>>');
            $this->_putstream($p);
            $this->_out('endobj');
        }
        // Pages root
        $this->offsets[2] = strlen($this->buffer);
        $this->_out('2 0 obj');
        $this->_out('<</Type /Pages');
        $kids = '/Kids [';
        for ($n = 1; $n <= $nb; $n++) {
            $kids .= $this->PageInfo[$n]['n'].' 0 R ';
        }
        $this->_out($kids.']');
        $this->_out('/Count '.$nb);
        $this->_out('>>');
        $this->_out('endobj');
    }

    protected function _putfonts() {
        foreach ($this->fonts as $k => $font) {
            $this->_newobj();
            $this->fonts[$k]['n'] = $this->n;
            $this->_out('<</Type /Font');
            $this->_out('/Subtype /Type1');
            $this->_out('/BaseFont /'.$font['name']);
            $this->_out('/Encoding /WinAnsiEncoding');
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    protected function _putresources() {
        $this->_putfonts();
        $this->_newobj();
        $this->resObjId = $this->n;
        $this->_out($this->n.' 0 obj');
        $this->_out('<<');
        $this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->_out('/Font <<');
        foreach ($this->fonts as $font) {
            $this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
        }
        $this->_out('>>');
        $this->_out('>>');
        $this->_out('endobj');
    }

    protected function _putinfo() {
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/Producer (Finzy FPDF Generator 1.86)');
        $this->_out('/CreationDate (D:'.date('YmdHis').')');
        if (!empty($this->metadata['Title'])) $this->_out('/Title ('.$this->_escape($this->metadata['Title']).')');
        if (!empty($this->metadata['Author'])) $this->_out('/Author ('.$this->_escape($this->metadata['Author']).')');
        if (!empty($this->metadata['Subject'])) $this->_out('/Subject ('.$this->_escape($this->metadata['Subject']).')');
        $this->_out('>>');
        $this->_out('endobj');
    }

    protected function _putcatalog() {
        $this->offsets[1] = strlen($this->buffer);
        $this->_out('1 0 obj');
        $this->_out('<</Type /Catalog /Pages 2 0 R>>');
        $this->_out('endobj');
    }

    protected function _newobj() {
        $this->n++;
        $this->offsets[$this->n] = strlen($this->buffer);
        $this->_out($this->n.' 0 obj');
    }

    protected function _putstream($s) {
        $this->_out('stream');
        $this->_out($s);
        $this->_out('endstream');
    }

    protected function _checkoutput() {
        if (PHP_SAPI !== 'cli') {
            if (headers_sent($file, $line)) {
                $this->Error('Some data has already been output, can\'t send PDF file (output started at '.$file.':'.$line.')');
            }
        }
    }

    protected function _UTF8toUTF16($s) {
        $res = "\xFE\xFF";
        $nb = strlen($s);
        $i = 0;
        while ($i < $nb) {
            $c1 = ord($s[$i++]);
            if ($c1 < 128) {
                $res .= "\x00".chr($c1);
            } elseif ($c1 < 224) {
                $c2 = ord($s[$i++]);
                $res .= chr(($c1 & 31) >> 2).chr((($c1 & 3) << 6) | ($c2 & 63));
            } else {
                $c2 = ord($s[$i++]);
                $c3 = ord($s[$i++]);
                $res .= chr((($c1 & 15) << 4) | (($c2 & 60) >> 2)).chr((($c2 & 3) << 6) | ($c3 & 63));
            }
        }
        return $res;
    }
}
