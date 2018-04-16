<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library;

use Goteo\Core\Model;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Symfony\Component\Yaml\Yaml;

/*
 * Clase para sacar textos dinámicos de la tabla text
 *  @TODO, definir donde se define y se cambia la constante LANG y utilizarla en los _::get_
 */
class Text {
    static protected $errors = array();

    /**
     * Compatibility text translation getter (instead of Lang::trans())
     * @param  string $id key to translate (will be searched in all group translations)
     * @return string     [description]
     */
    static public function get ($id, $args = null) {
        // Compatibilize with random number of arguments included
        if(!is_array($args)) {
            $args = func_get_args();
            if (count($args) > 1) {
                array_shift($args);
            } else {
                $args = array();
            }
        }
        return static::lang($id, Lang::current(), $args);
    }

    /**
     * like get, but using system lang (or passed)
     */
    static public function sys($id, $args = null) {
        // Compatibilize with random number of arguments included
        if(!is_array($args)) {
            $args = func_get_args();
            if (count($args) > 1) {
                array_shift($args);
            } else {
                $args = array();
            }
        }
        return static::lang($id, Config::get('lang'), $args);
    }

    /**
     * Gets the required text.
     * If text does not exists a fallback will be returned
     * @param  [type] $id   [description]
     * @param  [type] $lang [description]
     * @param  array  $vars [description]
     * @return [type]       [description]
     */
    static public function lang($id, $lang = null, array $vars = []) {
        if(!$lang) {
            $lang = Lang::current();
        }
        // Get from Symfony tranlator files
        if(\is_assoc($vars)) {
            // use Symfony auto parametres replacing
            $text = Lang::trans($id, $vars, $lang);
        }
        else {
            $text = Lang::trans($id, [], $lang);
            // old behaviour, assume sprintf like arguments
            // $text = vsprintf($text, $vars);
            $req_args = substr_count($text, '%') - 2*substr_count($text, '%%');

            if (!empty($vars) && $req_args > 0 && count($vars) >= $req_args) {
                $text = vsprintf($text, $vars);
            }


        }

        // \Goteo\Application\App::getService('logger')->debug("Lang: [$id => $text]");
        if(\Goteo\Application\App::debug()) {
            $all = Lang::translator()->getCatalogue()->all('messages');

            if(empty($all[$id])) {
                // for toolbar debuggin
                self::$errors[$id] = 'Not found lang [' . $lang . '] translated as [' . $text . ']';
            }
        }
        return $text;

    }

    /**
     * Devuelve un texto en HTML
     * @deprecated
     */
    static public function html ($id) {
        // sacamos el contenido del texto
        $text = call_user_func_array ( 'static::get' , func_get_args() );
        if (self::isHtml($id))
            return $text; // el texto ES html, lo devuelve tal cual
        else
            return htmlspecialchars ($text); // el texto NO es html, lo pasa por html especial chars
    }

    /*
     * Devuelve un texto sin HTML
     */
    static public function plain ($id) {
        // sacamos el contenido del texto
        $text = call_user_func_array ( 'static::get' , \func_get_args() );

        return \strip_tags($text) ; // ES html, le quitamos los tags
    }

    /*
     * Devuelve un texto con comillas escapadas para usarlo en javascript
     */
    static public function slash ($id) {
        // sacamos el contenido del texto
        $text = call_user_func_array ( 'static::get' , \func_get_args() );
        return \addslashes(trim(strip_tags(nl2br($text))));
    }

    /**
     * @deprecated
     * here to manually track old texts marked as html
     */
    static public function isHtml ($id) {
        $ids = ['invest-paypal_disabled',
            'dashboard-rewards-notice',
            'main-banner-header',
            'node-footer-about',
            'faq-payment-method',
            'feed-new_call-opened',
            'feed-new_call-published',
            'fatal-error-teapot',
            'discover-calls-header',
            'feed-new_project',
            'user-login-required',
            'fatal-error-node',
            'login-banner-header',
            'feed-project_fail',
            'feed-project_goon',
            'feed-project_finish',
            'feed-messages-new_thread',
            'feed-message_support-response',
            'feed-messages-response',
            'feed-updates-comment',
            'feed-blog-comment',
            'dashboard-translate-doing_project',
            'dashboard-translate-doing_call',
            'dashboard-translate-doing_node',
            'project-rewards-individual_reward-units_left',
            'call-project-got_explain',
            'invest-called-allready',
            'invest-called-maxproj',
            'invest-called-maxdrop',
            'invest-called-rest',
            'invest-called-nodrop',
            'currency-alert',
            'call-splash-invest_explain',
            'call-splash-drop_limit',
            'feed-new_support',
            'feed-new_update',
            'open-banner-header'];
        return in_array($id, $ids);
    }


    /*
     *  Metodo para la lista de textos segun idioma
     */
    public static function getAll($filters = array(), $lang = null) {
        $sqls = $texts = $skip = [];

        // Obtain SQL texts first
        if (empty($filters['filesonly'])) {
            $sql="SELECT text.* FROM text
             WHERE text.lang = :lang
             ORDER BY text.id ASC";
            $values = array(':lang' => $lang);
            $query = Model::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $text) {
                $sqls[$text->id] = $text;
            }
        }
        // print_r($sqls);
        // Add files translations from groups
        $langs_order = [$lang];
        $l = Lang::getFallback($lang);
        while(!in_array($l, $langs_order)) {
            $langs_order[] = $l;
            $l = Lang::getFallback($lang);
        }
        // print_r($langs_order);

