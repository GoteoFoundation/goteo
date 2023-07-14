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
use Goteo\Application\Lang;
use Goteo\Model\ImpactData;
use Goteo\Application\Exception\ModelException;

/**
 * Adds function to deal with ImpactData relationships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "MODEL_TABLE_impact" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "impact_data_id" as relationship field name pointing to impact_data.id
 */
trait ImpactDataRelationsTrait {

    private function getImpactDataTable(): string
    {
        $tb = strtolower(self::getTable());
        return "{$tb}_impact";
    }

    public function addImpactData(ImpactData $impact_data, int $order) {

        $tb = strtolower($this->getTable());
        $rel = $this->getImpactDataTable();
        $sql = "REPLACE INTO `$rel` (`{$tb}_id`, `impact_data_id`, `order`) VALUES (:tb_id, :impact_data_id, :order)";
        $values = [
            ':tb_id' => $this->id,
            ':impact_data_id' => $impact_data->id,
            ':order' => $order
        ];

        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException($e->getMessage());
        }
        return $this;
    }

    public function hasImpactData(ImpactData $impactData): bool
    {
        $tb = strtolower($this->getTable());
        $rel = $this->getImpactDataTable();

        $sql = "SELECT `$rel`.impact_data_id
            FROM `$rel`
            WHERE `$rel`.impact_data_id = ?";

        return (bool)self::query($sql, $impactData->id)->fetchColumn();
    }

    public function getAllImpactData($lang = null): array
    {
        $tb = strtolower($this->getTable());
        $rel = $this->getImpactDataTable();
        list($fields, $joins) = ImpactData::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                impact_data.id,
                $fields,
                impact_data.image,
                impact_data.lang,
                impact_data.type,
                impact_data.icon,
                impact_data.source,
                impact_data.result_msg,
                impact_data.operation_type
            FROM `$rel`
            INNER JOIN impact_data ON impact_data.id = `$rel`.impact_data_id
            $joins
            WHERE `$rel`.{$tb}_id = :id
            ORDER BY `$rel`.order ASC";
        $values = [':id' => $this->id];

        if($query = self::query($sql, $values)) {
            if( $impact_data = $query->fetchAll(\PDO::FETCH_CLASS, ImpactData::class) ) {
                return $impact_data;
            }
        }
        return [];
    }

    public function getListOfImpactData(array $filters = [], int $offset = 0, int $limit = 10, int $count = 0): array
    {
        $tb = strtolower($this->getTable());
        $rel = $this->getImpactDataTable();
        $lang = Lang::current();
        list($fields, $joins) = ImpactData::getLangsSQLJoins($lang);
        $sqlWhere = [];
        $sqlInner = "";

        if ($filters['source']) {
            if (is_array($filters['source'])) {
                $parts = [];
                foreach($filters['source'] as $i => $source) {
                    $parts[] = ':source' . $i;
                    $values[':source' . $i] = $source;
                }

                if($parts) $sqlWhere []= "impact_data.source IN (" . implode(',', $parts) . ")";
            } else {
                $sqlWhere[] = "impact_data.source = :source";
                $values[':source'] = $filters['source'];
            }
        }

        if ($filters['not_source']) {
            $sqlWhere[] = "impact_data.source != :not_source";
            $values[':not_source'] = $filters['not_source'];
        }

        if ($filters['type']) {
            if (is_array($filters['type'])) {
                $parts = [];
                foreach($filters['type'] as $i => $type) {
                    $parts[] = ':type' . $i;
                    $values[':type' . $i] = $type;
                }

                if($parts) $sqlWhere[] = "impact_data.type IN (" . implode(',', $parts) . ")";
            } else {
                $sqlWhere[] = "impact_data.type = :type";
                $values[':type'] = $filters['type'];
            }
        }

        if ($filters['not_type']) {
            $sqlWhere[] = "impact_data.type != :not_type";
            $values[':not_type'] = $filters['not_type'];
        }

        if ($filters['project']) {
            $sqlInner .= "INNER JOIN impact_data_project ON impact_data.id = impact_data_project.impact_data_id ";
            $sqlWhere[] = "impact_data_project.project_id = :project";
            $values[':project'] = $filters['project'];
        }

        $sqlWhere = $sqlWhere ? "AND " . implode(' AND ', $sqlWhere) : '';

        $sql = "SELECT
                impact_data.id,
                $fields,
                impact_data.image,
                impact_data.lang,
                impact_data.type,
                impact_data.icon,
                impact_data.source,
                impact_data.result_msg,
                impact_data.operation_type
            FROM `$rel`
            INNER JOIN impact_data ON impact_data.id = `$rel`.impact_data_id
            $joins
            $sqlInner
            WHERE `$rel`.{$tb}_id = :id
            $sqlWhere
            ORDER BY `$rel`.order ASC";
        $values[":id"] = $this->id;

        if($query = self::query($sql, $values)) {
            if( $impact_data = $query->fetchAll(\PDO::FETCH_CLASS, ImpactData::class) ) {
                return $impact_data;
            }
        }
        return [];
    }

    public function removeImpactData(ImpactData $impact_data) {

        $values = [
            ':id' => $this->id,
            ':impact_data_id' => $impact_data->id
        ];

        $tb = strtolower($this->getTable());
        $rel = $this->getImpactDataTable();
        $sql = "DELETE FROM `$rel` WHERE {$tb}_id = :id AND impact_data_id = :impact_data_id";
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException($e->getMessage());
        }
        return $this;
    }

}
