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
            $text = call_user_func_array ( 'Text::get' , \func_get_args() );
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
			$query = Model::query("SELECT `text` FROM text WHERE id = :id AND lang = :lang", array(':id' => $id, ':lang' => $lang));
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
                    Model::query("REPLACE INTO text (id, lang, `text`) VALUES (:id, :lang, :text)", array(':id' => $id, ':lang' => $lang, ':text' => $id));
                    Model::query("REPLACE INTO purpose (text, purpose) VALUES (:text, :purpose)", array(':text' => $id, ':purpose' => "Texto $id"));
                }
			}

            $text = nl2br($text);
            // apaño temporal para los magic quotes
            $text = \str_replace('\\', '', $text);

            return $texto;
		}

		static public function getPurpose ($id) {
			// buscamos la explicación del texto en la tabla
			$query = Model::query("SELECT purpose FROM purpose WHERE `text` = :id", array(':id' => $id));
			$exist = $query->fetchObject();
			if (!empty($exist->purpose)) {
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
		public static function getAll($filters = array()) {
            $texts = array();

            $values = array();

            $sql = "SELECT
                        purpose.text as id,
                        IFNULL(text.text,purpose.purpose) as text,
                        purpose.`group` as `group`
                    FROM purpose
                    LEFT JOIN text
                        ON text.id = purpose.text
                    WHERE purpose.text != ''
                    ";
            if (!empty($filters['idfilter'])) {
                $sql .= " AND purpose.text LIKE :idfilter";
                $values[':idfilter'] = "%{$filters['idfilter']}%";
            }
            if (!empty($filters['group'])) {
                $sql .= " AND purpose.`group` = :group";
                $values[':group'] = "{$filters['group']}";
            }
            if (!empty($filters['text'])) {
                $sql .= " AND ( text.text LIKE :text OR (text.text IS NULL AND purpose.purpose LIKE :text ))";
                $values[':text'] = "%{$filters['text']}%";
            }
            $sql .= " ORDER BY purpose.`group` ASC";
            
            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $text) {
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

            $sql = "REPLACE `text` SET
                            `text` = :text,
                            id = :id,
                            lang = :lang
                    ";
			if (Model::query($sql, array(':text' => $data['text'], ':id' => $data['id'], ':lang' => $data['lang']))) {
				return true;
			} else {
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
                'mandatory'     => 'Campos obligatorios',
                'tooltip'       => 'Consejos para rellenar el formulario de proyecto',
                'error-register'=> 'Errores al registrarse',
                'explain'       => 'Explicaciones',
                'guide-project' => 'Guias del formulario de proyecto',
                'guide-user'    => 'Guias del formulario de usuario',
                'step'          => 'Pasos del formulario',
                'validate'      => 'Validaciones de campos'
            );
        }

        /*
         * Grupos de textos
         */
        static public function groups()
        {
            return array(
                'profile'  => 'Perfil del usuario',
                'personal' => 'Datos personales del usuario',
                'overview' => 'Descripción del proyecto',
                'costs'    => 'Costes del proyecto',
                'rewards'  => 'Retornos y recompensas del proyecto',
                'supports' => 'Colaboraciones del proyecto',
                'preview'  => 'Previsualización del proyecto',
                'dashboard'=> 'Dashboard del usuario',
                'register' => 'Registro de usuarios',
                'login'    => 'Pagina de login',
                'public_profile' => 'Pagina de perfil de usuario',
                'project'  => 'Proyecto, pública y formulario',
                'general'  => 'Propósito general'
            );
        }


		/*
		 *   Pone el enlace a gmaps segun localidad
         * @TODO , ponerle el LANG
		 */
		static public function GmapsLink($location)
		{
			$texto = '<a href="http://maps.google.es/maps?q='.htmlspecialchars(rawurlencode($location)).'&hl=es" target="_blank">'.htmlspecialchars($location).'</a>';
			return $texto;
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
			$ultima = self::ultima_palabra($texto,$separadores );
			while ($texto != "" && (in_array($ultima,$palabras_vacias) || strlen($ultima)<=2) || ($html && $ultima{1} == "<" && substr($ultima,-1) == ">")) {
				$texto = substr($texto,0,strlen($texto)-strlen($ultima));
				while ($texto != "" && in_array(substr($texto,-1),$separadores)){
					$texto = substr($texto, 0, -1);
				}
				$ultima = self::ultima_palabra($texto,$separadores);
			}

			// Hemos cortado una etiqueta html?
			if ($html && strrpos($texto,"<") > strrpos($texto,">")) {
				$texto = substr($texto,0,strrpos($texto,"<"));
			}
			// Si el texto era html, cerramos las etiquetas
			if ($html) $texto = self::cerrar_etiquetas($texto);
			if ($puntos !== false) $texto .= $puntos;
			return $texto;
		}

        static public function ultima_palabra ($texto, $separadores = false) {
            $palabra = '';
            if ($separadores === false) $separadores = array(" ", ".", ",", ";");
            $i = strlen($texto) - 1;
            while ($i >= 0 && (!in_array(substr($texto,$i,1), $separadores))) {
                $palabra = substr($texto,$i,1).$palabra;
                $i--;
            }
            return $palabra;
        }

        static public function cerrar_etiquetas ($html) {
            // Ponemos todos los tags abiertos en un array
            preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $html, $res);
            $abiertas = $res[1];

            // Ponemos todos los tags cerrados en un array
            preg_match_all("#</([a-z]+)>#iU", $html, $res);
            $cerradas = $res[1];

            // Obtenemos el array de etiquetas no cerradas

            if (count($cerradas) == count($abiertas)) {
                // *Suponemos* que todas las etiquetas están cerradas
                return $html;
            }

            $abiertas = array_reverse($abiertas);

            // Cerramos
            for ($i = 0;$i < count($abiertas);$i++) {
                if (!in_array($abiertas[$i],$cerradas)){
                    $html .= "</".$abiertas[$i].">";
                } else {
                    unset($cerradas[array_search($abiertas[$i],$cerradas)]);
                }
            }
            return $html;
        }


		/*
		 *   Método para aplicar saltos de linea y poner links en las url
         *   ¿¡Como se puede ser tan guay!?
         *   http://www.kwi.dk/projects/php/UrlLinker/
         * -------------------------------------------------------------------------------
         *  UrlLinker - facilitates turning plaintext URLs into HTML links.
         *
         *  Author: SÃ¸ren LÃ¸vborg
         *
         *  To the extent possible under law, SÃ¸ren LÃ¸vborg has waived all copyright
         *  and related or neighboring rights to UrlLinker.
         *  http://creativecommons.org/publicdomain/zero/1.0/
         * -------------------------------------------------------------------------------
		 */
		static public function urlink($text)
		{
            /*
             *  Regular expression bits used by htmlEscapeAndLinkUrls() to match URLs.
             */
            $rexProtocol = '(https?://)?';
            $rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
            $rexPort     = '(:[0-9]{1,5})?';
            $rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
            $rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
            $rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
            $rexUrlLinker = "{\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))}";

            /**
             *  $validTlds is an associative array mapping valid TLDs to the value true.
             *  Since the set of valid TLDs is not static, this array should be updated
             *  from time to time.
             *
             *  List source:  http://data.iana.org/TLD/tlds-alpha-by-domain.txt
             *  Last updated: 2010-09-04
             */
            $validTlds = array_fill_keys(explode(" ", ".ac .ad .ae .aero .af .ag .ai .al .am .an .ao .aq .ar .arpa .as .asia .at .au .aw .ax .az .ba .bb .bd .be .bf .bg .bh .bi .biz .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cat .cc .cd .cf .cg .ch .ci .ck .cl .cm .cn .co .com .coop .cr .cu .cv .cx .cy .cz .de .dj .dk .dm .do .dz .ec .edu .ee .eg .er .es .et .eu .fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gov .gp .gq .gr .gs .gt .gu .gw .gy .hk .hm .hn .hr .ht .hu .id .ie .il .im .in .info .int .io .iq .ir .is .it .je .jm .jo .jobs .jp .ke .kg .kh .ki .km .kn .kp .kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mil .mk .ml .mm .mn .mo .mobi .mp .mq .mr .ms .mt .mu .museum .mv .mw .mx .my .mz .na .name .nc .ne .net .nf .ng .ni .nl .no .np .nr .nu .nz .om .org .pa .pe .pf .pg .ph .pk .pl .pm .pn .pr .pro .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si .sj .sk .sl .sm .sn .so .sr .st .su .sv .sy .sz .tc .td .tel .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .travel .tt .tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .xn--0zwm56d .xn--11b5bs3a9aj6g .xn--80akhbyknj4f .xn--9t4b11yi5a .xn--deba0ad .xn--fiqs8s .xn--fiqz9s .xn--fzc2c9e2c .xn--g6w251d .xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--j6w193g .xn--jxalpdlp .xn--kgbechtv .xn--kprw13d .xn--kpry57d .xn--mgbaam7a8h .xn--mgbayh7gpa .xn--mgberp4a5d4ar .xn--o3cw4h .xn--p1ai .xn--pgbs0dh .xn--wgbh1c .xn--xkc2al3hye2a .xn--ygbi2ammx .xn--zckzah .ye .yt .za .zm .zw"), true);

            /**
             *  Transforms plain text into valid HTML, escaping special characters and
             *  turning URLs into links.
             */
            $result = "";

            $position = 0;
            while (preg_match($rexUrlLinker, $text, $match, PREG_OFFSET_CAPTURE, $position))
            {
                list($url, $urlPosition) = $match[0];

                // Add the text leading up to the URL.
                $result .= htmlspecialchars(substr($text, $position, $urlPosition - $position));

                $domain = $match[2][0];
                $port   = $match[3][0];
                $path   = $match[4][0];

                // Check that the TLD is valid or that $domain is an IP address.
                $tld = strtolower(strrchr($domain, '.'));
                if (preg_match('{^\.[0-9]{1,3}$}', $tld) || isset($validTlds[$tld]))
                {
                    // Prepend http:// if no protocol specified
                    $completeUrl = $match[1][0] ? $url : "http://$url";

                    // Add the hyperlink.
                    $result .= '<a href="' . htmlspecialchars($completeUrl) . '" target="_blank">'
                        . htmlspecialchars("$domain$port$path")
                        . '</a>';
                }
                else
                {
                    // Not a valid URL.
                    $result .= htmlspecialchars($url);
                }

                // Continue text parsing from after the URL.
                $position = $urlPosition + strlen($url);
            }

            // Add the remainder of the text.
            $result .= htmlspecialchars(substr($text, $position));
            return $result;


		}

	}
    
}