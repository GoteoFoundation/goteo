<?php

namespace Goteo\Library;

use Goteo\Application\Session;
use Goteo\Model\Blog\Post;
use Goteo\Model\Blog\Post\PostRewardAccess;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Repository\InvestRepository;

class PostPrivacyAccess
{
    static public function canAccess(?User $user, Post $post): bool
    {
        $admin = Session::isAdmin();
        if ($admin)
            return true;

        if ($user instanceof User)
            return self::canUserAccess($user, $post);

        if ($post->access_limited) return false;

        if ($post->publish) return true;

        return false;
    }

    private function canUserAccess(User $user, Post $post): bool
    {
        if ($user->id == $post->author)
            return true;

        if ($post->access_limited){
            $investRepository = new InvestRepository();
            return $investRepository->hasInvestToPostRewards($user, $post);
        }

        return false;
    }

    /**
     * @return PostRewardAccess[]
     */
    public function getLimitingRewardsForPost(Post $post): array
    {
        $postRewardsCount = PostRewardAccess::count(['post_id' => $post->id]);
        return PostRewardAccess::getList(['post_id' => $post->id], 0, $postRewardsCount);
    }
}
