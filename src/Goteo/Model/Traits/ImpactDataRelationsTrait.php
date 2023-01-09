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
                impact_data.image
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
