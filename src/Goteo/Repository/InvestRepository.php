<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Invest;

class InvestRepository extends BaseRepository
{
    protected ?string $table = 'invest';

    /**
     * @return Invest[]
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        $sql = "SELECT *
                FROM invest
                LIMIT $limit
                OFFSET $offset";

        return $this->query($sql)->fetchAll(\PDO::FETCH_CLASS, Invest::class);
    }

    /**
     * @return Invest[]
     */
    public function getListByPayment(string $payment): array
    {
        $sql = "SELECT *
                FROM invest
                WHERE invest.payment = ?";

        return $this->query($sql, [$payment])->fetchAll(\PDO::FETCH_CLASS, Invest::class);
    }

    public function getListByTransaction(string $transaction): array
    {
        $sql = "SELECT *
                FROM invest
                WHERE invest.transaction = ?";

        return $this->query($sql, [$transaction])->fetchAll(\PDO::FETCH_CLASS, Invest::class);
    }
}
