<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Model;

use Goteo\Core\Model;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Core\Exception;

/*
 * Clase para gestionar el contenido de las páginas institucionales
 */
class Page extends Model{

    public
        $id,
        $lang,
        $name,
        $description,
        $url,
        $content,
        $type;

    static public function get ($id, $lang = null) {

        //Obtenemos el idioma de soporte
        $lang = self::default_lang_by_id($id, 'page_lang', $lang);

        $query = static::query("
            SELECT
                page.id as id,
                IFNULL(page_lang.name, page.name) as name,
                IFNULL(page_lang.description, page.description) as description,
                IFNULL(page_lang.content, page.content) as content,
                page.url as `url`,
                page.type as `type`
            FROM    page
            LEFT JOIN page_lang
                ON  page_lang.id = page.id
                AND page_lang.lang = :lang
            WHERE page.id = :id
            ", array(':id' => $id, ':lang'=>$lang));

        $page = $query->fetchObject(__CLASS__);

        if (!$page instanceof \Goteo\Model\Page) {
            // Create mock page
            $page = new self(['id' => $id, 'name' => "Page [$id]", 'description' => "Missing page [$id]", "content" => "Please create the page [$id] with proper content!"]);
        }

        return $page;
	}

	/*
	 *  Metodo para la lista de páginas
	 */
	public static function getAll($filters = array(), $lang = null) {

        $lang = Lang::current();
        $list = array();

        if(self::default_lang($lang) === Config::get('lang')) {
            $different_select=" IFNULL(page_lang.name, page.name) as name,
                                IFNULL(page_lang.description, page.description) as `description`,
                                IFNULL(page_lang.content, page.content) as `content`";
        } else {
            $different_select=" IFNULL(page_lang.name, IFNULL(eng.name, page.name)) as name,
                                IFNULL(page_lang.description, IFNULL(eng.description, page.description)) as `description`,
                                IFNULL(page_lang.content, IFNULL(eng.content, page.content)) as `content`";
            $eng_join=" LEFT JOIN page_lang as eng
                            ON  eng.id = page.id
                            AND eng.lang = 'en'";
        }

        if (!empty($filters['text'])) {
            $sqlFilters .= " AND ( page.name LIKE :text
                OR  page.description LIKE :text
                OR  page.content LIKE :text)";
            $values[':text'] = "%{$filters['text']}%";
        }
        // pendientes de traducir
        if (!empty($filters['pending'])) {
            $sqlFilters .= " HAVING page_lang.pending = 1";
        }

        $sql="
            SELECT
                page.id as id,
                $different_select,
                page.url as `url`,
                page.type as `type`
            FROM    page
            LEFT JOIN page_lang
                ON  page_lang.id = page.id
                AND page_lang.lang = :lang
            $eng_join
            WHERE page.url IS NOT NULL
            $sqlFilters
            ORDER BY name ASC";

        $query = static::query($sql, array(':lang'=>$lang));

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $page) {
            $list[$page->id] = $page;
        }

        return $list;
    }

	/*
	 *  Lista simple de páginas
	 */
	public static function getList($node = \GOTEO_NODE) {
        return self::getAll();
	}

    public function validate(&$errors = array()) {

        $allok = true;

        if (empty($this->id)) {
            $errors[] = 'Registro sin id';
            $allok = false;
        }

        if (empty($this->name)) {
            $errors[] = 'Registro sin nombre';
            $allok = false;
        }

        return $allok;
    }

	/*
	 *  Esto se usara para la gestión de contenido
	 */
	public function save(&$errors = array()) {
        if($this->validate($errors)) {
            try {
                $this->dbInsertUpdate(['page', 'name', 'type', 'description', 'content', 'type']);
                return true;
            } catch(\PDOException $e) {
                $errors[] = 'Page saving error! ' . $e->getMessage();
            }
        }
        return false;
	}

    /**
     * creates the html if is markdown
     *
     * @return string HTML content
     */
    public function parseContent() {
        if($this->type === 'md') {
            return App::getService('app.md.parser')->text($this->content);
        }
        return $this->content;
    }

	/*
	 *  Esto se usara para la gestión de contenido
	 */
	public function add(&$errors = array()) {

			try {
            $values = array(
                ':id' => $this->id,
                ':name' => $this->name,
                ':url' => '/about/'.$this->id
            );

			$sql = "INSERT INTO page
                        (id, name, url)
                    VALUES
                        (:id, :name, :url)
                    ";
			if (Model::query($sql, $values)) {
                return true;
            } else {
                $errors[] = "Ha fallado $sql con <pre>" . print_r($values, true) . "</pre>";
                return false;
            }

		} catch(\PDOException $e) {
            $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
            return false;
		}

	}

}
