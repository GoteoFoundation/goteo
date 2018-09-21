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
use Goteo\Model\Footprint;
use Goteo\Application\Exception\ModelException;

/**
 * Adds function to deal with Footprint relation ships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "MODEL_TABLE_footprint" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "footprint_id" as relationship field name pointing to footprint.id
 */
trait FootprintRelationsTrait {

    /**
     * Add footprints
     * @param [type]  $footprints  footprint or array of footprints
     */
    public function addFootprints($footprints) {
        if(!is_array($footprints)) $footprints = [$footprints];

        $inserts = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($footprints as $footprint) {
            if($footprint instanceOf Footprint) {
                $footprint = $footprint->id;
            }
            $inserts[] = "(:id, :footprint$i)";
            $values[":footprint$i"] = $footprint;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql = "INSERT INTO `{$tb}_footprint` ({$tb}_id, footprint_id) VALUES " . implode(', ', $inserts);
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add footprints: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return footprints
     * @return [type] [description]
     */
    public function getFootprints($lang = null) {
        $tb = strtolower($this->getTable());
        list($fields, $joins) = Footprint::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                footprint.id,
                footprint.icon,
                $fields
            FROM {$tb}_footprint
            INNER JOIN footprint ON footprint.id = {$tb}_footprint.footprint_id
            $joins
            WHERE {$tb}_footprint.{$tb}_id = :id
            ORDER BY {$tb}_footprint.order ASC";
        $values = [':id' => $this->id];
        if($query = self::query($sql, $values)) {
            if( $footprints = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Footprint') ) {
                return $footprints;
            }
        }
        return [];
    }

    /**
     * Delete footprints
     * @param [type]  $footprints  footprint or array of footprints
     */
    public function removeFootprints($footprints) {
        if(!is_array($footprints)) $footprints = [$footprints];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($footprints as $footprint) {
            if($footprint instanceOf Footprint) {
                $footprint = $footprint->id;
            }
            $deletes[] = ":footprint$i";
            $values[":footprint$i"] = $footprint;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql = "DELETE FROM `{$tb}_footprint` WHERE {$tb}_id = :id AND footprint_id IN (" . implode(', ', $deletes) . ")";
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to remove footprints: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return main footprint
     */
    public function getMainFootprint() {
        return $this->getFootprints() ? current($this->getFootprints()) : null;
    }

}
