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

    /**
     * Add spheres
     * @param [type]  $spheres  sphere or array of spheres
     */
    public function addSpheres($spheres) {
        if(!is_array($spheres)) $spheres = [$spheres];

        $inserts = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($spheres as $sphere) {
            if($sphere instanceOf Sphere) {
                $sphere = $sphere->id;
            }
            $inserts[] = "(:id, :sphere$i)";
            $values[":sphere$i"] = $sphere;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql = "INSERT INTO `{$tb}_sphere` ({$tb}_id, sphere_id) VALUES " . implode(', ', $inserts);
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add spheres: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return spheres
     * @return [type] [description]
     */
    public function getSpheres($lang = null) {
        $tb = strtolower($this->getTable());
        list($fields, $joins) = Sphere::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                sphere.id,
                sphere.image,
                sphere.order,
                sphere.landing_match,
                $fields
            FROM {$tb}_sphere
            INNER JOIN sphere ON sphere.id = {$tb}_sphere.sphere_id
            $joins
            WHERE {$tb}_sphere.{$tb}_id = :id
            ORDER BY {$tb}_sphere.order ASC";
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
        $sql = "DELETE FROM `{$tb}_sphere` WHERE {$tb}_id = :id AND sphere_id IN (" . implode(', ', $deletes) . ")";
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
