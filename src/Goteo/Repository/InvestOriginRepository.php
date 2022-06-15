<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Entity\Invest\InvestOrigin;
use PDO;
use PDOException;

class InvestOriginRepository extends BaseRepository
{
    protected ?string $table = 'invest_origin';

    public function getByInvestId(int $id): InvestOrigin
    {
        $sql = "SELECT *
                FROM invest_origin
                WHERE invest_origin.invest_id = ?";

        $investOrigin = $this->query($sql, [$id])->fetchObject(InvestOrigin::class);

        if (!$investOrigin instanceOf InvestOrigin)
            throw new ModelNotFoundException("InvestOrigin with invest_id $id not found");

        return $investOrigin;
    }

    /**
     * @return InvestOrigin[]
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        $sql = "SELECT *
                FROM invest_origin
                LIMIT $limit
                OFFSET $offset";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, InvestOrigin::class);
    }

    /**
     * @return InvestOrigin[]
     */
    public function getListBySource(string $source): array
    {
        $sql = "SELECT *
                FROM invest_origin
                WHERE invest_origin.source = ?";

        return $this->query($sql, $source)->fetchAll(PDO::FETCH_CLASS, InvestOrigin::class);
    }

    /**
     * @return InvestOrigin[]
     */
    public function getListByDetail(string $detail): array
    {
        $sql = "SELECT *
                FROM invest_origin
                WHERE invest_origin.detail = ?";

        return $this->query($sql, $detail)->fetchAll(PDO::FETCH_CLASS, InvestOrigin::class);
    }

    /**
     * @return InvestOrigin[]
     */
    public function getListByAllocated(string $allocated): array
    {
        $sql = "SELECT *
                FROM invest_origin
                WHERE invest_origin.allocated = ?";

        return $this->query($sql, $allocated)->fetchAll(PDO::FETCH_CLASS, InvestOrigin::class);
    }

    public function count(): int
    {
        $sql = "SELECT count(invest_origin.invest_id)
                FROM invest_origin";

        return $this->query($sql)->fetchColumn();
    }

    /**
     * @return string[]
     */
    public function getSources(): array {
        $sql = "SELECT distinct(invest_origin.source)
                FROM invest_origin";

        return $this->query($sql)->fetchAll();
    }

    /**
     * @return string[]
     */
    public function getDetails(): array {
        $sql = "SELECT distinct(invest_origin.detail)
                FROM invest_origin";

        return $this->query($sql)->fetchAll();
    }

    /**
     * @return string[]
     */
    public function getAllocateds(): array {
        $sql = "SELECT distinct(invest_origin.allocated)
                FROM invest_origin";

        return $this->query($sql)->fetchAll();
    }

    public function persist(InvestOrigin $investOrigin, array &$errors = []): InvestOrigin
    {
        $fields = [
            'invest_id' => ':invest_id',
            'source' => ':source',
            'detail' => ':detail',
            'allocated' => ':allocated'
        ];

        $values = [
            ':invest_id' => $investOrigin->getInvestId(),
            ':source' => $investOrigin->getSource(),
            ':detail' => $investOrigin->getDetail(),
            ':allocated' => $investOrigin->getAllocated(),
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields) ) . ") VALUES (" . implode(',', array_values($fields)) . ")";
        try {
            $this->query($sql, $values);
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
        }
        return $investOrigin;
    }

    public function delete(InvestOrigin $investOrigin)
    {
        $sql = "DELETE FROM $this->table WHERE $this->table.invest_id = ?";
        try {
            $this->query($sql, [$investOrigin->getInvestId()]);
        } catch (PDOException $exception) {
            throw new ModelException($exception->getMessage());
        }
    }
}
