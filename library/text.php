<?php

namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Core\Exception;
	/*
	 * Clase para sacar textos estáticos de la tabla text
	 *  (por ahora utilizar gettext no nos compensa, quizás más adelante)
	 *
     *  @TODO, definir donde se define y se cambia la constante LANG y utilizarla en los _::get_
	 */
    class Text {

        public
            $id,
            $lang,
            $text,
            $purpose,
            $html;

        /*
         * Devuelve un texto en HTML
         */
        static public function html ($id) {
            // sacamos el contenido del texto
            $text = call_user_func_array ( 'static::get' , \func_get_args() );
            if (self::isHtml($id))
                return $text; // el texto ES html, lo devuelve tal cual
            else
                return \htmlspecialchars ($text); // el texto NO es html, lo pasa por html especial chars
        }

        /*
         * Devuelve un testo sin HTML
         */
        static public function plain ($id) {
            // sacamos el contenido del texto
            $text = call_user_func_array ( 'Text::get' , \func_get_args() );
            if (self::isHtml($id))
                return \strip_tags($text) ; // ES html, le quitamos los tags
            else
                return $text;
        }

        static public function get ($id) {
            $lang = \GOTEO_DEFAULT_LANG; // @TODO idiomas

            if (\defined('GOTEO_ADMIN_NOCACHE')) {
                $nocache = true;
            } else {
                $nocache = false;
            }
            
            $nocache = true;

            // si hay mas de un argumento, hay que meter el resto con
            $args = \func_get_args();
            if (count($args) > 1) {
                array_shift($args);
            } else {
                $args = array();
            }

			// buscamos el texto en cache
			static $_cache = array();
			if (!$nocache && isset($_cache[$id][$lang]) && empty($args)) {
				return $_cache[$id][$lang];
            }
            
			// buscamos el texto en la tabla
			$query = Model::query("SELECT text FROM text WHERE id = :id AND lang = :lang", array(':id' => $id, ':lang' => $lang));
			$exist = $query->fetchObject();
			if ($exist->text) {
                $tmptxt = $_cache[$id][$lang] = $exist->text;

                //contamos cuantos argumentos necesita el texto
                $req_args = \substr_count($exist->text, '%');

                if (!empty($args) && $req_args > 0 && count($args) >= $req_args) {
                    $texto = $nocache ? vsprintf($exist->text, $args) : vsprintf($tmptxt, $args);
                } else {
                    $texto = $nocache ? $exist->text : $tmptxt;
                }

			} else {
                // si tenemos purpose, devolvemos eso
                $texto = self::getPurpose($id);

                if (strcmp($texto, $id) === 0) {
                // sino, lo metemos en la tabla y en purpose
                    Model::query("REPLACE INTO text (id, lang, text) VALUES (:id, :lang, :text)", array(':id' => $id, ':lang' => $lang, ':text' => $id));
                    Model::query("REPLACE INTO purpose (text, purpose) VALUES (:text, :purpose)", array(':text' => $id, ':purpose' => "Texto $id"));
                }
			}

            return $texto;
		}

		static public function getPurpose ($id) {
			// buscamos la explicación del texto en la tabla
			$query = Model::query("SELECT purpose FROM purpose WHERE text = :id", array(':id' => $id));
			$exist = $query->fetchObject();
			if ($exist->purpose) {
				return $exist->purpose;
			} else {
				Model::query("REPLACE INTO purpose (text, purpose) VALUES (:text, :purpose)", array(':text' => $id, ':purpose' => "Texto $id"));
				return $id;
			}
		}

        /*
         * Si un texto esta marcado como html devuelve true, si no está marcado así, false
         * Se marca en la tabla de propósitos ya que en la tabla texts habría que marcarlo en cada idioma
         */
		static public function isHtml ($id) {
            try
            {
                // lo miramos en la tabla de propósitos
                $query = Model::query("SELECT html FROM purpose WHERE text = :id", array(':id' => $id));
                $purpose = $query->fetchObject();
                if ($purpose->html == 1)
                    return true;
                else
                    return false;
            } catch (\PDOException $e) {
                return false; // Si la tabla purpose no tiene el campo html
            }
		}


		/*
		 *  Metodo para la lista de textos segun idioma
		 */
		public static function getAll($filter = null) {
            $texts = array();

            $values = array(':lang'=>\GOTEO_DEFAULT_LANG);

            $sql = "SELECT id, text FROM text WHERE lang = :lang";
            if (!empty($filter)) {
                $sql .= " AND id LIKE :filter";
                $values[':filter'] = "%$filter%";
            }
            $sql .= " ORDER BY id ASC";
            
            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $text) {
                    $text->purpose = self::getPurpose($text->id);
                    $texts[] = $text;
                }
                return $texts;
            } catch (\PDOException $e) {
                throw new Exception($e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

		/*
		 *  Esto se usara para la gestión de traducciones
		 */
		public static function save($data, &$errors = array()) {
			if (!is_array($data) ||
				empty($data['id']) ||
				empty($data['text']) ||
				empty($data['lang'])) {
					return false;
			}

			if (Model::query("UPDATE text SET text = :text WHERE id = :id AND lang = :lang", array(':text' => $data['text'], ':id' => $data['id'], ':lang' => $data['lang']))) {
				return true;
			}
			else {
				$errors[] = 'Error al insertar los datos <pre>' . print_r($data, 1) . '</pre>';
				return false;
			}


		}

        /*
         * Filtros de textos
         */
        static public function filters()
        {
            return array(
                'mandatory'=>'Campos obligatorios',
                'tooltip'=>'Consejos para rellenar el formulario de proyecto',
                'error-register'=>'Errores al registrarse',
                'explain'=>'Explicaciones',
                'guide-project'=>'Guias del formulario de proyecto',
                'guide-user'=>'Guias del formulario de usuario',
                'step'=>'Pasos del formulario',
                'validate'=>'Validaciones de campos'
            );
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