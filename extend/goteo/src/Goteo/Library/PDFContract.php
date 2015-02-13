<?php

namespace Goteo\Library;

require_once(__DIR__ . '/fpdf/html2pdf.php');

class PDFContract extends \PDF_HTML {

    const DYNAMIC_TEXT_SOURCE = "/Contract/contenido_dinamico.xml";
    const STATIC_TEXT_SOURCE = "/Contract/contenido_estatico.xml";
    const DRAFT_IMAGE = "/Contract/borrador.png";

    /* Document margins */
    const MARGIN_LEFT = 20;
    const MARGIN_TOP = 10;
    const MARGIN_RIGHT = 30;
    const X_START = 20;
    const X_STATIC_TEXT = 23;
    const HEIGHT = 8;
    const HEADER_FONT = "Arial";
    const FONT = "Arial";
    const FONT_HEADER_SIZE = 11;
    const FONT_SIZE = 10;
    const FONT_TITLE_SIZE = 12;
    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const DRAFT_X = 8;
    const DRAFT_Y = 15;
    const MAX_WIDTH = 750;
    const MAX_HEIGHT = 1500;

    /* Constants related with project identification */
    const TEXT_IDENTIFYING_DATA = "DATOS IDENTIFICATIVOS PROYECTO";
    const TEXT_PROJECT_NAME = "Nombre del proyecto/iniciativa: ";
    const TEXT_LINK_GOTEO = "Link de la página en Goteo: ";
    const TEXT_IMPULSOR = "Impulsor: ";
    const TEXT_USER = "Usuario desde el que se creó el proyecto: ";
    const TEXT_USER_PAGE = "Página del usuario: ";
    /* Constants related with project description */
    const TEXT_DESCRIPTION_AND_TARGET = "DESCRIPCIÓN Y OBJETIVO";
    /* Constants related with payment section */
    const TEXT_PAYMENT_HEADER = "CUENTA BANCARIA ORDINARIA Y DIRECCIÓN PAYPAL QUE SE DESIGNAN POR EL IMPULSOR A LOS EFECTOS DE LOS PAGOS REGULADOS EN LA CONDICIÓN SÉPTIMA";
    const TEXT_PAYMENT_ACCOUNT_NUMBER = "Número Cuenta Bancaria: ";
    const TEXT_PAYMENT_ACCOUNT_OWNER = "Titular: ";
    const TEXT_PAYMENT_PAYPAL_ACCOUNT = "Cuenta PayPal: ";
    const TEXT_PAYMENT_PAYPAL_OWNER = "Titular: ";
    /* Dynamic part */
    const TEXT_TYPE_0 = " en su propio nombre y derecho.";
    const TEXT_TYPE_1 = " en su calidad de %office%, en nombre y representación de la entidad %entity_name%, titular del CIF %entity_cif%, con domicilio social en %entity_location% (%entity_region%, CP %entity_zipcode%, %entity_country%), %entity_address%; entidad inscrita en el %reg_name%, %reg_number%.";
    const TEXT_TYPE_2 = " en su calidad de apoderado y %office%, en nombre y representación de la entidad %entity_name%, titular del CIF %entity_cif%, con domicilio social en %entity_location% (%entity_region%, CP %entity_zipcode%, %entity_country%), %entity_address%; entidad constituida en escritura pública otorgada en fecha %reg_date%. ante el Notario de %reg_idloc%. Don %reg_idname% (nº %reg_id% de su protocolo) e inscrita con el número %reg_number% en el %reg_name%.";
    /* Other texts */
    const TEXT_GENERAL_CONDITIONS = "DOCUMENTO DE CONDICIONES GENERALES USO";
    const TEXT_PLATFORM = "PLATAFORMA GOTEO";
    const TEXT_PLACE = "Palma de Mallorca, ";
    const TEXT_HEADER_NUM = "Núm. contrato  ";
    const TEXT_FOOTER_IMPULSOR = "El impulsor";
    const TEXT_FOOTER_FOUNDATION = "Fundación Fuentes Abiertas";

    private $data = array();

    /**
     * Sets the parameters to use to print the contract.
     * @param StdClass $data
     */
    public function setParameters($data) {
        $this->data = $data;
    }

    /* formatting methods */
    public function regular($size = self::FONT_SIZE) {
        $this->SetFont($this->FontFamily,'',$size);
    }

    public function bold($size = self::FONT_SIZE) {
        $this->SetFont($this->FontFamily,'B',$size);
    }

    public static function txt($txt) {
        return iconv('UTF-8', 'ISO-8859-15', $txt);
    }



