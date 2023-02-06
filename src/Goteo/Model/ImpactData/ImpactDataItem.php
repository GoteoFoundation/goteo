<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\ImpactData;

use Goteo\Core\Model;
use Goteo\Library\Text;
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Model\ImpactData;
use PDOException;

class ImpactDataItem extends Model
{
    private ImpactData $impactData;
    private ImpactItem $impactItem;

    protected $Table = 'impact_data_item';
    static protected $Table_static = 'impact_data_item';

    public function getImpactData(): ImpactData
    {
        return $this->impactData;
    }

    public function setImpactData(ImpactData $impactData): ImpactDataItem
    {
        $this->impactData = $impactData;
        return $this;
    }

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): ImpactDataItem
    {
        $this->impactItem = $impactItem;
        return $this;
    }

    /**
     * @return ImpactDataItem[]
     */
    static public function getListByImpactData(ImpactData $impactData): array
    {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE impact_data_id = ?";

        $list = [];

        try {
            $result = self::query($sql, [$impactData->id])->fetchAll(PDO::FETCH_OBJ);

            foreach($result as $obj) {
                $impactItem = ImpactItem::getById($obj->impact_item_id);
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

    static public function getByImpactItem(ImpactItem $impactItem): ImpactDataItem
    {
        $table = self::$Table_static;
        $sql = "SELECT * FROM $table WHERE impact_item_id = ?";

        $impactDataItem = self::query($sql, [$impactItem->getId()])->fetchObject(__CLASS__);
        $impactDataItem->impactData = ImpactData::get($impactDataItem->impact_data_id);
        $impactDataItem->impactItem = $impactItem;

        return $impactDataItem;
    }

    public function save(&$errors = array()): bool
    {
        // TODO: Implement save() method.
        if (!$this->validate($errors)) return false;

        $fields = [
            'impact_data_id' => ':impact_data_id',
            'impact_item_id' => ':impact_item_id',
        ];

        $values = [
            ':impact_data_id' => $this->impactData->id,
            ':impact_item_id' => $this->impactItem->getId()
        ];

        $sql = "REPLACE INTO `$this->Table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        return true;
    }

    public function validate(&$errors = array()): bool
    {
        if (empty($this->impactItem)) {
            $errors['impactItem'] = Text::get('validate-missing-impact-item');
        }

        if (empty($this->impactData)) {
            $errors['impactData'] = Text::get('validate-missing-impact-data');
        }

        return empty($errors);
    }

    public function dbDelete(array $where = ['impact_item_id', 'impact_data_id'])
    {
        return parent::dbDelete($where);
    }
}
