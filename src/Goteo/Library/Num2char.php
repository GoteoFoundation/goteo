<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library {

    /*
     * Clase para pasar un numero a letras
     *
     */

    class Num2char {

        private $letra;
        private $convertidos = Array();
        private $digitos = Array();
        private $lugares;
        private $decimal;
        private $valor;
        private $primparte;
        private $partedecimal;

        public function __construct($valor, $decimal) {
            $this->valor = $valor;
            $this->decimal = $decimal;
            $vale = $valor;
            $partido = explode(".", $vale);
            $numero = $partido[0];
            $this->primparte = $numero;
            /* if ($partido[1]==0)
              {
              $this->partedecimal=sprintf("%+02s",$partido[1]);
              }else */$this->partedecimal = /* sprintf("%-02s", */$partido[1]; /* ); */
            if ($numero == "")
                $numero = 0;
            $largo = strlen($numero);
            $lugares = ceil($largo / 3);
            $this->lugares = $lugares;
            $alreves = strrev($numero);
            for ($i = 0; $i < $lugares; $i++) {
                $dadosvuelta[] = substr($alreves, $i * 3, 3);
            }
            for ($j = 0; $j < count($dadosvuelta); $j++) {
                $this->digitos[] = strrev($dadosvuelta[$j]);
            }
            for ($h = 0; $h < count($this->digitos); $h++) {
                $this->convertidos[] = $this->convertir($this->digitos[$h]);
                //echo $this->convertir($this->digitos[$h])."<br>";
            }$this->armado($this->convertidos);
        }

        private function convertir($numero) {
            $unidades = array("un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve",
                "diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete",
                "dieciocho", "diecinueve", "veinte");
            $decenas = array("veinti", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa");
            $centenas = array("ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos",
                "setecientos", "ochocientos", "novecientos");
            if ($numero == 0 || $numero == "") {
                return "cero";
            } elseif ($numero < 100) {
                if ($numero <= 20)
                    return $unidades[$numero - 1];
                else {
                    $dec = $numero / 10;
                    if ((int) $dec > 2 && $numero % 10 != 0)
                        $espacio = " y ";
                    $unid = $numero % 10;
                    return $decenas[$dec - 2] . $espacio . $unidades[$unid - 1];
                    $espacio = "";
                }
            }
            else {
                $cent = $numero / 100;
                if ($numero % 100 > 20) {
                    $dec = ($numero % 100) / 10;

                    if ((int) $dec > 2 && ($numero % 100) % 10)
                        $espacio = " y ";

                    $unid = ($numero % 10);
                    return $centenas[$cent - 1] . " " . $decenas[$dec - 2] . $espacio . $unidades[$unid - 1];
                    $espacio = "";
                }
                else {
                    $dec = $numero % 100;
                    $moment = $this->nrosRaros($centenas[$cent - 1] . " " . $unidades[$dec - 1]);
                    return $moment;
                }
            }
        }

        public function getLetra() {
            if ($this->primparte == 0) {
                if ($this->decimal > 0)
                    return "cero con " . $this->decimales($this->partedecimal);
                else
                    return "cero";
            }
            else {
                return $this->letra;
            }
        }

        private function nrosRaros($raros) {
            if ($raros == "ciento ")
                $raros = "cien";
            return $raros;
        }

        private function armado($arreglo) {
            $contador = count($arreglo);
            for ($i = $contador; $i > 0; $i--) {
                $momentaneo[] = $this->posiciones($arreglo[$i - 1], $i, $arreglo[$i]);
            }
            for ($h = 0; $h < $contador; $h++) {
                $literal = $literal . $momentaneo[$h] . " ";
            }
            if ($this->decimal != 0) {
                $this->letra = $literal . " con " . $this->decimales($this->partedecimal);
            } else {
                $this->letra = $literal;
            }
        }

        private function posiciones($literal, $posicion, $anterior) {

            //echo $posicion."<br>";
            //echo $literal."<br>";
            $nronombre = array("", "mil", "millon", "millones", "billon", "billones");
            switch ($posicion) {
                case 0:
                    $devolver = $nronombre[0];
                    break;
                case 1:
                    if ($literal == "" || $literal == "cero") {
                        $devolver = $nronombre[0];
                        break;
                    }
                    $devolver = $literal . " " . $nronombre[0];
                    break;
                case 2:
                    if ($literal == "" || $literal == "cero") {
                        $devolver = $nronombre[0];
                        break;
                    }
                    $literal = str_replace("uno", "un", $literal);
                    $devolver = $literal . " " . $nronombre[1];
                    break;
                case 3:
                    if ($literal == "" || $literal == "cero") {
                        if ($anterior == "cero") {
                            $devolver = $nronombre[0];
                            break;
                        } else {
                            $devolver = $nronombre[0] . " " . $nronombre[3];
                            break;
                        }
                    }
                    $literal = str_replace("uno", "un", $literal);
                    if ($literal == "un") {
                        $devolver = $literal . " " . $nronombre[2];
                        break;
                    } else {
                        /* if($anterior=="cero"){$devolver=$literal." ".$nronombre[2];break;}
                          else { */$devolver = $literal . " " . $nronombre[3];
                        break;
                    }
                //}
                case 4:
                    if ($literal == "" || $literal == "cero") {
                        $devolver = $nronombre[0];
                        break;
                    }
                    $literal = str_replace("uno", "un", $literal);
                    $devolver = $literal . " " . $nronombre[1];
                    break;
                case 5:
                    if ($literal == "" || $literal == "cero") {
                        if ($anterior == "cero") {
                            $devolver = $nronombre[0];
                            break;
                        } else {
                            $devolver = $nronombre[0] . " " . $nronombre[5];
                            break;
                        }
                    }
                    $literal = str_replace("uno", "un", $literal);
                    if ($literal == "un") {
                        $devolver = $literal . " " . $nronombre[4];
                        break;
                    } else {
                        /* if($anterior=="cero"){$devolver=$literal." ".$nronombre[2];break;}
                          else { */$devolver = $literal . " " . $nronombre[5];
                        break;
                    }
                case 6:
                    if ($literal == "" || $literal == "cero") {
                        $devolver = $nronombre[0];
                        break;
                    }
                    $literal = str_replace("uno", "un", $literal);
                    $devolver = $literal . " " . $nronombre[1];
            }
            return $devolver;
        }

        private function decimales($decimal) {
            $redondeado = "0." . $decimal;
            $decimo = $this->redondeo($redondeado, $this->decimal);
            $lugares = pow(10, $this->decimal);
            $decimal = $decimo . "/" . $lugares;
            return $decimal;
        }

        private function redondeo($valor, $lugares) {
            $retorno = round($valor * pow(10, $lugares));
            return $retorno;
        }

    }

}
?>