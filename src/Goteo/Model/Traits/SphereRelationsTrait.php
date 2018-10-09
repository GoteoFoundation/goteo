<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Traits;

use Goteo\Application\Config;
use Goteo\Model\Sphere;
use Goteo\Application\Exception\ModelException;

/**
 * Adds function to deal with Sphere relation ships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "MODEL_TABLE_sphere" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "sphere_id" as relationship field name pointing to sphere.id
 */
trait SphereRelationsTrait {
    public function getSphereRelationalTable() {
        $tb = strtolower($this->getTable());
        return "{$tb}_sphere";
    }

    /**
     * Add spheres
     * @param [type]  $spheres  sphere or array of spheres
     */
    public function addSpheres($spheres, $remove_others=false) {
        if(!is_array($spheres)) $spheres = [$spheres];

        $inserts = [];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($spheres as $sphere) {
            if($sphere instanceOf Sphere) {
                $sphere = $sphere->id;
            }
            $inserts[] = "(:id, :sphere$i)";
            $deletes[] = ":sphere$i";
            $values[":sphere$i"] = $sphere;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $rel = $this->getSphereRelationalTable();
        $sql1 = "DELETE FROM `$rel` WHERE {$tb}_id=:id AND sphere_id NOT IN (" . implode(', ', $deletes ?: ['0']) .")";
        $sql2 = "INSERT IGNORE INTO `$rel` ({$tb}_id, sphere_id) VALUES " . implode(', ', $inserts);
        try {
            if($remove_others) {
                self::query($sql1, $values);
            }
            if($deletes) {
                self::query($sql2, $values);
            }
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add spheres: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Like removing all spheres associated and add the specified
     * @return [type] [description]
     */
    public function replaceSpheres($spheres) {
        return $this->addSpheres($spheres, true);
    }

    /**
     * Return spheres
     * @return [type] [description]
     */
    public function getSpheres($lang = null) {
        $tb = strtolower($this->getTable());
        $rel = $this->getSphereRelationalTable();
        list($fields, $joins) = Sphere::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                sphere.id,
                sphere.icon,
                sphere.order,
                sphere.landing_match,
                $fields
            FROM `$rel`
            INNER JOIN sphere ON sphere.id = `$rel`.sphere_id
            $joins
            WHERE `$rel`.{$tb}_id = :id
            ORDER BY `$rel`.order ASC";
        $values = [':id' => $this->id];
        if($query = self::query($sql, $values)) {
            if( $spheres = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Sphere') ) {
                return $spheres;
            }
        }
        return [];
    }

    /**
     * Delete spheres
     * @param [type]  $spheres  sphere or array of spheres
     */
    public function removeSpheres($spheres) {
        if(!is_array($spheres)) $spheres = [$spheres];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($spheres as $sphere) {
            if($sphere instanceOf Sphere) {
                $sphere = $sphere->id;
            }
            $deletes[] = ":sphere$i";
            $values[":sphere$i"] = $sphere;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $rel = $this->getSphereRelationalTable();
        $sql = "DELETE FROM `$rel` WHERE {$tb}_id = :id AND sphere_id IN (" . implode(', ', $deletes) . ")";
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to remove spheres: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return main sphere
     */
    public function getMainSphere() {
        return $this->getSpheres() ? current($this->getSpheres()) : null;
    }

}
