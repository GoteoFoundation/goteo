<?php

namespace Goteo\Library;

use Goteo\Application\Session;
use Goteo\Model\Blog\Post;
use Goteo\Model\Blog\Post\PostRewardAccess;
use Goteo\Model\Invest;
use Goteo\Model\User;

class PostPrivacyAccess
{
    static public function canAccess(?User $user, Post $post): bool
    {
        $admin = Session::isAdmin();
        if ($admin)
            return true;

        if ($user instanceof User) {
            return self::canUserAccess($user, $post);
        }

        if ($post->access_limited)
            return false;

        if ($post->publish) return true;

        return false;
    }

    private function canUserAccess(User $user, Post $post): bool
    {
        if ($user->id == $post->author)
            return true;

        if ($post->access_limited) {
            $postRewardAccessList = PostRewardAccess::getList(['post_id' => $post->id]);
            foreach($postRewardAccessList as $postRewardAccess) {
                $reward = $postRewardAccess->getReward();
                if ($reward->subscribable) {
                    $hasAccessToPost = Invest::hasUserInvestToRewardInTheLastMonth($user, $reward);
                    if ($hasAccessToPost)
                        return true;
                } else {
                    $hasAccessToPost = Invest::hasUserInvestToReward($user, $reward);
                    if ($hasAccessToPost)
                        return true;
                }
            }
        }

        return false;
    }

}
