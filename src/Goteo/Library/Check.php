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
	 * Clase para verificar valores
	 * Nif (DNI, NIE o pasaporte) - X0000000A
     * VAT (de los 27 UE)
	 * Mail - xxxx.xxxx@xxx.xxxx.xxxx
	 * Words - minimo de x palabras
	 *
	 */
    class Check {

		/*
		 * Debe validar nif, nie, pasaporte
		 */
		public static function nif ($value, &$type = '') {

            // quitamos puntos y guiones
            $value = str_replace(array('_', '.', ' ', '-', ',', '\\', '+', '*', '/'), '', $value);
			$value = strtoupper($value);

			for ($i = 0; $i < strlen($value); $i++) {
				$num[$i] = substr($value, $i, 1);
			}

			//comprobacion de NIFs estandar
			if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $value)) {
				if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($value, 0, 8) % 23, 1)) {
					$type = 'nif';
					return true;
				}
			}

			//algoritmo para comprobacion de codigos tipo CIF
			$suma = $num[2] + $num[4] + $num[6];
			for ($i = 1; $i < 8; $i += 2) {
				$suma += (int)substr((2 * $num[$i]), 0, 1) + (int)substr((2 * $num[$i]), 1, 1);
			}
			$n = 10 - (int)substr($suma, strlen($suma) - 1, 1);

			//comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
			if (preg_match('/^[KLM]{1}/', $value)) {
				if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($value, 1, 8) % 23, 1)) {
					$type = 'cif';
					return true;
				}
			}

			//comprobacion de CIFs
			if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $value)) {
				if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1)) {
					$type = 'cif';
					return true;
				}
			}

			//comprobacion de NIEs
			//T
			if (preg_match('/^[T]{1}/', $value)) {
				if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $value)) {
					$type = 'nie';
					return true;
				}
			}

			//XYZ
			if (preg_match('/^[XYZ]{1}/', $value)) {
				if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X', 'Y', 'Z'), array('0', '1', '2'), $value), 0, 8) % 23, 1)) {
					$type = 'nie';
					return true;
				}
			}

            // Registration numbers in Belgium (9-10 digits only)
            if (preg_match('/^\d{9,10}$/',$value)) {
					$type = 'bel';
                    return true;
            }

            // Siret (cif/nif) in France (14 digits only)
            if (preg_match('/^\d{14}$/',$value)) {
					$type = 'siret';
                    return true;
            }

            // RFC México
            if (preg_match('/^[a-zA-Z]{3,4}(\d{6})((\D|\d){3})?$/',$value)) {
					$type = 'rfc';
                    return true;
            }




            // Validación del numero VAT para los 27 paises de la UE
            $vats = array();
            $vats[] = '(AT)?U[0-9]{8}';
            $vats[] = '(BE)?[0]?[0-9]{9}';
            $vats[] = '(BG)?[0-9]{9,10}';
            $vats[] = '(CY)?[0-9]{8}[A-Z]';
            $vats[] = '(CZ)?[0-9]{8,10}';
            $vats[] = '(EE|EL|GR|PT)?[0-9]{9}';
            $vats[] = '(DE)?[0-9]{9,10}';
            $vats[] = '(FR)?[0-9A-Z]{2}[0-9]{7,9}';
            $vats[] = '(FI|HU|LU|MT|SI|DK)?[0-9]{8}';
            $vats[] = '(IE)?[0-9][0-9A-Z][0-9]{5}[A-Z]';
            $vats[] = '(IT|LV)?[0-9]{11}';
            $vats[] = '(LT)?([0-9]{9}|[0-9]{12})';
            $vats[] = '(NL)?[0-9]{9}B[0-9]{2}';
            $vats[] = '(PL|SK)?[0-9]{10}';
            $vats[] = '(RO)?[0-9]{2,10}';
            $vats[] = '(SE)?[0-9]{12}';
            $vats[] = '(ES)?([0-9A-Z][0-9]{7}[A-Z])|([A-Z][0-9]{7}[0-9A-Z])';
            $vats[] = '(GB)?([1-9][0-9]{2}[0-9]{4}[0-9]{2})|([1-9][0-9]{2}[0-9]{4}[0-9]{2}[0-9]{3})|((GD|HA)[0-9]{3})';

            $expr = '/^('.implode($vats, '|').')$/';

            if (preg_match($expr, $value)) {
                $type = 'vat';
                return true;
            }

            //si todavia no se ha verificado devuelve error
			return false;
		}

		/**
		 * Valida una dirección de correo.
		 *
		 * Extraido de:
		 * 	http://www.linuxjournal.com/article/9585
		 *
		 * @param type string	$value	E-mail
		 * @return type bool
		 */
		public static function mail ($value) {
			// First, we check that there's one @ symbol,
			// and that the lengths are right.
			if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $value)) {
			    // Email invalid because wrong number of characters
			    // in one section or wrong number of @ symbols.
			    return false;
			}
			// Split it into sections to make life easier
			$email_array = explode("@", $value);
			$local_array = explode(".", $email_array[0]);
			for ($i = 0; $i < sizeof($local_array); $i++) {
			    if(!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
			        return false;
			    }
			}
			// Check if domain is IP. If not,
			// it should be valid domain name
			if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
			    $domain_array = explode(".", $email_array[1]);
			    if (sizeof($domain_array) < 2) {
			        return false; // Not enough parts to domain
			    }
			    for ($i = 0; $i < sizeof($domain_array); $i++) {
			        if(!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/",
			        $domain_array[$i])) {
			            return false;
			        }
			    }
			}
			return true;
		}

		/**
		 * Valida una contraseña.
		 *
		 * @param type string	$value	Contraseña
		 * @return type bool
		 */
		public static function password ($value) {
		    if(strlen($value)<6) {
		        return false;
		    }
		    return true;
		}

		public static function words ($value, $number) {

			$words = explode(' ', $value);

			if (count($words) >= $number)
				return true;
			else
				return false;
		}

		public static function phone ($value) {

			$value = str_replace(array('_', '.', ' ', '-', ',', '+', '(', ')'), '', $value);

			// si no son numeros, chof
			if (preg_match('/^\d{9,14}$/', $value) > 0)
				return true;
			else
				return false;
		}

        public static function country() {
            //@TODO Sacar el pais de la ip ip2country de ode.google or something
            return 'España';
        }

        /*
         * Metodo para reordenar una tabla moviendo uno de sus registros
         * Necesita tener un campo de orden actualizable, por defecto `order`
         * Puede tener en cuenta que el registro tiene una seccion/categoria/agrupacion
         *
         */
        public static function reorder($idReg, $updown, $table, $idField = 'id', $orderField = 'order', $extra = array()) {

            //uso el modelo core para hacer los querys
            $model = '\Goteo\Core\Model';
            $regs = array();

            // ojo con el campos extra para no pisar otros tegistros
            $sqlSec = '';
            $and = 'WHERE';
            foreach ($extra as $campo=>$valor) {
                $sqlSec .= ($valor == '') ? " $and (`{$campo}` = '{$valor}' OR `{$campo}` IS NULL)" : " $and `{$campo}` = '{$valor}'";
                $and = 'AND';
            }
            //sacar de la tabla ordenando y poniendo en array de 10 en 10
            $sql = "SELECT `{$idField}` FROM {$table} {$sqlSec} ORDER  BY `{$orderField}` ASC";
            if ($query = $model::query($sql)) {
                $order = 10;
                while ($row = $query->fetchObject()) {
                    $regs[$row->$idField] = $order;
                    $order+=10;
                }

                //al elemento target cambiarle segun 'up'-5  'down'+5
                if ($updown == 'up') {
                    $regs[$idReg] -= 15;
                } elseif ($updown == 'down') {
                    $regs[$idReg] += 15;
                }
                //reordenar array
                \asort($regs);

                // hacer updates segun el nuevo orden en una transaccion
                try {
                    $model::query("START TRANSACTION");
                    $order = 1;
                    foreach ($regs as $id=>$ordenquenoponemos) {
                        $sql = "UPDATE {$table} SET `{$orderField}`=:order WHERE {$idField} = :id";
                        $query = $model::query($sql, array(':order'=>$order, ':id'=>$id));
                        $order++;
                    }
                    $model::query("COMMIT");

                    return true;
                } catch(\PDOException $e) {
                    return false;
                }
            }

        }

        /**
         * Metodo para calcular cuanto falta para una fecha (para las 0:00 de esa fecha)
         *
         * Usa los mismos periodos que feed::time_ago y la misma idea pero con la resta a la inversa
         */
        public static function time_togo($date,$granularity=1) {

            $per_id = array('sec', 'min', 'hour', 'day', 'week', 'month', 'year', 'dec');

            $per_txt = array();
            foreach (\explode('_', Text::get('feed-timeago-periods')) as $key=>$grptxt) {
                $per_txt[$per_id[$key]] = \explode('-', $grptxt);
            }

            $justnow = Text::get('feed-timeago-justnow');

            $retval = '';
            $date = strtotime($date); // fecha objetivo
            $ahora = time(); // ahora
            $difference = $date - $ahora;
            $periods = array('hour' => 3600,
                'min' => 60,
                'sec' => 1);

            foreach ($periods as $key => $value) {
                if ($difference >= $value) {
                    $time = floor($difference/$value);
                    $difference %= $value;
                    $retval .= ($retval ? ' ' : '').$time.' ';
                    $retval .= (($time > 1) ? $per_txt[$key][1] : $per_txt[$key][0]);
                    $granularity--;
                }
                if ($granularity == '0') { break; }
            }

            return empty($retval) ? $justnow : $retval;
        }



        }

}
