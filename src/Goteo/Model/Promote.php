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

use Goteo\Library\Text;
use Goteo\Model\Project;
use Goteo\Model\Image;
use Goteo\Model\Blog;
use Goteo\Library\Check;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class Promote extends \Goteo\Core\Model {

    public
        $id,
        $node,
        $project,
        $name,
        $title,
        $description,
        $order,
        $active;

    public static function getLangFields() {
        return ['title', 'description'];
    }

    /*
     *  Devuelve datos de un destacado
     */
    public static function get ($id, $lang = null) {

            //Obtenemos el idioma de soporte
            if(!$lang) $lang = Lang::current();
            list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

            $query = static::query("
                SELECT
                    promote.id as id,
                    promote.node as node,
                    promote.project as project,
                    project.name as name,
                    $fields,
                    promote.order as `order`,
                    promote.active as `active`
                FROM    promote
                $joins
                INNER JOIN project
                    ON project.id = promote.project
                WHERE promote.id = :id
                ", array(':id'=>$id));
            $promote = $query->fetchObject(__CLASS__);

            return $promote;
    }

    /*
     * Lista de proyectos destacados
     */
    public static function getAll ($activeonly = false, $node = \GOTEO_NODE, $lang = null) {
        if(empty($lang)) $lang = Lang::current();

        $promos = array();

        $sqlFilter = ($activeonly) ? " AND promote.active = 1 AND project.status=3" : '';

        if(self::default_lang($lang) == Config::get('sql_lang')) {
            $different_select=" IFNULL(promote_lang.title, promote.title) as title,
                                IFNULL(promote_lang.description, promote.description) as promo_text";
            $different_select_project=" IFNULL(project_lang.description, project.description) as description";
            }
        else {
                $different_select=" IFNULL(promote_lang.title, IFNULL(eng.title, promote.title)) as title,
                                    IFNULL(promote_lang.description, IFNULL(eng.description, promote.description)) as promo_text";
                $eng_join=" LEFT JOIN promote_lang as eng
                                ON  eng.id = promote.id
                                AND eng.lang = 'en'";
                $different_select_project=" IFNULL(project_lang.description, IFNULL(eng_proj.description, project.description)) as description";
                $eng_join_project=" LEFT JOIN project_lang as eng_proj
                                ON  eng_proj.id = project.id
                                   AND eng_proj.lang = 'en'";
            }

        $query = static::query("
            SELECT
                promote.id as id,
                promote.project as project,
                project.name as name,
                project.status as status,
                $different_select,
                project.published as published,
                project.created as created,
                project.updated as updated,
                project.success as success,
                project.closed as closed,
                project.mincost as mincost,
                project.maxcost as maxcost,
                $different_select_project,
                project.amount as amount,
                project.image as image,
                project.num_investors as num_investors,
                project.num_messengers as num_messengers,
                project.num_posts as num_posts,
                project.days as days,
                user.id as user_id,
                user.name as user_name,
                project_conf.noinvest as noinvest,
                project_conf.one_round as one_round,
                project_conf.days_round1 as days_round1,
                project_conf.days_round2 as days_round2
            FROM    promote
            LEFT JOIN promote_lang
                ON promote_lang.id = promote.id
                AND promote_lang.lang = :lang
            $eng_join
            INNER JOIN project
                ON project.id = promote.project
            LEFT JOIN project_lang
                ON project_lang.id = project.id
                AND project_lang.lang = :lang
            $eng_join_project
            INNER JOIN user
                ON user.id = project.owner
            LEFT JOIN project_conf
                ON project_conf.project = project.id
            WHERE promote.node = :node
            $sqlFilter
            ORDER BY promote.order ASC, title ASC
            ", array(':node' => $node, ':lang'=>$lang));

        foreach($query->fetchAll(\PDO::FETCH_CLASS, '\\Goteo\\Model\\Project') as $promo) {
            $promo->promo_text = Text::recorta($promo->promo_text, 100, false);
            //variables usadas en view/project/widget/project.html.php




            // aquí usará getWidget para sacar todo esto
            $promo->projectData = Project::getWidget($promo, $lang);

            $promos[] = $promo;
        }
            // print_r($promos);die;

        return $promos;
    }
    /*
     * Lista de destacados para Admin
     */
    public static function getList ($activeonly = false, $node = \GOTEO_NODE) {

        $list = array();

        $sqlFilter = ($activeonly) ? " AND promote.active = 1" : '';

        $query = static::query("
            SELECT
                promote.id as id,
                promote.project as project,
                project.name as name,
                project.status as status,
                promote.active as active,
                promote.order as `order`
            FROM    promote
            LEFT JOIN project
                ON project.id = promote.project
            WHERE promote.node = :node
            $sqlFilter
            ORDER BY promote.order ASC, title ASC
            ", array(':node' => $node));

        foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
            $list[] = $promo;
        }
            // print_r($promos);die;

        return $list;
    }

    /*
     * Lista de proyectos disponibles para destacar
     */
    public static function available ($current = null, $node = \GOTEO_NODE) {

        if (!empty($current)) {
            $sqlCurr = " AND project != '$current'";
        } else {
            $sqlCurr = "";
        }

        // para nodo tambien los financiados
        if ($node == \GOTEO_NODE) {
            $sqlFilter = " AND status = 3";
        } else {
            $sqlFilter = " AND (status = 3 OR status = 4 OR status = 5)";
        }

        $query = static::query("
            SELECT
                project.id as id,
                project.name as name,
                project.status as status
            FROM    project
            WHERE project.id NOT IN (SELECT project FROM promote WHERE promote.node = :node{$sqlCurr} )
            {$sqlFilter}
            ORDER BY name ASC
            ", array(':node' => $node));

        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    // ya no validamos esto
    public function validate (&$errors = array()) {
        // if (empty($this->node))                $errors[] = 'Falta nodo';

        if (empty($this->project))
            $errors[] = 'Hay que destacar un proyecto';

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        $fields = array(
            'id',
            'node',
            'project',
            'title',
            'description',
            'order',
            'active'
            );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Save error " . $e->getMessage();
            return false;
        }
    }

    /* Para activar/desactivar un destacado
     */
    public static function setActive ($id, $active = false) {

        $sql = "UPDATE promote SET active = :active WHERE id = :id";
        if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Para que salga antes  (disminuir el order)
     */
    public static function up ($id, $node = \GOTEO_NODE) {
        $extra = array (
                'node' => $node
            );
        return Check::reorder($id, 'up', 'promote', 'id', 'order', $extra);
    }

    /*
     * Para que salga despues  (aumentar el order)
     */
    public static function down ($id, $node = \GOTEO_NODE) {
        $extra = array (
                'node' => $node
            );
        return Check::reorder($id, 'down', 'promote', 'id', 'order', $extra);
    }

    /*
     *
     */
    public static function next ($node = \GOTEO_NODE) {
        $query = self::query('SELECT MAX(`order`) FROM promote WHERE node = :node'
            , array(':node'=>$node));
        $order = $query->fetchColumn(0);
        return ++$order;

    }


}
