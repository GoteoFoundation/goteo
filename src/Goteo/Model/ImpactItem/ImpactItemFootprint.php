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
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Model\Footprint;
use PDOException;

class ImpactItemFootprint extends Model
{
    private ImpactItem $impactItem;
    private Footprint $footprint;
    private int $impact_item_id;
    private int $footprint_id;

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): ImpactItemFootprint
    {
        $this->impactItem = $impactItem;
        return $this;
    }

    public function getFootprint(): Footprint
    {
        return $this->footprint;
    }

    public function setFootprint(Footprint $footprint): ImpactItemFootprint
    {
        $this->footprint = $footprint;
        return $this;
    }

    /**
     * @return ImpactItemFootprint[]
     */
    static public function getListByFootprint(Footprint $footprint): array
    {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE footprint_id = ?";

        $list = self::query($sql, [$footprint->id])->fetchAll(PDO::FETCH_CLASS, __CLASS__);
        foreach ($list as $impactItemFootprint) {
            $impactItem = ImpactItem::getById($impactItemFootprint->impact_item_id);

            $impactItemFootprint
                ->setImpactItem($impactItem)
                ->setFootprint($footprint);
        }

        return $list;
    }


    public function save(&$errors = array())
    {
        if (!$this->validate($errors)) return false;

        $fields = [
            'impact_item_id' => ':impact_item_id',
            'footprint_id' => ':footprint_id'
        ];

        $values = [
            ':impact_item_id' => $this->impactItem->getId(),
            ':footprint_id' => $this->footprint->id,
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
            $errors[] = Text::get('validate_impact_item');

        if (empty($this->footprint))
            $errors[] = Text::get('validate_footprint');
    }

    public function dbDelete(array $where = ['id'])
    {
        $this->impact_item_id = $this->impactItem->getId();
        $this->footprint_id = $this->footprint->id;

        return parent::dbDelete(['impact_item_id', 'footprint_id']);
    }
}
