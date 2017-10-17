<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\Event;

use Goteo\Model\Blog\Post;

use Symfony\Component\EventDispatcher\Event;

class FilterProjectPostEvent extends Event {
    protected $post;

    public function __construct(Post $post) {
        $this->post = $post;
    }

    public function getPost() {
        return $this->post;
    }
}