        foreach(Lang::groups() as $group => $files) {
            if (!empty($filters['group']) && $filters['group'] != $group) {
                continue;
            }
            foreach($langs_order as $l) {
                if(!isset($files[$l])) continue;
                $file = $files[$l];
                // echo "[$l $file]\n";
                $catalogue = Yaml::parse(file_get_contents($file));
                if(!$catalogue) continue;
                ksort($catalogue);
                foreach($catalogue as $i => $t) {
                    if(in_array($i, $skip)) continue;
                    $pending = false;
                    if($lang != $l) $pending = true;
                    // replace from sql source if exists
                    if(isset($sqls[$i])) {
                        $t = $sqls[$i]->text;
                        $pending = $sqls[$i]->pending;
                    }
                    elseif (!empty($filters['sqlonly'])) {
                        continue;
                    }

                    if(isset($texts[$i])) continue;
                    // apply filters
                    if (!empty($filters['strict']) && ($l != $lang && !isset($sqls[$i]))) {
                        continue;
                    }
                    if (!empty($filters['pending']) && !$pending) {
                        $skip[] = $i;
                        continue;
                    }

                    if (!empty($filters['idfilter']) && stripos($i, $filters['idfilter']) === false) {
                        continue;
                    }
                    if (!empty($filters['text']) && stripos($t, $filters['text']) === false) {
                        continue;
                    }

                    $texts[$i] = new \stdClass;
                    $texts[$i]->id = $i;
                    $texts[$i]->lang = $l;
                    $texts[$i]->group = $group;
                    $texts[$i]->pendiente = $pending;
                    $texts[$i]->pending = $pending;
                    $texts[$i]->text = $t;
                }
            }

            // add sql missing ids
            foreach($sqls as $id => $ob) {
                if((!array_key_exists($id, $texts) || $texts[$id]->lang != $ob->lang) && $ob->group == $group) {
                    // echo "$id\n";print_r($ob);
                    $texts[$id] = $ob;
                    $texts[$id]->pendiente = $ob->pending;
                }
                // else {
                //     // echo "[$id {$ob->group}]\n";
                //     if(array_key_exists($id, $texts)) print_r($texts[$id]);
                // }
            }
        }

