<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Entity\ImpactItem\ImpactItem;
use Goteo\Entity\ImpactItem\ImpactItemConversionTip;
use PDOException;

class ImpactItemConversionTipRepository extends BaseRepository
{
    protected ?string $table = 'impact_item_conversion_tip';

    public function getByImpactItem(ImpactItem $impactItem): ImpactItemConversionTip
    {
        $sql = "SELECT *
                FROM $this->table
                WHERE impact_item_id = ?";

        $impactItemConversionTip = $this->query($sql, [$impactItem->getId()])->fetchObject(ImpactItemConversionTip::class);
        if (!$impactItemConversionTip instanceof ImpactItemConversionTip)
            throw new ModelNotFoundException("ImpactItemConversionTip for ImpactItem $impactItem->getId() not found");

        $impactItemConversionTip->setImpactItem($impactItem);
        return $impactItemConversionTip;
    }

    public function persist(ImpactItemConversionTip $impactItemConversionTip, array &$errors = []): ImpactItemConversionTip
    {
        $fields = [
            'impact_item_id' => ':impact_item_id',
            'rate_tip_description' => ':rate_tip_description',
            'reference' => ':reference'
        ];

        $values = [
            ':impact_item_id' => $impactItemConversionTip->getImpactItem()->getId(),
            ':rate_tip_description' => $impactItemConversionTip->getRateTipDescription(),
            ':reference' => $impactItemConversionTip->getReference()
        ];

        $sql = "INSERT INTO $this->table (". implode(',', array_keys($fields)) .") VALUES (".implode(',', array_values($fields)) . ")";

        $this->query($sql, $values);
        return $impactItemConversionTip;
    }


    public function delete(ImpactItemConversionTip $impactItemConversionTip): void
    {
        $sql = "DELETE FROM $this->table WHERE impact_item_id = ?";
        $this->query($sql, [$impactItemConversionTip->getImpactItem()->getId()]);
    }
}
