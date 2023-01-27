<?php

namespace Goteo\Repository;

use Goteo\Entity\ImpactItem\ImpactProjectItem;
use Goteo\Entity\ImpactItem\ImpactProjectItemCost;

class ImpactProjectItemCostRepository extends BaseRepository
{
    protected ?string $table = 'impact_project_item_cost';

    public function persist(ImpactProjectItemCost $impactProjectItemCost, array &$errors = []): ?ImpactProjectItemCost
    {
        $fields = [
            'impact_project_item_id' => ':impact_project_item_id',
            'cost_id' => ':cost_id'
        ];

        $values = [
            ':impact_project_item_id' => $impactProjectItemCost->getItemProjectItem()->getId(),
            ':cost_id' => $impactProjectItemCost->getCost()->id,
        ];

        $sql = "REPLACE INTO $this->table (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return null;
        }

        return $impactProjectItemCost;
    }

    public function delete(ImpactProjectItemCost $impactProjectItemCost): void
    {
        $sql = "DELETE FROM $this->table WHERE impact_project_item_id = :impact_project_item_id AND cost_id = :cost_id";

        try {
            $this->query($sql, [':impact_item_id' => $impactProjectItemCost->getItemProjectItem()->getId(), ':cost_id' => $impactProjectItemCost->getCost()->id]);
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @return ImpactProjectItemCost[]
     */
    public function getListByFootprint(ImpactProjectItem $impactProjectItem): array
    {
        $sql = "SELECT * FROM $this->table WHERE impact_project_item_id = ?";

        $result = $this->query($sql, [$impactProjectItem->getId()])->fetchAll(PDO::FETCH_OBJ);
        $list = [];
        foreach ($result as $obj) {
            $cost = Cost::get($obj->cost_id);
            $impactProjectItemCost = new ImpactProjectItemCost();
            $impactProjectItemCost
                ->setItemProjectItem($impactProjectItem)
                ->setCost($cost);

            $list[] = $impactProjectItemCost;
        }

        return $list;
    }
}
