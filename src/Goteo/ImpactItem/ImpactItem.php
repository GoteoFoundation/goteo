<?php

namespace Goteo\ImpactItem;

class ImpactItem
{
    private int $id;
    private string $name;
    private string $description;
    private string $unit;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ImpactItem
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ImpactItem
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ImpactItem
    {
        $this->description = $description;
        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): ImpactItem
    {
        $this->unit = $unit;
        return $this;
    }
}
