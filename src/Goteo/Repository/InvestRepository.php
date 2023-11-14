<?php

namespace Goteo\Repository;

use Goteo\Model\Blog\Post;
use Goteo\Model\User;

class InvestRepository extends BaseRepository
{
    public function hasInvestToPostRewards(User $user, Post $post): bool
    {
        $sql = "SELECT true
                FROM invest i
                INNER JOIN invest_reward  ir ON ir.invest = i.id
                INNER JOIN reward r on ir.reward = r.id
                INNER JOIN post_reward_access pra ON pra.reward_id = r.id
                WHERE i.`user` = :user AND pra.post_id = :post and i.status = 1
                    and (r.subscribable = 0 or (r.subscribable = 1 and i.invested >= DATE_SUB(NOW(), INTERVAL 1 MONTH)))
                ";

        return (bool) $this->query($sql, [':user' => $user->id, ':post' => $post->id])->fetchColumn();
    }
}
