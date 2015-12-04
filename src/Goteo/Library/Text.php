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
            $sql="SELECT text.*,purpose.group FROM text
            LEFT JOIN purpose ON purpose.text=text.id
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

        foreach(Lang::groups() as $group => $files) {
            if (!empty($filters['group']) && $filters['group'] != $group) {
                continue;
            }
            foreach($langs_order as $l) {
                if(!isset($files[$l])) continue;
                $file = $files[$l];
                // echo "[$l $file]\n";
                $catalogue = Yaml::parse(file_get_contents($file));
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

        $sql = "REPLACE `text` SET
                        `text` = :text,
                        id = :id,
                        lang = :lang
                ";
        if (Model::query($sql, array(':text' => $data['text'], ':id' => $data['id'], ':lang' => $data['lang']))) {
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
        $sql = "SELECT ".implode(', ', $fields)." FROM {$table}{$sqlFilter}";
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
                $code = '<iframe frameborder="0" height="480px" src="'.$url.'" width="250px" scrolling="no"></iframe>';
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
         *  Last updated: 2012-09-06
         */
        $validTlds = array_fill_keys(explode(" ", ".ac .ad .ae .aero .af .ag .ai .al .am .an .ao .aq .ar .arpa .as .asia .at .au .aw .ax .az .ba .bb .bd .be .bf .bg .bh .bi .biz .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cat .cc .cd .cf .cg .ch .ci .ck .cl .cm .cn .co .com .coop .cr .cu .cv .cw .cx .cy .cz .de .dj .dk .dm .do .dz .ec .edu .ee .eg .er .es .et .eu .fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gov .gp .gq .gr .gs .gt .gu .gw .gy .hk .hm .hn .hr .ht .hu .id .ie .il .im .in .info .int .io .iq .ir .is .it .je .jm .jo .jobs .jp .ke .kg .kh .ki .km .kn .kp .kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mil .mk .ml .mm .mn .mo .mobi .mp .mq .mr .ms .mt .mu .museum .mv .mw .mx .my .mz .na .name .nc .ne .net .nf .ng .ni .nl .no .np .nr .nu .nz .om .org .pa .pe .pf .pg .ph .pk .pl .pm .pn .post .pr .pro .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si .sj .sk .sl .sm .sn .so .sr .st .su .sv .sx .sy .sz .tc .td .tel .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .travel .tt .tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .xn--0zwm56d .xn--11b5bs3a9aj6g .xn--3e0b707e .xn--45brj9c .xn--80akhbyknj4f .xn--80ao21a .xn--90a3ac .xn--9t4b11yi5a .xn--clchc0ea0b2g2a9gcd .xn--deba0ad .xn--fiqs8s .xn--fiqz9s .xn--fpcrj9c3d .xn--fzc2c9e2c .xn--g6w251d .xn--gecrj9c .xn--h2brj9c .xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--j6w193g .xn--jxalpdlp .xn--kgbechtv .xn--kprw13d .xn--kpry57d .xn--lgbbat1ad8j .xn--mgb9awbf .xn--mgbaam7a8h .xn--mgbayh7gpa .xn--mgbbh1a71e .xn--mgbc0a9azcg .xn--mgberp4a5d4ar .xn--o3cw4h .xn--ogbpf8fl .xn--p1ai .xn--pgbs0dh .xn--s9brj9c .xn--wgbh1c .xn--wgbl6a .xn--xkc2al3hye2a .xn--xkc2dl3a5ee0h .xn--yfro4i67o .xn--ygbi2ammx .xn--zckzah .xxx .ye .yt .za .zm .zw"), true);


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

                $linkHtml = '<a href="' . htmlspecialchars($completeUrl) . '">'
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

