<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Entity\ImpactItem\ImpactProjectItem;
use Goteo\Model\Project;
use PDO;
use PDOException;

class ImpactProjectItemRepository extends BaseRepository
{
    protected ?string $table = 'impact_project_item';


    public function persist(ImpactProjectItem $impactProjectItem, array &$errors = []): ?ImpactProjectItem
    {
        $fields = [
            'project_id' => ':project_id',
            'impact_item_id' => ':impact_item_id'
        ];

        $values = [
            ':project_id' => $impactProjectItem->getProject()->id,
            ':impact_item_id' => $impactProjectItem->getImpactItem()->getId(),
        ];

        $sql = "REPLACE INTO $this->table (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return null;
        }

        return $impactProjectItem;
    }

    public function delete(ImpactProjectItem $impactProjectItem): void
    {
        $sql = "DELETE FROM $this->table WHERE project_id = :project_id AND impact_item_id = :impact_item_id";

        try {
            $this->query($sql, [':project_id' => $impactProjectItem->getProject()->id, ':impact_item_id' => $impactProjectItem->getImpactItem()->getId()]);
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @return ImpactProjectItem[]
     */
    public function getListByProject(Project $project): array
    {
        $sql = "SELECT * FROM $this->table WHERE project_id = ?";

        $result = $this->query($sql, [$project->id])->fetchAll(PDO::FETCH_OBJ);
        $list = [];
        foreach ($result as $obj) {
            $impactItemRepository = new ImpactItemRepository();
            $impactItem = $impactItemRepository->getById($obj->impact_item_id);

            $impactProjectItem = new ImpactProjectItem();
            $impactProjectItem
                ->setImpactItem($impactItem)
                ->setProject($project);

            $list[] = $impactProjectItem;
        }

        return $list;
    }
}
