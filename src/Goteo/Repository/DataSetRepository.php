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

     public function save(DataSet $dataSet, array &$errors = [])
     {
        $fields = [
            'title' => ':title',
            'description' => ':description',
            'lang' => ':lang',
            'url' => ':url',
            'image' => ':image'
        ];

        $values = [
            ':title' => $dataSet->getTitle(),
            ':description' => $dataSet->getDescription(),
            ':lang' => $dataSet->getLang(),
            ':url' => $dataSet->getUrl(),
            ':image' => $dataSet->getImage()->name
        ];

        if ($dataSet->getId()) {
            $fields['id'] = ':id';
            $values[':id'] = $dataSet->getId();
        }

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields) ) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
            if (!$dataSet->getId())
                $dataSet->setId($this->insertId());
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
            return false;
        }
        return $dataSet;
    }

    public function delete(DataSet $dataSet): void {
        $sql = "DELETE FROM $this->table WHERE $this->table.id = :id";
        \sqldbg($sql);
        try {
            $this->query($sql, [':id' => $dataSet->getId()]);
        } catch (PDOException $exception) {
            throw new ModelException($exception->getMessage());
        }
    }
 }
