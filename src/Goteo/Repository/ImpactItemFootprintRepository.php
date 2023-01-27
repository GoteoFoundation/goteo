<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Entity\ImpactItem\ImpactItem;
use Goteo\Entity\ImpactItem\ImpactItemFootprint;
use Goteo\Model\Footprint;
use PDO;
use PDOException;

class ImpactItemFootprintRepository extends BaseRepository
{
    protected ?string $table = 'impact_item_footprint';

    public function persist(ImpactItemFootprint $impactItemFootprint, array &$errors = []): ?ImpactItemFootprint
    {
        $fields = [
            'impact_item_id' => ':impact_item_id',
            'footprint_id' => ':footprint_id'
        ];

        $values = [
            ':impact_item_id' => $impactItemFootprint->getImpactItem()->getId(),
            ':footprint_id' => $impactItemFootprint->getFootprint()->id,
        ];

        $sql = "REPLACE INTO $this->table (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return null;
        }

        return $impactItemFootprint;
    }

    public function delete(ImpactItemFootprint $impactItemFootprint): void
    {
        $sql = "DELETE FROM $this->table WHERE impact_item_id = :impact_item_id AND footprint_id = :footprint_id";

        try {
            $this->query($sql, [':impact_item_id' => $impactItemFootprint->getImpactItem()->getId(), ':footprint_id' => $impactItemFootprint->getFootprint()->id]);
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @return ImpactItemFootprint[]
     */
    public function getListByFootprint(Footprint $footprint): array
    {
        $sql = "SELECT * FROM $this->table WHERE footprint_id = ?";

        $result = $this->query($sql, [$footprint->id])->fetchAll(PDO::FETCH_OBJ);
        $list = [];
        foreach ($result as $obj) {
            $impactItemRepository = new ImpactItemRepository();
            $impactItem = $impactItemRepository->getById($obj->impact_item_id);

            $impactItemFootprint = new ImpactItemFootprint();
            $impactItemFootprint
                ->setImpactItem($impactItem)
                ->setFootprint($footprint);

            $list[] = $impactItemFootprint;
        }

        return $list;
    }


}
