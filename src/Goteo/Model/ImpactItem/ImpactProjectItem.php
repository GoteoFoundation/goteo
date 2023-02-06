<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\ImpactItem;

use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\ImpactData;
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Model\Project;
use PDO;

class ImpactProjectItem extends Model
{
    public int $id;
    private ImpactItem $impactItem;
    private Project $project;
    private string $value;
    private string $project_id;
    private int $impact_item_id;

    protected $Table = 'impact_project_item';

    static protected $Table_static = 'impact_project_item';

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ImpactProjectItem
    {
        $this->id = $id;
        return $this;
    }

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): ImpactProjectItem
    {
        $this->impactItem = $impactItem;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): ImpactProjectItem
    {
        $this->project = $project;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): ImpactProjectItem
    {
        $this->value = $value;
        return $this;
    }

    static public function get($id) {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE id = ?";

        $impactProjectItem = self::query($sql, [$id])->fetchObject( __CLASS__);
        $impactProjectItem->setImpactItem(ImpactItem::getById($impactProjectItem->impact_item_id));
        $impactProjectItem->setProject(Project::get($impactProjectItem->project_id));

        return $impactProjectItem;

    }
    /**
     * @return ImpactProjectItem[]
     */
    static public function getListByProject(Project $project): array
    {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE project_id = ?";

        $list = self::query($sql, [$project->id])->fetchAll(PDO::FETCH_CLASS, __CLASS__);
        foreach ($list as $impactProjectItem) {
            $impactProjectItem->setImpactItem(ImpactItem::getById($impactProjectItem->impact_item_id));
            $impactProjectItem->setProject($project);
        }

        return $list;
    }

    /**
     * @return ImpactProjectItem[]
     */
    static public function getListByProjectAndImpactData(Project $project, ImpactData $impactData): array
    {
        $table = self::$Table_static;
        $sql = "SELECT $table.*
                FROM $table
                INNER JOIN impact_data_item ON impact_data_item.impact_item_id = $table.impact_item_id
                WHERE project_id = :project_id and impact_data_item.impact_data_id = :impact_data_id";
        $values = [':project_id' => $project->id, ':impact_data_id' => $impactData->id];

        $list = self::query($sql, $values)->fetchAll(PDO::FETCH_CLASS, __CLASS__);

        foreach($list as $impactProjectItem) {
            $impactItem = ImpactItem::getById($impactProjectItem->impact_item_id);
            $impactProjectItem->setImpactItem($impactItem);
            $impactProjectItem->setProject($project);
        }

        return $list;
    }

    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) return false;

        $fields = [
            'id' => ':id',
            'project_id' => ':project_id',
            'impact_item_id' => ':impact_item_id',
            'value' => ':value'
        ];

        $values = [
            'id' => $this->id,
            ':project_id' => $this->project->id,
            ':impact_item_id' => $this->impactItem->getId(),
            ':value' => $this->value
        ];

        $sql = "REPLACE INTO $this->Table (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        return true;
    }

    public function validate(&$errors = array())
    {
        if (empty($this->impactItem))
            $errors['impactItem'] = Text::get('validate-missing-impact-item');

        if (empty($this->project))
            $errors['project'] = Text::get('validate-missing-project');

        if (empty($this->value))
            $errors['value'] = Text::get('validate-missing-value');

        return empty($errors);
    }

    public function dbDelete(array $where = ['id'])
    {
        $this->impact_item_id = $this->getImpactItem()->getId();
        $this->impact_project_id = $this->getProject()->id;

        return parent::dbDelete(['impact_item_id', 'project_id']);
    }
}
