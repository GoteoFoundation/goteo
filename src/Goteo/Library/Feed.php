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
use Goteo\Model\Blog\Post;
use Goteo\Library\Text;
use Goteo\Library\FeedBody;
use Goteo\Model\Mail;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;

/*
 * Clase para loguear eventos
 */
class Feed {

    public
        $id,
        $title, // titulo entrada o nombre usuario
        $url = null, // enlace del titulo
        $image = null, // enlace del titulo
        $scope = 'admin', // ambito del evento (public, admin, private)
        $type =  'system', // tipo de evento  ($public_types , $admin_types, $private_types)
        $timeago, // el hace tanto
        $date, // fecha y hora del evento
        $html, // contenido del evento en codigo html
        $unique = false, // si es un evento unique, no lo grabamos si ya hay un evento con esa url
        $unique_issue = false, // si se encuentra con que esta duplicando el feed
        $text,  // id del texto dinamico
        $params,  // (array serializado en bd) parametros para el texto dinamico
        $target_type, // tipo de objetivo del evento (user, project, call, node, etc..) normalmente project
        $target_id, // id registro del objetivo (normalmente varchar(50))
        $post = null; // id entrada de blog relacionada

    static public $admin_types = array(
        'all' => array(
            'label' => 'Todo',
            'color' => 'light-blue'
        ),
        'admin' => array(
            'label' => 'Administrador',
            'color' => 'red'
        ),
        'user' => array(
            'label' => 'Usuario',
            'color' => 'blue'
        ),
        'project' => array(
            'label' => 'Proyecto',
            'color' => 'light-blue'
        ),
        'call' => array(
            'label' => 'Convocatoria',
            'color' => 'light-blue'
        ),
        'money' => array(
            'label' => 'Transferencias',
            'color' => 'violet'
        ),
        'system' => array(
            'label' => 'Sistema',
            'color' => 'grey'
        )
    );

    static public $public_types = array(
        'goteo' => array(
            'label' => 'Goteo'
        ),
        'projects' => array(
            'label' => 'Proyectos'
        ),
        'community' => array(
            'label' => 'Comunidad'
        )
    );

    static public $private_types = array(
        'info' => array(
            'label' => 'Información'
        ),
        'alert' => array(
            'label' => 'Alerta'
        )
    );

    /**
     * Construct function will compatibilize old behaviour
     * (where text where just in the original language)
     * to more flexible one.
     * If html var is an serialized array it will be treated as a Text::lang() compatible arguments
     * array('text-identifier', 'argument1', 'argument2', ...)
     */
    public function __construct() {
        // Convert array to text translation
        $this->title = Text::get($this->title);
        $this->html = $this->getBody(); // for compatibility
    }
    /**
     * Metodo que rellena instancia
     */
    public function populate($title, $url, $html, $image = null) {
        $this->title = $title;
        $this->url = $url;
        $this->setBody($html);
        $this->image = ($image instanceof Image) ? $image->id : $image;
        return $this;
    }

    public function getBody () {
        $html = @unserialize($this->html);

        if($html instanceOf FeedBody) {
            return $html->render();
        }
        return $this->html;
    }

    // establece el html relacionado
    public function setBody ($html) {
        if($html instanceOf FeedBody) {
            $this->html = serialize($html);
        }
        else {
            $this->html = $html;
        }
        return $this;
    }
    public function setUnique($unique) {
        $this->unique = (bool) $unique;
        return $this;
    }

    /**
     * Metodo que establece el elemento al que afecta el evento
     *
     * Sufridor del evento: tipo (tabla) & id registro
     *
     * @param $id string normalmente varchar(50)
     * @param $type string (project, user, node, call, etc...)
     */
    public function setTarget ($id, $type = 'project') {
        $this->target_id = $id;
        $this->target_type = $type;
        return $this;
    }

    // establece el post relacionado
    public function setPost ($post) {
        $this->post = $post;
        return $this;
    }

    public function doAdmin ($type = 'system') {
        $this->doEvent('admin', $type);
        return $this;
    }

    public function doPublic ($type = 'goteo') {
        $this->doEvent('public', $type);
        return $this;
    }

    public function doPrivate ($type = 'info') {
        $this->doEvent('private', $type);
        return $this;
    }

    private function doEvent ($scope = 'admin', $type = 'system') {
        $this->scope = $scope;
        $this->type = $type;
        $this->add();
        return $this;
    }

