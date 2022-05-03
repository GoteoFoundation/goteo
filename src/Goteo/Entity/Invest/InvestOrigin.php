<?php

namespace Goteo\Entity\Invest;

use Goteo\Model\Invest;

class InvestOrigin
{
    private int $invest_id;
    private string $source;
    private string $detail;
    private string $allocated;

    public function getInvestId(): int
    {
        return $this->invest_id;
    }

    public function getInvest(): Invest
    {
        return Invest::get($this->invest_id);
    }

    public function setInvestId(int $invest_id): InvestOrigin
    {
        $this->invest_id = $invest_id;
        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): InvestOrigin
    {
        $this->source = $source;
        return $this;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): InvestOrigin
    {
        $this->detail = $detail;
        return $this;
    }

    public function getAllocated(): string
    {
        return $this->allocated;
    }

    public function setAllocated(string $allocated): InvestOrigin
    {
        $this->allocated = $allocated;
        return $this;
    }

}
