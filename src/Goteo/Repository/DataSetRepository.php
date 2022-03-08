<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with
 */

 namespace Goteo\Repository;

 use Goteo\Application\Exception\ModelException;
 use Goteo\Application\Exception\ModelNotFoundException;
 use Goteo\Entity\DataSet;
 use PDO;
 use PDOException;

 class DataSetRepository extends BaseRepository {
     protected ?string $table = 'data_set';

     public function getById(int $id): DataSet
     {
         $sql = "SELECT data_set.*
                FROM data_set
                WHERE data_set.id = ?";

         $dataSet = $this->query($sql, [$id])->fetchObject(DataSet::class);

         if (!$dataSet instanceOf DataSet)
             throw new ModelNotFoundException("DataSet with id $id not found");

         return $dataSet;
    }

    public function count(): int
    {
        $sql = "SELECT count(data_set.id)
                FROM data_set";

        return $this->query($sql)->fetchColumn();
    }

     /**
      * @return DataSet[]
      */
    public function getList(int $offset = 0, int $limit = 10): array
    {
         $sql = "SELECT data_set.*
                FROM data_set
                LIMIT $limit
                OFFSET $offset";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, DataSet::class );
    }

     /**
      * @return DataSet[]
      */
    public function getListByFootprint(array $footprints): array
    {

        $sqlWhere = "";
        if (!empty($footprints)) {
            $sqlWhere = "WHERE fds.footprint_id IN ( " . implode(',', $footprints) . ")";
        }

        $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN footprint_data_set fds ON fds.data_set_id = data_set.id
                {$sqlWhere}
                ";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, DataSet::class );
    }

     public function getLastByFootprintAndType(array $footprints, string $type): DataSet {
         $sqlWhere = "WHERE data_set.type = '{$type}'";

         if (!empty($footprints)) {
             $sqlWhere .= "AND fds.footprint_id IN ( " . implode(',', $footprints) . ")";
         }

         $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN footprint_data_set fds ON fds.data_set_id = data_set.id
                {$sqlWhere}
                ORDER BY data_set.modified_at DESC
                LIMIT 1
                ";

         $dataSet = $this->query($sql)->fetchObject(DataSet::class );

         if (!$dataSet instanceOf DataSet)
             throw new ModelNotFoundException("DataSet not found");

         return $dataSet;
     }

     /**
      * @return DataSet[]
      */
     public function getListBySDGs(array $sdgs): array
     {
        $sqlWhere = "";
        if (!empty($sdgs)) {
            $sqlWhere = "WHERE sds.sdg_id IN ( " . implode(',', $sdgs) . ")";
        }

        $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN sdg_data_set sds ON sds.data_set_id = data_set.id
                {$sqlWhere}
                ";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, DataSet::class );
     }

     public function getLastBySDGsAndType(array $sdgs, string $type): DataSet {
         $sqlWhere = "WHERE data_set.type = '{$type}' ";

         if (!empty($sdgs)) {
             $sqlWhere .= "AND sds.sdg_id IN ( " . implode(',', $sdgs) . ")";
         }

         $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN sdg_data_set sds ON sds.data_set_id = data_set.id
                {$sqlWhere}
                ORDER BY data_set.modified_at DESC
                LIMIT 1
                ";

         $dataSet = $this->query($sql)->fetchObject(DataSet::class );

         if (!$dataSet instanceOf DataSet)
             throw new ModelNotFoundException("DataSet not found");

         return $dataSet;
     }

     /**
      * @return DataSet[]
      */
     public function getListByFootprintAndSDGs(array $filter = [], int $offset = 0, int $limit = 10): array
     {
         $sqlWhere = "";
         $sqlWhereFilter = [];

         if (!empty($filter['footprints'])) {
             $sqlWhereFilter[] = "fds.footprint_id IN ( " . implode(',', $filter['footprints']) . ")";
         }

         if (!empty($filter['sdgs'])) {
             $sqlWhereFilter[] = "sds.sdg_id IN ( " . implode(',', $filter['sdgs']) . ")";
         }

         if (empty($filter['footprints']) && empty($filter['sdgs'])) {
            $sqlWhereFilter[] = "sds.sdg_id IS NOT NULL OR fds.footprint_id IS NOT NULL";
         }

         if (!empty($sqlWhereFilter))
             $sqlWhere = "WHERE " . implode(' OR ', $sqlWhereFilter);


         $sql = "SELECT data_set.*
                FROM data_set
                LEFT JOIN sdg_data_set sds ON sds.data_set_id = data_set.id
                LEFT JOIN footprint_data_set fds ON fds.data_set_id = data_set.id
                $sqlWhere
                LIMIT $limit
                OFFSET $offset";

         return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, DataSet::class );
     }

     /**
      * @return DataSet[]
      */
     public function getListByCall(array $calls): array
     {
        $sqlWhere = "";
        if (!empty($calls)) {
            $sqlWhere = "WHERE cds.call_id IN ( " . implode(',', $calls) . ")";
        }

        $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN call_data_set cds ON cds.data_set_id = data_set.id
                {$sqlWhere}
                ";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, DataSet::class );
     }

     public function getLastByCallAndType(array $calls, string $type): DataSet {
         $sqlWhere = "WHERE data_set.type = '{$type}' ";

         if (!empty($calls)) {
             foreach($calls as $index => $call) {
                 $parts[] = ':calls_' . $index;
                 $values[':calls_' . $index] = $call;
             }

             $sqlWhere .= "AND cds.call_id IN ( " . implode(',', $parts) . ")";
         }

         $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN call_data_set cds ON cds.data_set_id = data_set.id
                {$sqlWhere}
                ORDER BY data_set.modified_at DESC
                LIMIT 1
                ";

         $dataSet = $this->query($sql, $values)->fetchObject(DataSet::class );

         if (!$dataSet instanceOf DataSet)
             throw new ModelNotFoundException("DataSet not found");

         return $dataSet;
     }

     /**
      * @return DataSet[]
      */
     public function getListByChannel(array $channels): array
     {
         $sqlWhere = "";
         $values = [];
         $parts = [];

         if (!empty($channels)) {
             foreach($channels as $index => $channel) {
                 $parts[] = ':channels_' . $index;
                 $values[':channels_' . $index] = $channel;
             }

             $sqlWhere .= "AND nds.node_id IN ( " . implode(',', $parts) . ")";
         }

         $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN node_data_set nds ON nds.data_set_id = data_set.id
                {$sqlWhere}
                ";

         return $this->query($sql, $values)->fetchAll(PDO::FETCH_CLASS, DataSet::class );
     }

     public function getLastByChannelAndType(array $channels, string $type): DataSet {
         $sqlWhere = "WHERE data_set.type = '{$type}' ";

         if (!empty($channels)) {
             foreach($channels as $index => $call) {
                 $parts[] = ':channel_' . $index;
                 $values[':channel_' . $index] = $call;
             }

             $sqlWhere .= "AND nds.node_id IN ( " . implode(',', $parts) . ")";
         }

         $sql = "SELECT data_set.*
                FROM data_set
                INNER JOIN node_data_set nds ON nds.data_set_id = data_set.id
                {$sqlWhere}
                ORDER BY data_set.modified_at DESC
                LIMIT 1
                ";

         $dataSet = $this->query($sql, $values)->fetchObject(DataSet::class );

         if (!$dataSet instanceOf DataSet)
             throw new ModelNotFoundException("DataSet not found");

         return $dataSet;
     }

     public function persist(DataSet $dataSet, array &$errors = []): DataSet
     {
         if ($dataSet->getId())
            return $this->update($dataSet, $errors);

         return $this->create($dataSet, $errors);
    }

    private function create(DataSet $dataSet, &$errors = []): DataSet {
        $fields = [
            'title' => ':title',
            'description' => ':description',
            'type' => ':type',
            'lang' => ':lang',
            'url' => ':url',
            'image' => ':image'
        ];

        $values = [
            ':title' => $dataSet->getTitle(),
            ':description' => $dataSet->getDescription(),
            ':type' => $dataSet->getType(),
            ':lang' => $dataSet->getLang(),
            ':url' => $dataSet->getUrl(),
            ':image' => $dataSet->getImage()->name
        ];

        $sql = "INSERT INTO `$this->table` (" . implode(',', array_keys($fields) ) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
            $dataSet->setId($this->insertId());
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
            return false;
        }
        return $dataSet;

    }

    private function update(DataSet $dataSet, &$errors = []): DataSet {
        $fields = [
            'id' => ':id',
            'title' => ':title',
            'description' => ':description',
            'type' => ':type',
            'lang' => ':lang',
            'url' => ':url',
            'image' => ':image'
        ];

        $values = [
            ':id' => $dataSet->getId(),
            ':title' => $dataSet->getTitle(),
            ':description' => $dataSet->getDescription(),
            'type' => $dataSet->getType(),
            ':lang' => $dataSet->getLang(),
            ':url' => $dataSet->getUrl(),
            ':image' => $dataSet->getImage()->name
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields) ) . ") VALUES (" . implode(',', array_values($fields)) . ")";
        try {
            $this->query($sql, $values);
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
        }
        return $dataSet;
    }

    public function delete(DataSet $dataSet): void {
        $sql = "DELETE FROM $this->table WHERE $this->table.id = :id";
        try {
            $this->query($sql, [':id' => $dataSet->getId()]);
        } catch (PDOException $exception) {
            throw new ModelException($exception->getMessage());
        }
    }
 }
