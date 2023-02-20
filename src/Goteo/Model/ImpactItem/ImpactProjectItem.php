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
use Goteo\Model\Footprint;
use Goteo\Model\ImpactData;
use Goteo\Model\ImpactData\ImpactDataProject;
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Model\Project;
use Goteo\Model\Project\Cost;
use PDO;

class ImpactProjectItem extends Model
{
    public ?int $id = null;
    private ?ImpactItem $impactItem = null;
    private Project $project;
    private ?ImpactData $relatedImpactData;
    protected ?string $value = null;
    public ?string $project_id = null;
    public ?int $impact_item_id = null;

    protected $Table = 'impact_project_item';

    static protected $Table_static = 'impact_project_item';

    public function __construct()
    {
        if ($this->impact_item_id) {
            $this->impactItem = ImpactItem::getById($this->impact_item_id);
        }

        if ($this->project_id) {
            $this->project = Project::get($this->project_id);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): ImpactProjectItem
    {
        $this->id = $id;
        return $this;
    }

    public function getImpactItem(): ?ImpactItem
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): ImpactProjectItem
    {
        $this->value = $value;
        return $this;
    }

    public function getRelatedImpactData(): ?ImpactData
    {
        return $this->relatedImpactData;
    }

    public function setRelatedImpactData(ImpactData $impactData): ImpactProjectItem
    {
        $this->relatedImpactData = $impactData;
        return $this;
    }

    static public function get($id) {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE id = ?";

        return self::query($sql, [$id])->fetchObject( __CLASS__);
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
            $impactProjectItem->setRelatedImpactData($impactData);
        }

        return $list;
    }

    static public function getListByProjectAndFootprint(Project $project, Footprint $footprint): array
    {
        $table = self::$Table_static;

        $sql = "
            SELECT ipi.*
            FROM $table ipi
            INNER JOIN impact_item_footprint iif on iif.impact_item_id = ipi.impact_item_id
            WHERE project_id = :project_id AND iif.footprint_id = :footprint_id
            ";

        $values = [
            ':project_id' => $project->id,
            ':footprint_id' => $footprint->id
        ];

        return self::query($sql, $values)->fetchAll(PDO::FETCH_CLASS, __CLASS__);
    }

    public function getImpactDataProject(): ?ImpactDataProject
    {
        $sql = "
            SELECT *
            FROM impact_data_project
            INNER JOIN impact_data_item idi ON idi.impact_data_id = impact_data_project.impact_data_id
            INNER JOIN impact_project_item ipi ON ipi.impact_item_id = idi.impact_item_id
            WHERE ipi.id = ?
        ";

        return $this->query($sql, [$this->id])->fetchObject(ImpactDataProject::class);
    }

    public function getCosts(): ImpactProjectItemCost
    {
        return ImpactProjectItemCost::getListByImpactProjectItem($this->id);
    }

    public function getCostAmounts(): int
    {
        $impactProjectItemCostList = ImpactProjectItemCost::getListByImpactProjectItem($this);

        $amount = 0;
        foreach($impactProjectItemCostList as $impactProjectItemCost) {
            $cost = $impactProjectItemCost->getCost();
            $amount += $cost->amount;
        }

        return $amount;
    }


    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) return false;

        if ($this->project) {
            $this->project_id = $this->project->id;
        }

        if ($this->impactItem) {
            $this->impact_item_id = $this->impactItem->getId();
        }

        $fields = [
            'project_id' => ":project_id",
            'impact_item_id' => ":impact_item_id",
            'value' => ":value"
        ];

        $values = [
            ":project_id" => $this->project_id,
            ":impact_item_id" => $this->impact_item_id,
            ":value" => $this->value
        ];

        if ($this->id) {
            $fields["id"] = ":id";
            $values[":id"] = $this->id;
        }


        try {
            $table = $this->Table;
            $sql = "REPLACE INTO `$this->Table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";
            $this->query($sql, $values);

            $this->id = static::insertId();
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
}
