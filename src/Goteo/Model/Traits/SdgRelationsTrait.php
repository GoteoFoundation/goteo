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
use Goteo\Model\Sdg;
use Goteo\Model\Footprint;
use Goteo\Application\Exception\ModelException;

/**
 * Adds function to deal with Sdg relation ships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "sdg_MODEL_TABLE" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "sdg_id" as relationship field name pointing to sdg.id
 */
trait SdgRelationsTrait {

    /**
     * Add sdgs
     * @param [type]  $sdgs  sdg or array of sdgs
     */
    public function addSdgs($sdgs, $remove_others=false) {
        if(!is_array($sdgs)) $sdgs = [$sdgs];

        $inserts = [];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($sdgs as $sdg) {
            if($sdg instanceOf Sdg) {
                $sdg = $sdg->id;
            }
            $inserts[] = "(:id, :sdg$i)";
            $deletes[] = ":sdg$i";
            $values[":sdg$i"] = $sdg;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql1 = "DELETE FROM `sdg_{$tb}` WHERE {$tb}_id=:id AND sdg_id NOT IN (" . implode(', ', $deletes ?: ['0']) .")";
        $sql2 = "INSERT IGNORE INTO `sdg_{$tb}` ({$tb}_id, sdg_id) VALUES " . implode(', ', $inserts);
        // echo \sqldbg($sql1, $values)."\n";die;
        // echo \sqldbg($sql2, $values)."\n";
        try {
            if($remove_others) {
                self::query($sql1, $values);
            }
            if($deletes) {
                self::query($sql2, $values);
            }
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add sdgs: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Like removing all sdgs associated and add the specified
     * @return [type] [description]
     */
    public function replaceSdgs($sdgs) {
        return $this->addSdgs($sdgs, true);
    }



    /**
     * Return sdgs
     * @return [type] [description]
     */
    public function getSdgs($lang = null) {
        $tb = strtolower($this->getTable());
        list($fields, $joins) = Sdg::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                sdg.id,
                sdg.icon,
                sdg.modified,
                $fields

            FROM sdg_{$tb}
            INNER JOIN sdg ON sdg.id = sdg_{$tb}.sdg_id
            $joins
            WHERE sdg_{$tb}.{$tb}_id = :id
            ORDER BY sdg_{$tb}.order ASC";
        $values = [':id' => $this->id];
        if($query = self::query($sql, $values)) {
            if( $sdgs = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Sdg') ) {
                return $sdgs;
            }
        }
        return [];
    }

    /**
     * Returns footprints associated with
     * @return [type] [description]
     */
    public function getFootprints($lang = null) {
        return Footprint::getFromSdgs($this->getSdgs(), $lang);
    }

    /**
     * Delete sdgs
     * @param [type]  $sdgs  sdg or array of sdgs
     */
    public function removeSdgs($sdgs) {
        if(!is_array($sdgs)) $sdgs = [$sdgs];
        $deletes = [];
        $values = [':id' => $this->id];
        $i = 0;
        foreach($sdgs as $sdg) {
            if($sdg instanceOf Sdg) {
                $sdg = $sdg->id;
            }
            $deletes[] = ":sdg$i";
            $values[":sdg$i"] = $sdg;
            $i++;
        }

        $tb = strtolower($this->getTable());
        $sql = "DELETE FROM `sdg_{$tb}` WHERE {$tb}_id = :id AND sdg_id IN (" . implode(', ', $deletes) . ")";
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to remove sdgs: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return main sdg
     */
    public function getMainSdg() {
        return $this->getSdgs() ? current($this->getSdgs()) : null;
    }

}
