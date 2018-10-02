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
    public function getFootprintRelationalTable() {
        $tb = strtolower($this->getTable());
        return "{$tb}_footprint";
    }

    /**
     * Add footprints
     * @param [type]  $footprints  footprint or array of footprints
     */
    public function addFootprints($footprints, $remove_others=false) {
        if(!is_array($footprints)) $footprints = [$footprints];

        $inserts = [];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($footprints as $footprint) {
            if($footprint instanceOf Footprint) {
                $footprint = $footprint->id;
            }
            $inserts[] = "(:id, :footprint$i)";
            $deletes[] = ":footprint$i";
            $values[":footprint$i"] = $footprint;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $rel = $this->getFootprintRelationalTable();
        $sql1 = "DELETE FROM `$rel` WHERE {$tb}_id=:id AND footprint_id NOT IN (" . implode(', ', $deletes ?: ['0']) .")";
        $sql2 = "INSERT IGNORE INTO `$rel` ({$tb}_id, footprint_id) VALUES " . implode(', ', $inserts);
        try {
            if($remove_others) {
                self::query($sql1, $values);
            }
            if($deletes) {
                self::query($sql2, $values);
            }
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add footprints: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Like removing all footprints associated and add the specified
     * @return [type] [description]
     */
    public function replaceFootprints($footprints) {
        return $this->addFootprints($footprints, true);
    }

    /**
     * Return footprints
     * @return [type] [description]
     */
    public function getFootprints($lang = null) {
        $tb = strtolower($this->getTable());
        $rel = $this->getFootprintRelationalTable();
        list($fields, $joins) = Footprint::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                footprint.id,
                footprint.icon,
                $fields
            FROM `$rel`
            INNER JOIN footprint ON footprint.id = `$rel`.footprint_id
            $joins
            WHERE `$rel`.{$tb}_id = :id
            ORDER BY `$rel`.order ASC";
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
        $rel = $this->getFootprintRelationalTable();
        $sql = "DELETE FROM `$rel` WHERE {$tb}_id = :id AND footprint_id IN (" . implode(', ', $deletes) . ")";
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
