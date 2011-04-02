<?php

namespace Goteo\Library {

	/*
	 * Clase para verificar valores
	 * Nif (DNI, NIE o pasaporte) - X0000000A
	 * Mail - xxxx.xxxx@xxx.xxxx.xxxx
	 * Words - minimo de x palabras
	 *
	 */
    class Check {

		/*
		 * Debe validar nif, nie, pasaporte
		 */
		public static function Nif ($value) {

			// quitamos puntos y guiones
			$value = str_replace(array('_', '.', ' ', '-', ','), '', $value);
			
			$value = strtoupper($value);
			for ($i = 0; $i < 9; $i++) {
				$num[$i] = substr($value, $i, 1);
			}

			//si no tiene un formato valido devuelve error
			if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $value)) {
				return false;
			}

			//comprobacion de NIFs estandar
			if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $value)) {
				if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($value, 0, 8) % 23, 1)) {
					return true;
				} else {
					return false;
				}
			}

			//algoritmo para comprobacion de codigos tipo CIF
			$suma = $num[2] + $num[4] + $num[6];
			for ($i = 1; $i < 8; $i += 2) {
				$suma += substr((2 * $num[$i]), 0, 1) + substr((2 * $num[$i]), 1, 1);
			}
			$n = 10 - substr($suma, strlen($suma) - 1, 1);

			//comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
			if (preg_match('/^[KLM]{1}/', $value)) {
				if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($value, 1, 8) % 23, 1)) {
					return true;
				} else {
					return false;
				}
			}

			//comprobacion de CIFs
			if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $value)) {
				if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1)) {
					return true;
				} else {
					return false;
				}
			}

			//comprobacion de NIEs
			//T
			if (preg_match('/^[T]{1}/', $value)) {
				if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $value)) {
					return true;
				} else {
					return false;
				}
			}
			
			//XYZ
			if (preg_match('/^[XYZ]{1}/', $value)) {
				if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X', 'Y', 'Z'), array('0', '1', '2'), $value), 0, 8) % 23, 1)) {
					return true;
				} else {
					return false;
				}
			}
			//si todavia no se ha verificado devuelve error
			return false;
		}

		public static function Mail ($value) {
			
			if (preg_match("/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i", $value))
				return true;
			else
				return false;
		}

		public static function Words ($value, $number) {

			$words = explode(' ', $value);

			if (count($words) >= $number)
				return true;
			else
				return false;
		}

		public static function Phone ($value) {

			$value = str_replace(array('_', '.', ' ', '-', ','), '', $value);

			// si no son 9 numeros, chof
			if (preg_match('/^\d{9}$/', $value) > 0)
				return true;
			else
				return false;
		}


	}
	
}