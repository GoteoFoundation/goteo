<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Entity\Matcher;

use Goteo\Model\Matcher;
use Goteo\Model\Project\Reward;

class MatcherReward
{
    const STATUS_ACTIVE = 'active';
    private Matcher $matcher;
    private Reward $reward;
    private ?string $status = null;

    public function getMatcher(): ?Matcher
    {
        return $this->matcher;
    }

    public function setMatcher(?Matcher $matcher): MatcherReward
    {
        $this->matcher = $matcher;
        return $this;
    }

    public function getReward(): ?Reward
    {
        return $this->reward;
    }

    public function setReward(?Reward $reward): MatcherReward
    {
        $this->reward = $reward;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): MatcherReward
    {
        $this->status = $status;
        return $this;
    }
}
