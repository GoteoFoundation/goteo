<?php

namespace Goteo\Model {

	/*
	 * Clase para sacar textos estáticos de la tabla text
	 *  (por ahora utilizar gettext no nos compensa, quizás más adelante)
	 *
	 */
    class Text extends \Goteo\Core\Model {
		
		static public function get ($id = null) {
			if ($id === null)
				return '';

			$lang = 'es'; // por ahora solo español

			// buscamos el texto en cache
			static $_cache = array();
			if (isset($_cache[$id][$lang]))
				return $_cache[$id][$lang];

			// buscamos el texto en la tabla
			$query = self::query("SELECT text FROM text WHERE id = :id AND lang = :lang", array(':id' => $id, ':lang' => $lang));
			$exist = $query->fetchObject();
			if ($exist->text) {
				return $_cache[$id][$lang] = $exist->text;
			} else {
				// lo metemos en la tabla pero no en cache
				self::query("INSERT INTO text (id, lang, text) VALUES (:id, :lang, :text)", array(':id' => $id, ':lang' => $lang, ':text' => $id));

				return $id;
			}
		}

		/*
		 *  Metodo para la lista de textos segun idioma
		 */
		public static function getAll($lang = null) {
			if ($lang === null)
				return false;

			$query = self::query("SELECT id, text FROM text WHERE lang = ?", array($lang));
			return $query->fetchAll(\PDO::FETCH_CLASS);
		}

		/*
		 *  Esto se usara para la gestión de traducciones
		 */
		public function save($data, &$errors = array()) {
			if (!is_array($data) ||
				empty($data['id']) ||
				empty($data['text']) ||
				empty($data['lang'])) {
					return false;
			}

			if (self::query("UPDATE text SET text = :text WHERE MD5(id) = :id AND lang = :lang", array(':text' => $data['text'], ':id' => $data['id'], ':lang' => $data['lang']))) {
				return true;
			}
			else {
				$errors[] = 'Error al insertar los datos <pre>' . print_r($data, 1) . '</pre>';
				return false;
			}


		}


		/*
		 *   Método para formatear friendly un texto para ponerlo en la url
		 */
		static public function urliza($texto)
		{
			$texto = trim(strtolower($texto));
			// Acentos
			$texto = strtr($texto, "ÁÀÄÂáàâäÉÈËÊéèêëÍÌÏÎíìîïÓÒÖÔóòôöÚÙÛÜúùûüÇçÑñ", "aaaaaaaaeeeeeeeeiiiiiiiioooooooouuuuuuuuccnn");
			// Separadores
			$texto = preg_replace("/[\s\,\;\_\/\-]+/i", "-", $texto);
			$texto = preg_replace("/[^a-z0-9\.\-\+]/", "", $texto);
			return $texto;
		}

		/*
		 *   Método para recortar un texto
		 */
		static public function recorta ($texto, $longitud, $puntos = '...')  {
			// Es HTML?
			$html = (strip_tags($texto) != $texto);
			$palabras_vacias = array();
			$separadores = array(" ",".",",",";");

			$palabras_vacias = array ("un", "uno", "unos", "unas", "una",
			"dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve", "diez",
			"el", "la", "los", "las", "lo",
			"que",
			"o", "y", "u", "e", "a",
			"ante", "bajo", "cabe", "con", "contra", "de", "desde", "hasta", "hacia", "para", "por", "según", "sin", "sobre", "tras", "durante", "mediante",
			);

			$texto = trim($texto);
			if (strlen($texto) <= $longitud) return $texto;
			$texto = substr($texto,0,$longitud);

			// Buscamos el último espacio
			$texto = substr($texto, 0, strrpos($texto, " "));

			// Quitamos palabras vacías
			$ultima = ultima_palabra($texto,$separadores );
			while ($texto != "" && (in_array($ultima,$palabras_vacias) || strlen($ultima)<=2) || ($html && $ultima{1} == "<" && substr($ultima,-1) == ">")) {
				$texto = substr($texto,0,strlen($texto)-strlen($ultima));
				while ($texto != "" && in_array(substr($texto,-1),$separadores)){
					$texto = substr($texto, 0, -1);
				}
				$ultima = ultima_palabra($texto,$separadores);
			}

			// Hemos cortado una etiqueta html?
			if ($html && strrpos($texto,"<") > strrpos($texto,">")) {
				$texto = substr($texto,0,strrpos($texto,"<"));
			}
			// Si el texto era html, cerramos las etiquetas
			if ($html) $texto = cerrar_etiquetas($texto);
			if ($puntos !== false) $texto .= $puntos;
			return $texto;
		}

	}
    
}