    /**
	 *  Metodo para sacar los eventos
     *
     * @param string $type  tipo de evento (public: columnas goteo, proyectos, comunidad;  admin: categorias de filtro)
     * @param string $scope ambito de eventos (public | admin)
     * @param numeric $limit limite de elementos
     * @return array list of items
	 */
	public static function getAll($type = 'all', $scope = 'public', $limit = '99', $node = null) {
        $lang = Lang::current();

        $list = array();

        try {
            $values = array(':scope' => $scope, ':lang' => $lang);

            $sqlType = '';
            if ($type != 'all') {
                $sqlType = " AND feed.type = :type";
                $values[':type'] = $type;
            } else {
                // acciones del web service ultra secreto
                $sqlType = " AND feed.type != 'usws'";
            }

            $sqlNode = '';
            if (!empty($node) && $node != \GOTEO_NODE) {
                /* segun el objetivo del feed sea:
                 * proyectos del nodo
                 * usuarios del nodo
                 * convocatorias destacadas por el nodo (aunque inactivas)
                 * el propio nodo
                 * el blog
                 */
                $sqlNode = " AND (
                    (feed.target_type = 'project' AND feed.target_id IN (
                        SELECT id FROM project WHERE node = :node
                        )
                    )
                    OR (feed.target_type = 'user' AND feed.target_id IN (
                        SELECT id FROM user WHERE node = :node
                        )
                    )
                    OR (feed.target_type = 'call' AND feed.target_id IN (
                        SELECT `call` FROM campaign WHERE node = :node
                        )
                    )
                    OR (feed.target_type = 'node' AND feed.target_id = :node)
                    OR (feed.target_type = 'blog')
                )";
                // @FIXME : Cambiar los subselects anteriores por un join optimizdo para 3 tablas
                // $sqlInnerNode = " INNER JOIN ";
                $values[':node'] = $node;
            }


            if(\Goteo\Core\Model::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(post_lang.title, post.title) as post_title,
                                IFNULL(post_lang.text, post.text) as post_text";
            }
            else {
                $different_select=" IFNULL(post_lang.title, IFNULL(eng.title, post.title)) as post_title,
                                    IFNULL(post_lang.text, IFNULL(eng.text, post.text)) as post_text";
                $eng_join=" LEFT JOIN post_lang as eng
                                ON  eng.id = post.id
                                AND eng.lang = 'en'";
            }


            // todas las entradas de feed que tengan valor post cargan los datos del post, blog, traducción
            $sql = "SELECT
                        feed.id as id,
                        feed.title as title,
                        feed.url as url,

                        DATE_FORMAT(feed.datetime, '%H:%i %d|%m|%Y') as date,
                        feed.datetime as timer,
                        feed.html as html,
                        feed.target_type as target_type,
                        feed.target_id as target_id,
                        feed.type as columna,

                        feed.image as image,


                        feed.post as post_id,
                        blog.type as post_owner_type,
                        blog.owner as post_owner_id,
                        post.date as post_date,
                        post.image as post_image,

                        node.id as node_id,

                        $different_select
                    FROM feed
                    LEFT JOIN post
                        ON post.id = feed.post
                    LEFT JOIN blog
                        ON blog.id = post.blog
                    LEFT JOIN node
                        ON node.id = blog.owner
                        AND blog.type = 'node'
                        AND blog.owner != '".\GOTEO_NODE."'
                        AND node.active = 1
                    LEFT JOIN post_lang
                        ON post_lang.id = post.id
                        AND post_lang.lang = :lang
                    $eng_join
                    WHERE feed.scope = :scope
                    $sqlType
                    $sqlNode
                    ORDER BY datetime DESC
                    LIMIT $limit
                    ";

            // die (\sqldbg($sql, $values));

            $query = Model::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                // si es una entrada de post , vamos a cambiar el html por el del post traducido
                if (!empty($item->post_id)) {
                    // primero sacamos la id del post de la url
                    $matches = array();

                    \preg_match('(\d+)', $item->url, $matches);
                    if (!empty($matches[0])) {

                        // solo posts de nodos activos
                        if ($item->post_owner_type == 'node'
                            && $item->post_owner_id != \GOTEO_NODE
                            && empty($item->node_id)) {
                            continue;
                        } elseif ($item->post_owner_type == 'node') {
                            // y substituimos el $item->html por el $post->html solo para entradas de nodo
                            $item->html = Text::recorta($item->post_text, 250);
                        }

                        $item->title = $item->post_title;

                        // arreglo la fecha de publicación
                        $parts = explode(' ', $item->timer);
                        $item->timer = $item->post_date . ' ' . $parts[1];
                    }
                }

                //hace tanto
                $item->timeago = self::time_ago($item->timer);

                // IMAGEN
                // si es una entrada de blog o novedades, cogemos la imagen de esa entrada
                if ( isset($item->post_id) && !empty($item->post_image) && $item->post_image != 'empty' ) {
                    $item->image = Image::get($item->post_image);
                } elseif ( !empty($item->image) && $item->image != 'empty' ) {
                    $item->image = Image::get($item->image);
                } else {
                    $item->image = Image::get(1);
                }



                $list[] = $item;
            }
            return $list;
        } catch (\PDOException $e) {
            throw new \Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, true) . "</pre>");
        }
	}

    /**
	 *  Metodo para sacar los eventos de novedades de proyecto (solo)
     *
     * @param string $limit limite de elementos
     * @return array list of items
	 */
	public static function getProjUpdates($limit = '99') {

        $list = array();

        try {
            $sql = "SELECT
                        feed.id as id,
                        feed.title as title,
                        feed.url as url,
                        feed.image as image,
                        DATE_FORMAT(feed.datetime, '%H:%i %d|%m|%Y') as date,
                        feed.datetime as timer,
                        feed.html as html
                    FROM feed
                    WHERE feed.scope = 'public'
                    AND feed.type = 'projects'
                    AND feed.url LIKE '%updates%'
                    ORDER BY datetime DESC
                    LIMIT $limit
                    ";

            $query = Model::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                //hace tanto
                $item->timeago = self::time_ago($item->timer);

                $list[] = $item;
            }
            return $list;
        } catch (\PDOException $e) {
            throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, true) . "</pre>");
        }
	}

    /**
	 * Metodo para sacar los eventos relacionados con un usuario
     *
     * @param string $id id del usuario
     * @param string $filter  para tipos de eventos que queremos obtener
     * @return array list of items (como getAll)
	 */
	public static function getUserItems($id, $filter = 'private') {

        $list = array();

        try {
            $values = array();

            $wheres = array();
            if (!empty($filter)) {
                switch ($filter) {
                    case 'private':
                        // eventos que afecten al usuario
                        $wheres[] = "feed.target_type = 'user'";
                        $wheres[] = "feed.target_id = :target_id";
                        $values[':target_id'] = $id;
                        break;
                    case 'supported':
                        // eventos del proyectos que cofinancio (o he intentado cofinanciar)
                        $wheres[] = "feed.target_type = 'project'";
                        $wheres[] = "feed.target_id IN (
                            SELECT DISTINCT(invest.project) FROM invest WHERE invest.user  = :id
                            )";
                        $values[':id'] = $id;
                        break;
                    case 'comented':
                        // eventos de proyectos en los que comento pero que no cofinancio
                        $wheres[] = "feed.target_type = 'project'";
                        $wheres[] = "( feed.target_id IN (
                            SELECT DISTINCT(message.project) FROM message WHERE message.user  = :id
                            ) OR feed.target_id IN (
                            SELECT DISTINCT(blog.owner)
                            FROM comment
                            INNER JOIN post
                                ON post.id = comment.post
                            INNER JOIN blog
                                ON blog.id = post.blog
                                AND blog.type = 'project'
                            WHERE comment.user  = :id
                            )
                        )";
                        $wheres[] = "feed.target_id NOT IN (
                            SELECT DISTINCT(invest.project) FROM invest WHERE invest.user  = :id
                            )";
                        $values[':id'] = $id;
                        break;
                }
            }

            $sql = "SELECT
                        feed.id as id,
                        feed.title as title,
                        feed.url as url,
                        feed.image as image,
                        DATE_FORMAT(feed.datetime, '%H:%i %d|%m|%Y') as date,
                        feed.datetime as timer,
                        feed.html as html
                    FROM feed
                    WHERE " . implode(' AND ', $wheres) . "
                    ORDER BY datetime DESC
                    LIMIT 99
                    ";

            $query = Model::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                //hace tanto
                $item->timeago = self::time_ago($item->timer);

                $list[] = $item;
            }
            return $list;
        } catch (\PDOException $e) {
            App::getService('logger')->err('ERROR SQL en Feed::getItems [' . $e->getMessage() . "] SQL: " . \sqldbg($sql, $values));
            return array();
        }
    }

    /**
     *  Metodo para grabar eventos
     *
     *  Los datos del evento estan en el objeto
     *
     *
     * @param array $errors
     *
     * @access public
     * @return boolean true | false   as success
     *
     */
    public function add() {
        if (empty($this->html)) {
            App::getService('logger')->err('Empty content while adding feed FEED: ' . print_r($this, true));
            return false;
        }
        // TODO: Restricción UNIQUE en BD?
        // primero, verificar si es unique, no duplicarlo
        if ($this->unique === true) {
            $query = Model::query("SELECT id FROM feed WHERE title = :title AND html = :html AND url = :url AND scope = :scope AND type = :type",
                array(
                ':title' => $this->title,
                ':html' => $this->html,
                ':url' => $this->url,
                ':scope' => $this->scope,
                ':type' => $this->type
            ));
            if (($id = $query->fetchColumn(0)) != false) {
                $this->id = $id;
                $this->unique_issue = true;
                return true;
            }
        }

		try {
            $values = array(
                ':title' => $this->title,
                ':url' => $this->url,
                ':image' => $this->image,
                ':scope' => $this->scope,
                ':type' => $this->type,
                ':html' => $this->html,
                ':target_type' => $this->target_type,
                ':target_id' => $this->target_id,
                ':post' => $this->post
            );

			$sql = "INSERT INTO feed
                        (id, title, url, scope, type, html, image, target_type, target_id, post)
                    VALUES
                        ('', :title, :url, :scope, :type, :html, :image, :target_type, :target_id, :post)
                    ";

            // die(\sqldbg($sql, $values));
            if (Model::query($sql, $values)) {
                return true;
            } else {
                App::getService('logger')->err('Failed feed insert. SQL: ' . \sqldbg($sql, $values));
                return false;
            }

        } catch(\PDOException $e) {
            App::getService('logger')->err('Exception on feed insert [' . $e->getMessage() . '] SQL: ' . \sqldbg($sql, $values));
            return false;
		}

	}

    /**
     * Metodo para transformar un TIMESTAMP en un "hace tanto"
     *
     * Los periodos vienen de un texto tipo singular-plural_sg-pl_id-sg-pl_...
     * en mismo orden y cantidad que los per_id
     */
    public static function time_ago($date,$granularity=1) {

        $per_id = array('sec', 'min', 'hour', 'day', 'week', 'month', 'year', 'dec');

        $per_txt = array();
        foreach (\explode('_', Text::get('feed-timeago-periods')) as $key=>$grptxt) {
            $per_txt[$per_id[$key]] = \explode('-', $grptxt);
        }

        $justnow = Text::get('feed-timeago-justnow');

        $retval = '';
        $date = strtotime($date);
        $ahora = time();
        $difference = $ahora - $date;
        $periods = array('dec' => 315360000,
            'year' => 31536000,
            'month' => 2628000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
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

    /**
     *  Genera codigo html para enlace o texto dentro de feed
     *
     */
    public static function item ($type = 'system', $label = 'label', $id = null) {
        return FeedBody::item($type, $label, $id);
    }

    /**
     *  Genera codigo html para feed público
     *
     *  segun tenga imagen, ebnlace, titulo, tipo de enlace
     *
     */
    public static function subItem ($item) {

        $pub_timeago = Text::get('feed-timeago-published', $item->timeago);

        $content = '<div class="subitem">';

       // si enlace -> título como texto del enlace
       if (!empty($item->url)) {
            // si imagen -> segun enlace:
            if ($item->image && $item->image instanceof \Goteo\Model\Image) {

                if (substr($item->url, 0, 5) == '/user') {
                    $content .= '<div class="content-avatar">
                    <a href="'.$item->url.'" class="avatar"><img height="32" width="32" src="'.$item->image->getLink(32, 32, true).'" /></a>
                    <a href="'.$item->url.'" class="username">'.$item->title.'</a>
                    <span class="datepub">'.$pub_timeago.'</span>
                    </div>';
                } else {
                    $content .= '<div class="content-image">
                    <a href="'.$item->url.'" class="image"><img src="'.$item->image->getLink(90, 60, true).'" /></a>
                    <a href="'.$item->url.'" class="project light-blue">'.$item->title.'</a>
                    <span class="datepub">'.$pub_timeago.'</span>
                    </div>';
                }
            } else {
                // solo titulo con enlace
                $content .= '<div class="content-title">
                    <h5 class="light-blue"><a href="'.$item->url.'" class="project light-blue">'.$item->title.'</a></h5>
                    <span class="datepub">'.$pub_timeago.'</span>
               </div>';
            }
       } else {
           // solo el timeago
           $content .= '<span class="datepub">'.$pub_timeago.'</span>';
       }

       // y lo que venga en el html
       $content .= '<div class="content-pub">'.$item->html.'</div>';

       $content .= '</div>';

       return $content;
    }

}
