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

use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Faq\FaqSubsection;
use Goteo\Library\Check;
use Goteo\Library\Text;

class Faq extends \Goteo\Core\Model {

    public
        $id,
        $slug,
        $node,
        $section,
        $title,
        $subsection_id,
        $description,
        $order;

    static public function getLangFields() {
        return ['title', 'description'];
    }

    // fallbacks to getbyid
    public static function getBySlug($slug, $lang = null) {
        $faq = self::get((string)$slug, $lang);
        if(!$faq) {
            $faq = self::get((int)$slug, $lang);
        }
        return $faq;
    }

    public static function getById($id, $lang = null) {
        return self::get((int)$id, $lang);
    }

    /*
     *  Faq data
     */
    public static function get ($id, $lang = null) {
        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "
            SELECT
                faq.id as id,
                faq.id as slug,
                faq.subsection_id as subsection_id,
                faq.node as node,
                faq.section as section,
                $fields,
                faq.order as `order`
            FROM faq
            $joins
            ";


        if(is_string($id)) {
            $sql .= "WHERE faq.slug = :slug";
            $values = [':slug' => $id];
        } else {
            $sql .= "WHERE faq.id = :id";
            $values = [':id' => $id];
        }

         //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);

        $faq = $query->fetchObject(__CLASS__);

        return $faq;
    }

    /*
     * Lista de faqs
     */
    public static function getAll ($section = 'node', $lang = null) {
        if(!$lang) $lang = Lang::current();
        $values = array(':section' => $section);

        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="SELECT
                    faq.id as id,
                    faq.node as node,
                    faq.section as section,
                    $fields,
                    faq.order as `order`
                FROM faq
                $joins
                WHERE faq.section = :section
                ORDER BY `order` ASC";

        $query = static::query($sql, $values);

        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

     /**
     * Faq listing
     *
     * @param array filters
     * @param string node id
     * @param int limit items per page or 0 for unlimited
     * @param int page
     * @param int pages
     * @return array of programs instances
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $filter = [];
        $values = [];

        if ($filters['section']) {

            // get subsections from a section
            $subsections= FaqSubsection::getList(['section'=>$filters['section']]);

            foreach ($subsections as $subsection)
                $subsections_id[]=$subsection->id;

            $filter[] = "faq.subsection_id in ('".implode("','", $subsections_id)."')";

        }

        if ($filters['subsection']) {
            $filter[] = "faq.subsection_id = :subsection_id";
            $values[':subsection_id'] = $filters['subsection'];

        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        $sql="SELECT
                  faq.id as id,
                  faq.slug as slug,
                  faq.node as node,
                  $fields,
                  faq.section as section,
                  faq.subsection_id as subsection,
                  faq.order
              FROM faq
              $joins
              $sql
              ORDER BY faq.order ASC
              LIMIT $offset, $limit";
        //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    public function validate (&$errors = array()) {
        if (empty($this->node))
            $errors[] = 'Missing node';
            //Text::get('mandatory-faq-node');

        if (empty($this->section))
            $errors[] = 'Missing section';
            //Text::get('mandatory-faq-section');

        if (empty($this->title))
            $errors[] = 'Missing title';
            //Text::get('mandatory-faq-title');

        return empty($errors);
    }

    public function slugExists($slug) {
        $values = [':slug' => $slug];
        $sql = 'SELECT COUNT(*) FROM faq WHERE slug=:slug';
        if($this->id) {
            $values[':id'] = $this->id;
            $sql .= ' AND id!=:id';
        }

        return self::query($sql, $values)->fetchColumn() > 0;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

         // Attempt to create slug if not exists
        if(!$this->slug) {
            $this->slug = self::idealiza($this->title, false, false, 150);
            if($this->slug && $this->slugExists($this->slug)) {
                $this->slug = $this->slug .'-' . ($this->id ? $this->id : time());
            }
        }

        try {
            $this->dbInsertUpdate([
                'id',
                'slug',
                'node',
                'section',
                'title',
                'description',
                'order'
            ]);

            $extra = array(
                'section' => $this->section,
                'node' => $this->node
            );
            Check::reorder($this->id, $this->move, 'faq', 'id', 'order', $extra);

            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving faq: ' . $e->getMessage();
        }
    }

    /*
     * Para quitar una pregunta
     */
    public static function remove ($id, $node = null) {
        if(empty($node)) $node = Config::get('current_node');
        if(empty($id)) return false;
        try {
            $sql = "DELETE FROM faq WHERE id = :id AND node = :node";
            self::query($sql, array(':id'=>$id, ':node'=>$node));
        } catch (\PDOException $e) {
            // throw new Exception("Delete error in $sql");
            return false;
        }
        return true;
    }

    /*
     * Para que una pregunta salga antes  (disminuir el order)
     */
    public static function up ($id, $node = \GOTEO_NODE) {
        $query = static::query("SELECT section FROM faq WHERE id = ?", array($id));
        $faq = $query->fetchObject();
        $extra = array(
            'section' => $faq->section,
            'node' => $node
        );
        return Check::reorder($id, 'up', 'faq', 'id', 'order', $extra);
    }

    /*
     * Para que un proyecto salga despues  (aumentar el order)
     */
    public static function down ($id, $node = \GOTEO_NODE) {
        $query = static::query("SELECT section FROM faq WHERE id = ?", array($id));
        $faq = $query->fetchObject();
        $extra = array(
            'section' => $faq->section,
            'node' => $node
        );
        return Check::reorder($id, 'down', 'faq', 'id', 'order', $extra);
    }

    /*
     * Orden para añadirlo al final
     */
    public static function next ($section = 'node', $node = \GOTEO_NODE) {
        $query = self::query('SELECT MAX(`order`) FROM faq WHERE section = :section AND node = :node'
            , array(':section'=>$section, ':node'=>$node));
        $order = $query->fetchColumn(0);
        return ++$order;

    }

    public static function sections () {
        return array(
            'node' => Text::get('faq-main-section-header'),
            'project' => Text::get('faq-project-section-header'),
            'sponsor' => Text::get('faq-sponsor-section-header'),
            'investors' => Text::get('faq-investors-section-header'),
            'nodes' => Text::get('faq-nodes-section-header')
        );
    }

    public static function colors () {
        return array(
            'node' => '#808285',
            'project' => '#20b3b2',
            'sponsor' => '#96238f',
            'investors' => '#0c4e99',
            'nodes' => '#8f8f8f'
        );
    }


}

