<?php
require_once 'library/fpdf/fpdf.php';  // Libreria pdf

  class PDF extends FPDF{

    /*
    public function Footer() {
        $this->SetY(-22);
        $this->Cell(0,10,$this->elPie,0,0,'C');
    }
    */

    public function regular($size = 9) {
        $this->SetFont('Arial','',$size);
    }

    public function bold($size = 9) {
        $this->SetFont('Arial','B',$size);
    }

    public function underlined($size = 9) {
        $this->SetFont('Arial','U',$size);
    }

    public function blue() {
        $this->SetTextColor(0,0,255);
    }

    public function black() {
        $this->SetTextColor(0,0,0);
    }

    // para poner un separador
    public function hr($bold = false) {

        // si es bold es de 2mm sino de 1
        $h = $bold ? 0.8 :0.2;

        $this->SetFillColor(0, 0, 0);
        $this->Cell(0,$h,'',1,0,'',true);
        $this->SetFillColor(255, 255, 255);
    }

};


/*
 * Especifica para generar el objeto de certificado de donativo
 */
function donativeCert($data) {

//    die(trace($data));
    /*
     * Datos de la fundación
     */
    $data->ffa->name = 'Fundación Fuentes Abiertas';
    $data->ffa->cif = 'G57728172';
    $data->ffa->phone = '00 34 871 57 15 57';
    $data->ffa->email = 'info@fuentesabiertas.org';
    $data->ffa->site = 'http://www.fuentesabiertas.org';
    $data->ffa->address = "C/ Forn de l' Olivera 22, planta baja puerta derecha";
    $data->ffa->zipcode = '07012';
    $data->ffa->location= 'Palma de Mallorca, Baleares, España';

    // maquetacion de columnas para los datos de la fundacion y del donante
    $lh = 5; // separacion de linea standard
    $w1 = 80; //columna izq.
    $s  = 20; // separecion central

    // el simbolo euro para windows-1252
    $EURO = utf8_encode(chr(128));

    $pdf = new PDF('P','mm','A4');
    $pdf->SetAutoPageBreak(false);

    //afegir pagina
    $pdf->AddPage();
    $pdf->SetMargins(20, 20, 20);
    $pdf->black();

   //cabecera
    $pdf->bold(14);
    $pdf->cell(10);
    $pdf->cell($w1,10,"CERTIFICADO DE DONACIÓN");
    $pdf->cell(27);
    $pdf->Image('view/css/logo-ffa.jpg');

    $pdf->Ln(5);
    
    $pdf->hr();

    $pdf->Ln(2);
    
    $pdf->bold(11);
    $pdf->cell($w1,10,"FUNDACIÓN FUENTES ABIERTAS");
    $pdf->cell($s);
    $pdf->cell(0,10,"Donante:");

    $pdf->regular();

    $pdf->Ln($lh);
    $pdf->cell($w1,10,"CIF {$data->ffa->cif}");
    $pdf->cell($s);
    $pdf->cell(0,10,$data->name);

    $pdf->Ln($lh);
    $pdf->cell($w1,10,$data->ffa->address);
    $pdf->cell($s);
    $pdf->cell(0,10,"NIF {$data->nif}");

    $pdf->Ln($lh);
    $pdf->cell($w1,10,"{$data->ffa->zipcode} - {$data->ffa->location} -");
    $pdf->cell($s);
    $pdf->cell(0,10,$data->address);

    $pdf->Ln($lh);
    $pdf->cell($w1,10,"Tel. {$data->ffa->phone}");
    $pdf->cell($s);
    $pdf->cell(0,10,"{$data->zipcode} - {$data->location} ({$data->country})");

    $pdf->Ln($lh);
    $pdf->cell($w1,10,$data->ffa->email . ' - ' . $data->ffa->site);
    $pdf->cell($s);
    $pdf->cell(0,10,'');

    $pdf->Ln(12);

    $pdf->hr(true);

    $pdf->Ln(10);

    $pdf->bold();
    $pdf->Write(5,'Alfonso Pachecho Cifuentes');
    $pdf->regular();
    $pdf->Write(5,', como Secretario de la Fundación Fuentes Abiertas, inscrita con el número 1438 en el Registro de Fundaciones del Ministerio de Educación, Cultura y Deportes, a los efectos previstos en el art. 24 de la Ley 49/2002, de 23 de diciembre, de Régimen Fiscal de Entidades Sin Fines Lucrativos e Incentivos Fiscales al Mecenazgo,');

    $pdf->Ln(15);
    
    $pdf->regular(14);
    $pdf->Write(5,'CERTIFICA:');

    $pdf->Ln(10);

    // Comienza con fuente regular
    $pdf->regular();
    $pdf->Write(5,'Que ');
    $pdf->bold();
    $pdf->Write(5,trim($data->name));
    $pdf->regular();

    $pdf->Write(5,' ha donado ');
    // segun cantidad de donativos
    if (count($data->dates) <= 10) {
        foreach ($data->dates as $invest) {
            $pdf->Write(5,'en fecha ');
            $pdf->bold();
            $pdf->Write(5,$invest->date);
            $pdf->regular();
            $pdf->Write(5,' una aportación de');
            $pdf->bold();
            $pdf->Write(5,"{$invest->amount} euros");
            $pdf->regular();
            $pdf->Write(5,', ');
        }
    } 

    $pdf->Write(5,'con carácter irrevocable -sin perjuicio de lo establecido en las normas imperativas civiles que regulan la revocación de donaciones- donación dineraria a la Fundación Fuentes Abiertas de');
    $pdf->bold();
    $pdf->Write(5,$data->amount_char."euros (".$data->amount." euros)");
    $pdf->regular();
    $pdf->Write(5,'.');

    $pdf->Ln(10);
    $pdf->Write(5,'La Fundación destinará la donación recibida a las ayudas concedidas al proyecto o proyectos gestionados desde la plataforma Goteo.org y seleccionados por el/la donante registrado/a con el email "');
    $pdf->bold();
    $pdf->Write(5,$data->userData->email);
    $pdf->regular();
    $pdf->Write(5,'". Esta actividad es propia de la Fundación y está recogida dentro de los fines fundacionales de la misma (');
    $pdf->blue();
    $pdf->underlined();
    $pdf->Write(5,$data->ffa->site . '/?page_id=36',$data->ffa->site . '/?page_id=36');
    $pdf->black();
    $pdf->regular();
    $pdf->Write(5,').');

    $pdf->Ln(10);
    $pdf->Write(5,'En virtud de lo previsto en los artículos 19 y 20 de la citada Ley 49/2002 -y siempre en la forma y con los límites previstos en dicha Ley- con el presente certificado los/as contribuyentes del Impuesto sobre la Renta de las Personas Físicas tendrán derecho a deducir de la cuota íntegra el 25 por 100 del importe de la donación, mientras que los sujetos pasivos del Impuesto sobre Sociedades tendrán derecho a deducir de la cuota íntegra el 35 por 100 del importe de la donación.');
    
    $pdf->Ln(15);
        $pdf->Write(5,'En Palma de Mallorca, a ');
        $pdf->bold();
        $pdf->Write(5,'31');
        $pdf->regular();
        $pdf->Write(5,' de ');
        $pdf->bold();
        $pdf->Write(5,'diciembre');
        $pdf->regular();
        $pdf->Write(5,' de 2.012');
    $pdf->Ln(6);
        $pdf->Image('view/css/firmas.jpg');
    $pdf->Ln(1);
        $pdf->cell(80,10,'Fdo: Alfonso Pacheco Cifuentes');
        $pdf->cell(0,10,'Con el visto bueno de la presidenta', 0, 0, 'R');
    $pdf->Ln(5);
        $pdf->cell(80,10,'Secretario del Patronato y de la Fundación');
        $pdf->cell(0,10,'Susana Noguero García, representante', 0, 0, 'R');
    $pdf->Ln(5);
        $pdf->cell(80,10,'');
        $pdf->cell(0,10,'de Platoniq Sistema Cultural en el Patronato', 0, 0, 'R');
    $pdf->Ln(5);
        $pdf->cell(80,10,'');
        $pdf->cell(0,10,'por designación escritura constitución', 0, 0, 'R');

    $pdf->Ln(10);
    
    $pdf->hr();

    $pdf->Ln(3);

    // Footer Goteo
    $pdf->Image('view/css/footer_goteo.png');

    $pdf->Ln(3);
    
    $pdf->hr(true);

    return $pdf;
}

