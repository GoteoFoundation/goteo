<?php
require_once 'library/fpdf/fpdf.php';  // Libreria pdf

  class PDF extends FPDF{

    /*
    public function Footer() {
        $this->SetY(-22);
        //Cuenta
        $this->SetFont('Arial','',33.33);
        $this->SetTextColor('0','0','0','85');
        $this->Cell(0,10,$this->elPie,0,0,'C');
    }
    */

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
    $w2 = 80; // columna derecha

    // el simbolo euro para windows-1252
    $EURO = utf8_encode(chr(128));

    $pdf = new PDF('P','mm','A4');
    

    //afegir pagina
    $pdf->AddPage();

   //cabecera
    $pdf->SetFont('Arial','U',15);
    $pdf->cell(90,10,"CERTIFICADO DE DONACIÓN");
    $pdf->cell(10);
    $pdf->Image('view/css/logo-ffa.jpg');

    $pdf->Ln(10);
    
    $pdf->SetFont('Arial','B',11);
    $pdf->cell($w1,10,"FUNDACIÓN FUENTES ABIERTAS");
    $pdf->cell($s);
    $pdf->cell($w2,10,$data->name);

    $pdf->SetFont('Arial','',10);

    $pdf->Ln($lh);
    $pdf->cell($w1,10,"CIF {$data->ffa->cif}");
    $pdf->cell($s);
    $pdf->cell($w2,10,"NIF {$data->nif}");

    $pdf->Ln($lh);
    $pdf->cell($w1,10,$data->ffa->address);
    $pdf->cell($s);
    $pdf->cell($w2,10,$data->address);

    $pdf->Ln($lh);
    $pdf->cell($w1,10,"{$data->ffa->zipcode} - {$data->ffa->location} -");
    $pdf->cell($s);
    $pdf->cell($w2,10,"{$data->zipcode} - {$data->location} ({$data->country})");

    $pdf->Ln($lh);
    $pdf->cell($w1,10,"Tel. {$data->ffa->phone}");
    $pdf->cell($s);
    $pdf->cell($w2,10,'');

    $pdf->Ln($lh);
    $pdf->cell($w1,10,$data->ffa->email);
    $pdf->cell($s);
    $pdf->cell($w2,10,'');

    $pdf->Ln($lh);
    $pdf->SetTextColor(0,0,255);
    $pdf->SetFont('','U');
    $pdf->cell($w1,10,$data->ffa->site);
    $pdf->cell($s);
    $pdf->cell($w2,10,'');

    $pdf->Ln(15);

    // Comienza con fuente regular
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5,'Don ');
    $pdf->SetFont('Arial','B',10);
    $pdf->Write(5,'Alfonso Pachecho Cifuentes');
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5,', como Secretario de la Fundación Fuentes Abiertas, inscrita con el Nº 1438 en el Registro de Fundaciones del Ministerio de Educación, Cultura y deporte.');

    $pdf->Ln(10);
    
    $pdf->SetFont('Arial','',15);
    $pdf->Write(5,'CERTIFICA:');

    $pdf->Ln(5);

    // Comienza con fuente regular
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5,'Que ');
    $pdf->SetFont('Arial','B',10);
    $pdf->Write(5,$data->name);
    $pdf->SetFont('Arial','',10);

    // segun cantidad de donativos
    if (count($data->dates) > 1) {
        $pdf->Write(5,' ha realizado: ');
        foreach ($data->dates as $invest) {
            $pdf->Write(5,'en la fecha ');
            $pdf->SetFont('Arial','B',10);
            $pdf->Write(5,$invest->date);
            $pdf->SetFont('Arial','',10);
            $pdf->Write(5,' el importe de ');
            $pdf->SetFont('Arial','B',10);
            $pdf->Write(5,"{$invest->amount} {$EURO}, ");
            $pdf->SetFont('Arial','',10);
        }
        $pdf->Write(5,'un total de donaciones dinerarias a la Fundación Fuentes Abiertas de ');
    } else {
        $pdf->Write(5,' ha realizado en fecha ');
        $pdf->SetFont('Arial','B',10);
        $pdf->Write(5,$data->dates[0]->date);
        $pdf->SetFont('Arial','',10);
        $pdf->Write(5,', una donación dineraria a la Fundación Fuentes Abiertas de ');
    }

    $pdf->SetFont('Arial','B',10);
    $pdf->Write(5,$data->amount_char." Euros (".$data->amount." {$EURO})");
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5,'. Dicha donación se considera realizada con carácter irrevocable, lo cual se comunica al donante.');

    $pdf->Ln(10);
    $pdf->Write(5,'La Fundación destinará la donación recibida a las ayudas concedidas al proyecto o proyectos gestionados desde la plataforma Goteo.org y seleccionados por el donante registrado con ell email ('.$data->userData->email.'). ');
    $pdf->Ln(5);
    $pdf->Write(5,'Esta actividad es propia de la Fundación y está recogida dentro de los fines fundacionales (');
    $pdf->SetTextColor(0,0,255);
    $pdf->SetFont('','U');
    $pdf->Write(5,$data->ffa->site . '/?page_id=36',$data->ffa->site . '/?page_id=36');
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('');
    $pdf->Write(5,').');

    $pdf->Ln(10);
    $pdf->Write(5,'A los efectos previstos en la Ley 49/2002 de 23 de diciembre, las donaciones a la fundación Fuentes Abiertas se pueden desgravar fiscalmente (');
    $pdf->SetFont('Arial','B',10);
    $pdf->Write(5,'25%');
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5,' las personas físicas sobre el Impuesto de la Renta de las Personas Físiscas) de la base imponible y (');
    $pdf->SetFont('Arial','B',10);
    $pdf->Write(5,'35%');
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5,' las empresas sobre la cuota íntegra en el Impuesto de Sociedades), con el presente certificado que emite la entidad una vez realizada la donación con caracter irrevocable y para los fines generales de la misma.');
    
    $pdf->Ln(10);
    $pdf->cell(0,10,'En Palma de Mallorca, a xx de xxxx de 2.012', 0, 0, R);
    $pdf->Ln(20);
    $pdf->cell(0,10,'Fdo: Alfonso Pacheco Cifuentes', 0, 0, R);
    $pdf->Ln(5);
    $pdf->cell(0,10,'Con el visto bueno de la presidenta', 0, 0, L);
    $pdf->Ln(5);
    $pdf->cell(0,10,'Fdo: Susana García Noguero', 0, 0, L);

    return $pdf;
}

