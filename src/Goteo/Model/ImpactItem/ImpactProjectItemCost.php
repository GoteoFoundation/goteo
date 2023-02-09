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
use Goteo\Model\Project\Cost;
use PDO;
use PDOException;

class ImpactProjectItemCost extends Model
{
    private ImpactProjectItem $impactProjectItem;
    private Cost $cost;
    private int $impact_project_item_id;
    private int $cost_id;

    public function getImpactProjectItem(): ImpactProjectItem
    {
        return $this->impactProjectItem;
    }

    public function setImpactProjectItem(ImpactProjectItem $impactProjectItem): ImpactProjectItemCost
    {
        $this->impactProjectItem = $impactProjectItem;
        return $this;
    }

    public function getCost(): Cost
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
            ':impact_project_item_id' => $this->impactProjectItem,
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


    /**
     * @return ImpactProjectItemCost[]
     * @throws PDOException
     */
    static public function getListByImpactProjectItem(ImpactProjectItem $impactProjectItem): array
    {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE impact_project_item_id = ?";

        $list = self::query($sql, [$impactProjectItem->getId()])->fetchAll(PDO::FETCH_CLASS, __CLASS__);
        foreach ($list as $impactProjectItemCost) {
            $cost = Cost::get($impactProjectItemCost->cost_id);
            $impactProjectItemCost
                ->setItemProjectItem($impactProjectItem);

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
        return parent::dbDelete(['impact_project_item_id', 'cost_id']);
    }
}
