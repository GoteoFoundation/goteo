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

use Goteo\Application\Exception\ModelException;
use Goteo\Model\Post;
use Goteo\Model\Blog\Post as BlogPost;
use Symfony\Contracts\EventDispatcher\Event;

class FilterBlogPostEvent extends Event {
    protected $post;

    public function __construct($post) {
        if(!$post instanceOf Post && !$post instanceOf BlogPost)
            throw new ModelException('Post must be an instance of Model\Post or Model\Blog\Post');

        $this->post = $post;
    }

    public function getPost() {
        return $this->post;
    }
}
