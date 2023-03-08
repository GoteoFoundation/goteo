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

use Goteo\Application\Exception\ModelException;
use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\Project\Cost;
use PDO;
use PDOException;

class ImpactProjectItemCost extends Model
{
    private ImpactProjectItem $impactProjectItem;
    private ?Cost $cost = null;
    protected ?int $impact_project_item_id = null;
    protected ?int $cost_id = null;

    protected $Table = 'impact_project_item_cost';
    static protected $Table_static = 'impact_project_item_cost';

    public function __construct()
    {
        if ($this->impact_project_item_id) {
            $this->impactProjectItem = ImpactProjectItem::get($this->impact_project_item_id);
        }

        if ($this->cost_id) {
            $this->cost = Cost::get($this->cost_id);
        }
    }

    public function getImpactProjectItem(): ImpactProjectItem
    {
        return $this->impactProjectItem;
    }

    public function setImpactProjectItem(ImpactProjectItem $impactProjectItem): ImpactProjectItemCost
    {
        $this->impactProjectItem = $impactProjectItem;
        return $this;
    }

    public function getCost(): ?Cost
    {
        return $this->cost;
    }

    public function setCost(Cost $cost): ImpactProjectItemCost
    {
        $this->cost = $cost;
        return $this;
    }

    public function save(&$errors = array())
    {
        if (!$this->validate($errors))
            return false;

        $fields = [
            'impact_project_item_id' => ':impact_project_item_id',
            'cost_id' => ':cost_id'
        ];

        $values = [
            ':impact_project_item_id' => $this->impactProjectItem->getId(),
            ':cost_id' => $this->cost->id,
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

    static public function getByImpactProjectItemAndCost(ImpactProjectItem $impactProjectItem, Cost $cost): ImpactProjectItemCost
    {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE impact_project_item_id = :impact_project_item_id AND cost_id = :cost_id";

        $values = [
            ':impact_project_item_id' => $impactProjectItem->getId(),
            ':cost_id' => $cost->id
        ];

        return self::query($sql, $values)
            ->fetchObject(__CLASS__);
    }

    /**
     * @return ImpactProjectItemCost[]
     * @throws PDOException
     */
    static public function getListByImpactProjectItem(ImpactProjectItem $impactProjectItem): array
    {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE impact_project_item_id = ?";

        $impactProjectItemCostList = self::query($sql, [$impactProjectItem->getId()])->fetchAll(PDO::FETCH_CLASS, __CLASS__);

        $list = [];
        foreach ($impactProjectItemCostList as $impactProjectItemCost) {
            $cost = Cost::get($impactProjectItemCost->cost_id);
            $impactProjectItemCost
                ->setImpactProjectItem($impactProjectItem)
                ->setCost($cost);
            ;

            $list[] = $impactProjectItemCost;
        }

        return $list;
    }

    public function validate(&$errors = array())
    {
        if (empty($this->cost))
            $errors[] = Text::get('validate_missing_cost');

        if (empty($this->impactProjectItem))
            $errors[] = Text::get('validate_missing_impact_project_item');

        return empty($errors);
    }

    public function dbDelete(array $where = ['id'])
    {
        $this->impact_project_item_id = $this->impactProjectItem->getId();
        $this->cost_id = $this->cost->id;
        try {
            return parent::dbDelete(['impact_project_item_id', 'cost_id']);
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }
}