        return $texts;
    }

    /*
     *  Esto se usa para traducciones
     */
    public static function save($data, &$errors = array()) {
        if (!is_array($data) ||
            empty($data['id']) ||
            empty($data['text']) ||
            empty($data['lang'])) {
                return false;
        }

        $bind = array(':text' => $data['text'], ':id' => $data['id'], ':lang' => $data['lang']);
        $sql = "REPLACE `text` SET
                        `text` = :text,
                        id = :id,
                        lang = :lang";
        if(isset($data['pending'])) {
            $sql .= ", pending = :pending";
            $bind[':pending'] = (bool) $data['pending'];
        }
        if (Model::query($sql, $bind)) {
            return true;
        } else {
            $errors[] = 'Error al insertar los datos <pre>' . print_r($data, true) . '</pre>';
            return false;
        }
    }

    /*
     *  Esto se usa para marcar todas las traducciones de un texto como pendientes
     */
    public static function setPending($id, &$errors = array()) {
        $sql = "UPDATE `text` SET
                        `pending` = 1
                        WHERE `id` = :id
                ";
        try {
            Model::query($sql, array(':id' => $id));
            return true;

        } catch (\Exception $e) {
            $errors[] = 'Error al marcar traducción pendiente. ' . $e->getMessage();
            return false;
        }
    }

    /*
     *  Delete id
     */
    public static function delete($id, $lang) {
        $sql = "DELETE FROM `text` WHERE `id` = :id AND `lang` = :lang";
        Model::query($sql, array(':id' => $id, ':lang' => $lang));
    }

    /*
     * Grupos de textos
     */
    static public function groups()
    {
        $groups = [];
        foreach(Lang::groups() as $group => $files) {
            $groups[$group] = Lang::trans("text-group-$group");
        }
        asort($groups);
        return $groups;
    }

    /*
     * Devuelve el número de palabras de una cadena
     */
    static public function wcount ($string) {
        // contar palabras (ojo! hay que quitar los tags html)
        return count(explode(' ', \strip_tags($string)));
    }

    /*
     * Devuelve el número de palabras del contenido recibido
     */
    static public function wordCount ($section, $table, $fields = array(), &$total = 0 ) {

        $count = 0;
        $sqlFilter = '';

        switch ($section) {
            case 'texts':
                // get original texts
                $texts = self::getAll(['group' => $table]);
                foreach($texts as $t) {
                    $count += static::wcount($t->text);
                }
                return $count;
            case 'pages':
                // table nos indica si es la de descripciones o la de contenido,
                //  en la de contenido hay que filtrar nodo goteo y español
                if ($table == 'page_node') {
                    $sqlFilter = " WHERE node = 'goteo' AND lang = 'es'";
                }
                break;
            case 'contents':
            case 'home':
                // ojo! post es solo del blog 1 (goteo)
                if ($table == 'post') {
                    $sqlFilter = " WHERE blog = '1'";
                }
                break;
        }

        // seleccionar toda la tabla,
        if(!Model::query("SHOW TABLES LIKE '$table'")->fetchAll()) {
            return null;
        }

        $sql = "SELECT `".implode('`, `', $fields)."` FROM `$table`$sqlFilter";
        $query = Model::query($sql, $values);
        foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            // para cada campo
            foreach ($fields as $field) {
                $count += static::wcount($row[$field]);
            }
        }

        $total += $count;

        return $count;
    }


    /*
     * Devuelve el código embed de un widget de proyecto
     */
    static public function widget ($url, $type = 'project', $styles = null) {

        $style = (isset($styles)) ? ' style="'.$styles.'"' : '';

        switch ($type) {
            case 'fb':
                $code = '<div class="fb-like" data-href="'.$url.'" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>';
                break;
            case 'fb-nocount':
                $code = '<div class="fb-like"'.$style.' data-href="'.$url.'" data-send="false" data-layout="box_count" data-width="0" data-height="0" data-show-faces="false"></div>';
                break;
            case 'wof':
                $code = '<iframe frameborder="0" height="100%" src="'.$url.'" width="630px" scrolling="no"></iframe>';
                break;
            case 'project':
            default:
                $code = '<iframe frameborder="0" height="492px" src="'.$url.'" width="300px" scrolling="no"></iframe>';
                break;
        }

        return $code;
    }

    /*
     * Devuelve array de urls para compartir en redes sociales
     */
    static public function shareLinks ($url, $title, $user_twitter = null) {

        $author_twitter = str_replace(
                    array(
                        'https://',
                        'http://',
                        'www.',
                        'twitter.com/',
                        '#!/',
                        '@'
                    ), '', $user_twitter);
        $author = !empty($author_twitter) ? ' '.self::get('regular-by').' @'.$author_twitter.' ' : '';

        $urls = array(
            'twitter' => 'http://twitter.com/home?status=' . rawurlencode($title . $author.': ' . $url . ' #Goteo'),
            'facebook' => 'http://facebook.com/sharer.php?u=' . rawurlencode($url . '&t=' . rawurlencode($title))
        );

        return $urls;
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

    /**
     * Removes non-ascii characters from a string
     */
    static public function normalize($text) {
        // Acentos
        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y',
            'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', 'ª' => 'a', 'º' => 'o', 'ẃ' => 'w', 'Ẃ' => 'Ẃ', 'ẁ' => 'w', 'Ẁ' => 'Ẃ', '€' => 'eur',
            'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ś' => 's', 'Ś' => 'S', 'ẅ' => 'w', 'Ẅ' => 'W',
            '!' => '', '¡' => '', '?' => '', '¿' => '', '@' => '', '^' => '', '|' => '', '#' => '', '~' => '',
            '%' => '', '$' => '', '*' => '', '+' => '', '`' => "'", '´' => "'", '’' => '', '”' => '"', '“' => '"',
        );

        return strtr($text, $table);
    }
    /*
     *   Método para formatear friendly un texto para ponerlo en la url
     */
    static public function urliza($texto)
    {
        $texto = trim(strtolower($texto));
        // Acentos
//          $texto = strtr($texto, "ÁÀÄÂáàâäÉÈËÊéèêëÍÌÏÎíìîïÓÒÖÔóòôöÚÙÛÜúùûüÇçÑñ", "aaaaaaaaeeeeeeeeiiiiiiiioooooooouuuuuuuuccnn");
        $table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
        );

        $texto = strtr($texto, $table);
        // Separadores
        $texto = preg_replace("/[\s\,\;\_\/\-]+/i", "-", $texto);
        $texto = preg_replace("/[^a-z0-9\.\-\+]/", "", $texto);
        return $texto;
    }

    /*
     *   Método para recortar un texto
     */
    static public function recorta ($texto, $longitud = 100, $puntos = '...')  {
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
     *  Author: Søren Løvborg
     *
     *  To the extent possible under law, Søren Løvborg has waived all copyright
     *  and related or neighboring rights to UrlLinker.
     *  http://creativecommons.org/publicdomain/zero/1.0/
     * -------------------------------------------------------------------------------
     */
    static public function urlink($text)
    {
        /*
         *  Regular expression bits used by htmlEscapeAndLinkUrls() to match URLs.
         */
        $rexScheme    = 'https?://';
        // $rexScheme    = "$rexScheme|ftp://"; // Uncomment this line to allow FTP addresses.
        $rexDomain    = '(?:[-a-zA-Z0-9]{1,63}\.)+[a-zA-Z][-a-zA-Z0-9]{1,62}';
        $rexIp        = '(?:[1-9][0-9]{0,2}\.|0\.){3}(?:[1-9][0-9]{0,2}|0)';
        $rexPort      = '(:[0-9]{1,5})?';
        $rexPath      = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
        $rexQuery     = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexFragment  = '(#[!$-/0-9?:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexUsername  = '[^]\\\\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64}';
        $rexPassword  = $rexUsername; // allow the same characters as in the username
        $rexUrl       = "($rexScheme)?(?:($rexUsername)(:$rexPassword)?@)?($rexDomain|$rexIp)($rexPort$rexPath$rexQuery$rexFragment)";
        $rexTrailPunct= "[)'?.!,;:]"; // valid URL characters which are not part of the URL if they appear at the very end
        $rexNonUrl    = "[^-_#$+.!*'(),;/?:@=&a-zA-Z0-9]"; // characters that should never appear in a URL
        $rexUrlLinker = "{\\b$rexUrl(?=$rexTrailPunct*($rexNonUrl|$))}";
        // $rexUrlLinker .= 'i'; // Uncomment this line to allow uppercase URL schemes (e.g. "HTTP://google.com").

        /**
         *  $validTlds is an associative array mapping valid TLDs to the value true.
         *  Since the set of valid TLDs is not static, this array should be updated
         *  from time to time.
         *
         *  List source:  http://data.iana.org/TLD/tlds-alpha-by-domain.txt
         *  Last updated: 2016-09-13
         */
        $validTlds = array_fill_keys(array('.aaa', '.aarp', '.abarth', '.abb', '.abbott', '.abbvie', '.abc', '.able', '.abogado', '.abudhabi', '.ac', '.academy', '.accenture', '.accountant', '.accountants', '.aco', '.active', '.actor', '.ad', '.adac', '.ads', '.adult', '.ae', '.aeg', '.aero', '.aetna', '.af', '.afamilycompany', '.afl', '.ag', '.agakhan', '.agency', '.ai', '.aig', '.aigo', '.airbus', '.airforce', '.airtel', '.akdn', '.al', '.alfaromeo', '.alibaba', '.alipay', '.allfinanz', '.allstate', '.ally', '.alsace', '.alstom', '.am', '.americanexpress', '.americanfamily', '.amex', '.amfam', '.amica', '.amsterdam', '.analytics', '.android', '.anquan', '.anz', '.ao', '.apartments', '.app', '.apple', '.aq', '.aquarelle', '.ar', '.aramco', '.archi', '.army', '.arpa', '.art', '.arte', '.as', '.asda', '.asia', '.associates', '.at', '.athleta', '.attorney', '.au', '.auction', '.audi', '.audible', '.audio', '.auspost', '.author', '.auto', '.autos', '.avianca', '.aw', '.aws', '.ax', '.axa', '.az', '.azure', '.ba', '.baby', '.baidu', '.banamex', '.bananarepublic', '.band', '.bank', '.bar', '.barcelona', '.barclaycard', '.barclays', '.barefoot', '.bargains', '.bauhaus', '.bayern', '.bb', '.bbc', '.bbt', '.bbva', '.bcg', '.bcn', '.bd', '.be', '.beats', '.beauty', '.beer', '.bentley', '.berlin', '.best', '.bestbuy', '.bet', '.bf', '.bg', '.bh', '.bharti', '.bi', '.bible', '.bid', '.bike', '.bing', '.bingo', '.bio', '.biz', '.bj', '.black', '.blackfriday', '.blanco', '.blockbuster', '.blog', '.bloomberg', '.blue', '.bm', '.bms', '.bmw', '.bn', '.bnl', '.bnpparibas', '.bo', '.boats', '.boehringer', '.bofa', '.bom', '.bond', '.boo', '.book', '.booking', '.boots', '.bosch', '.bostik', '.bot', '.boutique', '.br', '.bradesco', '.bridgestone', '.broadway', '.broker', '.brother', '.brussels', '.bs', '.bt', '.budapest', '.bugatti', '.build', '.builders', '.business', '.buy', '.buzz', '.bv', '.bw', '.by', '.bz', '.bzh', '.ca', '.cab', '.cafe', '.cal', '.call', '.calvinklein', '.cam', '.camera', '.camp', '.cancerresearch', '.canon', '.capetown', '.capital', '.capitalone', '.car', '.caravan', '.cards', '.care', '.career', '.careers', '.cars', '.cartier', '.casa', '.cash', '.casino', '.cat', '.catering', '.cba', '.cbn', '.cbre', '.cbs', '.cc', '.cd', '.ceb', '.center', '.ceo', '.cern', '.cf', '.cfa', '.cfd', '.cg', '.ch', '.chanel', '.channel', '.chase', '.chat', '.cheap', '.chintai', '.chloe', '.christmas', '.chrome', '.chrysler', '.church', '.ci', '.cipriani', '.circle', '.cisco', '.citadel', '.citi', '.citic', '.city', '.cityeats', '.ck', '.cl', '.claims', '.cleaning', '.click', '.clinic', '.clinique', '.clothing', '.cloud', '.club', '.clubmed', '.cm', '.cn', '.co', '.coach', '.codes', '.coffee', '.college', '.cologne', '.com', '.comcast', '.commbank', '.community', '.company', '.compare', '.computer', '.comsec', '.condos', '.construction', '.consulting', '.contact', '.contractors', '.cooking', '.cookingchannel', '.cool', '.coop', '.corsica', '.country', '.coupon', '.coupons', '.courses', '.cr', '.credit', '.creditcard', '.creditunion', '.cricket', '.crown', '.crs', '.cruises', '.csc', '.cu', '.cuisinella', '.cv', '.cw', '.cx', '.cy', '.cymru', '.cyou', '.cz', '.dabur', '.dad', '.dance', '.date', '.dating', '.datsun', '.day', '.dclk', '.dds', '.de', '.deal', '.dealer', '.deals', '.degree', '.delivery', '.dell', '.deloitte', '.delta', '.democrat', '.dental', '.dentist', '.desi', '.design', '.dev', '.dhl', '.diamonds', '.diet', '.digital', '.direct', '.directory', '.discount', '.discover', '.dish', '.diy', '.dj', '.dk', '.dm', '.dnp', '.do', '.docs', '.doctor', '.dodge', '.dog', '.doha', '.domains', '.dot', '.download', '.drive', '.dtv', '.dubai', '.duck', '.dunlop', '.duns', '.dupont', '.durban', '.dvag', '.dz', '.earth', '.eat', '.ec', '.eco', '.edeka', '.edu', '.education', '.ee', '.eg', '.email', '.emerck', '.energy', '.engineer', '.engineering', '.enterprises', '.epost', '.epson', '.equipment', '.er', '.ericsson', '.erni', '.es', '.esq', '.estate', '.esurance', '.et', '.eu', '.eurovision', '.eus', '.events', '.everbank', '.exchange', '.expert', '.exposed', '.express', '.extraspace', '.fage', '.fail', '.fairwinds', '.faith', '.family', '.fan', '.fans', '.farm', '.farmers', '.fashion', '.fast', '.fedex', '.feedback', '.ferrari', '.ferrero', '.fi', '.fiat', '.fidelity', '.film', '.final', '.finance', '.financial', '.fire', '.firestone', '.firmdale', '.fish', '.fishing', '.fit', '.fitness', '.fj', '.fk', '.flickr', '.flights', '.flir', '.florist', '.flowers', '.fly', '.fm', '.fo', '.foo', '.foodnetwork', '.football', '.ford', '.forex', '.forsale', '.forum', '.foundation', '.fox', '.fr', '.fresenius', '.frl', '.frogans', '.frontdoor', '.frontier', '.ftr', '.fujitsu', '.fujixerox', '.fund', '.furniture', '.futbol', '.fyi', '.ga', '.gal', '.gallery', '.gallo', '.gallup', '.game', '.games', '.gap', '.garden', '.gb', '.gbiz', '.gd', '.gdn', '.ge', '.gea', '.gent', '.genting', '.george', '.gf', '.gg', '.ggee', '.gh', '.gi', '.gift', '.gifts', '.gives', '.giving', '.gl', '.glade', '.glass', '.gle', '.global', '.globo', '.gm', '.gmail', '.gmbh', '.gmo', '.gmx', '.gn', '.godaddy', '.gold', '.goldpoint', '.golf', '.goo', '.goodhands', '.goodyear', '.goog', '.google', '.gop', '.got', '.gov', '.gp', '.gq', '.gr', '.grainger', '.graphics', '.gratis', '.green', '.gripe', '.group', '.gs', '.gt', '.gu', '.guardian', '.gucci', '.guge', '.guide', '.guitars', '.guru', '.gw', '.gy', '.hamburg', '.hangout', '.haus', '.hbo', '.hdfc', '.hdfcbank', '.health', '.healthcare', '.help', '.helsinki', '.here', '.hermes', '.hgtv', '.hiphop', '.hisamitsu', '.hitachi', '.hiv', '.hk', '.hkt', '.hm', '.hn', '.hockey', '.holdings', '.holiday', '.homedepot', '.homegoods', '.homes', '.homesense', '.honda', '.honeywell', '.horse', '.host', '.hosting', '.hot', '.hoteles', '.hotmail', '.house', '.how', '.hr', '.hsbc', '.ht', '.htc', '.hu', '.hughes', '.hyatt', '.hyundai', '.ibm', '.icbc', '.ice', '.icu', '.id', '.ie', '.ieee', '.ifm', '.iinet', '.ikano', '.il', '.im', '.imamat', '.imdb', '.immo', '.immobilien', '.in', '.industries', '.infiniti', '.info', '.ing', '.ink', '.institute', '.insurance', '.insure', '.int', '.intel', '.international', '.intuit', '.investments', '.io', '.ipiranga', '.iq', '.ir', '.irish', '.is', '.iselect', '.ismaili', '.ist', '.istanbul', '.it', '.itau', '.itv', '.iwc', '.jaguar', '.java', '.jcb', '.jcp', '.je', '.jeep', '.jetzt', '.jewelry', '.jlc', '.jll', '.jm', '.jmp', '.jnj', '.jo', '.jobs', '.joburg', '.jot', '.joy', '.jp', '.jpmorgan', '.jprs', '.juegos', '.juniper', '.kaufen', '.kddi', '.ke', '.kerryhotels', '.kerrylogistics', '.kerryproperties', '.kfh', '.kg', '.kh', '.ki', '.kia', '.kim', '.kinder', '.kindle', '.kitchen', '.kiwi', '.km', '.kn', '.koeln', '.komatsu', '.kosher', '.kp', '.kpmg', '.kpn', '.kr', '.krd', '.kred', '.kuokgroup', '.kw', '.ky', '.kyoto', '.kz', '.la', '.lacaixa', '.ladbrokes', '.lamborghini', '.lamer', '.lancaster', '.lancia', '.lancome', '.land', '.landrover', '.lanxess', '.lasalle', '.lat', '.latino', '.latrobe', '.law', '.lawyer', '.lb', '.lc', '.lds', '.lease', '.leclerc', '.lefrak', '.legal', '.lego', '.lexus', '.lgbt', '.li', '.liaison', '.lidl', '.life', '.lifeinsurance', '.lifestyle', '.lighting', '.like', '.lilly', '.limited', '.limo', '.lincoln', '.linde', '.link', '.lipsy', '.live', '.living', '.lixil', '.lk', '.loan', '.loans', '.locker', '.locus', '.loft', '.lol', '.london', '.lotte', '.lotto', '.love', '.lpl', '.lplfinancial', '.lr', '.ls', '.lt', '.ltd', '.ltda', '.lu', '.lundbeck', '.lupin', '.luxe', '.luxury', '.lv', '.ly', '.ma', '.macys', '.madrid', '.maif', '.maison', '.makeup', '.man', '.management', '.mango', '.market', '.marketing', '.markets', '.marriott', '.marshalls', '.maserati', '.mattel', '.mba', '.mc', '.mcd', '.mcdonalds', '.mckinsey', '.md', '.me', '.med', '.media', '.meet', '.melbourne', '.meme', '.memorial', '.men', '.menu', '.meo', '.metlife', '.mg', '.mh', '.miami', '.microsoft', '.mil', '.mini', '.mint', '.mit', '.mitsubishi', '.mk', '.ml', '.mlb', '.mls', '.mm', '.mma', '.mn', '.mo', '.mobi', '.mobily', '.moda', '.moe', '.moi', '.mom', '.monash', '.money', '.montblanc', '.mopar', '.mormon', '.mortgage', '.moscow', '.motorcycles', '.mov', '.movie', '.movistar', '.mp', '.mq', '.mr', '.ms', '.msd', '.mt', '.mtn', '.mtpc', '.mtr', '.mu', '.museum', '.mutual', '.mutuelle', '.mv', '.mw', '.mx', '.my', '.mz', '.na', '.nab', '.nadex', '.nagoya', '.name', '.nationwide', '.natura', '.navy', '.nba', '.nc', '.ne', '.nec', '.net', '.netbank', '.netflix', '.network', '.neustar', '.new', '.news', '.next', '.nextdirect', '.nexus', '.nf', '.nfl', '.ng', '.ngo', '.nhk', '.ni', '.nico', '.nike', '.nikon', '.ninja', '.nissan', '.nissay', '.nl', '.no', '.nokia', '.northwesternmutual', '.norton', '.now', '.nowruz', '.nowtv', '.np', '.nr', '.nra', '.nrw', '.ntt', '.nu', '.nyc', '.nz', '.obi', '.off', '.office', '.okinawa', '.olayan', '.olayangroup', '.oldnavy', '.ollo', '.om', '.omega', '.one', '.ong', '.onl', '.online', '.onyourside', '.ooo', '.open', '.oracle', '.orange', '.org', '.organic', '.orientexpress', '.origins', '.osaka', '.otsuka', '.ott', '.ovh', '.pa', '.page', '.pamperedchef', '.panasonic', '.panerai', '.paris', '.pars', '.partners', '.parts', '.party', '.passagens', '.pay', '.pccw', '.pe', '.pet', '.pf', '.pfizer', '.pg', '.ph', '.pharmacy', '.philips', '.photo', '.photography', '.photos', '.physio', '.piaget', '.pics', '.pictet', '.pictures', '.pid', '.pin', '.ping', '.pink', '.pioneer', '.pizza', '.pk', '.pl', '.place', '.play', '.playstation', '.plumbing', '.plus', '.pm', '.pn', '.pnc', '.pohl', '.poker', '.politie', '.porn', '.post', '.pr', '.pramerica', '.praxi', '.press', '.prime', '.pro', '.prod', '.productions', '.prof', '.progressive', '.promo', '.properties', '.property', '.protection', '.pru', '.prudential', '.ps', '.pt', '.pub', '.pw', '.pwc', '.py', '.qa', '.qpon', '.quebec', '.quest', '.qvc', '.racing', '.raid', '.re', '.read', '.realestate', '.realtor', '.realty', '.recipes', '.red', '.redstone', '.redumbrella', '.rehab', '.reise', '.reisen', '.reit', '.ren', '.rent', '.rentals', '.repair', '.report', '.republican', '.rest', '.restaurant', '.review', '.reviews', '.rexroth', '.rich', '.richardli', '.ricoh', '.rightathome', '.rio', '.rip', '.ro', '.rocher', '.rocks', '.rodeo', '.room', '.rs', '.rsvp', '.ru', '.ruhr', '.run', '.rw', '.rwe', '.ryukyu', '.sa', '.saarland', '.safe', '.safety', '.sakura', '.sale', '.salon', '.samsclub', '.samsung', '.sandvik', '.sandvikcoromant', '.sanofi', '.sap', '.sapo', '.sarl', '.sas', '.save', '.saxo', '.sb', '.sbi', '.sbs', '.sc', '.sca', '.scb', '.schaeffler', '.schmidt', '.scholarships', '.school', '.schule', '.schwarz', '.science', '.scjohnson', '.scor', '.scot', '.sd', '.se', '.seat', '.secure', '.security', '.seek', '.select', '.sener', '.services', '.ses', '.seven', '.sew', '.sex', '.sexy', '.sfr', '.sg', '.sh', '.shangrila', '.sharp', '.shaw', '.shell', '.shia', '.shiksha', '.shoes', '.shop', '.shopping', '.shouji', '.show', '.showtime', '.shriram', '.si', '.silk', '.sina', '.singles', '.site', '.sj', '.sk', '.ski', '.skin', '.sky', '.skype', '.sl', '.sling', '.sm', '.smart', '.smile', '.sn', '.sncf', '.so', '.soccer', '.social', '.softbank', '.software', '.sohu', '.solar', '.solutions', '.song', '.sony', '.soy', '.space', '.spiegel', '.spot', '.spreadbetting', '.sr', '.srl', '.srt', '.st', '.stada', '.staples', '.star', '.starhub', '.statebank', '.statefarm', '.statoil', '.stc', '.stcgroup', '.stockholm', '.storage', '.store', '.stream', '.studio', '.study', '.style', '.su', '.sucks', '.supplies', '.supply', '.support', '.surf', '.surgery', '.suzuki', '.sv', '.swatch', '.swiftcover', '.swiss', '.sx', '.sy', '.sydney', '.symantec', '.systems', '.sz', '.tab', '.taipei', '.talk', '.taobao', '.target', '.tatamotors', '.tatar', '.tattoo', '.tax', '.taxi', '.tc', '.tci', '.td', '.tdk', '.team', '.tech', '.technology', '.tel', '.telecity', '.telefonica', '.temasek', '.tennis', '.teva', '.tf', '.tg', '.th', '.thd', '.theater', '.theatre', '.tiaa', '.tickets', '.tienda', '.tiffany', '.tips', '.tires', '.tirol', '.tj', '.tjmaxx', '.tjx', '.tk', '.tkmaxx', '.tl', '.tm', '.tmall', '.tn', '.to', '.today', '.tokyo', '.tools', '.top', '.toray', '.toshiba', '.total', '.tours', '.town', '.toyota', '.toys', '.tr', '.trade', '.trading', '.training', '.travel', '.travelchannel', '.travelers', '.travelersinsurance', '.trust', '.trv', '.tt', '.tube', '.tui', '.tunes', '.tushu', '.tv', '.tvs', '.tw', '.tz', '.ua', '.ubank', '.ubs', '.uconnect', '.ug', '.uk', '.unicom', '.university', '.uno', '.uol', '.ups', '.us', '.uy', '.uz', '.va', '.vacations', '.vana', '.vanguard', '.vc', '.ve', '.vegas', '.ventures', '.verisign', '.versicherung', '.vet', '.vg', '.vi', '.viajes', '.video', '.vig', '.viking', '.villas', '.vin', '.vip', '.virgin', '.visa', '.vision', '.vista', '.vistaprint', '.viva', '.vivo', '.vlaanderen', '.vn', '.vodka', '.volkswagen', '.vote', '.voting', '.voto', '.voyage', '.vu', '.vuelos', '.wales', '.walmart', '.walter', '.wang', '.wanggou', '.warman', '.watch', '.watches', '.weather', '.weatherchannel', '.webcam', '.weber', '.website', '.wed', '.wedding', '.weibo', '.weir', '.wf', '.whoswho', '.wien', '.wiki', '.williamhill', '.win', '.windows', '.wine', '.winners', '.wme', '.wolterskluwer', '.woodside', '.work', '.works', '.world', '.ws', '.wtc', '.wtf', '.xbox', '.xerox', '.xfinity', '.xihuan', '.xin', '.xn--11b4c3d', '.xn--1ck2e1b', '.xn--1qqw23a', '.xn--30rr7y', '.xn--3bst00m', '.xn--3ds443g', '.xn--3e0b707e', '.xn--3oq18vl8pn36a', '.xn--3pxu8k', '.xn--42c2d9a', '.xn--45brj9c', '.xn--45q11c', '.xn--4gbrim', '.xn--55qw42g', '.xn--55qx5d', '.xn--5su34j936bgsg', '.xn--5tzm5g', '.xn--6frz82g', '.xn--6qq986b3xl', '.xn--80adxhks', '.xn--80ao21a', '.xn--80asehdb', '.xn--80aswg', '.xn--8y0a063a', '.xn--90a3ac', '.xn--90ae', '.xn--90ais', '.xn--9dbq2a', '.xn--9et52u', '.xn--9krt00a', '.xn--b4w605ferd', '.xn--bck1b9a5dre4c', '.xn--c1avg', '.xn--c2br7g', '.xn--cck2b3b', '.xn--cg4bki', '.xn--clchc0ea0b2g2a9gcd', '.xn--czr694b', '.xn--czrs0t', '.xn--czru2d', '.xn--d1acj3b', '.xn--d1alf', '.xn--e1a4c', '.xn--eckvdtc9d', '.xn--efvy88h', '.xn--estv75g', '.xn--fct429k', '.xn--fhbei', '.xn--fiq228c5hs', '.xn--fiq64b', '.xn--fiqs8s', '.xn--fiqz9s', '.xn--fjq720a', '.xn--flw351e', '.xn--fpcrj9c3d', '.xn--fzc2c9e2c', '.xn--fzys8d69uvgm', '.xn--g2xx48c', '.xn--gckr3f0f', '.xn--gecrj9c', '.xn--h2brj9c', '.xn--hxt814e', '.xn--i1b6b1a6a2e', '.xn--imr513n', '.xn--io0a7i', '.xn--j1aef', '.xn--j1amh', '.xn--j6w193g', '.xn--jlq61u9w7b', '.xn--jvr189m', '.xn--kcrx77d1x4a', '.xn--kprw13d', '.xn--kpry57d', '.xn--kpu716f', '.xn--kput3i', '.xn--l1acc', '.xn--lgbbat1ad8j', '.xn--mgb9awbf', '.xn--mgba3a3ejt', '.xn--mgba3a4f16a', '.xn--mgba7c0bbn0a', '.xn--mgbaam7a8h', '.xn--mgbab2bd', '.xn--mgbayh7gpa', '.xn--mgbb9fbpob', '.xn--mgbbh1a71e', '.xn--mgbc0a9azcg', '.xn--mgbca7dzdo', '.xn--mgberp4a5d4ar', '.xn--mgbpl2fh', '.xn--mgbt3dhd', '.xn--mgbtx2b', '.xn--mgbx4cd0ab', '.xn--mix891f', '.xn--mk1bu44c', '.xn--mxtq1m', '.xn--ngbc5azd', '.xn--ngbe9e0a', '.xn--node', '.xn--nqv7f', '.xn--nqv7fs00ema', '.xn--nyqy26a', '.xn--o3cw4h', '.xn--ogbpf8fl', '.xn--p1acf', '.xn--p1ai', '.xn--pbt977c', '.xn--pgbs0dh', '.xn--pssy2u', '.xn--q9jyb4c', '.xn--qcka1pmc', '.xn--qxam', '.xn--rhqv96g', '.xn--rovu88b', '.xn--s9brj9c', '.xn--ses554g', '.xn--t60b56a', '.xn--tckwe', '.xn--unup4y', '.xn--vermgensberater-ctb', '.xn--vermgensberatung-pwb', '.xn--vhquv', '.xn--vuq861b', '.xn--w4r85el8fhu5dnra', '.xn--w4rs40l', '.xn--wgbh1c', '.xn--wgbl6a', '.xn--xhq521b', '.xn--xkc2al3hye2a', '.xn--xkc2dl3a5ee0h', '.xn--y9a3aq', '.xn--yfro4i67o', '.xn--ygbi2ammx', '.xn--zfr164b', '.xperia', '.xxx', '.xyz', '.yachts', '.yahoo', '.yamaxun', '.yandex', '.ye', '.yodobashi', '.yoga', '.yokohama', '.you', '.youtube', '.yt', '.yun', '.za', '.zappos', '.zara', '.zero', '.zip', '.zippo', '.zm', '.zone', '.zuerich', '.zw'), true);
        /**
         *  Transforms plain text into valid HTML, escaping special characters and
         *  turning URLs into links.
         */
        $html = '';

        $position = 0;
        while (preg_match($rexUrlLinker, $text, $match, PREG_OFFSET_CAPTURE, $position))
        {
            list($url, $urlPosition) = $match[0];

            // Add the text leading up to the URL.
            $html .= htmlspecialchars(substr($text, $position, $urlPosition - $position));

            $scheme      = $match[1][0];
            $username    = $match[2][0];
            $password    = $match[3][0];
            $domain      = $match[4][0];
            $afterDomain = $match[5][0]; // everything following the domain
            $port        = $match[6][0];
            $path        = $match[7][0];

            // Check that the TLD is valid or that $domain is an IP address.
            $tld = strtolower(strrchr($domain, '.'));
            if (preg_match('{^\.[0-9]{1,3}$}', $tld) || isset($validTlds[$tld]))
            {
                // Do not permit implicit scheme if a password is specified, as
                // this causes too many errors (e.g. "my email:foo@example.org").
                if (!$scheme && $password)
                {
                    $html .= htmlspecialchars($username);

                    // Continue text parsing at the ':' following the "username".
                    $position = $urlPosition + strlen($username);
                    continue;
                }

                if (!$scheme && $username && !$password && !$afterDomain)
                {
                    // Looks like an email address.
                    $completeUrl = "mailto:$url";
                    $linkText = $url;
                }
                else
                {
                    // Prepend http:// if no scheme is specified
                    $completeUrl = $scheme ? $url : "http://$url";
                    $linkText = "$domain$port$path";
                }

                $linkHtml = '<a target="_blank" href="' . htmlspecialchars($completeUrl) . '">'
                    . htmlspecialchars($linkText)
                    . '</a>';

                // Cheap e-mail obfuscation to trick the dumbest mail harvesters.
                $linkHtml = str_replace('@', '&#64;', $linkHtml);

                // Add the hyperlink.
                $html .= $linkHtml;
            }
            else
            {
                // Not a valid URL.
                $html .= htmlspecialchars($url);
            }

            // Continue text parsing from after the URL.
            $position = $urlPosition + strlen($url);
        }

        // Add the remainder of the text.
        $html .= htmlspecialchars(substr($text, $position));
        return $html;

    }

    /*
     * Método para ocultar parámetros de una url
     */
    public static function cutUrlParams($url) {
        return $url = preg_replace('#/.+#', '', preg_replace('#http|s?://#', '', $url));
    }

     /*
     * Método para eliminar etiquetas script, iframe, form y objet.
     */
    public static function tags_filter($text) {

        $paterns = array ('#<script(.*?)</script>#i', '#<iframe(.*?)</iframe>#i',
                          '#<embed(.*?)</embed>#i', '#<form(.*?)</form>#i');
        $sus = array ('', '', '','','');
        $text = preg_replace($paterns,$sus,$text);

        return $text;
    }


    static public function getErrors() {
        return self::$errors;
    }


}

