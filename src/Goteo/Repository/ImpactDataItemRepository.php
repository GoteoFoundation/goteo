<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Model\ImpactData;
use Goteo\Entity\ImpactData\ImpactDataItem;
use PDO;
use PDOException;

class ImpactDataItemRepository extends BaseRepository
{
    protected ?string $table = "impact_data_item";

    /**
     * @return ImpactDataItem[]
     */
    public function getListByImpactData(ImpactData $impactData): array
    {
        $sql = "SELECT * FROM $this->table WHERE impact_data_id = ?";

        $list = [];

        try {
            $result = $this->query($sql, [$impactData->id])->fetchAll(PDO::FETCH_OBJ);

            foreach($result as $obj) {
                $impactItemRepository = new ImpactItemRepository();
                $impactItem = $impactItemRepository->getById($obj->impact_item_id);
                $impactData = ImpactData::get($obj->impact_data_id);

                $impactDataItem = new ImpactDataItem();
                $impactDataItem
                    ->setImpactItem($impactItem)
                    ->setImpactData($impactData);
                $list[] = $impactDataItem;
            }

            return $list;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function persist(ImpactDataItem $impactDataItem, array &$errors = []): ?ImpactDataItem
    {
        $fields = [
            'impact_data_id' => ':impact_data_id',
            'impact_item_id' => ':impact_item_id',
        ];

        $values = [
            ':impact_data_id' => $impactDataItem->getImpactData()->id,
            ':impact_item_id' => $impactDataItem->getImpactItem()->getId()
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return null;
        }

        return $impactDataItem;
    }

    public function delete(ImpactDataItem $impactDataItem): void
    {
        $sql = "DELETE FROM $this->table WHERE impact_data_id = :impact_data_id and impact_item_id = :impact_item_id";
        $values = [
            ':impact_data_id' => $impactDataItem->getImpactData()->id,
            ':impact_item_id' => $impactDataItem->getImpactItem()->getId()
        ];

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }
}
