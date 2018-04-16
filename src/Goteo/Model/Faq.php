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
use Goteo\Library\Check;
use Goteo\Library\Text;

class Faq extends \Goteo\Core\Model {

    public
        $id,
        $node,
        $section,
        $title,
        $description,
        $order;

    static public function getLangFields() {
        return ['title', 'description'];
    }

    /*
     *  Devuelve datos de un destacado
     */
    public static function get ($id, $lang = null) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $query = static::query("
            SELECT
                faq.id as id,
                faq.node as node,
                faq.section as section,
                $fields,
                faq.order as `order`
            FROM faq
            $joins
            WHERE faq.id = :id
            ", array(':id' => $id));
        $faq = $query->fetchObject(__CLASS__);

        return $faq;
    }

    /*
     * Lista de proyectos destacados
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

    public function validate (&$errors = array()) {
        if (empty($this->node))
            $errors[] = 'Falta nodo';
            //Text::get('mandatory-faq-node');

        if (empty($this->section))
            $errors[] = 'Falta seccion';
            //Text::get('mandatory-faq-section');

        if (empty($this->title))
            $errors[] = 'Falta título';
            //Text::get('mandatory-faq-title');

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        try {
            $this->dbInsertUpdate([
                'id',
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

