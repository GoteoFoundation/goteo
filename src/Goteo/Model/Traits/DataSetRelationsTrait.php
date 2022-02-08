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
use Goteo\Core\Model;
use Goteo\Entity\DataSet;
use Goteo\Application\Exception\ModelException;
use PDOException;

/**
 * Adds function to deal with DataSet relationships
 * Works with any model who has relationship tables with the fields following this rules:
 * - "MODEL_TABLE_data_set" as table name
 * - "MODEL_TABLE_id" as relationship field name pointing to MODEL_TABLE primary ID
 * - "id" as field name for primary ID in MODEL_TABLE
 * - "data_set_id" as relationship field name pointing to data_set.id
 */
trait DataSetRelationsTrait {

    private function getDataSetTable(): string {
        $tb = strtolower(self::getTable());
        return "{$tb}_data_set";
    }

    public function addDataSet(DataSet $dataSet, int $order): Model {

        $tb = strtolower($this->getTable());
        $rel = $this->getDataSetTable();
        $sql = "REPLACE INTO `$rel` ({$tb}_id, data_set_id, `order`) VALUES (:tb_id, :data_set_id, :order)";
        $values = [
            ':tb_id' => $this->id,
            ':data_set_id' => $dataSet->getId(),
            ':order' => $order
        ];

        try {
            self::query($sql, $values);
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage());
        }

        return $this;
    }

    /**
     * @return DataSet[]
     */
    public function getAllDataSet($lang = null): array {
        $tb = strtolower($this->getTable());
        $rel = $this->getDataSetTable();
        list($fields, $joins) = DataSet::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                data_set.id,
                data_set.title,
                data_set.description,
                data_set.lang,
                data_set.url,
                data_set.image,
                data_set.created,
                data_set.modified
            FROM `$rel`
            INNER JOIN data_set ON data_set.id = `$rel`.data_set_id
            $joins
            WHERE `$rel`.{$tb}_id = :id
            ORDER BY `$rel`.order ASC";
        $values = [':id' => $this->id];

        if($query = self::query($sql, $values)) {
            if( $dataSet = $query->fetchAll(\PDO::FETCH_CLASS, DataSet::class) ) {
                return $dataSet;
            }
        }
        return [];
    }

    public function removeDataSet(DataSet $dataSet): Model {

        $values = [
            ':id' => $this->id,
            ':data_set_id' => $dataSet->getId()
        ];

        $tb = strtolower($this->getTable());
        $rel = $this->getDataSetTable();
        $sql = "DELETE FROM `$rel` WHERE {$tb}_id = :id AND data_set_id = :data_set_id";
        try {
            self::query($sql, $values);
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage());
        }
        return $this;
    }

}
