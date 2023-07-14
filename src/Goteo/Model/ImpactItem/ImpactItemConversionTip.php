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
use PDOException;

class ImpactItemConversionTip extends Model
{
    private ImpactItem $impactItem;
    private int $impact_item_id;
    private string $rateTipDescription;
    private string $reference;

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): ImpactItemConversionTip
    {
        $this->impactItem = $impactItem;
        return $this;
    }

    public function getRateTipDescription(): string
    {
        return $this->rateTipDescription;
    }

    public function setRateTipDescription(string $rateTipDescription): ImpactItemConversionTip
    {
        $this->rateTipDescription = $rateTipDescription;
        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): ImpactItemConversionTip
    {
        $this->reference = $reference;
        return $this;
    }

    static public function getByImpactItem(ImpactItem $impactItem): ImpactItemConversionTip
    {
        $table = self::$Table_static;
        $sql = "SELECT *
                FROM $table
                WHERE impact_item_id = ?";

        $impactItemConversionTip = self::query($sql, [$impactItem->getId()])->fetchObject(ImpactItemConversionTip::class);
        if (!$impactItemConversionTip instanceof ImpactItemConversionTip)
            throw new ModelNotFoundException("ImpactItemConversionTip for ImpactItem $impactItem->getId() not found");

        $impactItemConversionTip->setImpactItem($impactItem);
        return $impactItemConversionTip;
    }



    public function save(&$errors = array())
    {
        if (!$this->validate($errors))
            return false;

        $fields = [
            'impact_item_id' => ':impact_item_id',
            'rate_tip_description' => ':rate_tip_description',
            'reference' => ':reference'
        ];

        $values = [
            ':impact_item_id' => $this->impactItem->getId(),
            ':rate_tip_description' => $this->rateTipDescription,
            ':reference' => $this->reference
        ];

        try {
            $sql = "INSERT INTO $this->table (". implode(',', array_keys($fields)) .") VALUES (".implode(',', array_values($fields)) . ")";
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        $this->query($sql, $values);

        return true;
    }

    public function validate(&$errors = array())
    {
        if (empty($this->impactItem))
            $errors['impact_item'] = Text::get('validate_missing_impact_item');

        if (empty($this->rateTipDescription))
            $errors['rate_tip_description'] = Text::get('validate_missing_rate_tip_description');

        if (empty($this->reference))
            $errors['reference'] = Text::get('validate_missing_reference');
    }

    public function dbDelete(array $where = ['id'])
    {
        $this->impact_item_id = $this->impactItem->getId();
        return parent::dbDelete(['impact_item_id']);
    }
}
