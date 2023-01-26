<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Entity\ImpactItem\ImpactItem;

class ImpactItemRepository extends BaseRepository
{
    protected ?string $table = 'impact_item';

    public function persist(ImpactItem $impactItem, array &$errors = []): ?ImpactItem
    {
        $fields = [
            'id' => ':id',
            'name' => ':name',
            'description' => ':description',
            'unit' => ':unit'
        ];

        $values = [
            ':id' => $impactItem->getId(),
            ':name' => $impactItem->getName(),
            ':description' => $impactItem->getDescription(),
            ':unit' => $impactItem->getUnit()
        ];

        $sql = "REPLACE INTO $this->table (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (\PDOException $e) {
            $errors[] = $e->getMessage();
            return null;
        }

        return $impactItem;
    }

    public function getById(int $id): ImpactItem
    {

        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $impactItem = $this->query($sql, [$id])->fetchObject(ImpactItem::class);

        if (!$impactItem instanceOf ImpactItem)
            throw new ModelNotFoundException("ImpactItem with id $id not found");

        return $impactItem;
    }

    public function delete(ImpactItem $impactItem): void
    {
        $sql = "DELETE FROM $this->table WHERE id = ?";

        try {
            $this->query($sql, [$impactItem->getId()]);
        } catch (\PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }
}