    private function projectIdentification() {
        $this->setX(self::X_STATIC_TEXT);
        $this->bold(self::FONT_TITLE_SIZE);
        $this->MultiCell(0, 5, self::txt(self::TEXT_IDENTIFYING_DATA), "LTR", 1);
        $this->regular();
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_PROJECT_NAME . $this->data->project_name);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LR", 1);
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_LINK_GOTEO . $this->data->project_url);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LR", 1);
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_IMPULSOR . $this->data->project_user);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LR", 1);
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_USER . $this->data->project_owner);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LR", 1);
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_USER_PAGE . $this->data->project_profile);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LBR", 1);
    }

    private function projectDescription() {
        $this->setX(self::X_STATIC_TEXT);
        $this->bold(self::FONT_TITLE_SIZE);
        $this->MultiCell(0, 5, self::txt(self::TEXT_DESCRIPTION_AND_TARGET), "LTR", 1);
        $this->regular();
        $description =  self::txt($this->data->project_description);
        $invest =  self::txt($this->data->project_invest);
        $return =  self::txt($this->data->project_return);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $description . $invest . $return, "LBR", 1, "J");
    }

    /**
     * Draws the payment section in a box.
     */
    private function payment() {
        $this->setY($this->getY() + 5);
        $this->setX(self::X_STATIC_TEXT);
        $this->bold(self::FONT_TITLE_SIZE);
        $this->MultiCell(0, 3.8, iconv('UTF-8', 'ISO-8859-15', self::TEXT_PAYMENT_HEADER), "LTR", 1);
        $this->regular();
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_PAYMENT_ACCOUNT_NUMBER . $this->data->bank);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 6, $txt, "LR", 1);
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_PAYMENT_ACCOUNT_OWNER . $this->data->bank_owner);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LR", 1);
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_PAYMENT_PAYPAL_ACCOUNT . $this->data->paypal);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LR", 1);
        $txt = iconv('UTF-8', 'ISO-8859-15', self::TEXT_PAYMENT_PAYPAL_OWNER . $this->data->paypal_owner);
        $this->setX(self::X_STATIC_TEXT);
        $this->MultiCell(0, 5, $txt, "LBR", 1);
    }

    function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '') {
        $k = $this->k;
        if ($this->y + $h > $this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {
            $x = $this->x;
            $ws = $this->ws;
            if ($ws > 0) {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->AddPage($this->CurOrientation);
            $this->x = $x;
            if ($ws > 0) {
                $this->ws = $ws;
                $this->_out(sprintf('%.3f Tw', $ws * $k));
            }
        }
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $s = '';
        if ($fill == 1 or $border == 1) {
            if ($fill == 1)
                $op = ($border == 1) ? 'B' : 'f';
            else
                $op = 'S';
            $s = sprintf('%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
        }
        if (is_string($border)) {
            $x = $this->x;
            $y = $this->y;
            if (is_int(strpos($border, 'L')))
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y + $h)) * $k);
            if (is_int(strpos($border, 'T')))
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);
            if (is_int(strpos($border, 'R')))
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
            if (is_int(strpos($border, 'B')))
                $s.=sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
        }
        if ($txt != '') {
            if ($align == 'R')
                $dx = $w - $this->cMargin - $this->GetStringWidth($txt);
            elseif ($align == 'C')
                $dx = ($w - $this->GetStringWidth($txt)) / 2;
            elseif ($align == 'FJ') {
                //Set word spacing
                $wmax = ($w - 2 * $this->cMargin);
                $this->ws = ($wmax - $this->GetStringWidth($txt)) / substr_count($txt, ' ');
                $this->_out(sprintf('%.3f Tw', $this->ws * $this->k));
                $dx = $this->cMargin;
            }
            else
                $dx = $this->cMargin;
            $txt = str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));
            if ($this->ColorFlag)
                $s.='q ' . $this->TextColor . ' ';
            $s.=sprintf('BT %.2f %.2f Td (%s) Tj ET', ($this->x + $dx) * $k, ($this->h - ($this->y + .5 * $h + .3 * $this->FontSize)) * $k, $txt);
            if ($this->underline)
                $s.=' ' . $this->_dounderline($this->x + $dx, $this->y + .5 * $h + .3 * $this->FontSize, $txt);
            if ($this->ColorFlag)
                $s.=' Q';
            if ($link) {
                if ($align == 'FJ')
                    $wlink = $wmax;
                else
                    $wlink = $this->GetStringWidth($txt);
                $this->Link($this->x + $dx, $this->y + .5 * $h - .5 * $this->FontSize, $wlink, $this->FontSize, $link);
            }
        }
        if ($s)
            $this->_out($s);
        if ($align == 'FJ') {
            //Remove word spacing
            $this->_out('0 Tw');
            $this->ws = 0;
        }
        $this->lasth = $h;
        if ($ln > 0) {
            $this->y+=$h;
            if ($ln == 1)
                $this->x = $this->lMargin;
        }
        else
            $this->x+=$w;
    }

    private function parseText($text) {
        $reader = new \XMLReader;
        $reader->xml($text);
        while ($reader->read() !== FALSE) {
            if ($reader->nodeType === \XMLReader::ELEMENT) {
                $txt = self::txt($reader->readString());
                switch ($reader->name) {
                    case 'head':
                        $this->setX(self::X_START);
                        $this->bold();
                        $this->Cell(0, 5, $txt, 0, 0, "C");
                        break;
                    case 'name':
                        $this->SetX(self::X_START);
                        $this->bold();
                        $this->WriteHTML($txt);
                        $this->regular();
                        break;
                    case 'title':
                        $this->y = $this->y - 3;
                        $this->SetX(self::X_STATIC_TEXT);
                        $this->SetFont($this->FontFamily, 'IB', self::FONT_SIZE);
                        $this->WriteHTML($txt);
                        $this->regular();
                        $this->y = $this->y - 5;
                        break;
                    case 'text':
                        $this->SetX(self::X_START);
                        $this->regular();
                        if ($reader->getAttribute("option") == "type") {
                            switch ($this->data->type) {
                                case '0':
                                    $txt .= self::txt(self::TEXT_TYPE_0);
                                    break;
                                case '1':
                                    $txt .= self::txt(self::TEXT_TYPE_1);
                                    break;
                                case '2':
                                    $txt .= self::txt(self::TEXT_TYPE_2);
                                    break;
                            }
                            $txt = preg_replace('/%([a-z_]+)%/e', 'iconv(\'UTF-8\', \'ISO-8859-15\', $this->data->$1)', $txt);
                        }
                        $this->WriteHTML($txt);
                        break;
                    case 'data':
                        switch ($reader->getAttribute("option")) {
                            case 'project_identification':
                                $this->projectIdentification();
                                break;
                            case 'project_description':
                                $this->projectDescription();
                                break;
                            case 'payment':
                                $this->payment();
                                break;
                        }
                    case 'title':
                        $this->SetX(self::X_STATIC_TEXT);
                        $this->SetStyle("B", false);
                        $this->Cell(0, 5, $txt, 0, 0, "L");
                        $this->regular();
                        break;
                    default:
                        break;
                }
                $this->setY($this->getY() + 5);
            }
        }
    }

    private function contractStart() {
        $this->setX(self::X_START + 10);
        $this->bold(16);
        $this->Cell(0, 5, self::TEXT_GENERAL_CONDITIONS, 0, 1, "C");
        $this->setX(self::X_START);
        $this->Cell(0, 5, self::TEXT_PLATFORM, 0, 1, "C");
        $this->bold();
        $this->setY($this->getY()+7); // para separar
        $this->setX(self::X_START);
        $placeAndDate = self::TEXT_PLACE . $this->data->date;
        $this->Cell(0, 5, $placeAndDate, 0, 1, "C");
    }

    /**
     * Gets and fills the dynamic part of the contract.
     */
    private function getDynamicText() {
        $text = file_get_contents(__DIR__.self::DYNAMIC_TEXT_SOURCE);
        $text = preg_replace('/%([a-z_]+)%/e', '$this->data->$1', $text);
        $this->parseText($text);
    }

    /**
     * Gets and fills the static part of the contract.
     */
    private function getStaticText() {
        $text = file_get_contents(__DIR__.self::STATIC_TEXT_SOURCE);
        $this->parseText($text);
    }

    private function contractEnd() {
        $this->setX(self::X_STATIC_TEXT);
        $this->Cell(0, 5, self::TEXT_FOOTER_IMPULSOR);
        $this->setX(100);
        $this->Cell(0, 5, self::txt(self::TEXT_FOOTER_FOUNDATION));
    }

    /**
     * Draw header part
     * (non-PHPdoc)
     * @see FPDF::Header()
     */
    function Header() {
        $this->SetX(10);
        $this->SetFont(self::HEADER_FONT, '', self::FONT_HEADER_SIZE);
        $this->WriteHTML(self::txt(self::TEXT_HEADER_NUM) . $this->data->fullnum);
        $this->Cell(0, 5, "", 0, 1);
        if ($this->data->draft)
            $this->centerImage(__DIR__.self::DRAFT_IMAGE);
    }

    /**
     * (non-PHPdoc)
     * @see FPDF::Footer()
     */
    function Footer() {
        $this->SetY(-15);
        $this->SetFont(self::HEADER_FONT, '', self::FONT_HEADER_SIZE);
        $this->Cell(0, 10, $this->PageNo(), 0, 0, 'C');
    }

    private function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI;
    }

    private function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);

        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;

        $scale = min($widthScale, $heightScale);

        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }

    /**
     * Draws a centered image.
     * @param string $img
     */
    private function centerImage($img) {
        list($width, $height) = $this->resizeToFit($img);
        $this->Image(
                $img, self::DRAFT_X, self::DRAFT_Y, $width, $height
        );
    }

    /**
     * Generates the contract pdf document.
     * @param string $output I to show in browser, D to download the document.
     * @param string $draft true if the draft image will be shown in the document.
     */
    public function generate() {
        $this->FontFamily = self::FONT;
        $this->AliasNbPages();
        $this->AddPage("P", "A4");
        $this->SetFont(self::FONT, '', self::FONT_SIZE);
        $this->SetMargins(self::MARGIN_LEFT, self::MARGIN_TOP, self::MARGIN_RIGHT);
        $this->contractStart();
        $this->getDynamicText();
        $this->getStaticText();
        $this->contractEnd();
    }

}
