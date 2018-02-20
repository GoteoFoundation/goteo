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
use Goteo\Model\SocialCommitment;
use Goteo\Model\Image;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Library\Check;

class Banner extends \Goteo\Core\Model {

    public
        $id,
        $node,
        $project,
        $image,
        $order,
        $title,
        $description,
        $url,
        $active = false;

    public static function getLangFields() {
        return ['title', 'description', 'url'];
    }

    /*
     *  Devuelve datos de un banner de proyecto
     */
    public static function get ($id, $lang = null) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $query = static::query("
            SELECT
                banner.id as id,
                banner.node as node,
                banner.project as project,
                project.name as name,
                $fields,
                banner.image as image,
                banner.order as `order`,
                banner.active as `active`
            FROM banner
            $joins
            LEFT JOIN project
                ON project.id = banner.project
            WHERE banner.id = :id
            ", [':id' => $id]);
        if($banner = $query->fetchObject(__CLASS__)) {
            $banner->image = Image::get($banner->image);
        }

        return $banner;
    }

    /**
     * Lista de proyectos en banners
     */
    public static function getAll ($activeonly = false, $node = \GOTEO_NODE, $lang = null) {

        // estados
        $status = Project::status();

        $banners = array();

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sqlFilter = ($activeonly) ? " AND banner.active = 1" : '';

        // sacamos también los datos de proyecto que se necesitan
        $sql="SELECT
                banner.id as id,
                banner.node as node,
                banner.project as project,
                project.name as name,
                $fields,
                project.status as status,
                project.name as project_name,
                project.days as project_days,
                project.amount as project_amount,
                project.mincost as project_mincost,
                project.maxcost as project_maxcost,
                user.name as project_user_name,
                banner.image as image,
                banner.order as `order`,
                banner.active as `active`,
                project.social_commitment as project_social_commitment
            FROM    banner
            LEFT JOIN project
                ON project.id = banner.project
            LEFT JOIN user
                ON user.id = project.owner
            $joins
            WHERE banner.node = :node
            $sqlFilter
            ORDER BY `order` ASC";

        $query = static::query($sql, array(':node' => $node));

        $used_projects = array();
        foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
            // ahora Image::get ya no hace consulta sql porque el nombre de la imagene stá en la tabla
            $banner->image = Image::get($banner->image);
            $banner->status = $status[$banner->status];

            //mincost, maxcost, si mincost es zero, lo calculamos:
            if(!empty($banner->project) && empty($banner->project_mincost)) {
                $calc = Project::calcCosts($banner->project);
                $banner->project_mincost = $calc->mincost;
                $banner->project_maxcost = $calc->maxcost;
                //a partir de aqui ya deberia estar calculado para las siguientes consultas
            }

            if ($banner->project_social_commitment)
            {
                $banner->social_commitmentData = SocialCommitment::get($banner->project_social_commitment);
                $banner->social_commitmentData->image = Image::get($banner->social_commitmentData->image);
            }

            //rewards, metodo antiguo un sql por proyecto
            // $banner->project_social_rewards = Project\Reward::getAll($banner->project, 'social', Lang::current());
            //
            // usado para obtener los rewards de golpe
            if (!empty($banner->project)) $used_projects[$banner->project] = $banner->id;
            $banners[$banner->id] = $banner;
        }


        return $banners;
    }

    /*
     * Lista de banners
     */
    public static function getList ($node = \GOTEO_NODE) {

        $banners = array();
        // solo banenrs de nodo
        if ($node == \GOTEO_NODE) {
            return false;
        }

        $query = static::query("
            SELECT
                banner.id as id,
                banner.node as node,
                banner.title as title,
                banner.description as description
            FROM    banner
            WHERE banner.node = :node
            ORDER BY `order` ASC
            ", array(':node' => $node));

        foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
            $banners[] = $banner;
        }

        return $banners;
    }

    /*
     * Lista de proyectos disponibles para destacar
     */
    public static function available ($current = null, $node = \GOTEO_NODE) {

        if (!empty($current)) {
            $sqlCurr = " AND banner.project != '$current'";
        } else {
            $sqlCurr = "";
        }

        $query = static::query("
            SELECT
                project.id as id,
                project.name as name,
                project.status as status
            FROM    project
            WHERE status = 3
            AND project.id NOT IN (SELECT project FROM banner WHERE banner.node = :node AND project IS NOT NULL {$sqlCurr} )
            ORDER BY name ASC
            ", array(':node' => $node));

        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    // ya no validamos esto,  puede haber banners in proyecto y sin imagen
    public function validate (&$errors = array()) {
        if (empty($this->project))
            $errors[] = 'Falta proyecto';

        if (empty($this->image))
            $errors[] = 'Falta imagen';

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
//            if (!$this->validate($errors)) return false;

        // Imagen de fondo de banner
        if (is_array($this->image) && !empty($this->image['name'])) {
            $image = new Image($this->image);

            if ($image->save($errors)) {
                $this->image = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image = '';
            }
        }
        if (is_null($this->image)) {
            $this->image = '';
        }

        $fields = array(
            'id',
            'node',
            'title',
            'description',
            'url',
            'project',
            'image',
            'order',
            'active'
            );

        $set = '';
        $values = array();

        foreach ($fields as $field) {
            if ($set != '') $set .= ", ";
            $set .= "`$field` = :$field ";
            $values[":$field"] = $this->$field;
        }

        try {
            $sql = "REPLACE INTO banner SET " . $set;
            self::query($sql, $values);
            if (empty($this->id)) $this->id = self::insertId();

            return true;
        } catch(\PDOException $e) {
            $errors[] = "HA FALLADO!!! " . $e->getMessage();
            return false;
        }
    }

    /* Para activar/desactivar un banner
     */
    public static function setActive ($id, $active = false) {

        $sql = "UPDATE banner SET active = :active WHERE id = :id";
        if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Para que un proyecto salga antes  (disminuir el order)
     */
    public static function up ($id, $node = \GOTEO_NODE) {
        $extra = array (
                'node' => $node
            );
        return Check::reorder($id, 'up', 'banner', 'id', 'order', $extra);
    }

    /*
     * Para que un proyecto salga despues  (aumentar el order)
     */
    public static function down ($id, $node = \GOTEO_NODE) {
        $extra = array (
                'node' => $node
            );
        return Check::reorder($id, 'down', 'banner', 'id', 'order', $extra);
    }

    /*
     *
     */
    public static function next ($node = \GOTEO_NODE) {
        $query = self::query('SELECT MAX(`order`) FROM banner WHERE node = :node'
            , array(':node'=>$node));
        $order = $query->fetchColumn(0);
        return ++$order;

    }


}

