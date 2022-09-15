<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Entity\Matcher\MatcherReward;
use Goteo\Model\Matcher;
use Goteo\Model\Project\Reward;

class MatcherRewardRepository extends BaseRepository
{
    protected ?string $table = 'matcher_reward';

    /**
     * @return Matcher[]
     */
    public function getListByMatcher(Matcher $matcher): array
    {
        $sql = "SELECT *
            FROM matcher_reward
            WHERE matcher = ?
        ";

        return $this->query($sql, [$matcher->id])->fetchAll(\PDO::FETCH_CLASS, MatcherReward::class);
    }

    public function count(Matcher $matcher): int
    {
        $sql = "SELECT count(reward)
            FROM matcher_reward
            WHERE matcher = ?
            ";

        return $this->query($sql, [$matcher->id])->fetchColumn();
    }

    public function exists(Matcher $matcher, Reward $reward): bool
    {
        $sql = "SELECT matcher
            FROM matcher_reward
            WHERE matcher = :matcher and reward = :reward
        ";

        $values = [
            ':matcher' => $matcher->id,
            ':reward' => $reward->id
        ];

        try {
            $exists = (bool) $this->query($sql, $values)->fetchColumn();
        } catch ( \PDOException $e) {
            return false;
        }

        return $exists;
    }

    public function persist(MatcherReward $matcherReward, &$errors = []): ?MatcherReward
    {
        $fields = [
            'matcher' => ':matcher',
            'reward' => ':reward',
            'status' => ':status'
        ];

        $values = [
            ':matcher' => $matcherReward->getMatcher()->id,
            ':reward' => $matcherReward->getReward()->id,
            ':status' => $matcherReward->getStatus()
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (\PDOException $e) {
            $errors = $e->getMessage();
            return null;
        }

        return $matcherReward;
    }

    public function delete(MatcherReward $matcherReward): void
    {
        $sql = "DELETE FROM `$this->table` WHERE `matcher` = :matcher AND `reward` = :reward";
        $values = [
            ':matcher' => $matcherReward->getMatcher()->id,
            ':reward' => $matcherReward->getReward()->id
        ];
        try {
            $this->query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }
}
