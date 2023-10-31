<?php

namespace Goteo\Library;

use Goteo\Application\Session;
use Goteo\Model\Blog\Post;
use Goteo\Model\Blog\Post\PostRewardAccess;
use Goteo\Model\Invest;
use Goteo\Model\User;

class PostPrivacyAccess
{
    // this library has to handle if a user can access a certain post based on the relation between the user invest to a reward and if a reward is used as an access for the post through the post_reward_access table
    static public function canAccess(?User $user, Post $post): bool
    {
        $admin = Session::isAdmin();
        if ($admin)
            return true;

        if ($user instanceof User) {
            if ($user->id == $post->author)
                return true;

            if ($post->access_limited) {
                $postRewardAccessList = PostRewardAccess::getList(['post' => $post->id]);
                foreach($postRewardAccessList as $postRewardAccess) {
                    $hasAccessToPost = Invest::hasInvestToReward($user, $postRewardAccess->getReward());
                    if ($hasAccessToPost)
                        return true;
                }
            }
        }

        if ($post->access_limited)
            return false;

        if ($post->publish) return true;

        return false;
    }

}
